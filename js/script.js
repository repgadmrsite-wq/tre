// Custom JavaScript for Kishrun website

document.addEventListener("DOMContentLoaded", function() {

    // --- Theme Toggle Logic ---
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Function to apply the saved theme or default to light
    const applyTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark');
        } else {
            body.classList.remove('dark');
        }
    };

    // Toggle theme on button click
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark');
        if (body.classList.contains('dark')) {
            localStorage.setItem('theme', 'dark');
        } else {
            localStorage.setItem('theme', 'light');
        }
    });

    // Apply theme on initial load
    applyTheme();


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
    const fadeElms = document.querySelectorAll('.fade-in-section');
    fadeElms.forEach(el => observer.observe(el));


    // --- Persian Datepicker Initialization ---
    jalaliDatepicker.startWatch({
        // Optional: You can add configurations here if needed
        // For example:
        // container: 'body',
        // selector: '[data-jdp]',
        // minDate: 'today',
        // maxDate: 'attr'
    });

});
