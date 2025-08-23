<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $booking_id = (int)$_POST['booking_id'];
    $amount = (int)$_POST['amount'];
    $method = $_POST['method'];
    $status = $_POST['status'];
    $user_id = $pdo->prepare('SELECT user_id FROM bookings WHERE id=?');
    $user_id->execute([$booking_id]);
    $uid = $user_id->fetchColumn();
    if ($uid) {
        $stmt = $pdo->prepare('INSERT INTO payments (booking_id, user_id, amount, method, status) VALUES (?,?,?,?,?)');
        $stmt->execute([$booking_id, $uid, $amount, $method, $status]);
        if($status==='paid'){
            $pdo->prepare('INSERT INTO notifications (user_id,message) VALUES (?,?)')->execute([$uid,"پرداخت موفق ثبت شد"]);
        } else {
            $pdo->prepare('INSERT INTO notifications (user_id,message) VALUES (?,?)')->execute([$uid,"پرداخت معلق است"]);
        }
    }
    header('Location: finance.php');
    exit;
}

$filterUser = (int)($_GET['user'] ?? 0);
$filterMethod = $_GET['method'] ?? '';
$filterStart = $_GET['start'] ?? '';
$filterEnd = $_GET['end'] ?? '';

$query = "SELECT p.id, p.booking_id, u.name AS user_name, p.amount, p.method, p.status, p.paid_at FROM payments p JOIN users u ON p.user_id=u.id WHERE 1=1";
$params = [];
if ($filterUser) { $query .= ' AND p.user_id=?'; $params[] = $filterUser; }
if ($filterMethod) { $query .= ' AND p.method=?'; $params[] = $filterMethod; }
if ($filterStart) { $query .= ' AND DATE(p.paid_at) >= ?'; $params[] = $filterStart; }
if ($filterEnd) { $query .= ' AND DATE(p.paid_at) <= ?'; $params[] = $filterEnd; }
$query .= ' ORDER BY p.paid_at DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query('SELECT id, name FROM users ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

$bookings = $pdo->query("SELECT b.id, u.name AS user_name, (b.amount - COALESCE((SELECT SUM(amount) FROM payments WHERE booking_id=b.id AND status='paid'),0)) AS due FROM bookings b JOIN users u ON b.user_id=u.id ORDER BY b.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$debts = array_filter($bookings, fn($b) => $b['due'] > 0);

$monthlyStmt = $pdo->query("SELECT DATE_FORMAT(p.paid_at,'%Y-%m') m, SUM(p.amount) r FROM payments p WHERE p.status='paid' AND p.paid_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY m ORDER BY m");
$monthlyMap = [];
while ($row = $monthlyStmt->fetch(PDO::FETCH_ASSOC)) { $monthlyMap[$row['m']] = (int)$row['r']; }
$monthlyLabels = [];$monthlyRevenue = [];
for ($i=11;$i>=0;$i--) { $m=date('Y-m',strtotime("-$i month")); $monthlyLabels[]=$m; $monthlyRevenue[]=$monthlyMap[$m]??0; }
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت مالی - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center">
      <a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران - ادمین</a>
    </div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="finance.php"><i class="bi bi-receipt"></i><span>مالی</span></a></li>
      <li class="nav-item"><a class="nav-link" href="discounts.php"><i class="bi bi-ticket-perforated"></i><span>تخفیف‌ها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">گزارش پرداخت‌ها</h1>
      <form method="post" class="row g-2 mb-4">
        <input type="hidden" name="add_payment" value="1">
        <div class="col-md-4">
          <select name="booking_id" class="form-select" required>
            <option value="">رزرو</option>
            <?php foreach($bookings as $b): ?>
              <option value="<?= $b['id']; ?>">#<?= $b['id']; ?> - <?= htmlspecialchars($b['user_name']); ?> (بدهی: <?= number_format($b['due']); ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2"><input type="number" name="amount" class="form-control" placeholder="مبلغ" required></div>
        <div class="col-md-2">
          <select name="method" class="form-select">
            <option value="online">آنلاین</option>
            <option value="pos">حضوری</option>
            <option value="cash">نقدی</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="paid">پرداخت شد</option>
            <option value="pending">در انتظار</option>
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>

      <form method="get" class="row g-2 mb-4">
        <div class="col-md-2">
          <select name="user" class="form-select">
            <option value="">مشتری</option>
            <?php foreach($users as $u): ?>
              <option value="<?= $u['id']; ?>" <?= $filterUser==$u['id']?'selected':''; ?>><?= htmlspecialchars($u['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <select name="method" class="form-select">
            <option value="">روش پرداخت</option>
            <option value="online" <?= $filterMethod=='online'?'selected':''; ?>>آنلاین</option>
            <option value="pos" <?= $filterMethod=='pos'?'selected':''; ?>>حضوری</option>
            <option value="cash" <?= $filterMethod=='cash'?'selected':''; ?>>نقدی</option>
          </select>
        </div>
        <div class="col-md-2"><input type="date" name="start" value="<?= htmlspecialchars($filterStart); ?>" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="end" value="<?= htmlspecialchars($filterEnd); ?>" class="form-control"></div>
        <div class="col-md-2"><button class="btn btn-secondary w-100" type="submit">فیلتر</button></div>
      </form>

      <div class="table-responsive mb-4">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>رزرو</th><th>مشتری</th><th>مبلغ</th><th>روش</th><th>وضعیت</th><th>تاریخ</th><th>رسید</th></tr></thead>
          <tbody>
            <?php foreach($payments as $p): ?>
            <tr>
              <td><?= $p['id']; ?></td>
              <td>#<?= $p['booking_id']; ?></td>
              <td><?= htmlspecialchars($p['user_name']); ?></td>
              <td><?= number_format($p['amount']); ?></td>
              <td><?= $p['method']; ?></td>
              <td><?= $p['status']; ?></td>
              <td><?= $p['paid_at']; ?></td>
              <td><a href="invoice.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-secondary">رسید</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if($debts): ?>
      <div class="card mb-4">
        <div class="card-header">بدهی‌ها</div>
        <ul class="list-group list-group-flush">
          <?php foreach($debts as $d): ?>
            <li class="list-group-item d-flex justify-content-between"><span>#<?= $d['id']; ?> - <?= htmlspecialchars($d['user_name']); ?></span><span><?= number_format($d['due']); ?> تومان</span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">درآمد ماهانه</div>
        <div class="card-body"><canvas id="monthlyRevenueChart" height="150"></canvas></div>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyLabels = <?= json_encode($monthlyLabels); ?>;
const monthlyRevenue = <?= json_encode($monthlyRevenue); ?>;
</script>
<script src="../js/admin-panel.js"></script>
</body>
</html>
