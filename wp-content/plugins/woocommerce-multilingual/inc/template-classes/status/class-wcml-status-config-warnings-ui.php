<?php

use WPML\Core\Twig_SimpleFunction;

class WCML_Status_Config_Warnings_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $sitepress;
	private $sitepress_settings;

	public function __construct( $sitepress, $woocommerce_wpml, $sitepress_settings ) {
		parent::__construct();

		$this->woocommerce_wpml   = $woocommerce_wpml;
		$this->sitepress          = $sitepress;
		$this->sitepress_settings = $sitepress_settings;
	}

	public function init_twig_functions() {
		$function = new Twig_SimpleFunction( 'get_flag_url', [ $this, 'get_flag_url' ] );

		$twig = $this->get_twig();
		$twig->addFunction( $function );
	}

	public function get_model() {
		$this->init_twig_functions();

		$wcml_dependencies = \WPML\Container\make( WCML_Dependencies::class );

		$model = [
			'default_language'    => $this->sitepress->get_default_language(),
			'miss_slug_lang'      => $this->get_missed_product_slug_translations_languages(),
			'prod_slug'           => $this->woocommerce_wpml->strings->product_permalink_slug(),
			'dismiss_non_default' => isset( $this->woocommerce_wpml->settings['dismiss_non_default_language_warning'] ),
			'xml_config_errors'   => ! empty( $wcml_dependencies->xml_config_errors ) ? $wcml_dependencies->xml_config_errors : false,
			'slugs_tab'           => \WCML\Utilities\AdminUrl::getStoreURLTab(),
			'st_lang'             => $this->sitepress_settings['st']['strings_language'],
			'not_en_doc_page'     => 'https://wpml.org/?page_id=355545',
			'strings'             => [
				/* translators: Heading of the block on the Status screen that lists settings needing attention. */
				'conf'            => __( 'Configuration Warnings', 'woocommerce-multilingual' ),
				/* translators: Warning on the Status screen; the list of languages follows the colon. */
				'base_not_trnsl'  => __( 'Your product permalink base is not translated to:', 'woocommerce-multilingual' ),
				/* translators: Link on the Status screen that opens the store URLs screen. Verb phrase, imperative. */
				'trsl_urls'       => __( 'Translate URLs', 'woocommerce-multilingual' ),
				/* translators: Warning on the Status screen about the site's default language. */
				'run_not_en'      => __( 'Running WPML Multilingual & Multicurrency for WooCommerce with default language other than English.', 'woocommerce-multilingual' ),
				/* translators: Second line of the Status warning about a default language other than English; "This" is that setup. */
				'url_problems'    => __( 'This may cause problems with URLs in different languages.', 'woocommerce-multilingual' ),
				/* translators: Link on the Status screen that opens WPML's language settings. Verb phrase, imperative. */
				'change_def_lang' => __( 'Change default language', 'woocommerce-multilingual' ),
				/* translators: Introduction of the configuration warnings on the Status screen. */
				'attent_sett'     => __( 'There are some settings that require careful attention.', 'woocommerce-multilingual' ),
				/* translators: Warning on the Status screen that the plugin's own configuration file has been overridden. Keep the file name in English. */
				'over_sett'       => __( 'Some settings from the WPML Multilingual & Multicurrency for WooCommerce wpml-config.xml file have been overwritten.', 'woocommerce-multilingual' ),
				/* translators: %s is link to Multilingual Content Setup */
				'check_conf'      => __( 'You should check WPML configuration files added by other plugins or manual settings on the %s section.', 'woocommerce-multilingual' ),
				/* translators: Link text on the Status screen; it is the name of a WPML screen and is translated the same way there. */
				'cont_set'        => '<a href="' . admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/settings' ) . '">' . __( 'Multilingual Content Setup', 'woocommerce-multilingual' ) . '</a>',
			],
		];

		return $model;

	}

	private function get_missed_product_slug_translations_languages() {

		$slug_settings  = $this->sitepress->get_setting( 'posts_slug_translation' );
		$miss_slug_lang = [];

		if ( ! empty( $slug_settings['on'] ) ) {
			$slug = $this->woocommerce_wpml->strings->product_permalink_slug();

			if ( has_filter( 'wpml_slug_translation_available' ) ) {
				$slug_translation_languages = apply_filters( 'wpml_get_slug_translation_languages', [], 'product' );
			} else {
				$string_id         = icl_get_string_id( $slug, $this->woocommerce_wpml->url_translation->url_strings_context(), $this->woocommerce_wpml->url_translation->url_string_name( 'product' ) );
				$slug_translations = icl_get_string_translations_by_id( $string_id );
			}

			$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug, $this->woocommerce_wpml->url_translation->url_strings_context(), $this->woocommerce_wpml->url_translation->url_string_name( 'product' ) );

			foreach ( $this->sitepress->get_active_languages() as $lang_info ) {
				if (
					(
						( isset( $slug_translations ) && ! array_key_exists( $lang_info['code'], $slug_translations ) ) ||
						( isset( $slug_translation_languages ) && ! in_array( $lang_info['code'], $slug_translation_languages ) )
					) && $lang_info['code'] != $string_language
				) {
					$miss_slug_lang[] = $lang_info;
				}
			}
		}

		return $miss_slug_lang;
	}

	public function get_flag_url( $language ) {
		return $this->sitepress->get_flag_url( $language );
	}

	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/status/',
		];
	}

	public function get_template() {
		return 'conf-warn.twig';
	}

}
