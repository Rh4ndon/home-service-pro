<?php
session_start();
include '../../models/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$stats = getAdminDashboardStats();

echo json_encode([
    'success' => true,
    'stats' => [
        'total_customers' => (int)$stats['total_customers'],
        'total_repairmen' => (int)$stats['total_repairmen'],
        'active_tickets' => (int)$stats['active_tickets'],
        'completed_repairs' => (int)$stats['completed_repairs'],
        'pending_approvals' => (int)$stats['pending_approvals'],
        'flagged_issues' => (int)$stats['flagged_issues'],
        'schedules_today' => (int)$stats['schedules_today'],
        'reports_this_week' => (int)$stats['reports_this_week']
    ],
    'top_repairmen' => $stats['top_repairmen'],
    'recent_tickets' => $stats['recent_tickets'],
    'recent_completions' => $stats['recent_completions']
]);
