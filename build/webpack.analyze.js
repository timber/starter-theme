require('dotenv').config();
const merge = require('webpack-merge');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const SpeedMeasurePlugin = require('speed-measure-webpack-plugin');
const common = require('./webpack.common');
const dev = require('./webpack.dev');
const prod = require('./webpack.prod');
const cleanWebpack = require('./parts/webpack.clean');
const smp = new SpeedMeasurePlugin();

const configs = [common];

// analyzes production config by default
if (!process.env.ANALYZE || process.env.ANALYZE === 'production') {
  configs.push(prod, { plugins: [cleanWebpack()] });
}

if (process.env.ANALYZE === 'development') {
  configs.push(dev);
}

configs.push({
  plugins: [
    new BundleAnalyzerPlugin({
      analyzerPort: 8887,
      openAnalyzer: false
    })
  ]
});

const config = smp.wrap(merge(...configs));

module.exports = config;