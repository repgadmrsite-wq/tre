// ================================================================
// KISHUP - Main JavaScript
// این فایل شامل تمامی کدهای لازم برای تعامل با صفحه، فرم رزرو،
// نمایش پیشنهادها، لیست آماده‌ها، نظرات، نزدیک‌ترین موتورها و
// پنل‌های کاربری و مدیریتی است.
// ================================================================

(function () {
    "use strict";

    // Data definitions (نمونه داده‌ها برای نمایش)
    const vehiclesData = [
        {
            id: 1,
            model: "موتور برقی SuperX",
            description: "سرعت، هیجان و دوست‌دار محیط زیست.",
            price: 120000,
            img: "https://placehold.co/400x250/4ade80/111827?text=SuperX",
            status: "available",
            lat: 26.5332,
            lng: 53.9986
        },
        {
            id: 2,
            model: "موتور City Cruiser",
            description: "یک انتخاب عالی برای گشت و گذار در شهر.",
            price: 95000,
            img: "https://placehold.co/400x250/3b82f6/ffffff?text=Cruiser",
            status: "reserved",
            lat: 26.5358,
            lng: 53.9891
        },
        {
            id: 3,
            model: "اسکوتر Eco-Ride",
            description: "اقتصادی و کارآمد برای مسافت‌های کوتاه.",
            price: 80000,
            img: "https://placehold.co/400x250/6b7280/ffffff?text=Eco",
            status: "available",
            lat: 26.5387,
            lng: 54.0035
        },
        {
            id: 4,
            model: "موتور اسپرت Z-100",
            description: "طراحی اسپرت با عملکرد بالا.",
            price: 150000,
            img: "https://placehold.co/400x250/9333ea/ffffff?text=Z-100",
            status: "available",
            lat: 26.5251,
            lng: 54.0058
        }
    ];

    const specialsData = [
        {
            id: 5,
            model: "اسکوتر برقی Mini",
            description: "سبک و مناسب برای سفرهای درون‌شهری.",
            price: 70000,
            img: "https://placehold.co/400x250/fcd34d/111827?text=Mini"
        },
        {
            id: 6,
            model: "موتور Adventure",
            description: "برای عاشقان هیجان و سفرهای طولانی.",
            price: 130000,
            img: "https://placehold.co/400x250/f87171/ffffff?text=Adventure"
        }
    ];

    let reviewsData = [];

    /**
     * لیست مکان‌های شناخته‌شدهٔ جزیرهٔ کیش برای استفاده در پیشنهادات محلی.
     * شامل هتل‌ها، خیابان‌های اصلی، مراکز خرید، مراکز تفریحی، سواحل، بنادر و پارک‌هاست.
     * هر شیء می‌تواند مختصات تقریبی lat/lng داشته باشد. اگر مختصات خالی باشد،
     * در زمان انتخاب، از سرویس جستجوی نشان برای یافتن مختصات استفاده می‌شود.
     */
    const landmarksData = [
        // نقاط شاخص عمومی
        { name: "اسکله تفریحی", lat: 26.5332, lng: 53.9952 },
        { name: "پلاژ ساحلی", lat: 26.5311, lng: 53.9974 },
        { name: "دامون ساحلی", lat: 26.5420, lng: 53.9810 },
        { name: "پلاژ بانوان", lat: 26.5570, lng: 53.9875 },
        { name: "پلاژ آقایان" },
        { name: "ساحل دامون" },
        { name: "ساحل میرمهنا" },
        { name: "ساحل سیمرغ" },
        { name: "ساحل کشتی یونانی" },
        { name: "ساحل درختان نارگیل" },
        { name: "ساحل مرجان", lat: 26.5427, lng: 53.9945 },
        // هتل‌های معروف
        { name: "هتل ترنج" },
        { name: "هتل شایان", lat: 26.5326, lng: 53.9606 },
        { name: "هتل مارینا پارک" },
        { name: "هتل داریوش", lat: 26.5325, lng: 53.9902 },
        { name: "هتل ایران" },
        { name: "هتل پارمیس", lat: 26.5355, lng: 53.9905 },
        { name: "هتل آرام" },
        { name: "هتل آریان" },
        { name: "هتل سدف" },
        { name: "هتل آرامیس" },
        { name: "هتل سورینت" },
        { name: "هتل فلامینگو" },
        { name: "هتل هلیا" },
        { name: "هتل پارمیدا" },
        { name: "هتل پارسیان" },
        { name: "هتل لوتوس" },
        { name: "هتل سانرایز" },
        { name: "هتل آنا" },
        { name: "هتل گراند" },
        { name: "هتل سارا" },
        { name: "هتل جام‌جم" },
        { name: "هتل آفتاب شرق" },
        { name: "هتل آرامش" },
        { name: "هتل پارس‌نیک" },
        { name: "هتل شب‌هنگام" },
        { name: "هتل تماشا" },
        { name: "هتل پانیذ" },
        { name: "هتل اسپادانا" },
        { name: "هتل ستاره" },
        { name: "هتل سالار" },
        { name: "هتل فارابی" },
        { name: "هتل خاتم" },
        { name: "هتل قنوس" },
        { name: "هتل فانوس" },
        { name: "هتل فرشتگان" },
        { name: "هتل آسیا" },
        { name: "هتل دیدار" },
        { name: "هتل پالاس", lat: 26.5209, lng: 54.0101 },
        // خیابان‌ها و بلوارها
        { name: "جاده جهان" },
        { name: "بلوار دریا" },
        { name: "خیابان رودکی" },
        { name: "خیابان فردوسی" },
        { name: "خیابان سنایی" },
        { name: "بلوار ساحل" },
        { name: "خیابان وصال" },
        { name: "خیابان خلیج فارس" },
        { name: "بلوار خیام" },
        { name: "خیابان باباطاهر" },
        { name: "بلوار پیامبر" },
        { name: "خیابان آریان" },
        { name: "خیابان بینالود" },
        { name: "خیابان مرجان" },
        // مراکز خرید و بازارها
        { name: "مرکز خرید دامون" },
        { name: "بازار ونوس" },
        { name: "مرکز تجاری کیش", lat: 26.5339, lng: 53.9841 },
        { name: "مرکز خرید پدیده" },
        { name: "بازار صدف" },
        { name: "مرکز خرید سارینا 1" },
        { name: "مرکز خرید سارینا 2" },
        { name: "مرکز خرید مروارید", lat: 26.5398, lng: 53.9883 },
        { name: "مجتمع تجاری پردیس 1", lat: 26.5346, lng: 53.9902 },
        { name: "مجتمع تجاری پردیس 2", lat: 26.5358, lng: 53.9900 },
        { name: "رویا مال" },
        { name: "بازار مریم" },
        { name: "پانیذ مال" },
        { name: "مرکز خرید زیتون" },
        { name: "بازار صفین (بازار عرب‌ها)", lat: 26.5402, lng: 53.9845 },
        { name: "مرکز خرید دیپلمات" },
        { name: "مرکز خرید مرجان", lat: 26.5423, lng: 53.9956 },
        // مراکز تفریحی و دیدنی
        { name: "پارک آبی کیش" },
        { name: "باغ پرندگان کیش" },
        { name: "پارک دلفین کیش", lat: 26.5212, lng: 53.9966 },
        { name: "اسکله تفریحی کیش" },
        { name: "آکواریوم کیش" },
        { name: "شهربازی هایلند" },
        { name: "پارک بازی ماسه" },
        { name: "کشتی آکواریوم" },
        { name: "پارک شهر" },
        { name: "پارک ساحلی مرد ماهیگیر" },
        { name: "مسیر دوچرخه‌سواری کیش" },
        { name: "تم پارک سنتر کیش" },
        { name: "مجموعه مارینا کیش" },
        { name: "بولینگ مریم" },
        { name: "قلعه وحشت" },
        { name: "نمایشگاه خزندگان" },
        { name: "پارک کیبل اسکی آکواکوم" },
        { name: "پارک سافاری" },
        { name: "پارک ساحلی میرمهنا" },
        { name: "پارک برفی پنگوئن" },
        { name: "تله‌کابین میکامال" },
        { name: "باشگاه سوارکاری" },
        { name: "گلایدر" },
        { name: "مرکز فلای دامون" },
        { name: "کارتینگ کیش" },
        // بندرگاه‌ها و اسکله‌ها
        { name: "بندرگاه کیش" },
        { name: "اسکله میرمهنا" },
        { name: "اسکله مرجان" },
        { name: "اسکله سیمرغ" },
        { name: "اسکله ماشه" },
        { name: "بندر چارک" },
        { name: "بندر آفتاب" },
        // پارک‌ها
        { name: "پارک هنگام" },
        { name: "پارک دلفین‌ها", lat: 26.5212, lng: 53.9966 },
        { name: "پارک آبی اوشن", lat: 26.5214, lng: 53.9861 },
        { name: "پارک ساحلی مرجان", lat: 26.5427, lng: 53.9945 },
        { name: "پارک درختان نارگیل" },
        { name: "پارک آهوان" },
        { name: "پارک ساحلی مرد ماهیگیر" },
        { name: "پارک سیمرغ" },
        { name: "پارک میرمهنا" },
        { name: "پارک خانواده" },
        { name: "پارک آبشار" },
        { name: "پارک شهر" }
    ];

    // کلید API برای وب سرویس جستجوی نشان. برای استفاده از جستجوی مکان،
    // این مقدار باید با کلید معتبر شما جایگزین شود. در غیر این صورت از داده‌های محلی استفاده خواهد شد.
    // API keys: service key for data services (search, distance matrix) and map key for rendering maps
    const NESHAN_SERVICE_API_KEY = 'service.6f92c26cc02a47c6ac592bd12d083463';
    const NESHAN_MAP_API_KEY = 'web.3149369ad73f47aca745b15b9b87ee55';
    // Search API key (service) used to fetch suggestions; fallback to local landmarks when empty
    const NESHAN_SEARCH_API_KEY = NESHAN_SERVICE_API_KEY;

    // Reservation state (stores user selections)
    const reservationState = {
        vehicleId: null,
        duration: null,
        startDate: null,
        endDate: null,
        startTime: null,
        endTime: null,
        deliveryLocation: null
    };

    // Helper: Calculate haversine distance between two coords
    function haversineDistance(lat1, lon1, lat2, lon2) {
        const toRad = angle => angle * Math.PI / 180;
        const R = 6371; // Earth radius in km
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    // Initialize the entire page once DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        initThemeSwitcher();
        initHamburgerMenu();
        initStickyHeader();
        initializeBookingForm();
        renderSpecials();
        populateModelFilter();
        renderReadyList();
        renderReviews();
        // User/Admin panels have been removed from the public interface
        setupFilters();
        setupLocationSearch();
        setupNearestButton();
        // Initialize island map slider section
        initIslandMap();
        initHeroOverlay();
    });

    /* ============ Theme Switcher ============ */
    function initThemeSwitcher() {
        const switcher = document.querySelector('.theme-switcher');
        const body = document.body;
        if (!switcher) return;
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.classList.toggle('dark-mode', savedTheme === 'dark');
        } else {
            // If no saved preference, default to dark mode
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        }
        switcher.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
        });
    }

    /* ============ Hamburger Menu ============ */
    function initHamburgerMenu() {
        const hamburger = document.querySelector('.hamburger-menu');
        const nav = document.querySelector('.main-nav');
        if (!hamburger || !nav) return;
        hamburger.addEventListener('click', () => {
            nav.classList.toggle('open');
        });
    }

    /* ============ Sticky Header ============ */
    function initStickyHeader() {
        const header = document.querySelector('.main-header');
        if (!header) return;
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    /* ============ Hero Overlay ============ */
    function initHeroOverlay() {
        const availableCountElem = document.querySelector('.hero-overlay .available-count');
        const reservedCountElem = document.querySelector('.hero-overlay .reserved-count');
        const ctaBtn = document.getElementById('hero-cta');
        // Update counts
        if (availableCountElem && reservedCountElem) {
            const available = vehiclesData.filter(v => v.status === 'available').length;
            const reserved = vehiclesData.filter(v => v.status === 'reserved').length;
            availableCountElem.textContent = available.toString();
            reservedCountElem.textContent = reserved.toString();
        }
        // Scroll to booking on CTA click
        if (ctaBtn) {
            ctaBtn.addEventListener('click', () => {
                const bookingSection = document.getElementById('booking');
                if (bookingSection) {
                    bookingSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    }

    /* ============ Booking Form ============ */
    function initializeBookingForm() {
        const bookingForm = document.getElementById('booking-form');
        if (!bookingForm) return;

        const formSteps = bookingForm.querySelectorAll('.form-step');
        const progressBar = bookingForm.querySelector('.progress-bar');
        let currentStep = 0;
        let deliveryMap = null;
        let deliveryMarker = null;
        let mapInitialized = false;
        let datepickerInitialized = false;

        /**
         * نمایش مرحله‌ی فعلی و انجام تنظیمات مرتبط
         * از جمله بروزرسانی نوار پیشرفت، نمایش یا مخفی کردن نقشه،
         * و مقداردهی اولیه انتخابگر تاریخ.
         */
        const showStep = (index) => {
            currentStep = index;
            formSteps.forEach((step, i) => step.classList.toggle('active-step', i === index));
            // Update progress line and aria-valuenow
            const progressLine = progressBar.querySelector('.progress-line');
            if (progressLine) {
                progressLine.style.width = `${(index / (formSteps.length - 1)) * 100}%`;
            }
            progressBar.setAttribute('aria-valuenow', (index + 1).toString());
            // Show/hide map panel (only visible in step 3)
            const mapPanel = document.getElementById('map-panel');
            if (mapPanel) {
                if (index === 2) {
                    mapPanel.classList.remove('hidden');
                    if (!mapInitialized) {
                        initDeliveryMap();
                        mapInitialized = true;
                    }
                    setTimeout(() => { if (deliveryMap) deliveryMap.invalidateSize(); }, 0);
                } else {
                    mapPanel.classList.add('hidden');
                }
            }
            // Initialize datepicker only once when entering step 2
            if (index === 1 && !datepickerInitialized) {
                try {
                    jalaliDatepicker.startWatch({ minDate: 'today' });
                    datepickerInitialized = true;
                } catch (e) {
                    console.error('خطا در بارگذاری انتخابگر تاریخ:', e);
                }
            }
            // When entering confirmation step update summary
            if (index === 3) {
                updateSummary();
            }
        };

        // Initialize map for delivery location
        function initDeliveryMap() {
            const mapContainer = document.getElementById('delivery-map-container');
            if (!mapContainer) return;
            // اگر SDK نشان لود شده باشد، تلاش می‌کنیم از آن استفاده کنیم
            let mapCreated = false;
            try {
                if (typeof L !== 'undefined' && typeof L.Map === 'function') {
                    // استفاده از نقشهٔ نشان با کلید API. این کلید باید توسط مالک سایت در صورت داشتن حساب نشان جایگزین شود.
                    deliveryMap = new L.Map('delivery-map-container', {
                        key: NESHAN_MAP_API_KEY,
                        maptype: 'neshan',
                        poi: true,
                        traffic: false,
                        center: [26.5332, 53.9986],
                        zoom: 13
                    });
                    mapCreated = true;
                }
            } catch (err) {
                console.error('خطا در بارگذاری نقشهٔ نشان:', err);
            }
            // اگر نقشهٔ نشان ایجاد نشد، از لایهٔ OSM به عنوان پشتیبان استفاده می‌کنیم
            if (!mapCreated) {
                // اطمینان از وجود Leaflet
                if (typeof L !== 'undefined') {
                    deliveryMap = L.map(mapContainer).setView([26.5332, 53.9986], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(deliveryMap);
                    mapCreated = true;
                }
            }
            if (!mapCreated) {
                console.error('نقشه نمی‌تواند بارگذاری شود.');
                return;
            }
            // ذخیره نقشه در عنصر برای دسترسی در سایر بخش‌ها
            mapContainer._leaflet_map = deliveryMap;
            // رویداد کلیک برای قرار دادن مارکر تحویل
            deliveryMap.on('click', (e) => {
                if (deliveryMarker) deliveryMap.removeLayer(deliveryMarker);
                deliveryMarker = L.marker(e.latlng).addTo(deliveryMap).bindPopup('محل تحویل').openPopup();
                reservationState.deliveryLocation = e.latlng;
                updateSummary();
            });
        }

        // Populate vehicle selection list in Step 1
        const vehicleListContainer = document.getElementById('vehicle-selection-list');
        function renderVehicleList() {
            if (!vehicleListContainer) return;
            vehicleListContainer.innerHTML = '';
            vehiclesData.forEach(vehicle => {
                const item = document.createElement('div');
                item.className = 'vehicle-item';
                item.dataset.id = vehicle.id;
                item.innerHTML = `
                    <img src="${vehicle.img}" alt="${vehicle.model}">
                    <div class="vehicle-item-info">
                        <h5>${vehicle.model}</h5>
                        <p>ساعتی ${vehicle.price.toLocaleString()} تومان</p>
                    </div>
                `;
                item.addEventListener('click', () => {
                    selectVehicle(vehicle.id);
                });
                vehicleListContainer.appendChild(item);
            });
        }
        renderVehicleList();

        function selectVehicle(vehicleId) {
            const items = vehicleListContainer.querySelectorAll('.vehicle-item');
            items.forEach(i => i.classList.remove('selected'));
            const target = vehicleListContainer.querySelector(`.vehicle-item[data-id="${vehicleId}"]`);
            if (target) {
                target.classList.add('selected');
            }
            reservationState.vehicleId = vehicleId;
            // Enable next button in step 1
            const nextBtn = formSteps[0].querySelector('.next-btn');
            if (nextBtn) nextBtn.disabled = false;
            // Optionally move automatically to step 2
            setTimeout(() => showStep(1), 200);

            // Update summary when vehicle changes
            updateSummary();
        }

        // Grab duration and time/date elements
        const durationRadios = bookingForm.querySelectorAll('input[name="duration"]');
        const halfDayOptions = document.getElementById('half-day-options');
        const halfDayMorning = document.getElementById('half-day-morning');
        const halfDayEvening = document.getElementById('half-day-evening');

        const startDateInput = document.getElementById('start-date-input');
        const endDateInput = document.getElementById('end-date-input');
        const startTimeInput = document.getElementById('start-time');
        const endTimeInput = document.getElementById('end-time');

        // Quick date and duration-specific option containers
        const quickDateButtons = document.getElementById('quick-date-buttons');
        const todayButton = document.getElementById('today-button');
        const tomorrowButton = document.getElementById('tomorrow-button');
        const dailyOptions = document.getElementById('daily-options');
        const hourlyOptions = document.getElementById('hourly-options');
        // با کلیک روی فیلدهای ساعت، انتخابگر ساعت به صورت خودکار باز می‌شود (در مرورگرهای پشتیبان)
        if (startTimeInput) {
            startTimeInput.addEventListener('click', () => {
                if (startTimeInput.showPicker) startTimeInput.showPicker();
            });
        }
        if (endTimeInput) {
            endTimeInput.addEventListener('click', () => {
                if (endTimeInput.showPicker) endTimeInput.showPicker();
            });
        }

        // Quick date buttons for hourly bookings
        if (todayButton) {
            todayButton.addEventListener('click', () => {
                reservationState.startDate = 'امروز';
                if (startDateInput) startDateInput.value = 'امروز';
                // Toggle active state on buttons
                if (quickDateButtons) {
                    const btns = quickDateButtons.querySelectorAll('button');
                    btns.forEach(btn => btn.classList.remove('active'));
                    todayButton.classList.add('active');
                }
                // Reset end date/time since hourly only needs start date and start time
                reservationState.endDate = null;
                reservationState.endTime = null;
                // Focus on start time input if visible
                if (reservationState.duration === 'hourly' && startTimeInput && startTimeInput.parentElement && startTimeInput.parentElement.style.display !== 'none') {
                    startTimeInput.focus();
                }
                updateSummary();
            });
        }
        if (tomorrowButton) {
            tomorrowButton.addEventListener('click', () => {
                reservationState.startDate = 'فردا';
                if (startDateInput) startDateInput.value = 'فردا';
                if (quickDateButtons) {
                    const btns = quickDateButtons.querySelectorAll('button');
                    btns.forEach(btn => btn.classList.remove('active'));
                    tomorrowButton.classList.add('active');
                }
                reservationState.endDate = null;
                reservationState.endTime = null;
                if (reservationState.duration === 'hourly' && startTimeInput && startTimeInput.parentElement && startTimeInput.parentElement.style.display !== 'none') {
                    startTimeInput.focus();
                }
                updateSummary();
            });
        }

        // Helper function to compute end time for hourly bookings based on the selected
        // start time and hour count. If either value is missing, the end time is cleared.
        function computeEndTime() {
            // Only compute when duration is hourly and both startTime and hourCount exist
            if (reservationState.duration !== 'hourly') {
                return;
            }
            if (reservationState.startTime && reservationState.hourCount) {
                const [sh, sm] = reservationState.startTime.split(':').map(num => parseInt(num, 10));
                let endHour = sh + reservationState.hourCount;
                const endMinute = sm;
                // Wrap around 24h
                endHour = endHour % 24;
                const pad = (n) => n.toString().padStart(2, '0');
                reservationState.endTime = `${pad(endHour)}:${pad(endMinute)}`;
            } else {
                reservationState.endTime = null;
            }
        }

        // Handle selecting hour count for hourly bookings
        if (hourlyOptions) {
            const hourCountRadios = hourlyOptions.querySelectorAll('input[name="hourCount"]');
            hourCountRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const hours = parseInt(e.target.value, 10);
                    if (isNaN(hours)) return;
                    reservationState.hourCount = hours;
                    computeEndTime();
                    updateSummary();
                });
            });
        }

        // Handle selecting day count for daily bookings
        if (dailyOptions) {
            const dayCountRadios = dailyOptions.querySelectorAll('input[name="dayCount"]');
            dayCountRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const days = parseInt(e.target.value, 10);
                    if (isNaN(days)) return;
                    reservationState.dayCount = days;
                    updateSummary();
                });
            });
        }

        /**
         * تنظیم نمایش فیلدهای تاریخ و زمان بر اساس نوع رزرو
         * و بروزرسانی وضعیت رزرو
         */
        function handleDurationChange(value) {
            reservationState.duration = value;
            // Reset half-day selection and show/hide date/time fields appropriately
            // Reset any previous selections specific to other durations
            reservationState.halfDayOption = null;
            reservationState.hourCount = null;
            reservationState.dayCount = null;
            // Handle half-day duration
            if (value === 'half-day') {
                // Show half-day options and start date input
                if (halfDayOptions) halfDayOptions.classList.remove('hidden');
                if (startDateInput) startDateInput.style.display = '';
                // Hide end date input, daily and hourly options, quick date buttons
                if (endDateInput) endDateInput.style.display = 'none';
                if (dailyOptions) dailyOptions.classList.add('hidden');
                if (hourlyOptions) hourlyOptions.classList.add('hidden');
                if (quickDateButtons) quickDateButtons.classList.add('hidden');
                // Hide time inputs
                if (startTimeInput && startTimeInput.parentElement) {
                    startTimeInput.parentElement.style.display = 'none';
                    startTimeInput.required = false;
                }
                if (endTimeInput && endTimeInput.parentElement) {
                    endTimeInput.parentElement.style.display = 'none';
                    endTimeInput.required = false;
                }
                // Clear date/time values not relevant
                reservationState.endDate = reservationState.startDate;
                reservationState.startTime = null;
                reservationState.endTime = null;
            } else if (value === 'daily') {
                // Hide half-day and hourly options
                if (halfDayOptions) halfDayOptions.classList.add('hidden');
                if (hourlyOptions) hourlyOptions.classList.add('hidden');
                // Hide quick date buttons
                if (quickDateButtons) quickDateButtons.classList.add('hidden');
                // Show daily options and start date input
                if (dailyOptions) dailyOptions.classList.remove('hidden');
                if (startDateInput) startDateInput.style.display = '';
                // Hide end date input (we no longer need a separate end date field)
                if (endDateInput) endDateInput.style.display = 'none';
                // Hide time inputs
                if (startTimeInput && startTimeInput.parentElement) {
                    startTimeInput.parentElement.style.display = 'none';
                    startTimeInput.required = false;
                }
                if (endTimeInput && endTimeInput.parentElement) {
                    endTimeInput.parentElement.style.display = 'none';
                    endTimeInput.required = false;
                }
                // Clear times
                reservationState.startTime = null;
                reservationState.endTime = null;
                // End date will be calculated based on dayCount in summary
                reservationState.endDate = null;
            } else {
                // Hourly duration
                // Hide half-day and daily options
                if (halfDayOptions) halfDayOptions.classList.add('hidden');
                if (dailyOptions) dailyOptions.classList.add('hidden');
                // Show quick date buttons for selecting today/tomorrow
                if (quickDateButtons) quickDateButtons.classList.remove('hidden');
                // Hide both start and end date inputs – only quick date buttons are used
                if (startDateInput) startDateInput.style.display = 'none';
                if (endDateInput) endDateInput.style.display = 'none';
                // Show start time input for selecting delivery time
                if (startTimeInput && startTimeInput.parentElement) {
                    startTimeInput.parentElement.style.display = '';
                    startTimeInput.required = true;
                }
                // Hide end time input for hourly (end time will be computed)
                if (endTimeInput && endTimeInput.parentElement) {
                    endTimeInput.parentElement.style.display = 'none';
                    endTimeInput.required = false;
                }
                // Show hourly options grid for selecting hour count
                if (hourlyOptions) hourlyOptions.classList.remove('hidden');
                // Reset end date and times
                reservationState.endDate = null;
                reservationState.endTime = null;
            }
            updateSummary();
        }

        // Attach duration change listeners
        durationRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                handleDurationChange(e.target.value);
            });
        });

        // Half-day options: morning or evening
        if (halfDayMorning) {
            halfDayMorning.addEventListener('change', (e) => {
                if (e.target.checked) {
                    // Morning block: from midnight to noon
                    reservationState.startTime = '00:00';
                    reservationState.endTime = '12:00';
                    reservationState.halfDayOption = 'morning';
                    // End date equals start date for half-day
                    reservationState.endDate = reservationState.startDate;
                    updateSummary();
                }
            });
        }
        if (halfDayEvening) {
            halfDayEvening.addEventListener('change', (e) => {
                if (e.target.checked) {
                    // Evening block: from noon to midnight
                    reservationState.startTime = '12:00';
                    reservationState.endTime = '24:00';
                    reservationState.halfDayOption = 'evening';
                    reservationState.endDate = reservationState.startDate;
                    updateSummary();
                }
            });
        }

        // Date inputs change handler
        if (startDateInput) startDateInput.addEventListener('change', (e) => {
            reservationState.startDate = e.target.value;
            // برای نیم‌روز، تاریخ پایان معادل تاریخ شروع است
            if (reservationState.duration === 'half-day') {
                reservationState.endDate = e.target.value;
            }
            // در رزرو ساعتی پس از انتخاب تاریخ، مستقیماً به انتخاب ساعت تحویل بروید
            if (reservationState.duration === 'hourly' && startTimeInput && startTimeInput.parentElement) {
                // Clear any previous hour selection
                reservationState.hourCount = null;
                startTimeInput.focus();
            }
            // در رزرو روزانه پس از انتخاب تاریخ، کاربر باید تعداد روزها را انتخاب کند، لذا نیازی به فیلد تاریخ پایان نیست
            updateSummary();
        });
        if (endDateInput) endDateInput.addEventListener('change', (e) => {
            reservationState.endDate = e.target.value;
            updateSummary();
        });
        // Time inputs change handler
        if (startTimeInput) startTimeInput.addEventListener('change', (e) => {
            // Update start time and recompute end time if necessary
            reservationState.startTime = e.target.value;
            computeEndTime();
            updateSummary();
        });
        if (endTimeInput) endTimeInput.addEventListener('change', (e) => {
            reservationState.endTime = e.target.value;
            updateSummary();
        });

        // Navigation buttons
        bookingForm.addEventListener('click', (e) => {
            const target = e.target;
            if (!(target instanceof HTMLElement)) return;
            if (target.classList.contains('next-btn')) {
                e.preventDefault();
                if (currentStep < formSteps.length - 1) {
                    showStep(currentStep + 1);
                }
            }
            if (target.classList.contains('prev-btn')) {
                e.preventDefault();
                if (currentStep > 0) {
                    showStep(currentStep - 1);
                }
            }
        });

        // Submit / send OTP
        const otpButton = document.getElementById('send-otp-btn');
        const otpSection = document.getElementById('otp-section');
        if (otpButton) {
            otpButton.addEventListener('click', (e) => {
                e.preventDefault();
                // Minimal validation: require phone number
                const phone = document.getElementById('phone');
                if (phone && phone.value.trim() === '') {
                    alert('لطفاً شماره موبایل را وارد کنید.');
                    return;
                }
                // Simulate sending OTP
                otpSection.classList.remove('hidden');
                otpButton.disabled = true;
                otpButton.textContent = 'کد ارسال شد';
                alert('کد تایید ارسال شد!');
            });
        }

        // Show initial step
        showStep(0);
    }

    /* ============ Summary Section ============ */
    /**
     * بروزرسانی پنل خلاصه رزرو بر اساس وضعیت انتخاب‌ها
     * این تابع جزئیات موتور، مدت، تاریخ، ساعت، محل تحویل و قیمت نهایی را نمایش می‌دهد.
     */
    function updateSummary() {
        const summaryPanel = document.getElementById('summary-panel');
        if (!summaryPanel) return;
        summaryPanel.innerHTML = '';
        // If no vehicle selected yet, show simple message
        if (!reservationState.vehicleId) {
            summaryPanel.innerHTML = '<p>موتوری انتخاب نشده است.</p>';
            return;
        }
        const vehicle = vehiclesData.find(v => v.id === reservationState.vehicleId);
        if (!vehicle) return;
        // Determine duration label
        const durationLabel = convertDuration(reservationState.duration);
        // Compute total price
        let totalPrice = 0;
        if (reservationState.duration === 'hourly') {
            // اگر hourCount توسط کاربر انتخاب شده باشد، براساس آن قیمت محاسبه می‌کنیم
            if (reservationState.hourCount) {
                totalPrice = reservationState.hourCount * vehicle.price;
            } else if (reservationState.startDate && reservationState.startTime && reservationState.endTime) {
                // در غیر این صورت اختلاف ساعت را محاسبه می‌کنیم
                const startDate = reservationState.startDate;
                const start = new Date(`${startDate} ${reservationState.startTime}`);
                const end = new Date(`${startDate} ${reservationState.endTime}`);
                let diffHours = (end - start) / (1000 * 60 * 60);
                if (!isFinite(diffHours) || diffHours <= 0) diffHours = 1;
                totalPrice = diffHours * vehicle.price;
            }
        } else if (reservationState.duration === 'half-day') {
            // نیم‌روز: ۱۲ ساعت
            totalPrice = vehicle.price * 12;
        } else if (reservationState.duration === 'daily') {
            // محاسبه مبلغ بر اساس تعداد روز انتخابی
            const days = reservationState.dayCount || 1;
            totalPrice = vehicle.price * 24 * days;
        }
        // Format location text
        let locationText = 'انتخاب نشده';
        if (reservationState.deliveryLocation) {
            const lat = reservationState.deliveryLocation.lat.toFixed(4);
            const lng = reservationState.deliveryLocation.lng.toFixed(4);
            locationText = `${lat}, ${lng}`;
        }
        // Build summary HTML
        let summaryHTML = `<h5>خلاصه رزرو</h5>`;
        summaryHTML += `<p><strong>مدل موتور:</strong> ${vehicle.model}</p>`;
        summaryHTML += `<p><strong>مدت:</strong> ${durationLabel || '-'}</p>`;
        // Date/time display depending on duration
        if (reservationState.duration === 'hourly') {
            summaryHTML += `<p><strong>تاریخ:</strong> ${reservationState.startDate || '-'}</p>`;
            summaryHTML += `<p><strong>بازه:</strong> ${reservationState.startTime || '-'} تا ${reservationState.endTime || '-'}</p>`;
        } else if (reservationState.duration === 'half-day') {
            summaryHTML += `<p><strong>تاریخ:</strong> ${reservationState.startDate || '-'}</p>`;
            // زمان‌های نیم‌روز: نیم‌روز اول از ۰۰ تا ۱۲ و نیم‌روز دوم از ۱۲ تا ۲۴
            const timeLabel = reservationState.halfDayOption === 'morning'
                ? '۰۰:۰۰ - ۱۲:۰۰'
                : reservationState.halfDayOption === 'evening'
                ? '۱۲:۰۰ - ۲۴:۰۰'
                : '-';
            summaryHTML += `<p><strong>بازه زمانی:</strong> ${timeLabel}</p>`;
        } else if (reservationState.duration === 'daily') {
            summaryHTML += `<p><strong>تاریخ شروع:</strong> ${reservationState.startDate || '-'}</p>`;
            // در رزرو روزانه تاریخ پایان بر اساس تعداد روز انتخابی محاسبه می‌شود، نمایش فقط تعداد روز کافی است
            const days = reservationState.dayCount || 1;
            summaryHTML += `<p><strong>مدت:</strong> ${days} روز</p>`;
        }
        summaryHTML += `<p><strong>محل تحویل:</strong> ${locationText}</p>`;
        if (totalPrice > 0) {
            summaryHTML += `<p><strong>مبلغ کل:</strong> ${Math.round(totalPrice).toLocaleString()} تومان</p>`;
        }
        summaryPanel.innerHTML = summaryHTML;
    }
    function convertDuration(duration) {
        switch (duration) {
            case 'hourly': return 'ساعتی';
            case 'half-day': return 'نیم‌روز';
            case 'daily': return 'روزانه';
            default: return '-';
        }
    }

    /* ============ Specials Rendering ============ */
    function renderSpecials() {
        const specialsGrid = document.getElementById('specials-grid');
        if (!specialsGrid) return;
        specialsGrid.innerHTML = '';
        specialsData.forEach(item => {
            const card = document.createElement('div');
            card.className = 'special-card';
            card.innerHTML = `
                <img src="${item.img}" alt="${item.model}">
                <div class="special-content">
                    <h3>${item.model}</h3>
                    <p>${item.description}</p>
                    <span class="price">ساعتی ${item.price.toLocaleString()} تومان</span>
                    <button type="button" data-id="${item.id}">رزرو</button>
                </div>
            `;
            card.querySelector('button').addEventListener('click', () => {
                // Select this vehicle and scroll to booking
                reservationState.vehicleId = item.id;
                document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
                // Pre-select the vehicle in step 1 if exists
                const vehicleElement = document.querySelector(`.vehicle-item[data-id="${item.id}"]`);
                if (vehicleElement) {
                    vehicleElement.click();
                }
            });
            specialsGrid.appendChild(card);
        });
    }

    /* ============ Ready List Rendering ============ */
    function populateModelFilter() {
        const modelFilter = document.getElementById('model-filter');
        if (!modelFilter) return;
        // Get unique model names
        const models = Array.from(new Set(vehiclesData.map(v => v.model)));
        models.forEach(model => {
            const option = document.createElement('option');
            option.value = model;
            option.textContent = model;
            modelFilter.appendChild(option);
        });
    }
    function renderReadyList() {
        const readyGrid = document.getElementById('ready-grid');
        if (!readyGrid) return;
        readyGrid.innerHTML = '';
        // Apply filters
        const modelFilterValue = document.getElementById('model-filter').value;
        const statusFilterValue = document.getElementById('status-filter').value;
        const filtered = vehiclesData.filter(item => {
            const modelMatch = modelFilterValue === 'all' || item.model === modelFilterValue;
            const statusMatch = statusFilterValue === 'available' ? item.status === 'available' : item.status === 'reserved';
            return modelMatch && statusMatch;
        });
        if (filtered.length === 0) {
            readyGrid.innerHTML = '<p>موردی یافت نشد.</p>';
            return;
        }
        filtered.forEach(item => {
            const card = document.createElement('div');
            card.className = 'ready-card';
            card.innerHTML = `
                <img src="${item.img}" alt="${item.model}">
                <div class="card-content">
                    <span class="status ${item.status}">${item.status === 'available' ? 'موجود' : 'رزرو شده'}</span>
                    <h3>${item.model}</h3>
                    <p>${item.description}</p>
                    <span class="price">ساعتی ${item.price.toLocaleString()} تومان</span>
                    <button type="button" class="book-btn" ${item.status === 'reserved' ? 'disabled' : ''} data-id="${item.id}">رزرو</button>
                </div>
            `;
            const bookBtn = card.querySelector('.book-btn');
            bookBtn.addEventListener('click', () => {
                if (item.status === 'reserved') return;
                reservationState.vehicleId = item.id;
                document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
                const vehicleElement = document.querySelector(`.vehicle-item[data-id="${item.id}"]`);
                if (vehicleElement) vehicleElement.click();
            });
            readyGrid.appendChild(card);
        });
    }
    function setupFilters() {
        const modelFilter = document.getElementById('model-filter');
        const statusFilter = document.getElementById('status-filter');
        if (modelFilter) modelFilter.addEventListener('change', renderReadyList);
        if (statusFilter) statusFilter.addEventListener('change', renderReadyList);
    }

    /* ============ Reviews Rendering ============ */
    async function renderReviews() {
        const reviewsGrid = document.getElementById('reviews-grid');
        if (!reviewsGrid) return;
        reviewsGrid.innerHTML = '<p class="loading-text">در حال بارگذاری...</p>';
        try {
            const res = await fetch('api/reviews.php');
            reviewsData = await res.json();
        } catch (err) {
            reviewsGrid.innerHTML = '<p>خطا در بارگذاری نظرات</p>';
            return;
        }
        reviewsGrid.innerHTML = '';
        reviewsData.forEach(review => {
            const card = document.createElement('div');
            card.className = 'review-card';
            card.innerHTML = `
                <div class="reviewer">
                    <img src="https://i.pravatar.cc/100?u=${review.user_id}" alt="${review.name}">
                    <div class="reviewer-info">
                        <p class="reviewer-name">${review.name}</p>
                        <p class="review-date">${review.created_at.split(' ')[0]}</p>
                    </div>
                </div>
                <p>${review.comment}</p>
            `;
            reviewsGrid.appendChild(card);
        });
    }

    /* ============ Location Search ============ */
    function setupLocationSearch() {
        const input = document.getElementById('location-search-input');
        const suggestionsList = document.getElementById('suggestions-list');
        if (!input || !suggestionsList) return;
        // Helper: render suggestions from an array of objects with name/lat/lng
        function renderSuggestions(items) {
            suggestionsList.innerHTML = '';
            items.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.title || item.name;
                // ذخیره مختصات موجود، در غیر این صورت undefined باقی می‌ماند
                li.dataset.lat = item.lat || (item.location ? item.location.y : undefined);
                li.dataset.lng = item.lng || (item.location ? item.location.x : undefined);
                li.addEventListener('click', async () => {
                    input.value = li.textContent;
                    suggestionsList.innerHTML = '';
                    let lat = parseFloat(li.dataset.lat);
                    let lng = parseFloat(li.dataset.lng);
                    // اگر مختصات موجود نیست، از سرویس جستجو استفاده کن
                    if (isNaN(lat) || isNaN(lng)) {
                        // تلاش برای یافتن مختصات با جستجوی آنلاین
                        if (typeof fetchSearchSuggestions === 'function') {
                            try {
                                const results = await fetchSearchSuggestions(li.textContent);
                                if (results && results.length > 0) {
                                    const loc = results[0].location;
                                    if (loc && typeof loc.x === 'number' && typeof loc.y === 'number') {
                                        lng = loc.x;
                                        lat = loc.y;
                                    }
                                }
                            } catch (err) {
                                console.error('Error fetching coordinates:', err);
                            }
                        }
                        // اگر هنوز مختصات به دست نیامده باشد، از نقطه مرکزی جزیره استفاده می‌کنیم
                        if (isNaN(lat) || isNaN(lng)) {
                            lat = 26.5332;
                            lng = 53.9986;
                        }
                    }
                    reservationState.deliveryLocation = { lat, lng };
                    // به‌روزرسانی مارکر روی نقشه در صورت وجود نقشه
                    const mapContainer = document.getElementById('delivery-map-container');
                    if (mapContainer && mapContainer._leaflet_map) {
                        const map = mapContainer._leaflet_map;
                        // حذف مارکرهای قبلی
                        map.eachLayer(layer => {
                            if (layer instanceof L.Marker) map.removeLayer(layer);
                        });
                        L.marker([lat, lng]).addTo(map).bindPopup('محل تحویل').openPopup();
                        map.setView([lat, lng], 14);
                    }
                    updateSummary();
                });
                suggestionsList.appendChild(li);
            });
        }
        // Fetch suggestions from Neshan Search API
        async function fetchSearchSuggestions(query) {
            if (!NESHAN_SEARCH_API_KEY || NESHAN_SEARCH_API_KEY === 'YOUR_NESHAN_SEARCH_API_KEY') {
                return null;
            }
            const url = `https://api.neshan.org/v1/search?term=${encodeURIComponent(query)}&lat=26.5332&lng=53.9986`;
            try {
                const response = await fetch(url, {
                    headers: { 'Api-Key': NESHAN_SEARCH_API_KEY }
                });
                if (!response.ok) return null;
                const data = await response.json();
                return data.items || [];
            } catch (error) {
                console.error('Search API error:', error);
                return null;
            }
        }
        input.addEventListener('input', async () => {
            const query = input.value.trim();
            suggestionsList.innerHTML = '';
            // Only search when at least one character is entered
            if (query.length < 1) return;
            // Prefer online search if API key is provided
            let results = await fetchSearchSuggestions(query);
            if (results && results.length > 0) {
                renderSuggestions(results);
            } else {
                // Fallback to static landmarks data
                const matches = landmarksData.filter(item => item.name.includes(query));
                renderSuggestions(matches);
            }
        });
        // Hide suggestions on outside click
        document.addEventListener('click', (e) => {
            if (!suggestionsList.contains(e.target) && e.target !== input) {
                suggestionsList.innerHTML = '';
            }
        });
    }

    /* ============ Nearest Scooter ============ */
    function setupNearestButton() {
        const nearestBtn = document.getElementById('nearest-btn');
        const nearestResult = document.getElementById('nearest-result');
        if (!nearestBtn || !nearestResult) return;
        nearestBtn.addEventListener('click', () => {
            if (!navigator.geolocation) {
                nearestResult.innerHTML = '<p>مرورگر شما دسترسی به موقعیت مکانی را پشتیبانی نمی‌کند.</p>';
                return;
            }
            nearestBtn.disabled = true;
            nearestBtn.textContent = 'در حال جستجو...';
            navigator.geolocation.getCurrentPosition((position) => {
                const { latitude, longitude } = position.coords;
                // Find nearest vehicle based on travel time using Distance Matrix API if available,
                // otherwise fall back to Haversine distance.
                (async () => {
                    let nearestVehicle = null;
                    let minDurationSec = Infinity;
                    // Helper to call distance matrix API for each available scooter
                    async function getDuration(originLat, originLng, destLat, destLng) {
                        if (!NESHAN_SERVICE_API_KEY) {
                            return null;
                        }
                        const url = `https://api.neshan.org/v1/distance-matrix?origins=${originLat},${originLng}&destinations=${destLat},${destLng}`;
                        try {
                            const res = await fetch(url, {
                                headers: { 'Api-Key': NESHAN_SERVICE_API_KEY }
                            });
                            if (!res.ok) return null;
                            const data = await res.json();
                            if (data.rows && data.rows[0] && data.rows[0].elements && data.rows[0].elements[0]) {
                                const elem = data.rows[0].elements[0];
                                if (elem.status === 'OK') {
                                    return { duration: elem.duration.value, distance: elem.distance.value };
                                }
                            }
                        } catch (err) {
                            console.error('Distance matrix error:', err);
                        }
                        return null;
                    }
                    for (const v of vehiclesData) {
                        if (v.status !== 'available') continue;
                        let durationInfo = null;
                        if (NESHAN_SERVICE_API_KEY) {
                            durationInfo = await getDuration(latitude, longitude, v.lat, v.lng);
                        }
                        let duration = null;
                        if (durationInfo) {
                            duration = durationInfo.duration; // seconds
                        }
                        if (duration === null) {
                            // fallback to haversine distance (approx convert km to seconds)
                            const distKm = haversineDistance(latitude, longitude, v.lat, v.lng);
                            duration = distKm * 90; // assume 40 km/h -> 1 km in ~90s
                        }
                        if (duration < minDurationSec) {
                            minDurationSec = duration;
                            nearestVehicle = v;
                        }
                    }
                    nearestBtn.disabled = false;
                    nearestBtn.textContent = 'پیدا کردن';
                    if (!nearestVehicle) {
                        nearestResult.innerHTML = '<p>هیچ موتوری در دسترس نیست.</p>';
                        return;
                    }
                    let displayDist = '';
                    let displayTime = '';
                    if (NESHAN_SERVICE_API_KEY) {
                        // compute approximate distance from duration (if using fallback this will approximate)
                        const km = (minDurationSec / 90).toFixed(2);
                        displayDist = `${km} کیلومتر`;
                        const mins = Math.round(minDurationSec / 60);
                        displayTime = `${mins} دقیقه`;
                    }
                    nearestResult.innerHTML = `
                        <h3>${nearestVehicle.model}</h3>
                        ${displayDist ? `<p>مسافت: ${displayDist}</p>` : ''}
                        ${displayTime ? `<p>زمان تقریبی: ${displayTime}</p>` : ''}
                        <p>قیمت ساعتی: ${nearestVehicle.price.toLocaleString()} تومان</p>
                        <button type="button" class="book-btn" data-id="${nearestVehicle.id}">رزرو</button>
                    `;
                    const btn = nearestResult.querySelector('.book-btn');
                    btn.addEventListener('click', () => {
                        reservationState.vehicleId = nearestVehicle.id;
                        document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
                        const vehicleElement = document.querySelector(`.vehicle-item[data-id="${nearestVehicle.id}"]`);
                        if (vehicleElement) vehicleElement.click();
                    });
                })();
            }, (err) => {
                nearestBtn.disabled = false;
                nearestBtn.textContent = 'پیدا کردن';
                nearestResult.innerHTML = `<p>خطا در دریافت موقعیت مکانی: ${err.message}</p>`;
            });
        });
    }

    /* ============ Island Map Slider ============ */
    /**
     * نمایش نقشه‌ی کامل جزیره و علامت‌گذاری موتورها بر اساس وضعیت رزرو.
     * موتورها با رنگ سبز (موجود) و قرمز (رزرو شده) نمایش داده می‌شوند.
     * با کلیک روی هر مارکر اطلاعات موتور در یک پنجره ظاهر می‌شود.
     */
    function initIslandMap() {
        const mapContainer = document.getElementById('island-map-container');
        if (!mapContainer) return;
        let islandMap = null;
        let mapCreated = false;
        // Try to use Neshan SDK with API key
        try {
            if (typeof L !== 'undefined' && typeof L.Map === 'function') {
                islandMap = new L.Map('island-map-container', {
                    key: NESHAN_MAP_API_KEY,
                    maptype: 'neshan',
                    poi: true,
                    traffic: false,
                    center: [26.5332, 53.9986],
                    zoom: 13
                });
                mapCreated = true;
            }
        } catch (err) {
            console.error('خطا در بارگذاری نقشهٔ نشان:', err);
        }
        // Fallback to OpenStreetMap if Neshan not available
        if (!mapCreated) {
            if (typeof L !== 'undefined') {
                islandMap = L.map(mapContainer).setView([26.5332, 53.9986], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(islandMap);
                mapCreated = true;
            }
        }
        if (!mapCreated) {
            console.error('نقشهٔ جزیره نمی‌تواند بارگذاری شود.');
            return;
        }
        // Add markers for each vehicle
        vehiclesData.forEach(v => {
            const color = v.status === 'available' ? '#22c55e' : '#ef4444';
            // Use circle marker for halo effect
            const marker = L.circleMarker([v.lat, v.lng], {
                radius: 8,
                color: color,
                fillColor: color,
                fillOpacity: 0.6
            }).addTo(islandMap);
            const statusLabel = v.status === 'available' ? 'موجود' : 'رزرو شده';
            const popupContent = `<div class="map-popup"><strong>${v.model}</strong><br>وضعیت: ${statusLabel}<br>قیمت ساعتی: ${v.price.toLocaleString()} تومان</div>`;
            marker.bindPopup(popupContent);
        });
    }

    /* ============ User & Admin Panels ============ */
    function renderUserPanel() {
        const userPanelGrid = document.getElementById('user-panel-grid');
        if (!userPanelGrid) return;
        const panels = [
            {
                title: 'ورود / ثبت‌نام',
                description: 'برای استفاده از خدمات، وارد حساب خود شوید یا ثبت‌نام کنید.',
                action: () => alert('بخش ورود و ثبت‌نام در دست توسعه است.')
            },
            {
                title: 'رزروهای من',
                description: 'مشاهده و مدیریت رزروهای انجام‌شده.',
                action: () => alert('بخش رزروهای من در دست توسعه است.')
            },
            {
                title: 'پروفایل کاربری',
                description: 'ویرایش اطلاعات حساب کاربری و مشخصات.',
                action: () => alert('بخش پروفایل کاربری در دست توسعه است.')
            }
        ];
        userPanelGrid.innerHTML = '';
        panels.forEach(panel => {
            const card = document.createElement('div');
            card.className = 'dashboard-card';
            card.innerHTML = `
                <h3>${panel.title}</h3>
                <p>${panel.description}</p>
                <button type="button">ورود</button>
            `;
            card.querySelector('button').addEventListener('click', panel.action);
            userPanelGrid.appendChild(card);
        });
    }
    function renderAdminPanel() {
        const adminPanelGrid = document.getElementById('admin-panel-grid');
        if (!adminPanelGrid) return;
        const panels = [
            {
                title: 'مدیریت موتورها',
                description: 'افزودن، ویرایش و حذف وسایل نقلیه.',
                action: () => alert('بخش مدیریت موتورها در دست توسعه است.')
            },
            {
                title: 'مدیریت رزروها',
                description: 'مشاهده و کنترل رزروهای انجام‌شده.',
                action: () => alert('بخش مدیریت رزروها در دست توسعه است.')
            },
            {
                title: 'مدیریت کاربران',
                description: 'مدیریت و سطح دسترسی کاربران سایت.',
                action: () => alert('بخش مدیریت کاربران در دست توسعه است.')
            }
        ];
        adminPanelGrid.innerHTML = '';
        panels.forEach(panel => {
            const card = document.createElement('div');
            card.className = 'dashboard-card';
            card.innerHTML = `
                <h3>${panel.title}</h3>
                <p>${panel.description}</p>
                <button type="button">مدیریت</button>
            `;
            card.querySelector('button').addEventListener('click', panel.action);
            adminPanelGrid.appendChild(card);
        });
    }
})();
