<?php
/**
 * Shortcode vc_gitem_image integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $vc_gitem_add_link_param
 * @var array $vc_gitem_add_link_target_param
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	array_merge( $vc_gitem_add_link_param, [
		'edit_field_class' => 'vc_col-xs-6',
	] ),
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'param_name' => 'alignment',
		'value'       => [
			''   => [
				'label' => 'vc-c-justify-left',
				'title' => esc_html__( 'Left', 'js_composer' ),
			],
			'center'   => [
				'label' => 'vc-c-justify-center',
				'title' => esc_html__( 'Center', 'js_composer' ),
			],
			'right'   => [
				'label' => 'vc-c-justify-right',
				'title' => esc_html__( 'Right', 'js_composer' ),
			],
		],
		'std' => '',
		'description' => esc_html__( 'Select image alignment.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Open link in a new tab', 'js_composer' ),
		'param_name' => 'link_target',
		'description' => esc_html__( 'Select link target window.', 'js_composer' ),
		'dependency' => [
			'element' => 'link',
			'value_not_equal_to' => [ 'custom', 'none' ],
		],
	],
	[
		'type' => 'link',
		'heading' => esc_html__( 'Link', 'js_composer' ),
		'param_name' => 'url',
		'dependency' => [
			'element' => 'link',
			'value' => [ 'custom' ],
		],
		'description' => esc_html__( 'Add custom link.', 'js_composer' ),
		'settings' => [
			'is_title' => false,
		],
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Image size', 'js_composer' ),
		'param_name' => 'img_size',
		'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave parameter empty to use "thumbnail" by default.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Image style', 'js_composer' ),
		'param_name' => 'style',
		'value' => vc_get_shared( 'single image styles' ),
		'description' => esc_html__( 'Select image display style.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Border color', 'js_composer' ),
		'param_name' => 'custom_border_color',
		'settings' => [
			'default_colorpicker_color' => '#ebebeb',
		],
		'dependency' => [
			'element' => 'style',
			'value' => [
				'vc_box_border',
				'vc_box_border_circle',
				'vc_box_outline',
				'vc_box_outline_circle',
			],
		],
		'description' => esc_html__( 'Border color.', 'js_composer' ),
	],
	vc_config()->get_extra_class_params(),
	[
		'type' => 'css_editor',
		'param_name' => 'css',
		'group' => esc_html__( 'Design options', 'js_composer' ),
	],
];

$list['vc_gitem_image'] = [
	'name' => esc_html__( 'Post image', 'js_composer' ),
	'base' => 'vc_gitem_image',
	'icon' => 'vc_icon-vc-gitem-image',
	'category' => esc_html__( 'Post', 'js_composer' ),
	'description' => esc_html__( 'Featured image', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_css_animation_config() ),
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
