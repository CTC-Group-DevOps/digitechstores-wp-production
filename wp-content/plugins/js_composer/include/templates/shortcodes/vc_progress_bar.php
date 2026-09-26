<?php
/**
 * The template for displaying [vc_progress_bar] shortcode output of 'Progress Bar' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_progress_bar.php.
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
 * @var string $values
 * @var string $units
 * @var string $custombgcolor
 * @var string $customtxtcolor
 * @var string $add_text_shadow
 * @var string $text_shadow_color
 * @var string $options
 * @var string $el_class
 * @var string $el_id
 * @var string $css
 * @var string $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Progress_Bar $this
 */
$title = $values = $units = $css = $custombgcolor = $customtxtcolor = $el_class = $el_id = $css_animation = $striped = $animated = $add_text_shadow = $text_shadow_color = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$atts = $this->convertAttributesToNewProgressBar( $atts );

extract( $atts );
wp_enqueue_script( 'vc_waypoints' );

$el_class = $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );

$bar_options = [];

if ( 'true' === $animated ) {
	$bar_options[] = 'animated';
}
if ( 'true' === $striped ) {
	$bar_options[] = 'striped';
}

if ( '' !== $custombgcolor ) {
	$custombgcolor = ' style="' . esc_attr( vc_get_css_color( 'background-color', $custombgcolor ) ) . '"';
} else {
	$custombgcolor = '';
}

if ( '' !== $customtxtcolor ) {
	$style_text_color = esc_attr( vc_get_css_color( 'color', $customtxtcolor ) );
	$style_text_shadow = '';
	if ( 'true' === $add_text_shadow && $text_shadow_color ) {
		$style_text_shadow = 'text-shadow: 0 -1px 0 ' . esc_attr( $text_shadow_color ) . ';';
	}
	$customtxtcolor = ' style="' . trim( $style_text_color . ' ' . $style_text_shadow ) . '"';
} else {
	$customtxtcolor = '';
}

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'vc_progress_bar ' . esc_attr( $element_class );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );
$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '>';

$output .= wpb_widget_title( [
	'title' => $title,
	'extraclass' => 'wpb_progress_bar_heading',
] );

$values = (array) vc_param_group_parse_atts( $values );
$max_value = 0.0;
$graph_lines_data = [];
foreach ( $values as $data ) {
	$new_line = $data;
	$new_line['value'] = isset( $data['value'] ) ? $data['value'] : 0;
	$new_line['label'] = isset( $data['label'] ) ? $data['label'] : '';
	$new_line['bgcolor'] = $custombgcolor;
	$new_line['txtcolor'] = $customtxtcolor;
	if ( isset( $data['customcolor'] ) ) {
		$new_line['bgcolor'] = ' style="background-color: ' . esc_attr( $data['customcolor'] ) . ';"';
	}
	if ( isset( $data['customtxtcolor'] ) ) {
		$style_text_color = 'color: ' . esc_attr( $data['customtxtcolor'] ) . ';';
		$style_text_shadow = '';
		if ( 'true' === $data['add_text_shadow'] && $data['text_shadow_color'] ) {
			$style_text_shadow = 'text-shadow: 0 -1px 0 ' . $data['text_shadow_color'] . ';';
		}

		$new_line['txtcolor'] = ' style="' . trim( $style_text_color . ' ' . $style_text_shadow ) . '"';
	}

	if ( $max_value < (float) $new_line['value'] ) {
		$max_value = $new_line['value'];
	}
	$graph_lines_data[] = $new_line;
}

foreach ( $graph_lines_data as $line ) {
	$unit = ( '' !== $units ) ? ' <span class="vc_label_units">' . esc_attr( $line['value'] ) . wp_kses_post( $units ) . '</span>' : '';
	$output .= '<div class="vc_general vc_single_bar">';
	$output .= '<small class="vc_label"' . $line['txtcolor'] . '>' . wp_kses_post( $line['label'] ) . $unit . '</small>';
	if ( $max_value > 100.00 ) {
		$percentage_value = (float) $line['value'] > 0 && $max_value > 100.00 ? round( (float) $line['value'] / $max_value * 100, 4 ) : 0;
	} else {
		$percentage_value = $line['value'];
	}
	$output .= '<span class="vc_bar ' . esc_attr( implode( ' ', $bar_options ) ) . '" data-percentage-value="' . esc_attr( $percentage_value ) . '" data-value="' . esc_attr( $line['value'] ) . '"' . $line['bgcolor'] . '></span>';
	$output .= '</div>';
}

$output .= '</div>';

return $output;
