<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';
$motors=$pdo->query('SELECT id,model,lat,lng,status FROM motorcycles WHERE lat IS NOT NULL AND lng IS NOT NULL')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نقشه موتورها - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="../css/admin-panel.css">
  <style>#map{height:600px;}</style>
</head>
<body>
<div class="dashboard-layout">
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center"><a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران - ادمین</a></div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="map.php"><i class="bi bi-geo"></i><span>نقشه</span></a></li>
    </ul>
    <div class="mt-auto p-3"><a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a></div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">موقعیت موتورها</h1>
      <div id="map"></div>
    </div>
  </main>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var map=L.map('map').setView([26.5310,53.9860],12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
var motors=<?php echo json_encode($motors); ?>;
motors.forEach(function(m){
    L.marker([m.lat,m.lng]).addTo(map).bindPopup(m.model+' - '+m.status);
});
</script>
</body>
</html>
