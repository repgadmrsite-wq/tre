// Custom JavaScript for Kishrun website

document.addEventListener("DOMContentLoaded", function() {

    // --- Theme Toggle Logic ---
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // On page load, check for saved theme
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
        }
        // On button click
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark');
            // Save preference
            if (document.body.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.removeItem('theme');
            }
        });
    }

    // --- Navbar Scroll Effect ---
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // --- Booking Form Validation ---
    const bookingForm = document.querySelector('.booking-form-container form');
    if(bookingForm) {
        bookingForm.addEventListener('submit', function(event) {
            event.preventDefault();
            let isValid = true;

            const inputs = this.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.value.trim() === '' || (input.tagName === 'SELECT' && input.value === 'چه نوع موتوری؟')) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (isValid) {
                const formContainer = document.querySelector('.booking-form-container');
                formContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">درخواست شما ثبت شد!</h4>
                        <p class="text-muted">همکاران ما به زودی برای تایید نهایی با شما تماس خواهند گرفت.</p>
                    </div>
                `;
            }
        });
    }

    // --- Persian Datepicker Initialization ---
    jalaliDatepicker.startWatch({});

    // --- Motorcycle Details Modal Logic ---
    const motorcycleData = {
        scooter: {
            title: 'اسکوتر برقی',
            description: 'یک انتخاب عالی برای گشت و گذارهای سریع و بی‌صدا در سطح شهر. با طراحی مدرن و باتری قدرتمند، این اسکوتر تجربه‌ای لذت‌بخش و دوست‌دار محیط زیست را برای شما فراهم می‌کند.',
            price: 'شروع از ساعتی ۹۰ تومان',
            img: 'https://images.unsplash.com/photo-1620759398110-42f213327350?q=80&w=800',
            specs: [
                '<strong>حداکثر سرعت:</strong> ۴۵ کیلومتر بر ساعت',
                '<strong>ظرفیت باتری:</strong> ۶۰ کیلومتر با یک بار شارژ',
                '<strong>نوع ترمز:</strong> دیسکی در هر دو چرخ',
                '<strong>وزن:</strong> ۷۰ کیلوگرم'
            ]
        },
        classic: {
            title: 'موتور کلاسیک',
            description: 'با این موتور کلاسیک و زیبا، در خیابان‌های کیش بدرخشید. طراحی نوستالژیک در کنار مهندسی مدرن، ترکیبی از زیبایی و قدرت را برای شما به ارمغان می‌آورد.',
            price: 'شروع از ساعتی ۱۲۰ تومان',
            img: 'https://images.pexels.com/photos/1715193/pexels-photo-1715193.jpeg?auto=compress&cs=tinysrgb&w=800',
            specs: [
                '<strong>حجم موتور:</strong> ۱۵۰ سی‌سی',
                '<strong>گیربکس:</strong> ۴ سرعته دستی',
                '<strong>سیستم سوخت:</strong> انژکتور',
                '<strong>ظرفیت باک:</strong> ۱۲ لیتر'
            ]
        },
        cruiser: {
            title: 'موتور کروزر',
            description: 'برای کسانی که به دنبال قدرت، راحتی و تجربه‌ای هیجان‌انگیز در جاده‌های ساحلی هستند. این موتور کروزر با صندلی راحت و قدرت بالا، بهترین انتخاب برای سفرهای طولانی‌تر است.',
            price: 'شروع از ساعتی ۱۵۰ تومان',
            img: 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=800',
            specs: [
                '<strong>حجم موتور:</strong> ۲۵۰ سی‌سی',
                '<strong>گیربکس:</strong> ۵ سرعته دستی',
                '<strong>سیستم خنک‌کننده:</strong> رادیاتور روغن',
                '<strong>وزن:</strong> ۱۸۰ کیلوگرم'
            ]
        }
    };

    const motorcycleModal = new bootstrap.Modal(document.getElementById('motorcycleModal'));
    const modalTitle = document.getElementById('motorcycleModalLabel');
    const modalImg = document.getElementById('modal-img');
    const modalDesc = document.getElementById('modal-desc');
    const modalSpecs = document.getElementById('modal-specs');
    const modalPrice = document.getElementById('modal-price');

    document.querySelectorAll('.motorcycle-card').forEach(card => {
        card.addEventListener('click', function(event) {
            event.preventDefault();
            const motorcycleId = this.dataset.id;
            const data = motorcycleData[motorcycleId];

            if (data) {
                modalTitle.textContent = data.title;
                modalImg.src = data.img;
                modalDesc.textContent = data.description;
                modalPrice.innerHTML = data.price;

                modalSpecs.innerHTML = ''; // Clear previous specs
                data.specs.forEach(spec => {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="bi bi-check-circle-fill text-primary me-2"></i>${spec}`;
                    modalSpecs.appendChild(li);
                });

                motorcycleModal.show();
            }
        });
    });

    // --- Scroll-Reveal Animation Logic ---
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const fadeElms = document.querySelectorAll('.fade-in');
    fadeElms.forEach(el => observer.observe(el));

    // --- Interactive Map Logic ---
    const mapPoints = document.querySelectorAll('.map-point');
    const tooltip = document.getElementById('map-tooltip');
    if (tooltip) {
        mapPoints.forEach(point => {
            point.addEventListener('mousemove', function(e) {
                tooltip.textContent = this.dataset.name;
                tooltip.style.display = 'block';
                tooltip.style.left = e.pageX + 10 + 'px';
                tooltip.style.top = e.pageY + 10 + 'px';
            });
            point.addEventListener('mouseleave', function() {
                tooltip.style.display = 'none';
            });
        });
    }

});

