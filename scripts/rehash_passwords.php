<?php
// One-time script to migrate plaintext passwords to hashed values.
$pdo = new PDO('mysql:host=localhost;dbname=app', 'user', 'pass');

foreach (['users', 'admins'] as $table) {
    $stmt = $pdo->query("SELECT id, password FROM {$table}");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $info = password_get_info($row['password']);
        if ($info['algo'] === 0) { // Not hashed
            $hashed = password_hash($row['password'], PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE {$table} SET password = ? WHERE id = ?");
            $update->execute([$hashed, $row['id']]);
        }
    }
}

echo "Password rehash complete.\n";
?>
