module.exports = () => ({
  before: (app, server, compiler) => {
    
    compiler.hooks.shouldEmit.tap('interceptor', compilation => {
      compilation.namedChunks.forEach(chunk => {
        const emittedFiles = chunk.files;

        app.get(/\.js$/, (req, res, next) => {
          const file = path.basename(req.url);

          // check if the any of the files emitted by webpack are being requested
          if (emittedFiles.indexOf(file) !== -1) {
            next();
          }
          
          // if not, satisfy PHP
          res.setHeader('Content-Type', 'text/javascript');
          res.send(`console.log('Dummy "${file}" loaded!')`);
        });
      });
      
      return true;
    });

    // Intercept all requests for static assets that aren't js files output by WDS and send a (most-likely) empty response to satisfy PHP
    app.get(/^((?!js).)*$/, (req, res) => {
      const mimeType = mime.getType(req.url.split('?')[0]);
      res.setHeader('Content-Type', mimeType);
      res.send('');
    });
  },
  overlay: true,
  compress: true,
  hot: true,
  port: 9000
});