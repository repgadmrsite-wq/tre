<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_validate();
    foreach(['currency','language','work_hours'] as $key){
        if(isset($_POST[$key])){
            $stmt=$pdo->prepare('REPLACE INTO settings (setting_key,setting_value) VALUES (?,?)');
            $stmt->execute([$key,$_POST[$key]]);
        }
    }
}
$settings=[];
foreach($pdo->query('SELECT setting_key,setting_value FROM settings') as $row){$settings[$row['setting_key']]=$row['setting_value'];}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تنظیمات - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='settings'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">تنظیمات کلی</h1>
      <form method="post" class="row g-3">
        <?= csrf_input(); ?>
        <div class="col-md-4">
          <label class="form-label">واحد پولی</label>
          <input type="text" name="currency" class="form-control" value="<?= $settings['currency']??'تومان'; ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">زبان</label>
          <select name="language" class="form-select">
            <option value="fa" <?= (isset($settings['language'])&&$settings['language']=='fa')?'selected':''; ?>>فارسی</option>
            <option value="en" <?= (isset($settings['language'])&&$settings['language']=='en')?'selected':''; ?>>English</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">ساعت کاری</label>
          <input type="text" name="work_hours" class="form-control" value="<?= $settings['work_hours']??'09:00-21:00'; ?>">
        </div>
        <div class="col-12"><button class="btn btn-primary" type="submit">ذخیره</button></div>
      </form>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
