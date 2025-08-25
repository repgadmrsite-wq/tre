<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    csrf_validate();
    $name = trim($_POST['user_name']);
    $phone = trim($_POST['user_phone']);
    $email = trim($_POST['user_email']);
    $pass = password_hash(trim($_POST['user_password']), PASSWORD_DEFAULT);
    $status = $_POST['user_status'];
    $note = trim($_POST['user_note']);
    if ($name && $email && $_POST['user_password']) {
        $stmt = $pdo->prepare('INSERT INTO users (name, phone, email, password, status, note) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$name, $phone, $email, $pass, $status, $note]);
    }
    header('Location: users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    csrf_validate();
    $id = (int)$_POST['user_id'];
    $status = $_POST['status'];
    $note = trim($_POST['note']);
    $stmt = $pdo->prepare('UPDATE users SET status=?, note=? WHERE id=?');
    $stmt->execute([$status, $note, $id]);
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

$users = $pdo->query('SELECT u.id, u.name, u.phone, u.email, u.status, u.note, COUNT(b.id) AS bookings_count, COALESCE(SUM(b.amount),0) AS total_paid FROM users u LEFT JOIN bookings b ON b.user_id=u.id GROUP BY u.id ORDER BY u.id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت کاربران - KISH UP</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='users'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت کاربران</h1>
      <form method="post" class="row g-2 mb-4 card p-3">
        <?= csrf_input(); ?>
        <input type="hidden" name="add_user" value="1">
        <div class="col-md-2"><input type="text" name="user_name" class="form-control" placeholder="نام" required></div>
        <div class="col-md-2"><input type="text" name="user_phone" class="form-control" placeholder="تلفن"></div>
        <div class="col-md-3"><input type="email" name="user_email" class="form-control" placeholder="ایمیل" required></div>
        <div class="col-md-2"><input type="password" name="user_password" class="form-control" placeholder="رمز عبور" required></div>
        <div class="col-md-2">
          <select name="user_status" class="form-select">
            <option value="regular">معمولی</option>
            <option value="vip">VIP</option>
            <option value="blocked">بلاک</option>
          </select>
        </div>
        <div class="col-md-12"><input type="text" name="user_note" class="form-control" placeholder="یادداشت"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">افزودن</button></div>
      </form>
      <div class="table-responsive card p-3">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>نام</th><th>تلفن</th><th>ایمیل</th><th>رزروها</th><th>پرداخت</th><th>وضعیت</th><th>یادداشت</th><th>ویرایش</th><th>حذف</th></tr></thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u['id']; ?></td>
              <td><?= htmlspecialchars($u['name']); ?></td>
              <td><?= htmlspecialchars($u['phone']); ?></td>
              <td><?= htmlspecialchars($u['email']); ?></td>
              <td><?= $u['bookings_count']; ?></td>
              <td><?= number_format($u['total_paid']); ?></td>
              <td><?= $u['status']; ?></td>
              <td><?= htmlspecialchars($u['note']); ?></td>
              <td>
                <form method="post" class="d-flex flex-wrap align-items-center gap-1">
                  <?= csrf_input(); ?>
                  <input type="hidden" name="update_user" value="1">
                  <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                  <select name="status" class="form-select form-select-sm">
                    <option value="regular" <?= $u['status']=='regular'?'selected':''; ?>>معمولی</option>
                    <option value="vip" <?= $u['status']=='vip'?'selected':''; ?>>VIP</option>
                    <option value="blocked" <?= $u['status']=='blocked'?'selected':''; ?>>بلاک</option>
                  </select>
                  <input type="text" name="note" class="form-control form-control-sm" value="<?= htmlspecialchars($u['note']); ?>" placeholder="یادداشت">
                  <button class="btn btn-sm btn-primary" type="submit">ذخیره</button>
                </form>
              </td>
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
