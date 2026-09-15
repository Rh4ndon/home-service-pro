<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Use POST.']);
    exit;
}

$room_name  = isset($_POST['room_name']) ? trim($_POST['room_name']) : '';
$user_name  = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
$call_id    = isset($_POST['call_id'])   ? (int)$_POST['call_id']   : 0;

if (!$room_name) {
    echo json_encode(['success' => false, 'message' => 'Missing room_name.']);
    exit;
}

if (!$user_name) {
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $uid  = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $user_name = getUserDisplayNameById($uid, $role);
}

$call = $call_id ? getCallById($call_id) : getCallByRoomName($room_name);

// If the caller already ended/declined, block new joiners (except the caller re-joining)
if ($call && !in_array($call['Status'], ['ringing', 'active'])) {
    echo json_encode(['success' => false, 'message' => 'This call has already ended.']);
    exit;
}

$is_owner = $call ? (int)$call['Caller_ID'] === (int)($_POST['user_id'] ?? 0) : false;

$token_result = createDailyMeetingToken($room_name, $user_name, $is_owner);
if (isset($token_result['error']) || empty($token_result['token'])) {
    echo json_encode(['success' => false, 'message' => 'Failed to create meeting token: ' . (isset($token_result['error']) ? $token_result['error'] : 'unknown error')]);
    exit;
}

$room_url = $call ? $call['Room_URL'] : ('https://' . getDailySubdomain() . '/' . $room_name);

echo json_encode([
    'success'   => true,
    'token'     => $token_result['token'],
    'room_name' => $room_name,
    'room_url'  => $room_url,
]);