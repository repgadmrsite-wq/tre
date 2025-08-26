<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KISHUP – رزرو آنلاین موتور برقی در کیش</title>
    <!-- Preload hero image for faster LCP -->
    <link rel="preload" as="image" href="img/hero.webp" imagesrcset="img/hero.webp 1x, img/hero.png 1x" fetchpriority="high">
    <!-- Leaflet styles for the maps -->
    <!-- حذف کتابخانهٔ پیش‌فرض Leaflet و استفاده از SDK نشان برای نقشه‌ها -->
    <!-- نقشهٔ نشان شامل Leaflet و استایل‌های خود است، لذا از لینک زیر استفاده می‌کنیم -->
    <link rel="stylesheet" href="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.css">
    <!-- Jalali datepicker styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">
    <!-- Bootstrap Icons for map markers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Main site styles -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dark-mode">
    <?php include "includes/header.php"; ?>
    <main>
        <!-- Hero Section -->
        <section id="hero" class="hero">
            <picture class="hero-media">
                <source srcset="img/hero.webp" type="image/webp">
                <img src="img/hero.png" alt="موتور برقی در کیش" fetchpriority="high" />
            </picture>
            <div class="hero-content">
                <h1>اجاره موتور برقی در کیش</h1>
                <p class="hero-subhead">تحویل در محل + قیمت شفاف</p>
                <div class="hero-buttons">
                    <button type="button" id="hero-quick-btn" class="btn-glass primary">رزرو سریع</button>
                    <button type="button" id="hero-contact-btn" class="btn-glass outline">تماس با ما</button>
                </div>
            </div>
        </section>

        <!-- Island Map Section -->
        <section id="island-map" class="section map-section">
            <div class="container map-hero-container">
                <div class="section-header">
                    <h2>نقشهٔ زندهٔ موتورها</h2>
                    <p>نمایش موتورهای رزرو شده و آزاد در سراسر جزیرهٔ کیش</p>
                </div>
                <div id="island-map-container" class="island-map-wrapper"></div>
            </div>
        </section>

        <!-- Booking Section -->
        <section id="booking" class="section booking-section collapsed">
            <div class="container">
                <div class="section-header">
                    <h2>رزرو سریع و آسان</h2>
                    <p>موتور دلخواه خود را در چند مرحله رزرو کنید.</p>
                </div>
                <div class="booking-wrapper">
                    <!-- Form card -->
                    <div class="form-card">
                        <form id="booking-form" novalidate>
                            <ul class="progress-bar" role="progressbar" aria-valuemin="1" aria-valuemax="5" aria-valuenow="1">
                                <div class="progress-line"></div>
                                <li class="step active"><span class="step-number">۱</span><span class="step-label">مدل</span></li>
                                <li class="step"><span class="step-number">۲</span><span class="step-label">مدت</span></li>
                                <li class="step"><span class="step-number">۳</span><span class="step-label">زمان</span></li>
                                <li class="step"><span class="step-number">۴</span><span class="step-label">محل</span></li>
                                <li class="step"><span class="step-number">۵</span><span class="step-label">تأیید</span></li>
                            </ul>
                            <!-- Step 1: Vehicle selection -->
                            <fieldset class="form-step active-step" data-step="1">
                                <h4>انتخاب موتور</h4>
                                <div id="vehicle-selection-list" class="vehicle-list">
                                    <p class="loading-text">در حال بارگذاری...</p>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="next-btn" disabled>مرحله بعد</button>
                                </div>
                            </fieldset>
                            <!-- Step 2: Duration & quick reserve -->
                            <fieldset class="form-step" data-step="2">
                                <h4>مدت اجاره</h4>
                                <div class="duration-options">
                                    <input type="radio" name="duration" id="hourly" value="hourly">
                                    <label for="hourly">ساعتی</label>
                                    <input type="radio" name="duration" id="half-day" value="half-day">
                                    <label for="half-day">نیم‌روز</label>
                                    <input type="radio" name="duration" id="daily" value="daily">
                                    <label for="daily">روزانه</label>
                                </div>
                                <div id="quick-date-buttons" class="quick-date-buttons hidden">
                                    <button type="button" id="today-button">امروز</button>
                                    <button type="button" id="tomorrow-button">فردا</button>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="prev-btn">قبلی</button>
                                    <button type="button" class="next-btn">مرحله بعد</button>
                                </div>
                            </fieldset>
                            <!-- Step 3: Date & time -->
                            <fieldset class="form-step" data-step="3">
                                <h4>تاریخ و زمان تحویل</h4>
                                <div id="half-day-options" class="half-day-options hidden">
                                    <input type="radio" name="halfDayOption" id="half-day-morning" value="morning">
                                    <label for="half-day-morning">نیم‌روز اول (۰۰ تا ۱۲)</label>
                                    <input type="radio" name="halfDayOption" id="half-day-evening" value="evening">
                                    <label for="half-day-evening">نیم‌روز دوم (۱۲ تا ۲۴)</label>
                                </div>
                                <div class="date-range-picker-wrapper">
                                    <div class="date-inputs-container">
                                        <input type="text" id="start-date-input" placeholder="تاریخ شروع" data-jdp>
                                        <input type="text" id="end-date-input" placeholder="تاریخ پایان" data-jdp>
                                    </div>
                                    <div id="daily-options" class="daily-options hidden">
                                        <label>تعداد روز:</label>
                                        <div class="day-options">
                                            <input type="radio" name="dayCount" id="day1" value="1">
                                            <label for="day1">1 روز</label>
                                            <input type="radio" name="dayCount" id="day2" value="2">
                                            <label for="day2">2 روز</label>
                                            <input type="radio" name="dayCount" id="day3" value="3">
                                            <label for="day3">3 روز</label>
                                            <input type="radio" name="dayCount" id="day4" value="4">
                                            <label for="day4">4 روز</label>
                                            <input type="radio" name="dayCount" id="day5" value="5">
                                            <label for="day5">5 روز</label>
                                            <input type="radio" name="dayCount" id="day6" value="6">
                                            <label for="day6">6 روز</label>
                                            <input type="radio" name="dayCount" id="day7" value="7">
                                            <label for="day7">7 روز</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-pickers-wrapper">
                                    <div class="time-input-wrapper">
                                        <label for="start-time">ساعت تحویل:</label>
                                        <input type="time" id="start-time" required>
                                    </div>
                                </div>
                                <div id="hourly-options" class="hourly-options hidden">
                                    <label>مدت اجاره (ساعت):</label>
                                    <div class="hour-options">
                                        <input type="radio" name="hourCount" id="hour1" value="1"><label for="hour1">1 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour2" value="2"><label for="hour2">2 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour3" value="3"><label for="hour3">3 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour4" value="4"><label for="hour4">4 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour5" value="5"><label for="hour5">5 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour6" value="6"><label for="hour6">6 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour7" value="7"><label for="hour7">7 ساعت</label>
                                        <input type="radio" name="hourCount" id="hour8" value="8"><label for="hour8">8 ساعت</label>
                                    </div>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="prev-btn">قبلی</button>
                                    <button type="button" class="next-btn">مرحله بعد</button>
                                </div>
                            </fieldset>
                            <!-- Step 4: Delivery location -->
                            <fieldset class="form-step" data-step="4">
                                <h4>محل تحویل را مشخص کنید</h4>
                                <div class="location-search">
                                    <label for="location-search-input">جستجوی مکان:</label>
                                    <input type="text" id="location-search-input" placeholder="مثلاً اسکله تفریحی">
                                    <ul id="suggestions-list" class="suggestions-list"></ul>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="prev-btn">قبلی</button>
                                    <button type="button" class="next-btn">مرحله بعد</button>
                                </div>
                            </fieldset>
                            <!-- Step 5: Confirmation & OTP -->
                            <fieldset class="form-step" data-step="5">
                                <h4>تأیید و پرداخت</h4>
                                <label for="phone">شماره موبایل:</label>
                                <input type="tel" id="phone" name="phone" placeholder="۰۹۱۲۳۴۵۶۷۸۹" required>
                                <div id="otp-section" class="hidden">
                                    <label for="otp-code">کد ارسال شده:</label>
                                    <input type="text" id="otp-code" name="otp-code" placeholder="کد را وارد کنید" maxlength="6">
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="prev-btn">قبلی</button>
                                    <button type="submit" id="send-otp-btn" class="submit-btn">تایید و پرداخت</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <!-- Summary card -->
                    <div class="summary-card">
                        <div id="summary-panel" class="summary-panel"></div>
                        <div id="map-panel" class="map-wrapper hidden">
                            <div id="delivery-map-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Specials Section -->
        <section id="specials" class="section specials-section">
            <div class="container">
                <div class="section-header">
                    <h2>پیشنهادهای ویژه امروز</h2>
                    <p>پیشنهادهای ویژه ما برای رزرو امروز با بهترین قیمت‌ها.</p>
                </div>
                <div id="specials-grid" class="cards-grid specials-grid"></div>
            </div>
        </section>

        <!-- Ready Now Section -->
        <section id="ready" class="section ready-section">
            <div class="container">
                <div class="section-header">
                    <h2>موتورهای آماده رزرو</h2>
                    <p>مدل و وضعیت دلخواه خود را انتخاب کنید تا موتورهای موجود را مشاهده کنید.</p>
                </div>
                <div class="filter-controls">
                    <label for="model-filter">انتخاب مدل:</label>
                    <select id="model-filter">
                        <option value="all">همه مدل‌ها</option>
                    </select>
                    <label for="status-filter">وضعیت:</label>
                    <select id="status-filter">
                        <option value="available">موجود</option>
                        <option value="reserved">رزرو شده</option>
                    </select>
                </div>
                <div id="ready-grid" class="cards-grid ready-grid"></div>
            </div>
        </section>

        <!-- Reviews Section -->
        <section id="reviews" class="section reviews-section">
            <div class="container">
                <div class="section-header">
                    <h2>نظرات کاربران</h2>
                    <p>دیدگاه کاربران ما درباره خدمات KISHUP.</p>
                </div>
                <div id="reviews-grid" class="cards-grid reviews-grid"></div>
            </div>
        </section>

        <!-- Memories Section -->
        <section id="memories" class="section memories-section">
            <div class="container">
                <div class="section-header">
                    <h2>عکس‌های کاربران</h2>
                    <p>خاطرات شما از سفر با KISH UP</p>
                </div>
                <div id="memory-grid" class="cards-grid memory-grid"></div>
            </div>
        </section>

        <!-- Nearest Section -->
        <section id="nearest" class="section nearest-section">
            <div class="container">
                <div class="section-header">
                    <h2>نزدیک‌ترین موتور به من</h2>
                    <p>با استفاده از موقعیت شما، نزدیک‌ترین موتور موجود را پیدا می‌کنیم.</p>
                </div>
                <button type="button" id="nearest-btn" class="cta-button">پیدا کردن</button>
                <div id="nearest-result" class="nearest-card"></div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="section faq-section">
            <div class="container">
                <div class="section-header">
                    <h2>سوالات متداول</h2>
                    <p>پاسخ به سوالات رایج کاربران درباره رزرو و خدمات ما.</p>
                </div>
                <div class="faq-list">
                    <details>
                        <summary>چطور می‌توانم موتور رزرو کنم؟</summary>
                        <p>برای رزرو موتور، ابتدا موتور مورد نظر خود را انتخاب کنید، سپس مدت و زمان را مشخص کرده و محل تحویل را انتخاب کنید. در نهایت با وارد کردن شماره تلفن و تأیید، رزرو شما ثبت می‌شود.</p>
                    </details>
                    <details>
                        <summary>آیا امکان لغو رزرو وجود دارد؟</summary>
                        <p>بله، تا ۲۴ ساعت قبل از زمان تحویل می‌توانید رزرو خود را لغو کنید. برای این کار به بخش رزروهای من در پروفایل خود مراجعه کنید.</p>
                    </details>
                    <details>
                        <summary>هزینهٔ اجاره چگونه محاسبه می‌شود؟</summary>
                        <p>هزینهٔ اجاره بر اساس مدل موتور و مدت زمان انتخابی محاسبه می‌شود. تعرفه‌های ساعتی، نیم‌روزی و روزانه متفاوت است و در هنگام رزرو نمایش داده می‌شود.</p>
                    </details>
                    <details>
                        <summary>آیا می‌توانم محل تحویل را تغییر دهم؟</summary>
                        <p>بله، شما می‌توانید هنگام رزرو محل تحویل را تعیین کنید یا بعداً از طریق پروفایل کاربری آن را تغییر دهید. تغییر محل تحویل ممکن است شامل هزینه اضافی باشد.</p>
                    </details>
                </div>
            </div>
        </section>

    </main>

    <!-- ===================== Footer ===================== -->
    <footer id="contact" class="main-footer">
        <div class="container">
                <div class="footer-content">
                <div class="footer-info text-center text-md-start">
                    <img src="img/kishup-logo.png" alt="KishUp" class="brand-logo mb-2">
                    <p>&copy; 2025 - تمام حقوق برای KISHUP محفوظ است.</p>
                    <p>تلفن: ۰۹۱۲۳۴۵۶۷۸۹ | ایمیل: info@kishup.com</p>
                    <p>آدرس: جزیره کیش، بلوار ساحل، کنار اسکله تفریحی</p>
                </div>
                <div class="social-links">
                    <a href="#" aria-label="اینستاگرام"><i data-feather="instagram"></i></a>
                    <a href="#" aria-label="تلگرام"><i data-feather="send"></i></a>
                    <a href="#" aria-label="توییتر"><i data-feather="twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Quick Reserve Modal -->
    <div id="quick-modal" class="qr-modal hidden">
        <div class="qr-content">
            <h3>تأیید تاریخ و ساعت</h3>
            <p id="quick-preview"></p>
            <input type="text" id="quick-date" data-jdp>
            <input type="time" id="quick-time" step="900">
            <div class="qr-actions">
                <button type="button" id="quick-confirm" class="btn-glass primary">تأیید</button>
                <button type="button" id="quick-cancel" class="btn-glass outline">انصراف</button>
            </div>
        </div>
    </div>

    <!-- ===================== Scripts ===================== -->
    <!-- Neshan Maps SDK for Leaflet (شامل کتابخانهٔ Leaflet) -->
    <script src="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.js"></script>
    <!-- Feather icons library -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <!-- Jalali datepicker library -->
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>
    <!-- Injected environment configuration (API keys, etc.) -->
    <script src="js/env.js.php"></script>
    <!-- Main application script -->
    <script src="js/script.js"></script>
</body>
</html>
