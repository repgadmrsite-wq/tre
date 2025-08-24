<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_booking'])) {
    csrf_validate();
    $user_id = (int)$_POST['user_id'];
    $motor_id = (int)$_POST['motorcycle_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $status = $_POST['status'];
    $amount = (int)$_POST['amount'];
    $discount_code = trim($_POST['discount_code']);
    $discount_id = null;
    if ($user_id && $motor_id && $start && $end) {
        $st = $pdo->prepare('SELECT status FROM users WHERE id=?');
        $st->execute([$user_id]);
        $userStatus = $st->fetchColumn();
        if ($userStatus !== 'blocked') {
            if ($discount_code) {
                $dstmt = $pdo->prepare('SELECT * FROM discounts WHERE code=?');
                $dstmt->execute([$discount_code]);
                if ($disc = $dstmt->fetch(PDO::FETCH_ASSOC)) {
                    $today = date('Y-m-d');
                    if ((!$disc['start_date'] || $disc['start_date'] <= $today) && (!$disc['end_date'] || $disc['end_date'] >= $today) &&
                        (!$disc['usage_limit'] || $disc['used'] < $disc['usage_limit']) &&
                        (!$disc['vip_only'] || $userStatus=='vip') &&
                        (!$disc['motor_id'] || $disc['motor_id']==$motor_id)) {
                        $ustmt = $pdo->prepare('SELECT used_count FROM discount_usages WHERE discount_id=? AND user_id=?');
                        $ustmt->execute([$disc['id'],$user_id]);
                        $usedCount = $ustmt->fetchColumn() ?: 0;
                        if (!$disc['per_user_limit'] || $usedCount < $disc['per_user_limit']) {
                            $discount_id = $disc['id'];
                            if ($disc['type']=='percent') {
                                $amount = max(0, $amount - floor($amount * $disc['value']/100));
                            } else {
                                $amount = max(0, $amount - $disc['value']);
                            }
                            $pdo->prepare('UPDATE discounts SET used=used+1 WHERE id=?')->execute([$discount_id]);
                            $pdo->prepare('INSERT INTO discount_usages (discount_id,user_id,used_count) VALUES (?,?,1) ON DUPLICATE KEY UPDATE used_count=used_count+1')->execute([$discount_id,$user_id]);
                        }
                    }
                }
            }
            $pdo->prepare('INSERT INTO bookings (user_id, motorcycle_id, start_date, end_date, status, amount, discount_id) VALUES (?,?,?,?,?,?,?)')->execute([$user_id, $motor_id, $start, $end, $status, $amount, $discount_id]);
            $pdo->prepare('INSERT INTO notifications (message) VALUES (?)')->execute(["رزرو جدید ثبت شد"]);
        } else {
            $_SESSION['error'] = 'کاربر بلاک شده است';
        }
    }
    header('Location: bookings.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking'])) {
    csrf_validate();
    $id = (int)$_POST['booking_id'];
    $user_id = (int)$_POST['user_id'];
    $motor_id = (int)$_POST['motorcycle_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $status = $_POST['status'];
    $amount = (int)$_POST['amount'];
    $discount_id = (int)($_POST['discount_id'] ?? 0) ?: null;
    $pdo->prepare('UPDATE bookings SET user_id=?, motorcycle_id=?, start_date=?, end_date=?, status=?, amount=?, discount_id=? WHERE id=?')->execute([$user_id, $motor_id, $start, $end, $status, $amount, $discount_id, $id]);
    header('Location: bookings.php');
    exit;
}
if (isset($_GET['email'])) {
    $id = (int)$_GET['email'];
    $stmt = $pdo->prepare('SELECT b.id, u.email, u.name, m.model FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id WHERE b.id=?');
    $stmt->execute([$id]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $message = "رزرو شما برای موتور {$row['model']} ثبت شد.";
        @mail($row['email'], 'تایید رزرو', $message);
    }
    header('Location: bookings.php');
    exit;
}
if (isset($_GET['sms'])) {
    $id = (int)$_GET['sms'];
    $stmt = $pdo->prepare('SELECT b.id, u.name FROM bookings b JOIN users u ON b.user_id=u.id WHERE b.id=?');
    $stmt->execute([$id]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        file_put_contents(__DIR__.'/../sms.log', "SMS to {$row['name']} for booking {$row['id']}\n", FILE_APPEND);
    }
    header('Location: bookings.php');
    exit;
}

$filterUser = (int)($_GET['user'] ?? 0);
$filterMotor = (int)($_GET['motor'] ?? 0);
$filterStatus = $_GET['status'] ?? '';
$filterStart = $_GET['start'] ?? '';
$filterEnd = $_GET['end'] ?? '';
$query = "SELECT b.id, m.model AS motor_model, u.name AS user_name, b.start_date, b.end_date, b.status, b.amount, d.code AS discount_code FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id LEFT JOIN discounts d ON b.discount_id=d.id WHERE 1=1";
$params = [];
if ($filterUser) { $query .= ' AND b.user_id=?'; $params[] = $filterUser; }
if ($filterMotor) { $query .= ' AND b.motorcycle_id=?'; $params[] = $filterMotor; }
if ($filterStatus) { $query .= ' AND b.status=?'; $params[] = $filterStatus; }
if ($filterStart) { $query .= ' AND b.start_date>=?'; $params[] = $filterStart; }
if ($filterEnd) { $query .= ' AND b.end_date<=?'; $params[] = $filterEnd; }
$query .= ' ORDER BY b.id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query('SELECT id, name, status FROM users ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$motors = $pdo->query('SELECT id, model FROM motorcycles ORDER BY model')->fetchAll(PDO::FETCH_ASSOC);

$editBooking = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT b.*, d.code AS discount_code FROM bookings b LEFT JOIN discounts d ON b.discount_id=d.id WHERE b.id=?');
    $stmt->execute([$id]);
    $editBooking = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت رزروها - کیش‌ران</title>
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
      <li class="nav-item"><a class="nav-link active" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="finance.php"><i class="bi bi-receipt"></i><span>مالی</span></a></li>
      <li class="nav-item"><a class="nav-link" href="discounts.php"><i class="bi bi-ticket-perforated"></i><span>تخفیف‌ها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت رزروها</h1>
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <form method="post" class="row g-2 mb-4">
        <?= csrf_input(); ?>
        <?php if($editBooking): ?>
          <input type="hidden" name="update_booking" value="1">
          <input type="hidden" name="booking_id" value="<?= $editBooking['id']; ?>">
          <input type="hidden" name="discount_id" value="<?= $editBooking['discount_id']; ?>">
        <?php else: ?>
          <input type="hidden" name="add_booking" value="1">
        <?php endif; ?>
        <div class="col-md-3">
          <select name="user_id" class="form-select" required>
            <option value="">مشتری</option>
            <?php foreach($users as $u): ?>
              <option value="<?= $u['id']; ?>" <?= isset($editBooking['user_id']) && $editBooking['user_id']==$u['id'] ? 'selected' : ''; ?> <?= $u['status']=='blocked' ? 'disabled' : ''; ?>><?= htmlspecialchars($u['name']); ?><?= $u['status']=='blocked' ? ' (بلاک)' : ''; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select name="motorcycle_id" class="form-select" required>
            <option value="">موتور</option>
            <?php foreach($motors as $m): ?>
              <option value="<?= $m['id']; ?>" <?= isset($editBooking['motorcycle_id']) && $editBooking['motorcycle_id']==$m['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($m['model']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2"><input type="date" name="start_date" class="form-control" value="<?= $editBooking['start_date'] ?? ''; ?>" required></div>
        <div class="col-md-2"><input type="date" name="end_date" class="form-control" value="<?= $editBooking['end_date'] ?? ''; ?>" required></div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="pending" <?= isset($editBooking['status']) && $editBooking['status']=='pending' ? 'selected' : ''; ?>>در انتظار پرداخت</option>
            <option value="confirmed" <?= isset($editBooking['status']) && $editBooking['status']=='confirmed' ? 'selected' : ''; ?>>تایید شده</option>
            <option value="in_use" <?= isset($editBooking['status']) && $editBooking['status']=='in_use' ? 'selected' : ''; ?>>در حال استفاده</option>
            <option value="returned" <?= isset($editBooking['status']) && $editBooking['status']=='returned' ? 'selected' : ''; ?>>تحویل داده شد</option>
            <option value="cancelled" <?= isset($editBooking['status']) && $editBooking['status']=='cancelled' ? 'selected' : ''; ?>>لغو شده</option>
          </select>
        </div>
        <div class="col-md-2"><input type="text" name="discount_code" class="form-control" placeholder="کد تخفیف" value="<?= $editBooking['discount_code'] ?? ''; ?>" <?= $editBooking ? 'readonly' : '' ?>></div>
        <div class="col-md-2"><input type="number" name="amount" class="form-control" placeholder="مبلغ" value="<?= $editBooking['amount'] ?? ''; ?>" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><?= $editBooking ? 'ویرایش' : 'افزودن'; ?></button></div>
      </form>
      <form method="get" class="row g-2 mb-4">
        <div class="col-md-2">
          <select name="user" class="form-select">
            <option value="">مشتری</option>
            <?php foreach($users as $u): ?>
              <option value="<?= $u['id']; ?>" <?= $filterUser==$u['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($u['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <select name="motor" class="form-select">
            <option value="">موتور</option>
            <?php foreach($motors as $m): ?>
              <option value="<?= $m['id']; ?>" <?= $filterMotor==$m['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($m['model']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="">وضعیت</option>
            <option value="pending" <?= $filterStatus=='pending' ? 'selected' : ''; ?>>در انتظار پرداخت</option>
            <option value="confirmed" <?= $filterStatus=='confirmed' ? 'selected' : ''; ?>>تایید شده</option>
            <option value="in_use" <?= $filterStatus=='in_use' ? 'selected' : ''; ?>>در حال استفاده</option>
            <option value="returned" <?= $filterStatus=='returned' ? 'selected' : ''; ?>>تحویل داده شد</option>
            <option value="cancelled" <?= $filterStatus=='cancelled' ? 'selected' : ''; ?>>لغو شده</option>
          </select>
        </div>
        <div class="col-md-2"><input type="date" name="start" value="<?= htmlspecialchars($filterStart); ?>" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="end" value="<?= htmlspecialchars($filterEnd); ?>" class="form-control"></div>
        <div class="col-md-2"><button class="btn btn-secondary w-100" type="submit">فیلتر</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>کاربر</th><th>موتور</th><th>شروع</th><th>پایان</th><th>وضعیت</th><th>تخفیف</th><th>مبلغ</th><th>عملیات</th></tr></thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?= $b['id']; ?></td>
              <td><?= htmlspecialchars($b['user_name']); ?></td>
              <td><?= htmlspecialchars($b['motor_model']); ?></td>
              <td><?= $b['start_date']; ?></td>
              <td><?= $b['end_date']; ?></td>
              <td><?= $b['status']; ?></td>
              <td><?= $b['discount_code'] ?? '-'; ?></td>
              <td><?= number_format($b['amount']); ?></td>
              <td>
                <a href="contract.php?id=<?= $b['id']; ?>" class="btn btn-sm btn-secondary">قرارداد</a>
                <a href="?email=<?= $b['id']; ?>" class="btn btn-sm btn-info">ایمیل</a>
                <a href="?sms=<?= $b['id']; ?>" class="btn btn-sm btn-success">پیامک</a>
                <a href="?edit=<?= $b['id']; ?>" class="btn btn-sm btn-warning">ویرایش</a>
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
