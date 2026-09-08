<?php
session_start();
header('Content-Type: application/json');
include '../models/functions.php';

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

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

if (!in_array($role, ['customer', 'repairman'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Role must be customer or repairman']);
    exit;
}

if (isEmailTaken($email)) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already exists']);
    exit;
}

$userId = createUser($name, $email, $password, $role);

if ($role === 'customer') {
    insertRecord('clientaddress', [
        'Address_Line1' => '',
        'City_Min' => '',
        'Province' => '',
        'Zip_Code' => ''
    ]);
    $clientAddId = mysqli_insert_id($conn);

    insertOrder('clientcontact', [
        'Prl_MobileNo' => null,
        'Prl_TelNo' => null,
        'Sec_MobileNo' => null,
        'Sec_TelNo' => null
    ]);
    $clientContactId = mysqli_insert_id($conn);

    insertRecord('user_clientprofile', [
        'Name' => $name,
        'Email' => $email,
        'ClientAdd_ID' => $clientAddId,
        'ClientContact_ID' => $clientContactId,
        'User_ID' => $userId
    ]);

} elseif ($role === 'repairman') {
    insertOrder('repairmanagerbackground', [
        'Skills' => null,
        'Education' => null,
        'Certifications' => null
    ]);
    $bgId = mysqli_insert_id($conn);

    insertRecord('user_repairmanprofile', [
        'Name' => $name,
        'Email' => $email,
        'RepairmanBG_ID' => $bgId,
        'User_ID' => $userId
    ]);
}

echo json_encode([
    'success' => true,
    'id' => $userId,
    'name' => $name,
    'email' => $email,
    'role' => $role
]);
