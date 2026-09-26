<?php
/**
 * The template for displaying [vc_pie] shortcode output of 'Pie Chart' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_pie.php.
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
 * @var string $title
 * @var string $el_class
 * @var string $el_id
 * @var string $value
 * @var string $units
 * @var string $custom_color
 * @var string $label_value
 * @var string $css
 * @var string $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Pie $this
 */
$title = $el_class = $el_id = $value = $units = $custom_color = $label_value = $css = $css_animation = '';
$atts = $this->convertOldColorsToNew( $atts );
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'vc_pie' );

if ( $custom_color ) {
	$color = $custom_color;
} else {
	$color = '#ebebeb';
}

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'vc_pie_chart ' . esc_attr( $element_class );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );

$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '<div ' . implode( ' ', $wrapper_attributes ) . ' class="' . esc_attr( $css_class ) . '" data-pie-value="' . esc_attr( $value ) . '" data-pie-label-value="' . esc_attr( $label_value ) . '" data-pie-units="' . esc_attr( $units ) . '" data-pie-color="' . esc_attr( $color ) . '">';
$output .= '<div class="wpb_wrapper">';
$output .= '<div class="vc_pie_wrapper">';
$output .= '<span class="vc_pie_chart_back" style="border-color: ' . esc_attr( $color ) . '"></span>';
$output .= '<span class="vc_pie_chart_value"></span>';
$output .= '<canvas width="101" height="101"></canvas>';
$output .= '</div>';

if ( '' !== $title ) {
	$output .= '<h4 class="wpb_heading wpb_pie_chart_heading">' . wp_kses_post( $title ) . '</h4>';
}

$output .= '</div>';
$output .= '</div>';

return $output;
