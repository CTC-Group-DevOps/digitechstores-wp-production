<?php

namespace WPDesk\FCF\Pro\Validator\Error;

/**
 * {@inheritdoc}
 */
class ExcludedDateError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		return sprintf(
		/* translators: %s: field label */
			__( '%s field has an excluded date.', 'flexible-checkout-fields-pro' ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) )
		);
	}
}
