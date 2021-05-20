<?php
/**
 * Unifaun Online plug-in entry point
 *
 * Plugin Name: Mediastrategi Unifaun Online Shipping
 * Description: Custom Shipping Method for WooCommerce
 * Version: 1.3.13
 * Author: Mediastrategi Sverige AB
 * Author URI: https://www.mediastrategi.se/
 * License: proprietary
 * Domain Path: /languages
 * Text Domain: msunifaunonline
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once(ABSPATH . 'wp-admin/includes/plugin.php');

require_once(dirname(__FILE__) . '/cache.php');
require_once(dirname(__FILE__) . '/class.php');
require_once(dirname(__FILE__) . '/order.php');
require_once(dirname(__FILE__) . '/session.php');
require_once(dirname(__FILE__) . '/wordpress.php');

if (!function_exists('unifaun_online_wordpress_cant_find_woocommerce')) {
    function unifaun_online_wordpress_cant_find_woocommerce()
    {
        echo '<div class="notice notice-error"><p><strong>' . Mediastrategi_UnifaunOnline::METHOD_TITLE . ' ERROR:</strong> '
            . __( "Can't find WooCommerce, is it installed and activated?", 'msunifaunonline')
            . '</p></div>';
    }
}

// Check if WooCommerce is active (requires Wordpress 2.5.0)
$needle = 'woocommerce/woocommerce.php';
if (is_plugin_active($needle)) {
    require_once(dirname(__FILE__) . '/woocommerce.php');
    require_once(dirname(__FILE__) . '/shipping-method.php');
    Mediastrategi_UnifaunOnline::getWoocommerce();
    Mediastrategi_UnifaunOnline::getWordpress();
} else {
    add_action(
        'admin_notices',
        'unifaun_online_wordpress_cant_find_woocommerce'
    );
}

// Add update functionality to admin
if (is_admin()) {
    $pluginInfo = get_plugin_data(
        __FILE__,
        false,
        false
    );
    require_once(dirname(__FILE__) . '/update.php');
    new Mediastrategi_UnifaunOnline_Update(array(
        'archiveUrl' => Mediastrategi_UnifaunOnline::UPDATE_ARCHIVE_URL,
        'httpBasicAuthUsername' => Mediastrategi_UnifaunOnline::getOption('update_username'),
        'httpBasicAuthPassword' => Mediastrategi_UnifaunOnline::getOption('update_password'),
        'infoUrl' => Mediastrategi_UnifaunONLINE::UPDATE_INFO_URL,
        'name' => sprintf(
            '%s/%s',
            basename(dirname(__FILE__)),
            basename(__FILE__)
        ),
        'slug' => basename(dirname(__FILE__)),
        'version' => $pluginInfo['Version'],
    ));
}
