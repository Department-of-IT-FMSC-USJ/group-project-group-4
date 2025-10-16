<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Handle reset via GET parameter
if (isset($_GET['reset']) || !isset($_SESSION['nic_flow'])) {
    $_SESSION['nic_flow'] = [
        'step' => 'notice',
        'application_id' => null
    ];
    // Redirect to clean URL
    if (isset($_GET['reset'])) {
        header('Location: /nic.php');
        exit;
    }
}

// Initialize session data for NIC flow
if (!isset($_SESSION['nic_flow'])) {
    $_SESSION['nic_flow'] = [
        'step' => 'notice',
        'application_id' => null
    ];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'proceed_notice':
            $_SESSION['nic_flow']['step'] = 'application';
            header('Location: /nic.php');
            exit;

        case 'submit_application':
            try {
                // Get form data
                $familyName = trim($_POST['familyName'] ?? '');
                $givenName = trim($_POST['givenName'] ?? '');
                $dob = trim($_POST['dob'] ?? '');
                $birthCertNo = trim($_POST['birthCertNo'] ?? '');
                $purpose = trim($_POST['applicationPurpose'] ?? '');

                // Validate required fields
                if (empty($familyName) || empty($givenName) || empty($dob) || empty($birthCertNo) || empty($purpose)) {
                    $_SESSION['nic_flow']['error'] = 'Please fill all required fields.';
                    header('Location: /nic.php');
                    exit;
                }

                // Insert into database
                $sql = "INSERT INTO identity_card_applications (
                    district, divisional_secretariat_division, grama_niladari_number_and_division,
                    family_name, name_, surname, sex, civil_status, profession, date_of_birth,
                    birth_certificate_no, place_of_birth, birth_division, birth_district,
                    perm_house_name_no, perm_building_type, perm_road_street, perm_village_city, perm_postal_code,
                    postal_house_name_no, postal_building_type, postal_road_street, postal_village_city, postal_postal_code,
                    phone_mobile, email, application_purpose
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $_POST['district'] ?? 'Colombo',
                    $_POST['divSecretariat'] ?? 'Colombo',
                    $_POST['gramaNiladhari'] ?? 'GN001',
                    $familyName,
                    $givenName,
                    $_POST['surname'] ?? '',
                    $_POST['sex'] ?? 'Male',
                    $_POST['civilStatus'] ?? 'Single',
                    $_POST['profession'] ?? 'Student',
                    $dob,
                    $birthCertNo,
                    $_POST['placeOfBirth'] ?? 'Colombo',
                    $_POST['birthDivision'] ?? 'Colombo',
                    $_POST['birthDistrict'] ?? 'Colombo',
                    $_POST['permHouse'] ?? 'N/A',
                    $_POST['permBuildingType'] ?? 'House',
                    $_POST['permStreet'] ?? 'Main Road',
                    $_POST['permCity'] ?? 'Colombo',
                    $_POST['permPostal'] ?? '00100',
                    $_POST['postHouse'] ?? $_POST['permHouse'] ?? 'N/A',
                    $_POST['postBuildingType'] ?? $_POST['permBuildingType'] ?? 'House',
                    $_POST['postStreet'] ?? $_POST['permStreet'] ?? 'Main Road',
                    $_POST['postCity'] ?? $_POST['permCity'] ?? 'Colombo',
                    $_POST['postPostal'] ?? $_POST['permPostal'] ?? '00100',
                    $_POST['mobile_phone'] ?? '',
                    $_POST['email'] ?? '',
                    $purpose
                ]);

                $applicationId = $pdo->lastInsertId();
                $_SESSION['nic_flow']['application_id'] = $applicationId;
                $_SESSION['nic_flow']['step'] = 'pricing';
                $_SESSION['nic_flow']['error'] = null;
            } catch (PDOException $e) {
                $_SESSION['nic_flow']['error'] = 'Application submission failed. Please try again.';
            }
            header('Location: /nic.php');
            exit;

        case 'proceed_to_payment':
            $_SESSION['nic_flow']['step'] = 'payment';
            header('Location: /nic.php');
            exit;

        case 'reset':
            $_SESSION['nic_flow'] = [
                'step' => 'notice',
                'application_id' => null
            ];
            header('Location: /nic.php');
            exit;
    }
}

// Get current flow state
$flowStep = $_SESSION['nic_flow']['step'] ?? 'notice';
$applicationId = $_SESSION['nic_flow']['application_id'] ?? null;
$flowError = $_SESSION['nic_flow']['error'] ?? null;

require __DIR__ . '/views/nic.view.php';
