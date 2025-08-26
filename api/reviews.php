<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$limit = isset($_GET['limit']) ? max(1, min(20, (int)$_GET['limit'])) : 6;

$stmt = $pdo->prepare('SELECT r.id, r.rating, r.comment, r.created_at, u.name, r.user_id
                        FROM reviews r JOIN users u ON r.user_id=u.id
                        WHERE r.status="approved"
                        ORDER BY r.created_at DESC
                        LIMIT ?');
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($reviews, JSON_UNESCAPED_UNICODE);
