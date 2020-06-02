const paths = require('./parts/webpack.paths');

module.exports = {
  entry: {
    site: './src/main.js'
  },
  output: {
    path: paths.output,
    filename: '[name].js'
  }
};