<?php
/**
 * Configuration file for [vc_wp_archives] shortcode of 'WP Archives' element.
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
		'value' => esc_html__( 'Archives' ),
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Display type', 'js_composer' ),
		'param_name' => 'type',
		'value' => [
			[
				'value' => 'list',
				'label' => __( 'List', 'js_composer' ),
			],
			[
				'value' => 'dropdown',
				'label' => __( 'Dropdown', 'js_composer' ),
			],
		],
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show post count', 'js_composer' ),
		'param_name' => 'count',
	],
];

return [
	'name' => 'WP ' . esc_html__( 'archives' ),
	'base' => 'vc_wp_archives',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'A monthly archive of your sites posts', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
