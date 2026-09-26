<?php
/**
 * Compatibility with "Members" WordPress plugin.
 *
 * @see https://wordpress.org/plugins/members
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'plugins_loaded', 'vc_init_vendor_members' );

/**
 * Initialize Members plugin vendor.
 *
 * Registers WPBakery capabilities with Members to prevent them from being
 * removed when editing roles through the Members plugin interface.
 *
 * @since 9.0
 */
function vc_init_vendor_members() {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( 'members/members.php' ) || function_exists( 'members_plugin' ) ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-members.php' );
		$vendor = new Vc_Vendor_Members();
		$vendor->load();
	}
}
