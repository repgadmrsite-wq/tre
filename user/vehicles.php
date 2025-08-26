<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$model = trim($_GET['model'] ?? '');
$availability = $_GET['availability'] ?? '';
$min_price = trim($_GET['min_price'] ?? '');
$max_price = trim($_GET['max_price'] ?? '');
$min_range = trim($_GET['min_range'] ?? '');
$max_range = trim($_GET['max_range'] ?? '');

$conditions = "m.status='active'";
$params = [];
if ($model !== '') { $conditions .= " AND m.model LIKE ?"; $params[] = "%$model%"; }
if ($availability !== '') { $conditions .= " AND m.available = ?"; $params[] = $availability; }
if ($min_price !== '') { $conditions .= " AND m.price_per_day >= ?"; $params[] = $min_price; }
if ($max_price !== '') { $conditions .= " AND m.price_per_day <= ?"; $params[] = $max_price; }
if ($min_range !== '') { $conditions .= " AND m.range_km >= ?"; $params[] = $min_range; }
if ($max_range !== '') { $conditions .= " AND m.range_km <= ?"; $params[] = $max_range; }

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
    <title>انتخاب موتور - KISH UP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar d-flex flex-column p-0">
        <div class="sidebar-header text-center">
            <a class="navbar-brand d-block" href="../index.html">
                <img src="../img/kishup-logo.png" alt="KishUp" class="sidebar-logo mx-auto">
            </a>
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
            <li class="nav-item"><a class="nav-link" href="support.php"><i class="bi bi-life-preserver"></i><span>پشتیبانی</span></a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i><span>تنظیمات</span></a></li>
        </ul>
        <div class="mt-auto p-3">
            <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج از حساب</span></a>
        </div>
    </aside>
    <main class="main-content">
        <div class="container-fluid">
            <h1 class="h2 mb-4">انتخاب موتور</h1>
            <form id="vehicle-filter" class="row g-2 align-items-end filter-bar mb-4" method="get">
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-tag text-primary"></i> مدل</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($model) ?>" class="form-control rounded-pill">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-check-circle text-success"></i> وضعیت</label>
                    <select name="availability" class="form-select rounded-pill">
                        <option value="">همه</option>
                        <option value="1" <?= $availability==='1'?'selected':'' ?>>موجود</option>
                        <option value="0" <?= $availability==='0'?'selected':'' ?>>رزرو</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-cash-coin text-warning"></i> حداقل قیمت</label>
                    <input type="number" name="min_price" value="<?= htmlspecialchars($min_price) ?>" class="form-control rounded-pill">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-cash-coin text-warning"></i> حداکثر قیمت</label>
                    <input type="number" name="max_price" value="<?= htmlspecialchars($max_price) ?>" class="form-control rounded-pill">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-battery-half text-danger"></i> برد باتری (km)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="range" name="min_range" id="min_range" min="0" max="200" value="<?= $min_range!==''? (int)$min_range : 0 ?>" class="form-range flex-grow-1">
                        <span id="min_rangeOutput" class="small"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <input type="range" name="max_range" id="max_range" min="0" max="200" value="<?= $max_range!==''? (int)$max_range : 200 ?>" class="form-range flex-grow-1">
                        <span id="max_rangeOutput" class="small"></span>
                    </div>
                </div>
                <div class="col-12 col-md-2 ms-auto">
                    <button class="btn btn-primary rounded-pill w-100"><i class="bi bi-search"></i> جستجو</button>
                </div>
            </form>
            <div class="row">
                <?php foreach ($motors as $motor): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="bg-white rounded-xl shadow p-3 h-100 d-flex flex-column">
                            <div class="position-relative mb-2" data-bs-toggle="modal" data-bs-target="#motor<?= $motor['id'] ?>">
                                <?php if($motor['image']): ?>
                                    <img src="../<?= htmlspecialchars($motor['image']) ?>" class="w-100 h-36 object-cover rounded" alt="<?= htmlspecialchars($motor['model']) ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x300?text=No+Image" class="w-100 h-36 object-cover rounded" alt="">
                                <?php endif; ?>
                                <span class="badge <?= $motor['available']? 'bg-success':'bg-danger' ?> position-absolute top-0 start-0 m-2"><?= $motor['available']? 'موجود':'رزرو' ?></span>
                            </div>
                            <h3 class="h5 mb-2 flex-grow-0"><?= htmlspecialchars($motor['model']) ?></h3>
                            <ul class="list-unstyled small text-muted mb-2 flex-grow-1">
                                <li>باتری: <?= $motor['battery_kwh'] ?> kWh</li>
                                <li>برد: <?= $motor['range_km'] ?> km</li>
                                <li>سرعت: <?= $motor['top_speed'] ?> km/h</li>
                                <li>وزن: <?= $motor['weight'] ?> kg</li>
                            </ul>
                            <div class="text-sm mb-2">
                                <span class="d-block">ساعتی: <?= number_format($motor['price_per_hour']) ?> تومان</span>
                                <span class="d-block">نیم‌روز: <?= number_format($motor['price_half_day']) ?> تومان</span>
                                <span class="d-block">روزانه: <?= number_format($motor['price_per_day']) ?> تومان</span>
                            </div>
                            <div class="mt-auto d-flex gap-2">
                                <a href="bookings.php?motor=<?= $motor['id'] ?>" class="btn btn-success flex-fill">رزرو فوری</a>
                                <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-toggle="modal" data-bs-target="#motor<?= $motor['id'] ?>">جزئیات</button>
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
                                        <li>باتری: <?= $motor['battery_kwh'] ?> kWh</li>
                                        <li>برد: <?= $motor['range_km'] ?> km</li>
                                        <li>سرعت: <?= $motor['top_speed'] ?> km/h</li>
                                        <li>وزن: <?= $motor['weight'] ?> kg</li>
                                        <li>ساعتی: <?= number_format($motor['price_per_hour']) ?> تومان</li>
                                        <li>نیم‌روز: <?= number_format($motor['price_half_day']) ?> تومان</li>
                                        <li>روزانه: <?= number_format($motor['price_per_day']) ?> تومان</li>
                                    </ul>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <?php foreach($imgs as $idx => $img): ?>
                                            <img src="../<?= htmlspecialchars($img) ?>" class="img-thumbnail" style="width:60px;height:60px;object-fit:cover;cursor:pointer" data-bs-target="#carousel<?= $motor['id'] ?>" data-bs-slide-to="<?= $idx ?>">
                                        <?php endforeach; ?>
                                    </div>
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
