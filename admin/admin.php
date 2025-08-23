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

$todayBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(start_date)=CURDATE()")->fetchColumn();
$weekBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE YEARWEEK(start_date,1)=YEARWEEK(CURDATE(),1)")->fetchColumn();
$monthBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE YEAR(start_date)=YEAR(CURDATE()) AND MONTH(start_date)=MONTH(CURDATE())")->fetchColumn();
$yearBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE YEAR(start_date)=YEAR(CURDATE())")->fetchColumn();

$activeMotors = $pdo->query("SELECT COUNT(*) FROM motorcycles WHERE status='active'")->fetchColumn();
$maintenanceMotors = $pdo->query("SELECT COUNT(*) FROM motorcycles WHERE status='maintenance'")->fetchColumn();
$reservedMotors = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed' AND CURDATE() BETWEEN start_date AND end_date")->fetchColumn();

$dailyStmt = $pdo->query("SELECT DATE(start_date) d, SUM(amount) r FROM bookings WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY d ORDER BY d");
$dailyMap = [];
while($row = $dailyStmt->fetch(PDO::FETCH_ASSOC)){ $dailyMap[$row['d']] = (int)$row['r']; }
$dailyLabels = [];$dailyRevenue = [];
for($i=6;$i>=0;$i--){$d=date('Y-m-d',strtotime("-$i day"));$dailyLabels[]=$d;$dailyRevenue[]=$dailyMap[$d]??0;}

$monthlyStmt = $pdo->query("SELECT DATE_FORMAT(start_date,'%Y-%m') m, SUM(amount) r FROM bookings WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY m ORDER BY m");
$monthlyMap = [];
while($row=$monthlyStmt->fetch(PDO::FETCH_ASSOC)){ $monthlyMap[$row['m']] = (int)$row['r']; }
$monthlyLabels=[];$monthlyRevenue=[];
for($i=11;$i>=0;$i--){$m=date('Y-m',strtotime("-$i month"));$monthlyLabels[]=$m;$monthlyRevenue[]=$monthlyMap[$m]??0;}

$ongoing = $pdo->query("SELECT u.name user_name,m.model motor_model,b.end_date FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id WHERE b.status='confirmed' AND CURDATE() BETWEEN b.start_date AND b.end_date ORDER BY b.end_date LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$upcoming = $pdo->query("SELECT u.name user_name,m.model motor_model,b.start_date FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id WHERE b.status='confirmed' AND b.start_date > CURDATE() AND b.start_date <= DATE_ADD(CURDATE(),INTERVAL 7 DAY) ORDER BY b.start_date LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$overdue = $pdo->query("SELECT u.name user_name,m.model motor_model,b.end_date FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id WHERE b.status='confirmed' AND b.end_date < CURDATE() ORDER BY b.end_date")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>داشبورد مدیریت - کیش‌ران</title>
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
      <div class="row g-4 mt-1">
        <div class="col-xl-6">
          <div class="card h-100">
            <div class="card-header">آمار رزروها</div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-3"><h6>امروز</h6><p class="fs-4 mb-0"><?= $todayBookings; ?></p></div>
                <div class="col-3"><h6>هفته</h6><p class="fs-4 mb-0"><?= $weekBookings; ?></p></div>
                <div class="col-3"><h6>ماه</h6><p class="fs-4 mb-0"><?= $monthBookings; ?></p></div>
                <div class="col-3"><h6>سال</h6><p class="fs-4 mb-0"><?= $yearBookings; ?></p></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="card h-100">
            <div class="card-header">وضعیت موتورها</div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                <li>فعال: <?= $activeMotors; ?></li>
                <li>رزرو شده: <?= $reservedMotors; ?></li>
                <li>در تعمیر: <?= $maintenanceMotors; ?></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">درآمد روزانه</div>
            <div class="card-body"><canvas id="dailyRevenueChart" height="150"></canvas></div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">درآمد ماهانه</div>
            <div class="card-body"><canvas id="monthlyRevenueChart" height="150"></canvas></div>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header">رزروهای جاری</div>
            <ul class="list-group list-group-flush">
            <?php if ($ongoing): foreach($ongoing as $o): ?>
              <li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($o['user_name']); ?> - <?= htmlspecialchars($o['motor_model']); ?></span><span><?= $o['end_date']; ?></span></li>
            <?php endforeach; else: ?><li class="list-group-item">موردی نیست</li><?php endif; ?>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header">رزروهای نزدیک</div>
            <ul class="list-group list-group-flush">
            <?php if ($upcoming): foreach($upcoming as $u): ?>
              <li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($u['user_name']); ?> - <?= htmlspecialchars($u['motor_model']); ?></span><span><?= $u['start_date']; ?></span></li>
            <?php endforeach; else: ?><li class="list-group-item">موردی نیست</li><?php endif; ?>
            </ul>
          </div>
        </div>
      </div>

      <?php if ($overdue): ?>
      <div class="row g-4 mt-1">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-header bg-danger text-white">هشدار تحویل‌ندادن</div>
            <ul class="list-group list-group-flush">
            <?php foreach($overdue as $ov): ?>
              <li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($ov['user_name']); ?> - <?= htmlspecialchars($ov['motor_model']); ?></span><span><?= $ov['end_date']; ?></span></li>
            <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const dailyLabels = <?= json_encode($dailyLabels); ?>;
const dailyRevenue = <?= json_encode($dailyRevenue); ?>;
const monthlyLabels = <?= json_encode($monthlyLabels); ?>;
const monthlyRevenue = <?= json_encode($monthlyRevenue); ?>;
</script>
<script src="../js/admin-panel.js"></script>
</body>
</html>
