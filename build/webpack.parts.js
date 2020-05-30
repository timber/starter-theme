module.exports = {
  browserSyncOptions: [{
    open: false,
    host: 'localhost',
    proxy: {
      target: 'http://localhost:8888',
      proxyReq: [],
    },
    reloadDebounce: 2000,
    files: [
      '*.php'
    ]
  }, {
    reload: false
  }]
}