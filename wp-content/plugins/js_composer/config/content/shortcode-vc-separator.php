<?php
/**
 * Configuration file for [vc_separator] shortcode of 'Separator' element.
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
		'value'       => [
			'align_left'   => [
				'label' => 'vc-c-alignment-left',
				'title' => esc_html__( 'Left', 'js_composer' ),
			],
			'align_center'   => [
				'label' => 'vc-c-alignment-center',
				'title' => esc_html__( 'Center', 'js_composer' ),
			],
			'align_right'   => [
				'label' => 'vc-c-alignment-right',
				'title' => esc_html__( 'Right', 'js_composer' ),
			],
		],
		'std' => 'align_center',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Color', 'js_composer' ),
		'param_name' => 'accent_color',
		'settings' => [
			'default_colorpicker_color' => '#EBEBEB',
		],
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'param_name' => 'style',
		'value' => vc_get_shared( 'separator styles' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Border width', 'js_composer' ),
		'param_name' => 'border_width',
		'value' => '1',
		'settings' => [
			'min' => 1,
			'max' => 10,
			'units' => [ 'px' ],
		],
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'range',
		'heading' => esc_html__( 'Element width', 'js_composer' ),
		'param_name' => 'el_width',
		'value' => 100,
		'settings' => [
			'min' => 1,
			'max' => 100,
			'step' => 1,
			'unit' => '%',
		],
	],
];

return [
	'name' => esc_html__( 'Separator', 'js_composer' ),
	'base' => 'vc_separator',
	'icon' => 'icon-wpb-ui-separator',
	'element_default_class' => 'wpb_content_element',
	'show_settings_on_create' => true,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Horizontal separator line', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
