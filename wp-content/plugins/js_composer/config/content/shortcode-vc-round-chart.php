<?php
/**
 * Configuration file for [vc_round_chart] shortcode of 'Round Chart' element.
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
				'value' => '60',
				'custom_color' => '#5472d2',
			],
			[
				'title' => esc_html__( 'Two', 'js_composer' ),
				'value' => '40',
				'custom_color' => '#fe6c61',
			],
		] ) ),
		'params' => [
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'js_composer' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter title for chart area.', 'js_composer' ),
				'admin_label' => true,
			],
			[
				'type' => 'number',
				'heading' => esc_html__( 'Value', 'js_composer' ),
				'param_name' => 'value',
				'settings' => [
					'min' => 0,
				],
				'edit_field_class' => 'vc_col-xs-6',
				'description' => esc_html__( 'Enter value for area.', 'js_composer' ),
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'js_composer' ),
				'settings' => [
					'default_colorpicker_color' => '#E8E8E8',
				],
				'param_name' => 'custom_color',
				'edit_field_class' => 'vc_col-xs-6',
				'description' => esc_html__( 'Select custom area color.', 'js_composer' ),
			],
		],
		'callbacks' => [
			'after_add' => 'vcChartParamAfterAddCallback',
		],
		'section' => 'general',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Design', 'js_composer' ),
		'param_name' => 'type',
		'value' => [
			esc_html__( 'Pie', 'js_composer' ) => 'pie',
			esc_html__( 'Doughnut', 'js_composer' ) => 'doughnut',
		],
		'description' => esc_html__( 'Select type of chart.', 'js_composer' ),
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
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
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Animation', 'js_composer' ),
		'description' => esc_html__( 'Select animation style.', 'js_composer' ),
		'param_name' => 'animation',
		'value' => vc_get_shared( 'animation styles' ),
		'std' => 'easeInOutCubic',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Gap', 'js_composer' ),
		'param_name' => 'stroke_width',
		'settings' => [
			'min' => 0,
			'max' => 5,
		],
		'description' => esc_html__( 'Select gap size.', 'js_composer' ),
		'std' => 2,
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Outline color', 'js_composer' ),
		'param_name' => 'custom_stroke_color',
		'description' => esc_html__( 'Select custom outline color.', 'js_composer' ),
		'settings' => [
			'default_colorpicker_color' => '#FFFFFF',
		],
		'dependency' => [
			'element' => 'stroke_width',
			'value_not_equal_to' => '0',
		],
		'section' => 'general',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show values on hover', 'js_composer' ),
		'param_name' => 'tooltips',
		'description' => esc_html__( 'If checked, chart will show values on hover.', 'js_composer' ),
		'value' => 'yes',
		'std' => 'yes',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Show legend', 'js_composer' ),
		'param_name' => 'legend',
		'description' => esc_html__( 'If checked, chart will have legend.', 'js_composer' ),
		'value' => 'yes',
		'std' => 'yes',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Legend color', 'js_composer' ),
		'settings' => [
			'default_colorpicker_color' => '#2a2a2a',
		],
		'param_name' => 'custom_legend_color',
		'description' => esc_html__( 'Select custom legend color.', 'js_composer' ),
		'dependency' => [
			'element' => 'legend',
			'value' => 'yes',
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Legend position', 'js_composer' ),
		'param_name' => 'legend_position',
		'value' => [
			esc_html__( 'Top', 'js_composer' ) => 'top',
			esc_html__( 'Left', 'js_composer' ) => 'left',
			esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
			esc_html__( 'Right', 'js_composer' ) => 'right',
		],
		'description' => esc_html__( 'Select legend position.', 'js_composer' ),
		'std' => 'left',
		'dependency' => [
			'element' => 'legend',
			'value' => 'yes',
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
	'name' => esc_html__( 'Round chart', 'js_composer' ),
	'base' => 'vc_round_chart',
	'class' => '',
	'icon' => 'icon-wpb-vc-round-chart',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Pie and Doughnut charts', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'general', 'title' ], vc_config()->get_advanced_sections() ),
];
