<?php

namespace WCML\Utilities;

use WPML_Notices;

class AdminNotices {

	public static function get() {
		return function_exists( 'wcml_wpml_get_admin_notices' )
			? wcml_wpml_get_admin_notices()
			: wpml_get_admin_notices();
	}
}
