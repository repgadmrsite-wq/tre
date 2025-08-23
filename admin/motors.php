<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_motor'])) {
    $name = trim($_POST['motor_name']);
    $price_hour = (int)$_POST['price_hour'];
    $price_half = (int)$_POST['price_half'];
    $price_day = (int)$_POST['price_day'];
    if ($name && $price_hour && $price_day) {
        $stmt = $pdo->prepare('INSERT INTO motorcycles (name, price_per_hour, price_half_day, price_per_day) VALUES (?,?,?,?)');
        $stmt->execute([$name, $price_hour, $price_half, $price_day]);
    }
    header('Location: motors.php');
    exit;
}

if (isset($_GET['delete_motor'])) {
    $id = (int)$_GET['delete_motor'];
    $stmt = $pdo->prepare('DELETE FROM motorcycles WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: motors.php');
    exit;
}

$motors = $pdo->query('SELECT id, name, price_per_hour, price_half_day, price_per_day FROM motorcycles ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت موتورها - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت موتورها</h1>
      <form method="post" class="row g-2 mb-4">
        <input type="hidden" name="add_motor" value="1">
        <div class="col-md-3"><input type="text" name="motor_name" class="form-control" placeholder="نام موتور" required></div>
        <div class="col-md-2"><input type="number" name="price_hour" class="form-control" placeholder="قیمت ساعتی" required></div>
        <div class="col-md-2"><input type="number" name="price_half" class="form-control" placeholder="قیمت نیم‌روز" required></div>
        <div class="col-md-2"><input type="number" name="price_day" class="form-control" placeholder="قیمت روزانه" required></div>
        <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>نام</th><th>ساعتی</th><th>نیم‌روز</th><th>روزانه</th><th>حذف</th></tr></thead>
          <tbody>
            <?php foreach ($motors as $m): ?>
            <tr>
              <td><?= $m['id']; ?></td>
              <td><?= htmlspecialchars($m['name']); ?></td>
              <td><?= number_format($m['price_per_hour']); ?></td>
              <td><?= number_format($m['price_half_day']); ?></td>
              <td><?= number_format($m['price_per_day']); ?></td>
              <td><a href="?delete_motor=<?= $m['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف موتور؟');"><i class="bi bi-trash"></i></a></td>
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
