<?php

namespace WPDesk\FCF\Pro\Validator\Error;

use WPDesk\FCF\Pro\Settings\Option\Hour12ClockOption;
use WPDesk\FCF\Pro\Settings\Option\HourMinOption;

/**
 * {@inheritdoc}
 */
class HourMinError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		$min_hour = $this->field_data[ HourMinOption::FIELD_NAME ];
		if ( $this->field_data[ Hour12ClockOption::FIELD_NAME ] ?? false ) {
			$min_hour = date( 'h:i A', strtotime( $min_hour ) );
		}

		return sprintf(
		/* translators: %1$s: field label, %2$s: minimum hour */
			__( 'The minimum time allowed for the %1$s field is %2$s.', 'flexible-checkout-fields-pro' ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) ),
			sprintf( '<strong>%s</strong>', esc_html( $min_hour ) )
		);
	}
}
