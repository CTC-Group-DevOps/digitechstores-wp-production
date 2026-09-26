<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2024 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

class RevSliderLoadBalancer extends RevSliderFunctions {

	public $last_request = null;
	public $servers = [];
	public $defaults = ['themepunch.tools', 'themepunch-ext-a.tools', 'themepunch-ext-b.tools', 'themepunch-ext-c.tools'];
	 

	/**
	 * set the server list on construct
	 **/
	public function __construct(){
		$this->servers = get_option('revslider_servers', []);
		if(empty($this->servers)){
			shuffle($this->defaults);
			update_option('revslider_servers', $this->defaults);
		}
		
		$this->servers = (empty($this->servers)) ? $this->defaults : $this->servers;
		
		
	}

	public function get_last_request(){
		return $this->last_request;
	}

	/**
	 * get the url depending on the purpose, here with key, you can switch do a different server
	 **/
	public function get_url($purpose, $key = 0, $force_http = false){
		$url	 = ($force_http ) ? 'http://' : 'https://';
		$use_url = (!isset($this->servers[$key])) ? reset($this->servers) : $this->servers[$key];
		
		switch($purpose){
			case 'updates':
				$url .= 'updates.';
				break;
			case 'templates':
				$url .= 'templates.';
				break;
			case 'library':
				$url .= 'library.';
				break;
			default:
				return false;
		}
		
		$url .= $use_url;
		
		return $url;
	}
	
	/**
	 * refresh the server list to be used, will be done once in a month
	 **/
	public function refresh_server_list($force = false){
		global $wp_version;
		
		$rs_rsl		= (isset($_GET['rs_refresh_server'])) ? true : false;
		$last_check	= get_option('revslider_server_refresh', false);
		if($last_check === false || empty($last_check)) update_option('revslider_server_refresh', time());

		if($force === true || $rs_rsl === true || ($last_check !== false && time() - $last_check > 60 * 60 * 24 * 30)){
			$data = [
				'item'    => urlencode( RS_PLUGIN_SLUG ),
				'version' => urlencode( RS_REVISION )
			];
			$request = $this->call_url('https://updates.themepunch.tools/get_server_list.php', $data);
			if(!is_wp_error($request)){
				if($response = maybe_unserialize($request['body'])){
					$list = json_decode($response, true);
					if(json_last_error() === JSON_ERROR_NONE && is_array($list)) {
						update_option('revslider_servers', $list);
						$this->servers = $list;
					}
				}
			}
			
			update_option('revslider_server_refresh', time());
		}
	}
	
	/**
	 * move the server list, to take the next server as the one currently seems unavailable
	 **/
	public function move_server_list(){
		$servers	= $this->servers;
		$a			= array_shift($servers);
		$servers[]	= $a;
		
		$this->servers = $servers;
		update_option('revslider_servers', $servers);
	}
	
	/**
	 * call an themepunch URL and retrieve data
	 **/
	public function call_url($url, $data, $subdomain = 'updates', $force_http = false){
		global $wp_version;
		
		//add version if not passed
		$data['version'] = (!isset($data['version'])) ? urlencode(RS_REVISION) : $data['version'];
		$count	= 0;
		
		do{
			$is_full_url = false;
			$full_url = $url;
			if(!preg_match("/^https?:\/\//i", $full_url)){

				//just a filename passed, lets build an url
				$server	 = $this->get_url($subdomain, 0, $force_http);
				$full_url = $server . '/' . ltrim($full_url, '/');
			}else{
				$is_full_url = true;
				//full URL passed, lets check if we need to force http 
				if($force_http) $full_url = preg_replace("/^https:\/\//i", "http://", $full_url);
			}

			$cf_lock = $this->get_cf_rate_limit_lock($full_url);
			if ( ! $cf_lock ) {
				$request = wp_safe_remote_post($full_url, [
					'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
					'body'		 => $data,
					'timeout'	 => 45
				]);

				$response_code = wp_remote_retrieve_response_code($request);
				if($response_code == 200) return $request;

				if($response_code == 429) {
					$cf_lock = $this->set_cf_rate_limit_lock($full_url, $request);
					if ( $cf_lock ) {
						$request            = $this->get_cf_wp_error( $cf_lock );
						$this->last_request = $request;
					}
				}
			} else {
				$request = $this->get_cf_wp_error($cf_lock);
				$this->last_request = $request;
			}

			if ($is_full_url) {
				// full URL passed, no need to try other servers
				break;
			}

			$this->move_server_list();
			$count++;
		}while( $count < 3 );
		
		return $request;
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 */
	public function get_cf_transient_key($url){
		$host = parse_url($url, PHP_URL_HOST);
		if (empty($host) || !strpos($host, '.tools')) return '';

		return 'rs-cf-rate-limit-tools-' . md5($host);
	}

	/**
	 * @param string         $url      Requested URL
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 *
	 * @return false|int
	 */
	public function set_cf_rate_limit_lock($url, $response){
		$cf_transient_key = $this->get_cf_transient_key($url);
		if (empty($cf_transient_key)) return false;

		// CF send header "retry-after: 3586"
		// seconds before next attempt
		$retry_after = (int) wp_remote_retrieve_header($response, 'retry-after');
		if ($retry_after <= 0) {
			$retry_after = 3600;
		}

		$cf_lock = time() + $retry_after;
		set_transient($cf_transient_key, $cf_lock, $retry_after);

		return $cf_lock;
	}

	/**
	 * @param string $url Requested URL
	 *
	 * @return false|int
	 */
	public function get_cf_rate_limit_lock($url){
		$cf_transient_key = $this->get_cf_transient_key($url);
		if (empty($cf_transient_key)) return false;

		return get_transient($cf_transient_key);
	}

	/**
	 * @param int $cf_lock
	 *
	 * @return WP_Error
	 */
	public function get_cf_wp_error($cf_lock){
		return new WP_Error('rate_limit_error', 'Too many requests from your IP, please try again in ' . human_time_diff( time(), $cf_lock ) . '.');
	}

}
