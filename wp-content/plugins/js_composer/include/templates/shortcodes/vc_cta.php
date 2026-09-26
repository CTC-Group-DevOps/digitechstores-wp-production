<?php
/**
 * The template for displaying [vc_cta] shortcode output of 'Call to Action' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_cta.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 *
 * @var array $atts
 * @var string $content - shortcode content
 * @var WPBakeryShortCode_Vc_Cta $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$this->buildTemplate( $atts, $content );
$container_class = trim( 'vc_cta3-container ' . esc_attr( implode( ' ', $this->getTemplateVariable( 'container-class' ) ) ) );
$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$css_class = trim( 'vc_general ' . esc_attr( $element_class ) . ' ' . esc_attr( implode( ' ', $this->getTemplateVariable( 'css-class' ) ) ) );
$output = '';

$output .= '<section class="' . esc_attr( $container_class ) . '"' . ( ! empty( $atts['el_id'] ) ? ' id="' . esc_attr( $atts['el_id'] ) . '"' : '' );
if ( $this->getTemplateVariable( 'section-css' ) ) {
	$output .= ' style="' . esc_attr( implode( ' ', $this->getTemplateVariable( 'section-css' ) ) ) . '"';
}
$output .= '>';
$output .= '<div class="' . esc_attr( $css_class ) . '"';
if ( $this->getTemplateVariable( 'inline-css' ) ) {
	$output .= ' style="' . esc_attr( implode( ' ', $this->getTemplateVariable( 'inline-css' ) ) ) . '"';
}
$output .= '>'; // div.
$output .= $this->getTemplateVariable( 'icons-top' );
$output .= $this->getTemplateVariable( 'icons-left' );

$output .= '<div class="vc_cta3_content-container">';
$output .= $this->getTemplateVariable( 'actions-top' );
$output .= $this->getTemplateVariable( 'actions-left' );
$output .= '<div class="vc_cta3-content"';
// b.c prior 9.0.
if ( $this->getTemplateVariable( 'text-css' ) ) {
	$output .= ' style="' . esc_attr( implode( ' ', $this->getTemplateVariable( 'text-css' ) ) ) . '"';
}
$output .= '>'; // div.
$output .= '<header class="vc_cta3-content-header">';
$output .= $this->getTemplateVariable( 'heading1' );
$output .= $this->getTemplateVariable( 'heading2' );
$output .= '</header>';
$output .= $this->getTemplateVariable( 'content' );
$output .= '</div>';
$output .= $this->getTemplateVariable( 'actions-bottom' );
$output .= $this->getTemplateVariable( 'actions-right' );
$output .= '</div>';
$output .= $this->getTemplateVariable( 'icons-bottom' );
$output .= $this->getTemplateVariable( 'icons-right' );
$output .= '</div></section>';

return $output;
