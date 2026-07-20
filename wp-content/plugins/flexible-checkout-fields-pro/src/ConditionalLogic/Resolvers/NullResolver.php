<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\RuleResolver;

/**
 * Default rule resolver. Rule can not be resolved on backend.
 */
class NullResolver implements RuleResolver {
	use ResultComparisonAware;

	private const DEFAULT_RESULT = false;

	public function can_resolve(): bool {
		return false;
	}

	public function resolve( array $rule ): bool {
		return $this->get_result_by_comparison( self::DEFAULT_RESULT, $rule['comparison'] );
	}
}
