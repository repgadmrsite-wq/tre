(function(global){
  function toggleMenu(navbar, button){
    const active = navbar.classList.toggle('active');
    if(button) button.setAttribute('aria-expanded', active);
    return active;
  }

  if(typeof module !== 'undefined' && module.exports){
    module.exports = { toggleMenu };
  } else {
    global.toggleMenu = toggleMenu;
  }
})(typeof window !== 'undefined' ? window : globalThis);
