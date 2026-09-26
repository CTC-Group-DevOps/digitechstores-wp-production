<?php

namespace WPDesk\FCF\Pro\Field\Attr;

/**
 * Handles the argument added to the field settings.
 */
interface Attr {

	/**
	 * @param array $field_data .
	 *
	 * @return bool
	 */
	public function is_available( array $field_data ): bool;

	/**
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * @param array $field_data .
	 *
	 * @return string|null
	 */
	public function get_value( array $field_data );
}
