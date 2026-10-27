<?php
session_start();
include '../../models/functions.php';
blockIfRepairmanLocked(); // locked repairmen may only use the subscription page

header('Content-Type: application/json');

function get_user_id() {
    if (!empty($_SESSION['user_id'])) return $_SESSION['user_id'];
    if (!empty($_GET['user_id'])) return $_GET['user_id'];
    if (!empty($_POST['user_id'])) return $_POST['user_id'];
    return null;
}

function handle_get() {
    $user_id = get_user_id();
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "User ID is required."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    $action = $_GET['action'] ?? '';

    if ($action === 'get_estimate') {
        $schedule_id = (int)($_GET['schedule_id'] ?? 0);
        if (!$schedule_id) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Schedule ID is required."]);
            return;
        }

        $estimate = getRepairEstimateBySchedule($schedule_id, $repairman_id);
        if (!$estimate) {
            echo json_encode(["success" => false, "message" => "No estimate found for this schedule."]);
            return;
        }

        $estimate['Ticket_ID'] = (int)$estimate['Ticket_ID'];
        $estimate['Schedule_ID'] = $estimate['Schedule_ID'] !== null ? (int)$estimate['Schedule_ID'] : null;
        $estimate['Repairman_ID'] = (int)$estimate['Repairman_ID'];
        $estimate['Total'] = (float)($estimate['Total'] ?? 0);
        if (!isset($estimate['Items']) || !is_array($estimate['Items'])) {
            $estimate['Items'] = [];
        }

        echo json_encode(["success" => true, "estimate" => $estimate]);
        return;
    }

    $schedules = getRepairmanSchedule($repairman_id);
    echo json_encode(["success" => true, "data" => $schedules]);
}

function handle_post() {
    $user_id = get_user_id();
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "User ID is required."]);
        return;
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'accept':
            handle_accept($user_id);
            break;
        case 'decline':
            handle_decline($user_id);
            break;
        case 'complete':
            handle_complete($user_id);
            break;
        case 'update_estimate':
            handle_update_estimate($user_id);
            break;
        default:
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
}

