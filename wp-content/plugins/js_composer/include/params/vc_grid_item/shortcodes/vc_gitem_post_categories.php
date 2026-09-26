<?php
/**
 * Shortcode vc_gitem_post_categories integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_post_categories'] = [
	'name' => esc_html__( 'Post categories', 'js_composer' ),
	'base' => 'vc_gitem_post_categories',
	'icon' => 'vc_icon-vc-gitem-post-categories',
	// @todo change icon ?
	'category' => esc_html__( 'Post', 'js_composer' ),
	'description' => esc_html__( 'Categories of current post', 'js_composer' ),
	'params' => [
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Add link', 'js_composer' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to category?', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'alignment',
			'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
			'std' => 'center',
			'description' => esc_html__( 'Select image alignment.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'param_name' => 'category_style',
			'value' => [
				esc_html__( 'None', 'js_composer' ) => ' ',
				esc_html__( 'Comma', 'js_composer' ) => ', ',
				esc_html__( 'Rounded', 'js_composer' ) => 'filled vc_grid-filter-filled-round-all',
				esc_html__( 'Less Rounded', 'js_composer' ) => 'filled vc_grid-filter-filled-rounded-all',
				esc_html__( 'Border', 'js_composer' ) => 'bordered',
				esc_html__( 'Rounded Border', 'js_composer' ) => 'bordered-rounded vc_grid-filter-filled-round-all',
				esc_html__( 'Less Rounded Border', 'js_composer' ) => 'bordered-rounded-less vc_grid-filter-filled-rounded-all',
			],
			'description' => esc_html__( 'Select category display style.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'custom_category_color',
			'settings' => [
				'default_colorpicker_color' => '#EBEBEB',
			],
			'dependency' => [
				'element' => 'category_style',
				'value_not_equal_to' => [
					' ',
					', ',
				],
			],
			'description' => esc_html__( 'Select category color.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Size', 'js_composer' ),
			'param_name' => 'category_size',
			'value' => [
				'xs' => [
					'label' => esc_html__( 'XS', 'js_composer' ),
					'title' => esc_html__( 'Extra Small', 'js_composer' ),
				],
				'sm' => [
					'label' => esc_html__( 'S', 'js_composer' ),
					'title' => esc_html__( 'Small', 'js_composer' ),
				],
				'md' => [
					'label' => esc_html__( 'M', 'js_composer' ),
					'title' => esc_html__( 'Medium', 'js_composer' ),
				],
				'lg' => [
					'label' => esc_html__( 'L', 'js_composer' ),
					'title' => esc_html__( 'Large', 'js_composer' ),
				],
			],
			'std' => 'md',
			'description' => esc_html__( 'Select category size.', 'js_composer' ),
		],
		vc_config()->get_extra_class_params(),
		[
			'type' => 'css_editor',
			'param_name' => 'css',
			'group' => esc_html__( 'Design options', 'js_composer' ),
		],
	],
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
