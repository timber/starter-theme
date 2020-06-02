const path = require('path');

const root = path.resolve(__dirname, '../..');
const src = path.join(__dirname, 'src');
const build = path.join(root, 'static');
const output = path.join(build, 'scripts');

module.exports = { root, src, build, output };