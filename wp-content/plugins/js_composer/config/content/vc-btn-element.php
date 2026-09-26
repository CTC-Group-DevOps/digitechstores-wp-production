<?php
/**
 * Configuration file for [vc_btn] shortcode of 'Button' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @return array
 */
function vc_btn_element_params() { // phpcs:ignore:CognitiveComplexity.Complexity.MaximumComplexity.TooHigh

	$pixel_icons = vc_get_shared( 'pixel icons' );
	require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-icon-element.php' );

	$icons_params = vc_map_integrate_shortcode(
		vc_icon_element_params(),
		'i_',
		esc_html__( 'Icon', 'js_composer' ),
		[
			'include_only_regex' => '/^(type|icon_\w*)/', // we need only type, icon_fontawesome, icon_blabla..., NOT color and etc.
		],
		[
			'element' => 'add_icon',
			'value' => 'true',
		]
	);
	// populate integrated vc_icons params.
	if ( is_array( $icons_params ) && ! empty( $icons_params ) ) {
		foreach ( $icons_params as $key => $param ) {
			if ( is_array( $param ) && ! empty( $param ) ) {
				if ( 'i_type' === $param['param_name'] ) {
					// append pixelicons to dropdown.
					$icons_params[ $key ]['value'][ esc_html__( 'Pixel', 'js_composer' ) ] = 'pixelicons';
				}
				if ( isset( $param['admin_label'] ) ) {
					// remove admin label.
					unset( $icons_params[ $key ]['admin_label'] );
				}
			}
		}
	}
	$params = array_merge( [
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'title',
			// fully compatible to btn1 and btn2.
			'value' => esc_html__( 'Text on the button', 'js_composer' ),
			'section' => 'general',
		],
		[
			'type' => 'link',
			'heading' => esc_html__( 'URL', 'js_composer' ),
			'param_name' => 'link',
			'section' => 'general',
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'param_name' => 'shape',
			// need to be converted.
			'value' => [
				esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
				esc_html__( 'Square', 'js_composer' ) => 'square',
				esc_html__( 'Round', 'js_composer' ) => 'round',
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			// compatible with btn2, default left to be compatible with btn1.
			'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
			'std' => 'left',
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Size', 'js_composer' ),
			'param_name' => 'size',
			// compatible with btn2, default md, but need to be converted from btn1 to btn2.
			'std' => 'md',
			'value' => vc_config()->get_size_param_value( [ 'xs', 'sm', 'md', 'lg' ] ),
			'section' => 'general',
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'param_name' => 'style',
			'value' => [
				esc_html__( 'Modern', 'js_composer' ) => 'modern',
				esc_html__( 'Classic', 'js_composer' ) => 'classic',
				esc_html__( 'Flat', 'js_composer' ) => 'flat',
				esc_html__( 'Outline', 'js_composer' ) => 'outline',
				esc_html__( '3d', 'js_composer' ) => '3d',
				esc_html__( 'Custom', 'js_composer' ) => 'custom',
				esc_html__( 'Outline custom', 'js_composer' ) => 'outline-custom',
				esc_html__( 'Gradient', 'js_composer' ) => 'gradient-custom',
			],
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Start color', 'js_composer' ),
			'param_name' => 'gradient_custom_color_1',
			'settings' => [
				'default_colorpicker_color' => '#dd3333',
			],
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => '#dd3333',
			'dependency' => [
				'element' => 'style',
				'value' => [ 'gradient-custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'End color', 'js_composer' ),
			'param_name' => 'gradient_custom_color_2',
			'settings' => [
				'default_colorpicker_color' => '#eeee22',
			],
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => '#eeee22',
			'dependency' => [
				'element' => 'style',
				'value' => [ 'gradient-custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'gradient_text_color',
			'settings' => [
				'default_colorpicker_color' => '#ffffff',
			],
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => '#ffffff',
			// must have default color grey.
			'dependency' => [
				'element' => 'style',
				'value' => [ 'gradient-custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],

		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Background', 'js_composer' ),
			'param_name' => 'custom_background',
			'settings' => [
				'default_colorpicker_color' => '#ebebeb',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'modern', 'classic', 'flat', '3d' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'std' => '#ebebeb',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover background', 'js_composer' ),
			'param_name' => 'custom_hover_background',
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'modern', 'classic', 'flat', 'outline' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'custom_text',
			'settings' => [
				'default_colorpicker_color' => '#666',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'modern', 'classic', 'flat', 'outline', '3d' ],
			],
			'std' => '#666',
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover text', 'js_composer' ),
			'param_name' => 'custom_hover_text',
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'modern', 'classic', 'flat', 'outline' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Border', 'js_composer' ),
			'param_name' => 'custom_border',
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'modern', 'outline', '3d' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover border', 'js_composer' ),
			'param_name' => 'custom_hover_border',
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'modern', 'outline' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],

		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Outline and text', 'js_composer' ),
			'param_name' => 'outline_custom_color',
			'settings' => [
				'default_colorpicker_color' => '#666',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'outline-custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'std' => '#666',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover background', 'js_composer' ),
			'param_name' => 'outline_custom_hover_background',
			'settings' => [
				'default_colorpicker_color' => '#666',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'outline-custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'std' => '#666',
			'section' => 'general',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover text', 'js_composer' ),
			'param_name' => 'outline_custom_hover_text',
			'settings' => [
				'default_colorpicker_color' => '#fff',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'outline-custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'std' => '#fff',
			'section' => 'general',
		],

		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Full width', 'js_composer' ),
			'param_name' => 'button_block',
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'secondary',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Onclick action', 'js_composer' ),
			'param_name' => 'custom_onclick',
			'description' => esc_html__( 'Insert inline onclick javascript action.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'secondary',
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'On click code', 'js_composer' ),
			'param_name' => 'custom_onclick_code',
			'description' => esc_html__( 'Enter onclick action code.', 'js_composer' ),
			'dependency' => [
				'element' => 'custom_onclick',
				'not_empty' => true,
			],
			'section' => 'secondary',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'add_icon',
			'section' => 'secondary',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'i_align',
			'value' => vc_config()->get_text_align_param_value( [ 'center', 'justify' ] ),
			'std' => 'left',
			'dependency' => [
				'element' => 'add_icon',
				'value' => 'true',
			],
			'group' => esc_html__( 'Icon', 'js_composer' ),
		],
	], $icons_params
	);

	// class WPBakeryShortCode_Vc_Btn.
	return [
		'name' => esc_html__( 'Button', 'js_composer' ),
		'base' => 'vc_btn',
		'icon' => 'icon-wpb-ui-button',
		'element_default_class' => 'vc_do_btn',
		'category' => [
			esc_html__( 'Content', 'js_composer' ),
		],
		'description' => esc_html__( 'Eye catching button', 'js_composer' ),
		'params' => array_merge( $params, vc_config()->get_css_animation_config(), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( [ 'margin-bottom' => '22px' ] ) ),
		'js_view' => 'VcButton3View',
		'custom_markup' => '{{title}}<div class="vc_btn3-container"><button class="vc_general vc_btn3 vc_btn3-size-sm vc_btn3-shape-{{ params.shape }} vc_btn3-style-{{ params.style }} vc_btn3-color-{{ params.color }}">{{{ params.title }}}</button></div>',
		'sections' => array_merge( [ 'general', 'secondary' ], vc_config()->get_advanced_sections() ),
	];
}
