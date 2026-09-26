<?php
/**
 * Configuration file for [vc_column] shortcode of 'Column' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Video background', 'js_composer' ),
		'param_name' => 'video_bg',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'YouTube', 'js_composer' ) => 'yes',
		],
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Parallax', 'js_composer' ),
		'param_name' => 'video_bg_parallax',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'Simple', 'js_composer' ) => 'content-moving',
			esc_html__( 'With fade', 'js_composer' ) => 'content-moving-fade',
		],
		'edit_field_class' => 'vc_col-xs-6',
		'dependency' => [
			'element' => 'video_bg',
			'not_empty' => true,
		],
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Parallax', 'js_composer' ),
		'param_name' => 'parallax',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'Simple', 'js_composer' ) => 'content-moving',
			esc_html__( 'With fade', 'js_composer' ) => 'content-moving-fade',
		],
		'edit_field_class' => 'vc_col-xs-6',
		'dependency' => [
			'element' => 'video_bg',
			'is_empty' => true,
		],
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'YouTube URL', 'js_composer' ),
		'param_name' => 'video_bg_url',
		'value' => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
		// default video url.
		'description' => esc_html__( 'Add YouTube link.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-12',
		'dependency' => [
			'element' => 'video_bg',
			'not_empty' => true,
		],
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Speed', 'js_composer' ),
		'param_name' => 'parallax_speed_video',
		'value' => '1.5',
		'edit_field_class' => 'vc_col-xs-6 wpb_max-w-70',
		'settings' => [
			'min' => 1,
			'step' => 0.1,
		],
		'dependency' => [
			'element' => 'video_bg_parallax',
			'not_empty' => true,
		],
	],
	[
		'type' => 'attach_image',
		'heading' => esc_html__( 'Image', 'js_composer' ),
		'param_name' => 'parallax_image',
		'value' => '',
		'edit_field_class' => 'vc_col-xs-6',
		'dependency' => [
			'element' => 'parallax',
			'not_empty' => true,
		],
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Speed', 'js_composer' ),
		'param_name' => 'parallax_speed_bg',
		'value' => '1.5',
		'edit_field_class' => 'vc_col-xs-6 vc_align-with-labeled-field--top wpb_max-w-70',
		'settings' => [
			'min' => 1,
			'step' => 0.1,
		],
		'dependency' => [
			'element' => 'parallax',
			'not_empty' => true,
		],
	],
];

return [
	'name' => esc_html__( 'Column', 'js_composer' ),
	'icon' => 'icon-wpb-column',
	'is_container' => true,
	'content_element' => false,
	'description' => esc_html__( 'Place content elements inside the column', 'js_composer' ),
	'js_view' => 'VcColumnView',
	'params' => array_merge(
		$params,
		vc_config()->get_css_animation_config( false ),
		vc_config()->get_general_advanced_settings(),
		vc_config()->get_design_options_tab(),
		vc_config()->get_responsive_tab()
	),
];
