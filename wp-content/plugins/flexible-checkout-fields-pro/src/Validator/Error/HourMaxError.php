<?php

namespace WPDesk\FCF\Pro\Validator\Error;

use WPDesk\FCF\Pro\Settings\Option\Hour12ClockOption;
use WPDesk\FCF\Pro\Settings\Option\HourMaxOption;

/**
 * {@inheritdoc}
 */
class HourMaxError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		$max_hour = $this->field_data[ HourMaxOption::FIELD_NAME ];
		if ( $this->field_data[ Hour12ClockOption::FIELD_NAME ] ?? false ) {
			$max_hour = date( 'h:i A', strtotime( $max_hour ) );
		}

		return sprintf(
		/* translators: %1$s: field label, %2$s: maximum hour */
			__( 'The maximum time allowed for the %1$s field is %2$s.', 'flexible-checkout-fields-pro' ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) ),
			sprintf( '<strong>%s</strong>', esc_html( $max_hour ) )
		);
	}
}
