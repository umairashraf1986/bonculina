<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Gateway_Addons extends WC_Pay360_Gateway {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	public function hooks() {
		if ( true === self::$loaded ) {
			return;
		}
		
		// Scheduled payment
		add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array(
			$this,
			'scheduled_subscription_payment_request'
		), 10, 2 );
		
		// Meta data renewal remove
		add_action( 'wcs_resubscribe_order_created', array( $this, 'remove_renewal_order_meta' ), 10 );
		
		// Update change payment method
		add_action( 'woocommerce_subscription_failing_payment_method_updated_' . $this->id, array(
			$this,
			'changed_failing_payment_method'
		), 10, 2 );
		
		// Display card used details
		add_filter( 'woocommerce_my_subscriptions_payment_method', array(
			$this,
			'maybe_render_subscription_payment_method'
		), 10, 2 );
		
		// Handle display of the Admin facing payment method change
		add_filter( 'woocommerce_subscription_payment_meta', array(
			$this,
			'add_subscription_payment_meta'
		), 10, 2 );
		
		// Handle validation of the Admin facing payment method change
		add_filter( 'woocommerce_subscription_validate_payment_meta', array(
			$this,
			'validate_subscription_payment_meta'
		), 10, 2 );
		
		// Add support for Pre-Orders
		if ( WC_Pay360::is_pre_orders_active() ) {
			add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array(
				$this,
				'process_pre_order_release_payment'
			) );
		}
		
		parent::hooks();
	}
	
	/**
	 * Don't transfer Pay360 meta to resubscribe orders.
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $resubscribe_order The order created for the customer to resubscribe to the old expired/cancelled subscription
	 *
	 * @return void
	 */
	public function remove_renewal_order_meta( $resubscribe_order ) {
		$pay360_order = new \WcPay360\Pay360_Order( $resubscribe_order );
		$pay360_order->delete_customer_profile_id();
		$pay360_order->delete_customer_payment_token();
	}
	
	/**
	 * Perform a subscription scheduled payment
	 *
	 * @since 2.0
	 *
	 * @param          $amount_to_charge
	 * @param WC_Order $renewal_order
	 */
	public function scheduled_subscription_payment_request( $amount_to_charge, $renewal_order ) {
		try {
			WC_Pay360_Debug::add_debug_log( 'Scheduled payment: ' . print_r( WC_Pay360_Compat::get_order_id( $renewal_order ), true ) );
			
			if ( 'hosted_cashier' != $this->integration ) {
				throw new Exception( __( 'Scheduled payment can only be processed with Hosted Cashier integration.', WC_Pay360::TEXT_DOMAIN ) );
			}
			
			// Load the api class
			$integration = new WC_Pay360_Cashier_Request( $this );
			$response    = $integration->merchant_token_payment( $renewal_order, $amount_to_charge );
			
			$api_response = new WC_Pay360_Cashier_Response( $this );
			
			if ( $api_response->validate_response( $renewal_order, $response ) ) {
				$api_response->process_response( $renewal_order, $response, false );
			}
		}
		catch ( Exception $e ) {
			$renewal_order->update_status( 'failed', $e->getMessage() );
			
			// Debug log
			WC_Pay360_Debug::add_debug_log( $e->getMessage() );
		}
	}
	
	/**
	 * Add the Transaction ID to a changed failing payment method
	 *
	 * @since 2.0
	 *
	 * @param WC_Subscription $subscription
	 * @param WC_Order        $renewal_order
	 */
	public function changed_failing_payment_method( $subscription, $renewal_order ) {
		$pay360_sub     = new \WcPay360\Pay360_Order( $subscription );
		$pay360_renewal = new \WcPay360\Pay360_Order( $renewal_order );
		
		$pay360_sub->save_customer_profile_id( $pay360_renewal->get_customer_profile_id() );
		$pay360_sub->save_customer_payment_token( $pay360_renewal->get_customer_payment_token() );
	}
	
	/**
	 * Display the payment method info to the customer.
	 *
	 * @since 2.0
	 *
	 * @param                 $payment_method_to_display
	 * @param WC_Subscription $subscription
	 *
	 * @return string
	 */
	public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription ) {
		if ( $this->id !== WC_Pay360_Compat::get_order_prop( $subscription, 'payment_method' ) ) {
			return $payment_method_to_display;
		}
		
		$pay360_order = new \WcPay360\Pay360_Order( $subscription );
		$card         = $pay360_order->get_customer_payment_card();
		
		// If could not find any card info on the subscription try the order.
		if ( empty( $card ) ) {
			$pay360_sub_order = new \WcPay360\Pay360_Order( $subscription->order );
			$card             = $pay360_sub_order->get_customer_payment_card();
		}
		
		// If still empty, bail
		if ( empty( $card ) ) {
			return $payment_method_to_display;
		}
		
		if ( isset( $card['card_scheme'] ) && isset( $card['last4'] ) ) {
			$payment_method_to_display = sprintf( __( 'Via %s card ending in %s', WC_Pay360::TEXT_DOMAIN ), $card['card_scheme'], $card['last4'] );
		}
		
		return $payment_method_to_display;
	}
	
	/**
	 * Add payment method change fields
	 *
	 * @since 2.0
	 *
	 * @param $payment_meta
	 * @param $subscription
	 *
	 * @return mixed
	 */
	public function add_subscription_payment_meta( $payment_meta, $subscription ) {
		$pay360_order = new \WcPay360\Pay360_Order( $subscription );
		
		$payment_meta[ $this->id ]['post_meta']['_wc_pay360_customer_profile_id'] = array(
			'value' => $pay360_order->get_customer_profile_id(),
			'label' => 'Pay360 Customer Profile ID',
		);
		
		$payment_meta[ $this->id ]['post_meta']['_wc_pay360_customer_payment_card_token'] = array(
			'value' => $pay360_order->get_customer_payment_token(),
			'label' => 'Pay360 Payment Token',
		);
		
		return $payment_meta;
	}
	
	/**
	 * Validate Payment method change
	 *
	 * @since 2.0
	 *
	 * @param $payment_method_id
	 * @param $payment_meta
	 *
	 * @throws Exception
	 */
	public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {
		if ( $this->id === $payment_method_id ) {
			if ( ! isset( $payment_meta['post_meta']['_wc_pay360_customer_profile_id']['value'] )
			     || empty( $payment_meta['post_meta']['_wc_pay360_customer_profile_id']['value'] )
			) {
				throw new Exception( 'A Pay360 customer profile ID is required.' );
			}
			
			if ( ! isset( $payment_meta['post_meta']['_wc_pay360_customer_payment_card_token']['value'] )
			     || empty( $payment_meta['post_meta']['_wc_pay360_customer_payment_card_token']['value'] )
			) {
				throw new Exception( 'A Pay360 payment token value is required.' );
			}
		}
	}
	
	/**
	 * Charge the payment on order release
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 */
	public function process_pre_order_release_payment( \WC_Order $order ) {
		try {
			WC_Pay360_Debug::add_debug_log( 'Pre-Order release payment: ' . print_r( WC_Pay360_Compat::get_order_id( $order ), true ) );
			
			if ( 'hosted_cashier' != $this->integration ) {
				throw new Exception( __( 'Pro-Order release can only be processed with Hosted Cashier integration.', WC_Pay360::TEXT_DOMAIN ) );
			}
			
			// Load the api class
			$integration = new WC_Pay360_Cashier_Request( $this );
			$response    = $integration->merchant_token_payment( $order, $order->get_total() );
			
			$api_response = new WC_Pay360_Cashier_Response( $this );
			
			if ( $api_response->validate_response( $order, $response ) ) {
				$api_response->process_response( $order, $response, false );
			}
		}
		catch ( Exception $e ) {
			$order->add_order_note( $e->getMessage(), 'error' );
			
			// Debug log
			WC_Pay360_Debug::add_debug_log( $e->getMessage() );
		}
	}
}