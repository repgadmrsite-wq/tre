<?php
$pdo = new PDO('mysql:host=localhost;dbname=app', 'user', 'pass');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare('UPDATE admins SET username = ?, password = ? WHERE id = ?');
        $stmt->execute([$username, $hashedPassword, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $hashedPassword]);
    }
}
?>
