<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Tab\DisplayTab;
use WPDesk\FCF\Free\Settings\Option\DisplayOnOption;

/**
 * {@inheritdoc}
 */
class DisplayOnEmptyOption extends DisplayOnOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		return [
			DisplayOnThankYouEmptyOption::FIELD_NAME       => new DisplayOnThankYouEmptyOption(),
			DisplayOnAccountAddressEmptyOption::FIELD_NAME => new DisplayOnAccountAddressEmptyOption(),
			DisplayOnAccountOrderEmptyOption::FIELD_NAME   => new DisplayOnAccountOrderEmptyOption(),
			DisplayOnEmailsEmptyOption::FIELD_NAME         => new DisplayOnEmailsEmptyOption(),
		];
	}
}
