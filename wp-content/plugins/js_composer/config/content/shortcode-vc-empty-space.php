<?php
/**
 * Configuration file for [vc_empty_space] shortcode of 'Empty Space' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'number',
		'heading' => esc_html__( 'Height', 'js_composer' ),
		'param_name' => 'height',
		'std' => '32',
		'settings' => [
			'min' => 0,
			'units' => vc_get_shared( 'css-units' ),
		],
		'admin_label' => true,
		'description' => esc_html__( 'Enter empty space height (Note: CSS measurement units allowed).', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Empty space', 'js_composer' ),
	'base' => 'vc_empty_space',
	'icon' => 'icon-wpb-ui-empty_space',
	'show_settings_on_create' => true,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Blank space with custom height', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab() ),
];
