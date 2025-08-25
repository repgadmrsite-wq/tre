<?php
session_start();
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/csrf.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($email !== '' && $password !== '') {
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($admin = $stmt->fetch()) {
            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id'    => $admin['id'],
                    'name'  => $admin['name'],
                    'email' => $admin['email'],
                    'role'  => $admin['role'] // use role from DB so admin_auth works
                ];
                header('Location: admin/admin.php');
                exit;
            }
        }
        $stmt = $pdo->prepare('SELECT id, name, email, password, language, notify_email, wallet_balance FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id'=>$user['id'],
                    'name'=>$user['name'],
                    'email'=>$user['email'],
                    'role'=>'user',
                    'language'=>$user['language'],
                    'notify_email'=>$user['notify_email'],
                    'wallet_balance'=>$user['wallet_balance']
                ];
                header('Location: user/dashboard.php');
                exit;
            }
        }
        $error = 'ایمیل یا رمز عبور اشتباه است';
    } else {
        $error = 'تمام فیلدها الزامی است';
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
    <script>
    (function(){
        var t=localStorage.getItem('theme');
        if(t==='dark') document.documentElement.classList.add('dark-mode');
    })();
    </script>
</head>
<body>
<div class="login-container">
    <div class="card login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <a href="index.php"><img src="img/kishup-logo.png" alt="KishUp" class="auth-logo"></a>
            <h1 class="h4 my-3 fw-normal">ورود به حساب</h1>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_input(); ?>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                <label for="email">ایمیل</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="رمز عبور" required>
                <label for="password">رمز عبور</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">ورود</button>
            <p class="text-center mt-3 small">حساب ندارید؟ <a href="register.php">ثبت‌نام</a></p>
        </form>
        <p class="text-center mt-4"><a href="forgot.php" class="small">فراموشی رمز عبور</a></p>
    </div>
</div>
</body>
</html>
