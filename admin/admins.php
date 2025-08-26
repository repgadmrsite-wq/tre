<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    csrf_validate();
    $name = trim($_POST['admin_name']);
    $email = trim($_POST['admin_email']);
    $role = $_POST['admin_role'];
    $pass = password_hash(trim($_POST['admin_password']), PASSWORD_DEFAULT);
    if ($name && $email && $_POST['admin_password']) {
        $stmt = $pdo->prepare('INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $pass, $role]);
        $log = $pdo->prepare('INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)');
        $log->execute([$_SESSION['user']['id'], "add admin $email"]);
    }
    header('Location: admins.php');
    exit;
}

if (isset($_GET['delete_admin'])) {
    $id = (int)$_GET['delete_admin'];
    $stmt = $pdo->prepare('DELETE FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    $log = $pdo->prepare('INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)');
    $log->execute([$_SESSION['user']['id'], "delete admin #$id"]);
    header('Location: admins.php');
    exit;
}

$admins = $pdo->query('SELECT id, name, email, role FROM admins ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت مدیران - KISH UP</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
<link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='admins'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت مدیران</h1>
      <form method="post" class="row g-2 mb-4 card p-3">
        <?= csrf_input(); ?>
        <input type="hidden" name="add_admin" value="1">
        <div class="col-md-2"><input type="text" name="admin_name" class="form-control" placeholder="نام" required></div>
        <div class="col-md-3"><input type="email" name="admin_email" class="form-control" placeholder="ایمیل" required></div>
        <div class="col-md-2"><select name="admin_role" class="form-select"><option value="support">پشتیبان</option><option value="accountant">حسابدار</option><option value="mechanic">مکانیک</option><option value="super">مدیرکل</option></select></div>
        <div class="col-md-3"><input type="password" name="admin_password" class="form-control" placeholder="رمز عبور" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>
      <div class="table-responsive card p-3">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>نام</th><th>ایمیل</th><th>نقش</th><th>حذف</th></tr></thead>
          <tbody>
            <?php foreach ($admins as $a): ?>
            <tr>
              <td><?= $a['id']; ?></td>
              <td><?= htmlspecialchars($a['name']); ?></td>
              <td><?= htmlspecialchars($a['email']); ?></td>
              <td><?= htmlspecialchars($a['role']); ?></td>
              <td><a href="?delete_admin=<?= $a['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف مدیر؟');"><i class="bi bi-trash"></i></a></td>
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
