<?php

namespace WCML\Multicurrency\StripeConnect;

use WCML\StandAlone\IStandAloneAction;
use WCML\Utilities\AdminUrl;

class Factory implements \IWPML_Backend_Action_Loader, \IWPML_Frontend_Action_Loader, \IWPML_Deferred_Action_Loader, IStandAloneAction {

	public function get_load_action() {
		return 'init';
	}

	public function create() {
		if ( ! wcml_is_multi_currency_on() || ! Connect::isAvailable() ) {
			return null;
		}

		return new Hooks( new Connect( new \WC_Stripe_Connect_API(), AdminUrl::getMultiCurrencyDestination() ) );
	}
}
