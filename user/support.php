<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/notify.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject && $message) {
        $stmt = $pdo->prepare('INSERT INTO tickets (user_id, subject, message) VALUES (?,?,?)');
        $stmt->execute([$user_id, $subject, $message]);

        $admins = $pdo->query('SELECT id,email FROM admins')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($admins as $a) {
            $pdo->prepare('INSERT INTO notifications (admin_id, message) VALUES (?,?)')
                ->execute([$a['id'], "تیکت جدید توسط {$user['name']} ثبت شد"]);
            sendEmail($a['email'], 'تیکت جدید', "کاربر {$user['name']} تیکت جدیدی با موضوع {$subject} ثبت کرد");
        }
    }
    header('Location: support.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پشتیبانی - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-life-preserver"></i><span>پشتیبانی</span></a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i><span>تنظیمات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">پشتیبانی</h1>
            <form method="post" class="card p-3 mb-4 shadow-sm">
                <?= csrf_input(); ?>
                <div class="mb-3">
                    <label class="form-label">موضوع</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">پیام</label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>
                <button class="btn btn-primary">ارسال تیکت</button>
            </form>
            <?php foreach ($tickets as $t): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($t['subject']) ?></h5>
                            <small class="text-muted"><?php
                                if ($t['status'] === 'answered') echo 'پاسخ داده شده';
                                elseif ($t['status'] === 'closed') echo 'بسته شده';
                                else echo 'در انتظار پاسخ';
                            ?></small>
                        </div>
                        <?php if (!empty($t['response'])): ?>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resp<?= $t['id'] ?>">مشاهده پاسخ</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($t['response'])): ?>
                <div class="modal fade" id="resp<?= $t['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">پاسخ پشتیبانی</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body"><?= nl2br(htmlspecialchars($t['response'])) ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
