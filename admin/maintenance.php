<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $motor = (int)$_POST['motorcycle_id'];
    $date = $_POST['service_date'];
    $mileage = (int)$_POST['mileage'];
    $notes = trim($_POST['notes']);
    $cost = (int)$_POST['cost'];
    $stmt = $pdo->prepare('INSERT INTO maintenance (motorcycle_id, service_date, mileage, notes, cost) VALUES (?,?,?,?,?)');
    $stmt->execute([$motor,$date,$mileage,$notes,$cost]);
    $pdo->prepare('INSERT INTO notifications (message) VALUES (?)')->execute(["سرویس جدید ثبت شد"]);
    header('Location: maintenance.php');
    exit;
}

$records = $pdo->query('SELECT mn.id,m.model,mn.service_date,mn.mileage,mn.cost FROM maintenance mn JOIN motorcycles m ON mn.motorcycle_id=m.id ORDER BY mn.service_date DESC')->fetchAll();
$motors = $pdo->query('SELECT id,model FROM motorcycles ORDER BY model')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سرویس و نگهداری - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="maintenance.php"><i class="bi bi-wrench"></i><span>سرویس‌ها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">ثبت سرویس جدید</h1>
      <form method="post" class="row g-2 mb-4">
        <?= csrf_input(); ?>
        <div class="col-md-3"><select name="motorcycle_id" class="form-select" required>
          <?php foreach($motors as $m): ?><option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['model']); ?></option><?php endforeach; ?>
        </select></div>
        <div class="col-md-2"><input type="date" name="service_date" class="form-control" required></div>
        <div class="col-md-2"><input type="number" name="mileage" class="form-control" placeholder="کیلومتر"></div>
        <div class="col-md-3"><input type="text" name="notes" class="form-control" placeholder="توضیحات"></div>
        <div class="col-md-1"><input type="number" name="cost" class="form-control" placeholder="هزینه"></div>
        <div class="col-md-1"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>موتور</th><th>تاریخ</th><th>کیلومتر</th><th>هزینه</th></tr></thead>
          <tbody>
            <?php foreach($records as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td><td><?= htmlspecialchars($r['model']); ?></td><td><?= $r['service_date']; ?></td><td><?= $r['mileage']; ?></td><td><?= $r['cost']; ?></td>
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
