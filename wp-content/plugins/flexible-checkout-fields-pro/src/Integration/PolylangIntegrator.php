<?php

namespace WPDesk\FCF\Pro\Integration;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FCFProVendor\WPDesk_Plugin_Info;
use WPDesk\FCF\Pro\Settings\Form\EditSectionForm;
use WPDesk\FCF\Pro\Settings\Option\SectionTitleOption;

/**
 * Supports translation support via the Polylang plugin.
 */
class PolylangIntegrator implements Hookable {

	/**
	 * @var WPDesk_Plugin_Info .
	 */
	private $plugin_info;

	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'init_sections_translation' ] );
	}

	/**
	 * @return void
	 */
	public function init_sections_translation() {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		$sections_settings = get_option( EditSectionForm::SETTINGS_OPTION_NAME, [] );

		foreach ( $sections_settings as $section_settings ) {
			$section_title = $section_settings[ SectionTitleOption::FIELD_NAME ] ?? '';
			if ( $section_title !== '' ) {
				pll_register_string( $section_title, $section_title, $this->plugin_info->get_text_domain() );
			}
		}
	}
}
