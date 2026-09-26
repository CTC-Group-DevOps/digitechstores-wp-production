<?php
/**
 * Configuration file for [vc_wp_calendar] shortcode of 'WP Calendar' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 * @deprecated 4.6
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
];

return [
	'name' => 'WP ' . esc_html__( 'calendar' ),
	'base' => 'vc_wp_calendar',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'A calendar of your sites posts', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
