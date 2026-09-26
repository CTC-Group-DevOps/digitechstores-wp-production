<?php
/**
 * Configuration file for [vc_progress_bar] shortcode of 'Progress Bar' element.
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
		'description' => esc_html__( 'Enter values for graph - value, title and color.', 'js_composer' ),
		'value' => rawurlencode( wp_json_encode( [
			[
				'label' => esc_html__( 'Development', 'js_composer' ),
				'value' => '90',
			],
			[
				'label' => esc_html__( 'Design', 'js_composer' ),
				'value' => '80',
			],
			[
				'label' => esc_html__( 'Marketing', 'js_composer' ),
				'value' => '70',
			],
		] ) ),
		'params' => [
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Label', 'js_composer' ),
				'param_name' => 'label',
				'description' => esc_html__( 'Enter text used as title of bar.', 'js_composer' ),
				'admin_label' => true,
				'edit_field_class' => 'vc_col-xs-6',
			],
			[
				'type' => 'number',
				'heading' => esc_html__( 'Value', 'js_composer' ),
				'param_name' => 'value',
				'description' => esc_html__( 'Enter value of bar.', 'js_composer' ),
				'admin_label' => true,
				'settings' => [
					'min' => 0,
					'max' => 100,
				],
				'edit_field_class' => 'vc_col-xs-6',
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background color', 'js_composer' ),
				'param_name' => 'customcolor',
				'description' => esc_html__( 'Select custom single bar background color.', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-6',
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Text color', 'js_composer' ),
				'param_name' => 'customtxtcolor',
				'description' => esc_html__( 'Select custom single bar text color.', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-6',
			],
			[
				'type' => 'toggle',
				'heading' => esc_html__( 'Add shadow', 'js_composer' ),
				'description' => esc_html__( 'Enable text shadow', 'js_composer' ),
				'param_name' => 'add_text_shadow',
				'std' => '',
				'edit_field_class' => 'vc_col-xs-6',
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Shadow color', 'js_composer' ),
				'param_name' => 'text_shadow_color',
				'description' => esc_html__( 'Select custom text shadow color.', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-6',
				'dependency' => [
					'element' => 'add_text_shadow',
					'value' => [ 'true' ],
				],
			],
		],
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Units', 'js_composer' ),
		'param_name' => 'units',
		'description' => esc_html__( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'js_composer' ),
		'section' => 'general',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Background color', 'js_composer' ),
		'settings' => [
			'default_colorpicker_color' => '#e0e0e0',
		],
		'param_name' => 'custombgcolor',
		'description' => esc_html__( 'Select custom background color for bars.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Text color', 'js_composer' ),
		'settings' => [
			'default_colorpicker_color' => '#111111',
		],
		'param_name' => 'customtxtcolor',
		'description' => esc_html__( 'Select custom text color for bars.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Add shadow', 'js_composer' ),
		'description' => esc_html__( 'Enable text shadow', 'js_composer' ),
		'param_name' => 'add_text_shadow',
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6 vc_col-expand-next-hidden',
		'section' => 'general',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Shadow color', 'js_composer' ),
		'param_name' => 'text_shadow_color',
		'description' => esc_html__( 'Select custom text shadow color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'dependency' => [
			'element' => 'add_text_shadow',
			'value' => [ 'true' ],
		],
		'section' => 'general',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Add stripes', 'js_composer' ),
		'param_name' => 'striped',
		'value' => 'true',
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Add animation', 'js_composer' ),
		'description' => esc_html__( 'Animate striped bar.', 'js_composer' ),
		'param_name' => 'animated',
		'value' => 'true',
		'std' => '',
		'dependency' => [
			'element' => 'striped',
			'value' => [ 'true' ],
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		'section' => 'title',
	],
];

return [
	'name' => esc_html__( 'Progress bar', 'js_composer' ),
	'base' => 'vc_progress_bar',
	'icon' => 'icon-wpb-graph',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Animated progress bar', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'general', 'title' ], vc_config()->get_advanced_sections() ),
];
