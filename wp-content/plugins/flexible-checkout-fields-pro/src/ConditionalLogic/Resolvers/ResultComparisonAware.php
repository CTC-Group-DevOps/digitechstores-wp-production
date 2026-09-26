<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

/**
 * Trait for rule resolvers that need some extra functions for result comparisons.
 */
trait ResultComparisonAware {

	/**
	 * Check if two arrays have the same values.
	 * Similar to in_array, but can have array as a first argument.
	 *
	 * @param array<int, string> $needle
	 * @param array<int, string> $haystack
	 * @return boolean
	 */
	public function has_same_values( array $needle, array $haystack ): bool {
		$same_values = array_intersect( $needle, $haystack );
		return ! empty( $same_values );
	}

	/**
	 * Get result by comparison.
	 *
	 * @param boolean $result
	 * @param string $comparison
	 * @return boolean
	 */
	public function get_result_by_comparison( bool $result, string $comparison ): bool {
		switch ( $comparison ) {
			case 'is':
			case 'which_is':
				return $result;
			case 'is_not':
			case 'which_is_not':
				return ! $result;
			default:
				return false;
		}
	}
}
