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
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?');
        $stmt->execute([$name, $email, $phone, $user_id]);
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $message = 'اطلاعات با موفقیت به‌روزرسانی شد.';
    } elseif (isset($_POST['change_password'])) {
        $current = md5(trim($_POST['current_password']));
        $new = trim($_POST['new_password']);
        $confirm = trim($_POST['confirm_password']);
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $stored = $stmt->fetchColumn();
        if ($stored !== $current) {
            $error = 'رمز عبور فعلی نادرست است.';
        } elseif ($new !== $confirm) {
            $error = 'رمز عبور جدید با تکرار آن مطابقت ندارد.';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([md5($new), $user_id]);
            $message = 'رمز عبور با موفقیت تغییر کرد.';
        }
    }
}

$stmt = $pdo->prepare('SELECT name, email, phone FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$info = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل کاربر - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">پروفایل کاربری</h1>
            <?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card form-card p-4">
                        <h5 class="mb-3">اطلاعات شخصی</h5>
                        <form method="post">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="mb-3">
                                <label class="form-label">نام</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($info['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ایمیل</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($info['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">شماره تماس</label>
                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($info['phone']) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card form-card p-4">
                        <h5 class="mb-3"><i class="bi bi-shield-lock me-2"></i>تغییر رمز عبور</h5>
                        <form method="post">
                            <input type="hidden" name="change_password" value="1">
                            <div class="mb-3">
                                <label class="form-label">رمز عبور فعلی</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">رمز عبور جدید</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <div id="passwordHelp" class="form-text"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">تکرار رمز عبور جدید</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-warning">تغییر رمز عبور</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
