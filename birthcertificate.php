<?php
session_start();
require_once _DIR_ . '/config/database.php';


if (!isset($_SESSION['birth_flow'])) {
    $_SESSION['birth_flow'] = [
        'step' => 'notice',
        'certificate' => null,
        'order' => null,
        'error' => null,
    ];
}


function computeBCOrder(int $quantity): array
{
    $quantity = max(1, min(10, $quantity));
    $perCopy = 1000.00;
    $copiesCost = $perCopy * $quantity;
    $serviceFee = 100.00;
    $postalFee = 60.00;

    $subtotal = $copiesCost + $serviceFee + $postalFee;
    $tax = round($subtotal * 0.18, 2);
    $total = $subtotal + $tax;

    return [
        'quantity' => $quantity,
        'copiesCost' => $copiesCost,
        'serviceFee' => $serviceFee,
        'postalFee' => $postalFee,
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total,
    ];
}


if (isset($_GET['reset'])) {
    $_SESSION['birth_flow'] = [
        'step' => 'notice',
        'certificate' => null,
        'order' => null,
        'error' => null,
    ];
    header('Location: /birthcertificate.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'proceed_notice':
            $_SESSION['birth_flow']['step'] = 'certificate';
            $_SESSION['birth_flow']['error'] = null;
            header('Location: /birthcertificate.php');
            exit;

        case 'lookup_certificate':
            $number = trim($_POST['birthCertificateNumber'] ?? '');
            if ($number === '') {
                $_SESSION['birth_flow']['error'] = 'Please enter your birth certificate number (e.g., BC001).';
                $_SESSION['birth_flow']['step'] = 'certificate';
                header('Location: /birthcertificate.php');
                exit;
            }

            try {
                $stmt = $pdo->prepare('SELECT * FROM birth_certificates WHERE birth_certificate_number = ?');
                $stmt->execute([$number]);
                $certificate = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

                if ($certificate) {
                    $_SESSION['birth_flow']['certificate'] = $certificate;
                    $_SESSION['birth_flow']['error'] = null;
                    $_SESSION['birth_flow']['step'] = 'verification';
                } else {
                    $_SESSION['birth_flow']['certificate'] = null;
                    $_SESSION['birth_flow']['order'] = null;
                    $_SESSION['birth_flow']['error'] = 'Birth certificate not found. Please check the number and try again.';
                    $_SESSION['birth_flow']['step'] = 'certificate';
                }
            } catch (Throwable $e) {
                $_SESSION['birth_flow']['error'] = 'Lookup failed. Please try again later.';
                $_SESSION['birth_flow']['step'] = 'certificate';
            }
            header('Location: /birthcertificate.php');
            exit;

        case 'verify_identity':
            $cert = $_SESSION['birth_flow']['certificate'] ?? null;
            if (!$cert) {
                $_SESSION['birth_flow']['error'] = 'Please look up your birth certificate first.';
                $_SESSION['birth_flow']['step'] = 'certificate';
                header('Location: /birthcertificate.php');
                exit;
            }

            $place = trim($_POST['placeOfBirth'] ?? '');
            $fatherPlace = trim($_POST['fatherPlaceOfBirth'] ?? '');
            $motherPlace = trim($_POST['motherPlaceOfBirth'] ?? '');

            $ok = strcasecmp($place, $cert['place_of_birth'] ?? '') === 0
                && strcasecmp($fatherPlace, $cert['father_place_of_birth'] ?? '') === 0
                && strcasecmp($motherPlace, $cert['mother_place_of_birth'] ?? '') === 0;

            if (!$ok) {
                $_SESSION['birth_flow']['error'] = 'Verification failed. The details do not match the record.';
                $_SESSION['birth_flow']['step'] = 'verification';
            } else {
                $_SESSION['birth_flow']['error'] = null;
                $_SESSION['birth_flow']['step'] = 'copies';
            }
            header('Location: /birthcertificate.php');
            exit;

        case 'select_copies':
            $quantity = intval($_POST['copyQuantity'] ?? 0);
            if ($quantity <= 0) {
                $_SESSION['birth_flow']['error'] = 'Please select the number of copies.';
                $_SESSION['birth_flow']['step'] = 'copies';
                header('Location: /birthcertificate.php');
                exit;
            }

            $order = computeBCOrder($quantity);
            $_SESSION['birth_flow']['order'] = $order;
            $_SESSION['birth_flow']['error'] = null;
            $_SESSION['birth_flow']['step'] = 'payment';
            header('Location: /birthcertificate.php');
            exit;

        case 'reset':
            $_SESSION['birth_flow'] = [
                'step' => 'notice',
                'certificate' => null,
                'order' => null,
                'error' => null,
            ];
            header('Location: /birthcertificate.php');
            exit;
    }
}


$flowStep = $_SESSION['birth_flow']['step'] ?? 'notice';
$certificate = $_SESSION['birth_flow']['certificate'] ?? null;
$order = $_SESSION['birth_flow']['order'] ?? null;
$flowError = $_SESSION['birth_flow']['error'] ?? null;

require _DIR_ . '/views/birthcertificate.view.php';
