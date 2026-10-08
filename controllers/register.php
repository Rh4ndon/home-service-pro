<?php
session_start();
header('Content-Type: application/json');
include '../models/functions.php';
include 'mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (empty($name) || empty($email) || empty($password) || empty($role)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

$nameParts = preg_split('/\s+/', $name);
if (count($nameParts) < 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Please enter your full name (first and last name).']);
    exit;
}
foreach ($nameParts as $namePart) {
    if (!preg_match('/^[A-Za-z][A-Za-z.\'-]{1,}$/', $namePart)) {
        http_response_code(400);
        echo json_encode(['error' => 'Please enter a valid full name using letters only.']);
        exit;
    }
}

if (preg_match('/^\s*$/', $password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Password cannot be blank or whitespace only.']);
    exit;
}
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}
if (!preg_match('/[0-9]/', $password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must contain at least one number.']);
    exit;
}
if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must contain at least one symbol (e.g. !@#$).']);
    exit;
}

if (!in_array($role, ['customer', 'repairman'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Role must be customer or repairman']);
    exit;
}

if (($_POST['agree_terms'] ?? '') !== '1') {
    http_response_code(400);
    echo json_encode(['error' => 'You must read and agree to the Terms and Conditions to register.']);
    exit;
}

$latitude = trim($_POST['latitude'] ?? '');
$longitude = trim($_POST['longitude'] ?? '');
$formatted_address = trim($_POST['formatted_address'] ?? '');

if ($latitude === '' || $longitude === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Location is required. Please pin your address on the map or use your device location.']);
    exit;
}
if (!is_numeric($latitude) || !is_numeric($longitude) || (float) $latitude < -90 || (float) $latitude > 90 || (float) $longitude < -180 || (float) $longitude > 180) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid location coordinates.']);
    exit;
}

if ($role === 'repairman') {
    $mobile = trim($_POST['mobile'] ?? '');
    if (!isPhilippineMobile($mobile)) {
        http_response_code(400);
        echo json_encode(['error' => 'Please enter a valid Philippine mobile number (e.g. 09171234567).']);
        exit;
    }
    $mobile = normalizePhilippineMobile($mobile);
}

if (isEmailTaken($email)) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already exists']);
    exit;
}

$userId = createUser($name, $email, $password, $role, ($role === 'repairman' ? 'inactive' : 'active'));
saveTermsAcceptance($userId);

if ($role === 'customer') {
    insertRecord('user_clientprofile', [
        'Name' => $name,
        'Email' => $email,
        'User_ID' => $userId,
        'Latitude' => $latitude,
        'Longitude' => $longitude,
        'Formatted_Address' => $formatted_address ?: null
    ]);
}

if ($role === 'repairman') {
    $cert_entries = collectCertEntriesFromUploads();
    $certs_json = $cert_entries ? json_encode($cert_entries) : null;
    $education = trim($_POST['education'] ?? '');
    $skills = trim($_POST['skills'] ?? '');

    insertRecord('user_repairmanprofile', [
        'Name' => $name,
        'Email' => $email,
        'MobileNo' => $mobile,
        'Education' => $education ?: null,
        'Skills' => $skills ?: null,
        'Certifications' => $certs_json,
        'User_ID' => $userId,
        'Status' => 'inactive',
        'Latitude' => $latitude,
        'Longitude' => $longitude,
        'Formatted_Address' => $formatted_address ?: null
    ]);
}

$otp = generateOtp();
$sent = send_otp_email($email, $name, $otp, 'register');

echo json_encode([
    'success' => true,
    'id' => $userId,
    'name' => $name,
    'email' => $email,
    'role' => $role,
    'otp' => $otp,
    'otp_sent' => $sent,
    'pending_activation' => $role === 'repairman'
]);
