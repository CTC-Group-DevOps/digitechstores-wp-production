<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Route;

use WPDesk\FCF\Free\Settings\Route\RouteAbstract;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProviderRegistry;

/**
 * Base class for conditional logic routes.
 */
abstract class RuleBaseRoute extends RouteAbstract {

	/**
	 * @var OptionsProviderRegistry
	 */
	protected $options_provider_registry;

	public function __construct( OptionsProviderRegistry $options_provider_registry ) {
		$this->options_provider_registry = $options_provider_registry;
	}
}
