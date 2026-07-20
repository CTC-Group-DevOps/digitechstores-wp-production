<?php

/**
 * Base class for a Conditional_Content rule.
 */
class WC_Conditional_Content_Rule_Base {

	/**
	 * @var string The name of the rule.
	 */
	protected $name;

	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * Gets the list of possible values for the rule.
	 *
	 * Override to return the correct list of possible values for your rule object.
	 *
	 * @return array
	 */
	public function get_possible_rule_values(): array {
		return array();
	}

	/**
	 * Gets the list of possible rule operators available for this rule object.
	 *
	 * Override to return your own list of operators.
	 *
	 * @return array
	 */
	public function get_possible_rule_operators(): array {
		return array(
			'==' => __( 'is equal to', 'wc_conditional_content' ),
			'!=' => __( 'is not equal to', 'wc_conditional_content' ),
		);
	}

	/**
	 * Gets the input object type slug for this rule object.
	 *
	 * @return string
	 */
	public function get_condition_input_type(): string {
		return 'Select';
	}

	/**
	 * Checks if the conditions defined for this rule object have been met.
	 *
	 * @param $rule_data
	 * @param null      $arguments
	 *
	 * @return boolean
	 */
	public function is_match( $rule_data, $arguments = null ): bool {
		return false;
	}

	/**
	 * Helper function to wrap the return value from is_match and apply filters or other modifications in sub classes.
	 *
	 * @param boolean    $result The result that should be returned.
	 * @param array      $rule_data The array config object for the current rule.
	 * @param array|null $arguments Arguments passed to the action / filter which triggered this attempted match.
	 *
	 * @return boolean
	 */
	public function return_is_match( $result, $rule_data, $arguments = null ): bool {
		return apply_filters( 'woocommerce_conditional_content_is_match', $result, $rule_data, $arguments );
	}
}
