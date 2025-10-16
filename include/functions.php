<?php


function sendResponse($success, $message, $data = null)
{
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}


function validateInput($data)
{
    return htmlspecialchars(trim($data));
}


function validateRequired($fields, $data)
{
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    return $missing;
}


function generateId($prefix = '')
{
    return $prefix . time() . rand(100, 999);
}


function logError($message)
{
    $logDir = __DIR__ . "/../logs";
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, $logDir . "/error.log");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit(0);
}


function get_request_data()
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    $raw = file_get_contents('php://input');

    $data = [];


    if (!empty($raw) && stripos($contentType, 'application/json') !== false) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }


    if (!empty($_POST)) {
        $data = array_merge($data, $_POST);
    }


    if (!empty($_GET)) {
        $data = array_merge($_GET, $data);
    }

    return $data;
}


function redirectTo($url, $params = [])
{
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header("Location: $url");
    exit;
}

function showError($message)
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - OneID</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 100px auto;
                padding: 20px;
            }

            .error-box {
                background: #fee;
                border: 2px solid #c00;
                padding: 20px;
                border-radius: 8px;
            }

            h1 {
                color: #c00;
                margin-top: 0;
            }

            a {
                color: #0066cc;
            }
        </style>
    </head>

    <body>
        <div class="error-box">
            <h1>Error</h1>
            <p><?= htmlspecialchars($message) ?></p>
            <p><a href="index.php">← Return to Home</a></p>
        </div>
    </body>

    </html>
<?php
    exit;
}


function isPost()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}


function isGet()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}
