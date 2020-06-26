const CopyPlugin = require('copy-webpack-plugin');
const copyPatterns = [];

module.exports = (patterns = copyPatterns) => {
  return new CopyPlugin({
    patterns
  });
};