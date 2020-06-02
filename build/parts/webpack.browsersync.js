module.exports = () => ([{
  open: false,
  host: 'localhost',
  proxy: {
    target: 'http://localhost:8888',
    proxyReq: [ proxyReq => proxyReq.setHeader('X-Development', '1') ]
  },
  reloadDebounce: 2000,
  files: [
    '*.php'
  ]
}, {
  reload: false
}]);