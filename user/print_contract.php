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
$stmt = $pdo->prepare('SELECT booking_date, status, motorcycle_model FROM bookings WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$booking) {
    echo 'Booking not found';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Contract</title>
</head>
<body>
<h1>Booking Contract</h1>
<p>Date: <?= htmlspecialchars($booking['booking_date']) ?></p>
<p>Motorcycle: <?= htmlspecialchars($booking['motorcycle_model']) ?></p>
<p>Status: <?= htmlspecialchars($booking['status']) ?></p>
<script>
window.print();
</script>
</body>
</html>
