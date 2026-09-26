<?php
/**
 * The template for displaying [vc_gitem_post_categories] shortcode output.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_gitem_post_categories.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 *
 * @var array $atts
 * @var WPBakeryShortCode_Vc_Gitem_Post_Categories $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

return '{{ post_categories:' . http_build_query( [ 'atts' => $atts ] ) . ' }}';
