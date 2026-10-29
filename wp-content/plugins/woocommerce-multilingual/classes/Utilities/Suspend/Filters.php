<?php

namespace WCML\Utilities\Suspend;

use WPML\FP\Relation;
use function WPML\FP\pipe;
use function WPML\FP\spreadArgs;

class Filters implements Suspend {

	private $suspended;

	public function __construct( array $filtersToSuspend ) {
		$this->suspended = wpml_collect( $filtersToSuspend )->filter( spreadArgs( 'remove_filter' ) );
	}

	public function resume() {
		$this->suspended
			->filter( pipe( spreadArgs( 'has_filter' ), Relation::equals( false ) ) )
			->each( spreadArgs( 'add_filter' ) );
	}

	public function runAndResume( callable $function ) {
		$result = $function();

		$this->resume();

		return $result;
	}
}
