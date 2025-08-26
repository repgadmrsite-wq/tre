<?php
session_start();
require 'includes/db.php';
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گردونه شانس - KISHUP</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/wheel.css">
</head>
<body class="dark-mode">
<?php include 'includes/header.php'; ?>
<div class="wheel-wrapper">
    <h1>گردونه شانس</h1>
    <div id="wheel"></div>
    <button id="spin" class="glass-btn">شروع چرخش</button>
    <div id="result" class="result"></div>
</div>
<script src="js/wheel.js"></script>
</body>
</html>
