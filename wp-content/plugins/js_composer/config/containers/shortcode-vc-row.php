<?php
/**
 * Configuration file for [vc_row] shortcode of 'Row' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Row title', 'js_composer' ),
		'param_name' => 'row_title',
		'description' => esc_html__( 'This title is visible only in the admin area and helps site editors differentiate rows.', 'js_composer' ),
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Hide row', 'js_composer' ),
		'param_name' => 'disable_element',
		// Inner param name.
		'description' => esc_html__( 'If enabled the row won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Row stretch', 'js_composer' ),
		'param_name' => 'full_width',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'Stretch row', 'js_composer' ) => 'stretch_row',
			esc_html__( 'Stretch row and content', 'js_composer' ) => 'stretch_row_content',
			esc_html__( 'Stretch row and content (no paddings)', 'js_composer' ) => 'stretch_row_content_no_spaces',
		],
		'description' => esc_html__( 'Select stretching options for row and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).', 'js_composer' ),
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Column gap', 'js_composer' ),
		'param_name' => 'gap',
		'value' => '0',
		'std' => '0',
		'description' => esc_html__( 'Select gap between columns in row.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'settings' => [
			'units' => true,
			'min' => 0,
		],
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Min height', 'js_composer' ),
		'param_name' => 'min_height',
		'description' => sprintf( esc_html__( 'Set minimum height for the container.', 'js_composer' ) ),
		'edit_field_class' => 'vc_col-xs-6',
		'settings' => [
			'min' => 0,
			'units' => true,
		],
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Content position', 'js_composer' ),
		'param_name' => 'content_placement',
		'value' => vc_config()->get_vertical_position_param_value(),
		'std' => '',
		'description' => esc_html__( 'Select content position within columns.', 'js_composer' ),
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Full height', 'js_composer' ),
		'param_name' => 'full_height',
		'description' => esc_html__( 'If enabled, the row will be set to full height.', 'js_composer' ),
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Column position', 'js_composer' ),
		'param_name' => 'columns_placement',
		'value' => [
			esc_html__( 'Middle', 'js_composer' ) => 'middle',
			esc_html__( 'Top', 'js_composer' ) => 'top',
			esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
			esc_html__( 'Stretch', 'js_composer' ) => 'stretch',
		],
		'description' => esc_html__( 'Select columns position within row.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'dependency' => [
			'element' => 'full_height',
			'not_empty' => true,
		],
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Equal height', 'js_composer' ),
		'param_name' => 'equal_height',
		'description' => esc_html__( 'If enabled, columns will be set to equal height.', 'js_composer' ),
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'RTL columns', 'js_composer' ),
		'param_name' => 'rtl_reverse',
		'description' => esc_html__( 'If enabled, columns will be reversed in RTL.', 'js_composer' ),
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Video background', 'js_composer' ),
		'param_name' => 'video_bg',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'YouTube', 'js_composer' ) => 'yes',
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'video_background',
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
		'section' => 'video_background',
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
		'section' => 'video_background',
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
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'video_background',
		'dependency' => [
			'element' => 'video_bg',
			'not_empty' => true,
		],
	],
	[
		'type' => 'attach_image',
		'heading' => esc_html__( 'Image', 'js_composer' ),
		'param_name' => 'parallax_image',
		'value' => '',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'video_background',
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
		'edit_field_class' => 'vc_col-xs-6 wpb_max-w-70',
		'settings' => [
			'min' => 1,
			'step' => 0.1,
		],
		'section' => 'video_background',
		'dependency' => [
			'element' => 'video_bg_parallax',
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
		'section' => 'video_background',
		'dependency' => [
			'element' => 'parallax',
			'not_empty' => true,
		],
	],
];

$sections = vc_config()->get_advanced_sections();
array_splice( $sections, 1, 0, [ 'video_background' ] );

return [
	'name' => esc_html__( 'Row', 'js_composer' ),
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Place content elements inside the row', 'js_composer' ),
	'js_view' => 'VcRowView',
	'params' => array_merge( $params, vc_config()->get_css_animation_config( false ), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab() ),
	'sections' => $sections,
];
