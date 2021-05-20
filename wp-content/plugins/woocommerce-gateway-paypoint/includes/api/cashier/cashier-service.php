<?php

namespace WcPay360\Api\Cashier;

use WcPay360\Api\Client;
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
class Cashier_Service {
	
	/**
	 * @var Client
	 */
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
	 * Return the client
	 * @return Client
	 */
	public function get_client() {
		return $this->client;
	}
	
	/**
	 * Processes a payment and returns the result.
	 *
	 * @since 2.1
	 *
	 * @param array $arguments The arguments to be send with the request.
	 *
	 * @throws Exception
	 * @return object
	 */
	public function process_payment( $arguments ) {
		$process = new Payments( $this );
		
		$capture = $process->payment( $arguments );
		
		return $this->retrieve_response_body( $capture, 'payment' );
	}
	
	/**
	 * Processes a capture and returns the result.
	 *
	 * @since 2.0
	 *
	 * @param string $id      Transaction/Merchant Reference ID of the transaction we will capture fund for
	 * @param string $id_type The type of the ID
	 *
	 * @throws Exception
	 * @throws Invalid_Argument
	 * @return object
	 */
	public function process_transaction_lookup( $id, $id_type ) {
		
		// Type can't be anything else but 'merchantRef' and 'transaction'
		if ( 'merchantRef' != $id_type ) {
			$id_type = 'transaction';
		}
		
		$process = new Transactions( $this );
		
		$transaction = $process->retrieve_transaction( $id, $id_type );
		
		return $this->retrieve_response_body( $transaction, 'retrieve transaction' );
	}
	
	/**
	 * Processes a capture and returns the result.
	 *
	 * @since 2.0
	 *
	 * @param string $transaction_id Transaction ID of the transaction we will capture fund for
	 * @param array  $arguments      The arguments to be send with the request.
	 *
	 * @throws Exception
	 * @throws Invalid_Argument
	 * @return object
	 */
	public function process_capture( $transaction_id, $arguments ) {
		$process = new Captures( $this );
		
		$capture = $process->capture( $transaction_id, $arguments );
		
		return $this->retrieve_response_body( $capture, 'capture' );
	}
	
	/**
	 * Processes refund request
	 *
	 * @since 2.0
	 *
	 * @param $transaction_id
	 * @param $arguments
	 *
	 * @return object
	 * @throws Exception
	 * @throws Invalid_Argument
	 */
	public function process_refund( $transaction_id, $arguments ) {
		$process = new Refunds( $this );
		
		$refund = $process->refund( $transaction_id, $arguments );
		
		return $this->retrieve_response_body( $refund, 'refund' );
	}
	
	/**
	 * Processes cancellation request
	 *
	 * @since 2.0
	 *
	 * @param $transaction_id
	 * @param $arguments
	 *
	 * @return object
	 * @throws Exception
	 * @throws Invalid_Argument
	 */
	public function process_cancellation( $transaction_id, $arguments ) {
		$process = new Cancels( $this );
		
		$cancel = $process->cancel( $transaction_id, $arguments );
		
		return $this->retrieve_response_body( $cancel, 'cancel' );
	}
	
	/**
	 * Retrieves the response body from a Pay360 response
	 *
	 * @since 2.0
	 *
	 * @param $response
	 * @param $response_type
	 *
	 * @return object
	 * @throws Exception
	 */
	public function retrieve_response_body( $response, $response_type ) {
		// Get response body
		$response_body = wp_remote_retrieve_body( $response );
		
		// Check that the body is json.
		if ( ! $this->get_client()->check_json_response_body( $response_body ) ) {
			throw new Exception( sprintf( __( '%s request did not receive properly formatted response.', \WC_Pay360::TEXT_DOMAIN ), ucfirst( $response_type ) ) );
		}
		
		return json_decode( $response_body );
	}
	
	/**
	 * Returns the action part of the request URL
	 *
	 * @since 2.0
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public function get_action_endpoint( $type ) {
		switch ( $type ) {
			case 'payments' :
				$action = '/payments';
				break;
			case 'payment' :
				$action = '/payment';
				break;
			case 'capture' :
			case 'captures' :
				$action = '/capture';
				break;
			case 'refund' :
			case 'refunds' :
				$action = '/refund';
				break;
			case 'cancel' :
			case 'cancels' :
				$action = '/cancel';
				break;
			
			default:
				$action = '/';
		}
		
		return $action;
	}
}