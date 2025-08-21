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
    // Initialize datepicker on all elements with data-jdp attribute
    jalaliDatepicker.startWatch({});

});
