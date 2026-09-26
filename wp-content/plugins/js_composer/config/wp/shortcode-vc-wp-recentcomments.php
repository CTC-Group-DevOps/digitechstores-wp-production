<?php
/**
 * Configuration file for [vc_wp_recentcomments] shortcode of 'WP Recent Comments' element.
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
		'value' => esc_html__( 'Recent Comments' ),
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Number of comments', 'js_composer' ),
		'param_name' => 'number',
		'settings' => [
			'min' => 1,
		],
		'value' => 5,
		'admin_label' => true,
	],
];

return [
	'name' => 'WP ' . esc_html__( 'recent comments' ),
	'base' => 'vc_wp_recentcomments',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'The most recent comments', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
