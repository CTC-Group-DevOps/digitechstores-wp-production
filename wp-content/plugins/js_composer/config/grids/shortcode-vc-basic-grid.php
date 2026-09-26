<?php
/**
 * Configuration file for [vc_basic_grid] shortcode of 'Post grid' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once __DIR__ . '/class-vc-grids-common.php';
$grid_params = VcGridsCommon::getBasicAtts();

return [
	'name' => esc_html__( 'Post grid', 'js_composer' ),
	'base' => 'vc_basic_grid',
	'icon' => 'icon-wpb-application-icon-large',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Posts, pages or custom posts in grid', 'js_composer' ),
	'params' => $grid_params,
	'sections' => array_merge( [ 'items', 'styles', 'filter', 'filter_style', 'pagination_arrows', 'pagination_style', 'pagination_animation', 'general', 'secondary' ], vc_config()->get_advanced_sections() ),
];
