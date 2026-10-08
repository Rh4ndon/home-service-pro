<?php
session_start();
include '../../models/functions.php';
header('Content-Type: application/json');

const QR_ALLOWED_MIMES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

function adminJson($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    adminJson(['success' => false, 'message' => 'Admin access required.'], 403);
}
$admin_id = (int) ($_SESSION['user_id'] ?? 0);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    expireRepairmanSubscriptions();

    $status = $_GET['status'] ?? null;
    if ($status !== null && !in_array($status, ['pending', 'approved', 'rejected'], true)) $status = null;

    $qr_codes = [];
    foreach (getPaymentQrCodes() as $provider => $row) {
        $qr_codes[$provider] = ['path' => $row['File_Path'], 'updated_at' => $row['Updated_At']];
    }

    adminJson([
        'success' => true,
        'plans' => getSubscriptionPlans(),
        'qr_codes' => $qr_codes,
        'payments' => getAdminSubscriptionPayments($status),
    ]);
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_price') {
        $plan = getSubscriptionPlanById((int) ($_POST['plan_id'] ?? 0));
        if (!$plan) adminJson(['success' => false, 'message' => 'Plan not found.'], 404);

        $raw = trim((string) ($_POST['price'] ?? ''));
        if ($raw === '' || !is_numeric($raw) || (float) $raw < 0 || (float) $raw > 99999999.99) {
            adminJson(['success' => false, 'message' => 'Enter a valid price (0 or more).'], 400);
        }
        updateSubscriptionPlanPrice((int) $plan['ID'], round((float) $raw, 2));
        adminJson(['success' => true, 'message' => $plan['Name'] . ' price updated.']);
    }

    if ($action === 'upload_qr') {
        $provider = $_POST['provider'] ?? '';
        if (!in_array($provider, ['gcash', 'maya'], true)) {
            adminJson(['success' => false, 'message' => 'Provider must be gcash or maya.'], 400);
        }
        if (empty($_FILES['qr']) || $_FILES['qr']['error'] !== UPLOAD_ERR_OK) {
            adminJson(['success' => false, 'message' => 'Please choose a QR code image to upload.'], 400);
        }

        $old = getPaymentQrCodes()[$provider]['File_Path'] ?? null;

        try {
            $new_path = saveUploadedFileTo(
                $_FILES['qr']['tmp_name'],
                $_FILES['qr']['name'],
                'uploads/qr',
                $provider . '_qr',
                QR_ALLOWED_MIMES,
                5 * 1024 * 1024
            );
        } catch (Exception $e) {
            adminJson(['success' => false, 'message' => $e->getMessage()], 400);
        }

        if (!savePaymentQrCode($provider, $new_path)) {
            deleteUploadedProjectFile($new_path);
            adminJson(['success' => false, 'message' => 'Could not save the QR code.'], 500);
        }

        // Replacing a QR code removes the previous file so uploads/qr does not fill up
        if ($old && $old !== $new_path) deleteUploadedProjectFile($old);

        adminJson(['success' => true, 'message' => strtoupper($provider) . ' QR code updated.', 'path' => $new_path]);
    }

    if ($action === 'approve_payment') {
        $payment_id = (int) ($_POST['payment_id'] ?? 0);
        try {
            approveSubscriptionPayment($payment_id, $admin_id);
        } catch (Exception $e) {
            adminJson(['success' => false, 'message' => $e->getMessage()], 400);
        }
        adminJson(['success' => true, 'message' => 'Payment approved. The repairman is now subscribed.']);
    }

    if ($action === 'reject_payment') {
        $payment_id = (int) ($_POST['payment_id'] ?? 0);
        $note = trim((string) ($_POST['note'] ?? ''));
        if (mb_strlen($note) > 255) adminJson(['success' => false, 'message' => 'Note is too long.'], 400);
        if (!rejectSubscriptionPayment($payment_id, $admin_id, $note !== '' ? $note : null)) {
            adminJson(['success' => false, 'message' => 'Payment not found or already reviewed.'], 400);
        }
        adminJson(['success' => true, 'message' => 'Payment rejected.']);
    }

    if ($action === 'revoke_subscription') {
        $profile_id = (int) ($_POST['profile_id'] ?? 0);
        if (!$profile_id) adminJson(['success' => false, 'message' => 'Repairman is required.'], 400);
        revokeRepairmanSubscription($profile_id);
        adminJson(['success' => true, 'message' => 'Subscription revoked. The repairman is locked until they subscribe again.']);
    }

    adminJson(['success' => false, 'message' => 'Invalid action.'], 400);
}

adminJson(['success' => false, 'message' => 'Method not allowed.'], 405);
