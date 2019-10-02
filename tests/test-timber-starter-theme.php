<?php

	class TestTimberStarterTheme extends WP_UnitTestCase {

		function setUp() {
			self::_setupStarterTheme();
			switch_theme( basename( dirname( dirname( __FILE__ ) ) ) );
			require_once(__DIR__.'/../functions.php');
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
			$dest = WP_CONTENT_DIR . '/themes/' . basename( dirname( dirname( __FILE__ ) ) );
			$src  = realpath( __DIR__ . '/../../' . basename( dirname( dirname( __FILE__ ) ) ) );
			if ( is_dir($src) && !file_exists($dest) ) {
				symlink($src, $dest);
			}
		}


	}
