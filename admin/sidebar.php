<?php
if (!isset($activePage)) { $activePage = ''; }
?>
<aside class="sidebar d-flex flex-column p-0">
  <div class="sidebar-header text-center">
    <a class="navbar-brand d-flex align-items-center justify-content-center text-white" href="../index.html">
      <img src="../img/kishup-logo.png" alt="KishUp" class="sidebar-logo me-2">
      <span class="d-none d-sm-inline">ادمین</span>
    </a>
  </div>
  <ul class="nav flex-column my-4">
    <li class="nav-item"><a class="nav-link <?= $activePage==='dashboard' ? 'active' : '' ?>" href="admin.php"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='bookings' ? 'active' : '' ?>" href="bookings.php"><i class="bi bi-calendar-week"></i><span>رزروها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='finance' ? 'active' : '' ?>" href="finance.php"><i class="bi bi-receipt"></i><span>مالی</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='discounts' ? 'active' : '' ?>" href="discounts.php"><i class="bi bi-ticket-perforated"></i><span>تخفیف‌ها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='reviews' ? 'active' : '' ?>" href="reviews.php"><i class="bi bi-chat-left-text"></i><span>نظرات</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='photos' ? 'active' : '' ?>" href="photos.php"><i class="bi bi-camera"></i><span>عکس‌ها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='tickets' ? 'active' : '' ?>" href="tickets.php"><i class="bi bi-life-preserver"></i><span>تیکت‌ها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='maintenance' ? 'active' : '' ?>" href="maintenance.php"><i class="bi bi-wrench"></i><span>سرویس‌ها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='map' ? 'active' : '' ?>" href="map.php"><i class="bi bi-geo"></i><span>نقشه</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='reports' ? 'active' : '' ?>" href="reports.php"><i class="bi bi-graph-up"></i><span>گزارش‌ها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='marketing' ? 'active' : '' ?>" href="marketing.php"><i class="bi bi-megaphone"></i><span>مارکتینگ</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='admins' ? 'active' : '' ?>" href="admins.php"><i class="bi bi-shield-lock"></i><span>مدیران</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='logs' ? 'active' : '' ?>" href="logs.php"><i class="bi bi-list-check"></i><span>گزارش فعالیت</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='users' ? 'active' : '' ?>" href="users.php"><i class="bi bi-people-fill"></i><span>کاربران</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='motors' ? 'active' : '' ?>" href="motors.php"><i class="bi bi-bicycle"></i><span>موتورها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='notifications' ? 'active' : '' ?>" href="notifications.php"><i class="bi bi-bell"></i><span>اعلان‌ها</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $activePage==='settings' ? 'active' : '' ?>" href="settings.php"><i class="bi bi-gear"></i><span>تنظیمات</span></a></li>
  </ul>
  <div class="mt-auto p-3">
    <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>خروج</span></a>
  </div>
</aside>
<div id="admin-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:1080"></div>
<script>(function(){if(localStorage.getItem('theme')==='dark') document.body.classList.add('dark-mode');})();</script>
<script src="../js/admin-panel.js"></script>
