const path = require('path');
const merge = require('webpack-merge');
const prod = require('../../../build/webpack.prod');
const copyWebpack = require('../../../build/parts/webpack.copy');
const paths = require('../../../build/parts/webpack.paths');

delete prod.entry;

module.exports = merge(prod, {
  entry: {
    site: './tests/js/webpack/src/main.js'
  },
  plugins: [
    copyWebpack([
      {
        from: 'images/**',
        to: paths.build,
        context: path.join(__dirname, 'src')
      }
    ])
  ]
});