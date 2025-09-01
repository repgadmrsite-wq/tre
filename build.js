const fs = require('fs');
const path = require('path');

function minifyCSS(input){
  return input
    .replace(/\/\*[\s\S]*?\*\//g, '')
    .replace(/\s+/g, ' ')
    .replace(/\s*([{}:;,])\s*/g, '$1')
    .trim();
}

function minifyJS(input){
  return input
    .replace(/\/\*[\s\S]*?\*\//g, '')
    .replace(/(^|\n)\s*\/\/.*(?=\n)/g, '')
    .replace(/\s+/g, ' ')
    .replace(/\s*([{}();,:])\s*/g, '$1')
    .trim();
}

fs.mkdirSync('dist', {recursive: true});

const css = fs.readFileSync('style.css','utf8');
fs.writeFileSync(path.join('dist','style.min.css'), minifyCSS(css));

const jsDir = 'js';
const swiper = fs.readFileSync(path.join(jsDir,'swiper.js'),'utf8');

const moduleFiles = fs.readdirSync(jsDir)
  .filter(f => f.endsWith('.js') && !['swiper.js','scripts.js'].includes(f))
  .sort();

let bundledJs = swiper + '\n';

moduleFiles.forEach(file => {
  const content = fs.readFileSync(path.join(jsDir, file),'utf8');
  bundledJs += minifyJS(content) + '\n';
});

const scripts = fs.readFileSync(path.join(jsDir,'scripts.js'),'utf8');
bundledJs += minifyJS(scripts);
fs.writeFileSync(path.join('dist','scripts.min.js'), bundledJs);
