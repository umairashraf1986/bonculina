<?php

namespace WcPay360\Admin;

use WcPay360\Pay360_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Export and Erasure
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Privacy_Policy extends \WC_Abstract_Privacy {
	
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Pay360', \WC_Pay360::TEXT_DOMAIN ) );
		
		$this->add_exporter( 'woocommerce-gateway-pay360-order-data',
			__( 'WooCommerce Pay360 Order Data', \WC_Pay360::TEXT_DOMAIN ), array(
				$this,
				'order_data_exporter'
			) );
		
		if ( function_exists( 'wcs_get_subscriptions' ) ) {
			$this->add_exporter( 'woocommerce-gateway-pay360-subscriptions-data',
				__( 'WooCommerce Pay360 Subscriptions Data', \WC_Pay360::TEXT_DOMAIN ), array(
					$this,
					'subscriptions_data_exporter'
				) );
		}
		
		$this->add_eraser( 'woocommerce-gateway-pay360-order-data',
			__( 'WooCommerce Pay360 Data', \WC_Pay360::TEXT_DOMAIN ), array(
				$this,
				'order_data_eraser'
			) );
	}
	
	/**
	 * Returns list of orders paid with Pay360
	 *
	 * @param string $email_address
	 * @param int    $page
	 *
	 * @return array WC_Order
	 */
	protected function get_gateway_orders( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		
		$order_query = array(
			'payment_method' => array(
				'pay360',
			),
			'limit'          => 10,
			'page'           => $page,
		);
		
		if ( $user instanceof \WP_User ) {
			$order_query['customer_id'] = (int) $user->ID;
		} else {
			$order_query['billing_email'] = $email_address;
		}
		
		return wc_get_orders( $order_query );
	}
	
	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( __( 'By using this extension, you may be storing personal data or sharing data with an external service.', \WC_Pay360::TEXT_DOMAIN ) );
	}
	
	/**
	 * Handle exporting data for Orders.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function order_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();
		
		$orders = $this->get_gateway_orders( $email_address, (int) $page );
		
		if ( 0 < count( $orders ) ) {
			/**
			 * @var \WC_Order $order
			 */
			foreach ( $orders as $order ) {
				$pay360_order = new Pay360_Order( $order );
				$card_token   = $pay360_order->get_customer_payment_token();
				
				if ( empty( $card_token ) ) {
					continue;
				}
				
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_orders',
					'group_label' => __( 'Orders', \WC_Pay360::TEXT_DOMAIN ),
					'item_id'     => 'order-' . $order->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'Pay360 Token', \WC_Pay360::TEXT_DOMAIN ),
							'value' => $card_token,
						),
					),
				);
			}
			
			$done = 10 > count( $orders );
		}
		
		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}
	
	/**
	 * Handle exporting data for Subscriptions.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function subscriptions_data_exporter( $email_address, $page = 1 ) {
		$page           = (int) $page;
		$data_to_export = array();
		
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key'     => '_payment_method',
				'value'   => array(
					'pay360',
				),
				'compare' => 'IN',
			),
			array(
				'key'     => '_billing_email',
				'value'   => $email_address,
				'compare' => '=',
			),
		);
		
		$subscription_query = array(
			'posts_per_page' => 10,
			'page'           => $page,
			'meta_query'     => $meta_query,
		);
		
		$subscriptions = wcs_get_subscriptions( $subscription_query );
		
		$done = true;
		
		if ( 0 < count( $subscriptions ) ) {
			/**
			 * @var \WC_Subscription $subscription
			 */
			foreach ( $subscriptions as $subscription ) {
				$pay360_sub = new Pay360_Order( $subscription );
				$card_token = $pay360_sub->get_customer_payment_token();
				
				if ( empty( $card_token ) ) {
					continue;
				}
				
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_subscriptions',
					'group_label' => __( 'Subscriptions', \WC_Pay360::TEXT_DOMAIN ),
					'item_id'     => 'subscription-' . $subscription->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'Pay360 Token', \WC_Pay360::TEXT_DOMAIN ),
							'value' => $card_token,
						),
					),
				);
			}
			
			$done = 10 > count( $subscriptions );
		}
		
		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}
	
	/**
	 * Finds and erases order data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page          Page.
	 *
	 * @return array An array of personal data in name value pairs
	 */
	public function order_data_eraser( $email_address, $page ) {
		$orders = $this->get_gateway_orders( $email_address, (int) $page );
		
		$items_removed  = false;
		$items_retained = false;
		$messages       = array();
		
		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );
			
			list( $removed, $retained, $msgs ) = $this->maybe_handle_order( $order );
			$items_removed  = $items_removed || $removed;
			$items_retained = $items_retained || $retained;
			$messages       = array_merge( $messages, $msgs );
			
			list( $removed, $retained, $msgs ) = $this->maybe_handle_subscription( $order );
			$items_removed  = $items_removed || $removed;
			$items_retained = $items_retained || $retained;
			$messages       = array_merge( $messages, $msgs );
		}
		
		// Tell core if we have more orders to work on still
		$done = count( $orders ) < 10;
		
		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}
	
	/**
	 * Handle eraser of data tied to Subscriptions
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	protected function maybe_handle_subscription( $order ) {
		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return array( false, false, array() );
		}
		
		if ( ! wcs_order_contains_subscription( $order ) ) {
			return array( false, false, array() );
		}
		
		/**
		 * @var \WC_Subscription $subscription
		 */
		$subscription = current( wcs_get_subscriptions_for_order( $order->get_id() ) );
		
		$pay360_sub = new Pay360_Order( $subscription );
		$card_token = $pay360_sub->get_customer_payment_token();
		
		// Bail, if the subscription was not paid with the Pay360 token
		if ( empty( $card_token ) ) {
			return array( false, false, array() );
		}
		
		if ( $subscription->has_status( apply_filters( 'wc_pay360_privacy_eraser_subscription_statuses', array(
			'on-hold',
			'active'
		) ) )
		) {
			return array(
				false,
				true,
				array( sprintf( __( 'Order ID %d contains an active Subscription' ), $order->get_id() ) )
			);
		}
		
		$renewal_orders = $subscription->get_related_orders( 'ids', 'renewal' );
		
		foreach ( $renewal_orders as $renewal_order_id ) {
			$renewal        = wcs_get_subscription( $renewal_order_id );
			$pay360_renewal = new Pay360_Order( $renewal );
			$renewal_token  = $pay360_renewal->get_customer_payment_token();
			
			// Bail, if the renewal was not paid with the Pay360 token
			if ( empty( $renewal_token ) ) {
				continue;
			}
			
			$this->delete_order_data( $renewal );
		}
		
		$this->delete_order_data( $subscription );
		
		return array(
			true,
			false,
			array( __( 'Pay360 Subscription Data Erased.', \WC_Pay360::TEXT_DOMAIN ) )
		);
	}
	
	/**
	 * Handle eraser of data tied to Orders
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	protected function maybe_handle_order( $order ) {
		$pay360_order = new Pay360_Order( $order );
		$pay360_order->delete_customer_payment_token();
		
		return array(
			true,
			false,
			array( __( 'Pay360 personal data erased.', \WC_Pay360::TEXT_DOMAIN ) )
		);
	}
	
	public function delete_order_data( $order ) {
		if ( ! $order instanceof \WC_Order ) {
			return false;
		}
		
		$pay360_order = new Pay360_Order( $order );
		$pay360_order->delete_customer_payment_token();
		
		return true;
	}
}