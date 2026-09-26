<?php
/**
 * The template for displaying [vc_accordion_tab] shortcode.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_accordion_tab.php
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 *
 * @deprecated
 *
 * @var array $atts
 * @var string $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Accordion_tab $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$title = $el_id = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

/**
 * Extract variables.
 *
 * @var string $title
 * @var string $el_id
 */

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion_section group', $this->getSettings()['base'], $atts );

$output = '
	<div ' . ( ! empty( $el_id ) ? "id='" . esc_attr( $el_id ) . "'" : '' ) . 'class="' . esc_attr( $css_class ) . '">
		<h3 class="wpb_accordion_header ui-accordion-header"><a href="#' . sanitize_title( $title ) . '">' . esc_html( $title ) . '</a></h3>
		<div class="wpb_accordion_content ui-accordion-content vc_clearfix">
			' . ( ( '' === trim( $content ) ) ? esc_html__( 'Empty section. Edit page to add content here.', 'js_composer' ) : wpb_js_remove_wpautop( $content ) ) . '
		</div>
	</div>
';

return $output;
