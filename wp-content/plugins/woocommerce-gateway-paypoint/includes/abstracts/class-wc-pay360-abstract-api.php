<?php

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
abstract class WC_Pay360_Abstract_Api {

	protected $installation;
	protected $username;
	protected $password;
	protected $test_mode;
	protected $api_base;

	/**
	 * WC_Pay360_Abstract_Api constructor.
	 *
	 * @param string $installation
	 * @param string $username
	 * @param string $password
	 * @param bool   $test_mode
	 */
	public function __construct( $installation, $username, $password, $test_mode = false ) {
		$this->installation = $installation;
		$this->username     = $username;
		$this->password     = $password;
		$this->test_mode    = $test_mode;
	}

	/**
	 * Returns the API base url
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_base_url() {
		if ( null == $this->api_base ) {
			if ( $this->get_test_mode() ) {
				$this->api_base = 'https://api.mite.pay360.com';
			} else {
				$this->api_base = 'https://api.pay360.com';
			}
		}

		return $this->api_base;
	}

	/**
	 * Returns the ping endpoint
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_ping_endpoint() {
		return '/hosted/rest/sessions/ping';
	}

	/**
	 * Returns the test mode status
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function get_test_mode() {
		return $this->test_mode;
	}

	/**
	 * Sens the request to the Pay360 API
	 *
	 * @since 2.0
	 *
	 * @param        $url
	 * @param        $parameters
	 * @param string $method
	 *
	 * @return array|WP_Error
	 * @throws Exception
	 */
	public function send( $url, $parameters, $method = 'POST' ) {
		$post_args = array(
			'headers'   => array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . $this->get_authorization_credentials(),
			),
			'method'    => $method,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'   => 30,
		);

		if ( ! empty( $parameters ) ) {
			$post_args['body'] = json_encode( $parameters );
		}

		$response = wp_remote_post( $url, $post_args );

		// Error checks
		$this->check_response_is_wp_error( $response );
		$this->check_response_code( $response['response']['code'], $response['response']['message'] );

		return $response;
	}

	/**
	 * Pings the API to see if we have a connection
	 *
	 * @since 2.0
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function send_ping() {
		$url_ping = $this->get_base_url() . $this->get_ping_endpoint();
		$this->send( $url_ping, array(), 'GET' );

		return true;
	}

	/**
	 * Joins the username and password to form the authorization credentials
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_authorization_credentials() {
		return base64_encode( $this->username . ':' . $this->password );
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
	 * @throws Exception]
	 */
	public function check_json_response_body( $response_body ) {
		if ( 0 !== strpos( $response_body, '{' ) ) {
			throw new Exception( __( 'Payment setup response was not formatted appropriately. Please refresh and try again or contact administrator.', WC_Pay360::TEXT_DOMAIN ) );
		}
	}

	/**
	 * Checks to make sure that we got 200 or 201 response code
	 *
	 * @since 2.0
	 *
	 * @param $code
	 * @param $message
	 *
	 * @throws Exception
	 */
	public function check_response_code( $code, $message ) {
		if ( 200 != $code && 201 != $code ) {
			throw new Exception( sprintf( __( 'There was a problem with the request. Error code %s. Error message: %s.', WC_Pay360::TEXT_DOMAIN ), $code, $message ) );
		}
	}

	/**
	 * Checks if the response is WP error
	 *
	 * @since 2.0
	 *
	 * @param WP_Error|array $response
	 *
	 * @throws Exception
	 */
	public function check_response_is_wp_error( $response ) {
		if ( is_wp_error( $response ) ) {
			throw new Exception( sprintf( __( 'Error while processing your request. Error message: %s.', WC_Pay360::TEXT_DOMAIN ), $response->get_error_message() ) );
		}
	}
}