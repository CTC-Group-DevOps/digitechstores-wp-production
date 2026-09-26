<?php

namespace WPDesk\FCF\Pro\Validator\Error;

use WPDesk\FCF\Pro\Settings\Option\SelectedMinOption;

/**
 * {@inheritdoc}
 */
class SelectedMinError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		return sprintf(
		/* translators: %1$s: field label, %2$s: minimum of values */
			__( 'The number of selected options for the %1$s is field less than required %2$s.', 'flexible-checkout-fields-pro' ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) ),
			$this->field_data[ SelectedMinOption::FIELD_NAME ]
		);
	}
}
