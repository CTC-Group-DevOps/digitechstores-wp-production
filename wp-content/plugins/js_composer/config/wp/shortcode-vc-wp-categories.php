<?php
/**
 * Configuration file for [vc_wp_categories] shortcode of 'WP categories' element.
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
		'value' => esc_html__( 'Categories' ),
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Display type', 'js_composer' ),
		'param_name' => 'options',
		'value' => [
			esc_html__( 'List', 'js_composer' ) => 'list',
			esc_html__( 'Dropdown', 'js_composer' ) => 'dropdown',
		],
		'std' => '',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show post count', 'js_composer' ),
		'param_name' => 'count',
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show hierarchy', 'js_composer' ),
		'param_name' => 'hierarchical',
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
];

return [
	'name' => 'WP ' . esc_html__( 'categories' ),
	'base' => 'vc_wp_categories',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'A list or dropdown of categories', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
