<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2024 ThemePunch
 * @since	  6.2.0
 */

if(!defined('ABSPATH')) exit();

class RevSliderLicense extends RevSliderFunctions {
	/**
	 * Activate the Plugin through the ThemePunch Servers
	 * @before 6.0.0: RevSliderOperations::checkPurchaseVerification();
	 * @before 6.2.0: RevSliderAdmin::activate_plugin();
	 **/
	public function activate_plugin($code){
		$rstrack = new RevSliderTracking();
		$rstrack->_run(true);

		$rslb = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
		$data = array(
			'code'		=> urlencode($code),
			'version'	=> urlencode(RS_REVISION),
			'product'	=> urlencode(RS_PLUGIN_SLUG),
			'addition'	=> apply_filters('revslider_activate_plugin_info_addition', array())
		);
		
		$response	  = $rslb->call_url('activate.php', $data, 'updates');
		$version_info = wp_remote_retrieve_body($response);
		
		if(is_wp_error($version_info)) return false;
		if($version_info == 'valid'){
			update_option('revslider-valid', 'true');
			update_option('revslider-code', $code);
			update_option('revslider-deregister-popup', 'false');
			//the licence type the update check last reported belongs to the old key (or to none): the next admin page asks
			//again instead of in 48 hours, so a lifetime or CodeCanyon key shows its SR7 path at once (as SR7's License does)
			delete_option('revslider-update-check-short');

			return true;
		}
		if($version_info == 'exist') return 'exist';
		if($version_info == 'banned') return 'banned';
		
		return false;
	}
	
	
	/**
	 * Deactivate the Plugin through the ThemePunch Servers
	 * @before 6.0.0: RevSliderOperations::doPurchaseDeactivation();
	 * @before 6.2.0: RevSliderAdmin::deactivate_plugin();
	 **/
	public function deactivate_plugin(){
		$rstrack = new RevSliderTracking();
		$rstrack->_run(false);

		$rslb = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
		$code = get_option('revslider-code', '');
		$data = array(
			'code' => urlencode($code),
			'product' => urlencode(RS_PLUGIN_SLUG),
			'addition' => apply_filters('revslider_deactivate_plugin_info_addition', array())
		);
		
		$res = $rslb->call_url('deactivate.php', $data, 'updates');
		$vi	 = wp_remote_retrieve_body($res);
		
		if(is_wp_error($vi)) return false;

		if($vi == 'valid'){
			update_option('revslider-valid', 'false');
			update_option('revslider-code', '');
			update_option('revslider-deregister-popup', 'false'); //the user deregistered: "Deactivated!" is for a key the server took back (update.class.php)
			delete_option('revslider-update-check-short');

			return true;
		}
		
		return false;
	}
}