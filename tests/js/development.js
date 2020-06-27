const http = require('http');
const webpack = require('webpack');
const WebpackDevServer = require('webpack-dev-server');
const browserSync = require('browser-sync');
const config = require('./webpack/webpack.dev.test');
let devServer, browserSyncInstance;

const compiler = webpack(config);

describe('Development', function() {

  before(function(done) {
    this.timeout(0);
    
    devServer = new WebpackDevServer(compiler, config.devServer).listen(config.devServer.port);
    browserSyncInstance = browserSync.get('bs-webpack-plugin').instance;
    
    // wait for both WDS and BS to be running
    Promise
      .all([
        new Promise(resolve => devServer.on('listening', resolve)),
        new Promise(resolve => browserSyncInstance.emitter.on('init', resolve))
      ])
      .then(() => done())
      .catch(done);
  });

  after(function(done) {
    devServer.close(done);
  });

  it('should redirect to the asset being served via WDS', done => {
    http.get('http://localhost:3000/wp-content/themes/timber-copy/static/scripts/site.js', res => {
      expect(res.statusCode).to.match(/^3[\d]{2}$/);
      expect(res.headers.location).to.have.string(`localhost:${config.devServer.port}`);
      done();
    });
  });
});