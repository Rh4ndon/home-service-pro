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
        $stmt = $conn->prepare("UPDATE repairschedule SET Status = 'In Progress' WHERE ID = ? AND Repairman_ID = ?");
        $stmt->bind_param("si", $schedule_id, $repairman_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Also update repairticket
            $stmt = $conn->prepare("UPDATE repairticket rt JOIN repairschedule rs ON rt.Schedule_ID = rs.ID SET rt.Status = 'In Progress', rt.Repairman_ID = ? WHERE rs.ID = ? AND rs.Repairman_ID = ?");
            $stmt->bind_param("iii", $repairman_id, $schedule_id, $repairman_id);
            $stmt->execute();
        }

        commitTransaction();
        echo json_encode(["success" => true, "message" => "Schedule accepted successfully."]);
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handle_get();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_post();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
}
