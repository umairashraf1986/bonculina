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
class WC_Pay360_Hosted_Cashier_Request extends WC_Pay360_Abstract_Request {
	
	/**
	 * WC_Pay360_Hosted_Request constructor.
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}
	
	/**
	 * Generated the Hosted form parameters
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function get_payment_parameters( WC_Order $order ) {
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Integration method: Hosted Cashier. Generating parameters...' );
		
		$desc               = $this->get_detailed_description( $order );
		$suffix             = $this->get_attempts_suffix( $order );
		$merchant_reference = WC_Pay360_Compat::get_prop( $order, 'id' ) . ':' . WC_Pay360::get_order_number( $order ) . ':' . WC_Pay360_Compat::get_prop( $order, 'order_key' ) . ':' . $suffix;
		
		$cancel     = $order->get_cancel_order_url_raw();
		$return_url = add_query_arg( 'merchantRef', $merchant_reference, $this->get_return_url() );
		if ( $this->use_iframe() ) {
			$cancel = add_query_arg( 'pay360-hosted-cashier-return-cancel', WC_Pay360_Compat::get_order_id( $order ), $cancel );
		}
		$args = array(
			'transaction' => array(
				// For reference we will place the "[order_id]:[order_number]:[order_key]"
				'merchantReference' => $merchant_reference,
				
				'money' => array(
					'currency' => WC_Pay360_Compat::get_order_currency( $order ),
					'amount'   => array(
						'fixed' => number_format( $order->get_total(), 2, '.', '' ),
					),
				
				),
				
				'description' => $desc,
				'deferred'    => $this->is_deferred(),
				'do3DSecure'  => $this->maybe_do_3d_secure(),
			),
			
			'customer' => array(
				'details' => array(
					'name'         => WC_Pay360_Compat::get_order_billing_first_name( $order ) . ' ' . WC_Pay360_Compat::get_order_billing_last_name( $order ),
					'address'      => array(
						'line1'       => WC_Pay360_Compat::get_order_billing_address_1( $order ),
						'line2'       => WC_Pay360_Compat::get_order_billing_address_2( $order ),
						'city'        => WC_Pay360_Compat::get_order_billing_city( $order ),
						'region'      => $this->convert_state_code_to_name( WC_Pay360_Compat::get_order_billing_state( $order ), WC_Pay360_Compat::get_order_billing_country( $order ) ),
						'postcode'    => WC_Pay360_Compat::get_order_billing_postcode( $order ),
						'countryCode' => $this->convert_country_code_to_alpha_3( WC_Pay360_Compat::get_order_billing_country( $order ) ),
					),
					'telephone'    => WC_Pay360_Compat::get_order_billing_phone( $order ),
					'emailAddress' => WC_Pay360_Compat::get_order_billing_email( $order ),
					'ipAddress'    => $this->get_customer_ip(),
				),
			),
			
			'session' => array(
				'returnUrl' => array(
					'url' => $return_url,
				),
				
				'cancelUrl' => array(
					'url' => $cancel,
				),
				
				'skin' => $this->get_skin_id(),
			),
		);
		
		$args['customer']['registered'] = 0 < $order->get_user_id();
		if ( 0 < $order->get_user_id() ) {
			$args['customer']['identity']['merchantCustomerId'] = $this->get_merchant_customer_id( $order->get_user_id() );
		}
		
		// Allow for parameters modification
		$args = apply_filters( 'pay360_hosted_cashier_get_parameters', $args, $order, $this->get_gateway() );
		
		WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Request Parameters: ' . print_r( $args, true ) );
		
		return $args;
	}
	
	/**
	 * @param WC_Order $order
	 *
	 * @return array|mixed
	 */
	public function get_verification_parameters( WC_Order $order ) {
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Integration method: Hosted Cashier. Generating parameters...' );
		
		$desc               = $this->get_detailed_description( $order );
		$suffix             = $this->get_attempts_suffix( $order );
		$merchant_reference = WC_Pay360_Compat::get_prop( $order, 'id' ) . ':' . WC_Pay360::get_order_number( $order ) . ':' . WC_Pay360_Compat::get_prop( $order, 'order_key' ) . ':' . $suffix;
		
		$cancel                   = $order->get_cancel_order_url_raw();
		$is_change_payment_method = WC_Pay360::get_field( 'pay360_cpm', $_GET, false ) ? true : false;
		$return_url               = add_query_arg( array(
			'merchantRef' => $merchant_reference,
			'verify'      => true
		), $this->get_return_url() );
		if ( $is_change_payment_method ) {
			$return_url = add_query_arg( 'pay360_change_payment_method', $is_change_payment_method, $return_url );
		}
		
		if ( $this->use_iframe() ) {
			$cancel = add_query_arg( 'pay360-hosted-cashier-return-cancel', WC_Pay360_Compat::get_order_id( $order ), $cancel );
		}
		
		$args = array(
			'transaction' => array(
				// For reference we will place the "[order_id]:[order_number]:[order_key]"
				'merchantReference' => $merchant_reference,
				'money'             => array(
					'currency' => WC_Pay360_Compat::get_order_currency( $order ),
				),
				'channel'           => 'WEB',
				'description'       => $desc,
				'deferred'          => true,
				'do3DSecure'        => $this->maybe_do_3d_secure(),
			),
			
			'customer' => array(
				'details' => array(
					'name'         => WC_Pay360_Compat::get_order_billing_first_name( $order ) . ' ' . WC_Pay360_Compat::get_order_billing_last_name( $order ),
					'address'      => array(
						'line1'       => WC_Pay360_Compat::get_order_billing_address_1( $order ),
						'line2'       => WC_Pay360_Compat::get_order_billing_address_2( $order ),
						'city'        => WC_Pay360_Compat::get_order_billing_city( $order ),
						'region'      => $this->convert_state_code_to_name( WC_Pay360_Compat::get_order_billing_state( $order ), WC_Pay360_Compat::get_order_billing_country( $order ) ),
						'postcode'    => WC_Pay360_Compat::get_order_billing_postcode( $order ),
						'countryCode' => $this->convert_country_code_to_alpha_3( WC_Pay360_Compat::get_order_billing_country( $order ) ),
					),
					'telephone'    => WC_Pay360_Compat::get_order_billing_phone( $order ),
					'emailAddress' => WC_Pay360_Compat::get_order_billing_email( $order ),
					'ipAddress'    => $this->get_customer_ip(),
				),
			),
			
			'session'  => array(
				'returnUrl' => array(
					'url' => $return_url,
				),
				
				'cancelUrl' => array(
					'url' => $cancel,
				),
				
				'skin' => $this->get_skin_id(),
			),
			'features' => array(
				'paymentMethodRegistration' => 'always',
			),
		);
		
		$args['customer']['registered'] = 0 < $order->get_user_id();
		if ( 0 < $order->get_user_id() ) {
			$args['customer']['identity']['merchantCustomerId'] = $this->get_merchant_customer_id( $order->get_user_id() );
		}
		
		$args['verification'] = array(
			'acquirerPaymentMethod' => true,
		);
		
		// Allow for parameters modification
		$args = apply_filters( 'pay360_hosted_cashier_get_parameters', $args, $order, $this->get_gateway() );
		
		WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Request Parameters: ' . print_r( $args, true ) );
		
		return $args;
	}
	
