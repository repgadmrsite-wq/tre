<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_motor'])) {
    $model = trim($_POST['model']);
    $plate = trim($_POST['plate']);
    $color = trim($_POST['color']);
    $capacity = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $price_hour = (int)$_POST['price_hour'];
    $price_half = (int)$_POST['price_half'];
    $price_day = (int)$_POST['price_day'];
    $price_week = (int)$_POST['price_week'];
    $price_month = (int)$_POST['price_month'];
    $insurance = trim($_POST['insurance']);
    $year = (int)$_POST['year'];
    $mileage = (int)$_POST['mileage'];
    $available = (int)($_POST['available'] ?? 1);

    if ($model && $price_day) {
        $stmt = $pdo->prepare('INSERT INTO motorcycles (model, plate, color, capacity, description, status, price_per_hour, price_half_day, price_per_day, price_per_week, price_per_month, insurance, year, mileage, available) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$model, $plate, $color, $capacity, $description, $status, $price_hour, $price_half, $price_day, $price_week, $price_month, $insurance, $year, $mileage, $available]);
        $motorId = $pdo->lastInsertId();
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/';
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['images']['tmp_name'][$i];
                    $name = time() . '_' . $i . '_' . basename($_FILES['images']['name'][$i]);
                    $target = $uploadDir . $name;
                    if (move_uploaded_file($tmp, $target)) {
                        $path = 'uploads/' . $name;
                        $pdo->prepare('INSERT INTO motorcycle_images (motorcycle_id, image_path) VALUES (?,?)')->execute([$motorId, $path]);
                    }
                }
            }
        }
    }
    header('Location: motors.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_motor'])) {
    $id = (int)$_POST['motor_id'];
    $model = trim($_POST['model']);
    $plate = trim($_POST['plate']);
    $color = trim($_POST['color']);
    $capacity = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $price_hour = (int)$_POST['price_hour'];
    $price_half = (int)$_POST['price_half'];
    $price_day = (int)$_POST['price_day'];
    $price_week = (int)$_POST['price_week'];
    $price_month = (int)$_POST['price_month'];
    $insurance = trim($_POST['insurance']);
    $year = (int)$_POST['year'];
    $mileage = (int)$_POST['mileage'];
    $available = (int)($_POST['available'] ?? 1);
    $stmt = $pdo->prepare('UPDATE motorcycles SET model=?, plate=?, color=?, capacity=?, description=?, status=?, price_per_hour=?, price_half_day=?, price_per_day=?, price_per_week=?, price_per_month=?, insurance=?, year=?, mileage=?, available=? WHERE id=?');
    $stmt->execute([$model, $plate, $color, $capacity, $description, $status, $price_hour, $price_half, $price_day, $price_week, $price_month, $insurance, $year, $mileage, $available, $id]);
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = __DIR__ . '/../uploads/';
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['images']['tmp_name'][$i];
                $name = time() . '_' . $i . '_' . basename($_FILES['images']['name'][$i]);
                $target = $uploadDir . $name;
                if (move_uploaded_file($tmp, $target)) {
                    $path = 'uploads/' . $name;
                    $pdo->prepare('INSERT INTO motorcycle_images (motorcycle_id, image_path) VALUES (?,?)')->execute([$id, $path]);
                }
            }
        }
    }
    header('Location: motors.php');
    exit;
}

if (isset($_GET['delete_motor'])) {
    $id = (int)$_GET['delete_motor'];
    $stmt = $pdo->prepare('SELECT image_path FROM motorcycle_images WHERE motorcycle_id=?');
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $img) {
        @unlink(__DIR__ . '/../' . $img);
    }
    $pdo->prepare('DELETE FROM motorcycle_images WHERE motorcycle_id=?')->execute([$id]);
    $pdo->prepare('DELETE FROM motorcycles WHERE id=?')->execute([$id]);
    header('Location: motors.php');
    exit;
}

$editMotor = null;
if (isset($_GET['edit_motor'])) {
    $id = (int)$_GET['edit_motor'];
    $stmt = $pdo->prepare('SELECT * FROM motorcycles WHERE id=?');
    $stmt->execute([$id]);
    $editMotor = $stmt->fetch(PDO::FETCH_ASSOC);
}

