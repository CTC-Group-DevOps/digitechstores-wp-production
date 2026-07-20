<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;

/**
 * {@inheritdoc}
 */
class SettingSectionsSectionOption extends OptionAbstract {

	/**
	 * @var string
	 */
	private $section_name = '';

	/**
	 * @var string
	 */
	private $section_label = '';

	/**
	 * @var string
	 */
	private $section_key = '';

	/**
	 * Class constructor.
	 *
	 * @param string $section_name
	 * @param string $section_label
	 */
	public function __construct( string $section_name, string $section_label, string $section_key ) {
		$this->section_name  = $section_name;
		$this->section_label = $section_label;
		$this->section_key   = $section_key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return 'inspire_checkout_fields_' . $this->section_name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_CHECKBOX;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return sprintf( '%1$s (%2$s)', $this->section_label, $this->section_key );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return '0';
	}
}
