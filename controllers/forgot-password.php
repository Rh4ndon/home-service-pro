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

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
$action = $_POST['action'] ?? '';

if ($action == 'find-email') {
    if (empty($email) || empty($role)) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    $otp = generateOtp();

    if (!in_array($role, ['customer', 'repairman'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid role']);
        exit;
    }
    $user = findUserByEmail($email);
    if (!$user || $user['Role'] !== $role) {
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

    send_otp_email($user['Email'], $user['Name'], $otp, 'login');


    echo json_encode([
        'success' => true,
        'name' => $user['Name'],
        'email' => $user['Email'],
        'otp' => $otp,
        'role' => $user['Role']
    ]);
} else if ($action == 'reset-password') {
    if (empty($email) || empty($role) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    if (!in_array($role, ['customer', 'repairman'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid role']);
        exit;
    }

    $user = findUserByEmail($email);
    if (!$user || $user['Role'] !== $role) {
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

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $result = editRecord('users', ['Password' => $hashedPassword], "Email = '$email' AND Role = '$role'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}



exit;
