<?php
/**
 * The template for displaying [vc_gitem_row] shortcode output.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_gitem_row.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @var array $atts
 * @var string $css
 * @var string $el_class
 * @var string $position
 * @var string $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Gitem_Row $this
 */
$css = $el_class = $position = '';

extract( shortcode_atts( [
	'css' => '',
	'el_class' => '',
	'position' => 'top',
], $atts ) );

$css_class = 'vc_gitem_row vc_row' . ( strlen( $el_class ) ? ' ' . $el_class : '' ) . vc_shortcode_custom_css_class( $css, ' ' ) . ( $position ? ' vc_gitem-row-position-' . $position : '' );
if ( ! vc_gitem_has_content( $content ) ) {
	return;
}
$output = '<div class="' . esc_attr( $css_class ) . '">' . do_shortcode( $content ) . '</div>';

echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
