<?php
$pageTitle = 'تماس با پرشین میزبان | پرشین میزبان';
$pageDescription = 'تماس با پرشین میزبان از طریق پنل کاربری، چت آنلاین، تماس تلفنی و فرم ارسال نظر امکان پذیر است. بهترین روش برای پشتیبانی فنی ارسال تیکت است.';
include '../partials/header.php';
?>
                    <div class="image image2">
                        <img src="/img/img-employee2.png" alt="تماس با ما" >
                    </div>
                    <div class="pt-2 text-135 text-w-black pb-1" >
                        <div class="text-black mb-1">تماس با پرشین میزبان</div>
                        <a href="tel:02191011796"><bdi>۰۲۱ ۹ ۱۰ ۱۱ ۷۹۶</bdi></a>
                    </div>
                    <div class="text-center text-80 mb-12">
                        ۲۴ ساعت هفته خدمت شما
                    </div>
                    <a href="../my/submitticket.php" target="_blank">
                        <span class="bg-white text-red py-2 px-6 rounded-full mb-2 text-w-bold">ارسال تیکت</span>
                    </a>
                </div>
            </div>
            <div md="col-3" lg="col-8">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3238.5160086577152!2d51.3339091152604!3d35.73811988018101!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f8dfdeb1d0f0a47%3A0x9547a543dbbba254!2z2b7Ysdi024zZhiDZhduM2LLYqNin2YY!5e0!3m2!1sen!2snl!4v1652726104627!5m2!1sen!2snl" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div md="col-6" lg="col-7 pr-4">
                <div class="pt-2 mb-12">
                    جهت ارتباط با پرشین میزبان از طریق راه های زیر اقدام کنید:
                </div>
                <div class="d-flex a-items-center mb-3">
                    <div class="icon icon-phone"></div>
                    <div>
                        <div class="text-80 text-w-light">تلفن تماس</div>
                        <a href="tel:02191011796"><div class="text-w-black"><bdi>۰۲۱ ۹ ۱۰ ۱۱ <span class="text-primary">۷۹۶</span></bdi></div></a>
                    </div>
                </div>
                <div class="d-flex a-items-center mb-3">
                    <div class="icon icon-location"></div>
                    <div>
                        <div class="text-80 text-w-light">دفتر مرکزی</div>
                        <div class="text-90">تـهران، مرزداران، خیابان ابوالفضل، پلاک ۲۱۳، طبقه ۲</div>
                    </div>
                </div>
                <div class="d-flex a-items-center mb-3">
                    <div class="icon icon-email"></div>
                    <div>
                        <div class="text-80 text-w-light">ایمیل</div>
                        <a href="mailto:info@persianmizban.com"><div class="text-90">info [AT] PersianMizban.com</div></a>
                    </div>
                </div>
                <div class="d-flex a-items-center mb-6">
                    <div class="icon icon-clock"></div>
                    <div>
                        <div class="text-80 text-w-light">ساعات کاری</div>
                        <div class="text-90">۲۴ ساعته هفت روز هفته</div>
                    </div>
                </div>

                <div class="d-flex a-items-center mb-6">
                     <a href="https://www.instagram.com/persianmizban_com/"><div class="l-button">
                        <div class="icon-instagram2"></div>
                        اینستاگرام
                    </div></a>
                    <a href="https://tlgrm.in/Persian_Mizban"><div class="l-button">
                        <div class="icon-telegram2"></div>
                        کانال تلگرام
                    </div></a>
                </div>
            </div>
        </div>
    </div>
</section>


