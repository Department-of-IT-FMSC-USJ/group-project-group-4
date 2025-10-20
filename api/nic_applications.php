<?php


// Include required files
require_once _DIR_ . '/../config/database.php';
require_once _DIR_ . '/../include/functions.php';

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Route requests
try {
    // Check if this is a PDF download request
    if ($method === 'GET' && isset($_GET['pdf'])) {
        downloadPDF();
        exit;
    }

    switch ($method) {
        case 'POST':
            submitApplication();
            break;

        case 'GET':
            $id = $_GET['id'] ?? $_GET['applicationId'] ?? $_GET['application_id'] ?? null;
            if ($id) {
                getApplication($id);
            } else {
                getAllApplications();
            }
            break;

        case 'PUT':
            // Because PHP doesn't populate $_PUT by default, use request data helper
            updateApplicationPayment();
            break;

        case 'DELETE':
            deleteApplication();
            break;

        default:
            sendResponse(false, "Method not allowed");
    }
} catch (Exception $e) {
    logError("NIC Application API Error: " . $e->getMessage());
    sendResponse(false, "Server error occurred");
}


function submitApplication()
{
    global $pdo;

    $input = get_request_data();
    $familyName = $input['family_name'] ?? $input['familyName'] ?? '';
    $givenName = $input['name_'] ?? $input['givenName'] ?? '';
    $dob = $input['date_of_birth'] ?? $input['dob'] ?? '';
    $birthCertNo = $input['birth_certificate_no'] ?? $input['birthCertNo'] ?? '';
    $purpose = $input['application_purpose'] ?? $input['applicationPurpose'] ?? '';

    if (empty($familyName) || empty($givenName) || empty($dob) || empty($birthCertNo) || empty($purpose)) {
        sendResponse(false, "Missing required fields: family name, given name, date of birth, birth certificate number, purpose");
    }

    $stmt = $pdo->prepare('SELECT 1 FROM birth_certificates WHERE birth_certificate_number = ?');
    $stmt->execute([$birthCertNo]);
    if (!$stmt->fetchColumn()) {
        sendResponse(false, 'Birth certificate number not found in records');
    }

    $validPurposes = ['Lost', 'Changes', 'Renew', 'Damaged'];
    if (!in_array($purpose, $validPurposes)) {
        $purposeLower = strtolower($purpose);
        if (strpos($purposeLower, 'lost') !== false) $purpose = 'Lost';
        elseif (strpos($purposeLower, 'renew') !== false) $purpose = 'Renew';
        elseif (strpos($purposeLower, 'damage') !== false) $purpose = 'Damaged';
        else $purpose = 'Changes';
    }

    $district = $input['district'] ?? 'Colombo';
    $divSec = $input['divisional_secretariat_division'] ?? $input['divSecretariat'] ?? 'Colombo';
    $gramaNila = $input['grama_niladari_number_and_division'] ?? $input['gramaNiladhari'] ?? 'GN001';
    $permHouse = $input['perm_house_name_no'] ?? $input['permHouse'] ?? 'N/A';
    $permBuildingType = $input['perm_building_type'] ?? $input['permBuildingType'] ?? 'House';
    $permStreet = $input['perm_road_street'] ?? $input['permStreet'] ?? 'Main Road';
    $permCity = $input['perm_village_city'] ?? $input['permCity'] ?? 'Colombo';
    $permPostal = $input['perm_postal_code'] ?? $input['permPostal'] ?? '00100';
    $postHouse = $input['postal_house_name_no'] ?? $input['postHouse'] ?? $permHouse;
    $postBuildingType = $input['postal_building_type'] ?? $input['postBuildingType'] ?? $permBuildingType;
    $postStreet = $input['postal_road_street'] ?? $input['postStreet'] ?? $permStreet;
    $postCity = $input['postal_village_city'] ?? $input['postCity'] ?? $permCity;
    $postPostal = $input['postal_postal_code'] ?? $input['postPostal'] ?? $permPostal;
    $surname = $input['surname'] ?? '';
    $sex = $input['sex'] ?? 'Male';
    $civilStatus = $input['civil_status'] ?? 'Single';
    $profession = $input['profession'] ?? 'Student';
    $placeOfBirth = $input['place_of_birth'] ?? $input['placeOfBirth'] ?? 'Colombo';
    $birthDivision = $input['birth_division'] ?? $input['birthDivision'] ?? 'Colombo';
    $birthDistrict = $input['birth_district'] ?? $input['birthDistrict'] ?? 'Colombo';
    $phone = $input['phone_mobile'] ?? $input['mobile_phone'] ?? '';
    $email = $input['email'] ?? '';
    $fakePdf = base64_encode("Demo PDF content for " . $givenName);
    $photoLink = $input['photo_link'] ?? 'demo-photo-' . time() . '.jpg';

    $sql = "INSERT INTO identity_card_applications (
        district, divisional_secretariat_division, grama_niladari_number_and_division,
        family_name, name_, surname, sex, civil_status, profession, date_of_birth,
        birth_certificate_no, place_of_birth, birth_division, birth_district,
        perm_house_name_no, perm_building_type, perm_road_street, perm_village_city, perm_postal_code,
        postal_house_name_no, postal_building_type, postal_road_street, postal_village_city, postal_postal_code,
        phone_mobile, email, application_purpose, birth_certificate_pdf, photo_pdf, photo_link
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        $district,
        $divSec,
        $gramaNila,
        $familyName,
        $givenName,
        $surname,
        $sex,
        $civilStatus,
        $profession,
        $dob,
        $birthCertNo,
        $placeOfBirth,
        $birthDivision,
        $birthDistrict,
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
        $phone,
        $email,
        $purpose,
        $fakePdf,
        $fakePdf,
        $photoLink
    ])) {
        $applicationId = $pdo->lastInsertId();
        sendResponse(true, 'NIC application submitted successfully', [
            'application_id' => $applicationId,
            'status' => 'Processing',
            'estimated_completion' => date('Y-m-d', strtotime('+14 days'))
        ]);
    } else {
        sendResponse(false, "Application submission failed");
    }
}



