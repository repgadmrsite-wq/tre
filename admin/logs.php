<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';
$logs=$pdo->query('SELECT l.id,a.name,l.action,l.created_at FROM admin_logs l JOIN admins a ON l.admin_id=a.id ORDER BY l.id DESC LIMIT 100')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>گزارش فعالیت‌ها - KISH UP</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='logs'; include 'sidebar.php'; ?>
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
