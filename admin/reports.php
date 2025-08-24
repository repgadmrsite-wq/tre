<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if (isset($_GET['export']) && $_GET['export']==='bookings') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="bookings.csv"');
    $out=fopen('php://output','w');
    fputcsv($out,['ID','User','Motor','Start','End','Status']);
    $stmt=$pdo->query('SELECT b.id,u.name,m.model,b.start_date,b.end_date,b.status FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id');
    while($row=$stmt->fetch(PDO::FETCH_NUM)){fputcsv($out,$row);}fclose($out);exit;
}

$activeUsers=$pdo->query("SELECT COUNT(*) FROM users WHERE status!='blocked'")->fetchColumn();
$inactiveUsers=$pdo->query("SELECT COUNT(*) FROM users WHERE status='blocked'")->fetchColumn();
$revenueMonthly=$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid' AND YEAR(paid_at)=YEAR(CURDATE()) AND MONTH(paid_at)=MONTH(CURDATE())")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>گزارش‌ها - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link active" href="reports.php"><i class="bi bi-graph-up"></i><span>گزارش‌ها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">گزارش‌گیری</h1>
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">کاربران فعال</h5><p class="fs-3 mb-0"><?= $activeUsers; ?></p></div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">کاربران مسدود</h5><p class="fs-3 mb-0"><?= $inactiveUsers; ?></p></div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">درآمد ماه جاری</h5><p class="fs-3 mb-0"><?= number_format($revenueMonthly); ?> تومان</p></div></div>
        </div>
      </div>
      <a href="?export=bookings" class="btn btn-outline-primary">دانلود CSV رزروها</a>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
