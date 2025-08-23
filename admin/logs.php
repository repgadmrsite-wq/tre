<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super') {
    header('Location: admin.php');
    exit;
}
$logs=$pdo->query('SELECT l.id,a.name,l.action,l.created_at FROM admin_logs l JOIN admins a ON l.admin_id=a.id ORDER BY l.id DESC LIMIT 100')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>گزارش فعالیت‌ها - کیش‌ران</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
<link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center"><a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران - ادمین</a></div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="logs.php"><i class="bi bi-list-check"></i><span>گزارش فعالیت</span></a></li>
    </ul>
    <div class="mt-auto p-3"><a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a></div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">آخرین فعالیت‌ها</h1>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>#</th><th>مدیر</th><th>اقدام</th><th>تاریخ</th></tr></thead>
          <tbody>
            <?php foreach($logs as $l): ?>
              <tr><td><?= $l['id']; ?></td><td><?= htmlspecialchars($l['name']); ?></td><td><?= htmlspecialchars($l['action']); ?></td><td><?= $l['created_at']; ?></td></tr>
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
