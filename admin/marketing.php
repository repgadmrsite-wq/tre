<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_validate();
    $type=$_POST['type'];
    $content=trim($_POST['content']);
    if($content){$msg='پیام ثبت شد و برای ارسال صف خواهد شد.';}
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مارکتینگ - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='marketing'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">ارسال پیام گروهی</h1>
      <?php if($msg):?><div class="alert alert-success"><?= $msg; ?></div><?php endif; ?>
      <form method="post" class="mb-3">
        <?= csrf_input(); ?>
        <div class="mb-3">
          <label class="form-label">نوع پیام</label>
          <select name="type" class="form-select"><option value="sms">پیامک</option><option value="email">ایمیل</option></select>
        </div>
        <div class="mb-3"><textarea name="content" class="form-control" rows="4" placeholder="متن پیام"></textarea></div>
        <button class="btn btn-primary" type="submit">ذخیره در صف ارسال</button>
      </form>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
