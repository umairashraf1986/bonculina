<?php namespace WcPay360\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Factories {
	
	/**
	 * Returns the gateway class by looking through the available gateways and matching the gateway by ID
	 *
	 * @param $id
	 *
	 * @return bool|\WC_Payment_Gateway|\WC_Pay360_Gateway_Addons
	 */
	public static function get_gateway( $id ) {
		foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {
			if ( $id == $gateway->id ) {
				return $gateway;
			}
		}
		
		return false;
	}
}