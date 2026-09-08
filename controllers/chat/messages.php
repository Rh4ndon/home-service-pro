<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user_id      = isset($_SESSION['user_id'])    ? (int)$_SESSION['user_id']    : (isset($_GET['user_id'])    ? (int)$_GET['user_id']    : 0);
    $user_role    = isset($_SESSION['user_role'])  ? $_SESSION['user_role']        : (isset($_GET['user_role'])  ? $_GET['user_role']       : '');
    $contact_id   = isset($_GET['contact_id'])     ? (int)$_GET['contact_id']      : 0;
    $contact_role = isset($_GET['contact_role'])   ? $_GET['contact_role']         : '';

    if (!$user_id || !$user_role || !$contact_id || !$contact_role) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $raw_messages = getChatMessages($user_id, $user_role, $contact_id, $contact_role);

    $messages = [];
    foreach ($raw_messages as $row) {
        $messages[] = [
            'id'            => (int)$row['ID'],
            'sender_id'     => (int)$row['Sender_ID'],
            'sender_role'   => $row['Sender_Role'],
            'receiver_id'   => (int)$row['Receiver_ID'],
            'receiver_role' => $row['Receiver_Role'],
            'message'       => $row['Message'],
            'is_read'       => (int)$row['Is_Read'],
            'created_at'    => $row['Created_At'],
        ];
    }

    markChatMessagesRead($user_id, $user_role, $contact_id, $contact_role);

    echo json_encode([
        'success'  => true,
        'messages' => $messages,
    ]);

} elseif ($method === 'POST') {
    $sender_id    = isset($_POST['sender_id'])    ? (int)$_POST['sender_id']    : 0;
    $sender_role  = isset($_POST['sender_role'])  ? $_POST['sender_role']        : '';
    $receiver_id  = isset($_POST['receiver_id'])  ? (int)$_POST['receiver_id']  : 0;
    $receiver_role= isset($_POST['receiver_role'])? $_POST['receiver_role']      : '';
    $message      = isset($_POST['message'])      ? trim($_POST['message'])      : '';

    if (!$sender_id || !$sender_role || !$receiver_id || !$receiver_role || $message === '') {
        echo json_encode(['success' => false, 'message' => 'Missing required fields (sender_id, sender_role, receiver_id, receiver_role, message)']);
        exit;
    }

    $valid_roles = ['customer', 'repairman', 'admin'];
    if (!in_array($sender_role, $valid_roles) || !in_array($receiver_role, $valid_roles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit;
    }

    $new_id = sendChatMessage($sender_id, $sender_role, $receiver_id, $receiver_role, $message);

    if ($new_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Message sent',
            'id'      => (int)$new_id,
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Use GET or POST.']);
}
