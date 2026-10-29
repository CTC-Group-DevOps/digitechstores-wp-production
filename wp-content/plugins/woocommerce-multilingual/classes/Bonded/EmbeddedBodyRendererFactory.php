<?php

namespace WCML\Bonded;

use WCML\StandAlone\IStandAloneAction;

class EmbeddedBodyRendererFactory implements \IWPML_Backend_Action_Loader, \IWPML_Deferred_Action_Loader, IStandAloneAction {

	public function get_load_action() {
		return 'init';
	}

	public function create() {
		if ( class_exists( '\WCML_Bonded_Menu' ) && \WCML_Bonded_Menu::is_active() ) {
			return new EmbeddedBodyRenderer();
		}

		return null;
	}
}
