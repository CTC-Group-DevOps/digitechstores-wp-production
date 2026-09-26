<?php
/**
 * Param type 'autocomplete'
 * Used to create input field with predefined or ajax values suggestions.
 *
 * @see usage example in bottom of this file.Visual Composer AutoComplete Field
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'wp_ajax_vc_get_autocomplete_suggestion', 'vc_get_autocomplete_suggestion' );
/**
 * Handles AJAX requests for autocomplete suggestions.
 *
 * @since 4.4
 */
function vc_get_autocomplete_suggestion() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

	$query = vc_post_param( 'query' );
	$tag = wp_strip_all_tags( vc_post_param( 'shortcode' ) );
	$param_name = vc_post_param( 'param' );
	vc_render_suggestion( $query, $tag, $param_name );
}

/**
 * Renders autocomplete suggestions for a given query.
 *
 * @param string $query
 * @param string $tag
 * @param string $param_name
 *
 * @see vc_filter: vc_autocomplete_{tag}_{param_name}_callback - hook to get suggestions from ajax. (here you need to hook).
 * @since 4.4
 */
function vc_render_suggestion( $query, $tag, $param_name ) {
	$suggestions = apply_filters( 'vc_autocomplete_' . stripslashes( $tag ) . '_' . stripslashes( $param_name ) . '_callback', $query, $tag, $param_name );
	if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
		die( wp_json_encode( $suggestions ) );
	}
	die( wp_json_encode( [] ) ); // if nothing found..
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 *
 * @param array $settings
 * @param string $value
 * @param string $tag
 * @param string $param_id
 *
 * @return mixed rendered template for params in edit form
 * @since 4.4
 * @see vc_filter: vc_autocomplete_render_filter - hook to override output of edit for field "autocomplete"
 */
function vc_autocomplete_form_field( $settings, $value, $tag, $param_id ) {
	$settings_array = isset( $settings['settings'] ) ? $settings['settings'] : [];
	$param_type = $settings['type'];
	$param_name = $settings['param_name'];
	$params = [
		'id'             => wpbakery()->editForm()->get_value_control_id( $param_id, $param_type ),
		'classes'        => wpbakery()->editForm()->get_value_control_classes( $param_name, $param_type ),
		'name'           => $param_name,
		'value'          => $value,
		'tag'            => $tag,
		'settings'       => $settings_array,
		'param_settings' => $settings,
	];
	if ( isset( $settings['settings']['placeholder'] ) ) {
		$params['placeholder'] = $settings['settings']['placeholder'];
	}

	$output = WPB_Form_Field_Autocomplete::get( $params );

	return apply_filters( 'vc_autocomplete_render_filter', $output );
}
