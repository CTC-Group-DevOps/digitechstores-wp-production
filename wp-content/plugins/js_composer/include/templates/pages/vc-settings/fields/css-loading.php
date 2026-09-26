<?php
/**
 * CSS loading field template.
 *
 * @var string $current_value Current CSS loading value.
 * @var string $field_prefix  Field name prefix.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$field_name = $field_prefix . 'css_loading';
$hybrid_description = vc_get_template( 'editors/partials/param-info.tpl.php', [
	'description' => esc_html__( 'Apply optimized CSS loading to new pages and posts only', 'js_composer' ),
] );
?>
<div class="wpb_radio-container wpb_radio-container-vertical wpb-css-loading-options">
	<?php
	WPB_Form_Field_Radio::render( [
		'id'          => $field_prefix . 'css_loading_hybrid',
		'name'        => $field_name,
		'value'       => 'hybrid',
		'label'       => esc_html__( 'Hybrid CSS', 'js_composer' ),
		'checked'     => 'hybrid' === $current_value,
		'description' => $hybrid_description,
	] );
	WPB_Form_Field_Radio::render( [
		'id'      => $field_prefix . 'css_loading_optimized',
		'name'    => $field_name,
		'value'   => 'optimized',
		'label'   => esc_html__( 'Optimized CSS', 'js_composer' ),
		'checked' => 'optimized' === $current_value,
	] );
	WPB_Form_Field_Radio::render( [
		'id'      => $field_prefix . 'css_loading_legacy',
		'name'    => $field_name,
		'value'   => 'legacy',
		'label'   => esc_html__( 'Legacy CSS', 'js_composer' ),
		'checked' => 'legacy' === $current_value,
	] );
	?>
</div>
