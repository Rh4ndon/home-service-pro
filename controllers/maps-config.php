<?php
session_start();
include '../models/functions.php';
header('Content-Type: application/json');

$key = getGoogleMapsApiKey();
if (!$key) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'GOOGLE_MAP_API_KEY is not configured.']);
    exit;
}

echo json_encode(['status' => 'success', 'key' => $key]);