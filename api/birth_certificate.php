<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../include/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $action = $data['action'] ?? '';
    $certNumber = trim($data['certificateNumber'] ?? '');

    if ($action === 'lookup' && !empty($certNumber)) {
        lookupCertificate($certNumber);
    } else {
        sendResponse(false, "Invalid request");
    }
    exit;
}

sendResponse(false, "Invalid request method");


function sendResponse($success, $message, $data = null)
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}


function lookupCertificate($certNumber)
{
    global $pdo;

    try {
        $sql = "SELECT * FROM birth_certificates WHERE birth_certificate_number = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$certNumber]);
        $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($certificate) {
            sendResponse(true, "Certificate found", $certificate);
        } else {
            sendResponse(false, "Certificate not found. Please verify the certificate number.");
        }
    } catch (PDOException $e) {
        sendResponse(false, "Database error occurred");
    }
}