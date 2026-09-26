<?php
/**
 * Shortcode vc_gitem_post_author integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $custom_fonts_width_checkbox_params ,
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_post_author'] = [
	'name' => esc_html__( 'Post author', 'js_composer' ),
	'base' => 'vc_gitem_post_author',
	'icon' => 'vc_icon-vc-gitem-post-author',
	'category' => esc_html__( 'Post', 'js_composer' ),
	'description' => esc_html__( 'Author of current post', 'js_composer' ),
	'params' => array_merge(
		[
			[
				'type' => 'toggle',
				'heading' => esc_html__( 'Add link', 'js_composer' ),
				'param_name' => 'link',
				'description' => esc_html__( 'Add link to author?', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-6',
			],
		],
		$custom_fonts_width_checkbox_params,
		[
			[
				'type' => 'button_group',
				'heading' => esc_html__( 'Alignment', 'js_composer' ),
				'param_name' => 'text_align',
				'value'       => vc_config()->get_text_align_param_value(),
				'std' => 'left',
				'edit_field_class' => 'vc_col-xs-6',
			],
			[
				'type' => 'font_container',
				'param_name' => 'font_container',
				'value' => '',
				'settings' => [
					'fields' => [
						'tag' => 'div',
					],
				],
			],
		],
		[ vc_config()->get_extra_class_params() ],
		vc_config()->get_design_options_tab()
	),
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
