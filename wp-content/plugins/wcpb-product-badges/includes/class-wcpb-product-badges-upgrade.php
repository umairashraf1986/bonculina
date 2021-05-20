<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPB_Product_Badges_Upgrade' ) ) {

	class WCPB_Product_Badges_Upgrade {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'upgrade' ) ); // wp_loaded used as means plugin classes can be accessed

		}

		public function upgrade() {

			$version = get_option( 'wcpb_product_badges_version' );

			// If version defined in options is not the latest version or is empty as a new install or version older than wcrp_rental_products_version option

			if ( WCPB_PRODUCT_BADGES_VERSION !== $version ) {

				global $wpdb;

				// if ( $version < '0.0.0' ) {

					// Placeholder for future conditions

				// }

				update_option( 'wcpb_product_badges_version', WCPB_PRODUCT_BADGES_VERSION );

			}

		}

	}

}
