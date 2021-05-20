<?php

namespace WcPay360\Admin;

use WcPay360\Ajax\Admin\Admin_Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Admin_Loader {
	
	public $privacy;
	public $capture;
	public $ajax;
	public $status;
	
	public function __construct() {
		$this->load_privacy_policy();
		$this->load_capture();
		$this->load_admin_ajax();
		$this->load_status();
	}
	
	/**
	 * Loads the privacy policy
	 *
	 * @since 2.1.0
	 */
	public function load_privacy_policy() {
		if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
			return;
		}
		
		$this->privacy = new Privacy_Policy();
	}
	
	public function load_capture() {
		$this->capture = new Capture( 'pay360' );
		$this->capture->hooks();
	}
	
	public function load_admin_ajax() {
		$this->ajax = new Admin_Ajax();
		$this->ajax->hooks();
	}
	
	public function load_status() {
		$this->status = new System_Status( 'pay360' );
		$this->status->hooks();
	}
}