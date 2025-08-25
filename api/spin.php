<?php
session_start();
header('Content-Type: application/json');
require '../includes/db.php';
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'ابتدا وارد شوید']);
    exit;
}
$userId = $_SESSION['user']['id'];
$today = date('Y-m-d');
$stmt = $pdo->prepare('SELECT COUNT(*) FROM wheel_spins WHERE user_id = ? AND spin_date = ?');
$stmt->execute([$userId, $today]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'امروز قبلاً از گردونه استفاده کرده‌اید']);
    exit;
}
$prizes = [
    ['title' => '50 هزار تومان شارژ پنل کاربری', 'wallet' => 50000],
    ['title' => 'بلیط رایگان رفت کیش | KISH AIR AIRLINE'],
    ['title' => 'یک ساعت دچرخه رایگان'],
    ['title' => 'پوچ'],
    ['title' => 'یک روز موتور رایگان'],
    ['title' => '500 هزار تومان شارژ پنل کاربری', 'wallet' => 500000],
    ['title' => 'یک روز تمرین رایگان در باشگاه لامبو جیم'],
    ['title' => 'بلیط رایگان پارک دلفین ها'],
    ['title' => 'یک روز اقامت رایگان هتل پارمیس'],
    ['title' => 'بلیط غواصی رایگان'],
];
$index = random_int(0, count($prizes) - 1);
$prize = $prizes[$index];
$pdo->beginTransaction();
if (isset($prize['wallet'])) {
    $pdo->prepare('UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?')->execute([$prize['wallet'], $userId]);
    $pdo->prepare('INSERT INTO wallet_transactions (user_id, amount, type, created_at) VALUES (?, ?, "credit", NOW())')->execute([$userId, $prize['wallet']]);
}
$pdo->prepare('INSERT INTO wheel_spins (user_id, prize, spin_date) VALUES (?, ?, ?)')->execute([$userId, $prize['title'], $today]);
$pdo->commit();
echo json_encode(['success' => true, 'index' => $index, 'prize' => $prize['title']]);
