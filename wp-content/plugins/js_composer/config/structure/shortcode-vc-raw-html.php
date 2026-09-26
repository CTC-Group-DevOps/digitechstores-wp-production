<?php
/**
 * Configuration file for [vc_raw_html] shortcode of 'Raw HTML' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type'        => 'textarea_ace',
		'heading' => esc_html__( 'Raw HTML', 'js_composer' ),
		'param_name'  => 'content',
		'settings' => [
			'mode' => 'html',
		],
		'holder' => 'div',
		'value' => base64_encode( '<p>I am raw html block.<br/>Click edit button to change this html</p>' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		'description' => esc_html__( 'Enter your HTML content.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Raw HTML', 'js_composer' ),
	'base' => 'vc_raw_html',
	'icon' => 'icon-wpb-raw-html',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Structure', 'js_composer' ),
	'wrapper_class' => 'clearfix',
	'description' => esc_html__( 'Output raw HTML code on your page', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( [ 'margin-bottom' => '35px' ] ), vc_config()->get_css_animation_config() ),
];
