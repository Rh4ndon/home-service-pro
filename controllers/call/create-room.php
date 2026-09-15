<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Use POST.']);
    exit;
}

$caller_id    = isset($_POST['caller_id'])    ? (int)$_POST['caller_id']    : 0;
$caller_role  = isset($_POST['caller_role'])  ? trim($_POST['caller_role'])  : '';
$callee_id    = isset($_POST['callee_id'])    ? (int)$_POST['callee_id']    : 0;
$callee_role  = isset($_POST['callee_role'])  ? trim($_POST['callee_role'])  : '';
$caller_name  = isset($_POST['caller_name'])  ? trim($_POST['caller_name'])  : '';
$callee_name  = isset($_POST['callee_name'])  ? trim($_POST['callee_name'])  : '';

if (!$caller_id || !$caller_role || !$callee_id || !$callee_role) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit;
}

$valid_roles = ['customer', 'repairman', 'admin'];
if (!in_array($caller_role, $valid_roles) || !in_array($callee_role, $valid_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role.']);
    exit;
}

if (!$caller_name) {
    $caller_name = getUserDisplayNameById($caller_id, $caller_role);
}
if (!$callee_name) {
    $callee_name = getUserDisplayNameById($callee_id, $callee_role);
}

// Create the Daily.co room
$room = createDailyRoom(2);
if (isset($room['error'])) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Failed to create call room: ' . $room['error']]);
    exit;
}

$room_name = $room['name'];
$room_url  = $room['url'];

// Create a meeting token for the caller
$token_result = createDailyMeetingToken($room_name, $caller_name, true);
if (isset($token_result['error']) || empty($token_result['token'])) {
    echo json_encode(['success' => false, 'message' => 'Failed to create meeting token: ' . (isset($token_result['error']) ? $token_result['error'] : 'unknown error')]);
    exit;
}

// Save the call record
$call_id = createCallRecord($caller_id, $caller_role, $caller_name, $callee_id, $callee_role, $callee_name, $room_name, $room_url);
if (!$call_id) {
    echo json_encode(['success' => false, 'message' => 'Failed to save call record.']);
    exit;
}

echo json_encode([
    'success'   => true,
    'call_id'   => (int)$call_id,
    'room_name' => $room_name,
    'room_url'  => $room_url,
    'token'     => $token_result['token'],
    'callee_name' => $callee_name,
]);