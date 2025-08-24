<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if (isset($_GET['read'])) {
    $id=(int)$_GET['read'];
    $pdo->prepare('UPDATE notifications SET is_read=1 WHERE id=?')->execute([$id]);
}
$notes=$pdo->query('SELECT id,message,is_read,created_at FROM notifications WHERE admin_id IS NULL OR admin_id='.$_SESSION['user']['id'].' ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>اعلان‌ها - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='notifications'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">اعلان‌ها</h1>
      <ul class="list-group">
        <?php foreach($notes as $n): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center <?= $n['is_read']? '':'list-group-item-warning'; ?>">
            <span><?= htmlspecialchars($n['message']); ?></span>
            <small>
              <a href="?read=<?= $n['id']; ?>" class="btn btn-sm btn-outline-secondary">خواندم</a>
            </small>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
