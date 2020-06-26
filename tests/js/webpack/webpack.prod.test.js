const path = require('path');
const merge = require('webpack-merge');
const prod = require('../../../build/webpack.prod');
const copyWebpack = require('../../../build/parts/webpack.copy');
const cleanWebpack = require('../../../build/parts/webpack.clean');
const paths = require('../../../build/parts/webpack.paths');
const overrides = require('./webpack.overrides.test');

delete prod.entry;

module.exports = merge(prod, overrides, {
  plugins: [
    cleanWebpack(),
    copyWebpack([
      {
        from: 'images/**',
        to: paths.build,
        context: path.join(__dirname, 'src')
      }
    ])
  ]
});