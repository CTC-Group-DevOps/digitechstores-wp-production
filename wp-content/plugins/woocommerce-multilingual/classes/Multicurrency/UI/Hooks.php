<?php

namespace WCML\Multicurrency\UI;

use WCML\MultiCurrency\Settings;
use WCML\StandAlone\IStandAloneAction;
use WCML\Utilities\Resources;
use WCML_Multi_Currency;
use WCML_Currencies_Payment_Gateways;
use WPML\Collect\Support\Collection;
use WPML\Core\ISitePress;
use WCML\StandAlone\NullSitePress;
use SitePress;
use WPML\FP\Fns;
use WPML\FP\Obj;
use function WCML\functions\isStandAlone;
use function WPML\FP\curryN;

class Hooks implements \IWPML_Action, IStandAloneAction {

	const HANDLE = 'wcml-multicurrency-options';

	private $multiCurrency;

	private $currenciesPaymentGateways;

	private $sitepress;

	private $wcmlSettings;

	public function __construct(
		WCML_Multi_Currency $multiCurrency,
		WCML_Currencies_Payment_Gateways $currenciesPaymentGateways,
		ISitePress $sitepress,
		array $wcmlSettings
	) {
		$this->multiCurrency             = $multiCurrency;
		$this->currenciesPaymentGateways = $currenciesPaymentGateways;
		$this->sitepress                 = $sitepress;
		$this->wcmlSettings              = $wcmlSettings;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'loadAssets' ] );
	}

	public function loadAssets() {
		$gateways = $this->getGateways();
		$enqueue  = Resources::enqueueApp( 'multicurrencyOptions' );

		$enqueue( [
			'name' => 'wcmlMultiCurrency',
			'data' => [
				'endpoint'          => self::HANDLE,
				'activeCurrencies'  => $this->getActiveCurrencies( $gateways ),
				'allCurrencies'     => $this->getAllCurrencies(),
				'allCountries'      => $this->getAllCountries(),
				'languages'         => $this->getLanguages(),
				'gateways'          => $gateways->toArray(),
				'strings'           => $this->getStrings(),
				'mode'              => Settings::getMode(),
				'maxMindKeyExist'   => $this->checkMaxMindKeyExist(),
				'isStandalone'      => isStandAlone(),
				'isAutoRateEnabled' => Settings::isAutomaticRateEnabled(),
			],
		] );
	}

	private function getActiveCurrencies( Collection $gateways ) {
		$defaultCurrency = wcml_get_woocommerce_currency_option();

		$addCode = function( $currency, $code ) {
			return array_merge( $currency, [ 'code' => $code ] );
		};

		$buildActiveCurrency = function( $currency ) use ( $defaultCurrency ) {
			return [
				'isDefault'       => $currency['code'] === $defaultCurrency,
				'languages'       => array_map( 'intval', $currency['languages'] ),
				'gatewaysEnabled' => $this->currenciesPaymentGateways->is_enabled( $currency['code'] ),
			];
		};

		$addGatewaysSettings = function( $currency ) use ( $gateways ) {
			$addSettingsForGateway = curryN( 2, function( $code, array $gateway ) {
				return [ $gateway['id'] => Obj::pathOr( [], [ 'settings', $code ], $gateway ) ];
			} );

			return [ 'gatewaysSettings' => $gateways->mapWithKeys( $addSettingsForGateway( $currency['code'] ) )->toArray() ];
		};

		$addFormattedLastRateUpdate = function( $currency ) {
			return [
				'formattedLastRateUpdate' => isset( $currency['updated'] )
					? self::formatLastRateUpdate( $currency['updated'] )
					: null,
			];
		};

		$merge = function( $fn ) {
			return Fns::converge( 'array_merge', [ $fn, Fns::identity() ] );
		};

		return wpml_collect( $this->multiCurrency->get_currencies( true ) )
			->map( $addCode )
			->map( $merge( $buildActiveCurrency ) )
			->map( $merge( $addFormattedLastRateUpdate ) )
			->map( $merge( $addGatewaysSettings ) )
			->values()
			->toArray();
	}

	private function getAllCurrencies() {
		$currencyFormats = json_decode( file_get_contents( WCML_PLUGIN_PATH . '/res/currencies/currency_formats.json' ) );

		$buildCurrency = function( $label, $code ) use ( $currencyFormats ) {
			$getDefault = function( $prop, $default ) use ( $code, $currencyFormats ) {
				return Obj::pathOr( $default, [ $code, $prop ], $currencyFormats );
			};

			return (object) [
				'code'         => $code,
				'label'        => html_entity_decode( $label ),
				'symbol'       => html_entity_decode( get_woocommerce_currency_symbol( $code ) ),
				'position'     => $getDefault( 'position', 'left' ),
				'thousand_sep' => $getDefault( 'thousand_sep', '.' ),
				'decimal_sep'  => $getDefault( 'decimal_sep', ',' ),
				'num_decimals' => $getDefault( 'num_decimals', 2 ),
			];
		};

		return wpml_collect( get_woocommerce_currencies() )->map( $buildCurrency )->values()->toArray();
	}

	private function getLanguages() {
		$buildLanguage = function( $data ) {
			return (object) [
				'code'            => $data['code'],
				'displayName'     => $data['display_name'],
				'flagUrl'         => $this->sitepress->get_flag_url( $data['code'] ),
				'defaultCurrency' => $this->wcmlSettings['default_currencies'][ $data['code'] ] ?? false,
			];
		};

		return wpml_collect( $this->sitepress->get_active_languages() )
			->map( $buildLanguage )
			->values()
			->toArray();
	}

	private function getGateways() {
		$isSupported = function( \WCML_Payment_Gateway $gateway ) {
			return ! $gateway instanceof \WCML_Not_Supported_Payment_Gateway;
		};

		$buildGateway = function( \WCML_Payment_Gateway $gateway ) {
			return $gateway->get_output_model();
		};

		return wpml_collect( $this->currenciesPaymentGateways->get_gateways() )
			->prioritize( $isSupported )
			->map( $buildGateway )
			->values();
	}

	private function getStrings() {
		$trackingLink = new \WCML_Tracking_Link();

		return [
			/* translators: Heading of the currencies table on the plugin's multicurrency screen. Noun, plural. */
			'labelCurrencies'                => __( 'Currencies', 'woocommerce-multilingual' ),
			/* translators: Column heading and field label for the currency of an order, in the WooCommerce analytics reports, the payment-gateway settings and the plugin's multicurrency screen. Noun, singular. */
			'labelCurrency'                  => __( 'Currency', 'woocommerce-multilingual' ),
			/* translators: Button label on the plugin's multicurrency screen. Verb, imperative. */
			'labelAddCurrency'               => __( 'Add currency', 'woocommerce-multilingual' ),
			/* translators: Column heading for a currency's exchange rate on the multicurrency screen. Noun: the exchange rate, not a price or a rating. */
			'labelRate'                      => __( 'Rate', 'woocommerce-multilingual' ),
			/* translators: Marker shown in lower case next to the shop's default currency in the currencies list. Adjective. */
			'labelDefault'                   => __( 'default', 'woocommerce-multilingual' ),
			/* translators: Link text that opens a currency's settings on the multicurrency screen. Verb, imperative. */
			'labelEdit'                      => __( 'Edit', 'woocommerce-multilingual' ),
			/* translators: Label of the setting that picks which currency a visitor sees when they switch language. */
			'labelDefaultCurrency'           => __( 'Currency displayed first', 'woocommerce-multilingual' ),
			/* translators: Tooltip of the setting that picks which currency a visitor sees when they switch language. */
			'tooltipDefaultCurrency'         => __( 'Choose which currency to display when a client switches languages', 'woocommerce-multilingual' ),
			/* translators: Button label that cancels a delete and keeps the currency. Verb, imperative. */
			'labelKeep'                      => __( 'Keep', 'woocommerce-multilingual' ),
			/* translators: Button and link text that removes a currency or a currency switcher. Verb, imperative. */
			'labelDelete'                    => __( 'Delete', 'woocommerce-multilingual' ),
			/* translators: Heading of the table where each language gets the currencies it may show. */
			'labelCurrenciesToDisplay'       => __( 'Currencies to display for each language', 'woocommerce-multilingual' ),
			/* translators: Hint shown in place of the per-language currencies table while the shop has only one currency. */
			'labelAddCurrencyHint'           => __( 'Add a secondary currency to choose which currencies appear for each language.', 'woocommerce-multilingual' ),
			/* translators: %1$s is for language name and %2$s for currency name */
			'placeholderEnableFor'           => __( 'Enable %1$s for %2$s', 'woocommerce-multilingual' ),
			/* translators: %1$s is for language name and %2$s for currency name */
			'placeholderDisableFor'          => __( 'Disable %1$s for %2$s', 'woocommerce-multilingual' ),
			/* translators: Tab title and link text opening the settings of the plugin or of one currency. Noun, plural. */
			'labelSettings'                  => __( 'Settings', 'woocommerce-multilingual' ),
			/* translators: Title of the dialog that adds a currency to the shop. Verb phrase, imperative. */
			'labelAddNewCurrency'            => __( 'Add new currency', 'woocommerce-multilingual' ),
			/* translators: %s for currency label */
			'placeholderCurrencySettingsFor' => __( 'Currency settings for %s', 'woocommerce-multilingual' ),
			/* translators: Label of the dropdown that picks the currency to add, in the add-currency dialog. Verb phrase, imperative. */
			'labelSelectCurrency'            => __( 'Select currency', 'woocommerce-multilingual' ),
			/* translators: Label of the exchange-rate field in a currency's settings. Noun. */
			'labelExchangeRate'              => __( 'Exchange Rate', 'woocommerce-multilingual' ),
			/* translators: Validation hint under the exchange-rate field: only digits may be typed. */
			'labelOnlyNumeric'               => __( 'Only numeric', 'woocommerce-multilingual' ),
			/* translators: %s for currency exchange rate value */
			'placeholderPreviousRate'        => __( '(previous value: %s)', 'woocommerce-multilingual' ),
			/* translators: Label above the sample price that shows how amounts will look in this currency. */
			'labelCurrencyPreview'           => __( 'Currency Preview', 'woocommerce-multilingual' ),
			/* translators: Label of the setting that places the currency symbol before or after the amount. */
			'labelPosition'                  => __( 'Currency Position', 'woocommerce-multilingual' ),
			/* translators: Option of the currency-symbol position setting: the symbol goes before the amount. */
			'optionLeft'                     => __( 'Left', 'woocommerce-multilingual' ),
			/* translators: Option of the currency-symbol position setting: the symbol goes after the amount. */
			'optionRight'                    => __( 'Right', 'woocommerce-multilingual' ),
			/* translators: Option of the currency-symbol position setting: the symbol goes before the amount with a space between them. */
			'optionLeftSpace'                => __( 'Left with space', 'woocommerce-multilingual' ),
			/* translators: Option of the currency-symbol position setting: the symbol goes after the amount with a space between them. */
			'optionRightSpace'               => __( 'Right with space', 'woocommerce-multilingual' ),
			/* translators: Label of the field holding the character that separates thousands in an amount. */
			'labelThousandSep'               => __( 'Thousand Separator', 'woocommerce-multilingual' ),
			/* translators: Option shown in the separator dropdowns for a space character; the brackets say it is a description, not a literal value. */
			'labelSpaceSep'                  => __( '(Space)', 'woocommerce-multilingual' ),
			/* translators: Label of the field holding the character that separates the decimals in an amount. */
			'labelDecimalSep'                => __( 'Decimal Separator', 'woocommerce-multilingual' ),
			/* translators: Label of the field holding how many decimal digits an amount shows. */
			'labelNumDecimals'               => __( 'Number of Decimals', 'woocommerce-multilingual' ),
			/* translators: Label of the dropdown that rounds converted prices to a whole number. */
			'labelRounding'                  => __( 'Rounding to the nearest integer', 'woocommerce-multilingual' ),
			/* translators: Option of the price-rounding dropdown: no rounding is applied. Adjective describing the setting, not a command. */
			'optionDisabled'                 => __( 'Disabled', 'woocommerce-multilingual' ),
			/* translators: Option of the price-rounding dropdown: round the price upwards. */
			'optionUp'                       => __( 'Up', 'woocommerce-multilingual' ),
			/* translators: Option of the price-rounding dropdown: round the price downwards. */
			'optionDown'                     => __( 'Down', 'woocommerce-multilingual' ),
			/* translators: Option of the price-rounding dropdown: round the price to the closest whole number. */
			'optionNearest'                  => __( 'Nearest', 'woocommerce-multilingual' ),
			/* translators: Label of the field that sets the step prices are rounded to, such as 10 or 100. */
			'labelIncrement'                 => __( 'Increment for nearest integer', 'woocommerce-multilingual' ),
			/* translators: %s is an HTML break line */
			'tooltipIncrement'               => sprintf( __( 'The resulting price will be an increment of this value after initial rounding.%se.g.:', 'woocommerce-multilingual' ), '<br>' ) . '<br />' .
												/* translators: Example line in the tooltip of the rounding-increment field. Keep the numbers; &raquo; is the arrow between the price before and after rounding. */
												__( '1454.07 &raquo; 1454 when set to 1', 'woocommerce-multilingual' ) . '<br />' .
												/* translators: Example line in the tooltip of the rounding-increment field. Keep the numbers; &raquo; is the arrow between the price before and after rounding. */
												__( '1454.07 &raquo; 1450 when set to 10', 'woocommerce-multilingual' ) . '<br />' .
												/* translators: Example line in the tooltip of the rounding-increment field. Keep the numbers; &raquo; is the arrow between the price before and after rounding. */
												__( '1454.07 &raquo; 1500 when set to 100', 'woocommerce-multilingual' ) . '<br />',
			/* translators: %s is an HTML break line */
			'tooltipRounding'                => sprintf( __( 'Round the converted price to the closest integer. %se.g. 15.78 becomes 16.00', 'woocommerce-multilingual' ), '<br />' ),
			/* translators: First line of the tooltip of the auto-subtract field; "the amount obtained previously" is the price after rounding. */
			'tooltipAutosubtract'            => __( 'The value to be subtracted from the amount obtained previously.', 'woocommerce-multilingual' ) . '<br /><br />' .
												/* translators: Second line of the tooltip of the auto-subtract field, giving a worked example. Keep the numbers. */
												__( 'For 1454.07, when the increment for the nearest integer is 100 and the auto-subtract amount is 1, the resulting amount is 1499.', 'woocommerce-multilingual' ),
			/* translators: Label of the field holding the amount taken off a price after rounding. */
			'labelAutosubtract'              => __( 'Autosubtract amount', 'woocommerce-multilingual' ),
			/* translators: Heading of the section of a currency's settings that lists the payment methods it may use. */
			'labelPaymentGateways'           => __( 'Payment Gateways', 'woocommerce-multilingual' ),
			/* translators: %s is a currency code */
			'placeholderCustomSettings'      => __( 'Custom settings for %s', 'woocommerce-multilingual' ),
			'linkUrlLearn'                   => $trackingLink->getWcmlMultiCurrencyDoc( '#payment-gateways-settings' ),
			/* translators: Link text that opens the WPML documentation. Verb phrase, imperative. */
			'linkLabelLearn'                 => __( 'Learn more', 'woocommerce-multilingual' ),
			/* translators: Error shown under a field of the currency settings when what was typed is not a number. */
			'errorInvalidNumber'             => __( 'Please enter a valid number', 'woocommerce-multilingual' ),
			/* translators: Error shown in the currency settings dialog when the server did not store the currency. Two short sentences; the second one is an imperative. */
			'errorCurrencyNotSaved'          => __( 'The currency was not saved. Try again.', 'woocommerce-multilingual' ),
			/* translators: Error shown next to the "Show currencies based on" options when the server did not store the choice. Two short sentences; the second one is an imperative. */
			'errorModeNotSaved'              => __( 'The setting was not saved. Try again.', 'woocommerce-multilingual' ),
			/* translators: Button label that closes a dialog and discards the changes. Verb, imperative: the action, not the state "cancelled". */
			'labelCancel'                    => __( 'Cancel', 'woocommerce-multilingual' ),
			/* translators: Button label that stores the settings of a dialog. Verb, imperative. */
			'labelSave'                      => __( 'Save', 'woocommerce-multilingual' ),
			/* translators: Label above the dropdown that limits a currency to certain countries; the sentence continues with the choice made in the dropdown. */
			'labelAvailability'              => __( 'Currency available in', 'woocommerce-multilingual' ),
			/* translators: Option of the availability dropdown of a currency or a payment method: no country limit. */
			'labelAllCountries'              => __( 'All countries', 'woocommerce-multilingual' ),
			/* translators: Label that introduces the list of excluded countries for a currency; keep the colon and the trailing space, the country list follows on the same line. */
			'labelAllCountriesExcept'        => __( 'All countries except: ', 'woocommerce-multilingual' ),
			/* translators: Option of the availability dropdown of a currency or a payment method; the three dots say that countries have to be picked next. */
			'labelAllCountriesExceptDots'    => __( 'All countries except...', 'woocommerce-multilingual' ),
			/* translators: Option of the availability dropdown of a currency or a payment method: only the countries picked next. */
			'labelSpecificCountries'         => __( 'Specific countries', 'woocommerce-multilingual' ),
			/* translators: Label above the dropdown that decides what picks the shopper's currency; the sentence continues with the choice made in the dropdown. */
			'labelModeSelect'                => __( 'Show currencies based on', 'woocommerce-multilingual' ),
			/* translators: Tooltip on the disabled "Site Language" option, shown when WPML is not installed. A link with the buy text follows it. */
			'labelSiteLanguageTooltip'       => esc_html__( "You need the WPML plugin to make your store multilingual and display currencies based on site language. Don't have it yet?", 'woocommerce-multilingual' ) . '<br />' .
												/* translators: Link text at the end of the tooltip about the missing WPML plugin; it opens the WPML shop. Verb phrase, imperative. */
												'<a href="' . \WCML_Tracking_Link::getWpmlPurchase( true ) . '" class="wpml-external-link" target="_blank">' . esc_html__( 'Purchase WPML now', 'woocommerce-multilingual' ) . '</a>',
			/* translators: Option of the "Show currencies based on" dropdown: the language the visitor is browsing in decides the currency. */
			'labelSiteLanguage'              => __( 'Site Language', 'woocommerce-multilingual' ),
			/* translators: Option of the "Show currencies based on" dropdown: the shopper's country decides the currency. */
			'labelClientLocation'            => __( 'Client Location', 'woocommerce-multilingual' ),
			/* translators: Heading of the settings that appear once currencies are chosen by the shopper's location. */
			'labelLocationBased'             => __( 'Location based', 'woocommerce-multilingual' ),
			/* translators: Explanation under the location-based currency settings. "MaxMind Geolocation" is a service name and stays in English. */
			'maxMindDescription'             => __( 'WooCommerce integrates with MaxMind Geolocation to reliably determine the location of your customers.', 'woocommerce-multilingual' ),
			/* translators: Confirmation shown once a MaxMind key is saved; a link to the WooCommerce settings page follows it, so keep the trailing space and leave the sentence open. */
			'maxMindSuccess'                 => __( 'Great! Now you can use Geolocation to determine default currency for chosen languages. You can edit that key in ', 'woocommerce-multilingual' ),
			'maxMindSettingLink'             => \WCML\Utilities\AdminUrl::getWooSettings( 'integration' ),
			/* translators: Link text that closes the confirmation sentence about the saved MaxMind key; it opens the WooCommerce settings. */
			'maxMindSettingLinkText'         => __( 'WooCommerce settings page.', 'woocommerce-multilingual' ),
			/* translators: Label of the field holding the licence key of the MaxMind geolocation service. "MaxMind" is a service name and stays in English. */
			'maxMindLabel'                   => __( 'MaxMind Licence Key', 'woocommerce-multilingual' ),
			/* translators: Button label that puts the entered values into effect. Verb, imperative. */
			'apply'                          => __( 'Apply', 'woocommerce-multilingual' ),
			/* translators: Warning under the MaxMind licence-key field. */
			'maxMindNote'                    => __( 'Please note: without a free MaxMind key, your geolocation calls will be limited and your site performance may be impacted.', 'woocommerce-multilingual' ),
			'maxMindDocLink'                 => 'https://docs.woocommerce.com/document/maxmind-geolocation-integration/',
			/* translators: Link text under the MaxMind licence-key field; it opens the WooCommerce documentation. Verb phrase, imperative. */
			'maxMindDocLinkText'             => __( 'Learn how to generate a free license key', 'woocommerce-multilingual' ),
		];
	}

	private function getAllCountries() {

		$buildCountry = function( $label, $code ) {
			return (object) [
				'code'  => $code,
				'label' => html_entity_decode( $label ),
			];
		};

		return wpml_collect( WC()->countries->get_countries() )->map( $buildCountry )->values()->toArray();
	}

	private function checkMaxMindKeyExist() {
		$integrations = WC()->integrations->get_integrations();

		return isset( $integrations['maxmind_geolocation'] ) ? (bool) $integrations['maxmind_geolocation']->get_option( 'license_key' ) : false;
	}

	public static function formatLastRateUpdate( $lastRateUpdate ) {
		return $lastRateUpdate
			? sprintf(
				/* translators: %s is a date */
				__( 'Set on %s', 'woocommerce-multilingual' ),
				date( 'F j, Y g:i a', strtotime( $lastRateUpdate ) )
			)
			: null;
	}
}
