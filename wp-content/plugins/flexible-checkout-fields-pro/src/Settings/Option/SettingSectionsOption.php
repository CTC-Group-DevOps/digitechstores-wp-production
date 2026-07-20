<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;

/**
 * {@inheritdoc}
 */
class SettingSectionsOption extends OptionAbstract {

	const FIELD_NAME = 'settings_sections';

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_CHECKBOX_LIST;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'Custom Sections', 'flexible-checkout-fields-pro' );
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_label_tooltip(): string {
		return __( 'Selected sections appear as tabs in the plugin menu.', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		$sections = apply_filters( 'flexible_checkout_fields_all_sections', [] );
		$objects  = [];
		foreach ( $sections as $section_key => $section ) {
			if ( ! ( $section['custom_section'] ?? false ) ) {
				continue;
			}
			$objects[] = new SettingSectionsSectionOption(
				$section['section'],
				$section['title'],
				$section_key
			);
		}
		return $objects;
	}
}
