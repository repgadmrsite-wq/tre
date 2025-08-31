<?php
session_start();

$dsn = 'mysql:host=localhost;dbname=tre;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$pdo = new PDO($dsn, $dbUser, $dbPass, $options);

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    header('Location: ../login.html');
    exit;
}

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newDate = $_POST['date'] ?? '';
    if ($id && $newDate) {
        $stmt = $pdo->prepare('UPDATE bookings SET booking_date = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$newDate, $id, $userId]);
    }
    header('Location: bookings.php');
    exit;
}

$stmt = $pdo->prepare('SELECT booking_date FROM bookings WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$booking) {
    header('Location: bookings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Change Booking Date</title>
</head>
<body>
<h1>Change Booking Date</h1>
<form method="post">
    <input type="date" name="date" value="<?= htmlspecialchars($booking['booking_date']) ?>">
    <button type="submit">Save</button>
    <a href="bookings.php">Cancel</a>
</form>
</body>
</html>
