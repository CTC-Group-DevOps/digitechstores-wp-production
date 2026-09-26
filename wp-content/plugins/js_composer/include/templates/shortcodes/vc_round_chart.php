<?php
/**
 * The template for displaying [vc_round_chart] shortcode output of 'Round Chart' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_round_chart.php.
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
 * @var string $type
 * @var string $style
 * @var string $legend
 * @var string $animation
 * @var string $tooltips
 * @var string $custom_stroke_color
 * @var string $custom_legend_color
 * @var string $stroke_width
 * @var string $values
 * @var string $css
 * @var string $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Round_Chart $this
 */
$el_class = $el_id = $title = $type = $style = $legend = $animation = $tooltips = $stroke_width = $values = $css = $css_animation = $custom_stroke_color = $custom_legend_color = '';
$legend_position = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'vc_round_chart' );

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'vc_chart vc_round-chart ' . esc_attr( $element_class );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );

$options = [];

if ( ! empty( $legend ) ) {
	$options[] = 'data-vc-legend="1"';
}

if ( ! empty( $tooltips ) ) {
	$options[] = 'data-vc-tooltips="1"';
}

if ( ! empty( $animation ) ) {
	$options[] = 'data-vc-animation="' . esc_attr( str_replace( 'easein', 'easeIn', $animation ) ) . '"';
}

if ( $custom_stroke_color ) {
	$color = $custom_stroke_color;
	$options[] = 'data-vc-stroke-color="' . esc_attr( $color ) . '"';
}

if ( ! empty( $stroke_width ) ) {
	$options[] = 'data-vc-stroke-width="' . esc_attr( $stroke_width ) . '"';
}

$values = (array) vc_param_group_parse_atts( $values );
$data = [];

$labels = [];
$datasets = [];
$dataset_values = [];
$dataset_colors = [];
foreach ( $values as $single_value ) {
	$color = '';
	if ( ! empty( $single_value['custom_color'] ) ) {
		$color = $single_value['custom_color'];
		if ( 'modern' === $style ) {
			$color = [ $color, vc_colorCreator( $color, -7 ) ];
		}
	}

	$labels[] = $single_value['title'] ?? '';
	$dataset_values[] = (int) ( $single_value['value'] ?? 0 );
	$dataset_colors[] = $color;
}

$options[] = 'data-vc-type="' . esc_attr( $type ) . '"';

$legend_color = $atts['custom_legend_color'] ?? '#2a2a2a';

$round_chart_data = [
	'labels' => $labels,
	'datasets' => [
		[
			'data' => $dataset_values,
			'backgroundColor' => $dataset_colors,
		],
	],
];
$options[] = 'data-vc-values="' . esc_attr( wp_json_encode( $round_chart_data ) ) . '"';
$options[] = 'data-vc-legend-color="' . esc_attr( $legend_color ) . '"';
$options[] = 'data-vc-legend-position="' . esc_attr( $legend_position ) . '"';
if ( '' !== $title ) {
	$title = '<h2 class="wpb_heading">' . $title . '</h4>';
}

$canvas_html = '<canvas class="vc_round-chart-canvas" width="1" height="1"></canvas>';
if ( ! empty( $el_id ) ) {
	$options[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '
<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $options ) . '>
	' . wp_kses_post( $title ) . '
	<div class="wpb_wrapper">
		' . $canvas_html . '
	</div>' . '
</div>' . '
';

return $output;