<section id="contact-comments">
    <div class="bg-1"></div>
    <div class="bg-2"></div>

    <div class="container">
        <div class="d-flex j-content-center a-items-center mb-12">
            <div class="icon-cm"></div>
            <div>
                <div class="text-primary text-w-medium mb-1">ارسال پــیشنهادات و انــتقادات</div>
                <div class="text-80">صمیمانه پذیرای نظرات شما کاربران گرامی هستیم</div>
            </div>
        </div>

        <form class="grid cols-1" sm="cols-2" lg="cols-4">
            <div>
                <div class="icon icon-male"></div>
                <label for="form-name" >نام و نام خانوادگی</label>
                <input id="form-name" type="text" required />
            </div>
            <div>
                <div class="icon icon-mail"></div>
                <label for="form-email" >ایمیل</label>
                <input type="form-email" required />
            </div>
            <div>
                <div class="icon icon-iphone"></div>
                <label for="form-phone" >شماره تماس</label>
                <input type="form-phone" required />
            </div>
            <div>
                <div class="icon icon-edit"></div>
                <label for="form-subject" >موضوع</label>
                <input type="form-subject" required />
            </div>
            <div class="d-flex flex-col" sm="col-2 flex-row" lg="col-4">
                <textarea name="" id="" placeholder="شروع به نوشتن کنید ..." required></textarea>
                <div class="mt-auto" sm="mr-6" >
                    <button type="submit" onclick="alert('نظر شما با موفقیت ارسال شد. از همکاری شما متشکریم.')" >
                        <span class="icon-tick"></span>
                        ارسال پیام
                    </button>
                </div>
            </div>
        </form>

        <div class="list">
            <div class="comment">
                <div class="image">
                    <img src="/profile/cb.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        سیامک خسروپناه
                    </div>
                    <div class="text-left text-70">
                        سه شنبه - 7 تیر 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    با سلام، من از میزبان دیگری به پرشین میزبان مراجعه کردم و واقعا کیفیت پشتیبانی و همکاری تیم، من رو شگفت زده کرد. من با 1000 مشکل و سوال به این تیم رسیدم و تماما حمایت شدم، از کل تیم شما تشکر میکنم و بابت همکاری شما ازتون ممنونم.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/co.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        رویا حیاتی
                    </div>
                    <div class="text-left text-70">
                        یکشنبه - 5 تیر 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    سلام و احترام. من مدیر موسسه آموزش آنلاین طوطی ها هستم.تقریبا 11 سال در حوزه فناروی  فعالیت دارم و با شرکت های زیادی در زمینه فروش هاست  در این چند سال ارتباط داشتم. پرشین میزبان انتخابی بود که 6 سال پیش داشتم و تا به امروز این همکار پایدار مانده است و لحظه از انتخاب خودم شک نکرده ام. وقتی پای کیفیت و امنیت میان باشد پرشین میزبان حرفی برای گفتن دارد.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/cw.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        شهردخت اللهیاری
                    </div>
                    <div class="text-left text-70">
                        یکشنبه - 5 تیر 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    انگار همین دیروز بود که اولین دامنه را به توصیه برنامه نویس موسسه در پرشین میزبان ثبت کردیم و با هزار تردید اولین هاست را خریدیم و داستان آشنایی ما تبدیل شد به استفاده 8 ساله از خدمات و سرویس های قوی و بی نظیر پرشین میزبان... همیشه برقرار باشی برند محبوب ما
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/cr.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        داراب دیباج
                    </div>
                    <div class="text-left text-70">
                        چهارشنبه - 1 تیر 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    سلام و درود فراوان به گروه پرشین میزبان. بهترین سرویس ها، ارزان ترین قیمت ها، پشتیبانی عالی، آپتایم 100 درصد سرور ها رو فقط از پرشین میزبان بخواهید.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/cb.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        آرمین تهرانی
                    </div>
                    <div class="text-left text-70">
                        دوشنبه - 30 خرداد 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    واقعا بهترین هستید، ممنون از پشتیبانی عالی تون.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/co.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        نیما عالی
                    </div>
                    <div class="text-left text-70">
                        شنبه - 28 خرداد 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    با سلام، پرشین میزبان واقعا در خدمات بسیار متفاوت تر و قدرتمند تر نسبت به سایر شرکت ها عمل میکند. در خصوص قالب جدید من عاشق قالب قبلی پرشین میزبان بودم ولی از قالب جدید پرشین میزبان خوش نیومد با توجه به اینکه قابلیت ریسپانسیو داره ولی قالب قبلی خیلی زیبا بود.به هر حال تبریک.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/cw.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        دارا موسوی
                    </div>
                    <div class="text-left text-70">
                        یکشنبه - 15 خرداد 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    با سلام و احترام، بنده قبلا در یک شرکت دیگه هاست داشتم اما متاسفانه قطعی بسیار زیادی داشت، به همین دلیل پرشین میزبان رو برای انتقال سایتم انتخاب کردم، پرشین میزبان از هر نظری قابل اعتماد هست و همچنین برای پشتیبانی بسیار عالی از پرشین میزبان سپاسگزارم، با احترام فراوان
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/cr.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        آریا بختیار
                    </div>
                    <div class="text-left text-70">
                        پنجشنبه - 12 خرداد 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    چندسالی است که از سرویس های مختلف پرشین میزبان  استفاده می کنم و همیشه تمامی درخواست هایی که داشتم، بسیار عالی و سریع انجام شده است. از پشتیبانی خوب این تیم تشکر می کنم.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/cb.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        مرجان آشوری
                    </div>
                    <div class="text-left text-70">
                        چهارشنبه - 4 خرداد 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    از نظر مالکیت های دامنه و .. پرشین میزبان بسیار عالی است.
                </div>
            </div>

            <div class="comment">
                <div class="image">
                    <img src="/profile/co.png" alt="کاربر" />
                </div>
                <div class="grid cols-1 text-90 text-primary mb-2" sm="cols-2">
                    <div class="text-w-medium">
                        میترا مجتهد شبستری
                    </div>
                    <div class="text-left text-70">
                        شنبه - 31 اردیبهشت 1401
                    </div>
                </div>
                <div class="text-lh-4">
                    واقعا کارمندای پرشین میزبان زحمت میکشن و هر زمان تیکت زدم تو چند ثانیه جواب گرفتم. متشکرم
                </div>
            </div>

            <div class="more">
                <div class="more-button">
                    مشاهده بیشتر
                    <div class="icon-more"></div>
                </div>
            </div>
        </div>

    </div>
</section>

