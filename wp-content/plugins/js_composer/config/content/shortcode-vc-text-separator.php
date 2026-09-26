<?php
/**
 * Configuration file for [vc_text_separator] shortcode of 'Separator with Text' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once 'vc-icon-element.php';
$icon_params = vc_icon_element_params();

$icons_params = vc_map_integrate_shortcode( $icon_params, 'i_', esc_html__( 'Icon', 'js_composer' ), [
	'exclude' => [
		'align',
		'css',
		'el_class',
		'el_id',
		'link',
		'css_animation',
	],
], [
	'element' => 'add_icon',
	'value' => 'true',
] );

$icons_params = array_map( function ( $icon_param ) {
	if ( in_array( $icon_param['param_name'], [ 'i_custom_background_color', 'i_background_style' ], true ) ) {
		$icon_param['section'] = 'icon_shape';
	}

	return $icon_param;
}, $icons_params );

if ( is_array( $icons_params ) && ! empty( $icons_params ) ) {
	foreach ( $icons_params as $key => $param ) {
		if ( ! is_array( $param ) || empty( $param ) ) {
			continue;
		}
		if ( isset( $param['admin_label'] ) ) {
			unset( $icons_params[ $key ]['admin_label'] );
		}
		if ( 'i_custom_color' === $param['param_name'] ) {
			$icons_params[ $key ]['edit_field_class'] = 'vc_col-xs-12';
		}
		if ( 'i_custom_background_color' === $param['param_name'] ) {
			$icons_params[ $key ]['edit_field_class'] = 'vc_col-xs-6';
			$icons_params[ $key ]['heading'] = __( 'Color', 'js_composer' );
		}
	}
}

$params = array_merge(
	[
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'js_composer' ),
			'param_name' => 'title',
			'holder' => 'div',
			'value' => esc_html__( 'Title', 'js_composer' ),
		],
	],
	[
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Title position', 'js_composer' ),
			'param_name' => 'title_align',
			'value'       => [
				'separator_align_left'   => [
					'label' => 'vc-c-alignment-left',
					'title' => esc_html__( 'Left', 'js_composer' ),
				],
				'separator_align_center'   => [
					'label' => 'vc-c-alignment-center',
					'title' => esc_html__( 'Center', 'js_composer' ),
				],
				'separator_align_right'   => [
					'label' => 'vc-c-alignment-right',
					'title' => esc_html__( 'Right', 'js_composer' ),
				],
			],
			'std' => 'separator_align_center',
			'edit_field_class' => 'vc_col-xs-6',
		],
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
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'param_name' => 'style',
			'value' => vc_get_shared( 'separator styles' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'settings' => [
				'default_colorpicker_color' => '#EBEBEB',
			],
			'param_name' => 'accent_color',
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'range',
			'heading' => esc_html__( 'Border width', 'js_composer' ),
			'param_name' => 'border_width',
			'value' => '1',
			'settings' => [
				'min' => '1',
				'max' => '10',
				'step' => '1',
				'unit' => 'px',
			],
		],
		[
			'type' => 'range',
			'heading' => esc_html__( 'Element width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => '100',
			'settings' => [
				'min' => '1',
				'max' => '100',
				'step' => '1',
				'unit' => '%',
			],
		],
		[
			'type' => 'hidden',
			'param_name' => 'layout',
			'value' => 'separator_with_text',
		],
	],
	[
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'add_icon',
		],
	],
	$icons_params,
);

return [
	'name' => esc_html__( 'Separator with text', 'js_composer' ),
	'base' => 'vc_text_separator',
	'icon' => 'icon-wpb-ui-separator-label',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Horizontal separator line with heading', 'js_composer' ),
	'js_view' => 'VcTextSeparatorView',
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
