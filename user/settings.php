<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$user_id = $user['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'] === 'en' ? 'en' : 'fa';
    $notify = isset($_POST['notify_email']) ? 1 : 0;
    $stmt = $pdo->prepare('UPDATE users SET language = ?, notify_email = ? WHERE id = ?');
    $stmt->execute([$language, $notify, $user_id]);
    $_SESSION['user']['language'] = $language;
    $_SESSION['user']['notify_email'] = $notify;
    $message = 'تنظیمات ذخیره شد';
}

$stmt = $pdo->prepare('SELECT language, notify_email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$prefs = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیمات - کیش‌ران</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar d-flex flex-column p-0">
        <div class="sidebar-header text-center">
            <a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران</a>
        </div>
        <ul class="nav flex-column my-4">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
            <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar2-check"></i><span>رزروهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="vehicles.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="map.php"><i class="bi bi-map"></i><span>نقشه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history"></i><span>تاریخچه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
            <li class="nav-item"><a class="nav-link" href="support.php"><i class="bi bi-life-preserver"></i><span>پشتیبانی</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-gear"></i><span>تنظیمات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">تنظیمات</h1>
            <?php if ($message): ?><div class="alert alert-success" role="alert"><?= $message ?></div><?php endif; ?>
            <form method="post" class="card p-3 shadow-sm" style="max-width:500px;">
                <div class="mb-3">
                    <label class="form-label">زبان</label>
                    <select name="language" class="form-select">
                        <option value="fa" <?= $prefs['language']==='fa' ? 'selected' : '' ?>>فارسی</option>
                        <option value="en" <?= $prefs['language']==='en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="notify_email" name="notify_email" <?= $prefs['notify_email'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="notify_email">ارسال اعلان به ایمیل</label>
                </div>
                <button class="btn btn-primary">ذخیره تنظیمات</button>
            </form>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
