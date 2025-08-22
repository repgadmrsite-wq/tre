document.addEventListener('DOMContentLoaded', () => {
    // --- Multi-Step Booking Form Logic ---
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        const steps = Array.from(bookingForm.querySelectorAll('.form-step'));
        const stepIndicators = Array.from(document.querySelectorAll('.step-indicator .step'));
        const nextButtons = Array.from(bookingForm.querySelectorAll('.next-step'));
        const prevButtons = Array.from(bookingForm.querySelectorAll('.prev-step'));
        let currentStep = 0;

        const motorcycleOptions = [
            { id: 1, name: 'اسکوتر وسپا', price: '۱۸۰,۰۰۰ تومان / روز', image: 'https://images.unsplash.com/photo-1626247023795-88408819d54d?q=80&w=1974' },
            { id: 2, name: 'موتور کروزر', price: '۲۵۰,۰۰۰ تومان / روز', image: 'https://images.unsplash.com/photo-1593352222537-a515aed534a2?q=80&w=1964' },
            { id: 3, name: 'موتور برقی', price: '۱۵۰,۰۰۰ تومان / روز', image: 'https://images.unsplash.com/photo-1625043834839-a8a2c1a4b330?q=80&w=1964' }
        ];

        const motorcycleOptionsContainer = document.getElementById('motorcycle-options');
        if (motorcycleOptionsContainer) {
            motorcycleOptionsContainer.innerHTML = motorcycleOptions.map(m => `
                <div class="col-md-4 mb-3">
                    <div class="card h-100 motorcycle-card" data-id="${m.id}">
                        <img src="${m.image}" class="card-img-top" alt="${m.name}" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">${m.name}</h5>
                            <p class="card-text text-muted">${m.price}</p>
                        </div>
                    </div>
                </div>
            `).join('');

            motorcycleOptionsContainer.addEventListener('click', e => {
                const card = e.target.closest('.motorcycle-card');
                if (card) {
                    // Remove 'selected' from all cards
                    motorcycleOptionsContainer.querySelectorAll('.card').forEach(c => c.classList.remove('selected'));
                    // Add 'selected' to the clicked card
                    card.classList.add('selected');
                }
            });
        }

        const updateStep = (newStep) => {
            steps[currentStep].classList.remove('active');
            stepIndicators[currentStep].classList.remove('active');

            currentStep = newStep;

            steps[currentStep].classList.add('active');
            stepIndicators[currentStep].classList.add('active');
        };

        nextButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Basic validation
                if (currentStep === 0) { // Step 1: Motorcycle selection
                    if (!motorcycleOptionsContainer.querySelector('.card.selected')) {
                        alert('لطفا یک موتور را انتخاب کنید.');
                        return;
                    }
                }
                if (currentStep < steps.length - 1) {
                    updateStep(currentStep + 1);
                }
            });
        });

        prevButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (currentStep > 0) {
                    updateStep(currentStep - 1);
                }
            });
        });

        bookingForm.addEventListener('submit', e => {
            e.preventDefault();
            // Final validation and submission logic
            const phoneNumber = document.getElementById('phone-number').value;
            if (!phoneNumber) {
                 alert('لطفا شماره موبایل خود را وارد کنید.');
                 return;
            }
            alert('درخواست رزرو شما با موفقیت ثبت شد. همکاران ما به زودی با شما تماس خواهند گرفت.');
            bookingForm.reset();
            updateStep(0);
            motorcycleOptionsContainer.querySelectorAll('.card').forEach(c => c.classList.remove('selected'));
        });
    }

    // --- User Authentication Logic ---

    // Registration Form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', e => {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                alert('رمزهای عبور یکسان نیستند.');
                return;
            }

            // In a real app, you'd send this to a server.
            // For now, we use localStorage.
            const users = JSON.parse(localStorage.getItem('users')) || [];
            const userExists = users.some(user => user.email === email);

            if (userExists) {
                alert('این ایمیل قبلا ثبت‌نام کرده است.');
                return;
            }

            users.push({ fullName, email, password });
            localStorage.setItem('users', JSON.stringify(users));
            alert('ثبت‌نام با موفقیت انجام شد! اکنون می‌توانید وارد شوید.');
            window.location.href = 'login.html';
        });
    }

    // Login Form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
         loginForm.addEventListener('submit', e => {
            e.preventDefault();
            const email = document.getElementById('floatingInput').value;
            const password = document.getElementById('password').value;

            const users = JSON.parse(localStorage.getItem('users')) || [];
            const user = users.find(user => user.email === email && user.password === password);

            if (user) {
                // Simulate login session
                localStorage.setItem('loggedInUser', JSON.stringify(user));
                alert('شما با موفقیت وارد شدید.');
                window.location.href = 'dashboard.html';
            } else {
                alert('ایمیل یا رمز عبور نامعتبر است.');
            }
        });
    }

    // Check login status and update UI
    const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
    const navLinks = document.querySelector('.navbar-nav');
    if (loggedInUser) {
        if (navLinks) {
            navLinks.innerHTML = `
                <li class="nav-item"><a class="nav-link" href="index.html">خانه</a></li>
                <li class="nav-item"><a class="nav-link" href="dashboard.html">داشبورد</a></li>
                <li class="nav-item"><a class="nav-link" href="#" id="logout-link">خروج</a></li>
            `;
            const logoutLink = document.getElementById('logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', e => {
                    e.preventDefault();
                    localStorage.removeItem('loggedInUser');
                    alert('شما از حساب خود خارج شدید.');
                    window.location.href = 'index.html';
                });
            }
        }
    } else {
         if (navLinks) {
            navLinks.innerHTML = `
                <li class="nav-item"><a class="nav-link active" href="index.html">خانه</a></li>
                <li class="nav-item"><a class="nav-link" href="login.html">ورود / ثبت‌نام</a></li>
            `;
        }
    }

    // --- Dashboard Logic ---
    if (window.location.pathname.includes('dashboard.html')) {
        const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
        if (!loggedInUser) {
            // If no user is logged in, redirect to login page
            window.location.href = 'login.html';
            return; // Stop further execution
        }

        // --- Populate user-specific data ---
        const userWelcome = document.getElementById('user-welcome');
        if (userWelcome) {
            userWelcome.textContent = `خوش آمدید، ${loggedInUser.fullName}`;
        }

        // --- Populate with Mock Data ---
        const mockBookings = [
            { motorcycle: 'اسکوتر وسپا', startDate: '۱۴۰۳/۰۵/۰۱', endDate: '۱۴۰۳/۰۵/۰۳', status: 'تکمیل شده', price: '۳۶۰,۰۰۰ تومان' },
            { motorcycle: 'موتور کروزر', startDate: '۱۴۰۳/۰۶/۱۰', endDate: '۱۴۰۳/۰۶/۱۱', status: 'در انتظار', price: '۲۵۰,۰۰۰ تومان' },
            { motorcycle: 'موتور برقی', startDate: '۱۴۰۳/۰۴/۱۵', endDate: '۱۴۰۳/۰۴/۱۵', status: 'لغو شده', price: '۱۵۰,۰۰۰ تومان' },
            { motorcycle: 'موتور کروزر', startDate: '۱۴۰۳/۰۳/۲۰', endDate: '۱۴۰۳/۰۳/۲۲', status: 'تکمیل شده', price: '۵۰۰,۰۰۰ تومان' }
        ];

        const totalBookings = document.getElementById('stats-total-bookings');
        const completedTrips = document.getElementById('stats-completed-trips');
        const totalSpent = document.getElementById('stats-total-spent');
        const bookingsTableBody = document.getElementById('bookings-table-body');

        if (totalBookings) totalBookings.textContent = mockBookings.length;
        if (completedTrips) completedTrips.textContent = mockBookings.filter(b => b.status === 'تکمیل شده').length;
        if (totalSpent) {
            const total = mockBookings
                .filter(b => b.status === 'تکمیل شده')
                .reduce((sum, b) => sum + parseInt(b.price.replace(/,/g, '')), 0);
            totalSpent.textContent = `${total.toLocaleString('fa-IR')} تومان`;
        }

        if (bookingsTableBody) {
            bookingsTableBody.innerHTML = mockBookings.map(b => `
                <tr>
                    <td>${b.motorcycle}</td>
                    <td>${b.startDate}</td>
                    <td>${b.endDate}</td>
                    <td><span class="badge ${getStatusBadgeClass(b.status)}">${b.status}</span></td>
                    <td class="text-end">${b.price}</td>
                </tr>
            `).join('');
        }
    }

    function getStatusBadgeClass(status) {
        switch (status) {
            case 'تکمیل شده': return 'bg-success-subtle text-success-emphasis';
            case 'در انتظار': return 'bg-warning-subtle text-warning-emphasis';
            case 'لغو شده': return 'bg-danger-subtle text-danger-emphasis';
            default: return 'bg-secondary-subtle text-secondary-emphasis';
        }
    }
});
