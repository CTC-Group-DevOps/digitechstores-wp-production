<?php
/**
 * Configuration file for [vc_hoverbox] shortcode of 'Hover Box' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$font_container_fields = [
	'font_size',
	'line_height',
	'color',

	'default_colorpicker_color' => '#111111',

	'font_size_description' => esc_html__( 'Enter font size.', 'js_composer' ),
	'line_height_description' => esc_html__( 'Enter line height.', 'js_composer' ),
	'color_description' => esc_html__( 'Select heading color.', 'js_composer' ),

	'font_size_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
	'line_height_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right',
	'color_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
];
$h2_custom_heading = vc_map_integrate_shortcode(
	vc_custom_heading_element_params( $font_container_fields ),
	'primary_title_',
	esc_html__( 'Primary title', 'js_composer' ),
	[
		'exclude' => [
			'source',
			'text',
			'css',
		],
	],
	[
		'element' => 'use_custom_fonts_primary_title',
		'value' => 'true',
	],
	true
);

$font_container_fields = [
	'font_size',
	'line_height',
	'color',

	'default_colorpicker_color' => '#111111',

	'font_size_description' => esc_html__( 'Enter font size.', 'js_composer' ),
	'line_height_description' => esc_html__( 'Enter line height.', 'js_composer' ),
	'color_description' => esc_html__( 'Select heading color.', 'js_composer' ),

	'font_size_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
	'line_height_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right',
	'color_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
];

$h4_custom_heading = vc_map_integrate_shortcode(
	vc_custom_heading_element_params( $font_container_fields ),
	'hover_title_',
	esc_html__( 'Hover title', 'js_composer' ),
	[
		'exclude' => [
			'source',
			'text',
			'css',
		],
	],
	[
		'element' => 'use_custom_fonts_hover_title',
		'value' => 'true',
	],
	true
);

$button = vc_map_integrate_shortcode(
	'vc_btn',
	'hover_btn_',
	esc_html__( 'Hover Button', 'js_composer' ),
	[
		'exclude' => [ 'css' ],
	],
	[
		'element' => 'hover_add_button',
		'not_empty' => true,
	],
	true
);

foreach ( array_keys( $button ) as $key ) {
	if ( isset( $button[ $key ]['param_name'] ) && 'hover_btn_align' === $button[ $key ]['param_name'] ) {
		$button[ $key ]['std'] = 'center';
		break;
	}
}

$params = array_merge(
	[
		[
			'type' => 'attach_image',
			'heading' => esc_html__( 'Image', 'js_composer' ),
			'param_name' => 'image',
			'value' => '',
			'admin_label' => true,
			'edit_field_class' => 'wpb-grid wpb-grid-column-1',
			'section' => 'initial',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
			'std' => 'center',
			'edit_field_class' => 'wpb-grid wpb-grid-column-2',
			'section' => 'initial',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Reverse blocks', 'js_composer' ),
			'param_name' => 'reverse',
			'edit_field_class' => 'wpb-grid wpb-grid-column-2',
			'section' => 'initial',
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'js_composer' ),
			'admin_label' => true,
			'param_name' => 'primary_title',
			'value' => esc_html__( 'Hover Box Element', 'js_composer' ),
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'param_name' => 'shape',
			'std' => 'rounded',
			'value' => [
				esc_html__( 'Square', 'js_composer' ) => 'square',
				esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
				esc_html__( 'Round', 'js_composer' ) => 'round',
			],
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use custom font', 'js_composer' ),
			'param_name' => 'use_custom_fonts_primary_title',
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'number',
			'heading' => esc_html__( 'Width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => '100',
			'settings' => [
				'min' => 0,
				'units' => array_merge( [ '%' ], array_diff( vc_get_shared( 'default-units' ), [ '%' ] ) ),
			],
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Title alignment', 'js_composer' ),
			'param_name' => 'primary_align',
			'value' => vc_config()->get_text_align_param_value(),
			'std' => 'center',
		],
	],
	$h2_custom_heading,
	[
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Background color', 'js_composer' ),
			'param_name' => 'hover_custom_background',
			'group' => esc_html__( 'Hover', 'js_composer' ),
			'settings' => [
				'default_colorpicker_color' => '#EBEBEB',
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'hover_button',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Add button', 'js_composer' ),
			'group' => esc_html__( 'Hover', 'js_composer' ),
			'param_name' => 'hover_add_button',
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'hover_button',
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Hover title', 'js_composer' ),
			'param_name' => 'hover_title',
			'value' => 'Hover Box Element',
			'group' => esc_html__( 'Hover', 'js_composer' ),
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use custom font', 'js_composer' ),
			'param_name' => 'use_custom_fonts_hover_title',
			'group' => esc_html__( 'Hover', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Hover title alignment', 'js_composer' ),
			'param_name' => 'hover_align',
			'value' => vc_config()->get_text_align_param_value(),
			'std' => 'center',
			'group' => esc_html__( 'Hover', 'js_composer' ),
		],
		[
			'type' => 'textarea_html',
			'heading' => esc_html__( 'Hover content', 'js_composer' ),
			'param_name' => 'content',
			'value' => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ),
			'group' => esc_html__( 'Hover', 'js_composer' ),
		],
	],
	$h4_custom_heading,
	$button
);

return [
	'name' => esc_html__( 'Hover box', 'js_composer' ),
	'base' => 'vc_cta',
	'icon' => 'vc_icon-vc-hoverbox',
	'category' => [ esc_html__( 'Content', 'js_composer' ) ],
	'description' => esc_html__( 'Animated flip box with image and text', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params ),
	'sections' => array_merge(
		[ 'initial', 'hover_button', 'content', 'container', 'general', 'secondary' ],
		vc_config()->get_advanced_sections()
	),
];
