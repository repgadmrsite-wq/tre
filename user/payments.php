<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
$method = $_GET['method'] ?? '';

$conditions = ['user_id = ?'];
$params = [$user_id];
if ($start) { $conditions[] = 'paid_at >= ?'; $params[] = $start; }
if ($end) { $conditions[] = 'paid_at <= ?'; $params[] = $end; }
if ($method) { $conditions[] = 'method = ?'; $params[] = $method; }

$query = 'SELECT * FROM payments WHERE ' . implode(' AND ', $conditions) . ' ORDER BY paid_at DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly revenue data
$monthlyStmt = $pdo->prepare("SELECT DATE_FORMAT(paid_at,'%Y-%m') AS period, SUM(amount) AS total FROM payments WHERE user_id=? AND status='paid' GROUP BY period ORDER BY period");
$monthlyStmt->execute([$user_id]);
$monthData = $monthlyStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$labels = [];
$current = [];
$prev = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i month"));
    $labels[] = $m;
    $current[] = $monthData[$m] ?? 0;
    $pm = date('Y-m', strtotime('-'.($i + 1).' month'));
    $prev[] = $monthData[$pm] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرداخت‌ها - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">پرداخت‌ها</h1>
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-3"><input type="date" name="start" value="<?= htmlspecialchars($start) ?>" class="form-control" placeholder="از تاریخ"></div>
                <div class="col-md-3"><input type="date" name="end" value="<?= htmlspecialchars($end) ?>" class="form-control" placeholder="تا تاریخ"></div>
                <div class="col-md-3">
                    <select name="method" class="form-select">
                        <option value="">نوع پرداخت</option>
                        <option value="online" <?= $method==='online'?'selected':'' ?>>آنلاین</option>
                        <option value="cash" <?= $method==='cash'?'selected':'' ?>>نقدی</option>
                        <option value="pos" <?= $method==='pos'?'selected':'' ?>>حضوری</option>
                    </select>
                </div>
                <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">فیلتر</button></div>
            </form>
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom-0"><h5 class="mb-0">گزارش درآمد ماهانه</h5></div>
                <div class="card-body"><canvas id="financeChart" height="120"></canvas></div>
            </div>
            <div class="mb-4">
                <?php if (count($payments) > 0): ?>
                    <?php foreach ($payments as $p):
                        $statusClass = $p['status']==='paid' ? 'payment-paid' : 'payment-pending';
                        $methodClass = 'method-' . $p['method'];
                    ?>
                    <div class="card payment-card <?= $statusClass ?> <?= $methodClass ?> mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">رزرو #<?= $p['booking_id'] ?></h6>
                                <small class="text-muted"><?= $p['paid_at'] ?></small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold mb-1"><?= number_format($p['amount']) ?> تومان</div>
                                <span class="badge bg-secondary ms-1">
                                    <?php if($p['method']==='online') echo 'آنلاین'; elseif($p['method']==='cash') echo 'نقدی'; else echo 'حضوری'; ?>
                                </span>
                                <span class="badge <?= $p['status']==='paid' ? 'bg-success' : 'bg-danger' ?> ms-1">
                                    <?= $p['status']==='paid' ? 'موفق' : 'ناموفق' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">پرداختی یافت نشد.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script>
var financeLabels = <?= json_encode($labels) ?>;
var financeCurrent = <?= json_encode($current) ?>;
var financePrev = <?= json_encode($prev) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