// --- GOOGLE MAPS INITIALIZATION ---
function initMap() {
    const kishCoords = { lat: 26.5385, lng: 53.9801 };
    const locations = [
        { lat: 26.558, lng: 54.029, name: 'اسکله بزرگ تفریحی' },
        { lat: 26.513, lng: 53.883, name: 'کشتی یونانی' },
        { lat: 26.560, lng: 54.045, name: 'پلاژ بانوان' },
        { lat: 26.541, lng: 53.971, name: 'شهر زیرزمینی کاریز' }
    ];

    // Custom Map Style (Minimalist Dark)
    const mapStyle = [
        { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
        { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
        { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
        {
            featureType: "administrative.locality",
            elementType: "labels.text.fill",
            stylers: [{ color: "#d59563" }],
        },
        {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{ color: "#d59563" }],
        },
        {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{ color: "#263c3f" }],
        },
        {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{ color: "#6b9a76" }],
        },
        {
            featureType: "road",
            elementType: "geometry",
            stylers: [{ color: "#38414e" }],
        },
        {
            featureType: "road",
            elementType: "geometry.stroke",
            stylers: [{ color: "#212a37" }],
        },
        {
            featureType: "road",
            elementType: "labels.text.fill",
            stylers: [{ color: "#9ca5b3" }],
        },
        {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{ color: "#746855" }],
        },
        {
            featureType: "road.highway",
            elementType: "geometry.stroke",
            stylers: [{ color: "#1f2835" }],
        },
        {
            featureType: "road.highway",
            elementType: "labels.text.fill",
            stylers: [{ color: "#f3d19c" }],
        },
        {
            featureType: "transit",
            elementType: "geometry",
            stylers: [{ color: "#2f3948" }],
        },
        {
            featureType: "transit.station",
            elementType: "labels.text.fill",
            stylers: [{ color: "#d59563" }],
        },
        {
            featureType: "water",
            elementType: "geometry",
            stylers: [{ color: "#17263c" }],
        },
        {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{ color: "#515c6d" }],
        },
        {
            featureType: "water",
            elementType: "labels.text.stroke",
            stylers: [{ color: "#17263c" }],
        },
    ];

    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: kishCoords,
        styles: mapStyle,
        disableDefaultUI: true,
    });

    locations.forEach(location => {
        new google.maps.Marker({
            position: location,
            map: map,
            title: location.name,
        });
    });
}
