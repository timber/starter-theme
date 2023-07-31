<?php

use Timber\Timber;
use WorDBless\BaseTestCase;

class TestTimberStarterTheme extends BaseTestCase {

	public function set_up() {
		switch_theme( basename( dirname( __DIR__ ) ) . '/theme' );

		require dirname( __DIR__ ) . '/functions.php';

		Timber::$dirname = array_merge( (array) Timber::$dirname, [ '../views' ] );
		Timber::$dirname = array_unique( Timber::$dirname );

		// WorDBless includes wp-settings.php
		do_action( 'after_setup_theme' );

		parent::set_up();
	}

	function tear_down() {
		parent::tear_down();
		switch_theme('twentytwenty');
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
		// $version = Timber::compile_string("{{ version }}", [ 'version', Twig\Environment::VERSION ]);
	}

	function testTwigFilter() {
		$str = Timber::compile_string('{{ "foo"|myfoo }}');
		$this->assertEquals('foo bar!', $str);
	}
}
