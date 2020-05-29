const path = require('path');
const merge = require('webpack-merge');
const mime = require('mime');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const common = require('./webpack.common');

module.exports = merge(common, {
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
    new BrowserSyncPlugin({
      open: false,
      host: 'localhost',
      proxy: 'http://localhost:8888',
      reloadDebounce: 2000,
      files: [
        '*.php'
      ]
    }, {
      reload: false
    }),
    new BundleAnalyzerPlugin({
      analyzerPort: 8887,
      openAnalyzer: false
    })
  ],
  devServer: {
    before: app => {
      // Intercept all requests for static assets that aren't js files and send a (most-likely) empty file to satisfy PHP
      app.get(/\.(?!js).*$/, (req, res) => {
        const mimeType = mime.getType(req.url.split('?')[0]);
        res.setHeader('Content-Type', mimeType);
        res.send('');
      });
    },
    compress: true,
    hot: true,
    port: 9000
  }
});