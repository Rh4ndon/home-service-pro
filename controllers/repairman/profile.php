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

    $profile = getRepairmanProfile($user_id);
    if (!$profile) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    echo json_encode(["success" => true, "data" => $profile]);
}

function handle_post() {
    $user_id = get_user_id();
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "User ID is required."]);
        return;
    }

    $action = $_POST['action'] ?? 'update';

    switch ($action) {
        case 'update':
            handle_update($user_id);
            break;
        case 'update_availability':
            handle_update_availability($user_id);
            break;
        case 'add_skill':
            handle_add_skill($user_id);
            break;
        case 'remove_skill':
            handle_remove_skill($user_id);
            break;
        case 'add_cert':
            handle_add_cert($user_id);
            break;
        default:
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
}

function handle_update($user_id) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $tel = $_POST['tel'] ?? '';

    if (empty($name) || empty($email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Name and Email are required."]);
        return;
    }

    if (updateRepairmanProfile($user_id, $name, $email, $address, $mobile, $tel)) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update profile."]);
    }
}

function handle_update_availability($user_id) {
    $availability = $_POST['availability'] ?? '';

    if (empty($availability)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Availability is required."]);
        return;
    }

    if (updateRepairmanAvailability($user_id, $availability)) {
        echo json_encode(["success" => true, "message" => "Availability updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update availability."]);
    }
}

function handle_add_skill($user_id) {
    $skill = $_POST['skill'] ?? '';

    if (empty($skill)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Skill is required."]);
        return;
    }

    $profile = getRepairmanProfile($user_id);
    if (!$profile) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    $current_skills = $profile['Skills'] ?? '';
    $skills_array = array_filter(array_map('trim', explode(',', $current_skills)));
    $skills_array[] = $skill;
    $new_skills = implode(',', array_unique($skills_array));

    if (updateRepairmanSkills($profile['RepairmanBG_ID'], $new_skills)) {
        echo json_encode(["success" => true, "message" => "Skill added successfully.", "skills" => $new_skills]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to add skill."]);
    }
}

function handle_remove_skill($user_id) {
    $skill = $_POST['skill'] ?? '';

    if (empty($skill)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Skill is required."]);
        return;
    }

    $profile = getRepairmanProfile($user_id);
    if (!$profile) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    $current_skills = $profile['Skills'] ?? '';
    $skills_array = array_filter(array_map('trim', explode(',', $current_skills)));
    $skills_array = array_filter($skills_array, function ($s) use ($skill) {
        return strtolower($s) !== strtolower($skill);
    });
    $new_skills = implode(',', $skills_array);

    if (updateRepairmanSkills($profile['RepairmanBG_ID'], $new_skills)) {
        echo json_encode(["success" => true, "message" => "Skill removed successfully.", "skills" => $new_skills]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to remove skill."]);
    }
}

function handle_add_cert($user_id) {
    $cert_name = $_POST['cert_name'] ?? '';
    $issued = $_POST['issued'] ?? '';
    $valid_until = $_POST['valid_until'] ?? '';

    if (empty($cert_name)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Certificate name is required."]);
        return;
    }

    $profile = getRepairmanProfile($user_id);
    if (!$profile) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Repairman profile not found."]);
        return;
    }

    $current_certs = $profile['Certifications'] ?? '[]';
    $certs_array = json_decode($current_certs, true);
    if (!is_array($certs_array)) {
        $certs_array = [];
    }

    $certs_array[] = [
        'name' => $cert_name,
        'issued' => $issued,
        'valid_until' => $valid_until
    ];

    $new_certs = json_encode($certs_array);

    if (updateRepairmanCerts($profile['RepairmanBG_ID'], $new_certs)) {
        echo json_encode(["success" => true, "message" => "Certificate added successfully.", "certifications" => $certs_array]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to add certificate."]);
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
