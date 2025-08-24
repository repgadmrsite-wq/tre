<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$user_id = $_SESSION['user']['id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $booking_id = (int)($_POST['booking_id'] ?? 0);
    if (isset($_POST['cancel'])) {
        $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status IN ('pending','confirmed')");
        $stmt->execute([$booking_id, $user_id]);
        if ($stmt->rowCount()) {
            $message = 'رزرو با موفقیت لغو شد.';
        } else {
            $error = 'امکان لغو این رزرو وجود ندارد.';
        }
    } elseif (isset($_POST['change'])) {
        $start = $_POST['start_date'] ?? '';
        $end = $_POST['end_date'] ?? '';
        $stmt = $pdo->prepare("UPDATE bookings SET start_date=?, end_date=? WHERE id=? AND user_id=? AND status IN ('pending','confirmed')");
        $stmt->execute([$start, $end, $booking_id, $user_id]);
        if ($stmt->rowCount()) {
            $message = 'تاریخ رزرو به‌روزرسانی شد.';
        } else {
            $error = 'امکان تغییر تاریخ وجود ندارد.';
        }
    }
}

$statusFilter = $_GET['status'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$statusLabels = [
    'pending'   => 'در انتظار',
    'confirmed' => 'تایید شده',
    'in_use'    => 'در حال استفاده',
    'returned'  => 'تحویل داده شد',
    'cancelled' => 'لغو شده'
];

$statusClasses = [
    'pending'   => 'secondary',
    'confirmed' => 'info',
    'in_use'    => 'primary',
    'returned'  => 'success',
    'cancelled' => 'danger'
];

$sql = "SELECT b.*, m.model FROM bookings b JOIN motorcycles m ON b.motorcycle_id = m.id WHERE b.user_id=?";
$params = [$user_id];
if ($statusFilter) {
    $sql .= " AND b.status=?";
    $params[] = $statusFilter;
}
if ($from) {
    $sql .= " AND b.start_date >= ?";
    $params[] = $from;
}
if ($to) {
    $sql .= " AND b.end_date <= ?";
    $params[] = $to;
}
$sql .= " ORDER BY b.start_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رزروهای من - KISH UP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar d-flex flex-column p-0">
        <div class="sidebar-header text-center">
            <a class="navbar-brand fs-4 text-white" href="../index.html">KISH UP</a>
        </div>
        <ul class="nav flex-column my-4">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-calendar2-check"></i><span>رزروهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="vehicles.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="map.php"><i class="bi bi-map"></i><span>نقشه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history"></i><span>تاریخچه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
            <li class="nav-item"><a class="nav-link" href="support.php"><i class="bi bi-life-preserver"></i><span>پشتیبانی</span></a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i><span>تنظیمات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">رزروهای من</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form class="row g-2 mb-4">
                <div class="col-md-3"><input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control" placeholder="از تاریخ"></div>
                <div class="col-md-3"><input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control" placeholder="تا تاریخ"></div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">همه وضعیت‌ها</option>
                        <?php foreach ($statusLabels as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $statusFilter === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">فیلتر</button>
                </div>
            </form>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>موتور</th>
                        <th>از</th>
                        <th>تا</th>
                        <th>مبلغ (تومان)</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['model']) ?></td>
                        <td><?= $b['start_date'] ?></td>
                        <td><?= $b['end_date'] ?></td>
                        <td><?= number_format($b['amount']) ?></td>
                        <td><span class="badge bg-<?= $statusClasses[$b['status']] ?>"><?= $statusLabels[$b['status']] ?></span></td>
                        <td>
                            <a href="contract.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-secondary">قرارداد</a>
                            <?php if (in_array($b['status'], ['pending','confirmed'])): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-change-date" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?= $b['id'] ?>" data-start="<?= $b['start_date'] ?>" data-end="<?= $b['end_date'] ?>">تغییر تاریخ</button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-cancel-booking" data-id="<?= $b['id'] ?>">لغو</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <tr><td colspan="6" class="text-center">رزروی یافت نشد.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

        </div>
    </main>
</div>

<form method="post" id="cancelForm" class="d-none">
    <?= csrf_input() ?>
    <input type="hidden" name="booking_id">
    <input type="hidden" name="cancel" value="1">
</form>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?= csrf_input() ?>
        <input type="hidden" name="booking_id" id="editBookingId">
        <div class="modal-header">
          <h5 class="modal-title">تغییر تاریخ رزرو</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">از</label>
            <input type="date" class="form-control" name="start_date" id="editStart" required>
          </div>
          <div class="mb-3">
            <label class="form-label">تا</label>
            <input type="date" class="form-control" name="end_date" id="editEnd" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
          <button type="submit" name="change" value="1" class="btn btn-primary">ذخیره</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>

