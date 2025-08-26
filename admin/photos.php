<?php
$activePage = 'photos';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $id = (int)$_POST['approve'];
        $pdo->prepare('UPDATE gallery_photos SET approved=1 WHERE id=?')->execute([$id]);
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['delete'];
        $stmt = $pdo->prepare('SELECT image_path FROM gallery_photos WHERE id=?');
        $stmt->execute([$id]);
        $path = $stmt->fetchColumn();
        if ($path) {
            @unlink(__DIR__ . '/../' . $path);
        }
        $pdo->prepare('DELETE FROM gallery_photos WHERE id=?')->execute([$id]);
    }
}
$photos = $pdo->query('SELECT gp.*, u.name FROM gallery_photos gp JOIN users u ON gp.user_id=u.id ORDER BY gp.created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عکس‌های کاربران</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
<link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="content p-4">
    <h1 class="h3 mb-4">عکس‌های کاربران</h1>
    <div class="row g-3">
        <?php foreach ($photos as $p): ?>
        <div class="col-md-3">
            <div class="card">
                <img src="../<?= htmlspecialchars($p['image_path']) ?>" class="card-img-top" alt="">
                <div class="card-body">
                    <p class="card-text mb-2"><?= htmlspecialchars($p['name']) ?></p>
                    <form method="post" class="d-flex gap-2">
                        <?php if(!$p['approved']): ?>
                        <button name="approve" value="<?= $p['id'] ?>" class="btn btn-success btn-sm">تایید</button>
                        <?php endif; ?>
                        <button name="delete" value="<?= $p['id'] ?>" class="btn btn-danger btn-sm">حذف</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
