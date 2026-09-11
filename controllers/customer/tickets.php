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
    $tickets = getCustomerTickets($client_id);
    echo json_encode(['status' => 'success', 'tickets' => $tickets]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    // Accept both 'create' and 'book' for compatibility
    if ($action === 'create' || $action === 'book') {
        $date_time = trim(($_POST['preferred_date'] ?? '') . ' ' . ($_POST['preferred_time'] ?? ''));
        $repairman_id = isset($_POST['repairman_id']) && $_POST['repairman_id'] !== '' ? (int) $_POST['repairman_id'] : null;
        $client_lat = $_POST['client_lat'] !== '' ? $_POST['client_lat'] : null;
        $client_lng = $_POST['client_lng'] !== '' ? $_POST['client_lng'] : null;
        $route_distance_km = $_POST['route_distance_km'] !== '' ? $_POST['route_distance_km'] : null;
        $route_duration_min = $_POST['route_duration_min'] !== '' ? $_POST['route_duration_min'] : null;

        try {
            $result = createCustomerTicket(
                $client_id,
                $_POST['appliance_type'] ?? '',
                $_POST['appliance_name'] ?? '',
                $_POST['make_brand'] ?? '',
                $_POST['year'] ?? $_POST['year_model'] ?? '',
                $_POST['issue_desc'] ?? '',
                $date_time,
                $_POST['appliance_id'] ?? null,
                $repairman_id,
                $client_lat,
                $client_lng,
                $route_distance_km,
                $route_duration_min,
                $_POST['detailed_report'] ?? null
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Ticket created successfully.',
                'ticket_id' => $result['ticket_id'],
                'schedule_id' => $result['schedule_id']
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create ticket. ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'cancel') {
        $id = $_POST['ticket_id'] ?? $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Ticket ID is required.']); exit; }

        cancelCustomerTicket($id, $client_id);
        echo json_encode(['status' => 'success', 'message' => 'Ticket cancelled successfully.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
