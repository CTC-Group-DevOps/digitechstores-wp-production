<?php

class WCML_Custom_Prices_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $product_id;
	private $custom_prices;
	private $is_variation;

	private $custom_prices_fields;

	private $custom_prices_fields_labels;


	public function __construct( $woocommerce_wpml, $product_id ) {
		parent::__construct();

		$this->woocommerce_wpml            = $woocommerce_wpml;
		$this->product_id                  = $product_id;
		$this->is_variation                = get_post_type( $product_id ) == 'product_variation';
		$this->custom_prices               = get_post_custom( $product_id );
		$this->custom_prices_fields        = apply_filters( 'wcml_custom_prices_fields', [ '_regular_price', '_sale_price' ], $product_id );
		$this->custom_prices_fields_labels = apply_filters(
			'wcml_custom_prices_fields_labels',
			[
				/* translators: Label of the normal price field in the per-currency prices of a product. */
				'_regular_price' => __( 'Regular Price', 'woocommerce-multilingual' ),
				/* translators: Label of the reduced price field in the per-currency prices of a product. */
				'_sale_price'    => __( 'Sale Price', 'woocommerce-multilingual' ),
			],
			$product_id
		);
	}

	public function get_model() {
		$model = [
			'product_id'            => $this->product_id,
			'currencies'            => $this->get_currencies_info(),
			'checked_calc_auto'     => ! isset( $this->custom_prices['_wcml_custom_prices_status'] ) || ( isset( $this->custom_prices['_wcml_custom_prices_status'] ) && $this->custom_prices['_wcml_custom_prices_status'][0] == 0 ) ? 'checked="checked"' : '',
			'checked_calc_manually' => isset( $this->custom_prices['_wcml_custom_prices_status'] ) && $this->custom_prices['_wcml_custom_prices_status'][0] == 1 ? 'checked="checked"' : '',
			'wc_currencies'         => get_woocommerce_currencies(),
			'is_variation'          => $this->is_variation,
			'html_id'               => $this->is_variation ? '[' . $this->product_id . ']' : '',
			'strings'               => apply_filters(
				'wcml_custom_prices_strings',
				[
					'not_set'        => sprintf(
						/* translators: %1$s and %2$s are opening and closing HTML link tags */
						__(
							'Multi-currency is enabled, but no secondary currencies have been set. %1$sAdd secondary currency%2$s.',
							'woocommerce-multilingual'
						),
						'<a href="' . \WCML\Utilities\AdminUrl::getMultiCurrencyTab() . '">',
						'</a>'
					),
					/* translators: Label of the first radio button of the per-currency price settings: WPML converts the price using the exchange rate. */
					'calc_auto'      => __( 'Calculate prices in other currencies automatically', 'woocommerce-multilingual' ),
					/* translators: Tooltip of the link that opens the per-currency prices of a product. */
					'see_prices'     => __( 'Click to see the prices in the other currencies as they are currently shown on the front end.', 'woocommerce-multilingual' ),
					/* translators: Link that opens the per-currency prices of a product. Verb, imperative: reveal them; it is the counterpart of "Hide". */
					'show'           => __( 'Show', 'woocommerce-multilingual' ),
					/* translators: Link that closes the per-currency prices of a product. Verb, imperative; it is the counterpart of "Show". */
					'hide'           => __( 'Hide', 'woocommerce-multilingual' ),
					/* translators: Label of the second radio button of the per-currency price settings: the shop owner types each currency's price. */
					'set_manually'   => __( 'Set prices in other currencies manually', 'woocommerce-multilingual' ),
					/* translators: Heading above the fields where the shop owner types a product's price in each currency. */
					'enter_prices'   => __( 'Enter prices in other currencies', 'woocommerce-multilingual' ),
					/* translators: Link that closes the per-currency price fields of a product. Verb phrase, imperative. */
					'hide_prices'    => __( 'Hide prices in other currencies', 'woocommerce-multilingual' ),
					/* translators: Text shown in place of a price field when the price is converted from the default currency. */
					'det_auto'       => __( 'Determined automatically based on exchange rate', 'woocommerce-multilingual' ),
					/* translators: Label of the normal price field in the per-currency prices of a product. */
					'_regular_price' => __( 'Regular Price', 'woocommerce-multilingual' ),
					/* translators: Label of the reduced price field in the per-currency prices of a product. */
					'_sale_price'    => __( 'Sale Price', 'woocommerce-multilingual' ),
					/* translators: Label of the sale-price date range of a product in one currency. Noun: the dates the sale price runs. */
					'schedule'       => __( 'Schedule', 'woocommerce-multilingual' ),
					/* translators: Option for the sale dates of a product in one currency: use the same dates as the shop's default currency. */
					'same_as_def'    => __( 'Same as default currency', 'woocommerce-multilingual' ),
					/* translators: Link that opens the sale start and end date fields of a product in one currency. Verb phrase, imperative. */
					'set_dates'      => __( 'Set dates', 'woocommerce-multilingual' ),
					/* translators: Link that closes the sale date fields again. Verb, imperative. */
					'collapse'       => __( 'Collapse', 'woocommerce-multilingual' ),
					/* translators: Placeholder text in the sale start-date field. Keep the three dots: they say a date is expected. */
					'from'           => __( 'From&hellip;', 'woocommerce-multilingual' ),
					/* translators: Placeholder text in the sale end-date field. Keep the three dots: they say a date is expected. */
					'to'             => __( 'To&hellip;', 'woocommerce-multilingual' ),
					/* translators: Error shown under a sale-price field when the sale price is not lower than the normal price. */
					'enter_price'    => __( 'Please enter in a value less than the regular price', 'woocommerce-multilingual' ),
				],
				$this->product_id
			),
		];

		return $model;
	}


	public function get_currencies_info() {

		$currencies    = $this->woocommerce_wpml->multi_currency->get_currencies();
		$wc_currencies = get_woocommerce_currencies();

		foreach ( $currencies as $key => $currency ) {

			$currencies[ $key ]['currency_code'] = $key;

			foreach ( $this->custom_prices_fields as $price_field ) {
				$currencies[ $key ]['readonly_price'][ $price_field ] = '';
				$currencies[ $key ]['custom_price'][ $price_field ]   = '';
			}

			if ( $this->product_id ) {

				foreach ( $this->custom_prices_fields as $price_field ) {
					$currencies[ $key ]['readonly_price'][ $price_field ] = get_post_meta( $this->product_id, $price_field, true );
					if ( $currencies[ $key ]['readonly_price'][ $price_field ] ) {
						$currencies[ $key ]['readonly_price'][ $price_field ] = $currencies[ $key ]['readonly_price'][ $price_field ] * $currency['rate'];
						$currencies[ $key ]['readonly_price'][ $price_field ] = wc_format_localized_price( $currencies[ $key ]['readonly_price'][ $price_field ] );
					}
				}
			}

			if ( isset( $this->custom_prices['_wcml_custom_prices_status'] ) ) {

				foreach ( $this->custom_prices_fields as $price_field ) {
					if ( isset( $this->custom_prices[ $price_field . '_' . $key ][0] ) ) {
						$currencies[ $key ]['custom_price'][ $price_field ] = wc_format_localized_price( $this->custom_prices[ $price_field . '_' . $key ][0] );
					}
				}
			}

			$currencies[ $key ]['currency_format'] = $wc_currencies[ $key ] . ' ( ' . get_woocommerce_currency_symbol( $key ) . ' )';
			$currencies[ $key ]['currency_symbol'] = get_woocommerce_currency_symbol( $key );

			if ( $this->is_variation ) {
				$currencies[ $key ]['custom_id'] = '[' . $key . '][' . $this->product_id . ']';
			} else {
				$currencies[ $key ]['custom_id'] = '[' . $key . ']';

				$wc_input = [];

				$wc_input['custom_attributes'] = [];
				$wc_input['type_name']         = 'data_type';
				$wc_input['type_val']          = 'price';

				foreach ( $this->custom_prices_fields as $price_field ) {
					ob_start();
					woocommerce_wp_text_input(
						[
							'id'                   => '_custom' . $price_field . '[' . $key . ']',
							'value'                => wc_format_localized_price( $currencies[ $key ]['custom_price'][ $price_field ] ),
							'class'                => 'wc_input_price wcml_input_price short wcml' . $price_field,
							'label'                => $this->custom_prices_fields_labels[ $price_field ] . ' (' . $currencies[ $key ]['currency_symbol'] . ')',
							$wc_input['type_name'] => $wc_input['type_val'],
							'custom_attributes'    => $wc_input['custom_attributes'],
						]
					);
					$currencies[ $key ]['custom_html'][ $price_field ] = ob_get_contents();
					ob_end_clean();
				}

				$wc_input['custom_attributes'] = [
					'readonly' => 'readonly',
					'rel'      => $currency['rate'],
				];

				foreach ( $this->custom_prices_fields as $price_field ) {
					ob_start();
					woocommerce_wp_text_input(
						[
							'id'                   => '_readonly' . $price_field,
							'value'                => wc_format_localized_price( $currencies[ $key ]['readonly_price'][ $price_field ] ),
							'class'                => 'wc_input_price short',
							'label'                => $this->custom_prices_fields_labels[ $price_field ] . ' (' . $currencies[ $key ]['currency_symbol'] . ')',
							$wc_input['type_name'] => $wc_input['type_val'],
							'custom_attributes'    => $wc_input['custom_attributes'],
						]
					);
					$currencies[ $key ]['readonly_html'][ $price_field ] = ob_get_contents();
					ob_end_clean();
				}
			}

			$currencies[ $key ]['schedule_auto_checked'] = ( ! isset( $this->custom_prices[ '_wcml_schedule_' . $key ] ) || ( isset( $this->custom_prices[ '_wcml_schedule_' . $key ] ) && $this->custom_prices[ '_wcml_schedule_' . $key ][0] == 0 ) ) ? 'checked="checked"' : ' ';
			$currencies[ $key ]['schedule_man_checked']  = isset( $this->custom_prices[ '_wcml_schedule_' . $key ] ) && $this->custom_prices[ '_wcml_schedule_' . $key ][0] == 1 ? 'checked="checked"' : ' ';

			$currencies[ $key ]['sale_price_dates_from'] = ( isset( $this->custom_prices[ '_sale_price_dates_from_' . $key ] ) && $this->custom_prices[ '_sale_price_dates_from_' . $key ][0] != '' ) ? date_i18n( 'Y-m-d', $this->custom_prices[ '_sale_price_dates_from_' . $key ][0] ) : '';
			$currencies[ $key ]['sale_price_dates_to']   = ( isset( $this->custom_prices[ '_sale_price_dates_to_' . $key ] ) && $this->custom_prices[ '_sale_price_dates_to_' . $key ][0] != '' ) ? date_i18n( 'Y-m-d', $this->custom_prices[ '_sale_price_dates_to_' . $key ][0] ) : '';

		}

		return $currencies;

	}


	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/multi-currency/',
		];
	}

	public function get_template() {
		return 'custom-prices.twig';
	}

}
