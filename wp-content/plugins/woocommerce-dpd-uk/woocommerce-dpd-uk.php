<?php
/**
 * Plugin Name: WooCommerce DPD UK
 * Plugin URI: https://flexibleshipping.com/products/dpd-uk-dpd-local-woocommerce/
 * Description: WooCommerce DPD UK integration.
 * Version: 1.7.7
 * Author: WP Desk
 * Author URI: https://www.wpdesk.net/
 * Text Domain: woocommerce-dpd-uk
 * Domain Path: /languages/
 * Requires at least: 4.9
 * Tested up to: 5.6
 * WC requires at least: 4.5
 * WC tested up to: 4.9
 * Requires PHP: 7.0
 *
 * Copyright 2017 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package WooCommerce DPD UK
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '1.7.7';

$plugin_name        = 'WooCommerce DPD UK';
$product_id         = 'WooCommerce DPD UK';
$plugin_class_name  = 'WPDesk_WooCommerce_DPD_UK_Plugin';
$plugin_text_domain = 'woocommerce-dpd-uk';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

define( $plugin_class_name, $plugin_version );
define( 'WOOCOMMERCE_DPD_UK_VERSION', $plugin_version );

$requirements = array(
	'php'          => '7',
	'wp'           => '4.5',
	'repo_plugins' => array(
		array(
			'name'      => 'flexible-shipping/flexible-shipping.php',
			'nice_name' => 'Flexible Shipping',
			'version'   => '3.7',
		),
		array(
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '3.5',
		),
	),
);

require_once( plugin_basename( 'inc/wpdesk-woo27-functions.php' ) );
require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow/src/plugin-init-php52.php';
