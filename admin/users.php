<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);
    $pass = md5(trim($_POST['user_password']));
    if ($name && $email && $_POST['user_password']) {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $pass]);
    }
    header('Location: users.php');
    exit;
}

if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: users.php');
    exit;
}

$users = $pdo->query('SELECT id, name, email FROM users ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت کاربران - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت کاربران</h1>
      <form method="post" class="row g-2 mb-4">
        <input type="hidden" name="add_user" value="1">
        <div class="col-md-3"><input type="text" name="user_name" class="form-control" placeholder="نام" required></div>
        <div class="col-md-4"><input type="email" name="user_email" class="form-control" placeholder="ایمیل" required></div>
        <div class="col-md-3"><input type="password" name="user_password" class="form-control" placeholder="رمز عبور" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>نام</th><th>ایمیل</th><th>حذف</th></tr></thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u['id']; ?></td>
              <td><?= htmlspecialchars($u['name']); ?></td>
              <td><?= htmlspecialchars($u['email']); ?></td>
              <td><a href="?delete_user=<?= $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف کاربر؟');"><i class="bi bi-trash"></i></a></td>
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
