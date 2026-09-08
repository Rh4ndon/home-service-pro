<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $customer = getAdminCustomerById($id);

        if (!$customer) {
            http_response_code(404);
            echo json_encode(['error' => 'Customer not found']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'customer' => $customer
        ]);
        exit;
    }

    $search = $_GET['search'] ?? null;
    $customers = getAdminCustomers($search);

    echo json_encode([
        'success' => true,
        'customers' => $customers
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

    if ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($id) || empty($name) || empty($email)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID, name, and email are required']);
            exit;
        }

        updateAdminCustomer($id, $name, $email);

        echo json_encode([
            'success' => true,
            'message' => 'Customer updated successfully'
        ]);
        exit;
    }

    if ($action === 'deactivate') {
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
            exit;
        }

        deactivateAdminCustomer($id);

        echo json_encode([
            'success' => true,
            'message' => 'Customer deactivated successfully'
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
