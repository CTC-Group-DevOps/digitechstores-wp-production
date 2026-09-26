<?php
/**
 * The template for displaying [vc_toggle] shortcode output.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_toggle.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 *
 * Shortcode attributes
 * @var array|null $atts
 * @var string $content - shortcode content
 * @var string $css
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Toggle $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$title = $el_class = $style = $color = $size = $open = $css_animation = $css = $el_id = '';

$inverted = false;
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

/**
 * Expected variables.
 *
 * @var string $title
 * @var string $el_class
 * @var string $style
 * @var string $color
 * @var string $size
 * @var string $open
 * @var string $css_animation
 * @var string|null $el_id
 */

// checking is color inverted.
$style = str_replace( '_outline', '', $style, $inverted );
$settings = $this->getSettings();

$color_class     = $this->resolveColorClass( $color );
$hex_color_style = $this->buildHexColorStyle( $color, $style, $inverted );

// @since 4.4
$element_class_list = [
	'base' => apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_toggle', $settings['base'], $atts ),
	'style' => 'vc_toggle_' . $style,
	'color' => $color_class,
	'inverted' => ( $inverted ) ? 'vc_toggle_color_inverted' : '',
	'size' => ( $size ) ? 'vc_toggle_size_' . $size : '',
	'open' => ( 'true' === $open ) ? 'vc_toggle_active' : '',
	'extra' => $this->getExtraClass( $el_class ),
	'css_animation' => $this->getCSSAnimation( $css_animation ),
];

$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = trim( implode( ' ', $element_class_list ) );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );

$heading_output = apply_filters( 'wpb_toggle_heading', $this->getHeading( $atts ), [
	'title' => $title,
	'open' => $open,
] );
$output = $hex_color_style . '<div ' . ( ! empty( $el_id ) ? 'id="' . esc_attr( $el_id ) . '"' : '' ) . ' class="' . esc_attr( $element_class ) . ' ' . esc_attr( $css_class ) . '"><div class="vc_toggle_title">' . $heading_output . '<i class="vc_toggle_icon"></i></div><div class="vc_toggle_content">' . wpb_js_remove_wpautop( $content, true ) . '</div></div>';

return $output;
