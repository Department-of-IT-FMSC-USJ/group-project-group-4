<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Handle AJAX requests for marking items as processed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $action = $_POST['ajax_action'];
    $id = $_POST['id'] ?? null;
    $processed = isset($_POST['processed']) && $_POST['processed'] === 'true' ? 1 : 0;

    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID is required']);
        exit;
    }

    try {
        if ($action === 'update_nic_processed') {
            $stmt = $pdo->prepare("UPDATE identity_card_applications SET processed_by_admin = ? WHERE application_id = ?");
            $stmt->execute([$processed, $id]);
        } elseif ($action === 'update_birth_processed') {
            $stmt = $pdo->prepare("UPDATE birth_certificate_orders SET processed_by_admin = ? WHERE order_id = ?");
            $stmt->execute([$processed, $id]);
        } elseif ($action === 'update_fine_processed') {
            $stmt = $pdo->prepare("UPDATE fines SET processed_by_admin = ? WHERE fine_id = ?");
            $stmt->execute([$processed, $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            exit;
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $loginError = 'Please enter both username and password';
    } else {
        // Check credentials from database
        $stmt = $pdo->prepare("SELECT admin_id, username, password_hash, full_name, is_active FROM admins WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_full_name'] = $admin['full_name'];

            // Update last login time
            $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = ?");
            $updateStmt->execute([$admin['admin_id']]);

            header('Location: /admin.php');
            exit;
        } else {
            $loginError = 'Invalid username or password';
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
    session_destroy();
    header('Location: /admin.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Show login form
    require __DIR__ . '/views/admin-login.view.php';
    exit;
}

// Get filter
$filter = $_GET['filter'] ?? 'all';

// Fetch all NIC applications (FIFO: oldest unprocessed first; processed items at bottom)
$sqlNic = "SELECT 
            ia.application_id,
            ia.family_name,
            ia.name_,
            ia.surname,
            ia.date_of_birth,
            ia.birth_certificate_no,
            ia.application_purpose,
            ia.application_date,
            ia.phone_mobile,
            ia.email,
            ia.photo_link,
            ia.processed_by_admin,
            p.status as payment_status,
            p.amount as payment_amount,
            CASE 
                WHEN ia.processed_by_admin = 1 THEN 1
                ELSE 0
            END as processed_order
        FROM identity_card_applications ia
        LEFT JOIN payments p ON ia.payment_id = p.payment_id
        ORDER BY processed_order ASC, ia.application_date ASC";

$stmtNic = $pdo->query($sqlNic);
$nicApplications = $stmtNic->fetchAll(PDO::FETCH_ASSOC);

// Fetch all birth certificate orders (FIFO: oldest unprocessed first; processed items at bottom)
$sqlBirth = "SELECT 
            bco.order_id,
            bco.birth_certificate_number,
            bco.quantity,
            bco.order_date,
            bco.processed_by_admin,
            bc.date_of_birth,
            bc.place_of_birth,
            p.status as payment_status,
            p.amount as payment_amount,
            p.payment_date,
            CASE 
                WHEN bco.processed_by_admin = 1 THEN 1
                ELSE 0
            END as processed_order
        FROM birth_certificate_orders bco
        LEFT JOIN birth_certificates bc ON bco.birth_certificate_number = bc.birth_certificate_number
        LEFT JOIN payments p ON bco.payment_id = p.payment_id
        ORDER BY processed_order ASC, bco.order_date ASC";

$stmtBirth = $pdo->query($sqlBirth);
$birthCertificateOrders = $stmtBirth->fetchAll(PDO::FETCH_ASSOC);

// Fetch all fines (Completed first, then by date, processed items at bottom)
$sqlFines = "SELECT 
            f.fine_id,
            f.vehicle_number,
            f.license_number,
            f.place,
            f.driver_name,
            f.driver_address,
            f.issued_at,
            f.due_at,
            f.processed_by_admin,
            m.mistake,
            m.amount as fine_amount,
            p.status as payment_status,
            p.amount as payment_amount,
            p.payment_date,
            CASE 
                WHEN p.status IS NULL THEN 1
                WHEN p.status = 'Pending' THEN 1
                WHEN p.status = 'Completed' THEN 0
                WHEN p.status = 'Failed' THEN 2
                ELSE 3
            END as status_order,
            CASE 
                WHEN f.processed_by_admin = 1 THEN 1
                ELSE 0
            END as processed_order
        FROM fines f
        LEFT JOIN mistakes m ON f.mistake_id = m.mistake_id
        LEFT JOIN payments p ON f.payment_id = p.payment_id
        ORDER BY processed_order ASC, status_order ASC, f.issued_at DESC";

$stmtFines = $pdo->query($sqlFines);
$fines = $stmtFines->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/views/admin.view.php';
