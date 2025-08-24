<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

// fetch motor locations and availability
$stmt = $pdo->query("SELECT m.id, m.model, m.lat, m.lng, NOT EXISTS (SELECT 1 FROM bookings b WHERE b.motorcycle_id=m.id AND b.status IN ('pending','confirmed','in_use') AND CURDATE() BETWEEN b.start_date AND b.end_date) AS available FROM motorcycles m WHERE m.lat IS NOT NULL AND m.lng IS NOT NULL");
$motors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// popular pickup locations
$locations = [
    ['name' => 'فرودگاه کیش', 'lat' => 26.5262, 'lng' => 53.9800],
    ['name' => 'مرکز شهر کیش', 'lat' => 26.5441, 'lng' => 53.9659],
    ['name' => 'اسکله تفریحی', 'lat' => 26.5325, 'lng' => 53.9643]
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نقشه موتورها - کیش‌ران</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-sA+4psJ3QWZ8bI6FHRpZWZcFTt1sAwH6pGf1jW4xyJM=" crossorigin=""/>
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
            <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-map"></i><span>نقشه</span></a></li>
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
            <h1 class="h2 mb-4">نقشه موتورها</h1>
            <div class="row g-3 mb-4 align-items-end filter-bar">
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-search"></i> محل تحویل</label>
                    <input id="locationSearch" list="locationList" class="form-control rounded-pill" placeholder="جستجوی مکان">
                    <datalist id="locationList">
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= htmlspecialchars($loc['name']) ?>" data-lat="<?= $loc['lat'] ?>" data-lng="<?= $loc['lng'] ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                    <div id="pickupMap" class="mini-map mt-3"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label mb-2"><i class="bi bi-funnel"></i> فیلتر وضعیت</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="filterAvailable" checked>
                        <label class="form-check-label text-success" for="filterAvailable">آزاد</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="filterReserved" checked>
                        <label class="form-check-label text-danger" for="filterReserved">رزرو شده</label>
                    </div>
                </div>
            </div>
            <div id="motorMap"></div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o9N1j7kGStIXgNdvtz4mP1N0bRkRr34E9YPc1y6p3Kk=" crossorigin=""></script>
<script>var mapMotors = <?= json_encode($motors, JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="../js/script-user.js"></script>
</body>
</html>
