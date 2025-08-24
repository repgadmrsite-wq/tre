<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $amount = (int)($_POST['amount'] ?? 0);
    if ($amount > 0) {
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?')->execute([$amount, $user_id]);
        $pdo->prepare('INSERT INTO wallet_transactions (user_id, amount, type, description) VALUES (?,?,"credit","شارژ حساب")')->execute([$user_id, $amount]);
        $pdo->commit();
        $_SESSION['user']['wallet_balance'] = ($_SESSION['user']['wallet_balance'] ?? 0) + $amount;
    }
    header('Location: payments.php');
    exit;
}

$balanceStmt = $pdo->prepare('SELECT wallet_balance FROM users WHERE id=?');
$balanceStmt->execute([$user_id]);
$wallet_balance = (int)$balanceStmt->fetchColumn();

$transStmt = $pdo->prepare('SELECT amount, type, description, created_at FROM wallet_transactions WHERE user_id=? ORDER BY created_at DESC');
$transStmt->execute([$user_id]);
$transactions = $transStmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>پرداخت‌ها - KISH UP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar d-flex flex-column p-0">
        <div class="sidebar-header text-center">
            <a class="navbar-brand fs-4 text-white" href="../index.html">KISH UP</a>
        </div>
        <ul class="nav flex-column my-4">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
            <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar2-check"></i><span>رزروهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="vehicles.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="map.php"><i class="bi bi-map"></i><span>نقشه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history"></i><span>تاریخچه</span></a></li>
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
            <h1 class="h2 mb-4">پرداخت‌ها</h1>
            <div class="card mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>موجودی فعلی: <strong><?= number_format($wallet_balance) ?> تومان</strong></div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#topupModal">شارژ حساب</button>
                </div>
            </div>
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
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom-0"><h5 class="mb-0">تراکنش‌های کیف پول</h5></div>
                <div class="card-body">
                    <?php if (count($transactions) > 0): ?>
                        <?php foreach ($transactions as $t): ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div><?= $t['description'] ?: ($t['type']==='credit'?'واریز':'برداشت') ?></div>
                            <div class="<?= $t['type']==='credit'?'text-success':'text-danger' ?>">
                                <?= $t['type']==='credit' ? '+' : '-' ?><?= number_format($t['amount']) ?>
                            </div>
                            <div class="text-muted small"><?= $t['created_at'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">تراکنشی ثبت نشده است.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>
<div class="modal fade" id="topupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post">
        <?= csrf_input() ?>
        <div class="modal-header">
            <h5 class="modal-title">شارژ حساب</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <label for="amount" class="form-label">مبلغ (تومان)</label>
            <input type="number" name="amount" id="amount" class="form-control" min="1000" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">افزایش موجودی</button>
        </div>
    </form>
  </div>
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
