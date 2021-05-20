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
class WC_Pay360_Hosted_Cashier_Response extends WC_Pay360_Abstract_Response {
	
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
		add_action( 'woocommerce_api_' . strtolower( WC_Pay360::get_gateway_class() ), array(
			$this,
			'route_response'
		) );
	}
	
	/**
	 * Catch the Pay360 Response
	 *
	 * @since 2.0
	 **/
	public function route_response() {
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Received Response GET: ' . print_r( $_GET, true ) );
		
		try {
			// 1. Get transaction by 'merchantRef' id
			$ref_number = WC_Pay360::get_field( 'merchantRef', $_GET, '0:0:0' );
			$api        = $this->get_api_client();
			$response   = $api->get_cashier_service()->process_transaction_lookup( $ref_number, 'merchantRef' );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Transaction lookup response: ' . print_r( $response, true ) );
			
			if ( is_array( $response ) ) {
				$response = array_pop( $response );
			}
			
			// 2. Take the order from the transaction response.
			$order = $this->get_order_from_merchant_reference( $this->get_merchant_reference_from_response( $response ) );
			
			if ( ! $this->validate_response( $order, $response ) ) {
				$this->end_execution( $order );
			}
			
			$is_change_payment_method = WC_Pay360::get_field( 'pay360_change_payment_method', $_GET, false );
			$is_verification_only     = WC_Pay360::get_field( 'verify', $_GET, false );
			
			if ( WC_Pay360::is_subscriptions_active() && wcs_is_subscription( $order ) && $is_change_payment_method ) {
				// Process only the method change, no order manipulation
				$this->process_payment_method_change_response( $order, $response );
			} elseif ( $is_verification_only ) {
				// Does not include the same checks as normal response
				$this->process_verification_response( $order, $response );
			} else {
				$this->process_response( $order, $response );
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
	 * @param WC_Order $order
	 * @param object   $response
	 */
	public function process_verification_response( $order, $response ) {
		if ( 'SUCCESS' == $response->transaction->status ) {
			// Add transaction details
			$this->add_transaction_details_to_order_data( $order, $response, true );
			
			$pay360_order = new \WcPay360\Pay360_Order( $order );
			$pay360_order->complete_order( $response->transaction->transactionId );
			
			do_action( 'pay360_hosted_cashier_successful_verification', $order, $response->transaction );
			
			if ( WC_Pay360::is_subscriptions_active() ) {
				$this->save_meta_data_to_subscription( $order, $response );
			}
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( __( 'Hosted Cashier verification completed.', WC_Pay360::TEXT_DOMAIN ) );
		} else {
			
			$fail_message = isset( $response->processing->authResponse->message ) ? $response->processing->authResponse->message : '';
			$resp_code    = isset( $response->processing->authResponse->statusCode ) ? $response->processing->authResponse->statusCode : '';
			$message      = sprintf( __( 'Pay360 verification failed.', WC_Pay360::TEXT_DOMAIN ) );
			
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
			
			do_action( 'pay360_hosted_cashier_failed_verification', $order, $fail_message, $resp_code );
		}
		
		$this->save_pay360_customer_information_to_user( $response, $order->get_user_id() );
		$this->save_pay360_customer_information_to_order( $response, $order );
	}
	
	/**
	 * @param WC_Order $order
	 * @param          $response
	 */
	public function process_payment_method_change_response( $order, $response ) {
		if ( 'SUCCESS' == $response->transaction->status ) {
			// Add transaction details
			$this->add_transaction_details_to_order_data( $order, $response, true );
			
			do_action( 'pay360_hosted_cashier_successful_change_payment_method', $order, $response->transaction );
			
			if ( WC_Pay360::is_subscriptions_active() ) {
				$this->save_meta_data_to_subscription( $order, $response );
			}
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( __( 'Hosted Cashier change payment method completed.', WC_Pay360::TEXT_DOMAIN ) );
		} else {
			
			$fail_message = isset( $response->processing->authResponse->message ) ? $response->processing->authResponse->message : '';
			$resp_code    = isset( $response->processing->authResponse->statusCode ) ? $response->processing->authResponse->statusCode : '';
			$message      = sprintf( __( 'Pay360 change payment method failed.', WC_Pay360::TEXT_DOMAIN ) );
			
			if ( '' != $resp_code ) {
				$message .= sprintf( __( ' Bank fail code: "%s"', WC_Pay360::TEXT_DOMAIN ), $resp_code );
			}
			
			if ( '' != $fail_message ) {
				$message .= sprintf( __( ' Bank fail message: "%s"', WC_Pay360::TEXT_DOMAIN ), $fail_message );
			}
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			
			$order->add_order_note( $message );
			
			do_action( 'pay360_hosted_cashier_failed_change_payment_method', $order, $fail_message, $resp_code );
		}
	}
	
	/**
	 * Get the order from the merchant reference
	 *
	 * @since 2.0
	 *
	 * @param $reference
	 *
	 * @return bool|WC_Order
	 */
	public function get_order_from_merchant_reference( $reference ) {
		list( $order_id, $order_number, $order_key, $suffix ) = explode( ':', $reference );
		$order = wc_get_order( (int) $order_id );
		
		if ( ! $order ) {
			wp_die( __( 'Pay360 Response Failure. Invalid order id.', WC_Pay360::TEXT_DOMAIN ), 'Pay360 Response', array( 'response' => 200 ) );
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
	 * @param \WC_Order $order
	 * @param object   $response
	 *
	 * @throws Exception
	 */
	public function process_response( \WC_Order $order, $response ) {
		if ( 'SUCCESS' == $response->transaction->status ) {
			
			// Check amounts
			$this->validate_transaction_amount( $order, $response->transaction->amount );
			
			// Check currency
			$this->validate_store_currency( $order, $response->transaction->currency );
			
			if ( $this->is_deferred() ) {
				// The money were not taken, so we don't want to Process the order just, yet.
				$order->update_status( apply_filters( 'wc_pay360_deferred_payment_order_status', 'on-hold', $order, $this ), __( 'Transaction was successfully Authorized.', WC_Pay360::TEXT_DOMAIN ) );
				
				// Add transaction details
				$this->add_transaction_details_to_order_data( $order, $response, false );
				
				// Remove cart
				WC_Pay360_Compat::empty_cart();
				
				do_action( 'pay360_hosted_cashier_successful_deferred_transaction', $order, $response->transaction );
			} else {
				
				// Add transaction details
				$this->add_transaction_details_to_order_data( $order, $response, true );
				
				$pay360_order = new \WcPay360\Pay360_Order( $order );
				$pay360_order->complete_order( $response->transaction->transactionId );
				
				do_action( 'pay360_hosted_cashier_successful_transaction', $order, $response->transaction );
			}
			
			if ( WC_Pay360::is_subscriptions_active() ) {
				$this->save_meta_data_to_subscription( $order, $response );
			}
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( __( 'Hosted Cashier payment completed.', WC_Pay360::TEXT_DOMAIN ) );
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
			
			do_action( 'pay360_hosted_cashier_failed_transaction', $order, $fail_message, $resp_code );
		}
		
		$this->save_pay360_customer_information_to_user( $response, $order->get_user_id() );
		$this->save_pay360_customer_information_to_order( $response, $order );
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
	
	/**
	 * End script execution by exiting or redirecting to the return URL
	 *
	 * @since 2.0
	 *
	 * @param WC_Order|bool $order
	 */
	protected function end_execution( $order = false ) {
		
		if ( false === $order ) {
			wp_redirect( apply_filters( 'wc_pay360_no_order_response_url', wc_get_cart_url() ), $this->get_gateway() );
			exit;
		}
		
		$redirect_url = apply_filters( 'wc_pay360_response_return_url', $this->get_gateway()->get_return_url( $order ), $this->get_gateway(), $order );
		if ( $this->use_iframe() ) {
			wc_get_template(
				'pay360/iframe-break.php',
				array(
					'redirect_url' => $redirect_url,
				),
				'',
				WC_Pay360::plugin_path() . '/templates/'
			);
			exit;
		}
		
		wp_safe_redirect( $redirect_url );
		exit;
	}
}