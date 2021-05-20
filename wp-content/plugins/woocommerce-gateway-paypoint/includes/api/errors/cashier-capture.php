<?php

namespace WcPay360\Api\Errors;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Hosted Cashier error messages
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Cashier_Capture implements Errors_Interface {

	protected $response;

	/**
	 * Hosted_Cashier constructor.
	 *
	 * @param object $response
	 */
	public function __construct( $response ) {
		$this->response = $response;
	}

	/**
	 * Returns the error code
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	public function get_code() {
		return isset( $this->response->reasonCode ) ? $this->response->reasonCode : '';
	}

	/**
	 * Returns the message corresponding to the error code
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	public function get_message() {
		$map = array(
			'V100' => _x( 'Invalid Amount', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'V501' => _x( 'Amount exceeds the original pre-authorised amount', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'V502' => _x( 'Amount is less than the original pre-authorised amount', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
		);

		if ( isset( $map[ $this->get_code() ] ) ) {
			return $map[ $this->get_code() ];
		}

		return isset( $this->response->reasonMessage ) ? $this->response->reasonMessage : '';
	}
}