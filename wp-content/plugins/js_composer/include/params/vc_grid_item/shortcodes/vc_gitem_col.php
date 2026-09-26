<?php
/**
 * Shortcode vc_gitem_col integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_col'] = [
	'name' => esc_html__( 'Column', 'js_composer' ),
	'base' => 'vc_gitem_col',
	'icon' => 'icon-wpb-column',
	'weight' => 1000,
	'is_container' => true,
	'allowed_container_element' => false,
	'content_element' => false,
	'controls' => [ 'edit' ],
	'description' => esc_html__( 'Place content elements inside the column', 'js_composer' ),
	'params' => [
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Width', 'js_composer' ),
			'param_name' => 'width',
			'value' => [
				esc_html__( '1/12 - 1 column', 'js_composer' ) => '1/12',
				esc_html__( '1/6 - 2 columns', 'js_composer' ) => '1/6',
				esc_html__( '1/4 - 3 columns', 'js_composer' ) => '1/4',
				esc_html__( '1/3 - 4 columns', 'js_composer' ) => '1/3',
				esc_html__( '5/12 - 5 columns', 'js_composer' ) => '5/12',
				esc_html__( '1/2 - 6 columns', 'js_composer' ) => '1/2',
				esc_html__( '7/12 - 7 columns', 'js_composer' ) => '7/12',
				esc_html__( '2/3 - 8 columns', 'js_composer' ) => '2/3',
				esc_html__( '3/4 - 9 columns', 'js_composer' ) => '3/4',
				esc_html__( '5/6 - 10 columns', 'js_composer' ) => '5/6',
				esc_html__( '11/12 - 11 columns', 'js_composer' ) => '11/12',
				esc_html__( '1/1 - 12 columns', 'js_composer' ) => '1/1',
			],
			'description' => esc_html__( 'Select column width.', 'js_composer' ),
			'std' => '1/1',
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
	],
	'js_view' => 'VcGitemColView',
	'post_type' => Vc_Grid_Item_Editor::postType(),
	'sections' => vc_config()->get_advanced_sections(),
];
