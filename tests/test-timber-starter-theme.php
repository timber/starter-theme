<?php

class TestTimberStarterTheme extends \WorDBless\BaseTestCase {

	function setUp() {
		self::_setupStarterTheme();
		switch_theme( basename( dirname( __DIR__ ) ) . '/theme' );
		require_once dirname( __DIR__ ) . '/theme/functions.php';
		// WorDBless includes wp-settings.php
		do_action( 'after_setup_theme' );
	}

	function tearDown() {
		switch_theme('twentythirteen');
	}

	function testTimberExists() {
		$context = Timber::context();
		$this->assertTrue(is_array($context));
	}

	function testFunctionsPHP() {
		$context = Timber::context();
		$this->assertEquals('StarterSite', get_class($context['site']));
		$this->assertTrue(current_theme_supports('post-thumbnails'));
		$this->assertEquals('bar', $context['foo']);
	}

	function testLoading() {
		$str = Timber::compile('tease.twig');
		$this->assertStringStartsWith('<article class="tease tease-" id="tease-">', $str);
		$this->assertStringEndsWith('</article>', $str);
	}

	/**
	 * Helper test to output current twig version
	 */
	function testTwigVersion() {
		$str = Timber::compile_string("{{ constant('Twig_Environment::VERSION') }}");
		//error_log('Twig version = '.$str);
	}

	function testTwigFilter() {
		$str = Timber::compile_string('{{ "foo" | myfoo }}');
		$this->assertEquals('foo bar!', $str);
	}

	static function _setupStarterTheme(){
		$baseName = basename( dirname( __DIR__ ) );
		$src  = realpath( dirname( dirname( __DIR__ ) ) . '/' . $baseName );
		$dest = WP_CONTENT_DIR . '/themes/' . $baseName;
		if ( is_dir($src) && ! file_exists($dest) ) {
			symlink($src, $dest);
		}
	}
}
