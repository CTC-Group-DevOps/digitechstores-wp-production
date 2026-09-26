<?php
/**
 * Configuration file for [vc_masonry_grid] shortcode of 'Post masonry grid' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once __DIR__ . '/class-vc-grids-common.php';
$masonry_grid_params = VcGridsCommon::getMasonryCommonAtts();

return [
	'name' => esc_html__( 'Post masonry grid', 'js_composer' ),
	'base' => 'vc_masonry_grid',
	'icon' => 'vc_icon-vc-masonry-grid',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Posts, pages or custom posts in masonry grid', 'js_composer' ),
	'params' => $masonry_grid_params,
	'sections' => array_merge( [ 'items', 'styles', 'filter', 'filter_style', 'general', 'secondary' ], vc_config()->get_advanced_sections() ),
];
