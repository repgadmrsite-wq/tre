<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '') {
        $errors[] = 'نام الزامی است';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'ایمیل نامعتبر است';
    }
    if ($password === '' || $password !== $confirm) {
        $errors[] = 'رمز عبور و تکرار آن یکسان نیست';
    }

    // check existing email
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'ایمیل وارد شده قبلاً ثبت شده است';
        }
    }

    if (!$errors) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, phone, email, password, status, language, notify_email, wallet_balance) VALUES (?,?,?,?,"regular","fa",1,0)');
        $stmt->execute([$name, $phone, $email, $hashed]);
        $userId = $pdo->lastInsertId();
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$userId,'name'=>$name,'email'=>$email,'role'=>'user','language'=>'fa','notify_email'=>1,'wallet_balance'=>0];
        header('Location: user/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام - KISH UP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {background-color: var(--light-color, #f8f9fa);} .register-container {min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem;} .register-card{width:100%; max-width:500px;}
    </style>
    <script>
    (function(){var t=localStorage.getItem('theme');if(t==='dark') document.documentElement.classList.add('dark-mode');})();
    </script>
</head>
<body>
<div class="register-container">
    <div class="card register-card p-4 p-md-5">
            <div class="text-center mb-4">
                <a href="index.html"><img src="img/kishup-logo.png" alt="KishUp" class="auth-logo"></a>
                <h1 class="h3 my-3 fw-normal">ایجاد حساب کاربری</h1>
            </div>
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="m-0">
                    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_input(); ?>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="name" name="name" placeholder="نام" value="<?= htmlspecialchars($name ?? '') ?>" required>
                <label for="name">نام</label>
            </div>
            <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="۰۹۱۲۳۴۵۶۷۸۹" value="<?= htmlspecialchars($phone ?? '') ?>">
                <label for="phone">شماره تماس</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <label for="email">آدرس ایمیل</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">رمز عبور</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm" required>
                <label for="confirm">تکرار رمز عبور</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">ثبت‌نام</button>
            <hr class="my-4">
            <p class="text-center small">قبلاً ثبت‌نام کرده‌اید؟ <a href="login.php">ورود</a></p>
            <p class="text-center mt-4"><a href="index.html" class="text-muted small"><i class="bi bi-arrow-right-short"></i> بازگشت به صفحه اصلی</a></p>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
