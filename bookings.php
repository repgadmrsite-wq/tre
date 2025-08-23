<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['confirm'])) {
    $id = (int)$_GET['confirm'];
    $stmt = $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE id=?");
    $stmt->execute([$id]);
    header('Location: bookings.php');
    exit;
}
if (isset($_GET['cancel'])) {
    $id = (int)$_GET['cancel'];
    $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
    $stmt->execute([$id]);
    header('Location: bookings.php');
    exit;
}

$bookings = $pdo->query("SELECT b.id, m.name AS motor_name, u.name AS user_name, b.start_date, b.end_date, b.status, b.amount FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id ORDER BY b.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت رزروها - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="dashboard-layout">
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center">
      <a class="navbar-brand fs-4 text-white" href="index.html">کیش‌ران - ادمین</a>
    </div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت رزروها</h1>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>کاربر</th><th>موتور</th><th>شروع</th><th>پایان</th><th>وضعیت</th><th>مبلغ</th><th>اقدام</th></tr></thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?= $b['id']; ?></td>
              <td><?= htmlspecialchars($b['user_name']); ?></td>
              <td><?= htmlspecialchars($b['motor_name']); ?></td>
              <td><?= $b['start_date']; ?></td>
              <td><?= $b['end_date']; ?></td>
              <td><?= $b['status']; ?></td>
              <td><?= number_format($b['amount']); ?></td>
              <td>
                <?php if ($b['status'] === 'pending'): ?>
                  <a href="?confirm=<?= $b['id']; ?>" class="btn btn-sm btn-success">تایید</a>
                  <a href="?cancel=<?= $b['id']; ?>" class="btn btn-sm btn-warning">لغو</a>
                <?php endif; ?>
              </td>
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
