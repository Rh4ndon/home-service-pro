<?php
session_start();
include '../../models/functions.php';

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Admin access required.');
}

$type = $_GET['type'] ?? '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!in_array($type, ['customer', 'repairman'], true) || !$id || $id < 1) {
    http_response_code(400);
    exit('Invalid valid ID request.');
}

$table = $type === 'customer' ? 'user_clientprofile' : 'user_repairmanprofile';
$stmt = $conn->prepare("SELECT Valid_ID FROM `$table` WHERE ID = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$storedPath = $profile['Valid_ID'] ?? '';
if (!$storedPath || !preg_match('~^uploads/ids/[A-Za-z0-9._-]+$~', $storedPath)) {
    http_response_code(404);
    exit('Valid ID not found.');
}

$uploadDir = realpath(__DIR__ . '/../../uploads/ids');
$filePath = realpath(__DIR__ . '/../../' . $storedPath);
if (!$uploadDir || !$filePath || strpos($filePath, $uploadDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
    http_response_code(404);
    exit('Valid ID not found.');
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filePath);
finfo_close($finfo);
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($mime, $allowedMimes, true)) {
    http_response_code(415);
    exit('Unsupported valid ID image.');
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="valid-id.' . pathinfo($filePath, PATHINFO_EXTENSION) . '"');
header('Cache-Control: private, no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
readfile($filePath);
