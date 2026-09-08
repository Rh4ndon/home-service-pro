<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) { echo json_encode(['status' => 'error', 'message' => 'User ID is required.']); exit; }

$profile = getCustomerProfileByUserId($user_id);
if (!$profile) { echo json_encode(['status' => 'error', 'message' => 'Profile not found.']); exit; }

$address = $profile['ClientAdd_ID'] ? getClientAddressById($profile['ClientAdd_ID']) : null;
$contact = $profile['ClientContact_ID'] ? getClientContactById($profile['ClientContact_ID']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Transform field names to match frontend expectations
    $response = [
        'name' => $profile['Name'],
        'full_name' => $profile['Name'],
        'email' => $profile['Email'],
        'address' => $address ? [
            'address_line1' => $address['Address_Line1'],
            'address_line2' => $address['Address_Line2'],
            'barangay' => $address['Brgy'],
            'city' => $address['City_Min'],
            'province' => $address['Province'],
            'region' => $address['Region'],
            'zip_code' => $address['Zip_Code'],
        ] : [],
        'contact' => $contact ? [
            'primary_mobile' => $contact['Prl_MobileNo'],
            'primary_telephone' => $contact['Prl_TelNo'],
            'secondary_mobile' => $contact['Sec_MobileNo'],
            'secondary_telephone' => $contact['Sec_TelNo'],
        ] : [],
    ];
    echo json_encode(['status' => 'success', 'profile' => $response]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    updateCustomerProfile($profile['ID'], $name, $email);

    if ($profile['ClientAdd_ID']) {
        updateClientAddress(
            $profile['ClientAdd_ID'],
            $_POST['address_line1'] ?? '',
            $_POST['address_line2'] ?? '',
            $_POST['barangay'] ?? '',
            $_POST['city'] ?? '',
            $_POST['province'] ?? '',
            $_POST['region'] ?? '',
            $_POST['zip_code'] ?? ''
        );
    }

    if ($profile['ClientContact_ID']) {
        updateClientContact(
            $profile['ClientContact_ID'],
            $_POST['primary_mobile'] ?? '',
            $_POST['primary_telephone'] ?? '',
            $_POST['secondary_mobile'] ?? '',
            $_POST['secondary_telephone'] ?? ''
        );
    }

    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
