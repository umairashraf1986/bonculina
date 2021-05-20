<?php 

require_once 'wonderplugin-videoembed-functions.php';

class WonderPlugin_Videoembed_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-videoembed/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-videoembed/';
	}
	
	function get_settings() {
		
		$keepdata = get_option( 'wonderplugin_videoembed_keepdata', 1 );
		$disableupdate = get_option( 'wonderplugin_videoembed_disableupdate', 0 );
		$addjstofooter = get_option( 'wonderplugin_videoembed_addjstofooter', 0 );
		$zindex = get_option( 'wonderplugin_videoembed_zindex', '100102' );
		
		$settings = array(
				"keepdata" => $keepdata,
				"disableupdate" => $disableupdate,
				"addjstofooter" => $addjstofooter,
				"zindex" => $zindex
		);
	
		return $settings;
	}
	
	function save_settings($options) {
	
		$keepdata = (!isset($options) || !isset($options['keepdata'])) ? 0 : 1;
		update_option( 'wonderplugin_videoembed_keepdata', $keepdata );
		
		$disableupdate = (!isset($options) || !isset($options['disableupdate'])) ? 0 : 1;
		update_option( 'wonderplugin_videoembed_disableupdate', $disableupdate );
		
		$addjstofooter = (!isset($options) || !isset($options['addjstofooter'])) ? 0 : 1;
		update_option( 'wonderplugin_videoembed_addjstofooter', $addjstofooter );
		
		$zindex = (!isset($options) || !isset($options['zindex'])) ? '100102' : $options['zindex'];
		update_option( 'wonderplugin_videoembed_zindex', $zindex );
	}
	
	function get_plugin_info() {
	
		$info = get_option('wonderplugin_videoembed_information');
		if ($info === false)
			return false;
	
		return unserialize($info);
	}
	
	function save_plugin_info($info) {
	
		update_option( 'wonderplugin_videoembed_information', serialize($info) );
	}
	
	function check_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-videoembed-key']) )
		{
			return $ret;
		}
	
		$key = sanitize_text_field( $options['wonderplugin-videoembed-key'] );
		if ( empty($key) )
			return $ret;
	
		$update_data = $this->controller->get_update_data('register', $key);
		if( $update_data === false )
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
	
		if ( isset($update_data->key_status) )
			$ret['status'] = $update_data->key_status;
	
		return $ret;
	}
	
	function deregister_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-videoembed-key']) )
			return $ret;
	
		$key = sanitize_text_field( $options['wonderplugin-videoembed-key'] );
		if ( empty($key) )
			return $ret;
	
		$info = $this->get_plugin_info();
		$info->key = '';
		$info->key_status = 'empty';
		$info->key_expire = 0;
		$this->save_plugin_info($info);
	
		$update_data = $this->controller->get_update_data('deregister', $key);
		if ($update_data === false)
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
	
		$ret['status'] = 'success';
	
		return $ret;
	}
}