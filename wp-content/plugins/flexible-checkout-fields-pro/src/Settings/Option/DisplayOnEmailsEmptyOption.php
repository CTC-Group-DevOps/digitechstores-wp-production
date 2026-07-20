<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DisplayOnEmailsOption;

/**
 * {@inheritdoc}
 */
class DisplayOnEmailsEmptyOption extends DisplayOnEmailsOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return '';
	}
}
