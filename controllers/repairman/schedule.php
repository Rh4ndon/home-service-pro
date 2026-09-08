<?php
session_start();
include '../../models/functions.php';

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

    if (updateRepairmanScheduleStatus($schedule_id, $repairman_id, 'In Progress')) {
        echo json_encode(["success" => true, "message" => "Schedule accepted successfully."]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Schedule not found or not assigned to you."]);
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

    if (updateRepairmanScheduleStatus($schedule_id, $repairman_id, 'Declined')) {
        echo json_encode(["success" => true, "message" => "Schedule declined successfully."]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Schedule not found or not assigned to you."]);
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

    if (updateRepairmanScheduleStatus($schedule_id, $repairman_id, 'Completed')) {
        echo json_encode(["success" => true, "message" => "Schedule completed successfully."]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Schedule not found or not assigned to you."]);
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
