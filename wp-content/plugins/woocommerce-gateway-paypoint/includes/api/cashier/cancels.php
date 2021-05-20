<?php

namespace WcPay360\Api\Cashier;

use WcPay360\Api\Exceptions\Exception;
use WcPay360\Api\Exceptions\Invalid_Argument;

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
class Cancels implements Cashier_Process_Interface {

	/**
	 * @var Cashier_Service
	 */
	protected $service;
	protected $transaction_id;

	/**
	 * Captures constructor.
	 *
	 * @param Cashier_Service $service
	 */
	public function __construct( Cashier_Service $service ) {
		$this->service = $service;
	}

	/**
	 * Returns the resource part of the request URL
	 *
	 * @since 2.0
	 *
	 * @return string
	 * @throws Invalid_Argument
	 */
	public function get_resource_endpoint() {
		$transaction_id = $this->get_transaction_id();
		if ( empty( $transaction_id ) ) {
			throw new Invalid_Argument( "Transaction ID is not set and we can't build the request. Please provide transaction ID." );
		}

		return '/acceptor/rest/transactions/' . $this->service->get_client()->get_installation() . '/' . $this->get_transaction_id();
	}

	/**
	 * Sets and runs the capture request
	 *
	 * @since 2.0
	 *
	 * @param $transaction_id
	 * @param $arguments
	 *
	 * @return array|\WP_Error
	 * @throws Exception
	 * @throws Invalid_Argument
	 */
	public function cancel( $transaction_id, $arguments = array() ) {
		$this->set_transaction_id( $transaction_id );
		$url = $this->get_resource_endpoint() . $this->service->get_action_endpoint( 'cancel' );

		if ( ! is_array( $arguments ) ) {
			$arguments = array( 'transaction' => array() );
		}

		$cancel = $this->service->get_client()->send_request( $url, $arguments );

		$this->service->get_client()->check_response_code( $cancel, 'cashier_cancel' );

		if ( empty( $response ) ) {
			throw new Exception( __( 'There was a problem with cancel request. Pay360 response was empty.', \WC_Pay360::TEXT_DOMAIN ) );
		}

		return $cancel;
	}

	/**
	 * Sets the transaction ID
	 *
	 * @since 2.0
	 *
	 * @param $id
	 *
	 * @throws Invalid_Argument
	 */
	public function set_transaction_id( $id ) {
		if ( empty( $id ) ) {
			throw new Invalid_Argument( "Transaction ID can't be empty." );
		}

		$this->transaction_id = $id;
	}

	/**
	 * Returns the transaction ID
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_transaction_id() {
		return $this->transaction_id;
	}
}