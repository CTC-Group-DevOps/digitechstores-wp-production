<?php

class WC_Conditional_Content_Rule_General_Always extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'general_always' );
	}

	public function get_possible_rule_operators(): array {
		return [];
	}

	public function get_possible_rule_values(): array {
		return [];
	}

	public function get_condition_input_type(): string {
		return 'Html_Always';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		return true;
	}

}



