<?php

namespace WCML\Multicurrency\Analytics;

use WCML\COT\Helper as COTHelper;
use WCML\Orders\Helper as OrdersHelper;
use WCML\Utilities\Resources;
use WCML\Rest\Functions;
use WCML\StandAlone\IStandAloneAction;
use WPML\FP\Obj;
use WPML\FP\Fns;
use WCML\Utilities\WpAdminPages;

class Hooks implements \IWPML_Action, IStandAloneAction {

	const CURRENCY_FILTERED_EXPORT_REPORTS = [
		'orders',
		'products',
		'variations',
		'categories',
		'coupons',
		'taxes',
		'revenue',
	];

	private $woocommerce_wpml;

	private $wpdb;

	private $requestedCurrencyForReport;

	public function __construct( \woocommerce_wpml $woocommerce_wpml, \wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {
		if ( Functions::isAnalyticsPage() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
		}

		wpml_collect(
			[
				'revenue',
				'orders',
				'orders_stats',
				'products',
				'products_stats',
				'categories',
				'coupons',
				'coupons_stats',
				'taxes',
				'taxes_stats',
				'variations',
				'variations_stats',
			]
		)->each(
			function( $item ) {
				add_filter( "woocommerce_analytics_{$item}_query_args", [ $this, 'addCurrencyArg' ] );
			}
		);

		wpml_collect(
			[
				'products',
				'orders',
				'variations',
				'categories',
				'coupons',
				'taxes',
			]
		)->each(
			function( $item ) {
				add_filter( "woocommerce_analytics_clauses_where_{$item}_subquery", [ $this, 'addWhere' ] );
				add_filter( "woocommerce_analytics_clauses_where_{$item}_stats_total", [ $this, 'addWhere' ] );
				add_filter( "woocommerce_analytics_clauses_where_{$item}_stats_interval", [ $this, 'addWhere' ] );
			}
		);

		add_action( 'woocommerce_admin_report_export', [ $this, 'saveRequestedCurrencyForReport' ], -PHP_INT_MAX, 4 );
		add_filter( 'rest_request_before_callbacks', [ $this, 'saveRequestedCurrencyFromExportRequest' ], 10, 3 );
	}

	public function enqueueAssets() {
		$multiCurrency = $this->woocommerce_wpml->get_multi_currency();
		$currencies    = $multiCurrency->get_currency_codes();
		$enqueue       = Resources::enqueueApp( 'multicurrencyAnalytics' );

		$enqueue(
			[
				'name' => 'wcmlAnalytics',
				'data' => [
					'strings'         => [
						/* translators: Column heading and field label for the currency of an order, in the WooCommerce analytics reports, the payment-gateway settings and the plugin's multicurrency screen. Noun, singular. */
						'currencyLabel' => __( 'Currency', 'woocommerce-multilingual' ),
					],
					'filterItems'     => $this->getCurrencyFilterItems( $currencies ),
					'currencyConfigs' => $this->getCurrencyConfigs( $currencies, $multiCurrency ),
				],
			]
		);

		$source = WCML_PLUGIN_URL . '/dist/js/multicurrencyAnalytics/app.js?ver=' . WCML_VERSION;
		echo '<link rel="preload" href="' . esc_url( $source ) . '" as="script" />', "\n";
	}

	private function getCurrencyFilterItems( $currencies ) {
		$currencyLabels = get_woocommerce_currencies();

		return wpml_collect( $currencies )
			->map(
				function( $currency ) use ( $currencyLabels ) {
					return [
						'value' => $currency,
						'label' => $currencyLabels[ $currency ],
					];
				}
			)->toArray();
	}

	private function getCurrencyConfigs( $currencies, $multiCurrency ) {
		return wpml_collect( $currencies )
			->mapWithKeys(
				function( $currency ) use ( $multiCurrency ) {
					$get = Obj::prop( Fns::__, $multiCurrency->get_currency_details_by_code( $currency ) );

					return [
						$currency => [
							'code'              => $currency,
							'symbol'            => html_entity_decode( get_woocommerce_currency_symbol( $currency ) ),
							'symbolPosition'    => $get( 'position' ),
							'thousandSeparator' => $get( 'thousand_sep' ),
							'decimalSeparator'  => $get( 'decimal_sep' ),
							'precision'         => $get( 'num_decimals' ),
						],
					];
				}
			)->toArray();
	}

	public function addCurrencyArg( $args ) {
		return Obj::assoc( 'currency', $this->getCurrency(), $args );
	}

	public function addWhere( $clauses ) {
		if ( COTHelper::isUsageEnabled() ) {
			$orderTable = COTHelper::getTableName();

			$clauses[] = $this->wpdb->prepare(
				"AND EXISTS (
					SELECT 1 FROM {$orderTable}
					  WHERE id = {$this->wpdb->prefix}wc_order_stats.order_id
					  AND currency = %s
				)",
				$this->getCurrency()
			);
		} else {
			$clauses[] = $this->wpdb->prepare(
				"AND EXISTS (
					SELECT 1 FROM {$this->wpdb->postmeta}
					  WHERE post_id = {$this->wpdb->prefix}wc_order_stats.order_id
					  AND meta_key = '" . OrdersHelper::KEY_LEGACY_CURRENCY . "'
					  AND meta_value = %s
				)",
				$this->getCurrency()
			);
		}

		return $clauses;
	}

	public function saveRequestedCurrencyForReport( $page, $id, $type, $args ) {
		if ( in_array( $type, self::CURRENCY_FILTERED_EXPORT_REPORTS, true ) ) {
			$this->requestedCurrencyForReport = $this->sanitizeCurrency( Obj::prop( 'currency', (array) $args ) );
		}
	}

	public function saveRequestedCurrencyFromExportRequest( $response, $handler, $request ) {
		$reportType = Functions::getReportExportType( $request );

		if ( $reportType && in_array( $reportType, self::CURRENCY_FILTERED_EXPORT_REPORTS, true ) ) {
			$this->requestedCurrencyForReport = $this->sanitizeCurrency(
				Obj::prop( 'currency', (array) $request->get_param( 'report_args' ) )
			);
		}

		return $response;
	}

	private function getCurrency() {
		if ( ! empty( $this->requestedCurrencyForReport ) ) {
			return $this->requestedCurrencyForReport;
		}

		$mustUseDashboardCookie = WpAdminPages::isDashboard()
									|| \WCML_Admin_Currency_Selector::isDashboardWidgetRequest();

		$rawCurrency = $mustUseDashboardCookie
			? Obj::prop( '_wcml_dashboard_currency', $_COOKIE )
			: Obj::prop( 'currency', $_GET );

		$currency = $this->sanitizeCurrency( $rawCurrency );

		return $currency ? $currency : wcml_get_woocommerce_currency_option();
	}

	private function sanitizeCurrency( $value ) {
		if ( ! is_scalar( $value ) ) {
			return null;
		}

		$currency = strtoupper( sanitize_text_field( wp_unslash( (string) $value ) ) );

		return $this->woocommerce_wpml->get_multi_currency()->is_currency_active( $currency ) ? $currency : null;
	}

}
