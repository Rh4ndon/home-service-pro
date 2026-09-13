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

            // Store booking media (3 images + 1 video) if uploaded
            $ticket_id = $result['ticket_id'];
            $upload_dir = dirname(__DIR__, 2) . '/uploads/booking/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if (!is_writable($upload_dir)) @chmod($upload_dir, 0777);

            $saved = [];
            for ($i = 1; $i <= 3; $i++) {
                $field = 'image_' . $i;
                if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                    $allowed_img = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (in_array($ext, $allowed_img)) {
                        $fname = 't' . $ticket_id . '_img' . $i . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES[$field]['tmp_name'], $upload_dir . $fname)) {
                            saveBookingMedia($ticket_id, 'image', 'uploads/booking/' . $fname, $fname);
                        }
                    }
                }
            }

            // Video - limit to common web formats
            if (!empty($_FILES['video']['name']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
                $allowed_vid = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
                if (in_array($ext, $allowed_vid)) {
                    $fname = 't' . $ticket_id . '_vid_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['video']['tmp_name'], $upload_dir . $fname)) {
                        saveBookingMedia($ticket_id, 'video', 'uploads/booking/' . $fname, $fname);
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Ticket created successfully.',
                'ticket_id' => $result['ticket_id'],
                'schedule_id' => $result['schedule_id'],
                'media_saved' => count($saved)
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

    if ($action === 'complete') {
        $id = $_POST['ticket_id'] ?? $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Ticket ID is required.']); exit; }

        completeCustomerTicket($id, $client_id);
        echo json_encode(['status' => 'success', 'message' => 'Ticket marked as completed.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
