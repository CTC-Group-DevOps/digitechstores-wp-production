<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\RuleResolver;

/**
 * Resolves date rules.
 */
class DateResolver implements RuleResolver {

	public function can_resolve(): bool {
		return true;
	}

	public function resolve( array $rules ): bool {
		$current_timestamp = \current_time( 'timestamp' );
		$input_timestamp   = \strtotime( $rules['values'] );

		switch ( $rules['comparison'] ) {
			case 'is':
				return date( 'Y-m-d', $current_timestamp ) === date( 'Y-m-d', $input_timestamp );
			case 'from':
				return $current_timestamp >= $input_timestamp;
			case 'until':
				return $current_timestamp <= $input_timestamp;
		}

		return false;
	}
}
