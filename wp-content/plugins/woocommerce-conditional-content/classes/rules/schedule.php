<?php

class WC_Conditional_Content_Rule_Schedule_Date extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'schedule_date' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'>=' => __( "starts", 'wc_conditional_content' ),
			'<=' => __( "ends", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Date';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result  = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			if ( ! is_scalar( $rule_data['condition'] ) ) {
				return $this->return_is_match( false, $rule_data, $arguments );
			}

			$date = strtotime( (string) $rule_data['condition'] );

			switch ( $rule_data['operator'] ) {
				case '>=' :
					// current_time() rather than date(), which reads the SERVER
					// timezone. On a UTC host with an Auckland store these differ
					// by a calendar day, so a schedule started or ended a day
					// early or late for every store not on server time.
					$result = strtotime( current_time( 'Y-m-d' ) ) >= $date;
					break;
				case '<=' :
					$result = strtotime( current_time( 'Y-m-d' ) ) <= $date;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Schedule_Day extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'schedule_day' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'==' => __( "is", 'wc_conditional_content' ),
			'!=' => __( "is not", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {

		$options = array(
			'0' => __( 'Sunday', 'wc_conditional_content' ),
			'1' => __( 'Monday', 'wc_conditional_content' ),
			'2' => __( 'Tuesday', 'wc_conditional_content' ),
			'3' => __( 'Wednesday', 'wc_conditional_content' ),
			'4' => __( 'Thursday', 'wc_conditional_content' ),
			'5' => __( 'Friday', 'wc_conditional_content' ),
			'6' => __( 'Saturday', 'wc_conditional_content' )
		);

		return $options;
	}


	public function get_condition_input_type(): string {
		return 'Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			$date = intval( $rule_data['condition'] );

			switch ( $rule_data['operator'] ) {
				case '==' :
					// Server timezone again: a UTC host serving an Auckland
					// store reported Saturday while the store was on Sunday.
					$result = current_time( 'w' ) == $date;
					break;
				case '!=' :
					$result = current_time( 'w' ) != $date;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Schedule_Time extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'schedule_time' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'<=' => __( "is less or equal to", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Text';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			if ( ! is_scalar( $rule_data['condition'] ) ) {
				return $this->return_is_match( false, $rule_data, $arguments );
			}

			$time = strtotime( (string) $rule_data['condition'] );
			// Already site-local, so this is not a timezone fix: date_i18n()
			// runs its output through translation filters, which is a strange
			// thing to then hand to strtotime(). current_time() is the same
			// value without that exposure.
			$now  = strtotime( current_time( 'H:i:s' ) );
			switch ( $rule_data['operator'] ) {
				case '==' :
					$result = $time == $now;
					break;
				case '!=' :
					$result = $time != $now;
					break;
				case '>' :
					$result = $time < $now;
					break;
				case '<' :
					$result = $now < $time;
					break;
				case '>=' :
					$result = $time <= $now;
					break;
				case '<=' :
					$result = $now <= $time;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
