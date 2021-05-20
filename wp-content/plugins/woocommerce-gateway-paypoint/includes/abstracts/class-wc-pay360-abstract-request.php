<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Request class to serve as a basis for the integrations requests.
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WC_Pay360_Abstract_Request extends WC_Pay360_Abstract_Integration {

	/**
	 * Init the class
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}

	/**
	 * Return the order currency
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return mixed
	 */
	public function get_currency( $order ) {
		return WC_Pay360_Compat::get_order_currency( $order );
	}

	/**
	 * Returns the API return url
	 *
	 * @since 2.0
	 *
	 * @param string $name The name of the api endpoint we will use as return
	 *
	 * @return string
	 */
	public function get_return_url( $name = '' ) {

		if ( ! $name ) {
			$name = WC_Pay360::get_gateway_class();
		}

		return WC()->api_request_url( $name );
	}

	/**
	 * Get the item name with meta.
	 *
	 * @since 2.0
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function get_order_item_name( $item ) {
		$item_meta = WC_Pay360_Compat::wc_display_item_meta( $item );

		$item_name = WC_Pay360_Compat::get_item_name( $item );
		if ( $item_meta ) {
			$item_name .= ' (' . $item_meta . ')';
		}

		$prod_name = str_replace( '_', '', $item_name );

		return $prod_name;
	}

	/**
	 * Returns the customer IP address
	 *
	 * 'wc_pay360_get_user_ip_default_address' - modify the default IP address
	 * 'wc_pay360_get_user_ip_addr' - modify the IP address
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_customer_ip() {
		$ip = apply_filters( 'wc_pay360_get_user_ip_default_address', '127.0.0.1' );

		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return apply_filters( 'wc_pay360_get_user_ip_addr', $ip );
	}

	/**
	 * Returns order suffix, to prevent duplicate order reference numbers
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param          $increment $order
	 *
	 * @return string
	 */
	public function get_attempts_suffix( WC_Order $order, $increment = true ) {
		// Add a retry count suffix to the orderID.
		$attempts        = get_post_meta( WC_Pay360_Compat::get_order_id( $order ), '_pay360_order_payment_attempts', true );
		$attempts_suffix = (int) $attempts;;

		if ( $increment ) {
			$attempts_suffix ++;
		}

		// Save the incremented attempts
		update_post_meta( WC_Pay360_Compat::get_order_id( $order ), '_pay360_order_payment_attempts', $attempts_suffix );

		return $attempts_suffix;
	}
}