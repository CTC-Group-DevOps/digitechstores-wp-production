<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Validator\Error\ValidatorError;

interface ValidatorRule {

	/**
	 * @param string $value      .
	 * @param array  $field_data .
	 *
	 * @return ValidatorError|null
	 */
	public function validate_value( string $value, array $field_data );
}
