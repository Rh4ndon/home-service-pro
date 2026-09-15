<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$call_id = isset($_GET['call_id']) ? (int)$_GET['call_id'] : 0;
if (!$call_id) {
    echo json_encode(['success' => false, 'message' => 'Missing call_id.']);
    exit;
}

$call = getCallById($call_id);
if (!$call) {
    echo json_encode(['success' => false, 'message' => 'Call not found.']);
    exit;
}

echo json_encode([
    'success' => true,
    'call' => [
        'call_id'      => (int)$call['ID'],
        'room_name'    => $call['Room_Name'],
        'room_url'     => $call['Room_URL'],
        'status'       => $call['Status'],
        'caller_name'  => $call['Caller_Name'],
        'callee_name'  => $call['Callee_Name'],
        'created_at'   => $call['Created_At'],
        'started_at'   => $call['Started_At'],
        'ended_at'     => $call['Ended_At'],
    ],
]);