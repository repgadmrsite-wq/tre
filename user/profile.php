<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=app', 'user', 'pass');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hashedPassword, $userId]);
    }
}
?>
