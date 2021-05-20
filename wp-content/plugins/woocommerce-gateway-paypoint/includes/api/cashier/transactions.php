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
class Transactions implements Cashier_Process_Interface {

	/**
	 * @var Cashier_Service
	 */
	protected $service;
	/**
	 * @var string Transaction ID
	 */
	protected $transaction_id;
	/**
	 * @var string Merchant Reference ID
	 */
	protected $merchant_reference_id;
	/**
	 * @var string The type of the transaction we are processing. 'payment', 'lookup', 'autorization', ''
	 */
	protected $transaction_type;

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
		$id = $this->get_merchant_reference_id();
		if ( null == $id ) {
			$id = $this->get_transaction_id();
		}

		if ( empty( $id ) ) {
			throw new Invalid_Argument( "Transaction ID and Merchant Reference ID are not set and we can't build the request. Please provide at least one ID." );
		}

		$resource = $this->get_transaction_id();
		if ( null != $this->get_merchant_reference_id() ) {
			$resource = 'byRef?merchantRef=' . $this->get_merchant_reference_id();
		}

		return '/acceptor/rest/transactions/' . $this->service->get_client()->get_installation() . '/' . $resource;
	}

	/**
	 * Sets and runs the capture request
	 *
	 * @since 2.0
	 *
	 * @param string $id      The ID we'll use to retrieve the transaction
	 * @param string $id_type The type of the id: 'transaction', 'merchantRef'
	 *
	 * @return array|\WP_Error
	 * @throws Exception
	 * @throws Invalid_Argument
	 */
	public function retrieve_transaction( $id, $id_type = 'transaction' ) {
		if ( 'merchantRef' == $id_type ) {
			$this->set_merchant_reference_id( $id );
		} else {
			$this->set_transaction_id( $id );
		}

		$url = $this->get_resource_endpoint();

		$transaction = $this->service->get_client()->send_request( $url, array(), 'GET' );

		$this->service->get_client()->check_response_code( $transaction, 'cashier_generic' );

		return $transaction;
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
	 * Sets the merchant reference ID
	 *
	 * @since 2.0
	 *
	 * @param $id
	 *
	 * @throws Invalid_Argument
	 */
	public function set_merchant_reference_id( $id ) {
		if ( empty( $id ) ) {
			throw new Invalid_Argument( "Merchant Reference ID can't be empty." );
		}

		$this->merchant_reference_id = $id;
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

	/**
	 * Returns the transaction ID
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_merchant_reference_id() {
		return $this->merchant_reference_id;
	}
}