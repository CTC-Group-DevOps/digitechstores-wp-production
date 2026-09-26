<?php
/**
 * Configuration file for [vc_facebook] shortcode of 'Facebook Like' element.
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
			esc_html__( 'Horizontal', 'js_composer' ) => 'standard',
			esc_html__( 'Horizontal with count', 'js_composer' ) => 'button_count',
			esc_html__( 'Vertical with count', 'js_composer' ) => 'box_count',
		],
	],
];

return [
	'name' => esc_html__( 'Facebook Like', 'js_composer' ),
	'base' => 'vc_facebook',
	'icon' => 'icon-wpb-balloon-facebook-left',
	'content_element' => false,
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Social', 'js_composer' ),
	'description' => esc_html__( 'Facebook "Like" button', 'js_composer' ),
	'params' => vc_config()->merge_default_params(
		$params,
		[ 'margin-bottom' => '35px' ],
		[ 'background_style_default' => 'no-repeat' ]
	),
];
