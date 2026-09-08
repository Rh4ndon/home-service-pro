<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

$user_id   = isset($_SESSION['user_id'])   ? (int)$_SESSION['user_id']   : (isset($_GET['user_id'])   ? (int)$_GET['user_id']   : 0);
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role']       : (isset($_GET['user_role']) ? $_GET['user_role']       : '');

if (!$user_id || !$user_role) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id or user_role']);
    exit;
}

$valid_roles = ['customer', 'repairman', 'admin'];
if (!in_array($user_role, $valid_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

$raw_contacts = getChatContacts($user_id, $user_role);

$contacts = [];
foreach ($raw_contacts as $row) {
    $contact_id   = (int)$row['contact_id'];
    $contact_role = $row['contact_role'];

    $name = getChatContactName($contact_id, $contact_role);

    $last_msg = getChatMessageById($row['last_msg_id']);
    $last_msg_text = $last_msg ? $last_msg['Message'] : '';
    $last_msg_time = $last_msg ? $last_msg['Created_At'] : '';

    $unread = countAllRecords('chat_messages', "Sender_ID = $contact_id AND Sender_Role = '$contact_role' AND Receiver_ID = $user_id AND Receiver_Role = '$user_role' AND Is_Read = 0");

    $contacts[] = [
        'id'               => $contact_id,
        'name'             => $name,
        'role'             => $contact_role,
        'last_message'     => $last_msg_text,
        'last_message_time'=> $last_msg_time,
        'unread_count'     => (int)$unread,
    ];
}

echo json_encode([
    'success'  => true,
    'contacts' => $contacts,
]);
