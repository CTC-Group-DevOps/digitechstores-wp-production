<?php

class WCML_Exchange_Rates_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $exchange_rates_services;

	public function __construct( $woocommerce_wpml ) {
		parent::__construct();

		$this->woocommerce_wpml        = $woocommerce_wpml;
		$this->exchange_rates_services = $this->woocommerce_wpml->multi_currency->exchange_rate_services;
	}

	public function get_model() {
		$settings = $this->exchange_rates_services->get_settings();

		$last_updated = empty( $settings['last_updated'] ) ?
							/* translators: Shown in place of a date when the exchange rates have not been updated yet. Lower case, it sits after "Last updated". */
							'<i>' . __( 'never', 'woocommerce-multilingual' ) . '</i>' :
							date_i18n( 'F j, Y g:i a', $settings['last_updated'] );

		$model = [
			'strings'              => [

				/* translators: Heading of the section that fetches exchange rates from an online service. */
				'header'           => __( 'Automatic Exchange Rates', 'woocommerce-multilingual' ),
				/* translators: Message shown in place of the currency switcher preview, the exchange-rates table and the status list while the shop has only one currency. */
				'no_currencies'    => __( "You haven't added any secondary currencies.", 'woocommerce-multilingual' ),
				/* translators: Label of the checkbox that turns on fetching exchange rates from an online service. */
				'enable_automatic' => __( 'Enable automatic exchange rates', 'woocommerce-multilingual' ),
				/* translators: Label of the dropdown that picks which online service the exchange rates come from. */
				'services_label'   => __( 'Exchange rates source', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding a percentage added to the fetched exchange rate before it is used. */
				'lifting_label'    => __( 'Lifting charge', 'woocommerce-multilingual' ),
				/* translators: Explanation under the lifting-charge field; "the table above" is the list of currencies and their rates. */
				'lifting_details1' => __( 'The lifting charge adjusts the exchange rate provided by the selected service before it is saved. The exchange rates displayed in the table above include the lifting charge.', 'woocommerce-multilingual' ),
				/* translators: %s is an exchange rates service */
				'lifting_details2' => __( 'Exchange rate = %s exchange rate x (1 + lifting charge / 100)', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the key that identifies the shop to the exchange-rate service. "API key" stays in English: it is what the service calls it. */
				'services_api'     => __( 'API key (required)', 'woocommerce-multilingual' ),
				/* translators: Label of the dropdown that sets how often the exchange rates are fetched. */
				'frequency'        => __( 'Update frequency', 'woocommerce-multilingual' ),
				/* translators: Button that fetches the exchange rates once, straight away. Verb phrase, imperative. */
				'update'           => __( 'Update manually now', 'woocommerce-multilingual' ),
				/* translators: Tooltip of the disabled "Update manually now" button. */
				'update_tip'       => __( 'You have to save all settings before updating exchange rates', 'woocommerce-multilingual' ),
				/* translators: Option of the update-frequency dropdown: rates are only fetched when the shop owner asks. */
				'manually'         => __( 'Manually', 'woocommerce-multilingual' ),
				/* translators: Option of the update-frequency dropdown: rates are fetched every hour. */
				'hourly'           => __( 'Hourly', 'woocommerce-multilingual' ),
				/* translators: Option of the update-frequency dropdown: rates are fetched once a day. */
				'daily'            => __( 'Daily', 'woocommerce-multilingual' ),
				/* translators: Option of the update-frequency dropdown; a weekday dropdown follows it on the same line, so the phrase stays open. */
				'weekly'           => __( 'Weekly on', 'woocommerce-multilingual' ),
				/* translators: Option of the update-frequency dropdown; a day-of-month dropdown follows it on the same line, so the phrase stays open. */
				'monthly'          => __( 'Monthly on the', 'woocommerce-multilingual' ),
				/* translators: Placeholder text in the empty API-key field of the exchange-rate service. Verb phrase, imperative. */
				'key_placeholder'  => __( 'Enter API key', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the key that identifies the shop to the exchange-rate service. "API key" stays in English: it is what the service calls it. */
				'key_required'     => __( 'API key (required)', 'woocommerce-multilingual' ),
				/* translators: Warning shown when the Fixer.io exchange-rate service is picked. Keep the numbers, the currency code and the price. */
				'fixerio_warning'  => __( 'WARNING! Minor limitations include 1000 requests/month limit and EUR being the only available base currency for customers using a free account. If you need more than 1000 requests per month or want to use all 170 available base currencies, you’ll need to choose one of the paid plans starting at only $10 per month.', 'woocommerce-multilingual' ),
				/* translators: Warning shown when the exchange rates are set to update every hour. */
				'daily_warning'    => __( 'Updating the exchange rates on an hourly basis generates around 744 API calls a month. Please check that your exchange rates source can accommodate this higher usage.', 'woocommerce-multilingual' ),
				'nonce'            => wp_create_nonce( 'update-exchange-rates' ),
				'updated_time'     => sprintf(
					/* translators: %s is a date and time */
					__( 'Last updated: %s', 'woocommerce-multilingual' ),
					'<span class="time">' . $last_updated . '</span>'
				),
				/* translators: Confirmation shown after the exchange rates were fetched. */
				'updated_success'  => __( 'Exchange rates updated successfully', 'woocommerce-multilingual' ),
				/* translators: Link that opens the website of the chosen exchange-rate service. Verb phrase, imperative. */
				'visit_website'    => __( 'Visit website', 'woocommerce-multilingual' ),

			],

			'services'             => $this->get_services_model(),
			'settings'             => $settings,

			'secondary_currencies' => $this->woocommerce_wpml->multi_currency->get_currencies(),

			'exchange_rates_manually_updated' => (bool) wp_cache_get( WCML_Exchange_Rates::KEY_RATES_UPDATED_FLAG ),
			'has_actionable_service'          => $this->exchange_rates_services->is_current_service_actionable(),
		];

		return $model;
	}

	private function get_services_model() {
		$services = [];

		foreach ( $this->exchange_rates_services->get_services() as $id => $service ) {
			$services[ $id ] = [
				'name'         => $service->getName(),
				'url'          => $service->getUrl(),
				'requires_key' => $service->isKeyRequired(),
				'api_key'      => $service->getSetting( 'api-key' ),
				'last_error'   => $service->getLastError(),
			];
		}

		return $services;
	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/multi-currency/',
		];
	}

	public function get_template() {
		return 'exchange-rates.twig';
	}

}
