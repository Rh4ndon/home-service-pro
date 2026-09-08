<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $ticket = getAdminTicketById($id);

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            exit;
        }

        $ticket['appliance_issues'] = getAllRecords('applianceissue', "WHERE Ticket_ID = " . (int)$id);
        $ticket['client_appliances'] = getAllRecords('clientappliances', "WHERE Client_ID = " . (int)$ticket['Client_ID']);

        echo json_encode([
            'success' => true,
            'ticket' => $ticket
        ]);
        exit;
    }

    $status = $_GET['status'] ?? null;
    $tickets = getAdminTickets($status);

    echo json_encode([
        'success' => true,
        'tickets' => $tickets
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (empty($action)) {
        http_response_code(400);
        echo json_encode(['error' => 'Action is required']);
        exit;
    }

    if ($action === 'reassign') {
        $ticketId = $_POST['ticket_id'] ?? '';
        $newRepairmanId = $_POST['new_repairman_id'] ?? '';

        if (empty($ticketId) || empty($newRepairmanId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ticket_id and new_repairman_id are required']);
            exit;
        }

        $ticket = getRecord('repairticket', "ID = " . (int)$ticketId);
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            exit;
        }

        $repairman = getRecord('user_repairmanprofile', "ID = " . (int)$newRepairmanId);
        if (!$repairman) {
            http_response_code(404);
            echo json_encode(['error' => 'Repairman not found']);
            exit;
        }

        reassignAdminTicket($ticketId, $newRepairmanId);

        echo json_encode([
            'success' => true,
            'message' => 'Ticket reassigned successfully'
        ]);
        exit;
    }

    if ($action === 'close') {
        $ticketId = $_POST['ticket_id'] ?? '';

        if (empty($ticketId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ticket_id is required']);
            exit;
        }

        $ticket = getRecord('repairticket', "ID = " . (int)$ticketId);
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            exit;
        }

        closeAdminTicket($ticketId);

        echo json_encode([
            'success' => true,
            'message' => 'Ticket closed successfully'
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
