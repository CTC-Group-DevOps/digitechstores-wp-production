<?php

class WC_Conditional_Content_Rule_Sale_Status extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'sale_status' );
	}

	public function get_possible_rule_operators(): array {

		$operators = array(
		    '==' => __( "is", 'wc_conditional_content' ),
		    '!=' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$options = array(
		    '0' => __( 'Not On Sale', 'wc_conditional_content' ),
		    '1' => __( 'On Sale', 'wc_conditional_content' )
		);

		return $options;
	}

	public function get_condition_input_type(): string {
		return 'Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		global $post;
		$result = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in = $product->is_on_sale();
			if ( $rule_data['operator'] == '==' ) {
				$result = $rule_data['condition'] == 1 ? $in : !$in;
			}

			if ( $rule_data['operator'] == '!=' ) {
				$result = !($rule_data['condition'] == 1 ? $in : !$in);
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Sale_Schedule extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'sale_schedule' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
		    '>=' => __( "starts", 'wc_conditional_content' ),
		    '=<' => __( "ends", 'wc_conditional_content' )
		);
		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Date';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		global $post;
		$product = wc_get_product( get_the_ID() );
		$result = false;
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			// WooCommerce stores these as Unix timestamps, so the strtotime()
			// this used to call on them returned false - and comparing against
			// false meant "starts" always matched and "ends" never did. Read
			// them through the product object, which hands back a WC_DateTime.
			$start_date = $product->get_date_on_sale_from();
			$end_date   = $product->get_date_on_sale_to();

			if ( ! is_scalar( $rule_data['condition'] ) ) {
				return $this->return_is_match( false, $rule_data, $arguments );
			}

			$date = strtotime( (string) $rule_data['condition'] );

			switch ( $rule_data['operator'] ) {
				case '>=' :
					if ( $start_date ) {
						$result = $date >= $start_date->getTimestamp();
					}
					break;
				case '=<' :
					if ( $end_date ) {
						$result = $date <= $end_date->getTimestamp();
					}
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
