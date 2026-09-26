<?php
/**
 * Configuration file for [vc_wp_text] shortcode of 'WP Text' element.
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
	],
	[
		'type' => 'textarea_html',
		'holder' => 'div',
		'heading' => esc_html__( 'Text', 'js_composer' ),
		'param_name' => 'content',
	],
];

return [
	'name' => 'WP ' . esc_html__( 'text' ),
	'base' => 'vc_wp_text',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'Arbitrary text or HTML', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
