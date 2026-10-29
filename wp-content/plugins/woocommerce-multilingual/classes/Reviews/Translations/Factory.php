<?php

namespace WCML\Reviews\Translations;

class Factory implements \IWPML_Deferred_Action_Loader, \IWPML_Frontend_Action_Loader, \IWPML_AJAX_Action_Loader {

	const HANDLERS = [
		FrontEndHooks::class,
		DemandProcessor::class,
	];

	public function create() {
		return array_map( 'WPML\Container\make', self::HANDLERS );
	}

	public function get_load_action() {
		return 'init';
	}
}
