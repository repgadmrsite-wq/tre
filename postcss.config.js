const purgecss = require('@fullhuman/postcss-purgecss');

module.exports = {
  plugins: [
    require('autoprefixer'),
    ...(process.env.NODE_ENV === 'production'
      ? [
          purgecss({
            content: ['./**/*.php', './js/**/*.js'],
            safelist: ['dark', /^swiper/, /^icon-/, /^blink/],
          }),
        ]
      : []),
    require('cssnano')({
      preset: 'default',
    }),
  ],
};
