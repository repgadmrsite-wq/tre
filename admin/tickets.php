<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/notify.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $id = (int)($_POST['ticket_id'] ?? 0);
    $response = trim($_POST['response'] ?? '');
    if ($id && $response) {
        $stmt = $pdo->prepare('UPDATE tickets SET response=?, status="answered" WHERE id=?');
        $stmt->execute([$response, $id]);
        $info = $pdo->prepare('SELECT u.email, u.phone, u.id FROM tickets t JOIN users u ON t.user_id=u.id WHERE t.id=?');
        $info->execute([$id]);
        if ($row = $info->fetch(PDO::FETCH_ASSOC)) {
            sendEmail($row['email'], 'پاسخ پشتیبانی', $response);
            if (!empty($row['phone'])) {
                sendSMS($row['phone'], 'پاسخ جدید به تیکت شما در کیش‌ران ثبت شد');
            }
            $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (?,?)')
                ->execute([$row['id'], 'پاسخ جدید به تیکت شما ثبت شد']);
        }
    }
    header('Location: tickets.php');
    exit;
}

if (isset($_GET['close'])) {
    $id = (int)$_GET['close'];
    $pdo->prepare('UPDATE tickets SET status="closed" WHERE id=?')->execute([$id]);
    header('Location: tickets.php');
    exit;
}

$status = $_GET['status'] ?? 'all';
$query = 'SELECT t.*, u.name AS user_name, u.email FROM tickets t JOIN users u ON t.user_id=u.id';
$params = [];
if (in_array($status, ['open','answered','closed'], true)) {
    $query .= ' WHERE t.status=?';
    $params[] = $status;
}
$query .= ' ORDER BY t.created_at DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تیکت‌ها - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='tickets'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت تیکت‌ها</h1>
      <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link<?= $status==='all'?' active':'' ?>" href="tickets.php">همه</a></li>
        <li class="nav-item"><a class="nav-link<?= $status==='open'?' active':'' ?>" href="tickets.php?status=open">در انتظار</a></li>
        <li class="nav-item"><a class="nav-link<?= $status==='answered'?' active':'' ?>" href="tickets.php?status=answered">پاسخ داده شده</a></li>
        <li class="nav-item"><a class="nav-link<?= $status==='closed'?' active':'' ?>" href="tickets.php?status=closed">بسته شده</a></li>
      </ul>
      <?php foreach ($tickets as $t): ?>
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-1"><?= htmlspecialchars($t['subject']) ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">کاربر: <?= htmlspecialchars($t['user_name']) ?> - <?= $t['created_at'] ?></h6>
            <p class="card-text"><?= nl2br(htmlspecialchars($t['message'])) ?></p>
            <?php if ($t['status'] === 'answered' && $t['response']): ?>
              <div class="alert alert-info"><?= nl2br(htmlspecialchars($t['response'])) ?></div>
              <?php if ($t['status'] !== 'closed'): ?>
                <a href="?close=<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary">بستن تیکت</a>
              <?php endif; ?>
            <?php elseif ($t['status'] === 'open'): ?>
              <form method="post" class="mt-3">
                <?= csrf_input(); ?>
                <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                <div class="mb-2">
                  <textarea name="response" class="form-control" rows="3" required></textarea>
                </div>
                <button class="btn btn-primary btn-sm">ارسال پاسخ</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
