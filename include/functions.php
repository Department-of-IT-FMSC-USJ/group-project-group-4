<?php
/**
 * Common helper functions used by APIs
 */

/**
 * Read request data for POST/PUT/DELETE
 * - For application/json content-type, decode JSON body
 * - For form-encoded requests, use $_POST
 */
function get_request_data()
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($data) ? $data : [];
        }
        return [];
    }

    // For PUT/DELETE, PHP doesn't populate $_POST — try parsing php://input as form data
    if (in_array($method, ['PUT', 'DELETE'])) {
        $raw = file_get_contents('php://input');
        parse_str($raw, $parsed);
        return $parsed;
    }

    // Default: use $_POST for POST requests
    return $_POST ?? [];
}


function sendResponse($success, $message = '', $data = null)
{
    header('Content-Type: application/json; charset=utf-8');
    $payload = ['success' => (bool)$success, 'message' => $message];
    if (!is_null($data)) $payload['data'] = $data;
    echo json_encode($payload);
    exit;
}

function logError($msg)
{
    // Simple error logging to php error log. In production, replace with a proper logger.
    error_log('[OneID] ' . $msg);
}
