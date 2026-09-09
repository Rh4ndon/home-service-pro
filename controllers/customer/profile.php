<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) { echo json_encode(['status' => 'error', 'message' => 'User ID is required.']); exit; }

$profile = getCustomerProfileByUserId($user_id);
if (!$profile) { echo json_encode(['status' => 'error', 'message' => 'Profile not found.']); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Transform field names to match frontend expectations
    $response = [
        'name' => $profile['Name'],
        'full_name' => $profile['Name'],
        'email' => $profile['Email'],
        'address' => [
            'address_line1' => $profile['Address_Line1'] ?? '',
            'address_line2' => $profile['Address_Line2'] ?? '',
            'barangay' => $profile['Brgy'] ?? '',
            'city' => $profile['City_Min'] ?? '',
            'province' => $profile['Province'] ?? '',
            'region' => $profile['Region'] ?? '',
            'zip_code' => $profile['Zip_Code'] ?? '',
        ],
        'contact' => [
            'primary_mobile' => $profile['Prl_MobileNo'] ?? '',
            'primary_telephone' => $profile['Prl_TelNo'] ?? '',
            'secondary_mobile' => $profile['Sec_MobileNo'] ?? '',
            'secondary_telephone' => $profile['Sec_TelNo'] ?? '',
        ],
    ];
    echo json_encode(['status' => 'success', 'profile' => $response]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    updateCustomerProfile($profile['ID'], $name, $email);

    // Update address and contact fields directly on profile
    $stmt = $GLOBALS['conn']->prepare("
        UPDATE user_clientprofile 
        SET Address_Line1=?, Address_Line2=?, Brgy=?, City_Min=?, Province=?, Region=?, Zip_Code=?,
            Prl_MobileNo=?, Prl_TelNo=?, Sec_MobileNo=?, Sec_TelNo=?
        WHERE ID=?
    ");
    $stmt->bind_param("sssssssssssi",
        $_POST['address_line1'] ?? '',
        $_POST['address_line2'] ?? '',
        $_POST['barangay'] ?? '',
        $_POST['city'] ?? '',
        $_POST['province'] ?? '',
        $_POST['region'] ?? '',
        $_POST['zip_code'] ?? '',
        $_POST['primary_mobile'] ?? '',
        $_POST['primary_telephone'] ?? '',
        $_POST['secondary_mobile'] ?? '',
        $_POST['secondary_telephone'] ?? '',
        $profile['ID']
    );
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);