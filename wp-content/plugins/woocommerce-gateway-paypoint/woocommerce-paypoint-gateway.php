<?php
/*
 * Plugin Name: WooCommerce Pay360 (PayPoint) Gateway
 * Plugin URI: http://www.woothemes.com/products/paypoint/
 * Description: Allows you to use <a href="https://www.pay360.com/">Pay360 (PayPoint)</a> payment gateway with the WooCommerce plugin.
 * Version: 2.2.1
 * Author: VanboDevelops
 * Author URI: http://www.vanbodevelops.com
 * Woo: 18698:6895043abb36a50a969f564452eed060
 * WC requires at least: 2.6.0
 * WC tested up to: 4.7.0
 * Text Domain: woocommerce-gateway-pay360
 * Domain Path: /languages
 *
 *	Copyright: (c) 2012 - 2020 VanboDevelops
 *	License: GNU General Public License v3.0
 *	License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '6895043abb36a50a969f564452eed060', '18698' );

if ( ! is_woocommerce_active() ) {
	return;
}

class WC_Pay360 {
	
	/**
	 * Text domain string. Constant
	 */
	const TEXT_DOMAIN = 'woocommerce-gateway-pay360';
	/**
	 * The plugin version
	 */
	const VERSION = '2.2.1';
	/**
	 * The files and folders version
	 * Should be changes every time there is a new class file added or one deleted
	 * @since 2.0
	 */
	const FILES_VERSION = '1.1.1';
	/**
	 * Plugin URL
	 * @var string
	 */
	private static $plugin_url;
	/**
	 * Plugin Path
	 * @var string
	 */
	private static $plugin_path;
	private static $is_pre_orders_active;
	private static $is_subscriptions_active;
	public $scripts;
	public $admin;
	/**
	 * The single instance of the class.
	 *
	 * @var \WC_Pay360
	 * @since 2.2.0
	 */
	protected static $_instance = null;
	
	/**
	 * Returns main instance of the class
	 * @since 2.2.0
	 * @return \WC_Pay360
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * @since 2.2.0
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Not allowed!', self::TEXT_DOMAIN ), '2.2.0' );
	}
	
	/**
	 * @since 2.2.0
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Not allowed!', self::TEXT_DOMAIN ), '2.2.0' );
	}
	
	public function __construct() {
		
		// Add required files
		$this->load_autoloader();
		
		$this->update_procedure();
		
		add_action( 'init', array( $this, 'on_init' ) );
		
		add_action( 'init', array( $this, 'cancel_url_response_check' ) );
		
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
		
		// Add a 'Settings' link to the plugin action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array(
			$this,
			'settings_support_link'
		), 10, 4 );
	}
	
	public function load_autoloader() {
		require_once( 'includes/class-wc-pay360-autoloader.php' );
		
		$loader = new WC_Pay360_Autoloader( self::plugin_path(), self::FILES_VERSION );
		
		spl_autoload_register( array( $loader, 'load_classes' ) );
	}
	
	/**
	 * Runs the update procedure
	 *
	 * @since 2.0
	 */
	public function update_procedure() {
		$update = new WC_Pay360_Updates();
		$update->hooks();
	}
	
	/**
	 * @since 2.2.0
	 */
	public function on_init() {
		$this->load_text_domain();
		$this->load_scripts();
		
		if ( is_admin() ) {
			$this->load_admin();
		}
	}
	
	/**
	 * @since 2.2.0
	 */
	public function load_scripts() {
		$this->scripts = new \WcPay360\Scripts();
		$this->scripts->hooks();
	}
	
	/**
	 * @since 2.2.0
	 */
	public function load_admin() {
		$this->admin = new \WcPay360\Admin\Admin_Loader();
	}
	
	/**
	 * Localisation
	 **/
	public function load_text_domain() {
		load_plugin_textdomain( self::TEXT_DOMAIN, false, self::plugin_path() . '/languages/' );
	}
	
	/**
	 * Redirect the Customer to the Cancel order page.
	 * Used for iframe only, to escape from the iframe and return the customer to the main window.
	 *
	 * @since 2.0
	 */
	function cancel_url_response_check() {
		if ( false != self::get_field( 'cancel_order', $_GET, false )
		     && false != self::get_field( 'order', $_GET, false )
		     && false != self::get_field( 'order_id', $_GET, false )
		     && false != self::get_field( 'pay360-hosted-cashier-return-cancel', $_GET, false )
		) {
			$order = wc_get_order( (int) self::get_field( 'order_id', $_GET ) );
			
			$redirect_url = $order->get_cancel_order_url_raw();
			
			wc_get_template(
				'pay360/iframe-break.php',
				array(
					'redirect_url' => $redirect_url,
				),
				'',
				WC_Pay360::plugin_path() . '/templates/'
			);
			exit;
		}
	}
	
	/**
	 * Add 'Settings' link to the plugin actions links
	 *
	 * @since 1.1
	 * @return array associative array of plugin action links
	 */
	public function settings_support_link( $actions, $plugin_file, $plugin_data, $context ) {
		
		$gateway = self::get_gateway_class();
		if ( WC_Pay360_Compat::is_wc_2_6() ) {
			$gateway = 'pay360';
		}
		
		return array_merge(
			array(
				'settings' => '<a href="' . WC_Pay360_Compat::gateway_settings_page( $gateway ) . '">' . __( 'Settings', self::TEXT_DOMAIN ) . '</a>',
				'support'  => '<a href="https://docs.woocommerce.com/">' . __( 'Support', self::TEXT_DOMAIN ) . '</a>',
			),
			$actions
		);
	}
	
	/**
	 * Get the correct gateway class name to load
	 *
	 * @since 1.1
	 * @return string Class name
	 */
	public static function get_gateway_class() {
		if ( self::is_subscriptions_active() || self::is_pre_orders_active() ) {
			$methods = 'WC_Pay360_Gateway_Addons';
		} else {
			$methods = 'WC_Pay360_Gateway';
		}
		
		return $methods;
	}
	
	/**
	 * Safely retrieve an array or object key/property
	 *
	 * @since 2.0
	 *
	 * @param string $name    Name of the key/prop
	 * @param array  $stack   The stack we are looking in
	 * @param string $default Variable name
	 *
	 * @return mixed The variable value
	 */
	public static function get_field( $name, $stack, $default = '' ) {
		
		if ( is_array( $stack ) ) {
			if ( isset( $stack[ $name ] ) ) {
				return $stack[ $name ];
			}
		}
		
		if ( is_object( $stack ) ) {
			if ( isset( $stack->{$name} ) ) {
				return $stack->{$name};
			}
		}
		
		return $default;
	}
	
	/**
	 * Safely get POST variables
	 *
	 * @since 1.1
	 *
	 * @param string $name POST variable name
	 *
	 * @return string The variable value
	 */
	public static function get_post( $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			return $_POST[ $name ];
		}
		
		return null;
	}
	
	/**
	 * Safely get GET variables
	 *
	 * @since 1.1
	 *
	 * @param string $name GET variable name
	 *
	 * @return string The variable value
	 */
	public static function get_get( $name ) {
		if ( isset( $_GET[ $name ] ) ) {
			return $_GET[ $name ];
		}
		
		return null;
	}
	
	/**
	 * Add the gateway to WooCommerce
	 *
	 * @since 1.1
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	public function add_gateway( $methods ) {
		$methods[] = self::get_gateway_class();
		
		return $methods;
	}
	
	/**
	 * Add debug log message
	 *
	 * @deprecated Will be removed in 2019
	 *
	 * @since      1.1
	 *
	 * @param string $message
	 * @param string $handle The handle of the log file
	 * @param string $level  Level of severity: emergency|alert|critical|error|warning|notice|info|debug
	 */
	public static function add_debug_log( $message, $handle = 'pay360', $level = 'debug' ) {
		WC_Pay360_Debug::add_debug_log( $message, $handle, $level );
	}
	
	/**
	 * Check, if debug logging is enabled
	 *
	 * @deprecated Will be removed in 2019
	 *
	 * @since      1.2
	 *
	 * @return bool
	 */
	public static function is_debug_enabled() {
		return WC_Pay360_Debug::is_debug_enabled();
	}
	
	/**
	 * Get the plugin url
	 *
	 * @since 1.1
	 * @return string
	 */
	public static function plugin_url() {
		if ( self::$plugin_url ) {
			return self::$plugin_url;
		}
		
		return self::$plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	
	/**
	 * Get the plugin path
	 *
	 * @since 1.1
	 * @return string
	 */
	public static function plugin_path() {
		if ( self::$plugin_path ) {
			return self::$plugin_path;
		}
		
		return self::$plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	/**
	 * Return the order number with stripped # or n° ( french translations )
	 *
	 * @since 1.1
	 *
	 * @param WC_Order $order
	 *
	 * @return string
	 */
	public static function get_order_number( WC_Order $order ) {
		return str_replace( array( '#', 'n°' ), '', $order->get_order_number() );
	}
	
	/**
	 * Detect if Pre-Orders is active
	 *
	 * @since 2.1
	 * @return bool True if active, False if not
	 */
	public static function is_pre_orders_active() {
		if ( is_bool( self::$is_pre_orders_active ) ) {
			return self::$is_pre_orders_active;
		}
		
		self::$is_pre_orders_active = false;
		
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			self::$is_pre_orders_active = true;
		}
		
		return self::$is_pre_orders_active;
	}
	
	/**
	 * Detect if WC Subscriptions is active
	 *
	 * @since 2.1
	 * @return bool True if active, False if not
	 */
	public static function is_subscriptions_active() {
		if ( is_bool( self::$is_subscriptions_active ) ) {
			return self::$is_subscriptions_active;
		}
		
		self::$is_subscriptions_active = false;
		
		if ( class_exists( 'WC_Subscriptions' ) || function_exists( 'wcs_order_contains_subscription' ) ) {
			self::$is_subscriptions_active = true;
		}
		
		return self::$is_subscriptions_active;
	}
}

/**
 * Load the plugin main class
 */
add_action( 'plugins_loaded', 'wc_pay360_load_plugin', 10 );
function wc_pay360_load_plugin() {
	WC_Pay360::instance();
}