<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( !interface_exists( 'WPDesk_WooCommerce_DPD_UK_API_Data_Interface' ) ) {
	interface WPDesk_WooCommerce_DPD_UK_API_Data_Interface {

		public function get_api_url();

		public function get_services_for_gb();

		public function get_services_for_eu();

		public function get_services_for_world();

		public function get_tracking_url();

	}
}