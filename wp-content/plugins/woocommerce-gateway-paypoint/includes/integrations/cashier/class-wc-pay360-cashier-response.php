<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle responses for Pay360 Hosted Cashier integration
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Cashier_Response extends WC_Pay360_Abstract_Response {
	
	protected $transaction;
	
	/**
	 * Initialize the Response Hosted class
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}
	
	public function hooks() {
		// Process IPNs
		add_action( 'woocommerce_api_wc_pay360_gateway_cashier_integration', array( $this, 'route_response' ) );
	}
	
	/**
	 * Catch the Pay360 Response
	 *
	 * @since 2.1
	 **/
	public function route_response() {
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Received Response GET: ' . print_r( $_GET, true ) );
	}
	
	/**
	 * Get the order from the merchant reference
	 *
	 * @since 2.0
	 *
	 * @param $reference
	 *
	 * @throws \WcPay360\Api\Exceptions\Exception
	 * @return bool|WC_Order
	 */
	public function get_order_from_merchant_reference( $reference ) {
		list( $order_id, $order_number, $order_key, $suffix ) = explode( ':', $reference );
		$order = wc_get_order( (int) $order_id );
		
		if ( ! $order ) {
			throw new \WcPay360\Api\Exceptions\Exception( __( 'Pay360 Response Failure. Invalid order id.', WC_Pay360::TEXT_DOMAIN ), 'Pay360 Response', array( 'response' => 200 ) );
		}
		
		return $order;
	}
	
	/**
	 * Returns the merchant reference number from the provided transaction response
	 *
	 * @since 2.0
	 *
	 * @param object $response
	 *
	 * @return string
	 */
	public function get_merchant_reference_from_response( $response ) {
		if ( ! isset( $response->transaction ) || ! isset( $response->transaction->merchantRef ) ) {
			return '';
		}
		
		return $response->transaction->merchantRef;
	}
	
	/**
	 * Process a successfully validated response
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param object   $response
	 * @param bool     $save_card_data
	 *
	 * @throws Exception
	 */
	public function process_response( WC_Order $order, $response, $save_card_data = true ) {
		//Debug log
		WC_Pay360_Debug::add_debug_log( __( 'Process Cashier API response.', WC_Pay360::TEXT_DOMAIN ) );
		
		if ( 'SUCCESS' == $response->transaction->status ) {
			
			// Check currency
			$this->validate_store_currency( $order, $response->transaction->currency );
			
			if ( $this->is_deferred() ) {
				// The money were not taken, so we don't want to Process the order just, yet.
				$order->update_status( apply_filters( 'wc_pay360_deferred_payment_order_status', 'on-hold', $order, $this ), __( 'Transaction was successfully Authorized.', WC_Pay360::TEXT_DOMAIN ) );
				
				// Add transaction details
				$this->add_transaction_details_to_order_data( $order, $response, false );
				
				// Remove cart
				WC_Pay360_Compat::empty_cart();
				
				do_action( 'pay360_cashier_successful_deferred_transaction', $order, $response->transaction );
			} else {
				
				// Add transaction details
				$this->add_transaction_details_to_order_data( $order, $response, true );
				
				$this->complete_order( $order, $response->transaction->transactionId );
				
				do_action( 'pay360_cashier_successful_transaction', $order, $response->transaction );
			}
			
			if ( WC_Pay360::is_subscriptions_active() && $save_card_data ) {
				$this->save_meta_data_to_subscription( $order, $response );
			}
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( __( 'Cashier payment completed.', WC_Pay360::TEXT_DOMAIN ) );
		} else {
			
			$fail_message = isset( $response->processing->authResponse->message ) ? $response->processing->authResponse->message : '';
			$resp_code    = isset( $response->processing->authResponse->statusCode ) ? $response->processing->authResponse->statusCode : '';
			$message      = sprintf( __( 'Pay360 payment failed.', WC_Pay360::TEXT_DOMAIN ) );
			
			if ( '' != $resp_code ) {
				$message .= sprintf( __( ' Bank fail code: "%s"', WC_Pay360::TEXT_DOMAIN ), $resp_code );
			}
			
			if ( '' != $fail_message ) {
				$message .= sprintf( __( ' Bank fail message: "%s"', WC_Pay360::TEXT_DOMAIN ), $fail_message );
			}
			
			//Change status Failed
			$this->order_status_failed( $order, $message );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			
			do_action( 'pay360_cashier_failed_transaction', $order, $fail_message, $resp_code );
		}
		
		if ( $save_card_data ) {
			$this->save_pay360_customer_information_to_user( $response, $order->get_user_id() );
			$this->save_pay360_customer_information_to_order( $response, $order );
		}
	}
	
	/**
	 * Validate Pay360 Response
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param object   $response
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function validate_response( WC_Order $order, $response = null ) {
		$merchant_reference = $this->get_merchant_reference_from_response( $response );
		
		list( $order_id, $order_number, $order_key, $suffix ) = explode( ':', $merchant_reference );
		
		// Order should not be paid for
		$this->validate_order_not_paid( $order );
		
		// Check invoice
		$this->validate_order_invoice( $order, $order_key );
		
		return true;
	}
	
	/**
	 * Mark order as completed
	 *
	 * @since 2.1
	 *
	 * @param WC_Order $order
	 * @param string   $transaction_id
	 */
	public function complete_order( $order, $transaction_id = '' ) {
		$transaction = '';
		if ( ! empty( $transaction_id ) ) {
			$transaction = sprintf( __( 'Transaction ID: %s', WC_Pay360::TEXT_DOMAIN ), $transaction_id );
		}
		
		$order->add_order_note( sprintf( __( 'Pay360 payment completed. %s', WC_Pay360::TEXT_DOMAIN ), $transaction ) );
		
		$order->payment_complete( $transaction_id );
	}
}