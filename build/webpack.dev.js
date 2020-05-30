require('dotenv').config();
const path = require('path');
const merge = require('webpack-merge');
const mime = require('mime');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const common = require('./webpack.common');
const { browserSyncOptions } = require('./webpack.parts');

const config = {
  mode: 'development',
  devtool: 'inline-source-map',
  watch: true,
  module: {
    rules: [
      {
        test: /\.s[ac]ss$/i,
        use: [
          'style-loader',
          {
            loader: 'css-loader',
            options: { sourceMap: true }
          },
          {
            loader: 'sass-loader',
            options: { sourceMap: true }
          }
        ]
      }
    ]
  },
  plugins: [
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        '**/*',
        path.resolve(__dirname, '../static/styles/**/*.css')
      ]
    }),
    new BundleAnalyzerPlugin({
      analyzerPort: 8887,
      openAnalyzer: false
    })
  ],
  devServer: {
    before: app => {
      // Intercept all requests for static assets that aren't js files and send a (most-likely) empty file to satisfy PHP
      app.get(/^((?!js).)*$/, (req, res) => {
        const mimeType = mime.getType(req.url.split('?')[0]);
        res.setHeader('Content-Type', mimeType);
        res.send('');
      });
    },
    overlay: true,
    compress: true,
    hot: true,
    port: 9000
  }
};

if (process.env.HTTPS) {
  config.devServer.https = true;
  browserSyncOptions[0].https = true;
  browserSyncOptions[0].proxy.proxyReq.push(proxyReq => {
    // apply a custom header to all requests that occur from the wp-content/themes directory and that are not JS assets
    if (/wp-content\/themes/.test(proxyReq.path)) {
      proxyReq.setHeader('X-Development', '1');
    }
  });
}

config.plugins.push(new BrowserSyncPlugin(...browserSyncOptions));

module.exports = merge(common, config);