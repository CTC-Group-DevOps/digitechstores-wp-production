<?php

class WCML_Bonded_Menu {

	public static function is_active() {
		if ( defined( 'WCML_DISABLE_BONDED_MENU' ) && constant( 'WCML_DISABLE_BONDED_MENU' ) ) {
			return false;
		}

		return ! \WCML\functions\isStandAlone();
	}
}
