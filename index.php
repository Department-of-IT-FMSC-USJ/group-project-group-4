<?php
session_start();

// success msg handdle
$successMessage = null;
if (isset($_GET['success']) && isset($_GET['id'])) {
    $successType = $_GET['success'];
    $successId = $_GET['id'];

    $messages = [
        'payment' => 'Payment completed successfully!',
        'fine' => 'Fine payment processed successfully!',
        'birth' => 'Birth certificate request submitted!',
        'nic' => 'NIC application submitted successfully!',
        'mistake' => 'Mistake report submitted for review!'
    ];

    $successMessage = $messages[$successType] ?? 'Operation completed successfully!';
    $successMessage .= " Reference ID: " . htmlspecialchars($successId);
}

require __DIR__ . '/views/index.view.php';
