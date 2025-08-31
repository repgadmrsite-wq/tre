<?php
session_start();

// Database connection - adjust credentials accordingly
$dsn = 'mysql:host=localhost;dbname=tre;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    header('Location: ../login.html');
    exit;
}

$filters = [];
$params = [$userId];

if (!empty($_GET['date'])) {
    $filters[] = 'DATE(booking_date) = ?';
    $params[] = $_GET['date'];
}
if (!empty($_GET['status'])) {
    $filters[] = 'status = ?';
    $params[] = $_GET['status'];
}
if (!empty($_GET['model'])) {
    $filters[] = 'motorcycle_model LIKE ?';
    $params[] = '%' . $_GET['model'] . '%';
}

$sql = 'SELECT id, booking_date, status, motorcycle_model FROM bookings WHERE user_id = ?';
if ($filters) {
    $sql .= ' AND ' . implode(' AND ', $filters);
}
$sql .= ' ORDER BY booking_date DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusIcon(string $status): string {
    return match ($status) {
        'confirmed' => '✅',
        'pending' => '⏳',
        'cancelled' => '❌',
        default => 'ℹ️',
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings</title>
<style>
.table-responsive{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th,td{padding:8px;border:1px solid #ccc;text-align:left;}
.status-icon{margin-right:4px;}
.actions a{margin-right:6px;}
</style>
</head>
<body>
<h1>My Bookings</h1>
<form method="get">
    <label>
        Date:
        <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
    </label>
    <label>
        Status:
        <select name="status">
            <option value="">All</option>
            <?php foreach (['pending', 'confirmed', 'cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= (($_GET['status'] ?? '') === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Model:
        <input type="text" name="model" value="<?= htmlspecialchars($_GET['model'] ?? '') ?>">
    </label>
    <button type="submit">Filter</button>
</form>
<div class="table-responsive">
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Model</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($bookings)): ?>
            <tr><td colspan="4">No bookings found.</td></tr>
        <?php else: ?>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['motorcycle_model']) ?></td>
                <td><span class="status-icon"><?= statusIcon($b['status']) ?></span><?= htmlspecialchars(ucfirst($b['status'])) ?></td>
                <td class="actions">
                    <a href="cancel_booking.php?id=<?= $b['id'] ?>" onclick="return confirm('Cancel this booking?');">Cancel</a>
                    <a href="change_booking_date.php?id=<?= $b['id'] ?>">Change Date</a>
                    <a href="print_contract.php?id=<?= $b['id'] ?>" target="_blank">Print</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>
</body>
</html>
