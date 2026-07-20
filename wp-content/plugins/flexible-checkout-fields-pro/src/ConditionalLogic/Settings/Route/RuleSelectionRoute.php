<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Route;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Free\Settings\Route\RouteInterface;

/**
 * Route for getting list of selections for conditional logic.
 */
class RuleSelectionRoute extends RuleBaseRoute implements RouteInterface {

	const REST_API_ROUTE = 'conditional-logic-selection';

	public function get_endpoint_route(): string {
		return self::REST_API_ROUTE;
	}

	public function get_endpoint_response( RouteParamBag $params ) {
		$category = $params->collection( 'form_values' )->getString( 'category' );

		$provider = $this->options_provider_registry->get( $category );

		return $provider->get_selections( $params );
	}
}