	/**
	 * Return detailed description of the order.
	 * Returns two types of description
	 * 1. To be used for the 3D secure
	 * 2. To be used for the order details parameter
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return string
	 */
	protected function get_detailed_description( WC_Order $order ) {
		$desc = '';
		if ( 0 < count( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( WC_Pay360_Compat::get_item_quantity( $item ) ) {
					$item_name = $this->get_order_item_name( $item );
					$desc      .= WC_Pay360_Compat::get_item_quantity( $item ) . ' x ' . $item_name . ', ';
				}
			}
			
			// Remove the last delimiters
			$desc = substr( $desc, 0, - 2 );
		}
		
		// Description limit is 255 chars
		$desc = wc_trim_string( $desc, 255 );
		
		return $desc;
	}
	
	/**
	 * Loads the API class and run payment url request
	 *
	 * @since 2.0
	 *
	 * @param $parameters
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get_payment_url( $parameters ) {
		// 1. Init the API class for the hosted cashier
		$api = $this->get_api_client();
		
		// 2. Send the request for the payment url
		$payment_url = $api->get_hosted_cashier_service()->get_payment_url( $parameters );
		
		WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Payment URL: ' . print_r( $payment_url, true ) );
		
		// 3. Return the payment url
		return $payment_url;
	}
	
	/**
	 * Loads the API class and run verification url request
	 *
	 * @since 2.0
	 *
	 * @param $parameters
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get_verification_url( $parameters ) {
		// 1. Init the API class for the hosted cashier
		$api = $this->get_api_client();
		
		// 2. Send the request for the payment url
		$payment_url = $api->get_hosted_cashier_service()->get_verification_url( $parameters );
		
		WC_Pay360_Debug::add_debug_log( 'Hosted Cashier: Verify URL: ' . print_r( $payment_url, true ) );
		
		// 3. Return the payment url
		return $payment_url;
	}
	
	/**
	 * Return whether the transaction should be deferred or not.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function is_deferred() {
		if ( 'capture' == $this->get_gateway()->get_option( 'cashier_transaction_type' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns whether the transaction should use 3D Secure or not
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function maybe_do_3d_secure() {
		if ( 'no' == $this->get_gateway()->get_option( 'cashier_use_3ds' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns skin ID set by the customer
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_skin_id() {
		$skin = $this->get_gateway()->get_option( 'hosted_cashier_skin_default' );
		if ( 'other' == $skin ) {
			$skin = $this->get_gateway()->get_option( 'hosted_cashier_skin' );
		}
		
		return $skin;
	}
}