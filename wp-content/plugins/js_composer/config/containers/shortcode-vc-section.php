<?php
/**
 * Configuration file for [vc_section] shortcode of 'Section' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Hide section', 'js_composer' ),
		'param_name' => 'disable_element',
		'description' => esc_html__( 'If checked the section won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
		'value' => [ 'yes' ],
		'std' => '',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Section stretch', 'js_composer' ),
		'param_name' => 'full_width',
		'value' => [
			esc_html__( 'Default', 'js_composer' ) => '',
			esc_html__( 'Stretch section', 'js_composer' ) => 'stretch_row',
			esc_html__( 'Stretch section and content', 'js_composer' ) => 'stretch_row_content',
		],
		'description' => esc_html__( 'Select stretching options for section and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).', 'js_composer' ),
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Minimum height', 'js_composer' ),
		'param_name' => 'min_height',
		'description' => esc_html__( 'Set minimum height for the container.', 'js_composer' ),
		'settings' => [
			'units' => vc_get_shared( 'css-units' ),
		],
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Content position', 'js_composer' ),
		'param_name' => 'vertical_content_position',
		'value' => vc_config()->get_vertical_position_param_value( [ '' ] ),
		'std' => 'top',
		'description' => esc_html__( 'Select content position within section.', 'js_composer' ),
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Full height', 'js_composer' ),
		'param_name' => 'full_height',
		'description' => esc_html__( 'If checked section will be set to full height.', 'js_composer' ),
		'value' => [ 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Video background', 'js_composer' ),
		'param_name' => 'video_bg',
		'description' => esc_html__( 'If checked, video will be used as section background.', 'js_composer' ),
		'value' => [ 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'YouTube link', 'js_composer' ),
		'param_name' => 'video_bg_url',
		'value' => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
		// default video url.
		'description' => esc_html__( 'Add YouTube link.', 'js_composer' ),
		'dependency' => [
			'element' => 'video_bg',
			'not_empty' => true,
		],
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
		'description' => esc_html__( 'Add parallax type background for section.', 'js_composer' ),
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
		'description' => esc_html__( 'Add parallax type background for section (Note: If no image is specified, parallax will use background image from Design Options).', 'js_composer' ),
		'dependency' => [
			'element' => 'video_bg',
			'is_empty' => true,
		],
	],
	[
		'type' => 'attach_image',
		'heading' => esc_html__( 'Image', 'js_composer' ),
		'param_name' => 'parallax_image',
		'value' => '',
		'description' => esc_html__( 'Select image from media library.', 'js_composer' ),
		'dependency' => [
			'element' => 'parallax',
			'not_empty' => true,
		],
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Speed', 'js_composer' ),
		'param_name' => 'parallax_speed_video',
		'value' => '1.5',
		'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'js_composer' ),
		'settings' => [
			'min' => 1,
			'step' => 0.1,
		],
		'dependency' => [
			'element' => 'video_bg_parallax',
			'not_empty' => true,
		],
		'edit_field_class' => 'vc_col-xs-4',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Parallax speed', 'js_composer' ),
		'param_name' => 'parallax_speed_bg',
		'value' => '1.5',
		'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'js_composer' ),
		'settings' => [
			'step' => 0.1,
			'min' => 1,
		],
		'dependency' => [
			'element' => 'parallax',
			'not_empty' => true,
		],
		'edit_field_class' => 'vc_col-xs-4',
	],
];

return [
	'name' => esc_html__( 'Section', 'js_composer' ),
	'is_container' => true,
	'icon' => 'vc_icon-vc-section',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'as_parent' => [
		'only' => 'vc_row,vc_grid_container,vc_flexbox_container',
	],
	'as_child' => [
		'only' => '', // Only root.
	],
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Group multiple rows in section', 'js_composer' ),
	'js_view' => 'VcSectionView',
	'params' => array_merge( $params, vc_config()->get_css_animation_config( false ), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab() ),
];
