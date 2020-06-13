const merge = require('webpack-merge');
const common = require('./webpack.common');
const browserSync = require('./parts/webpack.browsersync');
const cleanWebpack = require('./parts/webpack.clean');
const devServer = require('./parts/webpack.devserver');
const loaders = require('./parts/webpack.loaders');

module.exports = merge(common, {
  mode: 'development',
  devtool: 'inline-source-map',
  watch: true,
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: [
          loaders.eslint({
            configFile: 'build/eslint/dev.js'
          })
        ]
      },
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
  devServer
});