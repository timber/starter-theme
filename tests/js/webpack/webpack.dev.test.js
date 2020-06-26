const merge = require('webpack-merge');
const dev = require('../../../build/webpack.dev');
const overrides = require('./webpack.overrides.test');

module.exports = merge(dev, overrides, {
  
});