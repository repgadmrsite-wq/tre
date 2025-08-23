<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$totalRevenue = $pdo->query('SELECT COALESCE(SUM(amount),0) FROM bookings')->fetchColumn();
$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalAdmins = $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$totalMotors = $pdo->query('SELECT COUNT(*) FROM motorcycles')->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>داشبورد مدیریت - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="dashboard-layout">
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center">
      <a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران - ادمین</a>
    </div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link active" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
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
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">داشبورد</h1>
        <div class="d-flex align-items-center">
          <span class="me-3 d-none d-md-inline"><?= htmlspecialchars($_SESSION['user']['name']); ?> عزیز، خوش آمدید</span>
          <i class="bi bi-person-circle fs-3"></i>
        </div>
      </div>
      <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-primary">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div><h5 class="card-title">درآمد کل</h5><p class="fs-3 fw-bold mb-0"><?= number_format($totalRevenue); ?> تومان</p></div>
              <i class="bi bi-cash-coin fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-success">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div><h5 class="card-title">کاربران</h5><p class="fs-3 fw-bold mb-0"><?= $totalUsers; ?></p></div>
              <i class="bi bi-people-fill fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-warning">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div><h5 class="card-title">رزروهای در انتظار</h5><p class="fs-3 fw-bold mb-0"><?= $pendingBookings; ?></p></div>
              <i class="bi bi-calendar-check fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-info">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div><h5 class="card-title">مدیران</h5><p class="fs-3 fw-bold mb-0"><?= $totalAdmins; ?></p></div>
              <i class="bi bi-shield-lock fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-secondary">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div><h5 class="card-title">کل موتورها</h5><p class="fs-3 fw-bold mb-0"><?= $totalMotors; ?></p></div>
              <i class="bi bi-bicycle fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
