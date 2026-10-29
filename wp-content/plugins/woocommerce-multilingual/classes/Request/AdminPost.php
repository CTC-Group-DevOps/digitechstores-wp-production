<?php

namespace WCML\Request;

final class AdminPost {

	public static function register( $action, array $spec, callable $callback, $priority = 10, $acceptedArgs = 1 ) {
		if ( self::coreAvailable() ) {
			\WPML\Request\Adapter\AdminPost::register( $action, Ajax::policy( $spec, $action ), $callback, $priority, $acceptedArgs );

			return;
		}

		add_action( 'admin_post_' . $action, $callback, $priority, $acceptedArgs );
		if ( isset( $spec['public'] ) || isset( $spec['machine'] ) ) {
			add_action( 'admin_post_nopriv_' . $action, $callback, $priority, $acceptedArgs );
		}
	}

	public static function url( $action, array $args = [] ) {
		$query = [ 'action' => (string) $action ];
		foreach ( $args as $key => $value ) {
			$query[ $key ] = rawurlencode( (string) $value );
		}

		return add_query_arg( $query, admin_url( 'admin-post.php' ) );
	}

	public static function coreAvailable() {
		return Ajax::coreAvailable() && class_exists( '\WPML\Request\Adapter\AdminPost' );
	}
}
