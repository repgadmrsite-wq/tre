<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: ../login.php');
    exit;
}

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
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center"><a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران - ادمین</a></div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="marketing.php"><i class="bi bi-megaphone"></i><span>مارکتینگ</span></a></li>
    </ul>
    <div class="mt-auto p-3"><a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a></div>
  </aside>
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
