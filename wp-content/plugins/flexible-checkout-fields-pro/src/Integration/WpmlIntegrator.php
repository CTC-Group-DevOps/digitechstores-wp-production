<?php

namespace WPDesk\FCF\Pro\Integration;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FCFProVendor\WPDesk_Plugin_Info;
use WPDesk\FCF\Pro\Settings\Form\EditSectionForm;
use WPDesk\FCF\Pro\Settings\Option\SectionTitleOption;

/**
 * Supports translation support via the WPML plugin.
 */
class WpmlIntegrator implements Hookable {

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
		add_action( 'admin_init', [ $this, 'init_sections_translation' ] );
	}

	/**
	 * @return void
	 */
	public function init_sections_translation() {
		if ( ! function_exists( 'icl_register_string' ) ) {
			return;
		}

		$sections_settings = get_option( EditSectionForm::SETTINGS_OPTION_NAME, [] );
		$icl_language_code = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : get_bloginfo( 'language' );

		foreach ( $sections_settings as $section_settings ) {
			$section_title = $section_settings[ SectionTitleOption::FIELD_NAME ] ?? '';
			if ( $section_title !== '' ) {
				icl_register_string( $this->plugin_info->get_text_domain(), $section_title, $section_title, false, $icl_language_code );
			}
		}
	}
}
