<?php
/**
 * Shortcode vc_single_image integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $vc_gitem_add_link_param
 * @var array $vc_gitem_add_link_target_param
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_single_image'] = [
	'name' => esc_html__( 'Single Image', 'js_composer' ),
	'base' => 'vc_single_image',
	'icon' => 'icon-wpb-single-image',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Simple image with CSS animation', 'js_composer' ),
	'params' => [
		$vc_gitem_add_link_param,
		$vc_gitem_add_link_target_param,
		[
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
			'param_name' => 'url',
			'dependency' => [
				'element' => 'link',
				'value' => [ 'custom' ],
			],
			'description' => esc_html__( 'Add custom link.', 'js_composer' ),
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Image source', 'js_composer' ),
			'param_name' => 'source',
			'value' => [
				esc_html__( 'Media library', 'js_composer' ) => 'media_library',
				esc_html__( 'External link', 'js_composer' ) => 'external_link',
			],
			'std' => 'media_library',
			'description' => esc_html__( 'Select image source.', 'js_composer' ),
		],
		[
			'type' => 'attach_image',
			'heading' => esc_html__( 'Image', 'js_composer' ),
			'param_name' => 'image',
			'value' => '',
			'description' => esc_html__( 'Select image from media library.', 'js_composer' ),
			'dependency' => [
				'element' => 'source',
				'value' => 'media_library',
			],
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'External link', 'js_composer' ),
			'param_name' => 'custom_src',
			'description' => esc_html__( 'Select external link.', 'js_composer' ),
			'dependency' => [
				'element' => 'source',
				'value' => 'external_link',
			],
		],
		vc_config()->get_css_animation(),
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Image size', 'js_composer' ),
			'param_name' => 'img_size',
			'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave parameter empty to use "thumbnail" by default.', 'js_composer' ),
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'alignment',
			'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
			'std' => '',
			'description' => esc_html__( 'Select image alignment.', 'js_composer' ),
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Image style', 'js_composer' ),
			'param_name' => 'style',
			'value' => vc_get_shared( 'single image styles' ),
			'description' => esc_html__( 'Select image display style.', 'js_composer' ),
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Border color', 'js_composer' ),
			'param_name' => 'border_color',
			'value' => vc_get_shared( 'colors' ),
			'std' => 'grey',
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
			'param_holder_class' => 'vc_colored-dropdown',
		],
		vc_config()->get_extra_class_params(),
		[
			'type' => 'css_editor',
			'param_name' => 'css',
			'group' => esc_html__( 'Design options', 'js_composer' ),
		],
	],
	'post_type' => Vc_Grid_Item_Editor::postType(),
	'sections' => vc_config()->get_advanced_sections(),
];
