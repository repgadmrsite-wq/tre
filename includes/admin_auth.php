<?php
// Basic admin role enforcement helper
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['user']['role'];
$page = basename($_SERVER['SCRIPT_NAME']);

$accessMap = [
    'admin.php'        => ['support','accountant','mechanic'],
    'admins.php'       => ['super'],
    'bookings.php'     => ['support'],
    'users.php'        => ['support'],
    'motors.php'       => ['mechanic'],
    'maintenance.php'  => ['mechanic'],
    'finance.php'      => ['accountant'],
    'discounts.php'    => ['accountant'],
    'reports.php'      => ['accountant'],
    'notifications.php'=> ['support'],
    'reviews.php'      => ['support'],
    'tickets.php'      => ['support'],
    'marketing.php'    => ['support'],
    'map.php'          => ['support','mechanic'],
    'logs.php'         => [],
    'settings.php'     => [],
    'contract.php'     => ['support','accountant'],
    'invoice.php'      => ['accountant']
];

if ($role !== 'super') {
    $allowed = $accessMap[$page] ?? [];
    if (!in_array($role, $allowed, true)) {
        http_response_code(403);
        exit('دسترسی غیرمجاز');
    }
}
?>
