<?php
/**
 * The template for displaying [vc_separator] shortcode output of 'Separator' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_separator.php.
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
 * @var string $el_width
 * @var string $style
 * @var string $border_width
 * @var string $accent_color
 * @var string $el_class
 * @var string $el_id
 * @var string $align
 * @var string $css
 * @var string $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Separator $this
 */
$el_width = $style = $border_width = $accent_color = $el_class = $el_id = $align = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];

$class_to_filter = vc_shortcode_custom_css_class( $css, ' ' ) . ' ' . esc_attr( $element_class ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );

$vc_text_separator = wpbakery()->getShortCode( 'vc_text_separator' );
$atts['el_class'] = $css_class;
$atts['layout'] = 'separator_no_text';
if ( is_object( $vc_text_separator ) ) {
	return $vc_text_separator->render( $atts );
}
