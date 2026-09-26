<?php
/**
 * Configuration file for [vc_cta] shortcode of 'Call to Action' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 *
 * @since 4.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$h2_custom_heading = vc_map_integrate_shortcode(
	vc_custom_heading_element_params(),
	'h2_',
	esc_html__( 'Heading', 'js_composer' ),
	[
		'exclude' => [
			'source',
			'text',
			'css',
		],
	],
	[
		'element' => 'use_custom_fonts_h2',
		'value' => 'true',
	],
	true
);

// This is needed to remove custom heading _tag and _align options.
if ( is_array( $h2_custom_heading ) && ! empty( $h2_custom_heading ) ) {
	foreach ( $h2_custom_heading as $key => $param ) {
		if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
			$h2_custom_heading[ $key ]['value'] = '';
		}
	}
}
$h4_custom_heading = vc_map_integrate_shortcode(
	vc_custom_heading_element_params(),
	'h4_',
	esc_html__( 'Subheading', 'js_composer' ),
	[
		'exclude' => [
			'source',
			'text',
			'css',
		],
	],
	[
		'element' => 'use_custom_fonts_h4',
		'value' => 'true',
	],
	true
);


// This is needed to remove custom heading _tag and _align options.
if ( is_array( $h4_custom_heading ) && ! empty( $h4_custom_heading ) ) {
	foreach ( $h4_custom_heading as $key => $param ) {
		if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
			$h4_custom_heading[ $key ]['value'] = 'tag:h4';
		}
	}
}

$button = vc_map_integrate_shortcode(
	'vc_btn',
	'btn_',
	esc_html__( 'Button', 'js_composer' ),
	[
		'exclude' => [ 'css' ],
	],
	[
		'element' => 'add_button',
		'not_empty' => true,
	],
	true
);

array_unshift($button, [
	'type' => 'dropdown',
	'heading' => esc_html__( 'Position', 'js_composer' ),
	'description' => esc_html__( 'Button position relative to call to action block.', 'js_composer' ),
	'param_name' => 'btn_position',
	'value' => [
		esc_html__( 'Top', 'js_composer' ) => 'top',
		esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
		esc_html__( 'Left', 'js_composer' ) => 'left',
		esc_html__( 'Right', 'js_composer' ) => 'right',
	],
	'group' => esc_html__( 'Button', 'js_composer' ),
	'section' => 'general',
	'integrated_shortcode' => 'vc_btn',
	'integrated_shortcode_field' => 'btn_',
	'dependency' => [
		'element'   => 'add_button',
		'not_empty' => true,
	],
]);

$icon = vc_map_integrate_shortcode(
	'vc_icon',
	'i_',
	esc_html__( 'Icon', 'js_composer' ),
	[
		'exclude' => [
			'align',
			'css',
		],
	],
	[
		'element' => 'add_icon',
		'not_empty' => true,
	],
	true
);

array_unshift($icon, [
	'type' => 'toggle',
	'param_name' => 'i_on_border',
	'heading' => esc_html__( 'Place icon on border', 'js_composer' ),
	'description' => esc_html__( 'Display icon on call to action element border.', 'js_composer' ),
	'group' => esc_html__( 'Icon', 'js_composer' ),
	'dependency' => [
		'element' => 'add_icon',
		'not_empty' => true,
	],
	'section' => 'style',
]);
array_unshift($icon, [
	'type' => 'dropdown',
	'heading' => esc_html__( 'Position', 'js_composer' ),
	'description' => esc_html__( 'Icon position relative to call to action block.', 'js_composer' ),
	'param_name' => 'i_position',
	'value' => [
		esc_html__( 'Top', 'js_composer' ) => 'top',
		esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
		esc_html__( 'Left', 'js_composer' ) => 'left',
		esc_html__( 'Right', 'js_composer' ) => 'right',
	],
	'group' => esc_html__( 'Icon', 'js_composer' ),
	'section' => 'style',
	'integrated_shortcode' => 'vc_i',
	'integrated_shortcode_field' => 'i_',
	'dependency' => [
		'element'   => 'add_icon',
		'not_empty' => true,
	],
]);

$params = array_merge(
	[
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Heading', 'js_composer' ),
			'admin_label' => true,
			'param_name' => 'h2',
			'value' => esc_html__( 'Hey! I am first heading line feel free to change me', 'js_composer' ),
			'description' => esc_html__( 'Enter text for heading line.', 'js_composer' ),
			'section' => 'general',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use custom font', 'js_composer' ),
			'param_name' => 'use_custom_fonts_h2',
			'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
			'section' => 'general',
		],
	],
	$h2_custom_heading,
	[
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Subheading', 'js_composer' ),
			'param_name' => 'h4',
			'value' => '',
			'description' => esc_html__( 'Enter text for subheading line.', 'js_composer' ),
			'section' => 'general',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use custom font', 'js_composer' ),
			'param_name' => 'use_custom_fonts_h4',
			'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
			'section' => 'general',
		],
	],
	$h4_custom_heading,
	[
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Text align', 'js_composer' ),
			'param_name' => 'txt_align',
			'value' => vc_config()->get_text_align_param_value(),
			'std' => 'left',
			'description' => esc_html__( 'Select text alignment in "Call to Action" block.', 'js_composer' ),
			'section' => 'general',
		],
		[
			'type' => 'textarea_html',
			'heading' => esc_html__( 'Content', 'js_composer' ),
			'param_name' => 'content',
			'value' => esc_html__( 'I am promo text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ),
			'section' => 'general',
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
			'description' => esc_html__( 'Select call to action shape.', 'js_composer' ),
			'section' => 'style',
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'param_name' => 'style',
			'value' => [
				esc_html__( 'Classic', 'js_composer' ) => 'classic',
				esc_html__( 'Flat', 'js_composer' ) => 'flat',
				esc_html__( 'Outline', 'js_composer' ) => 'outline',
				esc_html__( '3d', 'js_composer' ) => '3d',
				esc_html__( 'Custom', 'js_composer' ) => 'custom',
			],
			'std' => 'classic',
			'description' => esc_html__( 'Select call to action display style.', 'js_composer' ),
			'section' => 'style',
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Background color', 'js_composer' ),
			'param_name' => 'custom_background',
			'description' => esc_html__( 'Select custom background color.', 'js_composer' ),
			'settings' => [
				'default_colorpicker_color' => '#f0f0f0',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'flat', '3d', 'custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'style',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Title color', 'js_composer' ),
			'param_name' => 'custom_text',
			'description' => esc_html__( 'Select custom title color.', 'js_composer' ),
			'settings' => [
				'default_colorpicker_color' => '#111111',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'classic','3d', 'flat', 'outline', 'custom' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'style',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Border color', 'js_composer' ),
			'param_name' => 'custom_border',
			'description' => esc_html__( 'Select custom border color.', 'js_composer' ),
			'settings' => [
				'default_colorpicker_color' => '#d4d4d4',
			],
			'dependency' => [
				'element' => 'style',
				'value' => [ 'custom', 'outline', '3d' ],
			],
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'style',
		],
		[
			'type' => 'range',
			'heading' => esc_html__( 'Width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => '100',
			'settings' => [
				'min' => '0',
				'max' => '100',
				'unit' => '%',
			],
			'description' => esc_html__( 'Select call to action width (percentage).', 'js_composer' ),
			'section' => 'style',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Add button', 'js_composer' ),
			'description' => esc_html__( 'Add button for call to action.', 'js_composer' ),
			'param_name' => 'add_button',
			'section' => 'toggles',
			'edit_field_class' => 'vc_col-xs-6',
		],
	],
	$button,
	[
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Add icon', 'js_composer' ),
			'description' => esc_html__( 'Add icon for call to action.', 'js_composer' ),
			'param_name' => 'add_icon',
			'section' => 'toggles',
			'edit_field_class' => 'vc_col-xs-6',
		],
	],
	$icon
);

$default_values = [
	'padding-top' => '28px',
	'padding-right' => '28px',
	'padding-bottom' => '28px',
	'padding-left' => '28px',
	'margin-bottom' => '35px',
];

return [
	'name' => esc_html__( 'Call to action', 'js_composer' ),
	'base' => 'vc_cta',
	'icon' => 'icon-wpb-call-to-action',
	'element_default_class' => 'vc_do_cta3',
	'category' => [ esc_html__( 'Content', 'js_composer' ) ],
	'description' => esc_html__( 'Catch visitors attention with CTA block', 'js_composer' ),
	'since' => '4.5',
	'params' => array_merge( $params, vc_config()->get_css_animation_config(), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( $default_values ) ),
	'js_view' => 'VcCallToActionView3',
	'sections' => array_merge( [ 'secondary', 'icons', 'general', 'style', 'toggles' ], vc_config()->get_advanced_sections() ),
];
