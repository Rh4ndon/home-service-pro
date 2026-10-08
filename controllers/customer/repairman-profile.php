<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

// Public trust information a customer can use to judge a repairman.
// Contact details (mobile, exact address) are intentionally NOT returned:
// customers reach repairmen through the in-app chat.

$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'] ?? null;
if (!$user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
    exit;
}

// Target repairman: either their profile ID (book-service cards) or their
// user ID (chat contacts are keyed by user ID).
$profile_id = !empty($_GET['profile_id']) ? (int) $_GET['profile_id'] : null;
$repairman_user_id = !empty($_GET['repairman_user_id']) ? (int) $_GET['repairman_user_id'] : null;

if (!$profile_id && !$repairman_user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Repairman profile_id or repairman_user_id is required.']);
    exit;
}

$repairman = $profile_id
    ? getRepairmanPublicProfile($profile_id, null)
    : getRepairmanPublicProfile(null, $repairman_user_id);

if (!$repairman) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Repairman not found.']);
    exit;
}

$skills = array_values(array_filter(array_map('trim', explode(',', (string) $repairman['Skills']))));

$certifications = [];
$decoded = json_decode((string) $repairman['Certifications'], true);
if (is_array($decoded)) {
    foreach ($decoded as $cert) {
        if (!is_array($cert) || empty($cert['name'])) continue;
        $file = $cert['file'] ?? null;
        // Only expose files stored in the certificates folder
        if ($file && strpos($file, 'uploads/certificates/') !== 0) $file = null;
        $certifications[] = ['name' => $cert['name'], 'file' => $file];
    }
}

$reviews = [];
foreach (getRepairmanRecentReviews($repairman['ID'], 5) as $review) {
    $parts = preg_split('/\s+/', trim((string) $review['customer_name']), -1, PREG_SPLIT_NO_EMPTY);
    if (count($parts) >= 2) {
        $last = array_pop($parts);
        $masked = hideNameGCashStyle(implode(' ', $parts), $last);
    } elseif (count($parts) === 1) {
        $masked = hideNameGCashStyle($parts[0], '');
    } else {
        $masked = 'Customer';
    }
    $reviews[] = [
        'rating' => (int) $review['Rating'],
        'comments' => $review['Comments'],
        'created_at' => $review['Created_At'],
        'customer_name' => $masked
    ];
}

echo json_encode([
    'success' => true,
    'repairman' => [
        'id' => (int) $repairman['ID'],
        'user_id' => (int) $repairman['User_ID'],
        'name' => $repairman['Name'],
        'rating' => round((float) $repairman['Ratings'], 1),
        'review_count' => (int) $repairman['ReviewCount'],
        'completed_repairs' => (int) $repairman['CompletedRepairs'],
        'member_since' => $repairman['Member_Since'],
        'availability' => $repairman['Availability'],
        'skills' => $skills,
        'education' => $repairman['Education'],
        'certifications' => $certifications,
        'facebook_page' => $repairman['FacebookPage']
    ],
    'reviews' => $reviews
]);
