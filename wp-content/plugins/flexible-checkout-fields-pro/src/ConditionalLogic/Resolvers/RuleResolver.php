<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

interface RuleResolver {

	/**
	 * Whether given rule can be resolved on the backend.
	 *
	 * @deprecated since version 4.0.6
	 */
	public function can_resolve(): bool;

	/**
	 * Resolve a rule on the backend side.
	 */
	public function resolve( array $rules ): bool;
}
