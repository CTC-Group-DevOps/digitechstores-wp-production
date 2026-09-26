<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;

/**
 * {@inheritdoc}
 */
class HiddenFieldInfoOption extends OptionAbstract {

	const FIELD_NAME = 'hidden_field_info';

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_tab(): string {
		return GeneralTab::TAB_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_INFO;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		$url = esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-hidden-field-docs' ) );
		return sprintf(
		/* translators: %1$s: anchor opening tag, %2$s: anchor closing tag */
			__( 'The value of this field is set using the filter in functions.php. %1$sRead more%2$s', 'flexible-checkout-fields-pro' ),
			'<a href="' . $url . '" target="_blank" class="fcfArrowLink">',
			'</a>'
		);
	}
}