<section id="faq">
    <div class="container" >
        <div class="title" >
            <div class="icon-faq"></div>
            <div class="my-auto">
                <div class="text-110 text-w-semibold" >سوالات متداول</div>
                <div class="text-80" >با پشتیبانی 24 ساعته و همه روزه سرویس های میزبانی پرشین میزبان سوالی باقی نمی ماند، با خیال راحت به توسعه کسب و کارتان بپردازید.</div>
            </div>
        </div>
        <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >ساعات کاری پرشین میزبان به چه صورت است؟</div>
            </div>
            <div class="a" >پرشین میزبان به صورت همه روزه حتی در روز های تعطیل 24 ساعته از طریق تماس تلفنی و بخش تیکت آماده پاسخ گویی در واحد فنی می باشد.</div>
        </div>
        <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >پاسخ گویی تلفنی در چه ساعاتی انجام میشود؟</div>
            </div>
            <div class="a" >پاسخ گویی تلفنی به صورت شبانه روزی در مرکز تماس پرشین میزبان انجام میشود.</div>
        </div>
        <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >آیا امکان مراجعه حضوری به دفتر شرکت وجود دارد؟</div>
            </div>
            <div class="a" >بله امکان مراجعه حضوری به دفتر شرکت وجود دارد، اما قبل از مراجعه حضوری میبایست از قبل از طریق تیکت تایم رزرو کنید.</div>
        </div>
                <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >چگونه می توانیم از طریق پرشین میزبان کسب درآمد کنیم؟</div>
            </div>
            <div class="a" >از قسمت همکاری در فروش می توانید کسب درآمد کنید. با هر فروش موفق بین 20 تا 40 درصد پورسانت دریافت میکنید. در این خصوص کاربران باید در پرشین میزبان عضو شده و لینک همکاری در فروش مخصوص خودشان را از ناحیه کاربری دریافت کنند.</div>
        </div>
        <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >پرشین میزبان چه کمپین و آفر های حمایتی برای کسب و کار های تازه تاسیس در نظر گرفته است؟</div>
            </div>
            <div class="a" >پرشین میزبان به کسب و کار های نوپا 20 درصد تخفیف برای اولین خرید ارائه میکند، همین طور نیازی به پرداخت هزینه برای دریافت قالب یا افزونه ندارند و موارد فنی مرتبط با سایت یا اپلیکیشن هاشونو به صورت رایگان میتوانند به پشتیبان فنی پرشین میزبان بسپارند. امید داریم با حمایت فنی و مالی از کار آفرین ها قدمی در مسیر حمایت از اشتغال آفرینی برداشته باشیم.</div>
        </div>
        <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >از کجا و چگونه از اطلاعیه ها و جشنواره های مناسبتی پرشین میزبان مطلع شویم؟</div>
            </div>
            <div class="a" >می توانید به صورت منظم اخبار مجموعه را در وب سایت اصلی، پرشین بلاگ و همچنین کانال تلگرام و اینستاگرام پرشین میزبان مطالعه کنید.</div>
        </div>
                <div class="qa" >
            <div class="d-flex a-items-center" >
                <div class="icon" ></div>
                <div class="q" >چرا پرشین میزبان؟</div>
            </div>
            <div class="a" >گارانتی عودت وجه 14 روزه<br>ارائه پهنای باند نامحدود در تمام سرویس ها<br>انتقال رایگان اطلاعات سایت مشتری از شرکت های دیگر به پرشین میزبان<br>بهره گیری از فایروال های سخت افزاری قدرتمند به جهت افزایش امنیت در کل شبکه سرویس ها<br>بکاپ گیری منظم روزانه و نگهناری آرشیو بکاپ ها به مدت 7 روز<br>مانیتورینگ 24 ساعته حتی در روز های تعطیل سرور ها به جهت جلوگیری از بروز مشکلات احتمالی<br>استفاده از سرور های بروز و قدرتمند سخت افزاری با مالکیت 100 درصد انحصاری پرشین میزبان</div>
        </div>
    </div>
</section>

<section id="contact-head">
    <div class="container">
        <div class="box">
            <div>
                <div class="content">
                    <div lg="col-3 pr-8">
                        <div class="text-120 text-w-medium mb-1">پـــاسخگوی شما هستیم</div>
                        <div class="text-80 text-w-light mb-4">در هر لحظه از شبانه روز با کادری مجرب آماده شنیدن صدای گرم شما هستیم.</div>
                        <div>
                            <a href="https://www.persianmizban.com/my/submitticket.php"><span class="button">ارسال تیکت</span></a>
                            <a href="tel:02191011796"><bdi class="phone">۰۲۱ - ۹۱۰۱۱ <b>۷۹۶</b></bdi></a>
                        </div>
                    </div>
                    <div class="w-full h-full" i-lg="d-none">
                        <div class="d-flex flex-col w-full h-full a-items-end j-content-end pl-4">
                        <div class="text-130 text-w-medium">ارتـباط با ما</div>
                        <div class="text-135 text-w-light opacity-50">CONTACT</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../partials/footer.php'; ?>
