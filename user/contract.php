<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/contract_pdf.php';
$id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT b.*, u.name AS user_name, m.model, m.plate FROM bookings b JOIN users u ON b.user_id=u.id JOIN motorcycles m ON b.motorcycle_id=m.id WHERE b.id=? AND b.user_id=?");
$stmt->execute([$id, $user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) { die('Booking not found'); }
$data['id'] = $id;
output_contract_pdf($data);
?>
