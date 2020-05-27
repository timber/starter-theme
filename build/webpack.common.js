const path = require('path');

module.exports = {
  entry: {
    site: './src/main.js'
  },
  output: {
    path: path.resolve(__dirname, '..', 'static', 'scripts'),
    filename: '[name].js'
  }
};