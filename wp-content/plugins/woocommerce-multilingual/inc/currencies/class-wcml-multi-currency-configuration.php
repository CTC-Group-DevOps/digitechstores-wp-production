<?php

use WCML\MultiCurrency\Settings;
use WCML\Multicurrency\UI\Hooks;
use WCML\Utilities\AdminNotices;
use WCML\Utilities\AdminPages;
use WCML\Utilities\AdminUrl;
use WPML\FP\Obj;
use function WCML\functions\getSitePress;
use function WPML\Container\make;

class WCML_Multi_Currency_Configuration {

	const PERSISTED_PRICE_KEYS_CLEANED_OPTION = 'wcml_readonly_price_keys_cleaned';

	const NOTICES_GROUP = 'wcml-multi-currency';


	private static $multi_currency;
	private static $woocommerce_wpml;

	public static function set_up( WCML_Multi_Currency $multi_currency, woocommerce_wpml $woocommerce_wpml ) {

		self::$multi_currency   = $multi_currency;
		self::$woocommerce_wpml = $woocommerce_wpml;

		if ( isset( $_POST['action'] ) && $_POST['action'] === 'save-mc-options' ) {
			self::save_configuration();
		}

		self::set_prices_config();

		add_filter( 'option_icl_sitepress_settings', [ __CLASS__, 'inject_prices_readonly_config' ], 20 );

		self::add_hooks();
	}

