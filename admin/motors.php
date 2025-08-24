<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

function saveMotorImages($pdo, $motorId) {
    if (empty($_FILES['images']['name'][0])) {
        return;
    }
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        if ($_FILES['images']['size'][$i] > $maxSize) {
            continue;
        }
        $tmp = $_FILES['images']['tmp_name'][$i];
        $mime = finfo_file($finfo, $tmp);
        if (!isset($allowed[$mime])) {
            continue;
        }
        $ext = $allowed[$mime];
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $target = $uploadDir . $name;
        if (move_uploaded_file($tmp, $target)) {
            $path = 'uploads/' . $name;
            $pdo->prepare('INSERT INTO motorcycle_images (motorcycle_id, image_path) VALUES (?,?)')->execute([$motorId, $path]);
        }
    }
    finfo_close($finfo);
}

require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_motor'])) {
    csrf_validate();
    $errors = [];
    $model = trim($_POST['model']);
    $plate = trim($_POST['plate']);
    $color = trim($_POST['color']);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($capacity === false) { $errors[] = 'ظرفیت نامعتبر است'; }
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $price_hour = filter_input(INPUT_POST, 'price_hour', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_hour === false) { $errors[] = 'قیمت ساعتی نامعتبر است'; }
    $price_half = filter_input(INPUT_POST, 'price_half', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_half === false) { $errors[] = 'قیمت نیم‌روزه نامعتبر است'; }
    $price_day = filter_input(INPUT_POST, 'price_day', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_day === false) { $errors[] = 'قیمت روزانه نامعتبر است'; }
    $price_week = filter_input(INPUT_POST, 'price_week', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_week === false) { $errors[] = 'قیمت هفتگی نامعتبر است'; }
    $price_month = filter_input(INPUT_POST, 'price_month', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_month === false) { $errors[] = 'قیمت ماهانه نامعتبر است'; }
    $insurance = trim($_POST['insurance']);
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => (int)date('Y')]]);
    if ($year === false) { $errors[] = 'سال ساخت نامعتبر است'; }
    $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($mileage === false) { $errors[] = 'کیلومتر نامعتبر است'; }
    $available = filter_input(INPUT_POST, 'available', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1]]);
    if ($available === false) { $errors[] = 'وضعیت موجودی نامعتبر است'; }
    $lat = $_POST['lat'] !== '' ? filter_var($_POST['lat'], FILTER_VALIDATE_FLOAT) : null;
    if ($_POST['lat'] !== '' && $lat === false) { $errors[] = 'عرض جغرافیایی نامعتبر است'; }
    $lng = $_POST['lng'] !== '' ? filter_var($_POST['lng'], FILTER_VALIDATE_FLOAT) : null;
    if ($_POST['lng'] !== '' && $lng === false) { $errors[] = 'طول جغرافیایی نامعتبر است'; }

    if ($model === '') { $errors[] = 'مدل الزامی است'; }

    if ($errors) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: motors.php');
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO motorcycles (model, plate, color, capacity, description, status, price_per_hour, price_half_day, price_per_day, price_per_week, price_per_month, insurance, year, mileage, available, lat, lng) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$model, $plate, $color, $capacity, $description, $status, $price_hour, $price_half, $price_day, $price_week, $price_month, $insurance, $year, $mileage, $available, $lat, $lng]);
    $motorId = $pdo->lastInsertId();
    saveMotorImages($pdo, $motorId);
    header('Location: motors.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_motor'])) {
    csrf_validate();
    $errors = [];
    $id = (int)$_POST['motor_id'];
    $model = trim($_POST['model']);
    $plate = trim($_POST['plate']);
    $color = trim($_POST['color']);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($capacity === false) { $errors[] = 'ظرفیت نامعتبر است'; }
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $price_hour = filter_input(INPUT_POST, 'price_hour', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_hour === false) { $errors[] = 'قیمت ساعتی نامعتبر است'; }
    $price_half = filter_input(INPUT_POST, 'price_half', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_half === false) { $errors[] = 'قیمت نیم‌روزه نامعتبر است'; }
    $price_day = filter_input(INPUT_POST, 'price_day', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_day === false) { $errors[] = 'قیمت روزانه نامعتبر است'; }
    $price_week = filter_input(INPUT_POST, 'price_week', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_week === false) { $errors[] = 'قیمت هفتگی نامعتبر است'; }
    $price_month = filter_input(INPUT_POST, 'price_month', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price_month === false) { $errors[] = 'قیمت ماهانه نامعتبر است'; }
    $insurance = trim($_POST['insurance']);
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => (int)date('Y')]]);
    if ($year === false) { $errors[] = 'سال ساخت نامعتبر است'; }
    $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($mileage === false) { $errors[] = 'کیلومتر نامعتبر است'; }
    $available = filter_input(INPUT_POST, 'available', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1]]);
    if ($available === false) { $errors[] = 'وضعیت موجودی نامعتبر است'; }
    $lat = $_POST['lat'] !== '' ? filter_var($_POST['lat'], FILTER_VALIDATE_FLOAT) : null;
    if ($_POST['lat'] !== '' && $lat === false) { $errors[] = 'عرض جغرافیایی نامعتبر است'; }
    $lng = $_POST['lng'] !== '' ? filter_var($_POST['lng'], FILTER_VALIDATE_FLOAT) : null;
    if ($_POST['lng'] !== '' && $lng === false) { $errors[] = 'طول جغرافیایی نامعتبر است'; }

    if ($model === '') { $errors[] = 'مدل الزامی است'; }

    if ($errors) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: motors.php');
        exit;
    }

    $stmt = $pdo->prepare('UPDATE motorcycles SET model=?, plate=?, color=?, capacity=?, description=?, status=?, price_per_hour=?, price_half_day=?, price_per_day=?, price_per_week=?, price_per_month=?, insurance=?, year=?, mileage=?, available=?, lat=?, lng=? WHERE id=?');
    $stmt->execute([$model, $plate, $color, $capacity, $description, $status, $price_hour, $price_half, $price_day, $price_week, $price_month, $insurance, $year, $mileage, $available, $lat, $lng, $id]);
    saveMotorImages($pdo, $id);
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
  <title>مدیریت موتورها - KISH UP</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="dashboard-layout">
  <?php $activePage='motors'; include 'sidebar.php'; ?>
  <main class="main-content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">مدیریت موتورها</h1>
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data" class="row g-2 mb-4 card p-3">
        <?= csrf_input(); ?>
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
        <div class="col-md-2"><input type="text" name="lat" class="form-control" placeholder="lat" value="<?= htmlspecialchars($editMotor['lat'] ?? ''); ?>"></div>
        <div class="col-md-2"><input type="text" name="lng" class="form-control" placeholder="lng" value="<?= htmlspecialchars($editMotor['lng'] ?? ''); ?>"></div>
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
      <form method="get" class="row g-2 mb-4 card p-3">
        <div class="col-md-3"><input type="text" name="model" value="<?= htmlspecialchars($filterModel); ?>" class="form-control" placeholder="فیلتر مدل"></div>
        <div class="col-md-3"><input type="number" name="capacity" value="<?= $filterCapacity ?: ''; ?>" class="form-control" placeholder="حداقل ظرفیت"></div>
        <div class="col-md-3"><input type="number" name="price" value="<?= $filterPrice ?: ''; ?>" class="form-control" placeholder="حداکثر قیمت روزانه"></div>
        <div class="col-md-3"><button class="btn btn-secondary w-100" type="submit">اعمال فیلتر</button></div>
      </form>
      <div class="table-responsive card p-3">
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
