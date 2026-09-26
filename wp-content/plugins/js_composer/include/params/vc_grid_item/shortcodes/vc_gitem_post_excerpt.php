<?php
/**
 * Shortcode vc_gitem_post_excerpt integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $post_data_params
 * @var array $custom_fonts_params
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_post_excerpt'] = [
	'name' => esc_html__( 'Post Excerpt', 'js_composer' ),
	'base' => 'vc_gitem_post_excerpt',
	'icon' => 'vc_icon-vc-gitem-post-excerpt',
	'category' => esc_html__( 'Post', 'js_composer' ),
	'description' => esc_html__( 'Excerpt or manual excerpt', 'js_composer' ),
	'params' => array_merge( $post_data_params, $custom_fonts_params, [ vc_config()->get_extra_class_params() ] ),
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
