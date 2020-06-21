const fs = require('fs');
const path = require('path');
const paths = require('../../build/parts/webpack.paths');
const { exec } = require('child_process');
const { expect } = require('chai');

const outputCSSPath = path.join(paths.build, 'styles/site.css');

describe('Production', function() {

  before(function(done) {
    this.timeout(0);
  
    exec('webpack --config tests/js/webpack/webpack.prod.test.js', error => {
      if (error) {
        console.log(error.stack);
        console.log('Error code: '+error.code);
        console.log('Signal received: '+error.signal);
        throw new Error();
      }
      done();
    });
  });

  context('Styles', function() {

    it('should separate css from the bundle', done => {
      expect(() => {
        fs.access(
          outputCSSPath,
          fs.F_OK,
          err => { if (err) throw new Error(); });
      }).to.not.throw();
      done();
    });

    it('should output a minified css file', done => {
      fs.readFile(outputCSSPath, 'utf8', (err, data) => {
        expect(data).to.equal('body{background:red}');
        done();
      });
    });
  });
});
