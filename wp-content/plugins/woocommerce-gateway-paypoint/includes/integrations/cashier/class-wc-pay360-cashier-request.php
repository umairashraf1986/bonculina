<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hosted integration request class. Generate and submit the Hosted form
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Cashier_Request extends WC_Pay360_Abstract_Request {
	
	/**
	 * WC_Pay360_Hosted_Request constructor.
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}
	
	/**
	 * @param            $order
	 * @param float|null $amount
	 * @param string     $token
	 * @param string     $customer_id
	 *
	 * @throws Exception
	 * @return object
	 */
	public function merchant_token_payment( $order, $amount = null, $token = '', $customer_id = '' ) {
		if ( ! $order instanceof \WC_Order && is_numeric( $order ) ) {
			$order = wc_get_order( (int) $order );
		}
		
		if ( ! $order ) {
			throw new \WcPay360\Api\Exceptions\Exception( __( 'Order was not provided', WC_Pay360::TEXT_DOMAIN ) );
		}
		
		WC_Pay360_Debug::add_debug_log( 'Start Merchant Token Payment' );
		
		$pay360_order = new \WcPay360\Pay360_Order( $order );
		
		// Amount is the order total, if not given any different
		if ( '' === $token ) {
			$token = $pay360_order->get_customer_payment_token();
		}
		
		// No Token, no payment
		if ( empty( $token ) ) {
			throw new \WcPay360\Api\Exceptions\Exception( __( 'Token is not set or empty.', WC_Pay360::TEXT_DOMAIN ) );
		}
		
		if ( '' === $customer_id ) {
			$customer_id = $pay360_order->get_customer_profile_id();
		}
		
		// Customer ID is required
		if ( empty( $customer_id ) ) {
			throw new \WcPay360\Api\Exceptions\Exception( __( 'Customer id is not set or empty.', WC_Pay360::TEXT_DOMAIN ) );
		}
		
		// Amount is the order total, if not given any different
		if ( null === $amount ) {
			$amount = $order->get_total();
		}
		
		$desc               = sprintf( __( '[%s] Payment for order %s', WC_Pay360::TEXT_DOMAIN ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $order->get_order_number() );
		$suffix             = $this->get_attempts_suffix( $order );
		$merchant_reference = WC_Pay360_Compat::get_prop( $order, 'id' ) . ':' . WC_Pay360::get_order_number( $order ) . ':' . WC_Pay360_Compat::get_prop( $order, 'order_key' ) . ':' . $suffix;
		
		// We need the customer reference that hol
		$customer_merchant_reference = $this->get_merchant_customer_id( $order->get_user_id() );
		
		$arguments = array(
			"transaction"   => array(
				'currency'     => $this->get_currency( $order ),
				'amount'       => number_format( $amount, 2, '.', '' ),
				'description'  => $desc,
				'merchantRef'  => $merchant_reference,
				'commerceType' => 'ECOM',
				'channel'      => 'WEB'
			),
			'paymentMethod' => array(
				'cardToken'      => array(
					'token' => $token,
					'cv2'   => 123, // Note to be placed here
				),
				'billingAddress' => array(
					'line1'       => WC_Pay360_Compat::get_order_billing_address_1( $order ),
					'line2'       => WC_Pay360_Compat::get_order_billing_address_2( $order ),
					'city'        => WC_Pay360_Compat::get_order_billing_city( $order ),
					'region'      => $this->convert_state_code_to_name( WC_Pay360_Compat::get_order_billing_state( $order ), WC_Pay360_Compat::get_order_billing_country( $order ) ),
					'postcode'    => WC_Pay360_Compat::get_order_billing_postcode( $order ),
					'countryCode' => $this->convert_country_code_to_alpha_3( WC_Pay360_Compat::get_order_billing_country( $order ) ),
				)
			),
			'customer'      => array(
				'id'          => $customer_id,
				'merchantRef' => $customer_merchant_reference
			),
		);
		
		$api = $this->get_api_client();
		
		WC_Pay360_Debug::add_debug_log( 'Merchant Token Payment Request: ' . print_r( $arguments, true ) );
		
		// TODO: add this to a Response class
		$result = $api->get_cashier_service()->process_payment( $arguments );
		
		WC_Pay360_Debug::add_debug_log( 'Merchant Token Payment Response: ' . print_r( $result, true ) );
		
		return $result;
	}
	
	/**
	 * Captures a deferred payment
	 *
	 * @since 2.0
	 *
	 * @param $order
	 *
	 * @throws Exception
	 * @throws \WcPay360\Api\Exceptions\Invalid_Argument
	 * @return object
	 */
	public function capture_payment( $order ) {
		if ( ! $order instanceof \WC_Order && is_numeric( $order ) ) {
			$order = wc_get_order( (int) $order );
		}
		
		// No capture if the order is not provided
		if ( ! $order ) {
			throw new \WcPay360\Api\Exceptions\Exception( 'capture_payment: Order was not provided' );
		}
		
		WC_Pay360_Debug::add_debug_log( 'Capture Payment for order#: ' . WC_Pay360_Compat::get_prop( $order, 'id' ) );
		$pay360_order   = new \WcPay360\Pay360_Order( $order );
		$api            = $this->get_api_client();
		$ref_id         = $pay360_order->get_transaction_merchant_reference_id();
		$transaction_id = $order->get_transaction_id();
		
		$args = array(
			'transaction' => array(
				'merchantRef' => $ref_id
			),
		);
		
		WC_Pay360_Debug::add_debug_log( 'Capture Request: ' . print_r( $args, true ) );
		
		$capture = $api->get_cashier_service()->process_capture( $transaction_id, $args );
		
		WC_Pay360_Debug::add_debug_log( 'Capture Response: ' . print_r( $capture, true ) );
		
		return $capture;
	}
	
	/**
	 * Refunds a payment
	 *
	 * @since 2.0
	 *
	 * @param $order
	 * @param $amount
	 *
	 * @throws Exception
	 * @throws \WcPay360\Api\Exceptions\Invalid_Argument
	 * @return object
	 */
	public function refund_payment( $order, $amount ) {
		if ( ! $order instanceof \WC_Order ) {
			$order = wc_get_order( (int) $order );
		}
		
		// No refund if the order is not provided
		if ( ! $order ) {
			throw new \WcPay360\Api\Exceptions\Exception( 'refund_payment: Order was not provided' );
		}
		
		// Debug log
		WC_Pay360_Debug::add_debug_log( 'Refunding order#: ' . WC_Pay360_Compat::get_prop( $order, 'id' ) );
		
		$pay360_order = new \WcPay360\Pay360_Order( $order );
		$api          = $this->get_api_client();
		$ref_id       = $pay360_order->get_transaction_merchant_reference_id();
		
		$args = array(
			'transaction' => array(
				'merchantRef' => $ref_id,
				'amount'      => $amount,
				'currency'    => WC_Pay360_Compat::get_order_currency( $order ),
			),
		);
		
		// Debug log
		WC_Pay360_Debug::add_debug_log( 'Refund request: ' . print_r( $args, true ) );
		
		$refund = $api->get_cashier_service()->process_refund( $order->get_transaction_id(), $args );
		
		// Debug log
		WC_Pay360_Debug::add_debug_log( 'Refund request: ' . print_r( $refund, true ) );
		
		return $refund;
	}
}