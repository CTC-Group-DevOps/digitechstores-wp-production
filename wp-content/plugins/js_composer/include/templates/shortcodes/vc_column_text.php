<?php
/**
 * The template for displaying [vc_column_text] shortcode output of 'Text block' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_column_text.php.
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
 * @var string $el_class
 * @var string $el_id
 * @var string $css_animation
 * @var string $css
 * @var string $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Column_text $this
 */
$el_class = $el_id = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'wpb_text_column ' . esc_attr( $element_class ) . $this->getCSSAnimation( $css_animation );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );
$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '
	<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '>
		<div class="wpb_wrapper">
			' . wpb_js_remove_wpautop( $content, true ) . '
		</div>
	</div>
';

echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
