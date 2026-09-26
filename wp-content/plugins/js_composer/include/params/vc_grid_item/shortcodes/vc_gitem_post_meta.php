<?php
/**
 * Shortcode vc_gitem_post_meta integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_post_meta'] = [
	'name' => esc_html__( 'Custom field', 'js_composer' ),
	'base' => 'vc_gitem_post_meta',
	'icon' => 'vc_icon-vc-gitem-post-meta',
	'category' => [
		esc_html__( 'Elements', 'js_composer' ),
	],
	'description' => esc_html__( 'Custom fields data from meta values of the post.', 'js_composer' ),
	'params' => [
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Field key name', 'js_composer' ),
			'param_name' => 'key',
			'description' => esc_html__( 'Enter custom field name to retrieve meta data value.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Label', 'js_composer' ),
			'param_name' => 'label',
			'description' => esc_html__( 'Enter label to display before key value.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'value'       => vc_config()->get_text_align_param_value(),
			'std' => 'left',
			'description' => esc_html__( 'Select alignment.', 'js_composer' ),
		],
		vc_config()->get_extra_class_params(),
	],
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
