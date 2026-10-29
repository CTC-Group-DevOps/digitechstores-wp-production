<?php

namespace WCML\Abilities;

use WCML\StandAlone\IStandAloneAction;
use WPML\FP\Obj;

class Hooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_AJAX_Action, \IWPML_REST_Action, \IWPML_CLI_Action, \IWPML_DIC_Action, IStandAloneAction {

	const WOOCOMMERCE_NAMESPACE = 'woocommerce/';
	const LANG_PARAM            = 'lang';
	const CURRENCY_PARAM        = 'currency';

	const LANG_ABILITIES = [
		'woocommerce/products-query',
		'woocommerce/orders-query',
	];

	const CURRENCY_ABILITIES = [
		'woocommerce/products-query',
		'woocommerce/orders-query',
	];

	private $woocommerce_wpml;

	private $previousLanguage;

	private $previousCurrency;

	private $forcedLanguage;

	private $forcedCurrency;

	public function __construct( \woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_filter( 'wp_register_ability_args', [ $this, 'addParamsToSchema' ], 10, 2 );
		add_action( 'wp_before_execute_ability', [ $this, 'applyContext' ], 10, 2 );
		add_action( 'wp_after_execute_ability', [ $this, 'restoreContext' ], 10, 3 );
	}

	public function addParamsToSchema( $args, $name ) {
		if ( ! isset( $args['input_schema']['properties'] ) || ! is_array( $args['input_schema']['properties'] ) ) {
			return $args;
		}

		if ( in_array( $name, self::LANG_ABILITIES, true ) ) {
			$languages = $this->activeLanguageCodes();
			if ( count( $languages ) > 1 ) {
				$args['input_schema']['properties'][ self::LANG_PARAM ] = [
					'type'        => 'string',
					'enum'        => $languages,
					/* translators: Description of the language input offered to an AI assistant that queries WooCommerce products and orders. */
					'description' => __( 'Language of the content to read.', 'woocommerce-multilingual' ),
				];
			}
		}

		if ( in_array( $name, self::CURRENCY_ABILITIES, true ) ) {
			$currencies = $this->activeCurrencyCodes();
			if ( count( $currencies ) > 1 ) {
				$args['input_schema']['properties'][ self::CURRENCY_PARAM ] = [
					'type'        => 'string',
					'enum'        => $currencies,
					/* translators: Description of the currency input offered to an AI assistant that queries WooCommerce products and orders. */
					'description' => __( 'Currency for the returned prices.', 'woocommerce-multilingual' ),
				];
			}
		}

		return $args;
	}

	public function applyContext( $name, $input ) {
		if ( ! $this->isWooCommerceAbility( $name ) || ! is_array( $input ) ) {
			return;
		}

		$lang = Obj::prop( self::LANG_PARAM, $input );
		if ( $lang ) {
			$this->previousLanguage = apply_filters( 'wpml_current_language', null );
			$this->forcedLanguage   = $lang;
			$this->switchLanguage( $lang );
			add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', [ $this, 'filterOrdersByLanguage' ], 10, 2 );
			add_filter( 'woocommerce_orders_table_datastore_get_orders_query', [ $this, 'filterOrdersByLanguage' ], 10, 2 );
		}

		$currency = Obj::prop( self::CURRENCY_PARAM, $input );
		if ( $currency && $this->woocommerce_wpml->multi_currency ) {
			$this->previousCurrency = $this->woocommerce_wpml->multi_currency->get_rest_currency();
			$this->forcedCurrency   = $currency;
			$this->woocommerce_wpml->multi_currency->set_rest_currency( $currency );
			add_filter( 'wcml_client_currency', [ $this, 'forceCurrency' ] );
		}
	}

	public function restoreContext( $name, $input, $result ) {
		if ( null !== $this->previousLanguage ) {
			remove_filter( 'woocommerce_order_data_store_cpt_get_orders_query', [ $this, 'filterOrdersByLanguage' ] );
			remove_filter( 'woocommerce_orders_table_datastore_get_orders_query', [ $this, 'filterOrdersByLanguage' ] );
			$this->switchLanguage( $this->previousLanguage );
			$this->previousLanguage = null;
			$this->forcedLanguage   = null;
		}

		if ( null !== $this->previousCurrency && $this->woocommerce_wpml->multi_currency ) {
			remove_filter( 'wcml_client_currency', [ $this, 'forceCurrency' ] );
			$this->woocommerce_wpml->multi_currency->set_rest_currency( $this->previousCurrency );
			$this->previousCurrency = null;
			$this->forcedCurrency   = null;
		}
	}

	public function forceCurrency( $currency ) {
		return $this->forcedCurrency ?? $currency;
	}

	public function filterOrdersByLanguage( $query, $queryVars ) {
		$query['meta_query'][] = [
			'key'   => \WCML_Orders::KEY_LANGUAGE,
			'value' => $this->forcedLanguage,
		];

		return $query;
	}

	private function switchLanguage( $language ) {
		do_action( 'wpml_switch_language', $language );
	}

	private function isWooCommerceAbility( $name ) {
		return 0 === strpos( $name, self::WOOCOMMERCE_NAMESPACE );
	}

	private function activeLanguageCodes() {
		return array_keys( (array) apply_filters( 'wpml_active_languages', null ) );
	}

	private function activeCurrencyCodes() {
		if ( ! $this->woocommerce_wpml->multi_currency ) {
			return [];
		}

		return array_keys( (array) $this->woocommerce_wpml->multi_currency->get_currencies( true ) );
	}
}
