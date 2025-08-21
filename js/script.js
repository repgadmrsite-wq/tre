// Custom JavaScript for Kishrun website

document.addEventListener("DOMContentLoaded", function() {

    // --- Navbar Scroll Effect ---
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // --- Persian Datepicker Initialization ---
    jalaliDatepicker.startWatch({});

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

});
