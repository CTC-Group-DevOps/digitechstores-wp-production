<?php
/**
 * The template for displaying [vc_empty_space] shortcode output of 'Empty space' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_empty_space.php.
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
 * @var string $height
 * @var string $el_class
 * @var string $el_id
 * @var string $css
 * @var string $output
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Empty_space $this
 */
$height = $el_class = $el_id = $css = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$height = wpb_format_with_css_unit( $height );
if ( empty( $height ) ) {
	$height = wpb_format_with_css_unit( 0 );
}

$inline_css = ( (float) $height >= 0.0 ) ? ' style="height: ' . esc_attr( $height ) . '"' : '';

$class = 'vc_empty_space ' . $this->getExtraClass( $el_class ) . vc_shortcode_custom_css_class( $css, ' ' );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->getSettings()['base'], $atts );
$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

$output .= '<div class="' . esc_attr( trim( $css_class ) ) . '" ';
$output .= implode( ' ', $wrapper_attributes ) . ' ' . $inline_css;
$output .= '><span class="vc_empty_space_inner"></span></div>';

return $output;
