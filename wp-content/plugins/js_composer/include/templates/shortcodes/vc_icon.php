<?php
/**
 * The template for displaying [vc_icon] shortcode output of 'Icon' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_icon.php.
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
 * @var string $type
 * @var string $icon_fontawesome
 * @var string $icon_openiconic
 * @var string $icon_typicons
 * @var string $icon_entypo
 * @var string $icon_linecons
 * @var string $icon_monosocial
 * @var string $icon_material
 * @var string $icon_pixelicons
 * @var string $custom_color
 * @var string $background_style
 * @var string $custom_background_color
 * @var string $size
 * @var string $align
 * @var string $el_class
 * @var string $el_id
 * @var string $link
 * @var string $css_animation
 * @var string $css
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Icon $this
 */
$type = $icon_fontawesome = $icon_openiconic = $icon_typicons = $icon_entypo = $icon_linecons = $icon_monosocial = $icon_material = $icon_pixelicons = $custom_color = $background_style = $custom_background_color = $size = $align = $el_class = $el_id = $link = $css_animation = $css = $rel = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = '';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . ' ' . esc_attr( $element_class ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );

// Enqueue needed icon font.
vc_icon_element_fonts_enqueue( $type );

$url = vc_build_link( $link );
$has_style = false;
if ( strlen( $background_style ) > 0 ) {
	$has_style = true;
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$background_style .= ' vc_icon_element-outline'; // if we use outline style it is border in css.
	} else {
		$background_style .= ' vc_icon_element-background';
	}
}

$icon_class = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';

$style = '';
if ( $custom_background_color ) {
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$style = 'border-color:' . $custom_background_color;
	} else {
		$style = 'background-color:' . $custom_background_color;
	}
}
$style = $style ? ' style="' . esc_attr( $style ) . '"' : '';
$rel = '';
if ( ! empty( $url['rel'] ) ) {
	$rel = ' rel="' . esc_attr( $url['rel'] ) . '"';
}
$output = '';
$output .= '<div' . ( ! empty( $el_id ) ? ' id="' . esc_attr( $el_id ) . '"' : '' ) . ' class="vc_icon_element vc_icon_element-outer' . ( strlen( $css_class ) > 0 ? ' ' . esc_attr( trim( $css_class ) ) : '' ) . ' vc_icon_element-align-' . esc_attr( $align );
if ( $has_style ) {
	$output .= ' vc_icon_element-have-style';
}
$output .= '"><div class="vc_icon_element-inner ';
if ( $has_style ) {
	$output .= ' vc_icon_element-have-style-inner';
}
$output .= ' vc_icon_element-size-' . esc_attr( $size ) . ' vc_icon_element-style-' . esc_attr( $background_style ) . '" ' . $style . '><span class="vc_icon_element-icon ' . esc_attr( $icon_class ) . '" ' . ( $custom_color ? 'style="color:' . esc_attr( $custom_color ) . ' !important"' : '' ) . '></span>';

if ( strlen( $link ) > 0 && strlen( $url['url'] ) > 0 ) {
	$output .= '<a class="vc_icon_element-link" href="' . esc_url( $url['url'] ) . '" ' . $rel . ' title="' . esc_attr( $url['title'] ) . '" target="' . ( strlen( $url['target'] ) > 0 ? esc_attr( $url['target'] ) : '_self' ) . '"></a>';
}
$output .= '</div></div>';

return $output;
