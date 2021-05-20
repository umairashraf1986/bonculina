<?php

namespace WcPay360\Api\Errors;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface to handle loading of all error code/message handlers
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
interface Errors_Interface {

	/**
	 * Hosted_Cashier constructor.
	 *
	 * @param object $response
	 */
	public function __construct( $response );

	/**
	 * Returns the error code
	 *
	 * @return mixed
	 */
	public function get_code();

	/**
	 * Returns the message corresponding to the error code
	 *
	 * @return mixed
	 */
	public function get_message();
}