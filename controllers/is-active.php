<?php
include '../models/functions.php';

session_start();
$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
    exit;
}

$is_active = isActive($user_id);

if ($is_active) {
    echo json_encode(['status' => 'success', 'is_active' => $is_active,  'message' => 'User is active.']);
} else {
    echo json_encode(['status' => 'error', 'is_active' => $is_active, 'message' => 'User is not active.']);
}
