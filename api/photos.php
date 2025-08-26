<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
$photos = $pdo->query('SELECT image_path FROM gallery_photos WHERE approved=1 ORDER BY created_at DESC')->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($photos);
