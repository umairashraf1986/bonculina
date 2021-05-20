<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.1.3
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Debug {
	
	/**
	 * Is debug mode enabled
	 * @var bool
	 */
	private static $is_debug_enabled;
	/**
	 * WC Logger object
	 * @var object
	 */
	private static $log;
	
	/**
	 * Check, if debug logging is enabled
	 *
	 * @since 2.1.3
	 * @return bool
	 */
	public static function is_debug_enabled() {
		if ( self::$is_debug_enabled ) {
			return self::$is_debug_enabled;
		} else {
			$settings = get_option( 'woocommerce_pay360_settings' );
			
			self::$is_debug_enabled = isset( $settings['debug'] ) && 'yes' == $settings['debug'];
			
			return self::$is_debug_enabled;
		}
	}
	
	/**
	 * Add debug log message
	 *
	 * @since 2.1.3
	 *
	 * @param string $message
	 * @param string $handle The handle of the log file
	 * @param string $level  Level of severity: emergency|alert|critical|error|warning|notice|info|debug
	 *
	 * @return bool
	 */
	public static function add_debug_log( $message, $handle = 'pay360', $level = 'debug' ) {
		
		if ( ! self::is_debug_enabled() ) {
			return false;
		}
		
		if ( ! is_object( self::$log ) ) {
			self::$log = WC_Pay360_Compat::get_wc_logger();
		}
		
		if ( WC_Pay360_Compat::is_wc_3_0() ) {
			if ( ! \WC_Log_Levels::is_valid_level( $level ) ) {
				$level = 'debug';
			}
			self::$log->log( $level, $message, array( 'source' => $handle ) );
		} else {
			if ( self::is_debug_enabled() ) {
				self::$log->add( $handle, $message );
			}
		}
	}
}