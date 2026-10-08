<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

// Intentionally NOT calling blockIfRepairmanLocked(): this endpoint is the one a
// locked repairman is allowed to use, so they can subscribe.

const PROOF_ALLOWED_MIMES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf',
];

function subscriptionJson($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

$user_id = currentRequestUserId();
if (!$user_id) subscriptionJson(['success' => false, 'message' => 'User ID is required.'], 400);

$profile_id = getRepairmanIdFromUser($user_id);
if (!$profile_id) subscriptionJson(['success' => false, 'message' => 'Repairman profile not found.'], 404);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $state = getRepairmanSubscriptionState($profile_id);
    $pending = getRepairmanPendingPayment($profile_id);

    $qr_codes = [];
    foreach (getPaymentQrCodes() as $provider => $row) {
        $qr_codes[$provider] = ['path' => $row['File_Path'], 'updated_at' => $row['Updated_At']];
    }

    subscriptionJson([
        'success' => true,
        'subscription' => array_merge($state, [
            'has_pending' => (bool) $pending,
            'trial_days' => REPAIRMAN_TRIAL_DAYS,
        ]),
        'plans' => getSubscriptionPlans(),
        'qr_codes' => $qr_codes,
        'payments' => getRepairmanSubscriptionPayments($profile_id),
    ]);
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action !== 'submit_payment') {
        subscriptionJson(['success' => false, 'message' => 'Invalid action.'], 400);
    }

    $plan = getSubscriptionPlanById((int) ($_POST['plan_id'] ?? 0));
    if (!$plan) subscriptionJson(['success' => false, 'message' => 'Please select a subscription plan.'], 400);

    $payment_method = $_POST['payment_method'] ?? '';
    if (!in_array($payment_method, ['gcash', 'maya'], true)) {
        subscriptionJson(['success' => false, 'message' => 'Please choose GCash or Maya as your payment method.'], 400);
    }

    $qr = getPaymentQrCodes()[$payment_method] ?? null;
    if (!$qr) {
        subscriptionJson(['success' => false, 'message' => strtoupper($payment_method) . ' payment is not available yet. Please try the other method.'], 400);
    }

    if (getRepairmanPendingPayment($profile_id)) {
        subscriptionJson(['success' => false, 'message' => 'You already have a payment waiting for review. Please wait for the admin to verify it.'], 409);
    }

    if (empty($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
        subscriptionJson(['success' => false, 'message' => 'Please upload your proof of payment (screenshot or receipt).'], 400);
    }

    $reference_no = trim($_POST['reference_no'] ?? '');
    if (mb_strlen($reference_no) > 100) {
        subscriptionJson(['success' => false, 'message' => 'Reference number is too long.'], 400);
    }

    try {
        $proof_path = saveUploadedFileTo(
            $_FILES['proof']['tmp_name'],
            $_FILES['proof']['name'],
            'uploads/payments',
            'proof',
            PROOF_ALLOWED_MIMES,
            SUBSCRIPTION_PROOF_MAX_BYTES
        );
    } catch (Exception $e) {
        subscriptionJson(['success' => false, 'message' => $e->getMessage()], 400);
    }

    $payment_id = createSubscriptionPayment(
        $profile_id,
        $plan,
        $payment_method,
        $reference_no !== '' ? $reference_no : null,
        $proof_path,
        $plan['Price']
    );

    subscriptionJson([
        'success' => true,
        'message' => 'Proof of payment submitted. The admin will verify it shortly.',
        'payment_id' => $payment_id
    ]);
}

subscriptionJson(['success' => false, 'message' => 'Method not allowed.'], 405);
