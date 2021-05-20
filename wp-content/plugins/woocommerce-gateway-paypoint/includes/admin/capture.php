<?php

namespace WcPay360\Admin;

use WcPay360\Helpers\Factories;
use WcPay360\Pay360_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Capture {
	
	public $gateway_id;
	
	public function __construct( $gateway_id ) {
		$this->gateway_id = $gateway_id;
	}
	
	/**
	 * Loads the capture action for the plugin
	 *
	 * This is loaded here and not in the gateway
	 * because we need it to load a little bit earlier for the action to be added to order edit screen
	 *
	 * @since 2.2.0
	 */
	public function hooks() {
		/**
		 * @var \WC_Pay360_Gateway_Addons $gateway
		 */
		$gateway = Factories::get_gateway( $this->gateway_id );
		
		add_filter( 'woocommerce_order_actions', array( $gateway, 'add_order_capture_action' ) );
		add_action( 'woocommerce_order_action_pay360_capture_payment', array(
			$gateway,
			'capture_payment'
		) );
		
		// This is a custom filter that will run the capture_payment process and return its response
		add_filter( 'wc_pay360_capture_payment_for_order', array( $gateway, 'capture_payment' ), 10 );
		
		add_action( 'woocommerce_order_item_add_action_buttons', array(
			$this,
			'order_meta_box_add_capture_payment_buttons'
		) );
	}
	
	/**
	 * Adds Capture buttons to the admin order edit screen
	 *
	 * @since 2.2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return bool
	 */
	public function order_meta_box_add_capture_payment_buttons( $order ) {
		$method = \WC_Pay360_Compat::get_prop( $order, 'payment_method' );
		if ( $this->gateway_id != $method ) {
			return false;
		}
		
		$pay360_order           = new Pay360_Order( $order );
		$is_captured            = $pay360_order->get_is_payment_captured();
		$allowed_order_statuses = self::get_capture_allowed_order_statuses();
		
		if ( $pay360_order->is_subscription() || $is_captured || ! in_array( $order->get_status(), $allowed_order_statuses ) ) {
			return false;
		}
		
		$authorized_amount = $pay360_order->get_order_amount_authorized();
		if ( empty( $authorized_amount ) ) {
			$authorized_amount = $order->get_total();
		}
		
		?>
		<button type="button" class="button button-primary wc-pay360-capture-payment-init">
			<?php echo sprintf( __( 'Capture Transaction (%s)', \WC_Pay360::TEXT_DOMAIN ), get_woocommerce_currency_symbol( \WC_Pay360_Compat::get_order_currency( $order ) ) . $authorized_amount ); ?>
		</button>
		<?php
	}
	
	/**
	 * Returns the allowed order statuses to perform capture of a transaction.
	 * We naturally assume that the status of an order should be a paid order status, not completed and not failed payment.
	 *
	 * @since 2.2.0
	 *
	 * @return mixed
	 */
	public static function get_capture_allowed_order_statuses() {
		return apply_filters( 'wc_pay360_capture_allowed_order_statuses', array(
			'processing',
			'on-hold',
			'active',
		) );
	}
}