<?php

namespace WPDesk\FCF\Pro\Settings\Form;

use WPDesk\FCF\Free\Settings\Form\SettingsPageForm as DefaultSettingsPageForm;
use WPDesk\FCF\Free\Settings\Option\OptionIntegration;
use WPDesk\FCF\Free\Settings\Option\SettingJqueryOption;
use WPDesk\FCF\Pro\Settings\Option\SettingSectionsOption;

/**
 * {@inheritdoc}
 */
class SettingsPageForm extends DefaultSettingsPageForm {

	/**
	 * {@inheritdoc}
	 */
	public function get_form_data( array $form_data, string $form_key = '' ): array {
		$section_fields = [];

		$options = ( new SettingJqueryOption() )->get_children();
		foreach ( $options as $option ) {
			$option_status                                = get_option( $option->get_option_name(), 0 );
			$section_fields[ $option->get_option_name() ] = ( $option_status ) ? '1' : '0';
		}

		$options = ( new SettingSectionsOption() )->get_children();
		foreach ( $options as $option ) {
			$option_status                                = get_option( $option->get_option_name(), 0 );
			$section_fields[ $option->get_option_name() ] = ( $option_status ) ? '1' : '0';
		}

		$option_objects = $this->get_options_list();
		foreach ( $option_objects as $field_option ) {
			$form_data = $field_option['update_field_callback'](
				$form_data,
				$section_fields
			);
		}

		return $form_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_list(): array {
		return [
			( new OptionIntegration( new SettingJqueryOption() ) )->get_field_settings(),
			( new OptionIntegration( new SettingSectionsOption() ) )->get_field_settings(),
		];
	}
}
