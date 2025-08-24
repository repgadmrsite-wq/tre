<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

$token = $_GET['token'] ?? '';
$valid = false;
if ($token) {
    $stmt = $pdo->prepare('SELECT user_id FROM password_resets WHERE token = ? AND expires_at >= NOW() LIMIT 1');
    $stmt->execute([$token]);
    if ($row = $stmt->fetch()) {
        $valid = true;
        $userId = $row['user_id'];
    }
}

$done = false;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $stmt = $pdo->prepare('SELECT user_id FROM password_resets WHERE token = ? AND expires_at >= NOW() LIMIT 1');
    $stmt->execute([$token]);
    if (!($row = $stmt->fetch())) {
        $errors[] = 'لینک نامعتبر یا منقضی است';
    } elseif ($password === '' || $password !== $confirm) {
        $errors[] = 'رمز عبور و تکرار آن یکسان نیست';
        $userId = $row['user_id'];
        $valid = true;
    } else {
        $hash = md5($password);
        $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $row['user_id']]);
        $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$row['user_id']]);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازنشانی رمز عبور - KISH UP</title>
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
            <h1 class="h3 my-3 fw-normal">بازنشانی رمز عبور</h1>
        </div>
        <?php if ($done): ?>
            <div class="alert alert-success">رمز عبور با موفقیت تغییر کرد. <a href="login.php">ورود</a></div>
        <?php elseif (!$valid): ?>
            <div class="alert alert-danger">لینک بازنشانی معتبر نیست.</div>
        <?php else: ?>
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="m-0">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post">
                <?= csrf_input(); ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">رمز عبور جدید</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm" required>
                    <label for="confirm">تکرار رمز عبور</label>
                </div>
                <button class="w-100 btn btn-primary" type="submit">تغییر رمز عبور</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
