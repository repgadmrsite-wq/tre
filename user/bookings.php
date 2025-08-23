<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang='fa' dir='rtl'>
<head>
<meta charset='UTF-8'><title>رزروهای من</title>
</head>
<body>
<p>در حال توسعه...</p>
</body></html>
