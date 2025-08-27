const heroBg = document.querySelector('.hero-bg');
window.addEventListener('scroll', () => {
  const offset = window.pageYOffset;
  heroBg.style.transform = `translateY(${offset * 0.3}px)`;
});
