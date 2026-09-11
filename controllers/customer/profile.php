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
        'latitude' => $profile['Latitude'] ?? null,
        'longitude' => $profile['Longitude'] ?? null,
        'formatted_address' => $profile['Formatted_Address'] ?? '',
        'contact' => [
            'primary_mobile' => $profile['Prl_MobileNo'] ?? '',
        ],
    ];
    echo json_encode(['status' => 'success', 'profile' => $response]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    updateCustomerProfile($profile['ID'], $name, $email);

    // Update mobile only
    $stmt = $GLOBALS['conn']->prepare("
        UPDATE user_clientprofile 
        SET Prl_MobileNo=?
        WHERE ID=?
    ");
    $pm = $_POST['primary_mobile'] ?? '';
    $stmt->bind_param("si", $pm, $profile['ID']);
    $stmt->execute();

    // Update geographic coordinates (map pin) separately
    updateCustomerLocation(
        $profile['ID'],
        $_POST['latitude'] ?? '',
        $_POST['longitude'] ?? '',
        $_POST['formatted_address'] ?? ''
    );

    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);