const test = require('node:test');
const assert = require('node:assert/strict');
const {toggleMenu} = require('../js/menu.js');

test('toggleMenu toggles class and aria-expanded', () => {
  const navbar = {
    classList: {
      _active: false,
      toggle(){ this._active = !this._active; return this._active; },
      contains(){ return this._active; }
    }
  };
  const button = {
    attr: null,
    setAttribute(name, value){ if(name === 'aria-expanded') this.attr = value; }
  };
  let state = toggleMenu(navbar, button);
  assert.equal(state, true);
  assert.equal(button.attr, true);
  state = toggleMenu(navbar, button);
  assert.equal(state, false);
  assert.equal(button.attr, false);
});
