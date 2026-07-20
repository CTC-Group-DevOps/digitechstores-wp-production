<?php

/**
 * Display or retrieve conditional content
 *
 * @param int $content_id Optional. The content to process.
 * @param bool $echo Optional, default to true.Whether to display or return.
 *
 * @return null|string Null if no content rules match. String if $echo parameter is false and content rules match.
 * @since 1.0.0
 *
 */
function woocommerce_conditional_content( $content_id = 0, $echo = true ) {
	WC_Conditional_Content_Display::instance()->template_display( $content_id, $echo );
}

/**
 * Creates an instance of a rule object
 *
 * @param string $rule_type The slug of the rule type to load.
 *
 * @return WC_Conditional_Content_Rule_Base or superclass of WC_Conditional_Content_Rule_Base
 * @global array $woocommerce_conditional_content_rules
 */
function woocommerce_conditional_content_get_rule_object( string $rule_type ): ?WC_Conditional_Content_Rule_Base {
	global $woocommerce_conditional_content_rules;

	if ( isset( $woocommerce_conditional_content_rules[ $rule_type ] ) ) {
		return $woocommerce_conditional_content_rules[ $rule_type ];
	}

	$class = 'WC_Conditional_Content_Rule_' . $rule_type;
	if ( class_exists( $class ) ) {
		$woocommerce_conditional_content_rules[ $rule_type ] = new $class();

		return $woocommerce_conditional_content_rules[ $rule_type ];
	} else {
		return null;
	}
}

/**
 * Creates an instance of an input object
 *
 * @param string $input_type The slug of the input type to load
 *
 * @return WC_Conditional_Content_Input_Base An instance of an WC_Conditional_Content_Input object type
 * @global $woocommerce_conditional_content_inputs
 */
function woocommerce_conditional_content_get_input_object( $input_type ): ?WC_Conditional_Content_Input_Base {
	global $woocommerce_conditional_content_inputs;

	if ( isset( $woocommerce_conditional_content_inputs[ $input_type ] ) ) {
		return $woocommerce_conditional_content_inputs[ $input_type ];
	}

	$class = 'WC_Conditional_Content_Input_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $input_type ) ) );
	if ( class_exists( $class ) ) {
		$woocommerce_conditional_content_inputs[ $input_type ] = new $class();
	} else {
		$woocommerce_conditional_content_inputs[ $input_type ] = apply_filters( 'woocommerce_conditional_content_get_input_object', $input_type );
	}

	return $woocommerce_conditional_content_inputs[ $input_type ];
}

/**
 * Gets all product categories.
 * This function is a wrapper for get_terms() with the following defaults:
 * - taxonomy = product_cat
 * - hide_empty = false
 * - orderby = name
 * - order = ASC
 * - number = 0
 * @return WP_Term[]|int[]|string[]|string|WP_Error Array of terms, a count thereof as a numeric string,
 *                                                   or WP_Error if any of the taxonomies do not exist.
 *                                                   See the function description for more information.
 */
function wc_conditional_content_get_all_product_categories() {
	return get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => 0,
		)
	);
}

function wc_conditional_content_get_all_product_tags() {
	return get_terms(
		array(
			'taxonomy'   => 'product_tag',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => 0,
		)
	);
}

function wc_conditional_content_get_product_types() {
	return get_terms(
		array(
			'taxonomy'   => 'product_type',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => 0,
		)
	);
}
