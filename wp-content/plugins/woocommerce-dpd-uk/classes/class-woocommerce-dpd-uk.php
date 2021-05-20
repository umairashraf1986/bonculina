<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_WooCommerce_DPD_UK' ) ) {
	class WPDesk_WooCommerce_DPD_UK {

		const OPTION_NAME_DPD_REQUEST_COUNT = 'woocommerce_dpd_uk_get_request_count';

		const METHOD_INTEGRATION = 'dpd_uk';

		static $instance = null;

		private $plugin = null;

		public static function get_instance( WPDesk_WooCommerce_DPD_UK_Plugin $plugin ) {
			if ( self::$instance == null ) {
				self::$instance = new self( $plugin );
			}
			return self::$instance;
		}

		/**
		 * @param WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment
		 *
		 * @return array
		 * @throws Exception
		 */
		public function get_services_for_shipment( $shipment ) {
			$order = $shipment->get_order();
			/** @var WPDesk_WooCommerce_DPD_UK_API $api */
			$api = $this->get_api();
			$shipping_network = $api->shipping_network(
				wpdesk_get_order_meta( $order, '_shipping_country', true ),
				wpdesk_get_order_meta( $order, '_shipping_state', true ),
				wpdesk_get_order_meta( $order, '_shipping_postcode', true )
			);
			$options = array();
			foreach ( $shipping_network->data as $shipping_network_data ) {
				$options[$shipping_network_data->network->networkCode] = $shipping_network_data->network->networkDescription;
			}
			return $options;
		}

		/**
		 * @return array
		 */
		public static function get_package_types() {
			return array(
					'ENVELOPE' 		=> __( 'Koperta', 'woocommerce-dpd-uk' ),
					'PACKAGE'		=> __( 'Paczka', 'woocommerce-dpd-uk' ),
					'PALLET'		=> __( 'Paleta', 'woocommerce-dpd-uk' ),
			);
		}

		public function __construct( WPDesk_WooCommerce_DPD_UK_Plugin $plugin ) {
			$this->plugin = $plugin;
			$this->hooks();
		}

		public function hooks() {
			add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ), 20, 1 );

			add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );

			add_action( 'flexible_shipping_shipment_confirmed', array( $this, 'flexible_shipping_shipment_confirmed' ), 10, 2 );

			add_action( 'flexible_shipping_add_shipping_options', array( $this, 'flexible_shipping_add_shipping_options' ) );

			add_action( 'woocommerce_review_order_after_shipping', array( $this, 'woocommerce_review_order_after_shipping' ) );

			add_action( 'admin_init', array( $this, 'admin_init_get_dpd_uk_request' ) );

			add_action( 'woocommerce_init', array( $this, 'init_services_verifier' ) );
		}

		/**
		 * .
		 */
		public function init_services_verifier() {
			WC()->initialize_session();
			$services_verifier = new WPDesk_WooCommerce_DPD_UK_Services_Verifier( $this->get_api(), WC()->session );
			$services_verifier->hooks();
		}

		public function admin_init_get_dpd_uk_request() {
			if ( is_admin() && isset( $_GET['get_dpd_uk_request'] ) ) {
				try {
					/** @var WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment */
					$shipment = fs_get_shipment( $_GET['get_dpd_uk_request'] );
					header( 'Content-disposition: attachment; filename=' . $_GET['get_dpd_uk_request'] . '.json' );
					header( 'Content-type: application/json' );
					echo json_encode( $shipment->get_dpd_request_data(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
					$request_count = intval( get_option( self::OPTION_NAME_DPD_REQUEST_COUNT, 0 ) );
					$request_count++;
					update_option( self::OPTION_NAME_DPD_REQUEST_COUNT, $request_count );
				}
				catch ( Exception $e ) {
					echo $e->getMessage();
				}
				exit;
			}
		}

		public function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
			$dpd_uk = $all_shipping_methods['dpd_uk'];
			$settings = $dpd_uk->settings;
			if ( isset( $settings['auto_create'] ) && $settings['auto_create'] == 'auto' ) {
				if ( isset( $settings['order_status'] ) && 'wc-' . $new_status == $settings['order_status'] ) {
					$order = wc_get_order( $order_id );
					$shipments = fs_get_order_shipments( $order_id, 'dpd_uk' );
					foreach ( $shipments as $shipment ) {
						try {
							$shipment->api_create();
						}
						catch ( Exception $e ) {}
					}
				}
			}
		}

		public function flexible_shipping_shipment_confirmed( WPDesk_Flexible_Shipping_Shipment $shipment ) {
			if ( $shipment->get_meta( '_integration', '' ) != 'dpd_uk' ) {
				return;
			}
			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
			$shipping_method = $all_shipping_methods['dpd_uk'];
			if ( $shipping_method->get_option( 'complete_order', 'no' ) == 'yes' ) {
				$order = $shipment->get_order();
				$order->update_status( 'completed', __( 'Order status changed automatically  - DPD UK plugin.', 'woocommerce-dpd-uk' ) );
			}
		}


		public function get_shipping_method() {
			$shipping_methods = WC()->shipping()->shipping_methods;
			if ( empty( $shipping_methods ) || !is_array( $shipping_methods ) || count( $shipping_methods ) == 0 ) {
				$shipping_methods = WC()->shipping()->load_shipping_methods();
			}
			return $shipping_methods['dpd_uk'];
		}

		public function get_label( $shipment_id ) {

			$shipment = fs_get_shipment( $shipment_id );

			return $shipment->get_label();
		}

		public function get_api() {
			$shipping_method = $this->get_shipping_method();
			return $shipping_method->get_api();
		}


		public function woocommerce_shipping_methods( $methods ) {
			include_once( 'class-shipping-method.php' );
			$methods['dpd_uk'] = 'WPDesk_WooCommerce_DPD_UK_Shipping_Method';
			return $methods;
		}

		public function flexible_shipping_add_shipping_options( $options ) {
			$options['dpd_uk'] = 'DPD UK';
			return $options;
		}

		public function woocommerce_review_order_after_shipping() {
			$dpd_uk = false;

			$shippings            = WC()->session->get( 'chosen_shipping_methods' );
			$packages             = WC()->cart->get_shipping_packages();
			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
			if ( empty( $all_shipping_methods ) ) {
				$all_shipping_methods = WC()->shipping()->load_shipping_methods();
			}
			$flexible_shipping       = $all_shipping_methods['flexible_shipping'];
			$flexible_shipping_rates = $flexible_shipping->get_all_rates();

			$shipping_methods = array();

			foreach ( $packages as $id => $package ) {
				$shipping = $shippings[ $id ];
				if ( isset( $flexible_shipping_rates[ $shipping ] ) ) {
					$shipping_method = $flexible_shipping_rates[ $shipping ];
					if ( $shipping_method['method_integration'] == 'dpd_uk' ) {
						$shipping_methods[] = $shipping_method;
						$dpd_uk             = true;
					}
				}
			}

			if ( $dpd_uk ) {
				$args = array();
				echo $this->plugin->load_template( 'shipping-method-after', 'woocommerce', $args );
			}
		}

	}
}
