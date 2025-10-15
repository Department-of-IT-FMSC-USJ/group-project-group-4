<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? $_GET['mistake_id'] ?? $_GET['mistakeId'] ?? null;
            $search = $_GET['search'] ?? $_GET['q'] ?? null;

            if ($id) {
                getMistakeById($id);
            } elseif ($search) {
                searchMistakes($search);
            } else {
                getAllMistakes();
            }
            break;

        case 'POST':

            $data = get_request_data();
            $search = $data['search'] ?? $data['q'] ?? null;
            if ($search) {
                searchMistakes($search);
            } else {
                sendResponse(false, "Search query required");
            }
            break;

        default:
            sendResponse(false, "Method not allowed");
    }
} catch (Exception $e) {
    logError("Mistakes API Error: " . $e->getMessage());
    sendResponse(false, "Server error occurred");
}


function getAllMistakes()
{
    global $pdo;

    try {

        $stmt = $pdo->query("SELECT * FROM mistakes ORDER BY amount DESC");
        $mistakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(true, "All violations retrieved", [
            'count' => count($mistakes),
            'violations' => $mistakes
        ]);
    } catch (PDOException $e) {
        logError("getAllMistakes Error: " . $e->getMessage());
        sendResponse(false, "Failed to retrieve violations");
    }
}


function getMistakeById($id)
{
    global $pdo;

    try {

        if (empty($id)) {
            sendResponse(false, "Violation ID is required");
        }


        $stmt = $pdo->prepare("SELECT * FROM mistakes WHERE mistake_id = ?");
        $stmt->execute([$id]);
        $mistake = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mistake) {
            sendResponse(true, "Violation found", $mistake);
        } else {
            sendResponse(false, "Violation not found");
        }
    } catch (PDOException $e) {
        logError("getMistakeById Error: " . $e->getMessage());
        sendResponse(false, "Failed to retrieve violation");
    }
}


function searchMistakes($search)
{
    global $pdo;

    try {

        $searchTerm = validateInput($search);

        if (empty($searchTerm)) {
            sendResponse(false, "Search query cannot be empty");
        }

        $stmt = $pdo->prepare("SELECT * FROM mistakes WHERE mistake LIKE ? ORDER BY amount DESC");
        $stmt->execute(['%' . $searchTerm . '%']);
        $mistakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($mistakes)) {
            sendResponse(true, "Violations found", [
                'count' => count($mistakes),
                'search_term' => $searchTerm,
                'violations' => $mistakes
            ]);
        } else {
            sendResponse(true, "No violations found matching your search", [
                'count' => 0,
                'search_term' => $searchTerm,
                'violations' => []
            ]);
        }
    } catch (PDOException $e) {
        logError("searchMistakes Error: " . $e->getMessage());
        sendResponse(false, "Failed to search violations");
    }
}
