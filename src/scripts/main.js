console.log('hello from site.js!!');

($ => {
  console.log($, 'jQuery is ready!');
  console.log($('body').attr('class'));
})( jQuery );