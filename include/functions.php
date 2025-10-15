<?php

function sendResponse($success, $message, $data = null)
{
    header('Content-Type: application/json');
    $response = [
        'success' => (bool)$success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

function get_request_data()
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return $_GET;
    }

    return !empty($_POST) ? $_POST : [];
}

function validateInput($value)
{
    if (!is_string($value)) {
        return '';
    }
    return trim(filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
}

function logError($message)
{
    error_log($message);
}

?>

