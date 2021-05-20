<?php

namespace WcPay360\Api\Cashier;

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
interface Cashier_Process_Interface {

	/**
	 * Hosted_Cashier constructor.
	 *
	 * @param Cashier_Service $service
	 */
	public function __construct( Cashier_Service $service );

	public function get_resource_endpoint();
}