<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $repairman = getAdminRepairmanById($id);

        if (!$repairman) {
            http_response_code(404);
            echo json_encode(['error' => 'Repairman not found']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'repairman' => $repairman
        ]);
        exit;
    }

    $search = $_GET['search'] ?? null;
    $repairmen = getAdminRepairmen($search);

    echo json_encode([
        'success' => true,
        'repairmen' => $repairmen
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $address = $_POST['address'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $tel = $_POST['tel'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, email, and password are required']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            exit;
        }

        if (isEmailTaken($email)) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']);
            exit;
        }

        try {
            $cert_entries = collectCertEntriesFromUploads();
            $certs_json = $cert_entries ? json_encode($cert_entries) : null;
            $skills = trim($_POST['skills'] ?? '');
            $education = trim($_POST['education'] ?? '');
            $profileId = addAdminRepairman($name, $email, $password, $address, $mobile, $tel, $certs_json, $skills, $education);

            echo json_encode([
                'success' => true,
                'id' => $profileId,
                'message' => 'Repairman created successfully'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create repairman: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'activate') {
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
            exit;
        }

        activateAdminRepairman($id);

        echo json_encode([
            'success' => true,
            'message' => 'Repairman activated successfully'
        ]);
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $address = $_POST['address'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $tel = $_POST['tel'] ?? '';
        $availability = $_POST['availability'] ?? '';

        updateAdminRepairman($id, $name, $email, $address, $mobile, $tel, $availability);

        echo json_encode([
            'success' => true,
            'message' => 'Repairman updated successfully'
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

        deactivateAdminRepairman($id);

        echo json_encode([
            'success' => true,
            'message' => 'Repairman deactivated successfully'
        ]);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
            exit;
        }

        try {
            $result = deleteAdminRepairman($id);

            if (!$result) {
                http_response_code(404);
                echo json_encode(['error' => 'Repairman not found']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Repairman deleted successfully'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete repairman: ' . $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
