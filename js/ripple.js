const buttons = document.querySelectorAll('.btn, .btn-buy, .download-btn, .cta-btn, .tab-buttons button');

buttons.forEach(btn => {
  btn.addEventListener('click', function (e) {
    const rect = this.getBoundingClientRect();
    const circle = document.createElement('span');
    const diameter = Math.max(rect.width, rect.height);
    const radius = diameter / 2;
    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${e.clientX - rect.left - radius}px`;
    circle.style.top = `${e.clientY - rect.top - radius}px`;
    circle.classList.add('ripple');
    const ripple = this.getElementsByClassName('ripple')[0];
    if (ripple) ripple.remove();
    this.appendChild(circle);
  });
});
