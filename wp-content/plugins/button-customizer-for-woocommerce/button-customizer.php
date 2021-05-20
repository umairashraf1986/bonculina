<?php
/**
 * Plugin Name:       Custom Buttons for WooCommerce
 * Description:       Change the add to cart buttons for all product types, settings under WooCommerce sidebar.
 * Version:           1.2.1
 * Author:            Puri.io
 * Author URI:        https://puri.io/
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function bcfw_init() {
	// Check if WooCommerce is active.
	if ( class_exists( 'WooCommerce' ) ) {

		require_once dirname( __FILE__ ) . '/api/class-settings-api.php';

		require_once dirname( __FILE__ ) . '/settings/settings.php';

		require_once dirname( __FILE__ ) . '/functions/functions.php';

		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bcfw_settings_quick_link_plugin_page' );

		// Load our settings.
		new Button_Customizer_Admin_Settings();

	} else {
		add_action( 'admin_notices', 'bcfw_need_woocommerce_installed' );
	}

}

add_action( 'plugins_loaded', 'bcfw_init' );


function bcfw_need_woocommerce_installed() {
	?>
<div class="notice notice-success">
	<p><?php _e( 'Button Customizer for WooCommerce requires WooCommerce to be installed!', 'button-customizer' ); ?></p>
</div>
	<?php
}
