<?php

namespace WcPay360;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order wrapper for gateway order data
 *
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2018-2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Pay360_Order {
	
	/**
	 * @var \WC_Order
	 */
	public $order;
	
	/**
	 * @param $order
	 */
	public function __construct( \WC_Order $order ) {
		$this->order = $order;
	}
	
	/**---------------------------------
	 * GETTERS
	 * -----------------------------------*/
	
	/**
	 * Return the order number with stripped # or n° ( french translations )
	 *
	 * @return string
	 */
	public function get_order_number() {
		return str_replace( array( '#', 'n°' ), '', $this->order->get_order_number() );
	}
	
	/**
	 * Returns the payment captured data
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public function get_is_payment_captured() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_is_payment_captured' );
	}
	
	/**
	 * Returns the amount captured
	 *
	 * @since 2.2.0
	 *
	 * @return float
	 */
	public function get_order_amount_captured() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_order_amount_captured', '' );
	}
	
	/**
	 * Returns the amount authorized in the transaction
	 *
	 * @since 2.2.0
	 *
	 * @return float
	 */
	public function get_order_amount_authorized() {
		$amount = \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_order_amount_authorized' );
		
		if ( '' === $amount ) {
			$amount = $this->order->get_total();
		}
		
		return $amount;
	}
	
	public function get_customer_payment_token() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_customer_payment_card_token' );
	}
	
	public function get_customer_payment_card() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_customer_payment_card', true, array() );
	}
	
	public function get_customer_profile_id() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_customer_profile_id' );
	}
	
	public function get_transaction_merchant_reference_id() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_transaction_merchant_reference_id' );
	}
	
	public function get_transaction_id() {
		return \WC_Pay360_Compat::get_meta( $this->order, '_wc_pay360_transaction_id' );
	}
	
	/**---------------------------------------------------
	 * CREATE
	 * ---------------------------------------------------*/
	
	/**
	 * Marks the order the amount captured
	 *
	 * @param bool $is_captured
	 *
	 * @since 2.2.0
	 */
	public function save_is_payment_captured( $is_captured = false ) {
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_is_payment_captured', wc_clean( $is_captured ) );
	}
	
	/**
	 * Marks the order payment as captured or not
	 *
	 * @since 2.2.0
	 *
	 * @param bool $amount (optional) If not present the order total will be saved
	 */
	public function save_order_amount_captured( $amount = false ) {
		if ( false === $amount ) {
			$amount = $this->order->get_total();
		}
		
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_order_amount_captured', wc_clean( $amount ) );
	}
	
	/**
	 * Saves the amount authorized in the transaction
	 *
	 * @since 2.2.0
	 *
	 * @param bool $amount
	 */
	public function save_order_amount_authorized( $amount = false ) {
		if ( false === $amount ) {
			$amount = $this->order->get_total();
		}
		
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_order_amount_authorized', wc_clean( $amount ) );
	}
	
	/**
	 * @param $value
	 */
	public function save_customer_payment_token( $value ) {
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_customer_payment_card_token', wc_clean( $value ) );
	}
	
	public function save_customer_payment_card( $value ) {
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_customer_payment_card', wc_clean( $value ) );
	}
	
	/**
	 * @param $value
	 */
	public function save_customer_profile_id( $value ) {
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_customer_profile_id', wc_clean( $value ) );
	}
	
	public function save_transaction_merchant_reference_id( $value ) {
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_transaction_merchant_reference_id', wc_clean( $value ) );
	}
	
	public function save_transaction_id( $value ) {
		\WC_Pay360_Compat::update_meta( $this->order, '_wc_pay360_transaction_id', wc_clean( $value ) );
		\WC_Pay360_Compat::update_meta( $this->order, '_transaction_id', wc_clean( $value ) );
	}
	
	/**
	 * Returns all subscriptions for the order
	 * @return array
	 */
	public function get_subscriptions() {
		$subscriptions = array();
		
		if ( ! \WC_Pay360::is_subscriptions_active() ) {
			return $subscriptions;
		}
		
		// Also store it on the subscriptions being purchased or paid for in the order
		if ( wcs_order_contains_subscription( $this->order ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $this->order );
		} elseif ( wcs_order_contains_renewal( $this->order ) ) {
			$subscriptions = wcs_get_subscriptions_for_renewal_order( $this->order );
		}
		
		return $subscriptions;
	}
	
	/**---------------------------------------------------
	 * DELETE
	 * ---------------------------------------------------*/
	
	/**
	 * Deletes the is payment captured mark
	 *
	 * @since 2.2.0
	 */
	public function delete_is_payment_captured() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_is_payment_captured' );
	}
	
	/**
	 * Deletes the amount captured value
	 *
	 * @since 2.2.0
	 */
	public function delete_order_amount_captured() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_order_amount_captured' );
	}
	
	/**
	 * Deletes the amount authorized meta
	 *
	 * @since 2.2.0
	 *
	 * @return bool|int
	 */
	public function delete_order_amount_authorized() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_order_amount_authorized' );
	}
	
	/**
	 * @return bool|int
	 */
	public function delete_customer_payment_token() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_customer_payment_card_token' );
	}
	
	public function delete_customer_payment_card() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_customer_payment_card' );
	}
	
	/**
	 * @return bool|int
	 */
	public function delete_customer_profile_id() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_customer_profile_id' );
	}
	
	public function delete_transaction_merchant_reference_id() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_transaction_merchant_reference_id' );
	}
	
	public function delete_transaction_id() {
		return \WC_Pay360_Compat::delete_meta( $this->order, '_wc_pay360_transaction_id' );
	}
	
	/**---------------------------------------------------
	 * Functional Checks
	 * ---------------------------------------------------*/
	
	/**
	 * @param string $transaction_id
	 */
	public function complete_order( $transaction_id ) {
		if ( $this->is_pre_order_with_tokenization() ) {
			// Now that we have the info need for future payment, mark the order pre-ordered
			\WC_Pre_Orders_Order::mark_order_as_pre_ordered( $this->order );
		} else {
			$this->order->payment_complete( $transaction_id );
		}
	}
	
	/**
	 * Returns true, if order contains Subscription
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function contains_subscription() {
		if ( ! \WC_Pay360::is_subscriptions_active() ) {
			return false;
		}
		
		if ( wcs_order_contains_subscription( $this->order )
		     || wcs_order_contains_renewal( $this->order ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns whether or not the order is a WC_Subscription
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function is_subscription() {
		if ( ! \WC_Pay360::is_subscriptions_active() ) {
			return false;
		}
		
		return wcs_is_subscription( $this->order );
	}
	
	/**
	 * Returns true, if order contains Pre-Order
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function contains_pre_order() {
		if ( ! \WC_Pay360::is_pre_orders_active() ) {
			return false;
		}
		
		return \WC_Pre_Orders_Order::order_contains_pre_order( $this->order );
	}
	
	/**
	 * Returns true if the order is a pre-order and it requires tokenization(charged at release)
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function is_pre_order_with_tokenization() {
		if ( ! \WC_Pay360::is_pre_orders_active() ) {
			return false;
		}
		
		return \WC_Pre_Orders_Order::order_contains_pre_order( $this->order )
		       && \WC_Pre_Orders_Order::order_requires_payment_tokenization( $this->order );
	}
}