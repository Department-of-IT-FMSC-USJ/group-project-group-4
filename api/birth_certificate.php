<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../include/functions.php';


if (!function_exists('sendResponse')) {
    function sendResponse(bool $success, string $message, $data = [], int $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['success' => $success, 'message' => $message], is_array($data) ? ['data' => $data] : ['data' => $data]));
        exit;
    }
}

if (!function_exists('get_request_data')) {
    function get_request_data()
    {
        $data = $_POST;

        $raw = file_get_contents('php://input');
        if ($raw) {
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                $data = array_merge($data, $json);
            } else {

                parse_str($raw, $parsed);
                if (!empty($parsed)) {
                    $data = array_merge($data, $parsed);
                }
            }
        }

        return $data;
    }
}

if (!function_exists('validateInput')) {
    function validateInput($value)
    {
        if (is_array($value)) return $value;
        return trim(htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    }
}

if (!function_exists('validateRequired')) {
    function validateRequired(array $required, array $data): array
    {
        $missing = [];
        foreach ($required as $key) {
            if (!isset($data[$key]) || trim((string)$data[$key]) === '') {
                $missing[] = $key;
            }
        }
        return $missing;
    }
}

if (!function_exists('logError')) {
    function logError($msg)
    {
        error_log($msg);
    }
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'get';

    switch ($method) {
        case 'POST':
            if ($action === 'verify') {
                verifyBirthCertificate();
            } elseif ($action === 'verify-security') {
                verifySecurityDetails();
            } elseif ($action === 'request') {
                requestCopies();
            } else {
                sendResponse(false, "Invalid action");
            }
            break;

        case 'GET':
            $certNumber = $_GET['certificate_number'] ?? $_GET['number'] ?? null;
            if ($certNumber) {
                getBirthCertificate($certNumber);
            } else {
                sendResponse(false, "Certificate number required");
            }
            break;

        case 'PUT':
            linkPayment();
            break;

        default:
            sendResponse(false, "Method not allowed");
    }
} catch (Exception $e) {
    logError("Birth Certificates API Error: " . $e->getMessage());
    sendResponse(false, "Server error occurred");
}

function verifyBirthCertificate()
{
    global $pdo;

    try {
        $data = get_request_data();
        $certNumber = validateInput($data['birthCertificateNumber'] ?? $data['certificate_number'] ?? '');

        if (empty($certNumber)) {
            sendResponse(false, "Birth certificate number is required");
        }

        $stmt = $pdo->prepare("SELECT birth_certificate_number FROM birth_certificates WHERE birth_certificate_number = ? LIMIT 1");
        $stmt->execute([$certNumber]);
        $exists = $stmt->fetch();

        if ($exists) {
            sendResponse(true, "Certificate number verified", ['certificate_number' => $certNumber]);
        } else {
            sendResponse(false, "Certificate number not found in records");
        }
    } catch (PDOException $e) {
        logError("verifyBirthCertificate Error: " . $e->getMessage());
        sendResponse(false, "Failed to verify certificate");
    }
}

function verifySecurityDetails()
{
    global $pdo;

    try {
        $data = get_request_data();

        $required = ['birthCertificateNumber', 'placeOfBirth', 'fatherPlaceOfBirth', 'motherPlaceOfBirth'];
        $missing = validateRequired($required, $data);
        if (!empty($missing)) {
            sendResponse(false, "Missing required fields: " . implode(', ', $missing));
        }

        $certNumber = validateInput($data['birthCertificateNumber']);
        $placeOfBirth = validateInput($data['placeOfBirth']);
        $fatherPlace = validateInput($data['fatherPlaceOfBirth']);
        $motherPlace = validateInput($data['motherPlaceOfBirth']);

        $stmt = $pdo->prepare("SELECT place_of_birth, father_place_of_birth, mother_place_of_birth FROM birth_certificates WHERE birth_certificate_number = ? LIMIT 1");
        $stmt->execute([$certNumber]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            sendResponse(false, "Certificate not found");
        }

        $normalize = function ($s) {
            return mb_strtolower(trim((string)$s));
        };

        $matchPlace = $normalize($placeOfBirth) === $normalize($record['place_of_birth']);
        $matchFather = $normalize($fatherPlace) === $normalize($record['father_place_of_birth']);
        $matchMother = $normalize($motherPlace) === $normalize($record['mother_place_of_birth']);

        if ($matchPlace && $matchFather && $matchMother) {
            sendResponse(true, "Identity verified successfully", ['verified' => true]);
        } else {
            sendResponse(false, "The details entered do not match our records");
        }
    } catch (PDOException $e) {
        logError("verifySecurityDetails Error: " . $e->getMessage());
        sendResponse(false, "Failed to verify security details");
    }
}

function requestCopies()
{
    global $pdo;

    try {
        $data = get_request_data();
        $certNumber = validateInput($data['birthCertificateNumber'] ?? $data['certificate_number'] ?? '');
        $quantity = (int)($data['copyQuantity'] ?? $data['quantity'] ?? 0);

        if (empty($certNumber)) {
            sendResponse(false, "Certificate number required");
        }

        if ($quantity < 1 || $quantity > 4) {
            sendResponse(false, "Invalid copy quantity (must be 1-4)");
        }


        $stmt = $pdo->prepare("SELECT birth_certificate_number FROM birth_certificates WHERE birth_certificate_number = ? LIMIT 1");
        $stmt->execute([$certNumber]);
        if (!$stmt->fetch()) {
            sendResponse(false, "Certificate not found");
        }

        $perCopyFee = 150;
        $serviceFee = 40;
        $postalFee = 60;
        $subtotal = ($perCopyFee * $quantity) + $serviceFee + $postalFee;
        $tax = round($subtotal * 0.18, 2);
        $total = round($subtotal + $tax, 2);

        sendResponse(true, "Birth certificate request submitted", [
            'certificate_number' => $certNumber,
            'copies' => $quantity,
            'per_copy_fee' => $perCopyFee,
            'service_fee' => $serviceFee,
            'postal_fee' => $postalFee,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total_amount' => $total,
            'status' => 'Pending Payment'
        ]);
    } catch (PDOException $e) {
        logError("requestCopies Error: " . $e->getMessage());
        sendResponse(false, "Failed to create request");
    }
}

function getBirthCertificate($certNumber)
{
    global $pdo;

    try {
        $certNumber = validateInput($certNumber);
        if (empty($certNumber)) {
            sendResponse(false, "Certificate number required");
        }

        $stmt = $pdo->prepare("SELECT * FROM birth_certificates WHERE birth_certificate_number = ? LIMIT 1");
        $stmt->execute([$certNumber]);
        $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($certificate) {
            sendResponse(true, "Certificate found", $certificate);
        } else {
            sendResponse(false, "Certificate not found");
        }
    } catch (PDOException $e) {
        logError("getBirthCertificate Error: " . $e->getMessage());
        sendResponse(false, "Failed to retrieve certificate");
    }
}

function linkPayment()
{
    global $pdo;

    try {
        $data = get_request_data();
        $certNumber = $data['certificate_number'] ?? $data['birthCertificateNumber'] ?? '';
        $paymentId = $data['payment_id'] ?? $data['paymentId'] ?? '';

        $certNumber = validateInput($certNumber);
        $paymentId = validateInput($paymentId);

        if (empty($certNumber) || empty($paymentId)) {
            sendResponse(false, "Certificate number and payment ID required");
        }

        $stmt = $pdo->prepare("UPDATE birth_certificates SET payment_id = ? WHERE birth_certificate_number = ?");
        if ($stmt->execute([$paymentId, $certNumber])) {
            if ($stmt->rowCount() > 0) {
                sendResponse(true, "Payment linked successfully");
            } else {
                sendResponse(false, "No record updated (certificate may not exist)");
            }
        } else {
            sendResponse(false, "Failed to link payment");
        }
    } catch (PDOException $e) {
        logError("linkPayment Error: " . $e->getMessage());
        sendResponse(false, "Failed to link payment");
    }
}