$filterModel = $_GET['model'] ?? '';
$filterCapacity = (int)($_GET['capacity'] ?? 0);
$filterPrice = (int)($_GET['price'] ?? 0);
$query = "SELECT m.*, (SELECT image_path FROM motorcycle_images WHERE motorcycle_id=m.id LIMIT 1) AS image FROM motorcycles m WHERE 1=1";
$params = [];
if ($filterModel) { $query .= ' AND m.model LIKE ?'; $params[] = "%$filterModel%"; }
if ($filterCapacity) { $query .= ' AND m.capacity >= ?'; $params[] = $filterCapacity; }
if ($filterPrice) { $query .= ' AND m.price_per_day <= ?'; $params[] = $filterPrice; }
$query .= ' ORDER BY m.id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$motors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت موتورها - کیش‌ران</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <aside class="sidebar d-flex flex-column p-0">
    <div class="sidebar-header text-center">
      <a class="navbar-brand fs-4 text-white" href="../index.html">کیش‌ران - ادمین</a>
    </div>
    <ul class="nav flex-column my-4">
      <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
      <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="finance.php"><i class="bi bi-receipt"></i><span>مالی</span></a></li>
      <li class="nav-item"><a class="nav-link" href="discounts.php"><i class="bi bi-ticket-perforated"></i><span>تخفیف‌ها</span></a></li>
      <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
      <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
      <li class="nav-item"><a class="nav-link active" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    </ul>
    <div class="mt-auto p-3">
      <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
    </div>
  </aside>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت موتورها</h1>
      <form method="post" enctype="multipart/form-data" class="row g-2 mb-4">
        <?php if($editMotor): ?>
          <input type="hidden" name="update_motor" value="1">
          <input type="hidden" name="motor_id" value="<?= $editMotor['id']; ?>">
        <?php else: ?>
          <input type="hidden" name="add_motor" value="1">
        <?php endif; ?>
        <div class="col-md-3"><input type="text" name="model" class="form-control" placeholder="مدل" value="<?= htmlspecialchars($editMotor['model'] ?? ''); ?>" required></div>
        <div class="col-md-2"><input type="text" name="plate" class="form-control" placeholder="پلاک" value="<?= htmlspecialchars($editMotor['plate'] ?? ''); ?>"></div>
        <div class="col-md-2"><input type="text" name="color" class="form-control" placeholder="رنگ" value="<?= htmlspecialchars($editMotor['color'] ?? ''); ?>"></div>
        <div class="col-md-2"><input type="number" name="capacity" class="form-control" placeholder="ظرفیت" value="<?= htmlspecialchars($editMotor['capacity'] ?? ''); ?>"></div>
        <div class="col-md-3"><input type="text" name="insurance" class="form-control" placeholder="بیمه" value="<?= htmlspecialchars($editMotor['insurance'] ?? ''); ?>"></div>
        <div class="col-md-2"><input type="number" name="year" class="form-control" placeholder="سال ساخت" value="<?= htmlspecialchars($editMotor['year'] ?? ''); ?>"></div>
        <div class="col-md-2"><input type="number" name="mileage" class="form-control" placeholder="کیلومتر" value="<?= htmlspecialchars($editMotor['mileage'] ?? ''); ?>"></div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="active" <?= isset($editMotor['status']) && $editMotor['status']==='active' ? 'selected' : ''; ?>>فعال</option>
            <option value="inactive" <?= isset($editMotor['status']) && $editMotor['status']==='inactive' ? 'selected' : ''; ?>>غیرفعال</option>
            <option value="maintenance" <?= isset($editMotor['status']) && $editMotor['status']==='maintenance' ? 'selected' : ''; ?>>در تعمیر</option>
            <option value="sold" <?= isset($editMotor['status']) && $editMotor['status']==='sold' ? 'selected' : ''; ?>>فروخته‌شده</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="available" class="form-select">
            <option value="1" <?= !isset($editMotor['available']) || $editMotor['available'] ? 'selected' : ''; ?>>موجود</option>
            <option value="0" <?= isset($editMotor['available']) && !$editMotor['available'] ? 'selected' : ''; ?>>ناموجود</option>
          </select>
        </div>
        <div class="col-md-2"><input type="number" name="price_hour" class="form-control" placeholder="ساعتی" value="<?= htmlspecialchars($editMotor['price_per_hour'] ?? ''); ?>" required></div>
        <div class="col-md-2"><input type="number" name="price_half" class="form-control" placeholder="نیم‌روز" value="<?= htmlspecialchars($editMotor['price_half_day'] ?? ''); ?>" required></div>
        <div class="col-md-2"><input type="number" name="price_day" class="form-control" placeholder="روزانه" value="<?= htmlspecialchars($editMotor['price_per_day'] ?? ''); ?>" required></div>
        <div class="col-md-2"><input type="number" name="price_week" class="form-control" placeholder="هفتگی" value="<?= htmlspecialchars($editMotor['price_per_week'] ?? ''); ?>" required></div>
        <div class="col-md-2"><input type="number" name="price_month" class="form-control" placeholder="ماهانه" value="<?= htmlspecialchars($editMotor['price_per_month'] ?? ''); ?>" required></div>
        <div class="col-md-12"><textarea name="description" class="form-control" placeholder="توضیحات"><?= htmlspecialchars($editMotor['description'] ?? ''); ?></textarea></div>
        <div class="col-md-12"><input type="file" name="images[]" class="form-control" multiple></div>
        <div class="col-md-3"><button class="btn btn-primary w-100" type="submit"><?= $editMotor ? 'ویرایش' : 'افزودن'; ?></button></div>
      </form>
      <form method="get" class="row g-2 mb-4">
        <div class="col-md-3"><input type="text" name="model" value="<?= htmlspecialchars($filterModel); ?>" class="form-control" placeholder="فیلتر مدل"></div>
        <div class="col-md-3"><input type="number" name="capacity" value="<?= $filterCapacity ?: ''; ?>" class="form-control" placeholder="حداقل ظرفیت"></div>
        <div class="col-md-3"><input type="number" name="price" value="<?= $filterPrice ?: ''; ?>" class="form-control" placeholder="حداکثر قیمت روزانه"></div>
        <div class="col-md-3"><button class="btn btn-secondary w-100" type="submit">اعمال فیلتر</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead><tr><th>#</th><th>تصویر</th><th>مدل</th><th>روزانه</th><th>وضعیت</th><th>موجودی</th><th>ویرایش</th><th>حذف</th></tr></thead>
          <tbody>
            <?php foreach ($motors as $m): ?>
            <tr>
              <td><?= $m['id']; ?></td>
              <td><?php if($m['image']): ?><img src="../<?= $m['image']; ?>" width="60" alt=""><?php endif; ?></td>
              <td><?= htmlspecialchars($m['model']); ?></td>
              <td><?= number_format($m['price_per_day']); ?></td>
              <td><?= $m['status']; ?></td>
              <td><?= $m['available'] ? 'بله' : 'خیر'; ?></td>
              <td><a href="?edit_motor=<?= $m['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a></td>
              <td><a href="?delete_motor=<?= $m['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف موتور؟');"><i class="bi bi-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
