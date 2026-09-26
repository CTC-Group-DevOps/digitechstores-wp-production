<?php

namespace WPDesk\FCF\Pro\Field;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Field\Attr\Attr;
use WPDesk\FCF\Pro\Field\Attr\DateFormatAttr;
use WPDesk\FCF\Pro\Field\Attr\DateMaxAttr;
use WPDesk\FCF\Pro\Field\Attr\DateMinAttr;
use WPDesk\FCF\Pro\Field\Attr\DatesDisabled;
use WPDesk\FCF\Pro\Field\Attr\DaysDisabled;
use WPDesk\FCF\Pro\Field\Attr\Hour12Attr;
use WPDesk\FCF\Pro\Field\Attr\HourMaxAttr;
use WPDesk\FCF\Pro\Field\Attr\HourMinAttr;
use WPDesk\FCF\Pro\Field\Attr\MaxDatesAttr;
use WPDesk\FCF\Pro\Field\Attr\MinuteStepAttr;
use WPDesk\FCF\Pro\Field\Attr\WeekStartAttr;

/**
 * Adds additional custom attributes for FCF fields.
 */
class FieldCustomAttrs implements Hookable {

	/**
	 * @var Attr[]
	 */
	private $attrs = [];

	public function __construct() {
		$this->attrs[] = new DateFormatAttr();
		$this->attrs[] = new DateMinAttr();
		$this->attrs[] = new DateMaxAttr();
		$this->attrs[] = new DatesDisabled();
		$this->attrs[] = new DaysDisabled();
		$this->attrs[] = new MaxDatesAttr();
		$this->attrs[] = new WeekStartAttr();
		$this->attrs[] = new Hour12Attr();
		$this->attrs[] = new MinuteStepAttr();
		$this->attrs[] = new HourMinAttr();
		$this->attrs[] = new HourMaxAttr();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'flexible_checkout_fields_custom_attributes', [ $this, 'set_fields_custom_attributes' ], 10, 2 );
	}

	/**
	 * @internal
	 */
	public function set_fields_custom_attributes( array $custom_attrs, array $field_data ): array {
		foreach ( $this->attrs as $attr ) {
			if ( $attr->is_available( $field_data ) && ! is_null( $attr->get_value( $field_data ) ) ) {
				$custom_attrs[ $attr->get_name() ] = $attr->get_value( $field_data );
			}
		}

		return $custom_attrs;
	}
}
