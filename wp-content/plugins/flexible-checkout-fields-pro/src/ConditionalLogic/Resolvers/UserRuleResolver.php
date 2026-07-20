<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

/**
 * RuleResolver for cart rules
 */
class UserRuleResolver implements RuleResolver {
	use ResultComparisonAware;

	public function can_resolve(): bool {
		return true;
	}

	public function resolve( array $rules ): bool {
		$user = wp_get_current_user();

		if ( ! $user->roles ) {
			return false;
		}

		$raw_result = $this->has_same_values( $user->roles, $rules['values'] );

		return $this->get_result_by_comparison( $raw_result, $rules['comparison'] );
	}
}
