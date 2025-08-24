<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';
$user_id = $_SESSION['user']['id'];

if (isset($_GET['ajax'])) {
    $stmt = $pdo->prepare('SELECT id, message FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if (isset($_GET['read'])) {
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([intval($_GET['read']), $user_id]);
    exit;
}

$stmt = $pdo->prepare('SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اعلان‌ها - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">اعلان‌ها</h1>
            <?php if (count($notifications) > 0): ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($notifications as $n): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $n['is_read'] ? '' : 'fw-bold'; ?>">
                    <span><i class="bi bi-bell me-2 text-warning"></i><?= htmlspecialchars($n['message']) ?></span>
                    <?php if (!$n['is_read']): ?>
                    <button class="btn btn-sm btn-outline-secondary mark-read" data-id="<?= $n['id'] ?>">خوانده شد</button>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="text-muted">اعلانی وجود ندارد.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script-user.js"></script>
<script>
document.querySelectorAll('.mark-read').forEach(function(btn){
    btn.addEventListener('click', function(){
        fetch('notifications.php?read=' + btn.dataset.id).then(function(){
            btn.closest('li').classList.remove('fw-bold');
            btn.remove();
        });
    });
});
</script>
</body>
</html>
