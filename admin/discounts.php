<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_discount'])) {
    csrf_validate();
    $code = trim($_POST['code']);
    $type = $_POST['type'];
    $value = (int)$_POST['value'];
    $start = $_POST['start_date'] ?: null;
    $end = $_POST['end_date'] ?: null;
    $usage_limit = $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : null;
    $per_user = $_POST['per_user_limit'] !== '' ? (int)$_POST['per_user_limit'] : null;
    $vip = isset($_POST['vip_only']) ? 1 : 0;
    $motor_id = $_POST['motor_id'] ?: null;
    if ($code && $value) {
        $stmt = $pdo->prepare('INSERT INTO discounts (code, type, value, start_date, end_date, usage_limit, per_user_limit, vip_only, motor_id) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$code, $type, $value, $start, $end, $usage_limit, $per_user, $vip, $motor_id]);
    }
    header('Location: discounts.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM discounts WHERE id=?')->execute([$id]);
    header('Location: discounts.php');
    exit;
}

$motors = $pdo->query('SELECT id, model FROM motorcycles ORDER BY model')->fetchAll(PDO::FETCH_ASSOC);
$discounts = $pdo->query("SELECT d.*, COALESCE(SUM(u.used_count),0) AS used_total, m.model AS motor_model FROM discounts d LEFT JOIN discount_usages u ON d.id=u.discount_id LEFT JOIN motorcycles m ON d.motor_id=m.id GROUP BY d.id ORDER BY d.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت تخفیف‌ها - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link" href="finance.php"><i class="bi bi-receipt"></i><span>مالی</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="discounts.php"><i class="bi bi-ticket-perforated"></i><span>تخفیف‌ها</span></a></li>
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
      <h1 class="h3 mb-4">کدهای تخفیف</h1>
      <form method="post" class="row g-2 mb-4">
        <?= csrf_input(); ?>
        <input type="hidden" name="add_discount" value="1">
        <div class="col-md-2"><input type="text" name="code" class="form-control" placeholder="کد" required></div>
        <div class="col-md-2">
          <select name="type" class="form-select">
            <option value="percent">درصدی</option>
            <option value="fixed">مبلغی</option>
          </select>
        </div>
        <div class="col-md-2"><input type="number" name="value" class="form-control" placeholder="مقدار" required></div>
        <div class="col-md-2"><input type="date" name="start_date" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="end_date" class="form-control"></div>
        <div class="col-md-2"><input type="number" name="usage_limit" class="form-control" placeholder="حداکثر کل"></div>
        <div class="col-md-2"><input type="number" name="per_user_limit" class="form-control" placeholder="حداکثر هر کاربر"></div>
        <div class="col-md-2">
          <select name="motor_id" class="form-select">
            <option value="">همه موتورها</option>
            <?php foreach($motors as $m): ?>
              <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['model']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2 form-check">
          <input class="form-check-input" type="checkbox" name="vip_only" id="vip"> <label class="form-check-label" for="vip">VIP</label>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>کد</th><th>نوع</th><th>مقدار</th><th>بازه</th><th>استفاده</th><th>محدودیت‌کاربر</th><th>VIP</th><th>موتور</th><th>حذف</th></tr></thead>
          <tbody>
            <?php foreach($discounts as $d): ?>
            <tr>
              <td><?= $d['id']; ?></td>
              <td><?= htmlspecialchars($d['code']); ?></td>
              <td><?= $d['type']; ?></td>
              <td><?= $d['value']; ?></td>
              <td><?= $d['start_date'] . ' تا ' . $d['end_date']; ?></td>
              <td><?= $d['used_total']; ?><?= $d['usage_limit']?'/'.$d['usage_limit']:''; ?></td>
              <td><?= $d['per_user_limit'] ?: '-'; ?></td>
              <td><?= $d['vip_only'] ? 'بله' : 'خیر'; ?></td>
              <td><?= $d['motor_model'] ?: 'همه'; ?></td>
              <td><a href="?delete=<?= $d['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف؟');"><i class="bi bi-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
