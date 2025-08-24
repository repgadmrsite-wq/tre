<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motor_id = (int)$_POST['motor_id'];
    $rating = max(1, min(5, (int)$_POST['rating']));
    $comment = trim($_POST['comment']);

    $stmt = $pdo->prepare('SELECT id FROM reviews WHERE user_id=? AND motorcycle_id=?');
    $stmt->execute([$user_id, $motor_id]);
    if ($existing = $stmt->fetch()) {
        $upd = $pdo->prepare('UPDATE reviews SET rating=?, comment=?, status="pending", created_at=NOW() WHERE id=?');
        $upd->execute([$rating, $comment, $existing['id']]);
    } else {
        $ins = $pdo->prepare('INSERT INTO reviews (user_id, motorcycle_id, rating, comment) VALUES (?,?,?,?)');
        $ins->execute([$user_id, $motor_id, $rating, $comment]);
    }
}

$motorsStmt = $pdo->prepare('SELECT m.id, m.model,
    (SELECT ROUND(AVG(rating),1) FROM reviews r WHERE r.motorcycle_id=m.id AND r.status="approved") AS avg_rating,
    (SELECT rating FROM reviews r WHERE r.motorcycle_id=m.id AND r.user_id=? LIMIT 1) AS user_rating,
    (SELECT comment FROM reviews r WHERE r.motorcycle_id=m.id AND r.user_id=? LIMIT 1) AS user_comment
    FROM motorcycles m ORDER BY m.model');
$motorsStmt->execute([$user_id, $user_id]);
$motors = $motorsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظرات و بازخورد - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-star"></i><span>نظرات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">نظرات و بازخورد</h1>
            <?php foreach ($motors as $m): ?>
                <div class="card mb-4 review-motor-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><?= htmlspecialchars($m['model']) ?></h5>
                        <div class="rating-circle" style="--percent: <?= ($m['avg_rating'] ? ($m['avg_rating']/5*100) : 0) ?>%">
                            <span><?= $m['avg_rating'] ? $m['avg_rating'] : '0' ?></span>
                        </div>
                    </div>
                    <form method="post" class="mb-3">
                        <input type="hidden" name="motor_id" value="<?= $m['id'] ?>">
                        <div class="star-rating mb-2" data-current="<?= (int)$m['user_rating'] ?>">
                            <input type="hidden" name="rating" value="<?= (int)$m['user_rating'] ?>">
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <i class="bi bi-star" data-value="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="mb-2">
                            <textarea class="form-control" name="comment" rows="2" placeholder="نظر شما..."><?= htmlspecialchars($m['user_comment'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-sm btn-primary">ثبت نظر</button>
                    </form>
                    <?php
                        $revStmt = $pdo->prepare('SELECT r.rating, r.comment, u.name, r.user_id FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.motorcycle_id=? AND r.status="approved" ORDER BY r.created_at DESC LIMIT 5');
                        $revStmt->execute([$m['id']]);
                        $reviews = $revStmt->fetchAll();
                    ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="card review-card mb-2">
                            <div class="card-body d-flex">
                                <img src="https://i.pravatar.cc/40?u=<?= $rev['user_id'] ?>" class="rounded-circle ms-3" width="40" height="40" alt="avatar">
                                <div>
                                    <div class="mb-1"><strong><?= htmlspecialchars($rev['name']) ?></strong>
                                        <?php for ($i=1;$i<=5;$i++): ?>
                                            <i class="bi <?= $i <= $rev['rating'] ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="mb-0 small"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
