<?php $baseUrl = $baseUrl ?? '/'; ?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no, shrink-to-fit=no">
    <meta name="theme-color" content="#32228f">
    <?php if (!empty($pageDescription)): ?>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <?php endif; ?>
    <?php if (!empty($extraHead)) echo $extraHead; ?>
    <title><?php echo $pageTitle ?? ''; ?></title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/ico">
    <link rel="stylesheet" href="/dist/style.min.css">
</head>
<body>
<script>
(function() {
    var saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.body.classList.add('dark');
    }
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('theme-toggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                var isDark = document.body.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }
    });
})();
</script>
<div id="app" >
<section id="top-nav" >
    <div class="container" >
        <div class="d-flex" >
            <div class="mob-menu" ></div>
            <a class="logo" href="<?php echo $baseUrl; ?>"></a>
            <div class="nav-mid">
                <ul>
                    <li>
                        <a href="<?php echo $baseUrl; ?>">پرشین میزبان</a>
                    </li>
                    <li>
                        <a href="<?php echo $baseUrl; ?>about-us">درباره ما</a>
                    </li>
                    <li>
                        <a href="<?php echo $baseUrl; ?>card-number">شماره حساب ها</a>
                    </li>
                    <li>
                        <a href="<?php echo $baseUrl; ?>sales-cooperation">همکاری در فروش</a>
                    </li>
                    <li>
                        <a href="<?php echo $baseUrl; ?>contact-us/">تماس با ما</a>
                    </li>
                </ul>
                <div class="d-flex my-auto mr-auto" i-lg="border-top-1 border-primary-2 pt-6">
                    <a href="<?php echo $baseUrl; ?>my/register.php" i-lg="ml-auto">
                    <div class="button" >
                        <div class="icon icon-reg" ></div>
                        ثبت نام
                    </div>
                    </a>
                    <a href="<?php echo $baseUrl; ?>my/login" >
                    <div class="button" >
                        <div class="icon icon-log" ></div>
                        ورود
                    </div>
                    </a>
                    <button id="theme-toggle" class="theme-toggle">🌓</button>
                </div>
            </div>
           <div class="phone" >
                <div class="number">
                     <a href="tel:02191011796"><bdi>۰۲۱ - <span>۹ ۱۰ ۱۱ </span>۷۹۶</bdi></a>
                    <div>بـا ما در ارتـــباط باشیـد</div>
                </div>
                <div class="icon" ></div>
            </div>
        </div>
    </div>
    <div class="cover" ></div>
</section>

<section id="bottom-nav" class="active" >
    <div class="container" >
        <div class="d-flex">

            <div class="item" menu-item="1" >
                <div class="icon icon-host" ></div>
                <a href="<?php echo $baseUrl; ?>web-hosting/"><span>هاست</span></a>
                <div class="icon-more" ></div>
            </div>

            <div class="item" menu-item="2" >
                <div class="icon icon-domain" ></div>
                <a href="<?php echo $baseUrl; ?>domain/"><span>ثبت دامنه</span></a>
                <div class="icon-more" ></div>
            </div>

            <div class="item" menu-item="3" >
                <div class="icon icon-vps" ></div>
                <div class="blink">NEW</div>
                <a href="<?php echo $baseUrl; ?>vps/"><span>سرور مجازی</span></a>
                <div class="icon-more" ></div>
            </div>

            <div class="item" menu-item="4">
                <div class="icon icon-server" ></div>
                <a href="<?php echo $baseUrl; ?>vds/"><span>سرور اختصاصی</span></a>
                <div class="icon-more" ></div>
            </div>

            <div class="item !ml-0" lg="mr-auto" i-lg="pl-6" >
                <div class="icon icon-edu" ></div>
                <div class="blink blink2">BLOG</div>
                <a href="<?php echo $baseUrl; ?>blog/"><span>مرکز آموزش</span></a>
            </div>
        </div>
    </div>
