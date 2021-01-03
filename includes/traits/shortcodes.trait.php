<?php

trait Custom_Shortcodes {

	private $contact_data = [
		'phone_number' => [
			'icon' => 'mobile-alt',
			'fa_class' => 'fas'
		],
		'user_email' => [
			'icon' => 'envelope',
			'fa_class' => 'fas'
		],
		'admin_email' => [
			'icon' => 'envelope',
			'fa_class' => 'fas'
		],
		'facebook' => [
			'icon' => 'facebook',
			'fa_class' => 'fab'
		],
		'instagram' => [
			'icon' => 'instagram',
			'fa_class' => 'fab'
		]
	];

	public function services_table() {
		$context = Timber::context();
		$context['services'] = Timber::get_posts( [
			'post_type' => 'service',
			'posts_per_page' => '-1',
			'order' => 'ASC',
			'orderby' => 'menu_order'
		] );
		
		Timber::render( [ 'partial/services-table.twig' ], $context );
	}

  public function contact_info( $atts ) {
		$defaults = [
			'wrapper' => '1',
			'username' => false,
			'template' => 'page',
			'include' => 'phone_number,user_email',
			'exclude' => ''
		];
		
		$a = shortcode_atts( $defaults, $atts );
		$user = get_user_by( 'login', $a['username'] );

		$context = Timber::context();
		$yoast_options = get_option( 'wpseo_social' );
		$contact_values = [];
		$output = '';

		foreach ( explode( ',', trim( $a['include'] ) ) as $contact_info ) {
			if ( strpos( $a['exclude'], $contact_info ) !== false ) continue;

			$values_for_template = [];
			$href_val = null;

			// fallback values
			$values_for_template['value'] = $user ? $user->get( $contact_info ) : null;
			$values_for_template['value'] = empty( $values_for_template['value'] ) ? get_bloginfo( $contact_info ) : $values_for_template['value'];

			if ( $contact_info === 'phone_number' ) {
				$href_val = "tel:{$values_for_template['value']}";
			} else if ( $contact_info === 'user_email' || $contact_info === 'admin_email' ) {
				$href_val = "mailto:{$values_for_template['value']}";
			} else if ( $contact_info === 'facebook' ) {
				$href_val = $yoast_options['facebook_site'];
			} else if ( $contact_info === 'instagram' ) {
				$href_val = $yoast_options['instagram_url'];
			}

			$values_for_template['href'] = $href_val;
			$values_for_template['icon'] = $this->contact_data[$contact_info]['icon'] ?? null;
			$values_for_template['fa_class'] = $this->contact_data[$contact_info]['fa_class'] ?? null;

			$context['contact_info'] = $values_for_template;

			ob_start();
			Timber::render( [ "shortcodes/contact-info/{$a['template']}.twig" ], $context );
			$output .= ob_get_contents();
			ob_end_clean();
		}

		return $output;
	}
}