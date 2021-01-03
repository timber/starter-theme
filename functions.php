<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

require_once __DIR__ . '/includes/traits/shortcodes.trait.php';

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
	use Custom_Shortcodes;

	public $logo_width = 50;
	public $logo_height = 50;
	public $header_width = 740;
	public $header_height = 200;

	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );

		// wrap with checks/break into separate files
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_head', array( $this, 'head_modifications' ) );
		add_filter( 'wpcf7_form_response_output', array( $this, 'cf7_customizations' ), 10, 4 );
		add_action( 'acf/init', array( $this, 'acf_global_options_page' ) );
		add_filter( 'wpseo_metabox_prio', function() { return 'low'; } );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'logo_customizations' ) );		

		parent::__construct();
	}
	
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	/** This is where you can register custom shortcodes defined in the Shortcode trait. */
	public function register_shortcodes() {
		add_shortcode( 'contact_info', array( $this, 'contact_info' ) );
		add_shortcode( 'services_table', array( $this, 'services_table' ) );
	}

	public function register_scripts() {
		global $wp_customize;

		wp_register_style( 'vendor', get_stylesheet_directory_uri() . '/static/styles/vendor.css' );
		wp_register_style( 'main', get_stylesheet_directory_uri( ) . '/static/styles/site.css', [ 'vendor' ] );
		wp_register_script( 'vendor', get_stylesheet_directory_uri( ) . '/static/scripts/vendor.js', [ 'jquery' ], false, true );
		wp_register_script( 'common', get_stylesheet_directory_uri( ) . '/static/scripts/common.js' );
		wp_register_script( 'main', get_stylesheet_directory_uri( ) . '/static/scripts/site.js', [ 'jquery', 'vendor' ], false, true );
		wp_register_script( 'custom', get_stylesheet_directory_uri( ) . '/static/scripts/custom.js' );

		// fix customize view in dashboard
		if ( isset( $wp_customize ) ) {
			wp_enqueue_style( 'vendor' );
			wp_enqueue_style( 'main' );
		}
	}

	public function logo_customizations( $attr ) {
		// add custom class to logo element
		if ( isset( $attr['class'] ) && strpos( 'custom-logo', $attr['class'] ) !== false ) {
			$attr['class'] .= ' img-fluid';
		}
		return $attr;
	}

	public function head_modifications() {
		global $post;

		$full_src_path = null;
		$custom_header_data = get_custom_header();

		if ( class_exists( 'ACF' ) ) {
			$full_src_path = get_field( 'custom_header' )['ID'];
		} else if ( property_exists( $custom_header_data, 'attachment_id' ) ) {
			$full_src_path = $custom_header_data->attachment_id;
		}

		if ( ! $full_src_path ) return;

		$full_src_path = get_attached_file( $full_src_path );
		
		$src_path = substr( $full_src_path, strpos( $full_src_path, '/wp-content' ) );

		// style for header
		?>
		<style>
			.logo {
				max-width: <?php echo $this->logo_width; ?>px;
				max-height: <?php echo $this->logo_height; ?>px;
			}

			.header-image {
				background-image: url('<?php echo $src_path; ?>');
				background-repeat: no-repeat;
				background-position: center center;
				background-size: cover;
				width: 100%;
				height: <?php echo $this->header_height; ?>px;
			}
		</style>
		<?php
	}

	public function cf7_customizations( $output, $class, $content, $instance ) {
		$submission = WPCF7_Submission::get_instance( $instance );
		$status = $submission->get_status();
		$classes = [ $class, 'alert' ];

		// customizing the appearance of the user feedback
		if ( $status === 'validation_failed' ) {
			$custom_classes = [ 'alert-danger', 'border-danger' ];	
		}

		if ( isset( $custom_classes ) ) {
			return preg_replace( '/class=\"(.+?)\"/', 'class="' . implode( ' ', array_merge( $classes, $custom_classes ) ) . '"', $output );
		}
		
		return $output;
	}

	public function acf_global_options_page() {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page( [
			'page_title' => __( 'Global Settings' ),
			'menu_title' => __( 'Global' ),
			'redirect' => false,
			'parent_slug' => 'options-general.php'
		] );
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['menu']  = new Timber\Menu();
		$context['site']  = $this;
		$context['has_logo'] = function_exists( 'the_custom_logo' ) && has_custom_logo();
		$context['has_header_image'] = function_exists( 'header_image' ) && has_custom_header();
		$context['is_front_page'] = is_front_page();
		$context['is_mobile'] = wp_is_mobile();

		return $context;
	}

	public function theme_supports() {
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
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );

		add_theme_support( 'custom-logo', [
			'width' => $this->logo_width,
			'height' => $this->logo_height,
			'flex-width' => true,
			'flex-height' => true
		] );

		add_theme_support( 'custom-header', [
			'width' => $this->header_width,
			'height' => $this->header_height,
			'flex-width' => true,
			'flex-height' => true,
			'uploads' => true
		] );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		
		$twig->addFunction( new Twig_SimpleFunction( 'enqueue_script', function( $handle ) {
			wp_enqueue_script( $handle );
		} ) );
		
		$twig->addFunction( new Twig_SimpleFunction( 'enqueue_style', function( $handle ) {
			wp_enqueue_style( $handle );
		} ) );

		$twig->addFunction( new Twig_SimpleFunction( 'logo', function() {
			?><div class="logo grow-on-hover transition-all-normal"><?php the_custom_logo(); ?></div><?php
		} ) );
		
		return $twig;
	}
}


