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

const swiper = fs.readFileSync(path.join('js','swiper.js'),'utf8');
const scripts = fs.readFileSync(path.join('js','scripts.js'),'utf8');
const bundledJs = swiper + '\n' + minifyJS(scripts);
fs.writeFileSync(path.join('dist','scripts.min.js'), bundledJs);
