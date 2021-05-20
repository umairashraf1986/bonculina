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
abstract class WC_Pay360_Abstract_Response_Legacy extends WC_Pay360_Abstract_Response {

	/**
	 * Init the class
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}

	/**
	 * Get the order for the transaction, from the Pay360 response
	 *
	 * @since 2.0
	 *
	 * @return WC_Order
	 */
	public function get_order_from_response() {
		$order_id = WC_Pay360::get_get( 'order_id' );

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
	 * @param $order
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
	 * @param $order
	 * @param $amount
	 */
	protected function validate_transaction_amount( $order, $amount ) {
		if ( $amount != $order->get_total() ) {
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
	 * Ensure no test transaction is made, when plugin is supposed to run live transactions
	 *
	 * @since 2.0
	 *
	 * @param $order
	 * @param $transaction_mode
	 * @param $gateway_mode
	 */
	protected function validate_gateway_mode( WC_Order $order, $transaction_mode, $gateway_mode ) {
		if ( ( 0 < $transaction_mode ) && 'live' == $gateway_mode ) {
			$message = __( 'You cannot make test payments when your store is set to live mode.', WC_Pay360::TEXT_DOMAIN );

			$this->order_status_on_hold( $order, $message );

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
	 * @param \WC_Order|bool $order
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
}