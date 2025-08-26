<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (empty($_FILES['photo']['name'])) {
    echo json_encode(['message' => 'هیچ فایلی ارسال نشد']);
    exit;
}
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
$type = mime_content_type($_FILES['photo']['tmp_name']);
if (!isset($allowed[$type])) {
    echo json_encode(['message' => 'فرمت نامعتبر است']);
    exit;
}
if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
    echo json_encode(['message' => 'حجم فایل باید کمتر از ۲ مگابایت باشد']);
    exit;
}
$dir = __DIR__ . '/../uploads/memories';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
$filename = uniqid('mem_') . '.' . $allowed[$type];
$path = $dir . '/' . $filename;
if (!move_uploaded_file($_FILES['photo']['tmp_name'], $path)) {
    echo json_encode(['message' => 'خطا در ذخیره فایل']);
    exit;
}
$stmt = $pdo->prepare('INSERT INTO gallery_photos (user_id, image_path) VALUES (?, ?)');
$stmt->execute([$_SESSION['user']['id'], 'uploads/memories/' . $filename]);

echo json_encode(['message' => 'عکس ارسال شد و پس از تایید نمایش داده می‌شود']);
