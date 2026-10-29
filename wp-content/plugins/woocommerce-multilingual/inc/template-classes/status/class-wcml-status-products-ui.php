<?php

class WCML_Status_Products_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;

	private $sitepress;

	public function __construct( $woocommerce_wpml, $sitepress ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	public function get_model() {
		$model = [
			'products'   => $this->get_untranslated_products(),
			'trnsl_link' => admin_url( \WPML\UIPage::getTMDashboard() ),
			'strings'    => [
				/* translators: Heading of the block on the Status screen that counts untranslated products. */
				'products_missing' => __( 'Products Missing Translations', 'woocommerce-multilingual' ),
				/* translators: Line on the Status screen counting untranslated products in one language. %1$d: how many are missing, %2$s: the language name. */
				'miss_trnsl_one'   => __( '%1$d %2$s translation missing.', 'woocommerce-multilingual' ),
				/* translators: Line on the Status screen counting untranslated products in one language, when more than one is missing. %1$d: how many are missing, %2$s: the language name. */
				'miss_trnsl_more'  => __( '%1$d %2$s translations missing.', 'woocommerce-multilingual' ),
				/* translators: Card title and button label that open WPML's Translation Dashboard filtered to products. Verb phrase, imperative. */
				'transl'           => __( 'Translate Products', 'woocommerce-multilingual' ),
				/* translators: Line on the Status screen shown when every product is translated. */
				'not_to_trnsl'     => __( 'Right now, there are no products needing translation.', 'woocommerce-multilingual' ),
			],
		];

		return $model;

	}

	private function get_untranslated_products() {
		$products = [];

		foreach ( $this->sitepress->get_active_languages() as $language ) {

			$products_count = $this->woocommerce_wpml->products->get_untranslated_products_count( $language['code'] );
			if ( $products_count ) {
				$products[ $language['code'] ]['count']        = $this->woocommerce_wpml->products->get_untranslated_products_count( $language['code'] );
				$products[ $language['code'] ]['flag']         = $this->sitepress->get_flag_img( $language['code'] );
				$products[ $language['code'] ]['display_name'] = $language['display_name'];
			}
		}

		return $products;
	}

	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/status/',
		];
	}

	public function get_template() {
		return 'products.twig';
	}

}
