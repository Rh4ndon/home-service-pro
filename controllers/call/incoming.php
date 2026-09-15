<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id   = isset($_SESSION['user_id'])   ? (int)$_SESSION['user_id']   : (isset($_GET['user_id'])   ? (int)$_GET['user_id']   : 0);
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role']       : (isset($_GET['user_role']) ? trim($_GET['user_role']) : '');

if (!$user_id || !$user_role) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id or user_role.']);
    exit;
}

$call = getIncomingCall($user_id, $user_role);

if (!$call) {
    echo json_encode(['success' => false, 'has_incoming' => false]);
    exit;
}

echo json_encode([
    'success'      => true,
    'has_incoming' => true,
    'call' => [
        'call_id'     => (int)$call['ID'],
        'room_name'   => $call['Room_Name'],
        'room_url'    => $call['Room_URL'],
        'caller_id'   => (int)$call['Caller_ID'],
        'caller_role' => $call['Caller_Role'],
        'caller_name' => $call['Caller_Name'],
    ],
]);