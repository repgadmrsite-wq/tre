// Custom JavaScript for Kishrun website

document.addEventListener("DOMContentLoaded", function() {

    // --- Scroll-Reveal Animation Logic ---
    const observerOptions = {
        root: null, // observes intersections relative to the viewport
        rootMargin: '0px',
        threshold: 0.1 // trigger when 10% of the element is visible
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            // If the element is intersecting the viewport
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                // Stop observing the element once it's visible
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Select all elements you want to fade in
    const fadeElms = document.querySelectorAll('.fade-in-section');
    fadeElms.forEach(el => observer.observe(el));

});
