<?php

namespace WcPay360;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles script loading
 *
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Scripts {
	
	public function __construct() {
		$this->suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->version = $this->suffix ? \WC_Pay360::VERSION : rand( 1, 999 );
	}
	
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
	}
	
	/**
	 * Adds admin scripts
	 *
	 * @since 2.2.0
	 */
	public function admin_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		
		wp_register_script( 'pay360_admin', \WC_Pay360::plugin_url() . '/assets/js/admin.js', array( 'jquery' ), \WC_Pay360::VERSION, true );
		
		if ( in_array( str_replace( 'edit-', '', $screen_id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
			wp_enqueue_script( 'pay360_admin' );
		}
		
		wp_localize_script( 'pay360_admin', 'wc_pay360_params', array(
			'i18n_capture_payment'     => _x( 'Are you sure you want to capture the payment?', 'capture payment', \WC_Pay360::VERSION ),
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'capture_payment'          => wp_create_nonce( 'capture-payment' ),
			'il8n_integration_changed' => __( '<span>Integration Type changed!</span> <span>Save the change before you continue.</span >', \WC_Pay360::VERSION ),
		) );
	}
	
	public function frontend_scripts() {
	}
}