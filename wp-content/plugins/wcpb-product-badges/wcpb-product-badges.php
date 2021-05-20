<?php

/**
 * Plugin Name: Product Badges
 * Plugin URI: https://woocommerce.com/products/product-badges/
 * Description: Add promotional badges to products in your WooCommerce store.
 * Version: 2.0.0
 * Author: 99w
 * Author URI: https://99w.co.uk
 * Developer: 99w
 * Developer URI: https://99w.co.uk
 * Text Domain: wcpb-product-badges
 * Domain Path: /languages
 *
 * Woo: 6662686:47f602c9beac3790024f9e7c7f2b5e7e
 * WC requires at least: 4.2.0
 * WC tested up to: 4.8.0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPB_Product_Badges' ) ) {

	define( 'WCPB_PRODUCT_BADGES_VERSION', '2.0.0' );
	define( 'WCPB_PRODUCT_BADGES_BADGES_PATH', __DIR__ . '/badges/' );
	define( 'WCPB_PRODUCT_BADGES_BADGES_URL', plugin_dir_url( WCPB_PRODUCT_BADGES_BADGES_PATH ) . 'badges/' );

	load_plugin_textdomain( 'wcpb-product-badges', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	class WCPB_Product_Badges {

		public function __construct() {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Ensures is_plugin_active() can be used here
			
			require_once( __DIR__ . '/includes/class-wcpb-product-badges-upgrade.php' );
			new WCPB_Product_Badges_Upgrade();

			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { // If WooCommerce is active, works for standalone and multisite network

				require_once( __DIR__ . '/includes/class-wcpb-product-badges-admin.php' );
				require_once( __DIR__ . '/includes/class-wcpb-product-badges-public.php' );

				new WCPB_Product_Badges_Admin();
				new WCPB_Product_Badges_Public();

			}

		}

	}

	new WCPB_Product_Badges();

}
