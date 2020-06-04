require('dotenv').config();
const merge = require('webpack-merge');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const common = require('./webpack.common');
const dev = require('./webpack.dev');
const prod = require('./webpack.prod');

const configs = [common];

configs.push(process.env.ANALYZE === 'development' ? dev : prod);

configs.push({
  plugins: [
    new BundleAnalyzerPlugin({
      analyzerPort: 8887,
      openAnalyzer: false
    })
  ]
});

module.exports = merge(...configs);