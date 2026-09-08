<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) { echo json_encode(['status' => 'error', 'message' => 'User ID is required.']); exit; }

$client = getCustomerIdFromUser($user_id);
if (!$client || !$client['Client_ID']) { echo json_encode(['status' => 'error', 'message' => 'Client profile not found.']); exit; }

$schedules = getCustomerSchedule($client['Client_ID']);
echo json_encode(['status' => 'success', 'schedules' => $schedules]);
