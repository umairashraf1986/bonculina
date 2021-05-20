<?php
/**
 * Class InvalidNumberOfParcelsException
 *
 * @package WPDesk\DpdUk\Api\Exception
 */

namespace WPDesk\DpdUk\Api\Request\Exception;

/**
 * Invalid number of parcels.
 */
class InvalidNumberOfParcelsException extends \Exception {

	/**
	 * InvalidCurrencyException constructor.
	 *
	 * @param string          $message .
	 * @param int             $code .
	 * @param \Exception|null $previous .
	 */
	public function __construct( $message = '', $code = 0, \Exception $previous = null ) {
		if ( empty( $message ) ) {
			$message = __( 'Invalid number of parcels.', 'woocommerce-dpd-uk' );
		}
		parent::__construct( $message, $code, $previous );
	}

}
