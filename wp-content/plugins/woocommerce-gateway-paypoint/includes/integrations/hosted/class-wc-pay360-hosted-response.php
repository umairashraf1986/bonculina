<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle responses for Pay360 Hosted + IMA integration
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Hosted_Response extends WC_Pay360_Abstract_Response_Legacy {

	/**
	 * Initialize the Response Hosted class
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}

	/**
	 * Catch the Pay360 Response
	 *
	 * @since 2.0
	 **/
	public function route_response() {
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Hosted: Received Response GET: ' . print_r( $_GET, true ) );

		// Get the order from the response parameters
		$order = $this->get_order_from_response();

		try {
			if ( $this->validate_response( $order ) ) {
				$this->process_response( $order );
			}

			$this->end_execution( $order );
		}
		catch ( \WcPay360\Api\Exceptions\Response_Exception $e ) {
			$this->end_execution( $e->get_order() );
		}
		catch ( Exception $e ) {
			wc_add_notice( $e->getMessage() );
			$this->end_execution();
		}
	}

	/**
	 * Process a successfully validated response
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 */
	public function process_response( WC_Order $order ) {
		if ( 'A' == WC_Pay360::get_get( 'code' ) ) {

			// Check amounts
			$this->validate_transaction_amount( $order, WC_Pay360::get_get( 'amount' ) );

			// Check currency
			$this->validate_store_currency( $order, WC_Pay360::get_get( 'currency' ) );

			if ( 'auth' == $this->get_gateway()->transaction_type_h ) {
				// The money were not taken, so we don't want to Process the order just, yet.
				// But we will allow the merchant to set their own
				$status = apply_filters( 'wc_pay360_deferred_payment_order_status', 'on-hold', $order, $this );
				$status = ! wc_is_order_status( 'wc-' . ltrim( $status, 'wc-' ) ) ? 'on-hold' : $status;

				$order->update_status( $status, __( 'Transaction was successfully Authorized.', WC_Pay360::TEXT_DOMAIN ) );

				// Remove cart
				WC_Pay360_Compat::empty_cart();

				do_action( 'pay360_hosted_successful_auth_transaction', $order, WC_Pay360::get_get( 'code' ), WC_Pay360::get_get( 'auth_code' ) );
			} else {
				$this->complete_order( $order, WC_Pay360::get_get( 'auth_code' ) );

				do_action( 'pay360_hosted_successful_transaction', $order, WC_Pay360::get_get( 'code' ), WC_Pay360::get_get( 'auth_code' ) );
			}

			//Debug log
			WC_Pay360_Debug::add_debug_log( __( 'Pay360 payment completed.', WC_Pay360::TEXT_DOMAIN ) );
		} else {

			// resp_code - When "code = N" this parameter is the bank code
			// message - When "code = N" this parameter is the bank code

			$fail_message = WC_Pay360::get_get( 'message' );
			$resp_code    = WC_Pay360::get_get( 'resp_code' );
			$message      = sprintf( __( 'Pay360 payment failed. Bank fail message: %s', WC_Pay360::TEXT_DOMAIN ), $fail_message );

			if ( '' != $fail_message ) {
				$message .= sprintf( __( ' Bank fail message: %s', WC_Pay360::TEXT_DOMAIN ), $fail_message );
			}

			if ( '' != $resp_code ) {
				$message .= sprintf( __( ' Bank fail code: %s', WC_Pay360::TEXT_DOMAIN ), $resp_code );
			}

			//Change status Failed
			$this->order_status_failed( $order, $message );

			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );

			do_action( 'pay360_hosted_failed_transaction', $order, $fail_message, $resp_code );
		}
	}

	/**
	 * Validate Pay360 IMA Response
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 */
	public function validate_response( WC_Order $order, $response = null ) {
		// Validate security
		$this->validate_security_hash( $order, $this->hash_parameters(), WC_Pay360::get_get( 'hash' ) );

		// Check invoice
		$this->validate_order_invoice( $order, WC_Pay360::get_get( 'order_key' ) );

		// Check if the transaction is the same test status as your store
		$this->validate_gateway_mode( $order, WC_Pay360::get_get( 'test_status' ), $this->get_gateway()->testmode );

		return true;
	}

	/**
	 * Ensure no test transaction is made, when plugin is supposed to run live transactions
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $transaction_mode
	 * @param string   $gateway_mode
	 */
	protected function validate_gateway_mode( WC_Order $order, $transaction_mode, $gateway_mode ) {
		if ( 'true' == $transaction_mode && 'live' == $gateway_mode ) {
			$message = __( 'You cannot make test payments when your store is set to live mode.', WC_Pay360::TEXT_DOMAIN );

			$this->order_status_failed( $order, $message );

			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );

			$this->throw_response_exception( $order, $message );
		}
	}

	/**
	 * HOSTED PAYMENT
	 * Hash the main response parameters
	 *
	 * @since 2.0
	 */
	public function hash_parameters() {
		$hash_string = ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';

		if ( ! empty( $hash_string ) ) {
			// Remove the hash from the back of the string
			$hash_string = substr( $hash_string, 0, - 37 );
		}

		WC_Pay360_Debug::add_debug_log( 'The Hash String: ' . $hash_string );

		// Add the Secret at the back
		$hash_string .= $this->get_gateway()->digest_key_h;

		return md5( $hash_string );
	}
}