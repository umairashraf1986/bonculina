<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_WooCommerce_DPD_UK_API_Data' ) ) {

	class WPDesk_WooCommerce_DPD_UK_API_Data_DPD implements WPDesk_WooCommerce_DPD_UK_API_Data_Interface {

		const API_HOST_DPD = 'https://api.dpd.co.uk';
		const TRACKING_URL_DPD = 'https://www.dpd.co.uk/apps/tracking/?reference=%s&postcode=%s';

		/**
		 * Return API URL
		 *
		 * @return string
		 */
		public function get_api_url() {
			return self::API_HOST_DPD;
		}

		/**
		 * Services available in GB
		 *
		 * @return array
		 */
		public function get_services_for_gb() {
			return array(
				'1^01'   => __( 'PARCEL SUNDAY', 'woocommerce-dbd-uk' ),
				'1^06'   => __( 'FREIGHT PARCEL SUNDAY', 'woocommerce-dbd-uk' ),
				'1^08'   => __( 'PALLET SUNDAY', 'woocommerce-dbd-uk' ),
				'1^09'   => __( 'EXPRESSPAK SUNDAY', 'woocommerce-dbd-uk' ),
				'1^11'   => __( 'DPD TWO DAY', 'woocommerce-dbd-uk' ),
				'1^12'   => __( 'DPD NEXT DAY', 'woocommerce-dbd-uk' ),
				'1^13'   => __( 'DPD 12:00', 'woocommerce-dbd-uk' ),
				'1^14'   => __( 'DPD 10:30', 'woocommerce-dbd-uk' ),
				'1^16'   => __( 'PARCEL SATURDAY', 'woocommerce-dbd-uk' ),
				'1^17'   => __( 'PARCEL SATURDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^18'   => __( 'PARCEL SATURDAY 10:30', 'woocommerce-dbd-uk' ),
				//'1^22'   => __( 'PARCEL RETURN TO SHOP', 'woocommerce-dbd-uk' ),
				'1^29'   => __( 'PARCEL SUNDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^31'   => __( 'FREIGHT PARCEL SUNDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^32'   => __( 'EXPRESSPAK DPD NEXT DAY', 'woocommerce-dbd-uk' ),
				'1^33'   => __( 'EXPRESSPAK DPD 12:00', 'woocommerce-dbd-uk' ),
				'1^34'   => __( 'EXPRESSPAK DPD 10:30', 'woocommerce-dbd-uk' ),
				'1^36'   => __( 'EXPRESSPAK SATURDAY', 'woocommerce-dbd-uk' ),
				'1^37'   => __( 'EXPRESSPAK SATURDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^38'   => __( 'EXPRESSPAK SATURDAY 10:30', 'woocommerce-dbd-uk' ),
				'1^51'   => __( 'EXPRESSPAK SUNDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^69'   => __( 'PALLET SUNDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^71'   => __( 'PALLET DPD TWO DAY', 'woocommerce-dbd-uk' ),
				'1^72'   => __( 'PALLET DPD NEXT DAY', 'woocommerce-dbd-uk' ),
				'1^73'   => __( 'PALLET DPD 12:00', 'woocommerce-dbd-uk' ),
				'1^74'   => __( 'PALLET DPD 10:30', 'woocommerce-dbd-uk' ),
				'1^76'   => __( 'PALLET SATURDAY', 'woocommerce-dbd-uk' ),
				'1^77'   => __( 'PALLET SATURDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^78'   => __( 'PALLET SATURDAY 10:30', 'woocommerce-dbd-uk' ),
				'1^81'   => __( 'FREIGHT PARCEL DPD TWO DAY', 'woocommerce-dbd-uk' ),
				'1^82'   => __( 'FREIGHT PARCEL DPD NEXT DAY', 'woocommerce-dbd-uk' ),
				'1^83'   => __( 'FREIGHT PARCEL DPD 12:00', 'woocommerce-dbd-uk' ),
				'1^84'   => __( 'FREIGHT DPD 10:30', 'woocommerce-dbd-uk' ),
				'1^86'   => __( 'FREIGHT PARCEL SATURDAY', 'woocommerce-dbd-uk' ),
				'1^87'   => __( 'FREIGHT PARCEL SATURDAY 12:00', 'woocommerce-dbd-uk' ),
				'1^88'   => __( 'FREIGHT PARCEL SATURDAY 10:30', 'woocommerce-dbd-uk' ),
				//'1^91'   => __( 'PARCEL SHIP TO SHOP', 'woocommerce-dbd-uk' ),
				//'1^98'   => __( 'Expak - Pickup Classic', 'woocommerce-dbd-uk' ),
			);
		}

		/**
		 * Services available in EU
		 *
		 * @return array
		 */
		public function get_services_for_eu() {
			return array(
				'1^19'  => __( 'Parcel, DPD Classic', 'woocommerce-dbd-uk' ),
				'1^39'  => __( 'Expresspak, DPD Classic', 'woocommerce-dbd-uk' ),
				'1^50'  => __( 'Air Express', 'woocommerce-dbd-uk' ),
				'1^60'  => __( 'Air Classic', 'woocommerce-dbd-uk' ),
			);
		}

		/**
		 * Services available in World
		 *
		 * @return array
		 */
		public function get_services_for_world() {
			return array(
				'1^50' => __( 'Air Express', 'woocommerce-dbd-uk' ),
				'1^60'  => __( 'Air Classic', 'woocommerce-dbd-uk' ),
			);
		}

		/**
		 * Tracking URL
		 *
		 * @return string
		 */
		public function get_tracking_url() {
			return self::TRACKING_URL_DPD;
		}

	}

}