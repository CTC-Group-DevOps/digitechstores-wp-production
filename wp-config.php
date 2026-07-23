<?php



 // Added by WP Rocket

// // Added by WP Rocket

/** WP 2FA plugin data encryption key. For more information please visit melapress.com */
define( 'WP2FA_ENCRYPT_KEY', 'J4L6sg8l0/rf4Epf8+n6sA==' );
//define( 'WPCACHEHOME', '/home/digitechstores/public_html/wp-content/plugins/wp-super-cache/' );
define('WP_AUTO_UPDATE_CORE', 'minor');// This setting is required to make sure that WordPress updates can be properly managed in WordPress Toolkit. Remove this line if this WordPress website is not managed by WordPress Toolkit anymore.
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'digitechstores_prodnew');
/** MySQL database username */
define('DB_USER', 'digitechstores_prodnew');
/** MySQL database password */
define('DB_PASSWORD', '$2{K9KM$8pR_qken');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
define('WP_MEMORY_LIMIT', '1024M');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ccvuii7zuxbxetuxnhqu0ec3uhbxsuauddzpxxqx1co3alhiwluus5iahl3ul2lu');
define('SECURE_AUTH_KEY',  'dg2bn9wnntmejit5u2mttkfunses26kniwwrpgp7f3xsje5xvl8l7j4tgl5ces3h');
define('LOGGED_IN_KEY',    'maqekbibbvspjonig2akrvfbdupnezxbyibigoiofs0zgefkte3j4ar89hx0g0vm');
define('NONCE_KEY',        'wrwh8yrfowugov7tjhesq2y931iblqmqmhzzmozm8alfg7spfezbwpxgdkpcitqs');
define('AUTH_SALT',        'a1ts01glr3g1iwvusgbikpacazcelzjadg3bz5fdlx5l6grz1dt7bgqas9ixkvfj');
define('SECURE_AUTH_SALT', 'fylrcoo60cxew8oid8c1fzffek58jqkzaaal0ienxpnz2c5tk2lbtye3mjd5oxc6');
define('LOGGED_IN_SALT',   'uplkxl088zrk6nk0svudh1c6idp6prjzrhsrkfkn1fovgtvznghrrpxt5mdcar3w');
define('NONCE_SALT',       'z64karro5rdah67spitt1ffbrdbc3rgyrqfsmgtwhkqrgjl1w7y0f00umobljdya');
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp7e_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
#define(‘DISABLE_WP_CRON’, true);
/***************added by mustafa*********************/
@ini_set( 'display_errors', 0 );
/************************************/
//define('WP_ALLOW_REPAIR', true);
/*define( 'WP_DEBUG_LOG', true );
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );*/

define('DISABLE_WP_CRON', true);

/* That's all, stop editing! Happy blogging. */
define( 'AUTOMATIC_UPDATER_DISABLED', true );



/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	define('CONCATENATE_SCRIPTS', false);
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');