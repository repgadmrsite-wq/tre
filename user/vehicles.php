<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$model = trim($_GET['model'] ?? '');
$color = trim($_GET['color'] ?? '');
$capacity = trim($_GET['capacity'] ?? '');

$colors = $pdo->query("SELECT DISTINCT color FROM motorcycles WHERE color IS NOT NULL AND color<>''")->fetchAll(PDO::FETCH_COLUMN);
$capacities = $pdo->query("SELECT DISTINCT capacity FROM motorcycles WHERE capacity IS NOT NULL ORDER BY capacity")->fetchAll(PDO::FETCH_COLUMN);

$conditions = "m.status='active' AND m.available=1";
$params = [];
if ($model !== '') { $conditions .= " AND m.model LIKE ?"; $params[] = "%$model%"; }
if ($color !== '') { $conditions .= " AND m.color = ?"; $params[] = $color; }
if ($capacity !== '') { $conditions .= " AND m.capacity >= ?"; $params[] = $capacity; }

$query = "SELECT m.*, (SELECT image_path FROM motorcycle_images WHERE motorcycle_id=m.id LIMIT 1) AS image FROM motorcycles m WHERE $conditions ORDER BY m.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$motors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتخاب موتور - کیش‌ران</title>
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
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="map.php"><i class="bi bi-map"></i><span>نقشه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-wallet2"></i><span>پرداخت‌ها</span></a></li>
            <li class="nav-item"><a class="nav-link" href="history.php"><i class="bi bi-clock-history"></i><span>تاریخچه</span></a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>پروفایل</span></a></li>
            <li class="nav-item"><a class="nav-link" href="reviews.php"><i class="bi bi-star"></i><span>نظرات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">انتخاب موتور</h1>
            <form class="row g-2 align-items-end filter-bar mb-4" method="get">
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-tag text-primary"></i> مدل</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($model) ?>" class="form-control rounded-pill">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-palette-fill text-danger"></i> رنگ</label>
                    <select name="color" class="form-select rounded-pill">
                        <option value="">همه رنگ‌ها</option>
                        <?php foreach ($colors as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>" <?= $c==$color?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-people-fill text-success"></i> ظرفیت</label>
                    <select name="capacity" class="form-select rounded-pill">
                        <option value="">همه</option>
                        <?php foreach ($capacities as $cap): ?>
                            <option value="<?= $cap ?>" <?= $capacity==$cap?'selected':'' ?>><?= $cap ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary rounded-pill w-100"><i class="bi bi-search"></i> جستجو</button>
                </div>
            </form>
            <div class="row">
                <?php foreach ($motors as $motor): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card motor-card h-100" data-bs-toggle="modal" data-bs-target="#motor<?= $motor['id'] ?>">
                            <?php if($motor['image']): ?>
                                <img src="../<?= htmlspecialchars($motor['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($motor['model']) ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x300?text=No+Image" class="card-img-top" alt="">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($motor['model']) ?></h5>
                                <p class="text-muted mb-1"><i class="bi bi-palette-fill text-danger"></i> <?= htmlspecialchars($motor['color']) ?></p>
                                <p class="text-muted mb-1"><i class="bi bi-people-fill text-success"></i> ظرفیت: <?= $motor['capacity'] ?></p>
                                <p class="mb-0"><i class="bi bi-cash-coin text-warning"></i> <?= number_format($motor['price_per_day']) ?> تومان / روز</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="motor<?= $motor['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= htmlspecialchars($motor['model']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="carousel<?= $motor['id'] ?>" class="carousel slide mb-3" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php
                                            $imgStmt = $pdo->prepare('SELECT image_path FROM motorcycle_images WHERE motorcycle_id=?');
                                            $imgStmt->execute([$motor['id']]);
                                            $imgs = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
                                            if (!$imgs) { $imgs = ['https://via.placeholder.com/800x500?text=No+Image']; }
                                            foreach($imgs as $idx => $img): ?>
                                                <div class="carousel-item <?= $idx===0?'active':'' ?>">
                                                    <img src="../<?= htmlspecialchars($img) ?>" class="d-block w-100 rounded" alt="">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if(count($imgs) > 1): ?>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $motor['id'] ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $motor['id'] ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-palette-fill text-danger"></i> رنگ: <?= htmlspecialchars($motor['color']) ?></li>
                                        <li><i class="bi bi-people-fill text-success"></i> ظرفیت: <?= $motor['capacity'] ?> نفر</li>
                                        <li><i class="bi bi-cash-coin text-warning"></i> قیمت ساعتی: <?= number_format($motor['price_per_hour']) ?> تومان</li>
                                        <li><i class="bi bi-cash-coin text-warning"></i> نیم‌روز: <?= number_format($motor['price_half_day']) ?> تومان</li>
                                        <li><i class="bi bi-cash-coin text-warning"></i> روزانه: <?= number_format($motor['price_per_day']) ?> تومان</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script-user.js"></script>
</body>
</html>
