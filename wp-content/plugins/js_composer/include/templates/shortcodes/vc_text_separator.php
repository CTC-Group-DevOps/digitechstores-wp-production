<?php
/**
 * The template for displaying [vc_text_separator] shortcode output of 'Text Separator' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_separator.php
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
 * @var string $title_align
 * @var string $el_width
 * @var string $style
 * @var string $title
 * @var string $align
 * @var string $accent_color
 * @var string $el_class
 * @var string $el_id
 * @var string $layout
 * @var string $css
 * @var string $border_width
 * @var string $add_icon
 * Icons:
 * @var string $i_type
 * @var string $i_icon_fontawesome
 * @var string $i_icon_openiconic
 * @var string $i_icon_typicons
 * @var string $i_icon_entypo
 * @var string $i_icon_linecons
 * @var string $i_color
 * @var string $i_custom_color
 * @var string $i_background_style
 * @var string $i_background_color
 * @var string $i_custom_background_color
 * @var string $i_size
 * @var string $i_css_animation
 * @var string $css_animation
 * Shortcode class
 * @var WPBakeryShortcode_Vc_Text_Separator $this
 */

$title_align = $el_width = $style = $title = $align = $color = $accent_color = $inline_css = $el_class = $el_id = $layout = $css = $border_width = $add_icon = $i_type = $i_icon_fontawesome = $i_icon_openiconic = $i_icon_typicons = $i_icon_entypo = $i_icon_linecons = $i_color = $i_custom_color = $i_background_style = $i_background_color = $i_custom_background_color = $i_size = $i_css_animation = $css_animation = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class = 'vc_separator wpb_content_element';

$class .= ( '' !== $title_align ) ? ' vc_' . $title_align : '';
$class .= ( '' !== $style ) ? ' vc_sep_' . $style : '';
$class .= ( '' !== $border_width ) ? ' vc_sep_border_width_' . str_replace( 'px', '', $border_width ) : '';
$class .= ( '' !== $align ) ? ' vc_sep_pos_' . $align : '';

$class .= ( 'separator_no_text' === $layout ) ? ' vc_separator_no_text' : '';

if ( '' !== $accent_color ) {
	if ( 'shadow' === $style ) {
		$inline_css = vc_get_css_color( 'color', $accent_color );
	} elseif ( 'double' === $style ) {
		$inline_css = vc_get_css_color( 'border-color', $accent_color );
	} else {
		$inline_css = vc_get_css_color( 'border-top-color', $accent_color );
	}
	if ( $inline_css ) {
		$inline_css = ' style="' . esc_attr( $inline_css ) . '"';
	}
}

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = $class;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . ' ' . esc_attr( $element_class ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );
$css_class = esc_attr( trim( $css_class ) );
$icon = '';
if ( 'true' === $add_icon ) {
	vc_icon_element_fonts_enqueue( $i_type );
	$icon = $this->getVcIcon( $atts );
}

$content = '';
if ( $icon ) {
	$content = $icon;
}
if ( '' !== $title && 'separator_no_text' !== $layout ) {
	$css_class .= ' vc_separator-has-text';
	$content .= '<h4>' . wp_kses_post( $title ) . '</h4>';
}
$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$wrapper_attributes_html = implode( ' ', $wrapper_attributes );
$inline_style = empty( $el_width ) ? '' : ' style="width: ' . esc_attr( $el_width ) . '%;"';
$separator_html = <<<TEMPLATE
<div class="$css_class" $wrapper_attributes_html $inline_style><span class="vc_sep_holder vc_sep_holder_l"><span$inline_css class="vc_sep_line"></span></span>$content<span class="vc_sep_holder vc_sep_holder_r"><span$inline_css class="vc_sep_line"></span></span>
</div>
TEMPLATE;

return $separator_html;