function getApplication($id)
{
    global $pdo;

    $sql = "SELECT ia.*, p.status as payment_status, p.amount as payment_amount 
            FROM identity_card_applications ia 
            LEFT JOIN payments p ON ia.payment_id = p.payment_id 
            WHERE ia.application_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($application) {
        unset($application['birth_certificate_pdf']);
        unset($application['photo_pdf']);
        unset($application['police_report_doc']);
        sendResponse(true, "Application found", $application);
    } else {
        sendResponse(false, "Application not found");
    }
}


function getAllApplications()
{
    global $pdo;

    $sql = "SELECT 
                ia.application_id, ia.family_name, ia.name_, ia.surname,
                ia.application_purpose, ia.application_date,
                p.status as payment_status 
            FROM identity_card_applications ia 
            LEFT JOIN payments p ON ia.payment_id = p.payment_id 
            ORDER BY ia.application_date DESC 
            LIMIT 50";
    $stmt = $pdo->query($sql);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendResponse(true, "All applications retrieved", $applications);
}


function updateApplicationPayment()
{
    global $pdo;

    $input = get_request_data();
    $applicationId = $input['application_id'] ?? $input['applicationId'] ?? null;
    $paymentId = $input['payment_id'] ?? $input['paymentId'] ?? null;
    if (!$applicationId || !$paymentId) {
        sendResponse(false, "Application ID and Payment ID required");
    }
    $stmt = $pdo->prepare("UPDATE identity_card_applications SET payment_id = ? WHERE application_id = ?");
    if ($stmt->execute([$paymentId, $applicationId])) {
        if ($stmt->rowCount() > 0) {
            sendResponse(true, "Application linked to payment");
        } else {
            sendResponse(false, "Application not found");
        }
    } else {
        sendResponse(false, "Failed to update application");
    }
}


function deleteApplication()
{
    global $pdo;

    $input = get_request_data();
    $applicationId = $input['application_id'] ?? $input['applicationId'] ?? null;
    if (!$applicationId) {
        sendResponse(false, "Application ID required");
    }
    $stmt = $pdo->prepare("DELETE FROM identity_card_applications WHERE application_id = ?");
    if ($stmt->execute([$applicationId])) {
        if ($stmt->rowCount() > 0) {
            sendResponse(true, "Application deleted");
        } else {
            sendResponse(false, "Application not found");
        }
    } else {
        sendResponse(false, "Failed to delete application");
    }
}


function downloadPDF()
{
    global $pdo;

    $applicationId = $_GET['id'] ?? null;
    $type = $_GET['type'] ?? null;

    if (!$applicationId || !$type) {
        http_response_code(400);
        die("Missing required parameters: id and type");
    }

    // Validate type
    $validTypes = ['birth_certificate', 'police_report', 'photo'];
    if (!in_array($type, $validTypes)) {
        http_response_code(400);
        die("Invalid document type. Valid types: " . implode(', ', $validTypes));
    }

    // Map type to database column
    $columnMap = [
        'birth_certificate' => 'birth_certificate_pdf',
        'police_report' => 'police_report_doc',
        'photo' => 'photo_pdf'
    ];

    $column = $columnMap[$type];

    // Fetch the PDF from database
    $sql = "SELECT {$column}, family_name, name_ FROM identity_card_applications WHERE application_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicationId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || empty($result[$column])) {
        http_response_code(404);
        die("Document not found");
    }

    // Generate filename
    $name = $result['family_name'] . '' . $result['name'];
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    $filename = $name . '' . $type . '' . $applicationId . '.pdf';

    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($result[$column]));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    // Output the PDF
    echo $result[$column];
    exit;
}
