<?php
/**
 * Param type 'sorted_list'.
 *
 * Used to create sorted list filed.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Renders the form field for sorted_list param.
 *
 * @param array $settings
 * @param string $value
 *
 * @return string
 * @since 4.2
 */
function vc_sorted_list_form_field( $settings, $value ) {
	$options = $settings['options'] ?? [];
	return sprintf(
		'<div class="vc_sorted-list">%s<div class="vc_sorted-list-toolbar">%s</div><ul class="vc_sorted-list-container"></ul></div>',
		WPB_Form_Field_Hidden::get([
			'name' => $settings['param_name'],
			'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] . '_field' ),
			'value' => $value,
			'is_value_escape' => false,
		]),
		vc_sorted_list_parts_list( $options )
	);
}

/**
 * Get html output for sorted list parts.
 *
 * @param array $init_list
 *
 * @return string
 * @since 4.2
 */
function vc_sorted_list_parts_list( $init_list ) {
	$output = '';
	foreach ( $init_list as $control ) {
		$output .= sprintf( '<div class="vc_sorted-list-checkbox"><label><input type="checkbox" name="vc_sorted_list_element" value="%s" data-element="%s" data-subcontrol="%s"> <span>%s</span></label></div>', $control[0], $control[0], count( $control ) > 1 ? htmlspecialchars( wp_json_encode( array_slice( $control, 2 ) ) ) : '', htmlspecialchars( $control[1] ) );
	}

	return $output;
}

/**
 * Parses the value of sorted_list param.
 *
 * @param string $value
 *
 * @return array
 * @since 4.2
 */
function vc_sorted_list_parse_value( $value ) {
	$data = [];
	$split = preg_split( '/\,/', $value );
	foreach ( $split as $v ) {
		$v_split = array_map( 'rawurldecode', preg_split( '/\|/', $v ) );
		$count = count( $v_split );
		if ( $count > 0 ) {
			$data[] = [
				$v_split[0],
				$count > 1 ? array_slice( $v_split, 1 ) : [],
			];
		}
	}

	return $data;
}
