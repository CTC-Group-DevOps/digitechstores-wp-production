<?php

class WCML_Setup_Store_Pages_UI extends WCML_Setup_Step {

	const SLUG = 'store-pages';

	private $woocommerce_wpml;

	private $sitepress;

	public function __construct( $woocommerce_wpml, $sitepress, $next_step_url, $previous_step_url ) {
		parent::__construct( $next_step_url, $previous_step_url );

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	public function get_model() {
		$WCML_Status_Store_Pages_UI = new WCML_Status_Store_Pages_UI( $this->sitepress, $this->woocommerce_wpml );
		$store_pages_view           = $WCML_Status_Store_Pages_UI->get_view();

		$store_pages_view = preg_replace( '@<form [^>]+>@', '', $store_pages_view );
		$store_pages_view = preg_replace( '@</form>@', '', $store_pages_view );

		if ( 'non_exist' === $this->woocommerce_wpml->store->get_missing_store_pages() ) {

			$store_pages_view = preg_replace(
				'@<i class="otgs-ico-warning"></i>[^<]*@',
				/* translators: Line in the store-pages step of the setup wizard, shown when some WooCommerce pages are missing; the language list follows the colon. "Continue" is the button below it. */
				addcslashes( __( 'Click continue to create missing WooCommerce pages translate your store pages into the following languages:', 'woocommerce-multilingual' ), '\\$' ),
				$store_pages_view
			);
			$store_pages_view = preg_replace(
				'@<a [^>]+>[^<]+</a>@',
				addcslashes( $this->get_secondary_languages(), '\\$' ),
				$store_pages_view
			);

			$store_pages_view .= '<input type="hidden" name="install_missing_pages" value="1">';

		} else {

			$store_pages_view = preg_replace(
				'@<i class="otgs-ico-warning"></i>[^<]*@',
				/* translators: Line in the store-pages step of the setup wizard, shown when every WooCommerce page exists; the language list follows the colon. "Continue" is the button below it. */
				addcslashes( __( 'Click continue to translate your store pages into the following languages:', 'woocommerce-multilingual' ), '\\$' ),
				$store_pages_view
			);
			$store_pages_view = preg_replace(
				'@<button [^>]+>[^<]+</button>@',
				'<input type="hidden" name="create_pages" value="1">',
				$store_pages_view
			);

		}

		$store_pages_view .= '<input type="hidden" name="next_step_url" value="' . esc_url( $this->next_step_url ) . '">';

		return [
			'strings'      => [
				'step_id'     => 'store_pages_step',
				/* translators: Name of one step of the setup wizard, used in the step list and as that step's heading. Verb phrase, imperative. */
				'heading'     => __( "Create store pages in all your site's languages", 'woocommerce-multilingual' ),
				/* translators: Explanation under the heading of the store-pages step of the setup wizard; the four names are WooCommerce page names. */
				'description' => __( 'WPML automatically generates translated versions of default WooCommerce pages, such as Shop, Account, Checkout, and Cart.', 'woocommerce-multilingual' ),
				/* translators: Button label that moves the setup wizard on to the next step. Verb, imperative. */
				'continue'    => __( 'Continue', 'woocommerce-multilingual' ),
				/* translators: Button label that returns the setup wizard to the previous step. Verb phrase, imperative. */
				'go_back'     => __( 'Go back', 'woocommerce-multilingual' ),
			],
			'store_pages'  => $store_pages_view,
			'continue_url' => $this->next_step_url,
			'go_back_url'  => $this->previous_step_url,
		];
	}

	public function get_template() {
		return '/setup/store-pages.twig';
	}

	private function get_secondary_languages() {
		$default_language = $this->sitepress->get_default_language();
		$languages        = $this->sitepress->get_active_languages();
		unset( $languages[ $default_language ] );

		$secondary_languages = [];
		foreach ( $languages as $language ) {
			$secondary_languages[] = '<li><span class="wpml-title-flag">'
				. '<img src="' . $this->sitepress->get_flag_url( $language['code'] ) . '" alt="' . esc_attr( $language['english_name'] ) . '">'
				. '</span> ' . ucfirst( $language['display_name'] ) . '</li>';
		}

		return PHP_EOL . '<ul class="wcml-lang-list">' . implode( PHP_EOL, $secondary_languages ) . '</ul>' . PHP_EOL;
	}
}
