<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/notify.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $email = trim($_POST['email'] ?? '');
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($user = $stmt->fetch()) {
            // remove old tokens
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$user['id']]);
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)')->execute([$user['id'], $token, $expires]);
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $link = "$scheme://$host/reset.php?token=$token";
            $subject = 'بازنشانی رمز عبور';
            $body = "برای بازنشانی رمز عبور روی لینک زیر کلیک کنید:\n$link";
            sendEmail($email, $subject, $body);
        }
    }
    $sent = true;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فراموشی رمز عبور - KISH UP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body{background-color:var(--light-color,#f8f9fa);} .reset-container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem;} .reset-card{width:100%;max-width:450px;}
    </style>
</head>
<body>
<div class="reset-container">
    <div class="card reset-card p-4 p-md-5">
        <div class="text-center mb-4">
            <a class="navbar-brand fs-2" href="index.html">KISH UP</a>
            <h1 class="h3 my-3 fw-normal">فراموشی رمز عبور</h1>
        </div>
        <?php if ($sent): ?>
            <div class="alert alert-info">اگر ایمیل وارد شده در سیستم وجود داشته باشد، لینک بازنشانی ارسال شد.</div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_input(); ?>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                <label for="email">آدرس ایمیل</label>
            </div>
            <button class="w-100 btn btn-primary" type="submit">ارسال لینک بازنشانی</button>
            <p class="text-center mt-4"><a href="login.php" class="text-muted small"><i class="bi bi-arrow-right-short"></i> بازگشت به صفحه ورود</a></p>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
