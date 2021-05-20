<?php

namespace WcPay360\Api;

use WcPay360\Api\Cashier\Cashier_Service;
use WcPay360\Api\Exceptions\Exception;
use WcPay360\Api\Hosted_Cashier\Hosted_Cashier_Service;
use \WcPay360\Api\Errors\Hosted_Cashier as Hosted_Cashier_Errors;
use \WcPay360\Api\Errors\Cashier_Capture as Cashier_Capture_Errors;
use \WcPay360\Api\Errors\Cashier_Cancel as Cashier_Cancel_Errors;
use \WcPay360\Api\Errors\Cashier_Refund as Cashier_Refund_Errors;
use \WcPay360\Api\Errors\Cashier_Card_Lock as Cashier_Card_Lock_Errors;
use \WcPay360\Api\Errors\Cashier_Validation as Cashier_Validation_Errors;
use \WcPay360\Api\Errors\Cashier_General as Cashier_General_Errors;
use \WcPay360\Api\Errors\Cashier_Payments as Cashier_Payments_Errors;

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
class Client {

	protected $installation;
	protected $username;
	protected $password;
	protected $test_mode;
	protected $base_url;

	/**
	 * WC_Pay360_Abstract_Api constructor.
	 *
	 * @param string $installation
	 * @param string $username
	 * @param string $password
	 * @param bool   $test_mode
	 *
	 * @throws Exception
	 */
	public function __construct( $installation, $username, $password, $test_mode = false ) {
		if ( ! is_string( $installation ) ) {
			throw new Exception( 'Invalid parameter installations ID. String Expected' );
		}
		if ( ! is_string( $username ) ) {
			throw new Exception( 'Invalid parameter username. String Expected' );
		}
		if ( ! is_string( $password ) ) {
			throw new Exception( 'Invalid parameter password. String Expected' );
		}
		$this->installation = $installation;
		$this->username     = $username;
		$this->password     = $password;
		$this->test_mode    = $test_mode;
	}

	/**===============================
	 * Services
	 * ================================*/

	/**
	 * Returns the Ping service object
	 *
	 * @since 2.0
	 *
	 * @return Ping_Service
	 */
	public function get_ping_service() {
		return new Ping_Service( $this );
	}

	/**
	 * Returns the hosted cashier service object.
	 *
	 * @since 2.0
	 *
	 * @return Hosted_Cashier_Service
	 */
	public function get_hosted_cashier_service() {
		return new Hosted_Cashier_Service( $this );
	}

	/**
	 * Returns the hosted cashier service object.
	 *
	 * @since 2.0
	 *
	 * @return Cashier_Service
	 */
	public function get_cashier_service() {
		return new Cashier_Service( $this );
	}

	/**===============================
	 * Actions
	 * =================================*/
	/**
	 * Sends the request and returns the response
	 *
	 * @since 2.0
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 * @param string $method
	 *
	 * @return array|\WP_Error
	 * @throws \WcPay360\Api\Exceptions\Exception
	 */
	public function send_request( $endpoint, $parameters, $method = 'POST' ) {
		$url = $this->get_base_url() . $endpoint;

		$post_args = array(
			'headers'   => array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( $this->build_authorization_credentials() ),
			),
			'method'    => $method,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'   => 30,
		);

		if ( ! is_array( $parameters ) ) {
			$parameters = array();
		}
		$post_args['body'] = json_encode( $parameters );
		if ( empty( $parameters ) && 'GET' == $method ) {
			unset( $post_args['body'] );
		}
		
		$response = wp_remote_post( $url, $post_args );

		// Do the basic checks for the request
		$this->check_response_is_wp_error( $response );

		// Pass the complete response
		return $response;
	}

	/**===============================
	 * Getters
	 * =================================*/
	/**
	 * Returns installation
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_installation() {
		return $this->installation;
	}

	/**
	 * Returns the username
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_username() {
		return $this->username;
	}

	/**
	 * Returns the password
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_password() {
		return $this->password;
	}

	/**
	 * Returns the test mode
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function get_test_mode() {
		return $this->test_mode;
	}

	/**
	 * Returns the API base url
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_base_url() {
		if ( null == $this->base_url ) {
			if ( $this->get_test_mode() ) {
				$this->base_url = 'https://api.mite.pay360.com';
			} else {
				$this->base_url = 'https://api.pay360.com';
			}
		}

		return $this->base_url;
	}

	/**
	 * Joins the username and password to form the authorization credentials
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function build_authorization_credentials() {
		return $this->get_username() . ':' . $this->get_password();
	}


	/*===================================
	 * Error Checks
	 ====================================*/

	/**
	 * Checks the response body is JSON data
	 *
	 * @since 2.0
	 *
	 * @param $response_body
	 *
	 * @throws \WcPay360\Api\Exceptions\Exception
	 * @return bool
	 */
	public function check_json_response_body( $response_body ) {
		if ( 0 !== strpos( $response_body, '{' ) && 0 !== strpos( $response_body, '[{' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks to make sure that we got 200 or 201 response code
	 *
	 * @since 2.0
	 *
	 * @param array  $response
	 * @param string $service_type
	 *
	 * @throws \WcPay360\Api\Exceptions\Exception
	 */
	public function check_response_code( $response, $service_type ) {
		$code    = $response['response']['code'];
		$message = $response['response']['message'];
		if ( 200 != $code && 201 != $code ) {
			$response_body = wp_remote_retrieve_body( $response );
			if ( $this->check_json_response_body( $response_body ) ) {
				$decoded_body  = json_decode( $response_body );
				$error_service = $this->error_codes_service( $decoded_body, $service_type );
				$code          = $error_service->get_code();
				$message       = $error_service->get_message();
			}
			throw new Exception( sprintf( __( 'There was a problem with the request. Error code %s. Error message: %s.', \WC_Pay360::TEXT_DOMAIN ), $code, $message ) );
		}
	}

	/**
	 * Checks if the response is WP error
	 *
	 * @since 2.0
	 *
	 * @param \WP_Error|array $response
	 *
	 * @throws \WcPay360\Api\Exceptions\Exception
	 */
	public function check_response_is_wp_error( $response ) {
		if ( is_wp_error( $response ) ) {
			throw new Exception( sprintf( __( 'Error while processing your request. Error message: %s.', \WC_Pay360::TEXT_DOMAIN ), $response->get_error_message() ) );
		}
	}

	/**
	 * Returns the object that translates the error codes for the service type
	 *
	 * @since 2.0
	 *
	 * @param object $body         Response Body
	 * @param string $service_type The service type
	 *
	 * @return Hosted_Cashier_Errors
	 */
	public function error_codes_service( $body, $service_type ) {
		switch ( $service_type ) {
			case 'hosted_cashier' :
				$service = new Hosted_Cashier_Errors( $body );
				break;

			case 'cashier_payments' :
				$service = new Cashier_Payments_Errors( $body );
				break;

			case 'cashier_capture' :
				$service = new Cashier_Capture_Errors( $body );
				break;

			case 'cashier_refund' :
				$service = new Cashier_Refund_Errors( $body );
				break;

			case 'cashier_validation' :
				$service = new Cashier_Validation_Errors( $body );
				break;

			case 'cashier_cancel' :
				$service = new Cashier_Cancel_Errors( $body );
				break;

			case 'cashier_card_lock' :
				$service = new Cashier_Card_Lock_Errors( $body );
				break;

			default:
				$service = new Cashier_General_Errors( $body );
				break;
		}

		return $service;
	}
}