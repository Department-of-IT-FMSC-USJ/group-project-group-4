<?php

session_start();
require_once __DIR__ . '/config/database.php';


if (!isset($_SESSION['fines_flow'])) {
    $_SESSION['fines_flow'] = [
        'step' => 'lookup',
        'fine' => null,
        'order' => null,
        'error' => null,
        'just_paid' => false,
    ];
}


function computeFineOrder(array $fine): array
{

    $fineAmount = (float)($fine['fine_amount'] ?? $fine['amount'] ?? 0);
    $serviceFee = 250.00;
    $postalFee = 100.00;

    $subtotal = $fineAmount + $serviceFee + $postalFee;
    $tax = round($subtotal * 0.18, 2);
    $total = $subtotal + $tax;

    return [
        'fineAmount' => $fineAmount,
        'serviceFee' => $serviceFee,
        'postalFee' => $postalFee,
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total,
    ];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'lookup_fine':
            try {
                $input = trim($_POST['fineId'] ?? '');
                if ($input === '') {
                    $_SESSION['fines_flow']['error'] = 'Please enter a Fine ID, Vehicle Number, or License Number.';
                    $_SESSION['fines_flow']['step'] = 'lookup';
                    header('Location: /fines.php');
                    exit;
                }

                // Validate format
                $isValidVehicle = preg_match('/^[A-Z]{3}-[0-9]{4}$/', $input);
                $isValidLicense = preg_match('/^[A-Z][0-9]{7}$/', $input);
                $isValidFineId = ctype_digit($input);

                if (!$isValidFineId && !$isValidVehicle && !$isValidLicense) {
                    $_SESSION['fines_flow']['error'] = 'Invalid format. Use Fine ID (number), Vehicle (ABC-1234), or License (B1234567).';
                    $_SESSION['fines_flow']['step'] = 'lookup';
                    header('Location: /fines.php');
                    exit;
                }

                $fine = null;

                if (ctype_digit($input)) {
                    $stmt = $pdo->prepare("SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status, f.payment_id
                                            FROM fines f
                                            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                                            LEFT JOIN payments p ON f.payment_id = p.payment_id
                                            WHERE f.fine_id = ?");
                    $stmt->execute([$input]);
                    $fine = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
                }

                if (!$fine) {

                    $stmt = $pdo->prepare("SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status, f.payment_id
                                            FROM fines f
                                            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                                            LEFT JOIN payments p ON f.payment_id = p.payment_id
                                            WHERE f.vehicle_number = ?
                                            ORDER BY f.issued_at DESC
                                            LIMIT 1");
                    $stmt->execute([$input]);
                    $fine = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
                }

                if (!$fine) {

                    $stmt = $pdo->prepare("SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status, f.payment_id
                                            FROM fines f
                                            LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                                            LEFT JOIN payments p ON f.payment_id = p.payment_id
                                            WHERE f.license_number = ?
                                            ORDER BY f.issued_at DESC
                                            LIMIT 1");
                    $stmt->execute([$input]);
                    $fine = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
                }

                if (!$fine) {
                    $_SESSION['fines_flow']['error'] = 'No matching fine found. Please check your input and try again.';
                    $_SESSION['fines_flow']['step'] = 'lookup';
                    $_SESSION['fines_flow']['fine'] = null;
                    $_SESSION['fines_flow']['order'] = null;
                } else {

                    if ($fine['payment_status'] === 'Completed') {
                        $_SESSION['fines_flow']['fine'] = $fine;
                        $_SESSION['fines_flow']['order'] = null;
                        $_SESSION['fines_flow']['error'] = null;
                        $_SESSION['fines_flow']['step'] = 'done';
                        $_SESSION['fines_flow']['just_paid'] = false; // Already paid before
                    } else {
                        $order = computeFineOrder($fine);
                        $_SESSION['fines_flow']['fine'] = $fine;
                        $_SESSION['fines_flow']['order'] = $order;
                        $_SESSION['fines_flow']['error'] = null;
                        $_SESSION['fines_flow']['step'] = 'details';
                        $_SESSION['fines_flow']['just_paid'] = false;
                    }
                }
            } catch (Throwable $e) {
                $_SESSION['fines_flow']['error'] = 'Fine lookup failed. Please try again later.';
                $_SESSION['fines_flow']['step'] = 'lookup';
            }
            header('Location: /fines.php');
            exit;

        case 'proceed_to_payment':
            $_SESSION['fines_flow']['step'] = 'payment';
            header('Location: /fines.php');
            exit;


        case 'payment_done':

            $fineId = $_SESSION['fines_flow']['fine']['fine_id'] ?? null;
            $paymentId = intval($_POST['payment_id'] ?? 0);
            if ($fineId && $paymentId) {
                $stmt = $pdo->prepare("UPDATE fines SET payment_id = ? WHERE fine_id = ?");
                $stmt->execute([$paymentId, $fineId]);
                // Update session state to reflect paid
                $stmt = $pdo->prepare("SELECT f.*, m.mistake, m.amount AS fine_amount, p.status AS payment_status, f.payment_id
                                        FROM fines f
                                        LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
                                        LEFT JOIN payments p ON f.payment_id = p.payment_id
                                        WHERE f.fine_id = ?");
                $stmt->execute([$fineId]);
                $fine = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
                $_SESSION['fines_flow']['fine'] = $fine;
                $_SESSION['fines_flow']['order'] = null;
                $_SESSION['fines_flow']['error'] = null;
                $_SESSION['fines_flow']['step'] = 'done';
                $_SESSION['fines_flow']['just_paid'] = true; // Just paid now
            }
            header('Location: /fines.php');
            exit;

        case 'reset':
            $_SESSION['fines_flow'] = [
                'step' => 'lookup',
                'fine' => null,
                'order' => null,
                'error' => null,
                'just_paid' => false,
            ];
            header('Location: /fines.php');
            exit;
    }
}



$flowStep = $_SESSION['fines_flow']['step'] ?? 'lookup';
$fine = $_SESSION['fines_flow']['fine'] ?? null;
$order = $_SESSION['fines_flow']['order'] ?? null;
$flowError = $_SESSION['fines_flow']['error'] ?? null;
$justPaid = $_SESSION['fines_flow']['just_paid'] ?? false;


require __DIR__ . '/views/fines.view.php';
