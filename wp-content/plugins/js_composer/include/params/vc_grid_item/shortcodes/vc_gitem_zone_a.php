<?php
/**
 * Shortcode vc_gitem_zone_a integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $vc_gitem_add_link_target_param
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_zone_a'] = [
	'name' => esc_html__( 'Normal', 'js_composer' ),
	'base' => 'vc_gitem_zone_a',
	'content_element' => false,
	'is_container' => true,
	'show_settings_on_create' => false,
	'icon' => 'icon-wpb-gitem-zone',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'controls' => [ 'edit' ],
	'as_parent' => [ 'only' => 'vc_gitem_row' ],
	'js_view' => 'VcGitemZoneView',
	'params' => array_merge(
		[
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Height ratio', 'js_composer' ),
				'param_name' => 'height_mode',
				'value' => [
					'1:1' => '1-1',
					esc_html__( 'Original', 'js_composer' ) => 'original',
					'4:3' => '4-3',
					'3:4' => '3-4',
					'16:9' => '16-9',
					'9:16' => '9-16',
					esc_html__( 'Custom', 'js_composer' ) => 'custom',
				],
				'description' => esc_html__( 'Sizing proportions for height and width. Select "Original" to scale image without cropping.', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-6',
			],
			apply_filters( 'vc_gitem_add_link_param', [
				'type' => 'dropdown',
				'heading' => esc_html__( 'URL', 'js_composer' ),
				'param_name' => 'link',
				'value' => [
					esc_html__( 'None', 'js_composer' ) => 'none',
					esc_html__( 'Post link', 'js_composer' ) => 'post_link',
					esc_html__( 'Post author', 'js_composer' ) => 'post_author',
					esc_html__( 'Large image', 'js_composer' ) => 'image',
					esc_html__( 'Large image (prettyPhoto)', 'js_composer' ) => 'image_lightbox',
					esc_html__( 'Full image', 'js_composer' ) => 'image_full',
					esc_html__( 'Full image (prettyPhoto)', 'js_composer' ) => 'image_full_lightbox',
					esc_html__( 'Custom', 'js_composer' ) => 'custom',
				],
				'description' => esc_html__( 'Select link option.', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-6',
			] ),
			[
				'type' => 'number',
				'heading' => esc_html__( 'Height', 'js_composer' ),
				'param_name' => 'height',
				'dependency' => [
					'element' => 'height_mode',
					'value' => [ 'custom' ],
				],
				'settings' => [
					'units' => true,
					'min' => 0,
				],
				'description' => esc_html__( 'Enter custom height.', 'js_composer' ),
			],
		],
		[
			$vc_gitem_add_link_target_param,
			[
				'type' => 'link',
				'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
				'param_name' => 'url',
				'dependency' => [
					'element' => 'link',
					'value' => [ 'custom' ],
				],
				'description' => esc_html__( 'Add custom link.', 'js_composer' ),
			],
			[
				'type' => 'toggle',
				'heading' => esc_html__( 'Use featured image on background', 'js_composer' ),
				'param_name' => 'featured_image',
				'value' => [ 'yes' ],
				'std' => '',
				'description' => esc_html__( 'Note: Featured image overwrites background image and color from "Design Options".', 'js_composer' ),
			],
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Image size', 'js_composer' ),
				'param_name' => 'img_size',
				'value' => 'large',
				'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'js_composer' ),
				'dependency' => [
					'element' => 'featured_image',
					'not_empty' => true,
				],
			],
			[
				'type' => 'css_editor',
				'param_name' => 'css',
				'group' => esc_html__( 'Design options', 'js_composer' ),
			],
			vc_config()->get_extra_class_params( false ),
		]
	),
	'post_type' => Vc_Grid_Item_Editor::postType(),
	'sections' => vc_config()->get_advanced_sections(),
];