</section>
<section id="mega-menu" class="active">
    <ul class="menu-item-1">
        <li>
            <a href="<?php echo $baseUrl; ?>web-hosting/iran">هاست ایران</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/hostiran.png" alt="هاست ایران" />
                            </div>
                            <div>
                                <div class="fa" >هاست سی پنل ایران</div>
                                <div class="en" >IRAN CAPANEL HOSTING</div>
                            </div>
                        </div>
                        <ul>
                            <li>بکاپ گیری روزانه منظم و دقیق</li>
                            <li>انتقال رايگان از ساير شركت ها</li>
                            <li>۱۴ روز گارانتی بازگشت وجه</li>
                            <li>گواهی نامه امنیتی <a href="<?php echo $baseUrl; ?>ssl/">SSL</a> اتوماتیک و رایگان</li>
                            <li>سازگاری کامل با گوگل و موتورهای جستجو</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>web-hosting/iran"><div class="button" >مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۲۹,۰۰۰</span>  تومان
                                <small>/ سـه ماهه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>web-hosting/germany">هاست آلمان</a>

            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/brandenburg-gate.png" alt="هاست آلمان" />
                            </div>
                            <div>
                                <div class="fa" >هاست سی پنل آلمان</div>
                                <div class="en" >GERMANY CAPANEL HOSTING</div>
                            </div>
                        </div>
                        <ul>
                            <li>بکاپ گیری روزانه منظم و دقیق</li>
                            <li>انتقال رايگان از ساير شركت ها</li>
                            <li>۱۴ روز گارانتی بازگشت وجه</li>
                            <li>گواهی نامه امنیتی <a href="<?php echo $baseUrl; ?>ssl/">SSL</a> اتوماتیک و رایگان</li>
                            <li>سازگاری کامل با گوگل و موتورهای جستجو</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>web-hosting/germany"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱۴۹,۰۰۰</span>  تومان
                                <small>/ سـالانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>web-hosting/wordpress">هاست وردپرس</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/hostingwordpress.png" alt="هاست وردپرس" />
                            </div>
                            <div>
                                <div class="fa" >هاست مخصوص وردپرس</div>
                                <div class="en" >WORDPRESS SPECIAL HOSTING</div>
                            </div>
                        </div>
                        <ul>
                            <li>تیم پشتیبانی مخصوص وردپرس</li>
                            <li>نصب و کانفیگ رایگان، قالب و افزونه وردپرسی</li>
                            <li>نصب اتوماتیک ماژول کش روی وردپرس‌</li>
                            <li>لوکیشن : ایران، آلمان، هلند، فرانسه، فنلاند</li>
                            <li>سازگاری کامل با گوگل و موتورهای جستجو</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>web-hosting/wordpress"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۶۹،۰۰۰</span>  تومان
                                <small>/ مـاهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>web-hosting/iran-download-host">هاست دانلود ایران</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/hostdliran.png" alt="هاست دانلود ایران" />
                            </div>
                            <div>
                                <div class="fa" >هاست دانلود ایران</div>
                                <div class="en" >DOWNLOAD SPECIAL HOSTING (IRAN)</div>
                            </div>
                        </div>
                        <ul>
                            <li>هاست دانلود ایران با ترافیک نیم بها</li>
                            <li>انتقال رايگان از ساير شركت ها</li>
                            <li>۱۴ روز گارانتی بازگشت وجه</li>
                            <li>گواهی نامه امنیتی <a href="<?php echo $baseUrl; ?>ssl/">SSL</a> اتوماتیک و رایگان</li>
                            <li>بکاپ گیری روزانه منظم و دقیق</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>web-hosting/iran-download-host"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱۵،۰۰۰</span>  تومان
                                <small>/ مـاهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>web-hosting/germany-download-host">هاست دانلود آلمان</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/hostdlgermany.png" alt="هاست دانلود آلمان" />
                            </div>
                            <div>
                                <div class="fa" >هاست دانلود آلمان</div>
                                <div class="en" >DOWNLOAD HOSTING (GERMANY)</div>
                            </div>
                        </div>
                        <ul>
                            <li>هاست دانلود آلمان بدون تحریم و آزاد</li>
                            <li>انتقال رايگان از ساير شركت ها</li>
                            <li>۱۴ روز گارانتی بازگشت وجه</li>
                            <li>گواهی نامه امنیتی <a href="<?php echo $baseUrl; ?>ssl/">SSL</a> اتوماتیک و رایگان</li>
                            <li>بکاپ گیری روزانه منظم و دقیق</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>web-hosting/germany-download-host"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۲۰،۰۰۰</span>  تومان
                                <small>/ مـاهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>

    <ul class="menu-item-2">
        <li>
            <a href="<?php echo $baseUrl; ?>domain/">ثبت دامنه</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/domainmelli.png" alt="ثبت دامنه" />
                            </div>
                            <div>
                                <div class="fa" >ثبت دامنه با مالکیت کامل</div>
                                <div class="en" >DOMAIN REGISTRATION</div>
                            </div>
                        </div>
                        <ul>
                            <li>ارزان ترین ثبت کننده دامین در کشور</li>
                            <li>تنظیم DNS ها از پنل کاربری</li>
                            <li>ثبت آنی دامنه</li>
                            <li>قابلیت قفل دامنه</li>
                            <li>حفظ حریم خصوصی در Whois</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>domain/"><div class="button">ثبت دامنه</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۹,۰۰۰</span>  تومان
                                <small>/ سـالیانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>my/cart.php?a=add&domain=transfer">انتقال دامنه</a>

            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/tld.png" alt="انتقال دامنه" />
                            </div>
                            <div>
                                <div class="fa" >انتقال دامنه مطمئن</div>
                                <div class="en" >DOMAIN TRANSFER</div>
                            </div>
                        </div>
                        <ul>
                            <li>انتقال به همراه یک سال اعتبار</li>
                            <li>مدیریت آسان دامنه با پنل اختصاصی</li>
                            <li>قابلیت قفل دامنه برای جلوگیری از سرقت دامنه</li>
                            <li>پیرو و حامی قوانین GDPR</li>
                            <li>انتقال دامنه با ارزان ترین تعرفه</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>my/cart.php?a=add&domain=transfer"><div class="button">انتقال دامنه</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۹,۰۰۰</span>  تومان
                                <small>/ سـالیانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>my/login">تمدید دامنه</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/domaintransfer.png" alt="تمدید دامنه" />
                            </div>
                            <div>
                                <div class="fa" >تمدید دامنه ارزان</div>
                                <div class="en" >DOMAIN RENEWAL</div>
                            </div>
                        </div>
                        <ul>
                            <li>ارزان ترین تعرفه در کل کشور</li>
                            <li>مدیریت آسان دامنه با پنل اختصاصی</li>
                            <li>ثبت آنی پس از پرداخت</li>
                            <li>قابلیت قفل دامنه برای جلوگیری از سرقت دامنه</li>
                            <li>پیرو و حامی قوانین GDPR</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>my/login"><div class="button">تمدید دامنه</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۹,۰۰۰</span>  تومان
                                <small>/ سـالیانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>

    <ul class="menu-item-3">
        <li>
            <a href="<?php echo $baseUrl; ?>vps/iran">سرور مجازی ایران</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/iran.png" alt="سرور مجازی ایران" />
                            </div>
                            <div>
                                <div class="fa" >سرور مجازی ایران</div>
                                <div class="en" >IRAN VPS</div>
                            </div>
                        </div>
                        <ul>
                            <li>پینگ تایم بسیار پایین</li>
                            <li>دسترسی به سرور در صورت اختلال اینترنت</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>پورت 10G و پهنای باند نامحدود (منصفانه) </li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vps/iran"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۷۵,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>vps/germany">سرور مجازی آلمان</a>

            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/germany.png" alt="سرور مجازی آلمان" />
                            </div>
                            <div>
                                <div class="fa" >سرور مجازی آلمان</div>
                                <div class="en" >GERMANY VPS</div>
                            </div>
                        </div>
                        <ul>
                            <li>پینگ پایین برای شبکه ایران</li>
                            <li>قدرتمند برای میزبانی سایت و اپلیکیشن</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>پورت 10G و پهنای باند نامحدود</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vps/germany"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱۶۰,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
                <li>
            <a href="<?php echo $baseUrl; ?>vps/usa">سرور مجازی آمریکا</a>

            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/usa.png" alt="سرور مجازی آمریکا" />
                            </div>
                            <div>
                                <div class="fa" >سرور مجازی آمریکا</div>
                                <div class="en" >USA VPS</div>
                            </div>
                        </div>
                        <ul>
                            <li>دیتاسنتر فوق پیشرفته</li>
                            <li>نصب تمامی سیستم عامل ها</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>پورت 10G و پهنای باند نامحدود</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vps/usa"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱۸۵,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>vps/finland">سرور مجازی فنلاند</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/finland.png" alt="سرور مجازی فنلاند" />
                            </div>
                            <div>
                                <div class="fa" >سرور مجازی آلمان</div>
                                <div class="en" >FINLAND VPS</div>
                            </div>
                        </div>
                        <ul>
                            <li>پینگ پایین برای شبکه ایران</li>
                            <li>قدرتمند برای میزبانی سایت و اپلیکیشن</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>پورت 10G و پهنای باند نامحدود</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vps/finland"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱۶۰,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>

    <ul class="menu-item-4">
        <li>
            <a href="<?php echo $baseUrl; ?>vds/iran">سرور اختصاصی ایران</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/iran-vds.png" alt="سرور اختصاصی ایران" />
                            </div>
                            <div>
                                <div class="fa" >سرور اختصاصی ایران</div>
                                <div class="en" >IRAN DEDICATED SERVER</div>
                            </div>
                        </div>
                        <ul>
                            <li>نصب و راه اندازی رایگان</li>
                            <li> در بستر شبکه اختصاصی پرشین میزبان</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>ارائه پورت اختصاصی 1G, 10G و 40G</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vds/iran"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۸۹۰,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>vds/france">سرور اختصاصی فرانسه</a>

            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/france-vds.png" alt="سرور اختصاصی فرانسه" />
                            </div>
                            <div>
                                <div class="fa" >سرور اختصاصی فرانسه</div>
                                <div class="en" >FRANCE DEDICATED SERVER</div>
                            </div>
                        </div>
                        <ul>
                            <li>نصب و راه اندازی رایگان</li>
                            <li>سرعت و پایداری و منابع فوق العاده</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>ارائه پورت اختصاصی 1G, 10G و 40G</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vds/france"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱,۱۹۰,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>vds/netherlands">سرور اختصاصی هلند</a>

            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/netherlands-vds.png" alt="سرور اختصاصی هلند" />
                            </div>
                            <div>
                                <div class="fa" >سرور اختصاصی هلند</div>
                                <div class="en" >NETHERLANDS  DEDICATED SERVER</div>
                            </div>
                        </div>
                        <ul>
                            <li>نصب و راه اندازی رایگان</li>
                            <li>سرعت و پایداری و منابع فوق العاده</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>ارائه پورت اختصاصی 1G, 10G و 40G</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vds/netherlands"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۱,۲۹۰,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="<?php echo $baseUrl; ?>vds/germany">سرور اختصاصی آلمان</a>
            <div class="more-info" >
                <div class="grid cols-5" >
                    <div class="col-3">
                        <div class="title" >
                            <div class="icon" >
                                <img src="/profile/germany-vds.png" alt="سرور اختصاصی آلمان" />
                            </div>
                            <div>
                                <div class="fa" >سرور اختصاصی آلمان</div>
                                <div class="en" >GERMANY DEDICATED SERVER</div>
                            </div>
                        </div>
                        <ul>
                            <li>نصب و راه اندازی رایگان</li>
                            <li>سرعت و پایداری و منابع فوق العاده</li>
                            <li>Firewall نرم‌افزاری و سخت‌افزاری</li>
                            <li>14 روز گارانتی بازگشت وجه</li>
                            <li>ارائه پورت اختصاصی 1G, 10G و 40G</li>
                        </ul>
                    </div>
                    <div class="col-2 d-flex flex-col">
                        <a href="<?php echo $baseUrl; ?>vds/germany"><div class="button">مشاهده پلن ها</div></a>
                        <div class="price" >
                            <small>شروع از</small>
                            <div>
                                <span>۹۴۰,۰۰۰</span>  تومان
                                <small>/ ماهانه</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</section>
