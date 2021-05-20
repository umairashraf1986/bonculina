<?php
/**
 * Class InvalidCurrencyException
 *
 * @package WPDesk\DpdUk\Api\Exception
 */

namespace WPDesk\DpdUk\Api\Request\Exception;

/**
 * Invalid currency exception.
 */
class InvalidCurrencyException extends \Exception {

	/**
	 * InvalidCurrencyException constructor.
	 *
	 * @param string          $message .
	 * @param int             $code .
	 * @param \Exception|null $previous .
	 */
	public function __construct( $message = '', $code = 0, \Exception $previous = null ) {
		if ( empty( $message ) ) {
			$message = sprintf(
				// Translators: support link.
				__( 'DPD UK accepts only GBP currency. Should you require any further information please contact us directly at %1$shttps://flexibleshipping.com/support/%2$s.', 'woocommerce-dpd-uk' ),
				'<a href="https://flexibleshipping.com/support/" target="_blank">',
				'</a>'
			);
		}
		parent::__construct( $message, $code, $previous );
	}

}
