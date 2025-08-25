<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="container header-container">
        <a href="#hero" class="logo"><img src="img/kishup-logo.png" alt="KishUp" class="brand-logo"></a>
        <nav class="main-nav">
            <ul>
                <li><a href="#hero">خانه</a></li>
                <li><a href="#booking">رزرو موتور</a></li>
                <li><a href="#specials">ویژه‌ها</a></li>
                <li><a href="#ready">موتورهای آماده</a></li>
                <li><a href="#reviews">نظرات</a></li>
                <li><a href="#nearest">نزدیک‌ترین</a></li>
                <li><a href="#faq">سوالات متداول</a></li>
                <li><a href="#contact">تماس</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <button type="button" class="theme-switcher" aria-label="تغییر حالت نمایش">
                <i data-feather="moon" class="icon-moon"></i>
                <i data-feather="sun" class="icon-sun"></i>
            </button>
            <button type="button" class="hamburger-menu" aria-label="باز کردن منو">
                <i data-feather="menu"></i>
            </button>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-menu">
                    <button type="button" class="user-toggle">
                        <img src="https://i.pravatar.cc/40?u=<?= $_SESSION['user']['id']; ?>" alt="avatar" class="avatar">
                        <span class="user-name d-none d-md-inline"><?= htmlspecialchars($_SESSION['user']['name']); ?></span>
                        <i data-feather="chevron-down" class="d-none d-md-inline"></i>
                    </button>
                    <ul class="user-dropdown">
                        <li><a href="user/bookings.php">رزروها</a></li>
                        <li><a href="user/payments.php">فاکتورها</a></li>
                        <li><a href="user/profile.php">پروفایل</a></li>
                        <li><a href="user/support.php">تیکت پشتیبانی</a></li>
                        <li><a href="wheel.php">گردونه شانس</a></li>
                        <li><a href="user/payments.php">شارژ حساب</a></li>
                        <li><a href="logout.php">خروج</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php" class="btn-glass small">ورود</a>
                    <a href="register.php" class="btn-glass outline small">ثبت‌نام</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
