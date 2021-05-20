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
class Cashier_Card_Lock implements Errors_Interface {

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
			'V00' => _x( 'Bad request. Catch-all condition for an invalid or malformed tokenisation request', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'V01' => _x( 'Missing PAN. No card number was provided', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'V02' => _x( 'Invalid PAN. The card number provided was invalid (non-numeric, or too long or short)', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'A00' => _x( 'Access denied. Catch-all condition for an authentication/authorisation failure', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'A01' => _x( 'Unknown publishable ID. The publishable ID provided was not recognised', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'E00' => _x( 'Service unavailable. Catch-all condition for unclassified communication or service errors, including connection problems and time outs', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
			'E01' => _x( 'Internal error. Internal error occurred while issuing the token', 'cashier-capture-error', \WC_Pay360::TEXT_DOMAIN ),
		);

		if ( isset( $map[ $this->get_code() ] ) ) {
			return $map[ $this->get_code() ];
		}

		return isset( $this->response->reasonMessage ) ? $this->response->reasonMessage : '';
	}
}