<?php

namespace WPDesk\FCF\Pro\Validator\Error;

use WPDesk\FCF\Pro\Settings\Option\SelectedMaxOption;

/**
 * {@inheritdoc}
 */
class SelectedMaxError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		return sprintf(
		/* translators: %1$s: field label, %2$s: maximum of values */
			__( 'The number of selected options for the %1$s is field greater than allowed %2$s.', 'flexible-checkout-fields-pro' ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) ),
			$this->field_data[ SelectedMaxOption::FIELD_NAME ]
		);
	}
}
