<?php
require_once __DIR__ . '/includes/db.php';

function migrateTable(PDO $pdo, string $table): void {
    $stmt = $pdo->query("SELECT id, password FROM {$table}");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (strlen($row['password']) === 32 && ctype_xdigit($row['password'])) {
            echo "Enter plain password for {$table} #{$row['id']}: ";
            $plain = trim(fgets(STDIN));
            if (md5($plain) === $row['password']) {
                $hash = password_hash($plain, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE {$table} SET password=? WHERE id=?");
                $upd->execute([$hash, $row['id']]);
                echo "Updated {$table} #{$row['id']}\n";
            } else {
                echo "Skipped {$table} #{$row['id']} (password mismatch)\n";
            }
        }
    }
}

migrateTable($pdo, 'admins');
migrateTable($pdo, 'users');

echo "Migration complete\n";
