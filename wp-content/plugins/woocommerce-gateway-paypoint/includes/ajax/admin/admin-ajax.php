<?php

namespace WcPay360\Ajax\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ajax class to handle ajax requests
 *
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Admin_Ajax {
	
	public function hooks() {
		add_action( 'wp_ajax_wc_pay360_capture_payment', array( $this, 'capture_payment' ) );
	}
	
	/**
	 * @since 2.2.0
	 */
	public function capture_payment() {
		ob_start();
		
		try {
			check_ajax_referer( 'capture-payment', 'security' );
			
			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				throw new \Exception( __( 'No access right to perform this action', \WC_Pay360::TEXT_DOMAIN ) );
			}
			
			$order_id = (int) \WC_Pay360::get_field( 'order_id', $_POST, 0 );
			$order    = wc_get_order( $order_id );
			
			// Run the capture payment action
			$result = apply_filters( 'wc_pay360_capture_payment_for_order', $order );
			
			if ( true !== $result ) {
				wp_send_json_error( array( 'message' => $result ) );
			}
			
			wp_send_json_success( array( 'message' => __( 'Amount was successfully captured', \WC_Pay360::TEXT_DOMAIN ) ) );
		}
		catch ( \Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}
}