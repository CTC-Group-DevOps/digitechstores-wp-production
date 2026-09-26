<?php
/**
 * Configuration file for [vc_wp_pages] shortcode of 'WP Pages' element.
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
		'value' => esc_html__( 'Pages' ),
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Order by', 'js_composer' ),
		'param_name' => 'sortby',
		'value' => [
			esc_html__( 'Page title', 'js_composer' ) => 'post_title',
			esc_html__( 'Page order', 'js_composer' ) => 'menu_order',
			esc_html__( 'Page ID', 'js_composer' ) => 'ID',
		],
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Exclude', 'js_composer' ),
		'param_name' => 'exclude',
		'description' => esc_html__( 'Enter page IDs to be excluded (Note: separate values by commas (,)).', 'js_composer' ),
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
	],
];

return [
	'name' => 'WP ' . esc_html__( 'pages' ),
	'base' => 'vc_wp_pages',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'Your sites WordPress Pages', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
