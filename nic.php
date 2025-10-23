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
            unset($_SESSION['nic_form_data']);
            header('Location: /nic.php');
            exit;

        case 'submit_application':
            try {
                // Gather & trim inputs
                $familyName = trim($_POST['familyName'] ?? '');
                $givenName = trim($_POST['givenName'] ?? '');
                $surname = trim($_POST['surname'] ?? '');
                $sex = trim($_POST['sex'] ?? 'Male');
                $civilStatus = trim($_POST['civilStatus'] ?? 'Single');
                $profession = trim($_POST['profession'] ?? '');
                $dob = trim($_POST['dob'] ?? '');
                $birthCertNo = trim($_POST['birthCertNo'] ?? '');
                $placeOfBirth = trim($_POST['placeOfBirth'] ?? '');
                $birthDivision = trim($_POST['birthDivision'] ?? '');
                $birthDistrict = trim($_POST['birthDistrict'] ?? '');
                $district = trim($_POST['district'] ?? '');
                $divSecretariat = trim($_POST['divSecretariat'] ?? '');
                $gramaNiladhari = trim($_POST['gramaNiladhari'] ?? '');
                $permHouse = trim($_POST['permHouse'] ?? '');
                $permBuildingType = trim($_POST['permBuildingType'] ?? '');
                $permStreet = trim($_POST['permStreet'] ?? '');
                $permCity = trim($_POST['permCity'] ?? '');
                $permPostal = trim($_POST['permPostal'] ?? '');
                $postHouse = trim($_POST['postHouse'] ?? '');
                $postBuildingType = trim($_POST['postBuildingType'] ?? '');
                $postStreet = trim($_POST['postStreet'] ?? '');
                $postCity = trim($_POST['postCity'] ?? '');
                $postPostal = trim($_POST['postPostal'] ?? '');
                $mobilePhone = trim($_POST['mobile_phone'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $photoLink = trim($_POST['photoLink'] ?? '');
                $purposeRaw = trim($_POST['applicationPurpose'] ?? '');

                // Map/validate enums to DB values
                $sex = in_array($sex, ['Male', 'Female']) ? $sex : 'Male';
                $purpose = 'Changes';
                $purposeLower = strtolower($purposeRaw);
                if (strpos($purposeLower, 'lost') !== false) $purpose = 'Lost';
                elseif (strpos($purposeLower, 'renew') !== false) $purpose = 'Renew';
                elseif (strpos($purposeLower, 'damage') !== false) $purpose = 'Damaged';
                elseif (strpos($purposeLower, 'address') !== false || strpos($purposeLower, 'correction') !== false || strpos($purposeLower, 'change') !== false) $purpose = 'Changes';

                // Required fields check
                $required = [
                    $familyName,
                    $givenName,
                    $dob,
                    $birthCertNo,
                    $district,
                    $divSecretariat,
                    $gramaNiladhari,
                    $permHouse,
                    $permBuildingType,
                    $permStreet,
                    $permCity,
                    $permPostal,
                    $postHouse,
                    $postBuildingType,
                    $postStreet,
                    $postCity,
                    $postPostal,
                    $placeOfBirth,
                    $birthDivision,
                    $birthDistrict,
                    $photoLink,
                    $profession
                ];
                if (in_array('', $required, true)) {
                    $_SESSION['nic_flow']['error'] = 'Please fill all required fields.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                // Validate formats (lightweight)
                if (!preg_match('/^\d{3,10}$/', $permPostal) || !preg_match('/^\d{3,10}$/', $postPostal)) {
                    $_SESSION['nic_flow']['error'] = 'Postal code must be 3-10 digits.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }
                if ($mobilePhone !== '' && !preg_match('/^\d{10}$/', $mobilePhone)) {
                    $_SESSION['nic_flow']['error'] = 'Mobile number must be 10 digits.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }
                if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['nic_flow']['error'] = 'Please provide a valid email address.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                // Optional: verify birth certificate exists
                $check = $pdo->prepare('SELECT 1 FROM birth_certificates WHERE birth_certificate_number = ?');
                $check->execute([$birthCertNo]);
                if (!$check->fetchColumn()) {
                    $_SESSION['nic_flow']['error'] = 'Birth certificate number not found in records.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                // Handle PDFs (5MB max each)
                $maxSize = 5 * 1024 * 1024;
                $birthPdf = null;
                $photoPdf = null;
                if (!isset($_FILES['birthCertPdf']) || $_FILES['birthCertPdf']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['nic_flow']['error'] = 'Birth Certificate PDF is required.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }
                if (!isset($_FILES['photoPdf']) || $_FILES['photoPdf']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['nic_flow']['error'] = 'Photo PDF is required.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }
                if ($_FILES['birthCertPdf']['size'] > $maxSize || $_FILES['photoPdf']['size'] > $maxSize) {
                    $_SESSION['nic_flow']['error'] = 'Uploaded files must be 5MB or smaller.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }
                $birthPdf = file_get_contents($_FILES['birthCertPdf']['tmp_name']);
                $photoPdf = file_get_contents($_FILES['photoPdf']['tmp_name']);

                // Insert into database (aligns with API expectations)
                $sql = "INSERT INTO identity_card_applications (
                    district, divisional_secretariat_division, grama_niladari_number_and_division,
                    family_name, name_, surname, sex, civil_status, profession, date_of_birth,
                    birth_certificate_no, place_of_birth, birth_division, birth_district,
                    perm_house_name_no, perm_building_type, perm_road_street, perm_village_city, perm_postal_code,
                    postal_house_name_no, postal_building_type, postal_road_street, postal_village_city, postal_postal_code,
                    phone_mobile, email, application_purpose, birth_certificate_pdf, police_report_doc, photo_pdf, photo_link
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $district);
                $stmt->bindValue(2, $divSecretariat);
                $stmt->bindValue(3, $gramaNiladhari);
                $stmt->bindValue(4, $familyName);
                $stmt->bindValue(5, $givenName);
                $stmt->bindValue(6, $surname);
                $stmt->bindValue(7, $sex);
                $stmt->bindValue(8, $civilStatus);
                $stmt->bindValue(9, $profession);
                $stmt->bindValue(10, $dob);
                $stmt->bindValue(11, $birthCertNo);
                $stmt->bindValue(12, $placeOfBirth);
                $stmt->bindValue(13, $birthDivision);
                $stmt->bindValue(14, $birthDistrict);
                $stmt->bindValue(15, $permHouse);
                $stmt->bindValue(16, $permBuildingType);
                $stmt->bindValue(17, $permStreet);
                $stmt->bindValue(18, $permCity);
                $stmt->bindValue(19, $permPostal);
                $stmt->bindValue(20, $postHouse);
                $stmt->bindValue(21, $postBuildingType);
                $stmt->bindValue(22, $postStreet);
                $stmt->bindValue(23, $postCity);
                $stmt->bindValue(24, $postPostal);
                $stmt->bindValue(25, $mobilePhone);
                $stmt->bindValue(26, $email);
                $stmt->bindValue(27, $purpose);
                $stmt->bindValue(28, $birthPdf, PDO::PARAM_LOB);
                $emptyBlob = '';
                $stmt->bindValue(29, $emptyBlob, PDO::PARAM_LOB); // police report not collected online
                $stmt->bindValue(30, $photoPdf, PDO::PARAM_LOB);
                $stmt->bindValue(31, $photoLink);
                $stmt->execute();

                $applicationId = $pdo->lastInsertId();
                $_SESSION['nic_flow']['application_id'] = $applicationId;
                // Go straight to payment; user will see success only after payment success page
                $_SESSION['nic_flow']['step'] = 'payment';
                $_SESSION['nic_flow']['error'] = null;
                unset($_SESSION['nic_form_data']);
            } catch (PDOException $e) {
                $_SESSION['nic_flow']['error'] = 'Application submission failed. Please try again.';
                $_SESSION['nic_flow']['step'] = 'application';
                $_SESSION['nic_form_data'] = $_POST;
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
            unset($_SESSION['nic_form_data']);
            header('Location: /nic.php');
            exit;

        case 'payment_done':
            // Link payment with the current application after payment-success redirect
            try {
                $paymentId = intval($_POST['payment_id'] ?? 0);
                $applicationId = intval($_POST['application_id'] ?? ($_SESSION['nic_flow']['application_id'] ?? 0));
                if ($paymentId <= 0 || $applicationId <= 0) {
                    $_SESSION['nic_flow']['error'] = 'Missing payment confirmation. Please contact support.';
                    header('Location: /nic.php');
                    exit;
                }
                $stmt = $pdo->prepare("UPDATE identity_card_applications SET payment_id = ? WHERE application_id = ?");
                $stmt->execute([$paymentId, $applicationId]);
                // Optionally: verify one row updated
                $_SESSION['nic_flow']['error'] = null;
            } catch (PDOException $e) {
                // Log but don't expose sensitive info
                $_SESSION['nic_flow']['error'] = 'We recorded your payment but failed to finalize the application automatically. Our team will review it.';
            }
            // Always show the standard success page already rendered; nothing else to do here
            header('Location: /payment-success.php?success=nic&id=' . urlencode($_POST['payment_id'] ?? '') . '&linked=1');
            exit;
    }
}

// Get current flow state
$flowStep = $_SESSION['nic_flow']['step'] ?? 'notice';
$applicationId = $_SESSION['nic_flow']['application_id'] ?? null;
$flowError = $_SESSION['nic_flow']['error'] ?? null;

require __DIR__ . '/views/nic.view.php';
