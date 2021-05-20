<?php

namespace WcPay360\Api\Hosted_Cashier;

use WcPay360\Api\Client;
use WcPay360\Api\Exceptions\Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Hosted_Cashier_Service {

	protected $client;

	/**
	 * Request constructor.
	 *
	 * @param Client $client
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * @return Client
	 */
	public function get_client() {
		return $this->client;
	}

	/**
	 * Calls the API to get the redirect/payment url.
	 *
	 * @param $parameters
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get_payment_url( $parameters ) {
		$payments = new Payments( $this );

		$response = $payments->payment( $parameters );

		// Decode the body response
		$decoded_response = json_decode( $response );

		if ( 'FAILED' == $decoded_response->status ) {
			$error_service = $this->client->error_codes_service( $decoded_response, 'hosted_cashier' );
			$message       = $error_service->get_message();
			$error_message = sprintf( __( 'There was a problem with the payment request. Message: %s.', \WC_Pay360::TEXT_DOMAIN ), $message );
			\WC_Pay360_Debug::add_debug_log( $error_message );
			throw new Exception( $error_message );
		}

		return $decoded_response->redirectUrl;
	}

	/**
	 * Returns the action part of the request URL
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public function get_action_endpoint( $type ) {
		switch ( $type ) {
			case 'payments' :
			case 'payment' :
				$action = '/payments';
				break;
			case 'verify' :
				$action = '/verify';
				break;
			default:
				$action = '/';
		}

		return $action;
	}

	public function get_verification_url($parameters) {
		$verification = new Verification( $this );

		$response = $verification->verify( $parameters );

		// Decode the body response
		$decoded_response = json_decode( $response );

		if ( 'FAILED' == $decoded_response->status ) {
			$error_service = $this->client->error_codes_service( $decoded_response, 'hosted_cashier' );
			$message       = $error_service->get_message();
			$error_message = sprintf( __( 'There was a problem with the payment request. Message: %s.', \WC_Pay360::TEXT_DOMAIN ), $message );
			\WC_Pay360_Debug::add_debug_log( $error_message );
			throw new Exception( $error_message );
		}

		return $decoded_response->redirectUrl;
	}
}