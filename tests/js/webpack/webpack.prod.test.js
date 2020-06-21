const merge = require('webpack-merge');
const prod = require('../../../build/webpack.prod');

delete prod.entry;

module.exports = merge(prod, {
  entry: {
    site: './tests/js/webpack/src/main.js'
  }
});