<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $stmt = $pdo->prepare('UPDATE reviews SET status="approved" WHERE id=?');
    $stmt->execute([$id]);
}
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $stmt = $pdo->prepare('UPDATE reviews SET status="rejected" WHERE id=?');
    $stmt->execute([$id]);
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM reviews WHERE id=?');
    $stmt->execute([$id]);
}

$reviews = $pdo->query('SELECT r.id,u.name user_name,m.model motor_name,r.rating,r.comment,r.status FROM reviews r JOIN users u ON r.user_id=u.id JOIN motorcycles m ON r.motorcycle_id=m.id ORDER BY r.id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نظرات کاربران - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link" href="discounts.php"><i class="bi bi-ticket-perforated"></i><span>تخفیف‌ها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="reviews.php"><i class="bi bi-chat-left-text"></i><span>نظرات</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت نظرات</h1>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>کاربر</th><th>موتور</th><th>امتیاز</th><th>نظر</th><th>وضعیت</th><th>اقدامات</th></tr></thead>
          <tbody>
            <?php foreach($reviews as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td>
              <td><?= htmlspecialchars($r['user_name']); ?></td>
              <td><?= htmlspecialchars($r['motor_name']); ?></td>
              <td><?= $r['rating']; ?></td>
              <td><?= htmlspecialchars($r['comment']); ?></td>
              <td><?= $r['status']; ?></td>
              <td>
                <?php if($r['status']!=='approved'): ?><a class="btn btn-sm btn-success" href="?approve=<?= $r['id']; ?>">تایید</a><?php endif; ?>
                <?php if($r['status']!=='rejected'): ?><a class="btn btn-sm btn-warning" href="?reject=<?= $r['id']; ?>">رد</a><?php endif; ?>
                <a class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>" onclick="return confirm('حذف نظر؟');">حذف</a>
              </td>
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
