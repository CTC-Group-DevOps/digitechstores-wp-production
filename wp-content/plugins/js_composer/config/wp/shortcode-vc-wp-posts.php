<?php
/**
 * Configuration file for [vc_wp_posts] shortcode of 'WP Recent Posts' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' ),
		'value' => esc_html__( 'Recent Posts' ),
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Number of posts', 'js_composer' ),
		'param_name' => 'number',
		'value' => 5,
		'settings' => [
			'min' => 1,
		],
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Display post date', 'js_composer' ),
		'param_name' => 'show_date',
		'value' => [ 1 ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
];

return [
	'name' => 'WP ' . esc_html__( 'recent posts' ),
	'base' => 'vc_wp_posts',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'The most recent posts on your site', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
