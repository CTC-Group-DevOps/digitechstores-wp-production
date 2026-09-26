<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DisplayOnAccountAddressOption;

/**
 * {@inheritdoc}
 */
class DisplayOnAccountAddressEmptyOption extends DisplayOnAccountAddressOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return '';
	}
}
