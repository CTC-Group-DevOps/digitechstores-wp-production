<?php
/**
 * Configuration file for [vc_line_chart] shortcode of 'Line Chart' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'param_group',
		'heading' => esc_html__( 'Values', 'js_composer' ),
		'param_name' => 'values',
		'value' => rawurlencode( wp_json_encode( [
			[
				'title' => esc_html__( 'One', 'js_composer' ),
				'y_values' => '10; 15; 20; 25; 27; 25; 23; 25',
				'custom_color' => '#5472d2',
			],
			[
				'title' => esc_html__( 'Two', 'js_composer' ),
				'y_values' => '25; 18; 16; 17; 20; 25; 30; 35',
				'custom_color' => '#fe6c61',
			],
		] ) ),
		'params' => [
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'js_composer' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter title for chart dataset.', 'js_composer' ),
				'admin_label' => true,
			],
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Y-axis values', 'js_composer' ),
				'param_name' => 'y_values',
				'description' => esc_html__( 'Enter values for axis (Note: separate values with ";").', 'js_composer' ),
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'js_composer' ),
				'param_name' => 'custom_color',
				'description' => esc_html__( 'Select custom chart color.', 'js_composer' ),
			],
		],
		'callbacks' => [
			'after_add' => 'vcChartParamAfterAddCallback',
		],
		'section' => 'values',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'X-axis values', 'js_composer' ),
		'param_name' => 'x_values',
		'description' => esc_html__( 'Enter values for axis (Note: separate values with ";").', 'js_composer' ),
		'value' => 'JAN; FEB; MAR; APR; MAY; JUN; JUL; AUG',
		'section' => 'values',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Design', 'js_composer' ),
		'param_name' => 'type',
		'value' => [
			esc_html__( 'Line', 'js_composer' ) => 'line',
			esc_html__( 'Bar', 'js_composer' ) => 'bar',
		],
		'std' => 'bar',
		'description' => esc_html__( 'Select type of chart.', 'js_composer' ),
		'admin_label' => true,
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'description' => esc_html__( 'Select chart color style.', 'js_composer' ),
		'param_name' => 'style',
		'value' => [
			esc_html__( 'Flat', 'js_composer' ) => 'flat',
			esc_html__( 'Modern', 'js_composer' ) => 'modern',
		],
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Animation', 'js_composer' ),
		'description' => esc_html__( 'Select animation style.', 'js_composer' ),
		'param_name' => 'animation',
		'value' => vc_get_shared( 'animation styles' ),
		'std' => 'easeInOutCubic',
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6 vc_col-xs-6-expand',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show legend', 'js_composer' ),
		'param_name' => 'legend',
		'description' => esc_html__( 'If on, chart will have legend.', 'js_composer' ),
		'value' => 'yes',
		'std' => 'yes',
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show values on hover', 'js_composer' ),
		'param_name' => 'tooltips',
		'description' => esc_html__( 'If on, chart will show values on hover.', 'js_composer' ),
		'value' => 'yes',
		'std' => 'yes',
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6',
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
	'name' => esc_html__( 'Line chart', 'js_composer' ),
	'base' => 'vc_line_chart',
	'class' => '',
	'icon' => 'icon-wpb-vc-line-chart',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Line and Bar charts', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'values', 'general', 'title' ], vc_config()->get_advanced_sections() ),
];
