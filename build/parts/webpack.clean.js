const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const paths = require('./webpack.paths');

module.exports = () => {
  return new CleanWebpackPlugin({
    cleanOnceBeforeBuildPatterns: [
      '**/*',
      path.join(paths.build, 'styles/**/*.css')
    ]
  });
}