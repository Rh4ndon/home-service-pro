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

    $stats = getRepairmanDashboardStats($repairman_id);

    echo json_encode([
        "success" => true,
        "data" => [
            "stats" => [
                "todays_jobs" => (int)$stats['todays_jobs'],
                "pending_tickets" => (int)$stats['pending_tickets'],
                "completed_week" => (int)$stats['completed_week'],
                "avg_rating" => $stats['avg_rating']
            ],
            "schedules" => $stats['schedules']
        ]
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handle_get();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
}
