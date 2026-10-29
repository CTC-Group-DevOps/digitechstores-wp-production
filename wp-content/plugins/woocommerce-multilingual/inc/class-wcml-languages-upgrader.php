<?php

class WCML_Languages_Upgrader {

	const LANGUAGES_PAGE = '/menu/languages.php';

	private $url_translation;

	private $sitepress;

	public function __construct( ?WCML_Url_Translation $url_translation = null, ?SitePress $sitepress = null ) {
		$this->url_translation = $url_translation;
		$this->sitepress       = $sitepress;
	}

	public function add_hooks() {
		add_action( 'icl_update_active_languages', [ $this, 'update_woocommerce_localization' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_js' ] );
	}

	public function download_woocommerce_translations_for_active_languages() {
		$this->update_woocommerce_localization();
	}

	public function update_woocommerce_localization() {
		$this->download_missing_language_packs();
		$this->get_url_translation()->register_product_and_taxonomy_bases();
	}

	private function download_missing_language_packs() {
		$languages = $this->get_languages_with_missing_pack();

		if ( ! $languages ) {
			return;
		}

		$localization = new WPML_Download_Localization( $languages, $this->get_sitepress()->get_default_language() );
		$localization->download_plugin_translations( plugin_basename( WC_PLUGIN_FILE ) );
	}

	private function get_languages_with_missing_pack() {
		return array_filter(
			$this->get_sitepress()->get_active_languages(),
			function ( $language ) {
				$locale = $this->get_sitepress()->get_locale( $language['code'] );

				return 'en_US' !== $locale && ! file_exists( $this->get_mo_file_path( $locale ) );
			}
		);
	}

	private function get_mo_file_path( $locale ) {
		return WP_LANG_DIR . '/plugins/woocommerce-' . $locale . '.mo';
	}

	private function is_wpml_languages_page() {
		return WPML_PLUGIN_FOLDER . self::LANGUAGES_PAGE === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	}

	public function load_js() {
		if ( ! $this->is_wpml_languages_page() ) {
			return;
		}

		wp_register_script( 'wcml-lang-notice', WCML_PLUGIN_URL . '/res/js/languages_notice' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
		wp_enqueue_script( 'wcml-lang-notice' );

		wp_localize_script(
			'wcml-lang-notice',
			'wcml_language_upgrade_notices',
			[
				/* translators: Message shown while the plugin downloads its own translations in the background. */
				'dont_close' => esc_html__( "Downloading translations... Please don't close this page.", 'woocommerce-multilingual' ),
			]
		);
	}
	private function get_url_translation() {
		if ( ! $this->url_translation ) {
			$this->url_translation = $GLOBALS['woocommerce_wpml']->url_translation;
		}

		return $this->url_translation;
	}

	private function get_sitepress() {
		if ( ! $this->sitepress ) {
			$this->sitepress = $GLOBALS['sitepress'];
		}

		return $this->sitepress;
	}
}
