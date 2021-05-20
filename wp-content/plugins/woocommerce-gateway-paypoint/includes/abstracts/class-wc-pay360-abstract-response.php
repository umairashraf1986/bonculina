<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Response class to serve as a basis for the payment responses.
 *
 * @since
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WC_Pay360_Abstract_Response extends WC_Pay360_Abstract_Integration {
	
	/**
	 * Init the class
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
		
		// Legacy hook. For back compat.
		add_action( 'woocommerce_api_wc_gateway_paypoint', array( $this, 'route_response' ) );
	}
	
	/**
	 * Get the order for the transaction, from the Pay360 response
	 *
	 * @since 2.0
	 *
	 * @return WC_Order
	 */
	public function get_order_from_response() {
		$ref_number = WC_Pay360::get_field( 'merchantRef', $_POST, '0:0:0' );
		list( $order_id, $order_number, $order_key ) = explode( ':', $ref_number );
		$order = wc_get_order( (int) $order_id );
		
		if ( ! $order ) {
			wp_die( 'Pay360 Response Failure. Invalid order id.', 'Pay360 Response', array( 'response' => 200 ) );
		}
		
		return $order;
	}
	
	/**
	 * Mark order as completed
	 *
	 * @since 2.0
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
		
		// Remove cart
		WC_Pay360_Compat::empty_cart();
	}
	
	/**
	 * Mark order with On-Hold status
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $reason
	 */
	public function order_status_on_hold( $order, $reason = '' ) {
		$order->update_status( 'on-hold', $reason );
	}
	
	/**
	 * Mark order with Failed status
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $reason
	 */
	public function order_status_failed( $order, $reason = '' ) {
		$order->update_status( 'failed', $reason );
	}
	
	/**
	 * Validate response.
	 *
	 * @since 2.0
	 *
	 * @param WC_Order    $order    The order
	 * @param object|null $response The transaction response
	 *
	 * @return bool
	 */
	public function validate_response( WC_Order $order, $response = null ) {
		return true;
	}
	
	/**
	 * Validate security hash
	 *
	 * @since 2.0
	 *
	 * @param $order
	 * @param $hash
	 * @param $received_hash
	 *
	 * @throws Exception
	 */
	protected function validate_security_hash( $order, $hash, $received_hash ) {
		if ( $hash != $received_hash ) {
			$message = sprintf(
				__(
					'The hash security check did not pass. The hashes generated are different. '
					. 'Hash submitted: %s, hash received: %s.', WC_Pay360::TEXT_DOMAIN
				), $received_hash, $hash
			);
			
			$this->order_status_failed( $order, $message );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			$this->throw_response_exception( $order, $message );
		}
	}
	
	/**
	 * Check amount of transaction and amount of order
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param float    $amount
	 *
	 * @throws Exception
	 */
	protected function validate_transaction_amount( $order, $amount ) {
		if ( wc_format_decimal( $amount, 2 ) != wc_format_decimal( $order->get_total(), 2 ) ) {
			$message = sprintf( __( 'The amount paid (%s) does not match the order total (%s).', WC_Pay360::TEXT_DOMAIN ), $amount, $order->get_total() );
			$this->order_status_on_hold( $order, $message );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			$this->throw_response_exception( $order, $message );
		}
	}
	
	/**
	 * Check currency charged and currency ordered in.
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param          $currency
	 *
	 * @throws Exception
	 */
	protected function validate_store_currency( $order, $currency ) {
		$store_currency = WC_Pay360_Compat::get_order_currency( $order );
		if ( ! empty( $currency ) ) {
			if ( $currency != $store_currency ) {
				$message = sprintf( __( 'The currency paid in (%s) does not match your store currency (%s).', WC_Pay360::TEXT_DOMAIN ), $currency, $store_currency );
				$this->order_status_on_hold( $order, $message );
				
				//Debug log
				WC_Pay360_Debug::add_debug_log( $message );
				$this->throw_response_exception( $order, $message );
			}
		}
	}
	
	/**
	 * Check order key
	 *
	 * @since 2.0
	 *
	 * @param $order
	 * @param $invoice
	 *
	 * @throws Exception
	 */
	protected function validate_order_invoice( $order, $invoice ) {
		if ( $invoice != WC_Pay360_Compat::get_prop( $order, 'order_key' ) ) {
			$message = sprintf( __( 'The order invoice number does not match. Invoice received: %s, Order invoice: %s', WC_Pay360::TEXT_DOMAIN ), $invoice, WC_Pay360_Compat::get_prop( $order, 'order_key' ) );
			
			$this->order_status_failed( $order, $message );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			
			$this->throw_response_exception( $order, $message );
		}
	}
	
	/**
	 * Validate that the order is not already paid for
	 *
	 * @since 2.0
	 *
	 * @param $order
	 *
	 * @throws \WcPay360\Api\Exceptions\Response_Exception
	 */
	protected function validate_order_not_paid( $order ) {
		if ( in_array( WC_Pay360_Compat::get_prop( $order, 'status' ), WC_Pay360_Compat::get_is_paid_statuses() ) ) {
			$message = __( 'The order is already paid for.', WC_Pay360::TEXT_DOMAIN );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( $message );
			
			$this->throw_response_exception( $order, $message );
		}
	}
	
	/**
	 * End script execution by exiting or redirecting to the return URL (Hosted)
	 *
	 * @since 2.0
	 *
	 * @param WC_Order|bool $order
	 */
	protected function end_execution( $order = false ) {
		if ( 'hosted' == $this->get_gateway()->get_option( 'integration' ) ) {
			$redirect_url = apply_filters( 'wc_pay360_response_return_url', $this->get_gateway()->get_return_url( $order ), $this->get_gateway(), $order );
			
			//Debug log
			WC_Pay360_Debug::add_debug_log( 'Redirecting to: ' . $redirect_url );
			echo '<meta http-equiv="refresh" content="0;url=' . $redirect_url . '" />';
			exit;
		}
		
		exit;
	}
	
	/**
	 * Adds the transaction data to the order
	 *
	 * It will add the transaction_id again, even when this is added in the order complete process
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param object   $response
	 * @param bool     $is_captured
	 *
	 * @throws Exception
	 */
	public function add_transaction_details_to_order_data( $order, $response, $is_captured = true ) {
		// Add information about the order to the order meta
		$pay360_order = new \WcPay360\Pay360_Order( $order );
		$pay360_order->save_is_payment_captured( $is_captured );
		$pay360_order->save_order_amount_authorized( $order->get_total() );
		
		if ( $is_captured ) {
			$pay360_order->save_order_amount_captured( $order->get_total() );
		} else {
			$pay360_order->save_order_amount_captured( 0 );
		}
		
		$pay360_order->save_transaction_merchant_reference_id( $response->transaction->merchantRef );
		$pay360_order->save_transaction_id( $response->transaction->transactionId );
	}
	
	/**
	 * Add data from the response to the user meta
	 *
	 * @since 2.1
	 *
	 * @param $response
	 * @param $user_id
	 */
	public function save_pay360_customer_information_to_user( $response, $user_id ) {
		$customer_id = isset( $response->customer ) && isset( $response->customer->id ) ? $response->customer->id : '';
		if ( $customer_id ) {
			update_user_meta( $user_id, 'wc_pay360_customer_profile_id', wc_clean( $customer_id ) );
		}
	}
	
	/**
	 * Add data from the response to the order meta
	 *
	 * @since 2.1
	 *
	 * @param          $response
	 * @param WC_Order $order
	 */
	public function save_pay360_customer_information_to_order( $response, $order ) {
		$card  = array();
		$token = '';
		if ( isset( $response->paymentMethod->registered ) ) {
			if ( isset( $response->paymentMethod->card ) ) {
				$token = wc_clean( isset( $response->paymentMethod->card->cardToken ) ? $response->paymentMethod->card->cardToken : '' );
				$card  = array(
					'expiry'      => $response->paymentMethod->card->expiryDate,
					'usage_type'  => $response->paymentMethod->card->cardUsageType,
					'card_scheme' => $response->paymentMethod->card->cardScheme,
					'type'        => $response->paymentMethod->card->cardType,
					'last4'       => substr( $response->paymentMethod->card->maskedPan, - 4 ),
					'nickname'    => isset( $response->paymentMethod->card->cardNickname ) ? $response->paymentMethod->card->cardNickname : '',
				);
			}
		}
		
		$customer_id = isset( $response->customer ) && isset( $response->customer->id ) ? $response->customer->id : '';
		
		$pay360_order = new \WcPay360\Pay360_Order( $order );
		$pay360_order->save_customer_profile_id( $customer_id );
		$pay360_order->save_customer_payment_token( $token );
		if ( ! empty( $card ) ) {
			$pay360_order->save_customer_payment_card( $card );
		}
	}
	
	/**
	 * Save the transaction details to the Subscription
	 *
	 * @since 2.1
	 *
	 * @param $order
	 * @param $response
	 */
	public function save_meta_data_to_subscription( $order, $response ) {
		// Also store it on the subscriptions being purchased or paid for in the order
		if ( wcs_order_contains_subscription( $order ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $order );
		} elseif ( wcs_order_contains_renewal( $order ) ) {
			$subscriptions = wcs_get_subscriptions_for_renewal_order( $order );
		} elseif ( wcs_is_subscription( $order ) ) {
			$subscriptions = array( $order );
		} else {
			$subscriptions = array();
		}
		
		/**
		 * @var WC_Subscription $subscription
		 */
		foreach ( $subscriptions as $subscription ) {
			
			// Debug log
			WC_Pay360_Debug::add_debug_log( 'Saving details to subscription: ' . print_r( WC_Pay360_Compat::get_order_id( $subscription ), true ) );
			
			$this->save_pay360_customer_information_to_user( $response, $subscription->get_user_id() );
			$this->save_pay360_customer_information_to_order( $response, $subscription );
		}
	}
	
	/**
	 * Sets up an throws a Response_Exception.
	 *
	 * @since 2.1
	 *
	 * @param WC_Order|bool $order
	 * @param string        $message
	 *
	 * @throws \WcPay360\Api\Exceptions\Response_Exception
	 */
	public function throw_response_exception( $order = false, $message = '' ) {
		$exception = new \WcPay360\Api\Exceptions\Response_Exception( $message );
		
		if ( $order ) {
			$exception->set_order( $order );
		}
		
		throw $exception;
	}
}