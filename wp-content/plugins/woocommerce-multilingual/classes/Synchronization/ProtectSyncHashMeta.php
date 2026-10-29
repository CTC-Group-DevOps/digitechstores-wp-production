<?php

namespace WCML\Synchronization;

use WCML\Utilities\SyncHash;

class ProtectSyncHashMeta implements \IWPML_Backend_Action, \IWPML_Frontend_Action {

	public function add_hooks() {
		add_filter( 'is_protected_meta', [ $this, 'protectMeta' ], 10, 2 );
	}

	public function protectMeta( $isProtected, $metaKey ) {
		if ( SyncHash::META_KEY === $metaKey ) {
			return true;
		}

		return $isProtected;
	}
}
