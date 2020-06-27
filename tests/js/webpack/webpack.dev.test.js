const merge = require('webpack-merge');
const dev = require('../../../build/webpack.dev');
const overrides = require('./webpack.overrides.test');
const stats = 'errors-only';

const config = {
  stats,
  devServer: {
    stats,
    noInfo: true,
    publicPath: '/'
  }
};

module.exports = merge(dev, overrides, config);
