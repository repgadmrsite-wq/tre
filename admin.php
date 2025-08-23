<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// redirect to login if not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// handle add motor form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_motor'])) {
    $name = trim($_POST['motor_name']);
    $price = (int)$_POST['motor_price'];
    if ($name && $price) {
        $stmt = $pdo->prepare('INSERT INTO motorcycles (name, price_per_day) VALUES (?, ?)');
        $stmt->execute([$name, $price]);
    }
    header('Location: admin.php#motors');
    exit;
}

// handle delete motor request
if (isset($_GET['delete_motor'])) {
    $id = (int)$_GET['delete_motor'];
    $stmt = $pdo->prepare('DELETE FROM motorcycles WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: admin.php#motors');
    exit;
}

// handle add admin form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = trim($_POST['admin_name']);
    $email = trim($_POST['admin_email']);
    $pass = md5(trim($_POST['admin_password']));
    if ($name && $email && $_POST['admin_password']) {
        $stmt = $pdo->prepare('INSERT INTO admins (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $pass]);
    }
    header('Location: admin.php#admins');
    exit;
}

// handle delete admin request
if (isset($_GET['delete_admin'])) {
    $id = (int)$_GET['delete_admin'];
    $stmt = $pdo->prepare('DELETE FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: admin.php#admins');
    exit;
}

$totalRevenue = $pdo->query('SELECT COALESCE(SUM(amount),0) FROM bookings')->fetchColumn();
$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalAdmins = $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();

$admins = $pdo->query('SELECT id, name, email FROM admins ORDER BY id DESC')->fetchAll();
$users = $pdo->query('SELECT id, name, email FROM users ORDER BY id DESC')->fetchAll();
$bookings = $pdo->query('SELECT b.id, m.name AS motor_name, u.name AS user_name, b.start_date, b.end_date, b.status, b.amount FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id ORDER BY b.id DESC')->fetchAll();
$motors = $pdo->query('SELECT id, name, price_per_day FROM motorcycles ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>پنل مدیریت - کیش‌ران</title>
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
      <li class="nav-item">
        <a class="nav-link active" href="admin.php">
          <i class="bi bi-speedometer2"></i>
          <span>داشبورد</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#bookings">
          <i class="bi bi-calendar-week"></i>
          <span>مدیریت رزروها</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#admins">
          <i class="bi bi-shield-lock"></i>
          <span>مدیریت مدیران</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#users">
          <i class="bi bi-people-fill"></i>
          <span>مدیریت کاربران</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#motors">
          <i class="bi bi-bicycle"></i>
          <span>مدیریت موتورها</span>
        </a>
      </li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        <span>خروج</span>
      </a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">داشبورد مدیریت</h1>
        <div class="d-flex align-items-center">
          <span class="me-3 d-none d-md-inline"><?= htmlspecialchars($_SESSION['user']['name']); ?> عزیز، خوش آمدید</span>
          <i class="bi bi-person-circle fs-3"></i>
        </div>
      </div>
      <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-primary">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title">درآمد کل</h5>
                <p class="fs-3 fw-bold mb-0"><?= number_format($totalRevenue); ?> تومان</p>
              </div>
              <i class="bi bi-cash-coin fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-success">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title">کاربران</h5>
                <p class="fs-3 fw-bold mb-0"><?= $totalUsers; ?></p>
              </div>
              <i class="bi bi-people-fill fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-warning">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title">رزروهای در انتظار</h5>
                <p class="fs-3 fw-bold mb-0"><?= $pendingBookings; ?></p>
              </div>
              <i class="bi bi-calendar-check fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card text-white bg-info">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title">مدیران</h5>
                <p class="fs-3 fw-bold mb-0"><?= $totalAdmins; ?></p>
              </div>
              <i class="bi bi-shield-lock fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
      </div>

      <div id="admins" class="card mt-4">
        <div class="card-header bg-white border-bottom-0">
          <h5 class="card-title mb-0">مدیریت مدیران</h5>
        </div>
        <div class="card-body">
          <form method="post" class="row g-2">
            <input type="hidden" name="add_admin" value="1">
            <div class="col-md-3">
              <input type="text" name="admin_name" class="form-control" placeholder="نام" required>
            </div>
            <div class="col-md-4">
              <input type="email" name="admin_email" class="form-control" placeholder="ایمیل" required>
            </div>
            <div class="col-md-3">
              <input type="password" name="admin_password" class="form-control" placeholder="رمز عبور" required>
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary w-100" type="submit">افزودن</button>
            </div>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th class="text-end">حذف</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($admins as $a): ?>
                <tr>
                  <td><?= $a['id']; ?></td>
                  <td><?= htmlspecialchars($a['name']); ?></td>
                  <td><?= htmlspecialchars($a['email']); ?></td>
                  <td class="text-end"><a class="btn btn-sm btn-danger" href="?delete_admin=<?= $a['id']; ?>" onclick="return confirm('حذف شود؟')">حذف</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div id="users" class="card mt-4">
        <div class="card-header bg-white border-bottom-0">
          <h5 class="card-title mb-0">مدیریت کاربران</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>نام</th>
                <th>ایمیل</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td><?= $u['id']; ?></td>
                  <td><?= htmlspecialchars($u['name']); ?></td>
                  <td><?= htmlspecialchars($u['email']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div id="bookings" class="card mt-4">
        <div class="card-header bg-white border-bottom-0">
          <h5 class="card-title mb-0">مدیریت رزروها</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>موتور</th>
                <th>کاربر</th>
                <th>تاریخ شروع</th>
                <th>تاریخ پایان</th>
                <th>وضعیت</th>
                <th class="text-end">مبلغ</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $b): ?>
                <tr>
                  <td><?= htmlspecialchars($b['motor_name']); ?></td>
                  <td><?= htmlspecialchars($b['user_name']); ?></td>
                  <td><?= htmlspecialchars($b['start_date']); ?></td>
                  <td><?= htmlspecialchars($b['end_date']); ?></td>
                  <td><?= htmlspecialchars($b['status']); ?></td>
                  <td class="text-end"><?= number_format($b['amount']); ?> تومان</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div id="motors" class="card mt-4">
        <div class="card-header bg-white border-bottom-0">
          <h5 class="card-title mb-0">مدیریت موتورها</h5>
        </div>
        <div class="card-body">
          <form method="post" class="row g-2">
            <input type="hidden" name="add_motor" value="1">
            <div class="col-md-5">
              <input type="text" name="motor_name" class="form-control" placeholder="نام موتور" required>
            </div>
            <div class="col-md-5">
              <input type="number" name="motor_price" class="form-control" placeholder="قیمت روزانه" required>
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary w-100" type="submit">افزودن</button>
            </div>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>نام موتور</th>
                <th>قیمت روزانه</th>
                <th class="text-end">حذف</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($motors as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['name']); ?></td>
                  <td><?= number_format($m['price_per_day']); ?> تومان</td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-danger" href="?delete_motor=<?= $m['id']; ?>" onclick="return confirm('حذف شود؟')">حذف</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
