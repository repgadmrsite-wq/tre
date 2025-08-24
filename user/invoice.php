<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT p.*, u.name AS user_name, b.start_date, b.end_date, m.model
                       FROM payments p
                       JOIN bookings b ON p.booking_id = b.id
                       JOIN users u ON b.user_id = u.id
                       JOIN motorcycles m ON b.motorcycle_id = m.id
                       WHERE p.id = ? AND p.user_id = ?");
$stmt->execute([$id, $user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    die('Payment not found');
}
$lines = [
    'رسید پرداخت',
    'مشتری: ' . $data['user_name'],
    'موتور: ' . $data['model'],
    'رزرو: ' . $data['start_date'] . ' تا ' . $data['end_date'],
    'مبلغ: ' . number_format($data['amount']) . ' تومان',
    'روش پرداخت: ' . $data['method'],
    'تاریخ: ' . $data['paid_at']
];
$pdf = "%PDF-1.3\n";
$objects = [];
$objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R>>endobj\n";
$objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1>>endobj\n";
$content = "BT\n/F1 16 Tf\n50 750 Td\n";
foreach ($lines as $i => $line) {
    $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
    if ($i > 0) { $content .= "T*\n"; }
    $content .= "(" . $text . ") Tj\n";
}
$content .= "ET";
$objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj\n";
$objects[] = "4 0 obj<< /Length " . strlen($content) . " >>stream\n" . $content . "\nendstream\nendobj\n";
$objects[] = "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
$offsets = [];
$offset = strlen($pdf);
foreach ($objects as $obj) { $offsets[] = $offset; $pdf .= $obj; $offset += strlen($obj); }
$xref = $offset;
$pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
foreach ($offsets as $off) { $pdf .= sprintf("%010d 00000 n \n", $off); }
$pdf .= "trailer<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="invoice_' . $id . '.pdf"');
echo $pdf;
?>
