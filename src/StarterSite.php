<?php

/**
 * StarterSite class
 * This class is used to add custom functionality to the theme.
 */

namespace App;

use Timber\Site;
use Timber\Timber;
use Twig\Environment;
use Twig\TwigFilter;

/**
 * Class StarterSite.
 */
class StarterSite extends Site {



	/**
	 * StarterSite constructor.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'theme_supports' ] );
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

		add_filter( 'timber/context', [ $this, 'add_to_context' ] );
		add_filter( 'timber/twig/filters', [ $this, 'add_filters_to_twig' ] );
		add_filter( 'timber/twig/functions', [ $this, 'add_functions_to_twig' ] );
		add_filter( 'timber/twig/environment/options', [ $this, 'update_twig_environment_options' ] );

		parent::__construct();
	}

	/**
	 * This enqueues theme styles.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'timber-starter-style',
			get_template_directory_uri() . '/style.css',
			[],
			filemtime( get_template_directory() . '/style.css' )
		);
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies() {}

	/**
	 * This is where you add some context.
	 *
	 * @param array $context context['this'] Being the Twig's {{ this }}
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = Timber::get_menu( 'primary_navigation' );
		$context['site']  = $this;

		return $context;
	}

	/**
	 * This is where you can add your theme supports.
	 */
	public function theme_supports() {
		// Register navigation menus
		register_nav_menus(
			[
				'primary_navigation' => _x( 'Main menu', 'Backend - menu name', 'timber-starter' ),
			]
		);

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			[
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			]
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			[
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			]
		);

		add_theme_support( 'menus' );
	}

	/**
	 * This would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';

		return $text;
	}

	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @link https://timber.github.io/docs/v2/hooks/filters/#timber/twig/filters
	 * @param array $filters an array of Twig filters.
	 */
	public function add_filters_to_twig( $filters ) {

		$additional_filters = [
			'myfoo' => [
				'callable' => [ $this, 'myfoo' ],
			],
		];

		return array_merge( $filters, $additional_filters );
	}


	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @link https://timber.github.io/docs/v2/hooks/filters/#timber/twig/functions
	 * @param array $functions an array of existing Twig functions.
	 */
	public function add_functions_to_twig( $functions ) {
		$additional_functions = [
			'get_theme_mod' => [
				'callable' => 'get_theme_mod',
			],
		];

		return array_merge( $functions, $additional_functions );
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @see https://twig.symfony.com/doc/2.x/api.html#environment-options
	 *
	 * @param array $options an array of environment options
	 *
	 * @return array
	 */
	public function update_twig_environment_options( $options ) {
		// $options['autoescape'] = true;

		return $options;
	}
}
