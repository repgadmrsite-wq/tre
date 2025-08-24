<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

$error = '';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$lockoutSeconds = 900; // 15 minutes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // rate limiting
    $stmt = $pdo->prepare('SELECT attempts, last_attempt FROM login_attempts WHERE ip = ? AND email = ""');
    $stmt->execute([$ip]);
    $ipAttempt = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT attempts, last_attempt FROM login_attempts WHERE ip = "" AND email = ?');
    $stmt->execute([$email]);
    $emailAttempt = $stmt->fetch();

    $ipBlocked = $ipAttempt && $ipAttempt['attempts'] >= 5 && time() - strtotime($ipAttempt['last_attempt']) < $lockoutSeconds;
    $emailBlocked = $emailAttempt && $emailAttempt['attempts'] >= 5 && time() - strtotime($emailAttempt['last_attempt']) < $lockoutSeconds;

    if ($ipBlocked || $emailBlocked) {
        $error = 'تعداد تلاش‌های ناموفق زیاد است. لطفاً بعداً دوباره تلاش کنید.';
    } else {
        if ($ipAttempt && time() - strtotime($ipAttempt['last_attempt']) >= $lockoutSeconds) {
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = ? AND email = ""')->execute([$ip]);
            $ipAttempt = null;
        }
        if ($emailAttempt && time() - strtotime($emailAttempt['last_attempt']) >= $lockoutSeconds) {
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = "" AND email = ?')->execute([$email]);
            $emailAttempt = null;
        }

        // check admin table first
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role']
            ];
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = ? AND email = ""')->execute([$ip]);
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = "" AND email = ?')->execute([$email]);
            header('Location: admin/admin.php');
            exit;
        }

        // check regular users table
        $stmt = $pdo->prepare('SELECT id, name, email, password, language, notify_email FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => 'user',
                'language' => $user['language'],
                'notify_email' => $user['notify_email']
            ];
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = ? AND email = ""')->execute([$ip]);
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = "" AND email = ?')->execute([$email]);
            header('Location: user/dashboard.php');
            exit;
        }

        // failed login: increment attempts for IP and email
        if ($ipAttempt) {
            $pdo->prepare('UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE ip = ? AND email = ""')->execute([$ip]);
        } else {
            $pdo->prepare('INSERT INTO login_attempts (ip, email, attempts, last_attempt) VALUES (?, "", 1, NOW())')->execute([$ip]);
        }
        if ($emailAttempt) {
            $pdo->prepare('UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE ip = "" AND email = ?')->execute([$email]);
        } else {
            $pdo->prepare('INSERT INTO login_attempts (ip, email, attempts, last_attempt) VALUES ("", ?, 1, NOW())')->execute([$email]);
        }
        $error = 'اطلاعات ورود نادرست است';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به حساب کاربری - KISH UP</title>
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
                <a class="navbar-brand fs-2" href="index.html">KISH UP</a>
                <h1 class="h3 my-3 fw-normal">ورود به حساب کاربری</h1>
            </div>
              <?php if (isset($_GET['registered'])): ?>
                  <div class="alert alert-success">ثبت‌نام با موفقیت انجام شد. اکنون وارد شوید.</div>
              <?php endif; ?>
              <?php if ($error): ?>
                  <div class="alert alert-danger"><?= $error; ?></div>
              <?php endif; ?>
            <form method="post">
                <?= csrf_input(); ?>
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
                      <a href="forgot.php" class="small">فراموشی رمز عبور؟</a>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">ورود</button>
                <hr class="my-4">
                  <p class="text-center small">
                      حساب کاربری ندارید؟ <a href="register.php">ایجاد حساب کاربری</a>
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
