<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
    exit;
}

$client = getCustomerIdFromUser($user_id);
if (!$client || !$client['Client_ID']) {
    echo json_encode(['status' => 'error', 'message' => 'Client profile not found.']);
    exit;
}

$ticket_id = (int)($_GET['ticket_id'] ?? $_POST['ticket_id'] ?? 0);
if (!$ticket_id) {
    echo json_encode(['status' => 'error', 'message' => 'Ticket ID is required.']);
    exit;
}

// Make sure the ticket belongs to this customer before exposing the estimate.
$ticket = getRecord('repairticket', "ID = $ticket_id AND Client_ID = " . (int)$client['Client_ID']);
if (!$ticket) {
    echo json_encode(['status' => 'error', 'message' => 'Ticket not found.']);
    exit;
}

$estimate = getLatestRepairEstimate($ticket_id);
if (!$estimate) {
    echo json_encode(['status' => 'error', 'message' => 'No estimate has been provided for this ticket yet.']);
    exit;
}

$estimate['Ticket_ID'] = (int)$estimate['Ticket_ID'];
$estimate['Schedule_ID'] = $estimate['Schedule_ID'] !== null ? (int)$estimate['Schedule_ID'] : null;
$estimate['Repairman_ID'] = (int)$estimate['Repairman_ID'];
$estimate['Total'] = (float)($estimate['Total'] ?? 0);
if (!isset($estimate['Items']) || !is_array($estimate['Items'])) {
    $estimate['Items'] = [];
}

echo json_encode(['status' => 'success', 'estimate' => $estimate]);
