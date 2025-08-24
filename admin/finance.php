<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/notify.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    csrf_validate();
    $errors = [];
    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($booking_id === false) { $errors[] = 'رزرو نامعتبر است'; }
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($amount === false) { $errors[] = 'مبلغ نامعتبر است'; }
    $method = in_array($_POST['method'], ['online','pos','cash']) ? $_POST['method'] : null;
    if (!$method) { $errors[] = 'روش پرداخت نامعتبر است'; }
    $status = in_array($_POST['status'], ['paid','pending']) ? $_POST['status'] : null;
    if (!$status) { $errors[] = 'وضعیت نامعتبر است'; }
    if ($errors) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: finance.php');
        exit;
    }
    $info = $pdo->prepare('SELECT u.id, u.email, u.phone FROM bookings b JOIN users u ON b.user_id=u.id WHERE b.id=?');
    $info->execute([$booking_id]);
    if ($u = $info->fetch(PDO::FETCH_ASSOC)) {
        $stmt = $pdo->prepare('INSERT INTO payments (booking_id, user_id, amount, method, status) VALUES (?,?,?,?,?)');
        $stmt->execute([$booking_id, $u['id'], $amount, $method, $status]);
        if($status==='paid'){
            $pdo->prepare('INSERT INTO notifications (user_id,message) VALUES (?,?)')->execute([$u['id'],"پرداخت موفق ثبت شد"]);
            sendEmail($u['email'], 'وضعیت پرداخت', 'پرداخت شما با موفقیت ثبت شد');
        } else {
            $pdo->prepare('INSERT INTO notifications (user_id,message) VALUES (?,?)')->execute([$u['id'],"پرداخت معلق است"]);
            sendEmail($u['email'], 'وضعیت پرداخت', 'پرداخت شما در انتظار تایید است');
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
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <form method="post" class="row g-2 mb-4">
        <?= csrf_input(); ?>
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
