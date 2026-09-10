<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

function get_user_id()
{
    if (!empty($_SESSION['user_id'])) return $_SESSION['user_id'];
    if (!empty($_GET['user_id'])) return $_GET['user_id'];
    if (!empty($_POST['user_id'])) return $_POST['user_id'];
    return null;
}

function handle_get()
{
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

    $tickets = getRepairmanTickets($repairman_id);
    echo json_encode(["success" => true, "data" => $tickets]);
}

function handle_post()
{
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
        case 'complete':
            handle_complete($user_id);
            break;
        default:
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
}

function handle_accept($user_id)
{
    $ticket_id = $_POST['ticket_id'] ?? '';

    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Ticket ID is required."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    if (acceptRepairmanTicket($ticket_id, $repairman_id)) {
        echo json_encode(["success" => true, "message" => "Ticket accepted successfully."]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Ticket not found or not assigned to you."]);
    }
}

function handle_complete($user_id)
{
    $ticket_id = $_POST['ticket_id'] ?? '';

    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Ticket ID is required."]);
        return;
    }

    $repairman_id = getRepairmanIdFromUser($user_id);
    if (!$repairman_id) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    try {
        completeRepairmanTicket($ticket_id, $repairman_id);
        echo json_encode(["success" => true, "message" => "Ticket completed successfully."]);
    } catch (Exception $e) {
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
