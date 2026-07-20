<?php

namespace WPDesk\FCF\Pro\Validator\Error;

/**
 * {@inheritdoc}
 */
class FieldRequiredError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		return sprintf(
		/* translators: %1$s: field label */
			__( '%1$s is a required field.', 'flexible-checkout-fields-pro' ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) )
		);
	}
}
