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

                // Validate Birth Certificate format (BC followed by numbers)
                if (!preg_match('/^BC\d+$/', $birthCertNo)) {
                    $_SESSION['nic_flow']['error'] = 'Birth Certificate Number must start with BC followed by numbers (e.g., BC001).';
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
                $policePdf = null;

                // Validate Birth Certificate PDF
                if (!isset($_FILES['birthCertPdf']) || $_FILES['birthCertPdf']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['nic_flow']['error'] = 'Birth Certificate PDF is required.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                // Validate Police Report PDF
                if (!isset($_FILES['policeReportPdf']) || $_FILES['policeReportPdf']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['nic_flow']['error'] = 'Police Report PDF is required.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                // Check file sizes
                if ($_FILES['birthCertPdf']['size'] > $maxSize || $_FILES['policeReportPdf']['size'] > $maxSize) {
                    $_SESSION['nic_flow']['error'] = 'Uploaded files must be 5MB or smaller.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                // Check file types
                if ($_FILES['birthCertPdf']['type'] !== 'application/pdf' || $_FILES['policeReportPdf']['type'] !== 'application/pdf') {
                    $_SESSION['nic_flow']['error'] = 'Only PDF files are accepted.';
                    $_SESSION['nic_form_data'] = $_POST;
                    $_SESSION['nic_flow']['step'] = 'application';
                    header('Location: /nic.php');
                    exit;
                }

                $birthPdf = file_get_contents($_FILES['birthCertPdf']['tmp_name']);
                $policePdf = file_get_contents($_FILES['policeReportPdf']['tmp_name']);

                // Store all form data in session (don't save to database until payment is complete)
                $_SESSION['nic_form_data'] = [
                    'district' => $district,
                    'divSecretariat' => $divSecretariat,
                    'gramaNiladhari' => $gramaNiladhari,
                    'familyName' => $familyName,
                    'givenName' => $givenName,
                    'surname' => $surname,
                    'sex' => $sex,
                    'civilStatus' => $civilStatus,
                    'profession' => $profession,
                    'dob' => $dob,
                    'birthCertNo' => $birthCertNo,
                    'placeOfBirth' => $placeOfBirth,
                    'birthDivision' => $birthDivision,
                    'birthDistrict' => $birthDistrict,
                    'permHouse' => $permHouse,
                    'permBuildingType' => $permBuildingType,
                    'permStreet' => $permStreet,
                    'permCity' => $permCity,
                    'permPostal' => $permPostal,
                    'postHouse' => $postHouse,
                    'postBuildingType' => $postBuildingType,
                    'postStreet' => $postStreet,
                    'postCity' => $postCity,
                    'postPostal' => $postPostal,
                    'mobilePhone' => $mobilePhone,
                    'email' => $email,
                    'purpose' => $purpose,
                    'birthPdf' => base64_encode($birthPdf),
                    'policePdf' => base64_encode($policePdf),
                    'photoLink' => $photoLink
                ];

                // Go directly to payment step without saving to database
                $_SESSION['nic_flow']['step'] = 'payment';
                $_SESSION['nic_flow']['error'] = null;
            } catch (PDOException $e) {
                $_SESSION['nic_flow']['error'] = 'Application validation failed. Please try again.';
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
                if ($paymentId <= 0) {
                    $_SESSION['nic_flow']['error'] = 'Missing payment confirmation. Please contact support.';
                    header('Location: /nic.php');
                    exit;
                }

                // Get form data from session
                $formData = $_SESSION['nic_form_data'] ?? null;
                if (!$formData) {
                    $_SESSION['nic_flow']['error'] = 'Application data not found. Please start over.';
                    header('Location: /nic.php');
                    exit;
                }

                // Now save to database after successful payment
                $sql = "INSERT INTO identity_card_applications (
                    district, divisional_secretariat_division, grama_niladari_number_and_division,
                    family_name, name_, surname, sex, civil_status, profession, date_of_birth,
                    birth_certificate_no, place_of_birth, birth_division, birth_district,
                    perm_house_name_no, perm_building_type, perm_road_street, perm_village_city, perm_postal_code,
                    postal_house_name_no, postal_building_type, postal_road_street, postal_village_city, postal_postal_code,
                    phone_mobile, email, application_purpose, birth_certificate_pdf, police_report_doc, photo_link, payment_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $formData['district']);
                $stmt->bindValue(2, $formData['divSecretariat']);
                $stmt->bindValue(3, $formData['gramaNiladhari']);
                $stmt->bindValue(4, $formData['familyName']);
                $stmt->bindValue(5, $formData['givenName']);
                $stmt->bindValue(6, $formData['surname']);
                $stmt->bindValue(7, $formData['sex']);
                $stmt->bindValue(8, $formData['civilStatus']);
                $stmt->bindValue(9, $formData['profession']);
                $stmt->bindValue(10, $formData['dob']);
                $stmt->bindValue(11, $formData['birthCertNo']);
                $stmt->bindValue(12, $formData['placeOfBirth']);
                $stmt->bindValue(13, $formData['birthDivision']);
                $stmt->bindValue(14, $formData['birthDistrict']);
                $stmt->bindValue(15, $formData['permHouse']);
                $stmt->bindValue(16, $formData['permBuildingType']);
                $stmt->bindValue(17, $formData['permStreet']);
                $stmt->bindValue(18, $formData['permCity']);
                $stmt->bindValue(19, $formData['permPostal']);
                $stmt->bindValue(20, $formData['postHouse']);
                $stmt->bindValue(21, $formData['postBuildingType']);
                $stmt->bindValue(22, $formData['postStreet']);
                $stmt->bindValue(23, $formData['postCity']);
                $stmt->bindValue(24, $formData['postPostal']);
                $stmt->bindValue(25, $formData['mobilePhone']);
                $stmt->bindValue(26, $formData['email']);
                $stmt->bindValue(27, $formData['purpose']);
                $stmt->bindValue(28, base64_decode($formData['birthPdf']), PDO::PARAM_LOB);
                $stmt->bindValue(29, base64_decode($formData['policePdf']), PDO::PARAM_LOB);
                $stmt->bindValue(30, $formData['photoLink']);
                $stmt->bindValue(31, $paymentId);
                $stmt->execute();

                $applicationId = $pdo->lastInsertId();
                $_SESSION['nic_flow']['application_id'] = $applicationId;
                $_SESSION['nic_flow']['error'] = null;

                // Clear form data after successful save
                unset($_SESSION['nic_form_data']);
            } catch (PDOException $e) {
                // Log but don't expose sensitive info
                error_log("NIC Application Save Error: " . $e->getMessage());
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
