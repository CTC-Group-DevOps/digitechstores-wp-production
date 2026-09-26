<?php
/**
 * Shortcode vc_gitem_post_title integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $post_data_params
 * @var array $custom_fonts_params
 * @var array $vc_gitem_add_link_target_param
 * @var array $custom_fonts_width_checkbox_params
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_post_title'] = [
	'name' => esc_html__( 'Post title', 'js_composer' ),
	'base' => 'vc_gitem_post_title',
	'icon' => 'vc_icon-vc-gitem-post-title',
	'category' => esc_html__( 'Post', 'js_composer' ),
	'description' => esc_html__( 'Title of current post', 'js_composer' ),
	'params' => array_merge(
		[
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Link', 'js_composer' ),
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
			],
			[
				'type' => 'button_group',
				'heading' => esc_html__( 'Alignment', 'js_composer' ),
				'param_name' => 'text_align',
				'value'       => vc_config()->get_text_align_param_value(),
			],
		],
		[
			$vc_gitem_add_link_target_param,
		],
		[
			[
				'type' => 'link',
				'heading' => esc_html__( 'Link', 'js_composer' ),
				'param_name' => 'url',
				'dependency' => [
					'element' => 'link',
					'value' => [ 'custom' ],
				],
				'description' => esc_html__( 'Add custom link.', 'js_composer' ),
			],
		],
		[
			[
				'type' => 'font_container',
				'param_name' => 'font_container',
				'value' => '',
				'settings' => [
					'fields' => [
						'tag' => 'div',
					],
				],
				'edit_field_class' => 'vc_col-xs-6',
			],
		],
		$custom_fonts_width_checkbox_params,
		[ vc_config()->get_extra_class_params() ],
		vc_config()->get_design_options_tab()
	),
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
