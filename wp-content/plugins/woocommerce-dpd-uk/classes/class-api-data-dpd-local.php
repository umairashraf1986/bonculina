<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_WooCommerce_DPD_UK_API_Data' ) ) {

	class WPDesk_WooCommerce_DPD_UK_API_Data_DPD_Local implements WPDesk_WooCommerce_DPD_UK_API_Data_Interface {

		const API_HOST_DPD_LOCAL = 'https://api.interlinkexpress.com';
		const TRACKING_URL_DPD_LOCAL = 'https://www.dpdlocal.co.uk/apps/tracking/?reference=%s&postcode=%s';

		/**
		 * API URL
		 *
		 * @return string
		 */
		public function get_api_url() {
			return self::API_HOST_DPD_LOCAL;
		}

		/**
		 * Services available in GB
		 *
		 * @return array
		 */
		public function get_services_for_gb() {
			return array(
				'2^68' => __('Expak 1 Next Day', 'woocommerce-dpd-uk' ),
				'2^04' => __('Expak 1 By 1030', 'woocommerce-dpd-uk' ),
				'2^03' => __('Expak 1 By 12', 'woocommerce-dpd-uk' ),
				'2^72' => __('Expak 1 Saturday', 'woocommerce-dpd-uk' ),
				'2^06' => __('Expak 1 Sat By 1030', 'woocommerce-dpd-uk' ),
				'2^05' => __('Expak 1 Sat By 12', 'woocommerce-dpd-uk' ),
				'2^76' => __('Expak 1 Sunday', 'woocommerce-dpd-uk' ),
				'2^47' => __('Expak 1 Sun By 12', 'woocommerce-dpd-uk' ),
				'2^12' => __('Parcel Next Day', 'woocommerce-dpd-uk' ),
				'2^08' => __('Parcel By 1030', 'woocommerce-dpd-uk' ),
				'2^13' => __('Parcel By 12', 'woocommerce-dpd-uk' ),
				'2^71' => __('Parcel Saturday', 'woocommerce-dpd-uk' ),
				'2^09' => __('Parcel Sat By 1030', 'woocommerce-dpd-uk' ),
				'2^17' => __('Parcel Sat By 12', 'woocommerce-dpd-uk' ),
				'2^75' => __('Parcel Sunday', 'woocommerce-dpd-uk' ),
				'2^15' => __('Parcel Sun By 12', 'woocommerce-dpd-uk' ),
				'2^32' => __('Expak 5 Next Day', 'woocommerce-dpd-uk' ),
				'2^28' => __('Expak 5 By 1030', 'woocommerce-dpd-uk' ),
				'2^33' => __('Expak 5 By 12', 'woocommerce-dpd-uk' ),
				'2^73' => __('Expak 5 Saturday', 'woocommerce-dpd-uk' ),
				'2^29' => __('Expak 5 Sat By 1030', 'woocommerce-dpd-uk' ),
				'2^37' => __('Expak 5 Sat By 12', 'woocommerce-dpd-uk' ),
				'2^77' => __('Expak 5 Sunday', 'woocommerce-dpd-uk' ),
				'2^51' => __('Expak 5 Sun By 12', 'woocommerce-dpd-uk' ),
				'2^82' => __('Freight Next Day', 'woocommerce-dpd-uk' ),
				'2^65' => __('Freight By 1030', 'woocommerce-dpd-uk' ),
				'2^83' => __('Freight By 12', 'woocommerce-dpd-uk' ),
				'2^74' => __('Freight Saturday', 'woocommerce-dpd-uk' ),
				'2^69' => __('Freight Sat By 1030', 'woocommerce-dpd-uk' ),
				'2^87' => __('Freight Sat By 12', 'woocommerce-dpd-uk' ),
				'2^78' => __('Freight Sunday', 'woocommerce-dpd-uk' ),
				'2^45' => __('Freight Sun By 12', 'woocommerce-dpd-uk' ),
				'2^11' => __( 'DPD TWO DAY', 'woocommerce-dbd-uk' ),
				//'2^22' => __('Parcel Return to Shop', 'woocommerce-dpd-uk' ),
				//'2^91' => __('Parcel Ship to Shop', 'woocommerce-dpd-uk' ),
			);
		}

		/**
		 * Services available in EU
		 *
		 * @return array
		 */
		public function get_services_for_eu() {
			return array(
				'2^19' => __( 'Parcel - DPD Europe By Road', 'woocommerce-dbd-uk' ),
				'2^50' => __( 'Air Express', 'woocommerce-dbd-uk' ),
				'2^60' => __( 'Air Classic', 'woocommerce-dbd-uk' ),
				//'2^99' => __( 'Parcel - Pickup Classic', 'woocommerce-dbd-uk' ),
			);
		}

		/**
		 * Services available in World
		 *
		 * @return array
		 */
		public function get_services_for_world() {
			return array(
				'2^50' => __( 'Air Express', 'woocommerce-dbd-uk' ),
				'2^60' => __( 'Air Classic', 'woocommerce-dbd-uk' ),
			);
		}

		/**
		 * Tracking URL
		 *
		 * @return string
		 */
		public function get_tracking_url() {
			return self::TRACKING_URL_DPD_LOCAL;
		}


	}

}