class StarterSite_Dev extends StarterSite {
	
	public function __construct() {
		add_action( 'upgrader_process_complete', array( $this, 'bulk_update_wp_meta' ), 10, 2 );
		add_action( 'activated_plugin', function( $plugin ) { $this->update_plugin_meta_status( $plugin, 'active' ); } );
		add_action( 'deactivated_plugin', function( $plugin ) { $this->update_plugin_meta_status( $plugin, 'inactive' ); } );

		// check for existance of WP CLI
		if ( ! shell_exec( 'wp --info' ) ) {
			error_log( print_r( __FILE__ . ' Line ' . __LINE__ . ': WP CLI is not available in this environment.', true ) );
			$this->wp_cli = true;
		}

		parent::__construct();
	}

	public function bulk_update_wp_meta( $upgrader, $options ) {
		// do not run outside of a development environment
		// exit if wp cli is not installed
		if ( ! $this->wp_cli || $options['action'] !== 'update' ) {
			return;
		}

		// update core meta; wp-config.json file
		if ( $options['type'] === 'core' ) {
			return $this->update_wp_core_meta();
		}

		// update plugin meta; installed-plugins.json file
		$this->run_wp_plugin_list();
	}

	public function update_plugin_meta_status( $plugin, $status = '' ) {
		if ( ! $this->wp_cli ) return;

		$plugin_data_filepath = get_home_path() . 'plugins-installed.json';
		
		if ( ! file_exists( $plugin_data_filepath ) || empty ( $status ) ) return;

		$plugin_slug = explode( '/', $plugin )[0];
		$plugin_data = file_get_contents( $plugin_data_filepath );

		// generate a new config file if missing or not a valid json array
		if ( ! is_array( json_decode( file_get_contents( $plugin_data_filepath ), true ) ) ) {
			$this->run_wp_plugin_list();
		}

		if ( strpos( $plugin_data, $plugin_slug ) === false ) {
			return;
		}

		$plugin_data_arr = json_decode( $plugin_data, true );

		foreach ( $plugin_data_arr as $index => $plugin_data ) {
			if ( $plugin_data[ 'name' ] === $plugin_slug ) {
				$plugin_data_arr[$index]['status'] = $status;
			}
		}

		$updated_json = json_encode( $plugin_data_arr );

		file_put_contents( $plugin_data_filepath, $updated_json );
	}

	public function update_wp_core_meta() {
		if ( ! $this->wp_cli ) return;

		$config = [];
		$config['core_version'] = str_replace( "\n", '', shell_exec( 'wp core version' ) );

		file_put_contents( get_home_path() . 'wp-config.json', json_encode( $config ) );
	}

	public function run_wp_plugin_list() {
		if ( ! $this->wp_cli ) return;
		$root_dir = get_home_path();
		$cmd = "wp plugin list --format=json > {$root_dir}plugins-installed.json";
		shell_exec( $cmd );
	}
}

if ( getenv( 'WP_DEV_SITEURL' ) !== false ) {
	new StarterSite_Dev();
} else {
	new StarterSite();
}
