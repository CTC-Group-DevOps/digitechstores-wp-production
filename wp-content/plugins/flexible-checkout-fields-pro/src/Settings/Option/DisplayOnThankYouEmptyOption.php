<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DisplayOnThankYouOption;

/**
 * {@inheritdoc}
 */
class DisplayOnThankYouEmptyOption extends DisplayOnThankYouOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return '';
	}
}
