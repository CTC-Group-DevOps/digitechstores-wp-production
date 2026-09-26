<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule;

use Traversable;
use ArrayIterator;
use IteratorAggregate;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider\OptionsProvider;

/**
 * Collection class for rule fields options providers.
 * Every conditional logic rule category have to implement its own options provider.
 *
 * @implements IteratorAggregate<string, OptionsProvider>
 */
class OptionsProviderRegistry implements IteratorAggregate {

	/**
	 * List of options providers.
	 *
	 * @var array<string, OptionsProvider>
	 */
	private $providers = [];

	public function register( OptionsProvider $provider ): void {
		$this->providers[ $provider->get_value() ] = $provider;
	}

	public function get( string $value ): OptionsProvider {
		return $this->providers[ $value ];
	}

	/**
	 * @return Traversable<string, OptionsProvider>
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->providers );
	}

	/**
	 * @param \Closure $callback The callback function to apply
	 * @param mixed $initial The initial value
	 * @return mixed The single value resulting from the reduction
	 */
	public function reduce( \Closure $callback, $initial = null ) {
		return array_reduce( $this->providers, $callback, $initial );
	}
}
