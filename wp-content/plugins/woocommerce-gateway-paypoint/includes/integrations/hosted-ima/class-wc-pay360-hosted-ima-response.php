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
class WC_Pay360_Hosted_IMA_Response extends WC_Pay360_Abstract_Response_Legacy {
	
	/**
	 * Initialize the Response IMA class
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
		if ( WC_Pay360::get_post( 'PT_pay360Listener' ) == 'pay360Response' ) {
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( 'Hosted + IMA Response received:  ' . print_r( $_POST, true ) );
			
			// Get the order from the response parameters
			$order = $this->get_order_from_response();
			
			try {
				if ( $this->validate_response( $order ) ) {
					$this->process_response( $order );
				}
			}
			catch ( \WcPay360\Api\Exceptions\Response_Exception $e ) {
				$this->end_execution( $e->get_order() );
			}
			catch ( Exception $e ) {
				wc_add_notice( $e->getMessage() );
				$this->end_execution();
			}
			
			// Notify Pay360 response was received
			header( 'HTTP/1.0 200 OK' );
			exit;
		}
		
		// If returning customer
		if ( WC_Pay360::get_post( 'strCartID' ) && null == WC_Pay360::get_post( 'PT_pay360Listener' ) ) {
			
			$_POST = stripslashes_deep( $_POST );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( 'Hosted + IMA Response received:  ' . print_r( $_POST, true ) );
			
			$args  = explode( ':', WC_Pay360::get_post( 'strCartID' ) );
			$order = wc_get_order( (int) $args[0] );
			if ( 0 == WC_Pay360::get_post( 'intStatus' ) ) {
				$this->process_failed_payment( $order, WC_Pay360::get_post( 'intStatus' ) );
			}
			
			wp_redirect( $this->get_gateway()->get_return_url( $order ) );
			exit;
		}
	}
	
	/**
	 * Process a successfully validated response
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 */
	public function process_response( $order ) {
		$transaction_status = WC_Pay360::get_post( 'intStatus' );
		$transaction_id     = WC_Pay360::get_post( 'intTransID' );
		
		if ( 1 == $transaction_status ) {
			$this->process_successful_payment( $order, $transaction_status, $transaction_id );
		} else {
			$this->process_failed_payment( $order, $transaction_status );
		}
	}
	
	/**
	 * Processes a successful payment.
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $transaction_status
	 * @param string   $transaction_id
	 *
	 * @return bool
	 */
	public function process_successful_payment( $order, $transaction_status, $transaction_id ) {
		if ( $order->has_status( WC_Pay360_Compat::get_is_paid_statuses() ) ) {
			return false;
		}
		
		$this->complete_order( $order, $transaction_id );
		
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Hosted + IMA Payment Completed' );
		
		do_action( 'pay360_ima_successful_transaction', $order, $transaction_status, $transaction_id );
		
		return true;
	}
	
	/**
	 * Processes a failed payment.
	 *
	 * 1. Marks the order as failed
	 * 2. Adds a note to the order.
	 * 3. Runs a failed payment action "pay360_ima_failed_transaction"
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $status
	 *
	 * @return bool
	 */
	public function process_failed_payment( $order, $status ) {
		
		if ( $order->has_status( WC_Pay360_Compat::get_is_paid_statuses() ) ) {
			return false;
		}
		
		$received_message = WC_Pay360::get_post( 'strMessage' );
		$failure_message  = '';
		if ( '' != $received_message ) {
			$failure_message = sprintf( __( 'Bank fail message: %s', WC_Pay360::TEXT_DOMAIN ), $received_message );
		}
		$message = sprintf( __( 'Pay360 payment failed. %s', WC_Pay360::TEXT_DOMAIN ), $failure_message );
		
		//Change status Failed
		$this->order_status_failed( $order, $message );
		
		//Debug log
		WC_Pay360_Debug::add_debug_log( $message );
		
		do_action( 'pay360_ima_failed_transaction', $order, $status, $failure_message );
		
		return true;
	}
	
	/**
	 * Validate Pay360 IMA Response
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function validate_response( WC_Order $order, $response = null ) {
		// Validate security
		$this->validate_security_hash( $order, $this->hash_params( $_POST ), WC_Pay360::get_post( 'PT_hash' ) );
		
		// Check installation ID
		$this->validate_installation_id( $order, WC_Pay360::get_post( 'intInstID' ) );
		
		// Check amounts
		$this->validate_transaction_amount( $order, str_replace( ',', '', WC_Pay360::get_post( 'fltAmount' ) ) );
		
		// Check currency
		$this->validate_store_currency( $order, WC_Pay360::get_post( 'strCurrency' ) );
		
		// Check invoice
		$this->validate_order_invoice( $order, WC_Pay360::get_post( 'PT_invoice' ) );
		
		// Check if the transaction is the same test status as your store
		$this->validate_gateway_mode( $order, WC_Pay360::get_post( 'intTestMode' ), $this->get_gateway()->testmode );
		
		return true;
	}
	
	/**
	 * Check that the installation ID the amount was paid to,
	 * matches the installation ID of the gateway in store.
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $installation_id
	 *
	 * @throws \Exception
	 */
	private function validate_installation_id( $order, $installation_id ) {
		if ( $installation_id != $this->get_gateway()->intInstID ) {
			$message = sprintf( __( 'The installation ID paid to does not match the one set in your store. Installation ID paid to: %s, Store installation ID: %s.', WC_Pay360::TEXT_DOMAIN ), $installation_id, $this->get_gateway()->intInstID );
			
			$this->order_status_on_hold( $order, $message );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			$this->throw_response_exception( $order, $message );
		}
	}
	
	/**
	 * Get the order for the transaction, from the Pay360 response
	 *
	 * @since 2.0
	 *
	 * @return WC_Order
	 */
	public function get_order_from_response() {
		$cart_id = WC_Pay360::get_post( 'strCartID' );
		
		// Extract order number
		$split    = explode( ':', $cart_id );
		$order_id = $split[0];
		
		$order = wc_get_order( (int) $order_id );
		
		if ( ! $order ) {
			wp_die( 'Pay360 Response Failure. Missing order id.', 'Pay360 IMA Response', array( 'response' => 200 ) );
		}
		
		return $order;
	}
	
	/**
	 * HOSTED+IMA PAYMENT
	 * Hash the main request parameters
	 *
	 * @since 2.0
	 *
	 * @param $params
	 *
	 * @return string
	 */
	public function hash_params( $params ) {
		$hash_string = WC_Pay360::get_field( 'intInstID', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'strCartID', $params, '' ) . ':' .
		               str_replace( ',', '', WC_Pay360::get_field( 'fltAmount', $params, '' ) ) . ':' .
		               WC_Pay360::get_field( 'strCurrency', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'PT_invoice', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'intTestMode', $params, '0' );
		
		return md5( $hash_string );
	}
}