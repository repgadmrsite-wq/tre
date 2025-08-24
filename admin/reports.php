<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if (isset($_GET['export']) && $_GET['export']==='bookings') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="bookings.csv"');
    $out=fopen('php://output','w');
    fputcsv($out,['ID','User','Motor','Start','End','Status']);
    $stmt=$pdo->query('SELECT b.id,u.name,m.model,b.start_date,b.end_date,b.status FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id');
    while($row=$stmt->fetch(PDO::FETCH_NUM)){fputcsv($out,$row);}fclose($out);exit;
}

$activeUsers=$pdo->query("SELECT COUNT(*) FROM users WHERE status!='blocked'")->fetchColumn();
$inactiveUsers=$pdo->query("SELECT COUNT(*) FROM users WHERE status='blocked'")->fetchColumn();
$revenueMonthly=$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid' AND YEAR(paid_at)=YEAR(CURDATE()) AND MONTH(paid_at)=MONTH(CURDATE())")->fetchColumn();
$ticketTotal=$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$ticketOpen=$pdo->query("SELECT COUNT(*) FROM tickets WHERE status='open'")->fetchColumn();
$ticketAvg=$pdo->query("SELECT AVG(TIMESTAMPDIFF(HOUR,created_at,responded_at)) FROM tickets WHERE responded_at IS NOT NULL")->fetchColumn();
$ticketCats=$pdo->query("SELECT COALESCE(category,'نامشخص') cat, COUNT(*) c FROM tickets GROUP BY category")->fetchAll(PDO::FETCH_ASSOC);
$ticketCatLabels=json_encode(array_column($ticketCats,'cat'));
$ticketCatCounts=json_encode(array_map('intval',array_column($ticketCats,'c')));
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>گزارش‌ها - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='reports'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">گزارش‌گیری</h1>
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">کاربران فعال</h5><p class="fs-3 mb-0"><?= $activeUsers; ?></p></div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">کاربران مسدود</h5><p class="fs-3 mb-0"><?= $inactiveUsers; ?></p></div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">درآمد ماه جاری</h5><p class="fs-3 mb-0"><?= number_format($revenueMonthly); ?> تومان</p></div></div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">کل تیکت‌ها</h5><p class="fs-3 mb-0"><?= $ticketTotal; ?></p></div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">تیکت‌های باز</h5><p class="fs-3 mb-0"><?= $ticketOpen; ?></p></div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body"><h5 class="card-title">میانگین زمان پاسخ‌گویی (ساعت)</h5><p class="fs-3 mb-0"><?= $ticketAvg ? round($ticketAvg,1) : 0; ?></p></div></div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card"><div class="card-body"><canvas id="ticketCats" height="200"></canvas></div></div>
        </div>
      </div>
      <a href="?export=bookings" class="btn btn-outline-primary">دانلود CSV رزروها</a>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const catCtx=document.getElementById('ticketCats');
if(catCtx){
  new Chart(catCtx,{type:'doughnut',data:{labels:<?= $ticketCatLabels ?>,datasets:[{data:<?= $ticketCatCounts ?>,backgroundColor:['#0d6efd','#198754','#dc3545','#ffc107','#0dcaf0']}]},options:{plugins:{legend:{position:'bottom'}}}});
}
</script>
</body>
</html>
