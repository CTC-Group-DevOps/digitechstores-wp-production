<?php

class WCML_Setup_Multi_Currency_UI extends WCML_Setup_Step {

	const SLUG = 'multi-currency';

	public function get_model() {
		return [
			'strings'           => [
				'step_id'     => 'currency_step',
				/* translators: Heading of the setup-wizard step about multicurrency. */
				'heading'     => __( 'Do you want to add more currencies to your store?', 'woocommerce-multilingual' ),
				/* translators: Line above the list of what multicurrency offers, in the setup wizard; the list follows the colon. */
				'description' => __( 'You will be able to:', 'woocommerce-multilingual' ),
				/* translators: Item in the list of what multicurrency offers, in the setup wizard. Verb phrase, imperative. */
				'bullet1'     => __( 'Add a currency switcher to your store', 'woocommerce-multilingual' ),
				/* translators: Item in the list of what multicurrency offers, in the setup wizard. Verb phrase, imperative. */
				'bullet2'     => __( 'Set exchange rates', 'woocommerce-multilingual' ),
				/* translators: Item in the list of what multicurrency offers, in the setup wizard. Verb phrase, imperative. */
				'bullet3'     => __( 'Create custom pricing in each currency', 'woocommerce-multilingual' ),
				/* translators: Last item in the list of what multicurrency offers, in the setup wizard. */
				'bullet4'     => __( 'And more!', 'woocommerce-multilingual' ),
				/* translators: Button in the setup wizard that turns multicurrency on. */
				'enable'      => __( 'Yes, enable multicurrency mode', 'woocommerce-multilingual' ),
				/* translators: Button in the setup wizard that leaves the shop on one currency. */
				'continue'    => __( 'No, use only one currency', 'woocommerce-multilingual' ),
				/* translators: Button label that returns the setup wizard to the previous step. Verb phrase, imperative. */
				'go_back'     => __( 'Go back', 'woocommerce-multilingual' ),
			],
			'documentation_url' => WCML_Tracking_Link::getWcmlMultiCurrencyDoc( false, [ 'utm_term' => WCML_Tracking_Link::UTM_TERM_WIZARD ] ),
			'multi_currency_on' => wcml_is_multi_currency_on(),
			'continue_url_multicurrency_on'  => add_query_arg( WCML_Setup::MULTI_CURRENCY_STATUS_GET_KEY, '1', $this->next_step_url ),
			'continue_url_multicurrency_off' => add_query_arg( WCML_Setup::MULTI_CURRENCY_STATUS_GET_KEY, '0', $this->next_step_url ),
			'go_back_url'       => $this->previous_step_url,
		];
	}

	public function get_template() {
		return '/setup/multi-currency.twig';
	}
}
