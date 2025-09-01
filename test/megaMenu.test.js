const test = require('node:test');
const assert = require('node:assert/strict');
const {computeMegaMenuLeft} = require('../js/megaMenu.js');

test('computeMegaMenuLeft clamps to minimum', () => {
  const rect = {left: 0, width: 100};
  const result = computeMegaMenuLeft(rect, 100);
  assert.equal(result, 1040);
});

test('computeMegaMenuLeft calculates offset', () => {
  const rect = {left: 1200, width: 100};
  const result = computeMegaMenuLeft(rect, 50);
  assert.equal(result, 1200 + 100 + 50 * 2);
});
