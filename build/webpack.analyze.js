require('dotenv').config();
const merge = require('webpack-merge');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const SpeedMeasurePlugin = require('speed-measure-webpack-plugin');
const common = require('./webpack.common');
const dev = require('./webpack.dev');
const prod = require('./webpack.prod');
const cleanWebpack = require('./parts/webpack.clean');
const smp = new SpeedMeasurePlugin();

const {
  ANALYZE,
  BUNDLE_ANALYZER_PORT
} = process.env;

const configs = [common];

// analyzes production config by default
if (ANALYZE === 'development') {
  configs.push(dev);
} else {
  configs.push(prod, { plugins: [cleanWebpack()] });
}

configs.push({
  plugins: [
    new BundleAnalyzerPlugin({
      analyzerPort: BUNDLE_ANALYZER_PORT || 8887,
      openAnalyzer: false
    })
  ]
});

const config = smp.wrap(merge(...configs));

module.exports = config;