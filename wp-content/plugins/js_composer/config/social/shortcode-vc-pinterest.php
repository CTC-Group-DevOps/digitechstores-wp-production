<?php
/**
 * Configuration file for [vc_pinterest] shortcode of 'Pinterest' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Button type', 'js_composer' ),
		'param_name' => 'type',
		'admin_label' => true,
		'value' => [
			esc_html__( 'Horizontal', 'js_composer' ) => 'horizontal',
			esc_html__( 'Vertical', 'js_composer' ) => 'vertical',
			esc_html__( 'No count', 'js_composer' ) => 'none',
		],
	],
];

return [
	'name' => esc_html__( 'Pinterest', 'js_composer' ),
	'base' => 'vc_pinterest',
	'icon' => 'icon-wpb-pinterest',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Social', 'js_composer' ),
	'description' => esc_html__( 'Pinterest button', 'js_composer' ),
	'params' => vc_config()->merge_default_params(
		$params,
		[ 'margin-bottom' => '35px' ],
		[ 'background_style_default' => 'no-repeat' ]
	),
];
