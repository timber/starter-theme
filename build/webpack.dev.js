const path = require('path');
const merge = require('webpack-merge');
const mime = require('mime');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const common = require('./webpack.common');
const paths = require('./parts/webpack.paths');
const browserSyncConfig = require('./parts/webpack.browsersync');
const devServerConfig = require('./parts/webpack.devserver');

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
    new BrowserSyncPlugin(...browserSyncConfig()),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        '**/*',
        path.join(paths.build, 'styles/**/*.css')
      ]
    })
  ],
  devServer: devServerConfig()
});