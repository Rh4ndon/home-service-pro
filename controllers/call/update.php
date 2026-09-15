<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Use POST.']);
    exit;
}

$call_id = isset($_POST['call_id']) ? (int)$_POST['call_id'] : 0;
$status  = isset($_POST['status'])  ? trim($_POST['status'])  : '';

if (!$call_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing call_id or status.']);
    exit;
}

// Only allow 'answered' to transition ringing -> active
// We accept 'answered' | 'ended' | 'declined' | 'missed' | 'ringing'
$normalized = $status;
if ($status === 'answered') {
    $normalized = 'active';
}

$valid = ['ringing', 'active', 'ended', 'declined', 'missed'];
if (!in_array($normalized, $valid)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status.']);
    exit;
}

$call = getCallById($call_id);
if (!$call) {
    echo json_encode(['success' => false, 'message' => 'Call not found.']);
    exit;
}

// Prevent a stale declined/missed/ended call from flipping back to active/ringing
if (in_array($call['Status'], ['ended', 'declined', 'missed']) && in_array($normalized, ['active', 'ringing'])) {
    echo json_encode(['success' => false, 'message' => 'Call already finished.']);
    exit;
}

if (updateCallStatus($call_id, $normalized)) {
    echo json_encode([
        'success' => true,
        'status'  => $normalized,
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update call status.']);
}