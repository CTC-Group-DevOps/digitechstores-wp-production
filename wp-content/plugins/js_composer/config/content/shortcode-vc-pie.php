<?php
/**
 * Configuration file for [vc_pie] shortcode of 'Pie Chart' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'number',
		'heading' => esc_html__( 'Value', 'js_composer' ),
		'param_name' => 'value',
		'description' => esc_html__( 'Enter value for graph (Note: choose range from 0 to 100).', 'js_composer' ),
		'value' => '50',
		'settings' => [
			'min' => 0,
			'max' => 100,
		],
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Label', 'js_composer' ),
		'param_name' => 'label_value',
		'description' => esc_html__( 'Enter label for pie chart (Note: leaving empty will set value from "Value" field).', 'js_composer' ),
		'value' => '',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Units', 'js_composer' ),
		'param_name' => 'units',
		'description' => esc_html__( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Color', 'js_composer' ),
		'param_name' => 'custom_color',
		'description' => esc_html__( 'Select custom color.', 'js_composer' ),
		'settings' => [
			'default_colorpicker_color' => '#EBEBEB',
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		'admin_label' => true,
		'section' => 'title',
	],
];

return [
	'name' => esc_html__( 'Pie chart', 'js_composer' ),
	'base' => 'vc_pie',
	'class' => '',
	'icon' => 'icon-wpb-vc_pie',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Animated pie chart', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'general', 'title' ], vc_config()->get_advanced_sections() ),
];
