const merge = require('webpack-merge');
const common = require('./webpack.common');
const browserSync = require('./parts/webpack.browsersync');
const cleanWebpack = require('./parts/webpack.clean');
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
    browserSync(),
    cleanWebpack()
  ],
  devServer: devServerConfig()
});