	public static function add_hooks(){
		if ( wp_doing_ajax() ) {

			\WCML\Request\Ajax::register( 'wcml_save_currency', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'save_currency' ] );
			\WCML\Request\Ajax::register( 'wcml_delete_currency', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'delete_currency' ] );
			\WCML\Request\Ajax::register( 'wcml_update_currency_lang', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'update_currency_lang' ] );
			\WCML\Request\Ajax::register( 'wcml_update_default_currency', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'update_default_currency_ajax' ] );
			\WCML\Request\Ajax::register( 'wcml_set_currency_mode', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'set_currency_mode' ] );
			\WCML\Request\Ajax::register( 'wcml_set_max_mind_key', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'set_max_mind_key' ] );
			\WCML\Request\Ajax::register( 'wcml_get_auto_exchange_rate', [ 'authorize' => [ 'WCML_Capabilities', 'canManageWcml' ], 'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)', 'nonce' => [ 'wcml-multicurrency-options', 'nonce' ] ], [ __CLASS__, 'get_auto_exchange_rate' ] );
		}
	}

	public static function save_configuration() {
		if ( check_admin_referer( 'wcml_mc_options', 'wcml_nonce' ) ) {

			$wcml_settings = self::$woocommerce_wpml->settings;

			$wcml_settings['enable_multi_currency'] = isset( $_POST['multi_currency'] ) ? intval( $_POST['multi_currency'] ) : 0;
			$wcml_settings['display_custom_prices'] = isset( $_POST['display_custom_prices'] ) ? intval( $_POST['display_custom_prices'] ) : 0;

			if ( $wcml_settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {

				$options = [
					'woocommerce_currency_pos'       => 'position',
					'woocommerce_price_thousand_sep' => 'thousand_sep',
					'woocommerce_price_decimal_sep'  => 'decimal_sep',
					'woocommerce_price_num_decimals' => 'num_decimals',
				];

				$woocommerce_currency = wcml_get_woocommerce_currency_option();

				foreach ( $options as $wc_key => $key ) {
					$wcml_settings['currency_options'][ $woocommerce_currency ][ $key ] = get_option( $wc_key, true );
				}

				if ( ! isset( $wcml_settings['currency_options'][ $woocommerce_currency ]['location_mode'] ) ) {
					$wcml_settings['currency_options'][ $woocommerce_currency ]['location_mode'] = 'all';
				}
			}

			$wcml_settings['currency_switcher_product_visibility'] = isset( $_POST['currency_switcher_product_visibility'] ) ? intval( $_POST['currency_switcher_product_visibility'] ) : 0;
			$wcml_settings['currency_switcher_additional_css']     = isset( $_POST['currency_switcher_additional_css'] ) ? sanitize_text_field( $_POST['currency_switcher_additional_css'] ) : '';

			if ( ! isset( $wcml_settings['currencies_order'] ) ) {
				$wcml_settings['currencies_order'] = self::$multi_currency->currency_codes;
			}

			Settings::setMode( filter_input( INPUT_POST, 'currency_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
			self::$woocommerce_wpml->update_settings( $wcml_settings );

			do_action( 'wcml_saved_mc_options', $_POST );

			$notices = AdminNotices::get();
			$notice  = $notices->create_notice(
				'wcml-mc-settings-saved',
				/* translators: Confirmation notice shown after the plugin's settings were stored. */
				__( 'Your settings have been saved.', 'woocommerce-multilingual' ),
				self::NOTICES_GROUP
			);
			$notice->set_flash( true );
			$notice->set_css_class_types( 'notice-success' );
			$notice->set_restrict_to_pages( [ AdminPages::BONDED_SETTINGS_PAGE, AdminUrl::PAGE_WPML_WCML ] );
			$notices->add_notice( $notice );

			if ( ! \WCML\functions\isStandAlone() )  {
				$wpml_admin_notices = wpml_get_admin_notices();
				$wpml_admin_notices->remove_notice( 'wcml-save-multi-currency-options', 'wcml-fixerio-api-key-required' );
			}
		}

	}

	public static function add_currency( $currency_code, $rate = null ) {
		if ( null === $rate ) {
			$rate = filter_input( INPUT_POST, 'currency_value', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		}

		if ( ! is_numeric( $rate ) ) {
			wp_send_json_error( 'Invalid exchange rate.' );
		}

		$rate = (float) $rate;
		if ( ! is_finite( $rate ) || $rate <= 0 ) {
			wp_send_json_error( 'Invalid exchange rate.' );
		}

		$settings = self::$woocommerce_wpml->get_settings();

		foreach ( getSitePress()->get_active_languages() as $language ) {
			if ( ! isset( $settings['currency_options'][ $currency_code ]['languages'][ $language['code'] ] ) ) {
				$settings['currency_options'][ $currency_code ]['languages'][ $language['code'] ] = 1;
			}
		}
		$settings['currency_options'][ $currency_code ]['rate']    = $rate;
		$settings['currency_options'][ $currency_code ]['updated'] = date( 'Y-m-d H:i:s' );

		$wc_currency = wcml_get_woocommerce_currency_option();
		if ( ! isset( $settings['currencies_order'] ) ) {
			$settings['currencies_order'][] = $wc_currency;
		}

		$settings['currencies_order'][] = $currency_code;

		self::$woocommerce_wpml->update_settings( $settings );
		self::$multi_currency->init_currencies();

	}

	public static function save_currency() {
		self::verify_nonce();
		$data = self::get_data();

		$wc_currency   = wcml_get_woocommerce_currency_option();

		$options = $data['currency_options'];

		$currency_code = $options['code'];

		if ( $wc_currency !== $currency_code
			&& ( isset( $options['rate'] ) || ! isset( self::$multi_currency->currencies[ $currency_code ] ) )
		) {
			if ( ! is_numeric( $options['rate'] ?? null ) ) {
				wp_send_json_error( 'Invalid exchange rate.' );
			}

			$options['rate'] = (float) $options['rate'];
			if ( ! is_finite( $options['rate'] ) || $options['rate'] <= 0 ) {
				wp_send_json_error( 'Invalid exchange rate.' );
			}
		}

		if ( isset( $options['gatewaysSettings'] ) ) {

			$payment_gateways = self::$multi_currency->currencies_payment_gateways->get_gateways();

			foreach ( $options['gatewaysSettings'] as $code => $gateways_settings ) {
				if ( isset( $payment_gateways[ $code ] ) ) {
					$payment_gateways[ $code ]->save_setting( $currency_code, $gateways_settings );
				}
			}

			self::$multi_currency->currencies_payment_gateways->set_enabled( $currency_code, $options['gatewaysEnabled'] );
		}

		if ( isset( $options['countries'] ) ) {
			$options['countries'] = wc_string_to_array( $options['countries'], ',' );
		}

		if ( isset( $options['num_decimals'] ) ) {
			$options['num_decimals'] = absint( $options['num_decimals'] );
		}

		if ( $wc_currency !== $currency_code ) {
			if ( ! isset( self::$multi_currency->currencies[ $currency_code ] ) ) {
				self::add_currency( $currency_code, $options['rate'] );
			}

			foreach ( [ 'thousand_sep', 'decimal_sep' ] as $separator_key ) {
				$options[ $separator_key ] = self::sanitizePriceSeparator(
					$options[ $separator_key ] ?? null,
					$currency_code,
					$separator_key
				);
			}

			foreach ( self::$multi_currency->currencies[ $currency_code ] as $key => $value ) {

				if ( isset( $options[ $key ] ) && $options[ $key ] != $value ) {
					if ( $key === 'rate' ) {
						$previous_rate = self::$multi_currency->currencies[ $currency_code ][ $key ];
						self::$multi_currency->currencies[ $currency_code ]['previous_rate'] = $previous_rate;
						self::$multi_currency->currencies[ $currency_code ]['updated']       = date( 'Y-m-d H:i:s' );
					}
					self::$multi_currency->currencies[ $currency_code ][ $key ] = $options[ $key ];
				}
			}
		} else {
			self::$multi_currency->currencies[ $currency_code ]['countries']     = $options['countries'];
			self::$multi_currency->currencies[ $currency_code ]['location_mode'] = $options['location_mode'];
		}

		self::$woocommerce_wpml->update_setting( 'currency_options', self::$multi_currency->currencies );

		wp_send_json_success( [
			'formattedLastRateUpdate' => Hooks::formatLastRateUpdate(
				Obj::path( [ $currency_code, 'updated' ], self::$multi_currency->currencies )
			),
		] );
	}

	public static function delete_currency() {
		self::verify_nonce();
		$data = self::get_data();

		self::$multi_currency->delete_currency_by_code( $data['code'] );
		wp_send_json_success();
	}

	public static function update_currency_lang() {
		self::verify_nonce();
		$data = self::get_data();

		$settings = self::$woocommerce_wpml->get_settings();
		$settings['currency_options'][ $data['code'] ]['languages'][ $data['lang'] ] = (int) $data['value'];

		self::$woocommerce_wpml->update_settings( $settings );
		wp_send_json_success();
	}

	private static function verify_nonce() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! wp_verify_nonce( $nonce,  WCML\Multicurrency\UI\Hooks::HANDLE ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		WCML_Capabilities::requireCanManageWcmlForAjax();
	}

	private static function get_data() {
		return json_decode( stripslashes( $_POST['data'] ), true );
	}

	private static function sanitizePriceSeparator( $separator, string $currencyCode, string $key ): string {
		if ( is_string( $separator ) ) {
			$separator = wp_kses( $separator, [] );
			$decoded   = html_entity_decode( $separator, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

			if ( 0 === preg_match( '/\p{N}/u', $decoded ) ) {
				return $separator;
			}
		}

		return (string) Obj::path( [ $currencyCode, $key ], self::$multi_currency->currencies );
	}

	public static function update_default_currency_ajax() {
		self::verify_nonce();
		self::update_default_currency();
		wp_send_json_success();
	}

	public static function update_default_currency() {

		$data = self::get_data();

		if ( $data['lang'] === wcml_user_store_get( WCML_Multi_Currency::CURRENCY_LANGUAGE_STORAGE_KEY ) && $data['code'] !== 'location' ) {

			wcml_user_store_set( WCML_Multi_Currency::CURRENCY_STORAGE_KEY, $data['code'] );
		}

		self::$woocommerce_wpml->settings['default_currencies'][ $data['lang'] ] = $data['code'];
		self::$woocommerce_wpml->update_settings();

	}

	public static function currency_options_update_default_currency( $settings, $current_currency, $new_currency ) {

		if ( isset( $settings['currency_options'][ $current_currency ] ) ) {
			$currency_settings                             = $settings['currency_options'][ $current_currency ];
			$settings['currency_options'][ $new_currency ] = $currency_settings;
			$settings                                      = self::$woocommerce_wpml->multi_currency->delete_currency_by_code( $current_currency, $settings, false );
		}

		$notice_id = 'wcml-default-currency-changed';
		$text      = sprintf(
			/* translators: %1$s and %2$s are opening and closing HTML link tags */
			__(
				'The default currency was changed. In order to show accurate prices in all currencies, you need to update the exchange rates under the %1$sMulti-currency%2$s configuration.',
				'woocommerce-multilingual'
			),
			'<a href="' . AdminUrl::getMultiCurrencyTab() . '">',
			'</a>'
		);

		$notices = AdminNotices::get();
		$notices->remove_notice( self::NOTICES_GROUP, $notice_id );

		$notice = $notices->create_notice( $notice_id, $text, self::NOTICES_GROUP );
		$notice->set_css_class_types( 'notice-warning' );
		$notice->set_dismissible( true );
		$notice->reset_dismiss();
		$notices->add_notice( $notice );

		return $settings;
	}

	public static function set_prices_config() {
		global $iclTranslationManagement;

		if ( ! isset( $iclTranslationManagement ) ||
			! ( $iclTranslationManagement instanceof TranslationManagement ) ) {
			return;
		}

		$expected = [];
		foreach ( self::price_meta_keys() as $key ) {
			$expected[ $key ] = WPML_COPY_CUSTOM_FIELD;

			if ( self::$multi_currency instanceof WCML_Multi_Currency ) {
				foreach ( self::$multi_currency->get_currency_codes() as $code ) {
					$expected[ $key . '_' . $code ] = WPML_IGNORE_CUSTOM_FIELD;
				}
			}
		}

		$modes = [];
		foreach ( \WPML\TM\Settings\PreferenceResolver::modes( 'post', array_keys( $expected ) ) as $key => $mode ) {
			if ( $expected[ $key ] !== $mode ) {
				$modes[ $key ] = $expected[ $key ];
			}
		}

		if ( $modes ) {
			\WPML\TM\Settings\PreferenceWriter::setModes( 'post', $modes );
		}
	}

	public static function inject_prices_readonly_config( $settings ) {
		if ( ! is_array( $settings ) ) {
			return $settings;
		}

		$config = isset( $settings['translation-management']['custom_fields_readonly_config'] )
			? (array) $settings['translation-management']['custom_fields_readonly_config']
			: [];

		foreach ( self::price_meta_keys() as $key ) {
			$config[] = $key;

			if ( self::$multi_currency instanceof WCML_Multi_Currency ) {
				foreach ( self::$multi_currency->get_currency_codes() as $code ) {
					$config[] = $key . '_' . $code;
				}
			}
		}

		$settings['translation-management']['custom_fields_readonly_config'] = array_values( array_unique( $config ) );

		return $settings;
	}

	public static function clean_up_persisted_price_keys() {
		global $iclTranslationManagement;

		if ( ! is_admin() || get_option( self::PERSISTED_PRICE_KEYS_CLEANED_OPTION ) ) {
			return;
		}

		if ( ! isset( $iclTranslationManagement ) ||
			! ( $iclTranslationManagement instanceof TranslationManagement ) ) {
			return;
		}

		$config = isset( $iclTranslationManagement->settings['custom_fields_readonly_config'] )
			? (array) $iclTranslationManagement->settings['custom_fields_readonly_config']
			: [];

		$cleaned = array_values( array_unique( array_filter( $config, function ( $entry ) {
			return ! self::is_price_meta_key( (string) $entry );
		} ) ) );

		if ( $cleaned !== array_values( $config ) ) {
			$iclTranslationManagement->settings['custom_fields_readonly_config'] = $cleaned;
			$iclTranslationManagement->save_settings();
		}

		update_option( self::PERSISTED_PRICE_KEYS_CLEANED_OPTION, true, true );
	}

	private static function is_price_meta_key( $entry ) {
		foreach ( self::price_meta_keys() as $key ) {
			if ( $entry === $key || 0 === strpos( $entry, $key . '_' ) ) {
				return true;
			}
		}

		return false;
	}

	private static function price_meta_keys() {
		return [
			'_regular_price',
			'_sale_price',
			'_price',
			'_min_variation_regular_price',
			'_min_variation_sale_price',
			'_min_variation_price',
			'_max_variation_regular_price',
			'_max_variation_sale_price',
			'_max_variation_price',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'_wcml_schedule',
		];
	}

	public static function set_currency_mode() {
		self::verify_nonce();
		$data = self::get_data();

		Settings::setMode( $data['mode'] );

		wp_send_json_success();
	}

	public static function set_max_mind_key() {
		self::verify_nonce();
		$data = self::get_data();

		if ( WC()->integrations instanceof WC_Integrations) {
			$integrations = WC()->integrations->get_integrations();

			if ( isset( $integrations['maxmind_geolocation'] ) ) {
				try {
					$integrations['maxmind_geolocation']->validate_license_key_field( 'license_key', $data['MaxMindKey'] );
					$integrations['maxmind_geolocation']->update_option( 'license_key', $data['MaxMindKey'] );
					wp_send_json_success();
				} catch ( Exception $e ) {
					wp_send_json_error( $e->getMessage() );
				}
			}
		}
	}

	public static function get_auto_exchange_rate() {
		self::verify_nonce();

		try {
			$currency = sanitize_text_field( Obj::prop( 'currency', self::get_data() ) );
			$rate     = Obj::prop( $currency, make( \WCML_Exchange_Rates::class )->fetch_exchange_rates_from_active_service( [ $currency ] ) );

			if ( null === $rate ) {
				throw new \Exception();
			}

			wp_send_json_success( $rate );
		} catch ( \Exception $e ) {
			wp_send_json_error();
		}
	}
}
