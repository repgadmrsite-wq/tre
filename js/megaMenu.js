(function(global){
  function computeMegaMenuLeft(triggerRect, menuWidth){
    let l = triggerRect.left + triggerRect.width + menuWidth * 2;
    return l < 1040 ? 1040 : l;
  }

  if(typeof module !== 'undefined' && module.exports){
    module.exports = { computeMegaMenuLeft };
  } else {
    global.computeMegaMenuLeft = computeMegaMenuLeft;
  }
})(typeof window !== 'undefined' ? window : globalThis);
