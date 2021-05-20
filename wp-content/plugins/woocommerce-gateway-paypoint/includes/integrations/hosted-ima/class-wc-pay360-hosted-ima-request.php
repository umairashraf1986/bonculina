<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hosted IMA integration request class. Generate and submit the IMA form
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Hosted_IMA_Request extends WC_Pay360_Abstract_Request_Legacy {

	/**
	 * URL the form should be submitted to
	 * @var string
	 */
	public $form_url;

	/**
	 * WC_Pay360_Hosted_IMA_Request constructor.
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		$this->form_url = 'https://secure.metacharge.com/mcpe/purser';

		parent::__construct( $gateway );
	}

	/**
	 * Generate the hosted + IMA form parameters
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function get_parameters( WC_Order $order ) {

		$order_number = WC_Pay360::get_order_number( $order );

		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Integration method: Hosted + IMA. Generating parameter...' );

		$amount   = number_format( $order->get_total(), 2, '.', '' );
		$currency = $this->get_currency( $order );

		$args = array(
			'intInstID'         => $this->get_gateway()->intInstID,
			'strCartID'         => WC_Pay360_Compat::get_order_id( $order ) . ':' . WC_Pay360_Compat::get_prop( $order, 'order_key' ) . ':' . $order_number,
			'fltAmount'         => $amount,
			'strCurrency'       => $currency,
			'strCardHolder'     => WC_Pay360_Compat::get_order_billing_first_name( $order ) . ' ' . WC_Pay360_Compat::get_order_billing_last_name( $order ),
			'strAddress'        => WC_Pay360_Compat::get_order_billing_address_1( $order ) . ' ' . WC_Pay360_Compat::get_order_billing_address_2( $order ),
			'strCity'           => WC_Pay360_Compat::get_order_billing_city( $order ),
			'strState'          => WC_Pay360_Compat::get_order_billing_state( $order ),
			'strPostcode'       => WC_Pay360_Compat::get_order_billing_postcode( $order ),
			'strCountry'        => WC_Pay360_Compat::get_order_billing_country( $order ),
			'strTel'            => WC_Pay360_Compat::get_order_billing_phone( $order ),
			'strEmail'          => WC_Pay360_Compat::get_order_billing_email( $order ),
			'PT_invoice'        => WC_Pay360_Compat::get_prop( $order, 'order_key' ),
			'PT_pay360Listener' => 'pay360Response',
		);

		$args['intTestMode'] = $this->gateway_status();
		$args['strDesc']     = $this->get_order_details( $order, $order_number );

		// Allow for parameters modification
		$args = apply_filters( 'pay360_ima_get_parameters', $args, $order, $this->get_gateway() );

		//Hash the main parameters for extra security
		$args['PT_hash'] = $this->hash_params( $args );

		return $args;
	}

	/**
	 * Get the correct form URL,
	 * depending on the integration
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	public function get_form_url() {
		return $this->form_url;
	}

	/**
	 * Get the IMA Gateway request status.
	 * Test mode - success or fail
	 * Live mode
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	private function gateway_status() {
		if ( 'test_success' == $this->get_testmode_status() ) {
			$status = 1;
		} elseif ( 'test_fail' == $this->get_testmode_status() ) {
			$status = 2;
		} else {
			$status = 0;
		}

		return $status;
	}

	/**
	 * Returns the order details description
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $order_number
	 *
	 * @return mixed
	 */
	private function get_order_details( WC_Order $order, $order_number ) {
		// Cart Contents - Generate cart description
		$desc = sprintf( __( 'Payment for Order# %s: ', WC_Pay360::TEXT_DOMAIN ), $order_number );
		if ( 0 < sizeof( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( WC_Pay360_Compat::get_item_quantity( $item ) ) {
					$item_name = $this->get_order_item_name( $item );
					$desc .= WC_Pay360_Compat::get_item_quantity( $item ) . ' x ' . $item_name . ', ';
				}
			}
			//Add the description
			$desc = substr( htmlspecialchars( $desc ), 0, - 2 );
		}

		return $desc;
	}

	/**
	 * HOSTED+IMA PAYMENT
	 * Hash the main request parameters
	 *
	 * @since 2.0
	 *
	 * @param $params
	 *
	 * @return string
	 */
	public function hash_params( $params ) {
		$hash_string = WC_Pay360::get_field( 'intInstID', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'strCartID', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'fltAmount', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'strCurrency', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'PT_invoice', $params, '' ) . ':' .
		               WC_Pay360::get_field( 'intTestMode', $params, '0' );

		return md5( $hash_string );
	}
}