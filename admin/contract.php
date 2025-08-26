<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/contract_pdf.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT b.*, u.name AS user_name, m.model, m.plate FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id WHERE b.id=?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) { die('Booking not found'); }
$data['id'] = $id;
output_contract_pdf($data);
?>
