<?php
/**
 * Configuration file for [vc_toggle] shortcode of 'FAQ' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$cta_custom_heading = vc_map_integrate_shortcode(
	vc_custom_heading_element_params(),
	'custom_',
	esc_html__( 'Title', 'js_composer' ),
	[
		'exclude' => [
			'source',
			'text',
			'css',
			'link',
		],
	],
	[
		'element' => 'use_custom_heading',
		'value' => 'true',
	],
	true
);

$params = array_merge( [
	[
		'type' => 'textfield',
		'holder' => 'h4',
		'class' => 'vc_toggle_title',
		'heading' => esc_html__( 'Title', 'js_composer' ),
		'param_name' => 'title',
		'value' => esc_html__( 'Toggle title', 'js_composer' ),
		'description' => esc_html__( 'Enter title of toggle block.', 'js_composer' ),
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Custom font', 'js_composer' ),
		'param_name' => 'use_custom_heading',
		'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
	],
	[
		'type' => 'textarea_html',
		'holder' => 'div',
		'class' => 'vc_toggle_content',
		'heading' => esc_html__( 'Content', 'js_composer' ),
		'param_name' => 'content',
		'value' => '<p>' . esc_html__( 'Toggle content goes here, click edit button to change this text.', 'js_composer' ) . '</p>',
		'description' => esc_html__( 'Toggle block content.', 'js_composer' ),
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'param_name' => 'style',
		'value' => vc_get_shared( 'toggle styles' ),
		'description' => esc_html__( 'Select toggle design style.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Size', 'js_composer' ),
		'param_name' => 'size',
		'value' => vc_config()->get_size_param_value( [ 'sm', 'md', 'lg' ] ),
		'std' => 'md',
		'description' => esc_html__( 'Select toggle size.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Icon color', 'js_composer' ),
		'param_name' => 'color',
		'settings' => [
			'default_colorpicker_color' => '#bababa',
		],
		'description' => esc_html__( 'Select icon color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'dependency' => [
			'element' => 'style',
			'value_not_equal_to' => 'text_only',
		],
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Open by default', 'js_composer' ),
		'param_name' => 'open',
		'description' => esc_html__( 'Enable if you want toggle to be open by default.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
], $cta_custom_heading );

return [
	'name' => esc_html__( 'FAQ', 'js_composer' ),
	'base' => 'vc_toggle',
	'icon' => 'icon-wpb-toggle-small-expand',
	'element_default_class' => 'vc_do_toggle',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Toggle element for Q&A block', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_css_animation_config(), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( [ 'margin-bottom' => '22px' ] ) ),
	'js_view' => 'VcToggleView',
	'sections' => array_merge(
		[ 'content', 'container' ],
		vc_config()->get_advanced_sections()
	),
];