function handle_accept($user_id) {
    $schedule_id = $_POST['schedule_id'] ?? '';

    if (empty($schedule_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Schedule ID is required."]);
        return;
    }

    $materials_list = $_POST['materials'] ?? [];
    $service_list = $_POST['service'] ?? [];
    $price_list = $_POST['price'] ?? [];

    if (!is_array($materials_list)) $materials_list = [$materials_list];
    if (!is_array($service_list)) $service_list = [$service_list];
    if (!is_array($price_list)) $price_list = [$price_list];

    $line_items = [];
    $row_count = max(count($materials_list), count($service_list), count($price_list));
    for ($i = 0; $i < $row_count; $i++) {
        $materials = trim($materials_list[$i] ?? '');
        $service = trim($service_list[$i] ?? '');
        $price = $price_list[$i] ?? '';

        // Ignore completely empty rows (e.g. a row the repairman added then left blank).
        if ($materials === '' && $service === '' && trim((string)$price) === '') continue;

        if ($materials === '' || $service === '' || !is_numeric($price) || (float)$price < 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Please complete the materials, service, and a valid price for every estimate row."]);
            return;
        }

        $line_items[] = [
            'materials' => $materials,
            'service' => $service,
            'price' => (float)$price
        ];
    }

    if (!$line_items) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Please add at least one estimate row."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    global $conn;
    startTransaction();
    try {
        // Make sure the schedule belongs to this repairman and is still actionable.
        $details = getRepairScheduleDetails($schedule_id, $repairman_id);
        if (!$details) {
            throw new Exception('Schedule not found or not assigned to you.');
        }

        $current_status = strtolower($details['schedule_status'] ?? '');
        if (in_array($current_status, ['cancelled', 'declined', 'completed'], true)) {
            throw new Exception('This appointment can no longer be accepted.');
        }

        // Update repairschedule
        $stmt = $conn->prepare("UPDATE repairschedule SET Status = 'In Progress' WHERE ID = ? AND Repairman_ID = ?");
        $stmt->bind_param("si", $schedule_id, $repairman_id);
        $stmt->execute();

        // Also update repairticket
        $stmt = $conn->prepare("UPDATE repairticket rt JOIN repairschedule rs ON rt.Schedule_ID = rs.ID SET rt.Status = 'In Progress', rt.Repairman_ID = ? WHERE rs.ID = ? AND rs.Repairman_ID = ?");
        $stmt->bind_param("iii", $repairman_id, $schedule_id, $repairman_id);
        $stmt->execute();

        // Create the estimate that will be shared with the customer.
        $appliance_name = trim($details['appliance_name'] ?? '');
        if ($appliance_name === '') $appliance_name = 'Appliance';
        $ticket_id = (int)$details['ticket_id'];
        $estimate_id = createRepairEstimate(
            $ticket_id,
            (int)$schedule_id,
            $repairman_id,
            $appliance_name
        );

        foreach ($line_items as $index => $item) {
            addRepairEstimateItem($estimate_id, $item['materials'], $item['service'], $item['price'], $index);
        }

        // Include the estimate in the chat so the customer sees it immediately.
        $client_user_id = (int)($details['client_user_id'] ?? 0);
        if ($client_user_id) {
            $lines = [];
            $total = 0;
            foreach ($line_items as $index => $item) {
                $line_total = (float)$item['price'];
                $total += $line_total;
                $lines[] = ($index + 1) . ". {$item['materials']} | {$item['service']} | ₱" . number_format($line_total, 2);
            }

            $message = "Hello! I have accepted your repair appointment and prepared an estimate.\n\n"
                . "Ticket #{$ticket_id}\n"
                . "Appliance: {$appliance_name}\n\n"
                . implode("\n", $lines) . "\n\n"
                . "Total Estimated Price: ₱" . number_format($total, 2) . "\n\n"
                . "You can download the estimate PDF from this chat or from My Tickets.";
            sendChatMessage($user_id, 'repairman', $client_user_id, 'customer', $message, 'estimate', $ticket_id);
        }

        commitTransaction();
        echo json_encode([
            "success" => true,
            "message" => "Schedule accepted successfully.",
            "estimate_id" => $estimate_id,
            "ticket_id" => $ticket_id
        ]);
    } catch (Exception $e) {
        rollbackTransaction();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

function handle_decline($user_id) {
    $schedule_id = $_POST['schedule_id'] ?? '';

    if (empty($schedule_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Schedule ID is required."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    global $conn;
    startTransaction();
    try {
        // Update repairschedule
        $stmt = $conn->prepare("UPDATE repairschedule SET Status = 'Declined' WHERE ID = ? AND Repairman_ID = ?");
        $stmt->bind_param("si", $schedule_id, $repairman_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Also update repairticket - set status to Declined and clear Repairman_ID so it can be reassigned
            $stmt = $conn->prepare("UPDATE repairticket rt JOIN repairschedule rs ON rt.Schedule_ID = rs.ID SET rt.Status = 'Declined', rt.Repairman_ID = NULL WHERE rs.ID = ? AND rs.Repairman_ID = ?");
            $stmt->bind_param("ii", $schedule_id, $repairman_id);
            $stmt->execute();
        }

        commitTransaction();
        echo json_encode(["success" => true, "message" => "Schedule declined successfully."]);
    } catch (Exception $e) {
        rollbackTransaction();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

function handle_complete($user_id) {
    $schedule_id = $_POST['schedule_id'] ?? '';

    if (empty($schedule_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Schedule ID is required."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    global $conn;
    startTransaction();
    try {
        // Get ticket_id from schedule
        $stmt = $conn->prepare("SELECT rt.ID as ticket_id, rt.Appliance_ID FROM repairticket rt JOIN repairschedule rs ON rt.Schedule_ID = rs.ID WHERE rs.ID = ? AND rs.Repairman_ID = ?");
        $stmt->bind_param("ii", $schedule_id, $repairman_id);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();

        if (!$ticket) {
            throw new Exception('Schedule not found or not assigned to you.');
        }

        $ticket_id = $ticket['ticket_id'];
        $appliance_id = $ticket['Appliance_ID'] ?? 0;

        // Update repairschedule
        $stmt = $conn->prepare("UPDATE repairschedule SET Status = 'Completed' WHERE ID = ? AND Repairman_ID = ?");
        $stmt->bind_param("si", $schedule_id, $repairman_id);
        $stmt->execute();

        // Update repairticket
        $stmt = $conn->prepare("UPDATE repairticket SET Status = 'Completed' WHERE ID = ? AND Repairman_ID = ?");
        $stmt->bind_param("ii", $ticket_id, $repairman_id);
        $stmt->execute();

        if (!$appliance_id) {
            $stmt = $conn->prepare("SELECT ID FROM clientappliances WHERE Issue_ID = (SELECT ID FROM applianceissue WHERE Ticket_ID = ?)");
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $appliance = $stmt->get_result()->fetch_assoc();
            $appliance_id = $appliance ? $appliance['ID'] : 0;
        }

        insertRecord('repairhistory', [
            'Repairman_ID' => $repairman_id,
            'Schedule_ID' => $schedule_id,
            'Ticket_ID' => $ticket_id,
            'ClientAppliances_ID' => $appliance_id,
            'Status' => 'Completed'
        ]);

        commitTransaction();
        echo json_encode(["success" => true, "message" => "Schedule completed successfully."]);
    } catch (Exception $e) {
        rollbackTransaction();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

function handle_update_estimate($user_id) {
    $schedule_id = $_POST['schedule_id'] ?? '';
    $estimate_id = $_POST['estimate_id'] ?? '';

    if (empty($schedule_id) || empty($estimate_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Schedule ID and Estimate ID are required."]);
        return;
    }

    $materials_list = $_POST['materials'] ?? [];
    $service_list = $_POST['service'] ?? [];
    $price_list = $_POST['price'] ?? [];

    if (!is_array($materials_list)) $materials_list = [$materials_list];
    if (!is_array($service_list)) $service_list = [$service_list];
    if (!is_array($price_list)) $price_list = [$price_list];

    $line_items = [];
    $row_count = max(count($materials_list), count($service_list), count($price_list));
    for ($i = 0; $i < $row_count; $i++) {
        $materials = trim($materials_list[$i] ?? '');
        $service = trim($service_list[$i] ?? '');
        $price = $price_list[$i] ?? '';

        // Ignore completely empty rows
        if ($materials === '' && $service === '' && trim((string)$price) === '') continue;

        if ($materials === '' || $service === '' || !is_numeric($price) || (float)$price < 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Please complete the materials, service, and a valid price for every estimate row."]);
            return;
        }

        $line_items[] = [
            'materials' => $materials,
            'service' => $service,
            'price' => (float)$price
        ];
    }

    if (!$line_items) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Please add at least one estimate row."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    global $conn;
    startTransaction();
    try {
        // Verify schedule belongs to this repairman and is in progress
        $details = getRepairScheduleDetails($schedule_id, $repairman_id);
        if (!$details) {
            throw new Exception('Schedule not found or not assigned to you.');
        }

        $current_status = strtolower($details['schedule_status'] ?? '');
        $current_status = str_replace(' ', '_', $current_status);
        if (!in_array($current_status, ['in_progress', 'progress'], true)) {
            throw new Exception('Estimate can only be updated while the appointment is In Progress.');
        }

        // Update the estimate
        updateRepairEstimate((int)$estimate_id, $repairman_id, $line_items);

        // Send updated estimate to chat
        $ticket_id = (int)$details['ticket_id'];
        $client_user_id = (int)($details['client_user_id'] ?? 0);
        if ($client_user_id) {
            $appliance_name = trim($details['appliance_name'] ?? '');
            if ($appliance_name === '') $appliance_name = 'Appliance';

            $lines = [];
            $total = 0;
            foreach ($line_items as $index => $item) {
                $line_total = (float)$item['price'];
                $total += $line_total;
                $lines[] = ($index + 1) . ". {$item['materials']} | {$item['service']} | ₱" . number_format($line_total, 2);
            }

            $message = "Hello! I have updated the estimate for your repair appointment.\n\n"
                . "Ticket #{$ticket_id}\n"
                . "Appliance: {$appliance_name}\n\n"
                . implode("\n", $lines) . "\n\n"
                . "Total Estimated Price: ₱" . number_format($total, 2) . "\n\n"
                . "You can download the updated estimate PDF from this chat or from My Tickets.";
            sendChatMessage($user_id, 'repairman', $client_user_id, 'customer', $message, 'estimate', $ticket_id);
        }

        commitTransaction();
        echo json_encode([
            "success" => true,
            "message" => "Estimate updated successfully.",
            "estimate_id" => $estimate_id,
            "ticket_id" => $ticket_id
        ]);
    } catch (Exception $e) {
        rollbackTransaction();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handle_get();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_post();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
}
