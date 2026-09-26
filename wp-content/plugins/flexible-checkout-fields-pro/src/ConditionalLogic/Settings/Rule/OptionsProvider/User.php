<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleValues;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleSelection;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleComparison;

/**
 * Options provider for rules around user role.
 */
class User extends OptionsProvider {

	public function get_label(): string {
		return __( 'User', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'user';
	}

	public function rule_definition(): array {
		return [
			LogicRuleSelection::FIELD_NAME,
			LogicRuleComparison::FIELD_NAME,
			LogicRuleValues::FIELD_NAME,
		];
	}

	public function get_selections( RouteParamBag $params ): array {
		return [
			'role' => __( 'Role', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_comparisons( RouteParamBag $params ): array {
		return [
			'is'     => __( 'Is', 'flexible-checkout-fields-pro' ),
			'is_not' => __( 'Is not', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_values( RouteParamBag $params ): array {
		return $this->get_available_roles();
	}

	/**
	 * Get list of available roles.
	 *
	 * @return array<string, string>
	 */
	private function get_available_roles(): array {
		$all_roles = wp_roles()->roles;

		$values = [];
		foreach ( $all_roles as $role_slug => $role ) {
			$values[ $role_slug ] = $role['name'];
		}
		asort( $values );

		return $values;
	}
}
