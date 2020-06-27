const merge = require('webpack-merge');
const dev = require('../../../build/webpack.dev');
const overrides = require('./webpack.overrides.test');

const config = {
  devServer: {
    publicPath: '/'
  }
};

module.exports = merge(dev, overrides, config);
