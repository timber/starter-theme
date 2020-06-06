require('dotenv').config();
const merge = require('webpack-merge');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const common = require('./webpack.common');
const dev = require('./webpack.dev');
const prod = require('./webpack.prod');
const cleanWebpack = require('./parts/webpack.clean');

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

module.exports = merge(...configs);