<?php

namespace WcPay360\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ajax Loader Class
 *
 * @since  2.2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Ajax_Loader {
	
	/**
	 * Registers all Ajax classes and initializes them.
	 */
	public function register() {
		$classes = array(
			'admin_ajax'        => '\\WcPay360\\Ajax\\Admin\\Ajax',
		);
		
		foreach ( $classes as $prop => $class ) {
			$this->{$prop} = new $class;
			$this->{$prop}->hooks();
		}
	}
}