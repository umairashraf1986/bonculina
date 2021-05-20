<?php
/**
 * Plugin Name: Enable/Disable DPD UK Shipping
 * Description: Custom plugin to enable or disable DPD UK shipping
 * Version: 1.0.0
 * Author: Ciklum Pvt. Ltd.
 * Author URI: https://www.ciklum.com/
 * License: proprietary
 */

// if ( is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('woocommerce-dpd-uk/woocommerce-dpd-uk.php') && ! class_exists( 'WPDesk_WooCommerce_DPD_UK_Extension' ) ) {

// 	$pluginClass = plugin_dir_path( __DIR__ ) . 'woocommerce-dpd-uk/classes/class-woocommerce-dpd-uk.php';
//     if ( file_exists( $pluginClass ) ) {
//         include( $pluginClass );
//     }

// 	class WPDesk_WooCommerce_DPD_UK_Extension {
// 		public function __construct() {
// 			$this->addEnableDisableShippingOption();
// 			$instance = WPDesk_WooCommerce_DPD_UK::$instance;
// 			$pluginlog = plugin_dir_path(__FILE__).'debug.log';
// 			ob_start();
// 			echo '<pre>';
// 			print_r($instance);
// 			echo '</pre>';
// 			$objectdata = ob_get_contents();
// 			ob_end_clean();
// 			error_log($objectdata, 3, $pluginlog);
// 			remove_action( 'woocommerce_order_status_changed', array( $instance, 'woocommerce_order_status_changed' ), 10, 3 );
// 			add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );
// 		}

// 		public function addEnableDisableShippingOption() {
// 			add_filter('woocommerce_get_sections_shipping', array($this, 'enableDisableShippingTab'));

// 			add_filter('woocommerce_get_settings_shipping', array($this, 'enableDisableShippingTabSettings'), 10, 2);
// 		}

// 		public function enableDisableShippingTab($sections)	{
// 			$sections['dpd_shipping_option'] = __('Enable/Disable DPD shipping', 'woocommerce');

// 			return $sections;
// 		}

// 		public function enableDisableShippingTabSettings($settings, $currentSection) {
// 			if ($currentSection == 'dpd_shipping_option') {
// 				$postCodesSettings = [];
//             	// Add Title to the Settings
// 				$postCodesSettings[] = [
// 					'name' => __('Enable/Disable DPD UK shipping', 'woocommerce'),
// 					'type' => 'title',
// 					'desc' => __(''),
// 					'id' => 'dpd-shipping-option'
// 				];
//             	// Add first checkbox option
// 				$postCodesSettings[] = array(
// 					'name' => __('Enable DPD UK Shipping', 'text-domain'),
// 	                'desc_tip' => '',
// 	                'id' => 'dpdShippingOption',
// 	                'type' => 'checkbox',
// 	                'default' => 'yes',
// 	                'css' => 'min-width:300px;min-height:300px',
// 	                'desc' => ''
// 	            );

// 				$postCodesSettings[] = array('type' => 'sectionend', 'id' => 'dpd-shipping-option');
// 				return $postCodesSettings;

// 	            /**
// 	             * If not, return the standard settings
// 	             **/
// 	        } else {
// 	        	return $settings;
// 	        }

// 	    }

// 		public function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
// 			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
// 			$dpd_uk = $all_shipping_methods['dpd_uk'];
// 			$settings = $dpd_uk->settings;
// 			if ( isset( $settings['auto_create'] ) && $settings['auto_create'] == 'auto' ) {
// 				if ( isset( $settings['order_status'] ) && 'wc-' . $new_status == $settings['order_status'] ) {
// 					$order = wc_get_order( $order_id );
// 					$shipments = fs_get_order_shipments( $order_id, 'dpd_uk' );
// 					// foreach ( $shipments as $shipment ) {
// 						try {
// 							$pluginlog = plugin_dir_path(__FILE__).'debug.log';
// 							ob_start();
// 							echo '<pre>';
// 							print_r($shipments);
// 							echo '</pre>';
// 							$objectdata = ob_get_contents();
// 							ob_end_clean();
// 							error_log($objectdata, 3, $pluginlog);
// 							// $shipment->api_create();
// 						}
// 						catch ( Exception $e ) {}
// 					// }
// 				}
// 			}
// 		}
// 	}
// 	new \WPDesk_WooCommerce_DPD_UK_Extension();
// }