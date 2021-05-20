<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_WooCommerce_DPD_UK_API_Data_Factory' ) ) {

	class WPDesk_WooCommerce_DPD_UK_API_Data_Factory {

		/**
		 * @param string $api_type
		 *
		 * @return WPDesk_WooCommerce_DPD_UK_API_Data_Interface
		 */
		public static function get_api_data_for_api_type( $api_type ) {
			if ( $api_type == WPDesk_WooCommerce_DPD_UK_API::API_TYPE_DPD_LOCAL ) {
				return new WPDesk_WooCommerce_DPD_UK_API_Data_DPD_Local();
			}
			else {
				return new WPDesk_WooCommerce_DPD_UK_API_Data_DPD();
			}
		}

	}

}