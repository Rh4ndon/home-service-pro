<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) { echo json_encode(['status' => 'error', 'message' => 'User ID is required.']); exit; }

$client = getCustomerIdFromUser($user_id);
if (!$client || !$client['Client_ID']) { echo json_encode(['status' => 'error', 'message' => 'Client profile not found.']); exit; }

$lat = $_GET['lat'] ?? $_POST['lat'] ?? null;
$lng = $_GET['lng'] ?? $_POST['lng'] ?? null;

if ($lat === null || $lng === null || $lat === '' || $lng === '') {
    echo json_encode(['status' => 'error', 'message' => 'Latitude and longitude are required.']);
    exit;
}

$radius = max(1, min(30, (float) ($_GET['radius_km'] ?? $_POST['radius_km'] ?? 10)));
$skill = trim($_GET['skill'] ?? $_POST['skill'] ?? '') ?: null;

$result = findNearbyRepairmen((float) $lat, (float) $lng, $radius, $skill);

echo json_encode([
    'status' => 'success',
    'method' => $result['method'],
    'radius_km' => $result['radius_km'],
    'count' => count($result['repairmen']),
    'repairmen' => $result['repairmen']
]);