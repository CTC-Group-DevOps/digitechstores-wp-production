<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Route;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Free\Settings\Route\RouteInterface;

/**
 * Route for getting list of categories for conditional logic.
 */
class RuleCategoryRoute extends RuleBaseRoute implements RouteInterface {

	const REST_API_ROUTE = 'conditional-logic-category';

	public function get_endpoint_route(): string {
		return self::REST_API_ROUTE;
	}

	public function get_endpoint_response( RouteParamBag $params ) {
		return $this->options_provider_registry->reduce(
			function ( $options, $provider ) {
				$options[ $provider->get_value() ] = $provider->get_label();
				return $options;
			},
			[]
		);
	}
}
