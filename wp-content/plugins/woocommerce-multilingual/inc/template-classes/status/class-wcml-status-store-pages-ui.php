<?php

use WPML\Core\Twig_SimpleFunction;

class WCML_Status_Store_Pages_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $sitepress;

	public function __construct( $sitepress, $woocommerce_wpml ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	public function init_twig_functions() {
		$function = new Twig_SimpleFunction( 'get_flag_url', [ $this, 'get_flag_url' ] );

		$twig = $this->get_twig();
		$twig->addFunction( $function );
	}

	public function get_model() {
		$this->init_twig_functions();

		$model = [
			'miss_lang'    => $this->woocommerce_wpml->store->get_missing_store_pages(),
			'install_link' => \WCML\Utilities\AdminUrl::getWooStatus( 'tools' ),
			'request_uri'  => $_SERVER['REQUEST_URI'],
			'strings'      => [
				/* translators: Heading of the block on the Status screen about the cart, checkout and other WooCommerce pages. */
				'store_pages'     => __( 'WooCommerce Store Pages', 'woocommerce-multilingual' ),
				/* translators: Explanation under the "WooCommerce Store Pages" heading; "this menu" is the block itself. */
				'pages_trnsl'     => __( "To run a multilingual e-commerce site, you need to have the WooCommerce shop pages translated to all the site's languages. Once all the pages are installed you can add the translations for them from this menu.", 'woocommerce-multilingual' ),
				/* translators: Warning on the Status screen when a WooCommerce page is missing in the original language. */
				'not_created'     => __( 'One or more WooCommerce pages have not been created.', 'woocommerce-multilingual' ),
				/* translators: Button on the Status screen that creates the missing WooCommerce pages. Verb phrase, imperative. */
				'install'         => __( 'Install WooCommerce Pages', 'woocommerce-multilingual' ),
				/* translators: Warning on the Status screen; the list of languages follows the colon. */
				'not_exist'       => __( 'WooCommerce store pages do not exist for these languages:', 'woocommerce-multilingual' ),
				/* translators: Button on the Status screen that creates the missing translations of the WooCommerce pages. Verb phrase, imperative. */
				'create_transl'   => __( 'Create missing translations', 'woocommerce-multilingual' ),
				/* translators: Line on the Status screen; the list of pages follows it, so keep the colon and the trailing space. */
				'translated_wpml' => __( 'These pages are currently being translated by translators via WPML: ', 'woocommerce-multilingual' ),
				/* translators: Line on the Status screen shown when every WooCommerce page has all its translations. */
				'translated'      => __( "WooCommerce store pages are translated to all the site's languages.", 'woocommerce-multilingual' ),
			],
			'nonces'       => [
				'create_pages' => wp_nonce_field( 'create_pages', 'wcml_nonce', true, false ),
			],
		];

		return $model;

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
		return 'store-pages.twig';
	}

}
