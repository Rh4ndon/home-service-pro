<?php
session_start();
header('Content-Type: application/json');
include '../models/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (empty($email) || empty($password) || empty($role)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if ($role === 'admin') {
    $user = findAdminByEmail($email);

    if (!$user || !password_verify($password, $user['Password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    $_SESSION['user_id'] = $user['ID'];
    $_SESSION['user_name'] = $user['Name'];
    $_SESSION['user_email'] = $user['Email'];
    $_SESSION['user_role'] = 'admin';

    echo json_encode([
        'success' => true,
        'id' => $user['ID'],
        'name' => $user['Name'],
        'email' => $user['Email'],
        'role' => 'admin'
    ]);
    exit;

} else {
    if (!in_array($role, ['customer', 'repairman'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid role']);
        exit;
    }

    $user = findUserByEmail($email);

    if (!$user || $user['Role'] !== $role || !password_verify($password, $user['Password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    if ($user['Status'] !== 'active') {
        http_response_code(403);
        if ($role === 'repairman') {
            echo json_encode(['error' => 'Your account is pending admin approval. You cannot access the system yet.', 'code' => 'pending_activation']);
        } else {
            echo json_encode(['error' => 'Account is not active', 'code' => 'inactive']);
        }
        exit;
    }

    $_SESSION['user_id'] = $user['ID'];
    $_SESSION['user_name'] = $user['Name'];
    $_SESSION['user_email'] = $user['Email'];
    $_SESSION['user_role'] = $user['Role'];

    echo json_encode([
        'success' => true,
        'id' => $user['ID'],
        'name' => $user['Name'],
        'email' => $user['Email'],
        'role' => $user['Role']
    ]);
    exit;
}
