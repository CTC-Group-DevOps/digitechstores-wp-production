<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\CssOption;

/**
 * {@inheritdoc}
 */
class CssSelect2Option extends CssOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return 'form-row-wide select2';
	}
}
