<?php
/**
 * The template for displaying [vc_line_chart] shortcode output of 'Line Chart' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_line_chart.php.
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
 * @var string $x_values
 * @var string $values
 * @var string $css
 * @var string $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Line_Chart $this
 */
$el_class = $el_id = $title = $type = $legend = $style = $tooltips = $animation = $x_values = $values = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'vc_line_chart' );

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'vc_chart vc_line-chart ' . esc_attr( $element_class );
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

$values = (array) vc_param_group_parse_atts( $values );
$data = [
	'labels' => explode( ';', trim( $x_values, ';' ) ),
	'datasets' => [],
];

foreach ( $values as $k => $v ) {
	if ( ! empty( $v['custom_color'] ) ) {
		$color = $v['custom_color'];
		$highlight = vc_colorCreator( $v['custom_color'], - 10 );
	} else {
		$color = '#ebebeb';
		$highlight = '#ebebeb';
	}

	if ( 'modern' === $style ) {
		$stroke_color = is_array( $color ) ? end( $color ) : $color;
		$highlight_stroke_color = vc_colorCreator( $stroke_color, - 7 );
	} else {
		$stroke_color = $color;
		$highlight_stroke_color = $highlight;
	}

	$data['datasets'][] = [
		'label' => isset( $v['title'] ) ? $v['title'] : '',
		'borderColor' => $stroke_color,
		'backgroundColor' => ( 'modern' === $style ? [
			$stroke_color,
			$highlight_stroke_color,
		] : $stroke_color ),
		'data' => explode( ';', isset( $v['y_values'] ) ? trim( $v['y_values'], ';' ) : '' ),
	];
}

$options[] = 'data-vc-type="' . esc_attr( $type ) . '"';
$options[] = 'data-vc-values="' . htmlentities( wp_json_encode( $data ) ) . '"';

if ( '' !== $title ) {
	$title = '<h2 class="wpb_heading">' . $title . '</h4>';
}

$canvas_html = '<canvas class="vc_line-chart-canvas" width="1" height="1"></canvas>';

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
