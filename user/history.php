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

// Fetch past bookings with payments
$stmt = $pdo->prepare("SELECT b.*, m.model, p.id AS payment_id, p.amount, p.paid_at
                        FROM bookings b
                        JOIN motorcycles m ON b.motorcycle_id = m.id
                        LEFT JOIN payments p ON p.booking_id = b.id
                        WHERE b.user_id = ? AND b.end_date < ?
                        ORDER BY b.start_date DESC");
$stmt->execute([$user_id, $today]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status counts for pie chart
$statusCounts = [];
foreach ($bookings as $b) {
    $statusCounts[$b['status']] = ($statusCounts[$b['status']] ?? 0) + 1;
}
$historyLabels = array_keys($statusCounts);
$historyData = array_values($statusCounts);

// Monthly totals for bar chart (last 6 months)
$monthStmt = $pdo->prepare("SELECT DATE_FORMAT(end_date,'%Y-%m') AS period, COUNT(*) AS total
                            FROM bookings
                            WHERE user_id = ? AND end_date < ?
                            GROUP BY period ORDER BY period");
$monthStmt->execute([$user_id, $today]);
$rows = $monthStmt->fetchAll(PDO::FETCH_KEY_PAIR);
$historyMonths = [];
$historyCounts = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i month"));
    $historyMonths[] = $m;
    $historyCounts[] = $rows[$m] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تاریخچه رزرو - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
            <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar2-check"></i><span>رزروهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="vehicles.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="map.php"><i class="bi bi-map"></i><span>نقشه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-clock-history"></i><span>تاریخچه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
            <li class="nav-item"><a class="nav-link" href="support.php"><i class="bi bi-life-preserver"></i><span>پشتیبانی</span></a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i><span>تنظیمات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">تاریخچه رزرو</h1>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100"><div class="card-body"><canvas id="historyStatusChart" height="200"></canvas></div></div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100"><div class="card-body"><canvas id="historyMonthChart" height="200"></canvas></div></div>
                </div>
            </div>
            <h4 class="mb-3">رزروهای گذشته</h4>
            <?php if(count($bookings) > 0): ?>
                <?php foreach($bookings as $b):
                    $badgeClass = 'bg-secondary';
                    if($b['status']==='returned') $badgeClass='bg-success';
                    elseif($b['status']==='cancelled') $badgeClass='bg-danger';
                    elseif($b['status']==='in_use') $badgeClass='bg-warning';
                ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><?= htmlspecialchars($b['model']) ?></h6>
                            <small class="text-muted"><?= $b['start_date'] ?> تا <?= $b['end_date'] ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge <?= $badgeClass ?> ms-2"><?= $b['status']==='returned'?'تحویل شد':($b['status']==='cancelled'?'لغو شده':'در حال استفاده') ?></span>
                            <?php if($b['payment_id']): ?>
                                <a class="btn btn-sm btn-outline-primary" href="invoice.php?id=<?= $b['payment_id'] ?>">فاکتور</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">رزرو گذشته‌ای یافت نشد.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    var historyLabels = <?= json_encode($historyLabels) ?>;
    var historyData = <?= json_encode($historyData) ?>;
    var historyMonths = <?= json_encode($historyMonths) ?>;
    var historyCounts = <?= json_encode($historyCounts) ?>;
</script>
<script src="../js/script-user.js"></script>
</body>
</html>
