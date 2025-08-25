<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';
$motors=$pdo->query("SELECT m.id,m.model,m.lat,m.lng,m.status, b.start_date,b.end_date
    FROM motorcycles m
    LEFT JOIN bookings b ON b.motorcycle_id=m.id AND CURDATE() BETWEEN b.start_date AND b.end_date AND b.status IN ('confirmed','in_use')
    WHERE m.lat IS NOT NULL AND m.lng IS NOT NULL")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نقشه موتورها - KISH UP</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
  <style>
    #map{height:600px;}
    .motor-marker{position:relative;width:24px;}
    .motor-marker .progress{position:absolute;top:-6px;left:0;width:24px;height:4px;margin:0;}
    .motor-marker .progress-bar{height:100%;}
    .motor-marker .dot{width:16px;height:16px;border-radius:50%;margin:4px auto 0;}
    .motor-marker.available .dot{background:#22c55e;}
    .motor-marker.reserved .dot{background:#ef4444;}
  </style>
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='map'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">موقعیت موتورها</h1>
      <div id="map"></div>
    </div>
  </main>
</div>
<script src="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.js"></script>
<script src="../js/env.js.php"></script>
<script>
var KISH_BOUNDS=[[26.4,53.8],[26.65,54.1]];
var map=new L.Map('map',{key:NESHAN_MAP_API_KEY,maptype:'neshan',center:[26.5310,53.9860],zoom:12,maxBounds:KISH_BOUNDS});
var motors=<?php echo json_encode($motors); ?>;

function remainingPercent(start,end){
  var s=new Date(start),e=new Date(end),now=new Date();
  var total=e-s; if(total<=0) return 0;
  var rem=e-now; return Math.max(0,Math.min(100,(rem/total)*100));
}

motors.forEach(function(m){
    var reserved=m.start_date && m.end_date;
    var pct=reserved?remainingPercent(m.start_date,m.end_date):0;
    var progress=reserved?`<div class="progress"><div class="progress-bar bg-danger" style="width:${pct}%"></div></div>`:'';
    var iconHtml=`<div class="motor-marker ${reserved?'reserved':'available'}">${progress}<div class="dot"></div></div>`;
    var marker=L.marker([m.lat,m.lng],{icon:L.divIcon({html:iconHtml,className:''})}).addTo(map);
    var statusText=reserved?'رزرو شده':'آزاد';
    marker.bindPopup(`<strong>${m.model}</strong><br>وضعیت: ${statusText}`);
});
</script>
</body>
</html>
