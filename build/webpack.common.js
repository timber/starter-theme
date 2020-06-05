const paths = require('./parts/webpack.paths');

module.exports = {
  entry: {
    site: './src/main.js',
    custom: './src/custom.js',
    arbitrary: './src/arbitrary.js'
  },
  output: {
    path: paths.output,
    filename: '[name].js'
  },
  externals: {
    jquery: 'jQuery'
  }
};