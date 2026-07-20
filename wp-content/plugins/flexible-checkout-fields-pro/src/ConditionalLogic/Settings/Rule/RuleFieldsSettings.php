<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Rule fields display definition setting.
 */
class RuleFieldsSettings implements Hookable {

	/**
	 * @var OptionsProviderRegistry
	 */
	private $provider_registry;

	/**
	 * @var string
	 */
	private const RULES_DEFINITION_SLUG = 'logicRulesDefinition';

	public function __construct( OptionsProviderRegistry $provider_registry ) {
		$this->provider_registry = $provider_registry;
	}

	public function hooks() {
		add_filter( 'flexible_checkout_fields/init_admin_settings', [ $this, 'init_admin_settings' ], 10, 1 );
	}

	/**
	 * Updates admin settings for javascript.
	 *
	 * @param array<string, mixed> $settings An array of settings.
	 * @return array<string, mixed> The updated settings array.
	 */
	public function init_admin_settings( array $settings ): array {
		$settings[ self::RULES_DEFINITION_SLUG ] = $this->provider_registry->reduce(
			function ( $definitions, $provider ) {
				$definitions[ $provider->get_value() ] = $provider->rule_definition();
				return $definitions;
			},
			[]
		);

		return $settings;
	}
}
