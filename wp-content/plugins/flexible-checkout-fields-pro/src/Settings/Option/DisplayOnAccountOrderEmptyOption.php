<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DisplayOnAccountOrderOption;

/**
 * {@inheritdoc}
 */
class DisplayOnAccountOrderEmptyOption extends DisplayOnAccountOrderOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return '';
	}
}
