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
class Payments implements Hosted_Cashier_Process_Interface {

	/**
	 * @var Hosted_Cashier_Service
	 */
	protected $service;

	/**
	 * Captures constructor.
	 *
	 * @param Hosted_Cashier_Service $service
	 */
	public function __construct( Hosted_Cashier_Service $service ) {
		$this->service = $service;
	}

	/**
	 * Returns the resource part of the request URL
	 *
	 * @return string
	 */
	public function get_resource_endpoint() {
		return '/hosted/rest/sessions/' . $this->service->get_client()->get_installation();
	}

	public function payment( $arguments ) {
		$url = $this->get_resource_endpoint() . $this->service->get_action_endpoint( 'payments' );

		$response = $this->service->get_client()->send_request( $url, $arguments );

		// Validates the response code
		$this->service->get_client()->check_response_code( $response, 'hosted_cashier' );

		if ( empty( $response ) ) {
			throw new Exception( __( 'There was a problem with the payment request. Payment processor response was empty.', \WC_Pay360::TEXT_DOMAIN ) );
		}

		// Get response body
		$response_body = wp_remote_retrieve_body( $response );

		// Check that the body is json.
		if ( ! $this->service->get_client()->check_json_response_body( $response_body ) ) {
			throw new Exception( __( 'Payment setup response was not formatted appropriately. Please refresh and try again or contact administrator.', \WC_Pay360::TEXT_DOMAIN ) );
		}

		return $response_body;
	}
}