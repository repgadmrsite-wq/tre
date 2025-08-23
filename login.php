<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        $redirect = $user['role'] === 'admin' ? 'admin.php' : 'dashboard.html';
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'اطلاعات ورود نادرست است';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به حساب کاربری - کیش‌ران</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: var(--light-color, #f8f9fa);
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <a class="navbar-brand fs-2" href="index.html">کیش‌ران</a>
                <h1 class="h3 my-3 fw-normal">ورود به حساب کاربری</h1>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                    <label for="email">آدرس ایمیل</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <label for="password">رمز عبور</label>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            مرا به خاطر بسپار
                        </label>
                    </div>
                    <a href="#" class="small">فراموشی رمز عبور؟</a>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">ورود</button>
                <hr class="my-4">
                <p class="text-center small">
                    حساب کاربری ندارید؟ <a href="#">ایجاد حساب کاربری</a>
                </p>
                <p class="text-center mt-4">
                    <a href="index.html" class="text-muted small"><i class="bi bi-arrow-right-short"></i> بازگشت به صفحه اصلی</a>
                </p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
