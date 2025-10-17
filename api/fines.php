<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../include/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        getFineById($_GET['id']);
    } elseif (isset($_GET['vehicle'])) {
        getFinesByVehicle($_GET['vehicle']);
    } elseif (isset($_GET['license'])) {
        getFinesByLicense($_GET['license']);
    } else {
        getAllFines();
    }
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = get_request_data();
    $action = $data['action'] ?? '';

    if ($action === 'linkPayment') {
        linkPaymentToFine();
    } else {
        getFines();
    }
    exit;
}

sendResponse(false, "Invalid request method");


function get_request_data()
{
    $data = [];
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        $data = $_POST;
        if (empty($data)) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true) ?? [];
        }
    } else {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?? [];
    }
    return $data;
}


function sendResponse($success, $message, $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}


function getFines()
{
    global $pdo;

    $data = get_request_data();
    $vehicleNumber = trim($data['vehicleNumber'] ?? $data['vehicle_number'] ?? '');
    $licenseNumber = trim($data['licenseNumber'] ?? $data['license_number'] ?? '');

    if (empty($vehicleNumber) && empty($licenseNumber)) {
        sendResponse(false, "Please provide vehicle number or license number");
    }

    if (!empty($vehicleNumber) && !empty($licenseNumber)) {
        $sql = "SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status
                FROM fines f
                LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                LEFT JOIN payments p ON f.payment_id = p.payment_id
                WHERE f.vehicle_number = ? OR f.license_number = ?
                ORDER BY f.issued_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vehicleNumber, $licenseNumber]);
    } elseif (!empty($vehicleNumber)) {
        $sql = "SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status
                FROM fines f
                LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                LEFT JOIN payments p ON f.payment_id = p.payment_id
                WHERE f.vehicle_number = ?
                ORDER BY f.issued_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vehicleNumber]);
    } else {
        $sql = "SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status
                FROM fines f
                LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                LEFT JOIN payments p ON f.payment_id = p.payment_id
                WHERE f.license_number = ?
                ORDER BY f.issued_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$licenseNumber]);
    }

    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($fines)) {
        sendResponse(true, "Fines found", ['count' => count($fines), 'fines' => $fines]);
    } else {
        sendResponse(true, "No fines found", ['count' => 0, 'fines' => []]);
    }
}


function getFineById($id)
{
    global $pdo;

    $sql = "SELECT f.*, m.mistake, m.amount, p.status AS payment_status, p.payment_date
            FROM fines f
            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
            LEFT JOIN payments p ON f.payment_id = p.payment_id
            WHERE f.fine_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $fine = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fine) {
        sendResponse(true, "Fine found", $fine);
    } else {
        sendResponse(false, "Fine not found");
    }
}


function getFinesByVehicle($vehicleNumber)
{
    global $pdo;

    $sql = "SELECT f.*, m.mistake, m.amount, p.status AS payment_status
            FROM fines f
            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
            LEFT JOIN payments p ON f.payment_id = p.payment_id
            WHERE f.vehicle_number = ?
            ORDER BY f.issued_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vehicleNumber]);
    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($fines)) {
        sendResponse(true, "Fines found", ['count' => count($fines), 'fines' => $fines]);
    } else {
        sendResponse(false, "No fines found for this vehicle");
    }
}


function getFinesByLicense($licenseNumber)
{
    global $pdo;

    $sql = "SELECT f.*, m.mistake, m.amount, p.status AS payment_status
            FROM fines f
            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
            LEFT JOIN payments p ON f.payment_id = p.payment_id
            WHERE f.license_number = ?
            ORDER BY f.issued_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$licenseNumber]);
    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($fines)) {
        sendResponse(true, "Fines found", ['count' => count($fines), 'fines' => $fines]);
    } else {
        sendResponse(false, "No fines found for this license");
    }
}


function linkPaymentToFine()
{
    global $pdo;

    $data = get_request_data();
    $fineId = $data['fine_id'] ?? $data['fineId'] ?? null;
    $paymentId = $data['payment_id'] ?? $data['paymentId'] ?? null;

    if (!$fineId || !$paymentId) {
        sendResponse(false, "Fine ID and Payment ID are required");
    }

    $stmt = $pdo->prepare("UPDATE fines SET payment_id = ? WHERE fine_id = ?");
    if ($stmt->execute([$paymentId, $fineId])) {
        if ($stmt->rowCount() > 0) {
            sendResponse(true, "Payment linked to fine successfully");
        } else {
            sendResponse(false, "Fine not found");
        }
    } else {
        sendResponse(false, "Failed to link payment");
    }
}


function getAllFines()
{
    global $pdo;

    $sql = "SELECT f.fine_id, f.vehicle_number, f.license_number, f.driver_name, f.issued_at, f.due_at,
                   m.mistake, m.amount AS fine_amount, p.status AS payment_status
            FROM fines f
            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
            LEFT JOIN payments p ON f.payment_id = p.payment_id
            ORDER BY f.issued_at DESC
            LIMIT 100";
    $stmt = $pdo->query($sql);
    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendResponse(true, "All fines retrieved", $fines);
}
