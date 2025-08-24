<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

$today = date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));
$startOfMonth = date('Y-m-01');
$endOfMonth = date('Y-m-t');
$startOfYear = date('Y-01-01');
$endOfYear = date('Y-12-31');

$statQueries = [
    'today' => [$today, $today],
    'week' => [$startOfWeek, $endOfWeek],
    'month' => [$startOfMonth, $endOfMonth],
    'year' => [$startOfYear, $endOfYear]
];
$stats = [];
foreach ($statQueries as $key => $range) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE user_id = ? AND start_date BETWEEN ? AND ?');
    $stmt->execute([$user_id, $range[0], $range[1]]);
    $stats[$key] = (int)$stmt->fetchColumn();
}

$statusCounts = ['confirmed' => 0, 'in_use' => 0, 'cancelled' => 0];
$statusStmt = $pdo->prepare('SELECT status, COUNT(*) AS c FROM bookings WHERE user_id = ? GROUP BY status');
$statusStmt->execute([$user_id]);
foreach ($statusStmt as $row) {
    if (isset($statusCounts[$row['status']])) {
        $statusCounts[$row['status']] = (int)$row['c'];
    }
}

$currentStmt = $pdo->prepare('SELECT b.*, m.model FROM bookings b JOIN motorcycles m ON b.motorcycle_id = m.id WHERE b.user_id = ? AND b.status IN ("confirmed","in_use") AND ? BETWEEN b.start_date AND b.end_date ORDER BY b.start_date');
$currentStmt->execute([$user_id, $today]);
$currentBookings = $currentStmt->fetchAll();

$upcomingStmt = $pdo->prepare('SELECT b.*, m.model FROM bookings b JOIN motorcycles m ON b.motorcycle_id = m.id WHERE b.user_id = ? AND b.start_date > ? AND b.start_date <= DATE_ADD(?, INTERVAL 7 DAY) AND b.status IN ("pending","confirmed") ORDER BY b.start_date');
$upcomingStmt->execute([$user_id, $today, $today]);
$upcomingBookings = $upcomingStmt->fetchAll();
$upcomingCount = count($upcomingBookings);

$paymentStmt = $pdo->prepare('SELECT DATE_FORMAT(paid_at, "%Y-%m") AS period, SUM(amount) AS total FROM payments WHERE user_id = ? GROUP BY period ORDER BY period');
$paymentStmt->execute([$user_id]);
$paymentData = $paymentStmt->fetchAll();

$revenueQueries = [
    'daily' => [$today, $today],
    'monthly' => [$startOfMonth, $endOfMonth],
    'yearly' => [$startOfYear, $endOfYear]
];
$revenueStats = [];
foreach ($revenueQueries as $key => $range) {
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE user_id = ? AND paid_at BETWEEN ? AND ?');
    $stmt->execute([$user_id, $range[0], $range[1]]);
    $revenueStats[$key] = (float)$stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد کاربر - کیش‌ران</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar d-flex flex-column p-0">
        <div class="sidebar-header text-center">
            <a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران</a>
        </div>
        <ul class="nav flex-column my-4">
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a>
            </li>
            <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar2-check"></i><span>رزروهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">داشبورد</h1>
                <div class="d-flex align-items-center"><span class="me-3 d-none d-md-inline">خوش آمدید، <?= htmlspecialchars($user['name']) ?></span><img src="https://i.pravatar.cc/40" class="rounded-circle" alt="User Avatar"></div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card p-3 text-center"><h6>امروز</h6><p class="fs-4 mb-0"><?= $stats['today'] ?></p></div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card p-3 text-center"><h6>این هفته</h6><p class="fs-4 mb-0"><?= $stats['week'] ?></p></div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card p-3 text-center"><h6>این ماه</h6><p class="fs-4 mb-0"><?= $stats['month'] ?></p></div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card p-3 text-center"><h6>امسال</h6><p class="fs-4 mb-0"><?= $stats['year'] ?></p></div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="card stat-card bg-primary text-white p-3 text-center">
                        <h6>رزروهای تایید شده</h6>
                        <p class="fs-4 mb-0"><?= $statusCounts['confirmed'] ?></p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card stat-card bg-success text-white p-3 text-center">
                        <h6>رزروهای فعال</h6>
                        <p class="fs-4 mb-0"><?= $statusCounts['in_use'] ?></p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card stat-card bg-danger text-white p-3 text-center">
                        <h6>رزروهای لغو شده</h6>
                        <p class="fs-4 mb-0"><?= $statusCounts['cancelled'] ?></p>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-white border-bottom-0"><h5 class="mb-0">رزروهای جاری</h5></div>
                        <div class="card-body">
                            <?php if (count($currentBookings) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($currentBookings as $b): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($b['model']) ?> (<?= $b['start_date'] ?> - <?= $b['end_date'] ?>)</span>
                                    <span class="badge bg-success">در حال انجام</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="text-muted mb-0">رزرو فعالی ندارید.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-white border-bottom-0"><h5 class="mb-0"><i class="bi bi-bell me-2"></i>یادآوری‌ها</h5></div>
                        <div class="card-body">
                            <?php if (count($upcomingBookings) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($upcomingBookings as $b): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($b['model']) ?> (<?= $b['start_date'] ?>)</span>
                                    <span class="badge bg-warning text-dark">در پیش‌رو</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="text-muted mb-0">یادآوری فعلی ندارید.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header bg-white border-bottom-0"><h5 class="card-title mb-0">گزارش پرداخت‌ها</h5></div>
                <div class="card-body"><canvas id="paymentsChart" height="120"></canvas></div>
            </div>
            <div class="card mt-4">
                <div class="card-header bg-white border-bottom-0"><h5 class="card-title mb-0">درآمد روزانه / ماهانه / سالانه</h5></div>
                <div class="card-body"><canvas id="revenuePie" height="120"></canvas></div>
            </div>
        </div>
    </main>
</div>
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-bell me-2"></i>یادآوری رزروهای نزدیک</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($upcomingCount > 0): ?>
        <ul class="list-group list-group-flush">
            <?php foreach ($upcomingBookings as $b): ?>
            <li class="list-group-item"><?= htmlspecialchars($b['model']) ?> (<?= $b['start_date'] ?>)</li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="mb-0">یادآوری فعلی ندارید.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script>
var paymentData = <?= json_encode($paymentData); ?>;
var revenueStats = <?= json_encode($revenueStats); ?>;
var upcomingCount = <?= $upcomingCount; ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
