<?php
/**
 * Configuration file for [vc_zigzag] shortcode of 'ZigZag Separator' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'param_name' => 'align',
		'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
		'std' => 'center',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Color', 'js_composer' ),
		'settings' => [
			'default_colorpicker_color' => '#000000',
		],
		'param_name' => 'custom_color',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Border width', 'js_composer' ),
		'param_name' => 'el_border_width',
		'std' => '12',
		'value' => [
			'8'      => [
				'label' => esc_html__( 'XS', 'js_composer' ),
				'title' => esc_html__( 'Extra Small', 'js_composer' ),
			],
			'10'       => [
				'label' => esc_html__( 'S', 'js_composer' ),
				'title' => esc_html__( 'Small', 'js_composer' ),
			],
			'12' => [
				'label' => esc_html__( 'M', 'js_composer' ),
				'title' => esc_html__( 'Medium', 'js_composer' ),
			],
			'15' => [
				'label' => esc_html__( 'L', 'js_composer' ),
				'title' => esc_html__( 'Large', 'js_composer' ),
			],
			'20' => [
				'label' => esc_html__( 'XL', 'js_composer' ),
				'title' => esc_html__( 'Extra Large', 'js_composer' ),
			],
		],
	],
	[
		'type' => 'range',
		'heading' => esc_html__( 'Element width', 'js_composer' ),
		'param_name' => 'el_width',
		'settings' => [
			'min' => 1,
			'max' => 100,
			'step' => 1,
			'unit' => '%',
		],
		'value' => 100,
	],
];

return [
	'name' => esc_html__( 'Zigzag separator', 'js_composer' ),
	'base' => 'vc_zigzag',
	'icon' => 'vc_icon-vc-zigzag',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Horizontal zigzag separator line', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params ),
];
