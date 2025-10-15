<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../include/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            processPayment();
            break;

        case 'GET':
            $id = $_GET['id'] ?? $_GET['payment_id'] ?? $_GET['paymentId'] ?? null;
            if ($id) {
                getPayment($id);
            } else {
                getAllPayments();
            }
            break;

        case 'PUT':
            updatePaymentStatus();
            break;

        default:
            sendResponse(false, "Method not allowed");
    }
} catch (Exception $e) {
    logError("Payments API Error: " . $e->getMessage());
    sendResponse(false, "Server error occurred");
}


function processPayment()
{
    global $pdo;
    $data = get_request_data();

    
    $amount = floatval($data['amount'] ?? $data['order_total'] ?? 0);
    if ($amount <= 0) {
        sendResponse(false, "Invalid payment amount");
    }

    
    $cardholderName = validateInput($data['cardholdername'] ?? '');
    $cardNumber = validateInput($data['cardnumber'] ?? '');
    $expiryDate = validateInput($data['expirydate'] ?? '');
    $cvv = validateInput($data['cvv'] ?? '');
    $email = validateInput($data['email'] ?? $data['payment-email'] ?? '');
    $phone = validateInput($data['phonenumber'] ?? $data['phone'] ?? '');
    $address = validateInput($data['address'] ?? '');
    $successVariant = $data['success_variant'] ?? 'payment';
    $serviceId = $data['service_id'] ?? null;

    
    if (empty($cardholderName) || empty($cardNumber) || empty($expiryDate) ||
        empty($cvv) || empty($email) || empty($phone) || empty($address)) {
        sendResponse(false, "All payment fields are required");
    }

    
    $cardDigits = preg_replace('/\D/', '', $cardNumber);
    if (strlen($cardDigits) < 13 || strlen($cardDigits) > 19) {
        sendResponse(false, "Invalid card number");
    }

    
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiryDate)) {
        sendResponse(false, "Invalid expiry date format (use MM/YY)");
    }

    
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        sendResponse(false, "Invalid CVV");
    }

    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, "Invalid email format");
    }

    
    $status = 'Completed';
    $paymentDate = date('Y-m-d H:i:s');

    
    $stmt = $pdo->prepare("INSERT INTO payments (amount, payment_date, status) VALUES (?, ?, ?)");
    if ($stmt->execute([$amount, $paymentDate, $status])) {
        $paymentId = $pdo->lastInsertId();

        sendResponse(true, "Payment processed successfully", [
            'payment_id' => $paymentId,
            'amount' => $amount,
            'status' => $status,
            'payment_date' => $paymentDate,
            'success_variant' => $successVariant,
            'service_id' => $serviceId
        ]);
    } else {
        sendResponse(false, "Payment processing failed");
    }
}


function getPayment($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE payment_id = ?");
    $stmt->execute([$id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
        sendResponse(true, "Payment found", $payment);
    } else {
        sendResponse(false, "Payment not found");
    }
}


function getAllPayments()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM payments ORDER BY payment_date DESC LIMIT 100");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendResponse(true, "All payments retrieved", $payments);
}


function updatePaymentStatus()
{
    global $pdo;
    $data = get_request_data();

    $paymentId = intval($data['payment_id'] ?? 0);
    $status = $data['status'] ?? '';

    if ($paymentId <= 0 || empty($status)) {
        sendResponse(false, "Payment ID and status are required");
    }

    $validStatuses = ['Pending', 'Completed', 'Failed', 'Cancelled'];
    if (!in_array($status, $validStatuses)) {
        sendResponse(false, "Invalid status value");
    }

    $stmt = $pdo->prepare("UPDATE payments SET status = ? WHERE payment_id = ?");
    if ($stmt->execute([$status, $paymentId])) {
        sendResponse(true, "Payment status updated successfully", [
            'payment_id' => $paymentId,
            'new_status' => $status
        ]);
    } else {
        sendResponse(false, "Failed to update payment status");
    }
}
?>
