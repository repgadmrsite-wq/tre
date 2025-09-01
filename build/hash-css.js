const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const cssPath = path.join(__dirname, '../dist/style.min.css');
if (!fs.existsSync(cssPath)) {
  console.error('CSS file not found at', cssPath);
  process.exit(1);
}
const css = fs.readFileSync(cssPath);
const hash = crypto.createHash('md5').update(css).digest('hex').slice(0,10);
const newName = `style.${hash}.min.css`;
fs.renameSync(cssPath, path.join(__dirname, '../dist/', newName));
console.log(`Generated /dist/${newName}`);
