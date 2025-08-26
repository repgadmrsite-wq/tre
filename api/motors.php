<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
$stmt = $pdo->query("SELECT id, model, lat, lng, available, is_special FROM motorcycles WHERE status='active' AND lat IS NOT NULL AND lng IS NOT NULL");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
