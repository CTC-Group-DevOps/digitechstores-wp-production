<?php
/**
 * Plugin Name: Woodmart Core
 * Description: Woodmart Core needed for Woodmart theme
 * Version: 1.1.9
 * Text Domain: woodmart-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

define( 'WOODMART_CORE_PLUGIN_VERSION', '1.1.9' );

require_once __DIR__ . '/functions.php';

require_once 'class-post-types.php';

require_once 'inc/shortcodes.php';
require_once 'inc/widgets.php';
require_once 'inc/meta-boxes.php';

require_once 'vendor/opauth/opauth/lib/Opauth/Opauth.php';
require_once 'vendor/opauth/twitteroauth/twitteroauth.php';
require_once 'inc/class-auth.php';
