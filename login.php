<?php
session_start();
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/csrf.php';
require_once __DIR__.'/includes/notify.php';

$error = '';
$otpStep = isset($_SESSION['otp_phone']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    if (isset($_POST['phone']) && !$otpStep) {
        $phone = trim($_POST['phone']);
        $stmt = $pdo->prepare('SELECT id, name, email, language, notify_email, wallet_balance FROM users WHERE phone = ? LIMIT 1');
        $stmt->execute([$phone]);
        $user = $stmt->fetch();
        if ($user) {
            $code = rand(100000,999999);
            $_SESSION['otp_phone'] = $phone;
            $_SESSION['otp_code'] = (string)$code;
            $_SESSION['otp_user'] = $user;
            sendSMS($phone, "کد ورود: $code");
            $otpStep = true;
        } else {
            $error = 'کاربری با این شماره یافت نشد';
        }
    } elseif (isset($_POST['otp']) && $otpStep) {
        if (trim($_POST['otp']) === ($_SESSION['otp_code'] ?? '')) {
            $u = $_SESSION['otp_user'];
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'role' => 'user',
                'language' => $u['language'],
                'notify_email' => $u['notify_email'],
                'wallet_balance' => $u['wallet_balance']
            ];
            unset($_SESSION['otp_phone'], $_SESSION['otp_code'], $_SESSION['otp_user']);
            header('Location: user/dashboard.php');
            exit;
        } else {
            $error = 'کد وارد شده نادرست است';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود - KISH UP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {background-color: var(--light-color, #f8f9fa);} 
        .login-container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem;}
        .login-card{width:100%;max-width:450px;}
    </style>
</head>
<body>
<div class="login-container">
    <div class="card login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <a href="index.php"><img src="img/kishup-logo.png" alt="KishUp" class="auth-logo"></a>
            <h1 class="h4 my-3 fw-normal">ورود با شماره موبایل</h1>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if (!$otpStep): ?>
            <form method="post">
                <?= csrf_input(); ?>
                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" name="phone" id="phone" placeholder="09123456789" required>
                    <label for="phone">شماره موبایل</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">ارسال کد ورود</button>
                <p class="text-center mt-3 small">حساب ندارید؟ <a href="register.php">ثبت‌نام</a></p>
            </form>
        <?php else: ?>
            <form method="post">
                <?= csrf_input(); ?>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="otp" id="otp" placeholder="******" maxlength="6" required>
                    <label for="otp">کد ارسال شده</label>
                </div>
                <button class="w-100 btn btn-lg btn-success" type="submit">ورود</button>
            </form>
        <?php endif; ?>
        <p class="text-center mt-4"><a href="index.php" class="text-muted small"><i class="bi bi-arrow-right-short"></i> بازگشت به صفحه اصلی</a></p>
    </div>
</div>
</body>
</html>
