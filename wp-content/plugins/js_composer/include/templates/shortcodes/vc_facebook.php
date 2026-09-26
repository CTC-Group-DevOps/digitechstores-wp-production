<?php
/**
 * The template for displaying [vc_facebook] shortcode output of 'Facebook Like' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_facebook.php.
 *
 * @note For a frontend editor this element uses $this::contentInline() method to output template.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 * @see WPBakeryShortCode_Vc_Facebook::contentInline()
 *
 * @var array $atts
 * @var WPBakeryShortCode_Vc_Facebook $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$type = $css = $el_class = $el_id = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

/**
 * Extracted variables.
 *
 * @var string $type
 * @var string|null $el_class
 * @var string $el_id
 * @var string $css
 * @var string $css_animation
 */

$url = get_permalink();
$el_class = isset( $el_class ) ? $el_class : '';

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'fb_like wpb_content_element fb_type_' . $type;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . ' ' . esc_attr( $element_class ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );

$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '><iframe src="https://www.facebook.com/plugins/like.php?href='
	. esc_url( $url ) . '&amp;layout='
	. esc_attr( $type ) . '&amp;show_faces=false&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true"></iframe></div>';

return $output;
