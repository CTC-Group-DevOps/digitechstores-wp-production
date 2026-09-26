<?php

namespace WPDesk\FCF\Pro\Validator;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Field\Type\FileType;
use WPDesk\FCF\Pro\Field\Type\MultiCheckboxType;
use WPDesk\FCF\Pro\Field\Type\TimeType;
use WPDesk\FCF\Pro\Validator\Rule\ArrayValueRequiredRule;
use WPDesk\FCF\Pro\Validator\Rule\DateFormatRule;
use WPDesk\FCF\Pro\Validator\Rule\DatesLimitRule;
use WPDesk\FCF\Pro\Validator\Rule\DaysAfterRule;
use WPDesk\FCF\Pro\Validator\Rule\DaysBeforeRule;
use WPDesk\FCF\Pro\Validator\Rule\ExcludedDatesRule;
use WPDesk\FCF\Pro\Validator\Rule\ExcludedWeekDaysRule;
use WPDesk\FCF\Pro\Validator\Rule\HourMaxRule;
use WPDesk\FCF\Pro\Validator\Rule\HourMinRule;
use WPDesk\FCF\Pro\Validator\Rule\SelectedMaxRule;
use WPDesk\FCF\Pro\Validator\Rule\SelectedMinRule;
use WPDesk\FCF\Pro\Validator\Rule\TodayMaxHourRule;
use WPDesk\FCF\Pro\Validator\Rule\ValidatorRule;

/**
 * Supports the validation of field values.
 */
class FieldValidator implements Hookable {

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'flexible_checkout_fields_validate_' . MultiCheckboxType::FIELD_TYPE, [ $this, 'validate_multicheckbox_field' ], 10, 2 );
		add_action( 'flexible_checkout_fields_validate_' . DateType::FIELD_TYPE, [ $this, 'validate_date_field' ], 10, 2 );
		add_action( 'flexible_checkout_fields_validate_' . TimeType::FIELD_TYPE, [ $this, 'validate_time_field' ], 10, 2 );
		add_action( 'flexible_checkout_fields_validate_' . FileType::FIELD_TYPE, [ $this, 'validate_file_field' ], 10, 2 );
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function validate_multicheckbox_field( string $value, array $field_data ) {
		$this->validate_field_value( new SelectedMinRule(), $value, $field_data )
		&& $this->validate_field_value( new SelectedMaxRule(), $value, $field_data );
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function validate_date_field( string $value, array $field_data ) {
		$this->validate_field_value( new DateFormatRule(), $value, $field_data )
		&& $this->validate_field_value( new DatesLimitRule(), $value, $field_data )
		&& $this->validate_field_value( new DaysAfterRule(), $value, $field_data )
		&& $this->validate_field_value( new DaysBeforeRule(), $value, $field_data )
		&& $this->validate_field_value( new ExcludedDatesRule(), $value, $field_data )
		&& $this->validate_field_value( new ExcludedWeekDaysRule(), $value, $field_data )
		&& $this->validate_field_value( new TodayMaxHourRule(), $value, $field_data );
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function validate_time_field( string $value, array $field_data ) {
		$this->validate_field_value( new HourMinRule(), $value, $field_data )
		&& $this->validate_field_value( new HourMaxRule(), $value, $field_data );
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function validate_file_field( string $value, array $field_data ) {
		$this->validate_field_value( new ArrayValueRequiredRule(), $value, $field_data );
	}

	private function validate_field_value( ValidatorRule $rule, string $value, array $field_data ): bool {
		$validator_error = $rule->validate_value( $value, $field_data );
		if ( $validator_error === null ) {
			return true;
		}

		wc_add_notice( $validator_error->get_error_message(), 'error' );
		return false;
	}
}
