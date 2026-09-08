<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) { echo json_encode(['status' => 'error', 'message' => 'User ID is required.']); exit; }

$client = getCustomerIdFromUser($user_id);
if (!$client || !$client['Client_ID']) { echo json_encode(['status' => 'error', 'message' => 'Client profile not found.']); exit; }

$client_id = $client['Client_ID'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appliances = getCustomerAppliances($client_id);
    echo json_encode(['status' => 'success', 'appliances' => $appliances]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $id = addCustomerAppliance(
            $client_id,
            $_POST['type'] ?? $_POST['appliance_type'] ?? '',
            $_POST['name'] ?? $_POST['appliance_name'] ?? '',
            $_POST['make'] ?? $_POST['make_brand'] ?? '',
            $_POST['year'] ?? $_POST['year_model'] ?? '',
            $_POST['details'] ?? ''
        );
        echo $id
            ? json_encode(['status' => 'success', 'message' => 'Appliance added successfully.', 'id' => $id])
            : json_encode(['status' => 'error', 'message' => 'Failed to add appliance.']);
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Appliance ID is required.']); exit; }

        updateCustomerAppliance(
            $id, $client_id,
            $_POST['type'] ?? $_POST['appliance_type'] ?? '',
            $_POST['name'] ?? $_POST['appliance_name'] ?? '',
            $_POST['make'] ?? $_POST['make_brand'] ?? '',
            $_POST['year'] ?? $_POST['year_model'] ?? '',
            $_POST['details'] ?? ''
        );
        echo json_encode(['status' => 'success', 'message' => 'Appliance updated successfully.']);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Appliance ID is required.']); exit; }

        deleteCustomerAppliance($id, $client_id);
        echo json_encode(['status' => 'success', 'message' => 'Appliance deleted successfully.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
