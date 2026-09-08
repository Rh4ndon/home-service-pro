<?php
include '../../models/functions.php';

$contact_id   = isset($_GET['contact_id'])   ? (int)$_GET['contact_id']     : 0;
$contact_role = isset($_GET['contact_role']) ? $_GET['contact_role']        : '';
$user_id      = isset($_GET['user_id'])      ? (int)$_GET['user_id']        : 0;
$user_role    = isset($_GET['user_role'])     ? $_GET['user_role']            : '';
$last_id      = isset($_GET['last_id'])      ? (int)$_GET['last_id']        : 0;

if (!$user_id || !$user_role || !$contact_id || !$contact_role) {
    http_response_code(400);
    echo "event: error\ndata: {\"message\":\"Missing required parameters\"}\n\n";
    exit;
}

// SSE headers
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

// Disable output buffering
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
}
@ini_set('zlib.output_compression', '0');
while (ob_get_level() > 0) {
    ob_end_flush();
}
@ini_set('implicit_flush', '1');

// Send initial connection event
echo "event: connected\ndata: {\"message\":\"Connected to chat stream\"}\n\n";
flush();

$timeout = 30;
$elapsed = 0;

while ($elapsed < $timeout) {
    $new_messages = getNewChatMessages($user_id, $user_role, $contact_id, $contact_role, $last_id);

    if (!empty($new_messages)) {
        foreach ($new_messages as $row) {
            $payload = json_encode([
                'id'            => (int)$row['ID'],
                'sender_id'     => (int)$row['Sender_ID'],
                'sender_role'   => $row['Sender_Role'],
                'receiver_id'   => (int)$row['Receiver_ID'],
                'receiver_role' => $row['Receiver_Role'],
                'message'       => $row['Message'],
                'created_at'    => $row['Created_At'],
            ]);
            echo "id: " . $row['ID'] . "\n";
            echo "event: message\n";
            echo "data: " . $payload . "\n\n";
            flush();

            if ((int)$row['ID'] > $last_id) {
                $last_id = (int)$row['ID'];
            }
        }
    }

    // Send a keep-alive comment to prevent connection timeout
    echo ": keep-alive\n\n";
    flush();

    sleep(2);
    $elapsed += 2;
}

// Loop ended, client should reconnect
echo "event: timeout\ndata: {\"message\":\"Stream timed out, please reconnect\"}\n\n";
flush();
