<?php

use WPDesk\DpdUk\Api\Request\Exception\InvalidCurrencyException;

class WPDesk_Flexible_Shipping_Shipment_dpd_uk extends WPDesk_Flexible_Shipping_Shipment implements WPDesk_Flexible_Shipping_Shipment_Interface {

	private $dpd_uk_one_time_message = null;

	static $shipment_date = null;

	const DPD_UK_CONSOLIDATE_META_KEY = '_dpd_uk_consolidate';

	const META_KEY_LOW_LINE = '_';

	/** @var WPDesk_WooCommerce_DPD_UK_Plugin */
	private static $dpd_uk_plugin;

	public function __construct( $shipment, WC_Order $order = null ) {
		parent::__construct( $shipment, $order );
	}

	/**
	 * Inject plugin for further use.
	 *
	 * @param WPDesk_WooCommerce_DPD_UK_Plugin $plugin
	 */
	public static function set_plugin( WPDesk_WooCommerce_DPD_UK_Plugin $plugin ) {
		self::$dpd_uk_plugin = $plugin;
	}

	public function checkout( array $fs_method, $package ) {
		$order = $this->get_order();
		$this->set_meta( '_dpd_uk_service', $fs_method['dpd_uk_service'] );

		$this->set_meta( '_dpd_uk_liability', $fs_method['dpd_uk_liability'] );

		$this->set_meta( self::DPD_UK_CONSOLIDATE_META_KEY, $fs_method[ WPDesk_WooCommerce_DPD_UK_FS_Hooks::DPD_UK_CONSOLIDATE ] );

		$this->set_meta( '_dpd_uk_liability_value', $order->get_total() );

		$this->set_meta( '_dpd_uk_parcel_description', $fs_method['dpd_uk_parcel_description'] );

		$weight = wc_get_weight( fs_calculate_package_weight( $package ), 'kg' );
		$this->set_meta( '_dpd_uk_weight', $weight );

		$this->set_meta( '_dpd_uk_number_of_parcels', 1 );

		$references = array(
			'reference1' => $fs_method['dpd_uk_reference1'],
			'reference2' => $fs_method['dpd_uk_reference2'],
			'reference3' => $fs_method['dpd_uk_reference3'],
		);

		foreach ( $references as $key => $reference ) {
			$references[ $key ] = str_replace( '[order_number]', $order->get_order_number(), $references[ $key ] );
			$references[ $key ] = str_replace( '[shop_name]', get_bloginfo( 'name' ), $references[ $key ] );
			$references[ $key ] = str_replace( '[shop_url]', home_url(), $references[ $key ] );
			$this->set_meta( '_dpd_uk_' . $key, $references[ $key ] );
		}

		if ( ! empty( $_POST['dpd_uk_delivery_instructions'] ) ) {
			$this->set_meta( '_dpd_uk_delivery_instructions', $_POST['dpd_uk_delivery_instructions'] );
		} else {
			$this->set_meta( '_dpd_uk_delivery_instructions', '' );
		}

	}

	public function order_metabox_content() {

		if ( ! function_exists( 'woocommerce_form_field' ) ) {
			$wc_template_functions = trailingslashit( dirname( __FILE__ ) ) . '../../woocommerce/includes/wc-template-functions.php';
			if ( file_exists( $wc_template_functions ) ) {
				include_once( $wc_template_functions );
			}
		}

		$settings = $this->get_shipping_method()->settings;

		$order = $this->get_order();

		$id = $this->get_id();

		$dpd_uk_status = $this->get_meta( '_dpd_uk_status' );

		$disabled = false;
		if ( isset( $dpd_uk_status ) && $dpd_uk_status == 'ok' ) {
			$disabled = true;
		}

		$dpd_uk_service = $this->get_meta( '_dpd_uk_service' );

		$dpd_uk_weight = $this->get_meta( '_dpd_uk_weight' );

		$dpd_uk_number_of_parcels = $this->get_meta( '_dpd_uk_number_of_parcels' );

		$dpd_uk_delivery_instructions = $this->get_meta( '_dpd_uk_delivery_instructions', '' );

		$dpd_uk_reference1 = $this->get_meta( '_dpd_uk_reference1' );
		$dpd_uk_reference2 = $this->get_meta( '_dpd_uk_reference2' );
		$dpd_uk_reference3 = $this->get_meta( '_dpd_uk_reference3' );

		$dpd_uk_parcel_description = $this->get_meta( '_dpd_uk_parcel_description' );

		$dpd_uk_liability = $this->get_meta( '_dpd_uk_liability' );

		$dpd_uk_liability_value = $this->get_meta( '_dpd_uk_liability_value' );

		$dpd_uk_consolidate = $this->get_meta( self::DPD_UK_CONSOLIDATE_META_KEY );

		$dpd_uk_one_time_message = $this->get_meta( '_dpd_uk_one_time_message', '' );

		$dpd_uk_message = $this->get_meta( '_dpd_uk_message' );

		$tracking_url = $this->get_tracking_url();

		$dpd_uk_package_id = $this->get_meta( '_dpd_uk_package_id' );

		$dpd_uk_package_number = $this->get_tracking_number();

		$label_url = $this->get_label_url();

		$label_available = $this->label_avaliable();

		$dpd_uk_one_time_message = $this->dpd_uk_one_time_message;

		$shipment = $this;

		$dpd_uk = self::$dpd_uk_plugin->dpd_uk;

		ob_start();

		$echo = false;

		include( 'views/order-metabox-content.php' );

		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	public function order_metabox() {
		echo $this->order_metabox_content();
	}

	public function get_order_metabox_title() {
		return __( 'DPD UK', 'woocommerce-dpd-uk' );
	}

	public function ajax_request( $action, $data ) {
		$ret = array();
		if ( $action == 'save' ) {
			$this->save_ajax_data( $data );
			$this->save();
			$ret['message'] = __( 'Shipping saved.', 'woocommerce-dpd-uk' );
		} else if ( $action == 'send' ) {
			$this->save_ajax_data( $data );
			$this->api_create();
			$ret['message'] = __( 'Shipping created.', 'woocommerce-dpd-uk' );
		} else if ( $action == 'cancel' ) {
			$dpd_package_number = $this->get_meta( '_dpd_package_number' );
			$this->save_ajax_data( $data );
			$this->api_cancel();
			$ret['message'] = __( 'Shipping canceled.', 'woocommerce-dpd-uk' );
		} else if ( $action == 'refresh' ) {
			$this->api_refresh();
		} else {
			throw new Exception( __( 'Unknown action: "' . $action . '"', 'woocommerce-dpd-uk' ) );
		}
		$ret['content'] = $this->order_metabox_content();

		return $ret;
	}

	public function api_refresh() {
	}

	/**
	 * @param array $data
	 * @param string $key
	 * @param null|string $callback
	 */
	private function set_or_remove( $data, $key, $callback = null ) {

		if ( isset( $data[ $key ] ) ) {
			if ( $callback ) {
				$data [ $key ] = call_user_func( $callback, $data[ $key ] );
			}

			$this->set_meta( self::META_KEY_LOW_LINE . $key, $data[ $key ] );
		} else {
			$this->delete_meta( self::META_KEY_LOW_LINE . $key );
		}
	}

	public function save_ajax_data( $data ) {
		$this->set_or_remove( $data, 'dpd_uk_service' );
		$this->set_or_remove( $data, 'dpd_uk_weight', 'wc_format_decimal' );
		$this->set_or_remove( $data, 'dpd_uk_number_of_parcels' );
		$this->set_or_remove( $data, 'dpd_uk_delivery_instructions' );
		$this->set_or_remove( $data, 'dpd_uk_reference1' );
		$this->set_or_remove( $data, 'dpd_uk_reference2' );
		$this->set_or_remove( $data, 'dpd_uk_reference3' );
		$this->set_or_remove( $data, 'dpd_uk_parcel_description' );
		$this->set_or_remove( $data, 'dpd_uk_liability' );
		$this->set_or_remove( $data, WPDesk_WooCommerce_DPD_UK_FS_Hooks::DPD_UK_CONSOLIDATE );
		$this->set_or_remove( $data, 'dpd_uk_liability_value', 'wc_format_decimal' );
	}

	public function get_error_message() {
		return $this->get_meta( '_dpd_uk_message' );
	}

	public function get_tracking_number() {
		return $this->get_meta( '_dpd_uk_parcel_number' );
	}

	public function get_tracking_url() {
		$tracking_url   = null;
		$dpd_uk_package = $this->get_meta( '_dpd_uk_package', '' );
		if ( $dpd_uk_package != '' ) {
			$api          = $this->get_api();
			$order        = $this->get_order();
			$tracking_url = $api->build_package_tracking_url( $this->get_tracking_number(), $order->get_shipping_postcode() );
		}

		return $tracking_url;
	}

	/**
	 * Creates shipment?
	 *
	 * @throws Exception
	 */
	public function api_create() {
		$this->api_create_webapi();
		$this->save();
		$error_message = $this->get_error_message();
		if ( ! empty( $error_message ) ) {
			throw new Exception( $error_message );
		}
		$order = $this->get_order();

		/**
		 * Can modify is_customer_note parameter for WC_Order::add_order_note
		 *
		 * @param int $is_customer_note Is customer note?
		 *
		 * @return int
		 *
		 * If you want to send created shipment note to customer add code below to functions.php file in theme folder:
		 * add_filter( 'woocommerce_dpd_uk_shipment_created_is_customer_note', function() { return 1; } );
		 */
		$is_customer_note = apply_filters( 'woocommerce_dpd_uk_shipment_created_is_customer_note', 0 );

		$order->add_order_note(
			sprintf( __( 'DPD UK Shipment %s was created.', 'woocommerce-dpd-uk' ), $this->get_meta( '_dpd_uk_parcel_number' ) ),
			$is_customer_note
		);
	}

	/**
	 * @param WPDesk_WooCommerce_DPD_UK_API $api .
	 *
	 * @return array
	 *
	 * @throws Exception .
	 * @throws InvalidCurrencyException .
	 */
	public function prepare_shipment_data( WPDesk_WooCommerce_DPD_UK_API $api ) {
		$shipment_data = ( new \WPDesk\DpdUk\Api\Request\ShipmentRequestFactory() )->create_for_shipment_and_settings( $this, $this->get_shipping_method()->settings );

		/**
		 * Force array for backward compatibility for filter below.
		 */
		$shipment_data = json_decode( json_encode( $shipment_data ), true );

		/**
		 * Can modify shipment data before send it to API.
		 *
		 * @param array                                    $shipment_data Shipment data.
		 * @param WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment Shipment.
		 *
		 * @return array
		 *
		 * Shipment data is an array with data required by API to create shipment.
		 *
		 * Shipment is instance of WPDesk_Flexible_Shipping_Shipment_dpd_uk class.
		 * To access order use this code: $shipment->get_order();
		 */
		return apply_filters( 'woocommerce_dpd_uk_shipment_data', $shipment_data, $this );

	}

	/**
	 * Creates order metadata based on package info from api
	 *
	 * @throws Exception
	 */
	public function api_create_webapi() {

		if ( $this->get_status() != 'fs-new' && $this->get_status() != 'fs-failed' ) {
			throw new Exception( sprintf( __( 'Invalid status: %s. Package already created?', 'woocommerce-dpd-uk' ), $this->get_status() ) );
		}

		/** @var WPDesk_WooCommerce_DPD_UK_API $api */
		$api = $this->get_api();

		try {

			$shipment_data = $this->prepare_shipment_data( $api );

			$dpd_uk_package = $api->shipping_shipment( $shipment_data );

			if ( empty( $dpd_uk_package->error ) ) {
				$this->set_meta( '_dpd_uk_status', 'ok' );
				$this->set_meta( '_dpd_uk_package', $dpd_uk_package );
				$this->set_meta( '_dpd_uk_shipment_id', $dpd_uk_package->data->shipmentId );
				$this->set_meta( '_dpd_uk_parcel_number', $dpd_uk_package->data->consignmentDetail[0]->parcelNumbers[0] );

				$this->delete_meta( '_dpd_uk_message' );
				$this->update_status( 'fs-confirmed' );
				$this->save();
				do_action( 'flexible_shipping_shipment_confirmed', $this );
			} else {
				$message = '';
				$dpd_uk_package->error = ! is_array( $dpd_uk_package->error ) ? array( $dpd_uk_package->error ) : $dpd_uk_package->error;
				foreach ( $dpd_uk_package->error as $error ) {
					$message .= ' ' . $error->obj . ': ' . $error->errorMessage;
				}
				throw new Exception( sprintf( __( 'DPD UK API error: %s', 'woocommerce-dpd-uk' ), $message ) );
			}
		} catch ( Exception $e ) {
			$this->set_meta( '_dpd_uk_status', 'error' );
			$this->set_meta( '_dpd_uk_message', $e->getMessage() );
			$this->update_status( 'fs-failed' );
			$this->save();
			throw $e;
		}

	}


	public function api_cancel() {

		if ( $this->get_status() != 'fs-confirmed' ) {
			throw new Exception( sprintf( __( 'Invalid shipment status: %s. Shipment already canceled or manifest created?', 'woocommerce-dpd-uk' ), $this->get_status() ) );
		}

		$dpd_package_number = $this->get_meta( '_dpd_package_number' );

		$this->delete_meta( '_dpd_uk_status' );
		$this->delete_meta( '_dpd_uk_package' );
		$this->delete_meta( '_dpd_uk_shipment_id' );
		$this->delete_meta( '_dpd_uk_parcel_number' );
		$this->delete_meta( '_dpd_uk_message' );

		$this->update_status( 'fs-new' );
		$this->save();
		$order = $this->get_order();

		/**
		 * Can modify is_customer_note parameter for WC_Order::add_order_note
		 *
		 * @param int $is_customer_note Is customer note?
		 *
		 * @return int
		 *
		 * If you want to send deleted shipment note to customer add code below to functions.php file in theme folder:
		 * add_filter( 'woocommerce_dpd_uk_shipment_deleted_is_customer_note', function() { return 1; } );
		 */
		$is_customer_note = apply_filters( 'woocommerce_dpd_uk_shipment_deleted_is_customer_note', 0 );
		$order->add_order_note( sprintf( __( 'DPD UK Shipment %s deleted.', 'woocommerce-dpd-uk' ), $dpd_package_number ), $is_customer_note );
	}

	public function get_label() {
		if ( ! $this->label_avaliable() ) {
			throw new Exception( sprintf( __( 'Label unavailable for status %s.', 'woocommerce-dpd-uk' ), $this->get_status() ) );
		}

		return $this->api_get_label();
	}

	public function get_email_after_order_table() {
		if ( $this->label_avaliable() ) {
			$args = array(
				'dpd_uk_packages' => array(
					array(
						'tracking_url' => $this->get_tracking_url(),
						'shipment_id'  => $this->get_meta( '_dpd_uk_parcel_number' ),
					)
				)
			);
			echo self::$dpd_uk_plugin->load_template( 'email_after_order_table', 'woocommerce', $args );
		}
	}

	public function get_after_order_table() {
		if ( $this->label_avaliable() ) {
			$args = array(
				'dpd_uk_packages' => array(
					array(
						'tracking_url' => $this->get_tracking_url(),
						'shipment_id'  => $this->get_meta( '_dpd_uk_parcel_number' ),
					)
				)
			);
			echo self::$dpd_uk_plugin->load_template( 'order_details_after_order_table', 'woocommerce', $args );
		}
	}

	/**
	 * Get label from API.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function api_get_label() {
		$label_data = array(
			'label_format' => 'html',
			'content'      => null,
			'file_name'    => 'dpd_uk_' . $this->get_tracking_number() . '.html'
		);

		$shipping_method = $this->get_shipping_method();
		$settings        = $shipping_method->settings;
		$api             = $this->get_api();

		$label_format = $settings['label_format'];
		$label        = $api->get_label( $this->get_meta( '_dpd_uk_shipment_id' ), $label_format );

		if ( 'CLP' === $label_format ) {
			$label_format = 'clp';
		} elseif ( 'EPL' === $label_format ) {
			$label_format = 'epl';
		} else {
			$label_format = 'html';
		}

		$label_data['content']      = $label;
		$label_data['label_format'] = $label_format;
		$label_data['file_name']    = 'dpd_' . str_replace( ', ', '-',
				$this->get_tracking_number() ) . '.' . $label_format;

		/**
		 * Can perform action after label received form API or modify received label.
		 *
		 * @param array                                    $label_data Label data.
		 * @param WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment Shipment.
		 *
		 * @return array
		 *
		 * Label data is an array with keys:
		 *     - content: label received from API
		 *     - label_format
		 *     - file_name
		 *
		 * Shipment is instance of WPDesk_Flexible_Shipping_Shipment_dpd_uk class.
		 * To access order use this code: $shipment->get_order();
		 */
		return apply_filters( 'woocommerce_dpd_uk_label_data', $label_data, $this );

	}

	/**
	 * @return WPDesk_WooCommerce_DPD_UK_API
	 */
	public function get_api() {
		$shipping_method = $this->get_shipping_method();

		return $shipping_method->get_api();
	}

	/**
	 * @return \WPDesk\DpdUk\Api\Request\ShipmentRequest
	 */
	public function get_dpd_request_data() {
		$api = $this->get_api();

		return $this->prepare_shipment_data( $api );
	}

	/**
	 * @return WPDesk_WooCommerce_DPD_UK_Shipping_Method
	 */
	public function get_shipping_method() {
		$shipping_methods = WC()->shipping()->shipping_methods;
		if ( empty( $shipping_methods ) || ! is_array( $shipping_methods ) || count( $shipping_methods ) == 0 ) {
			$shipping_methods = WC()->shipping()->load_shipping_methods();
		}

		return $shipping_methods['dpd_uk'];
	}

	public function admin_add_shipment() {
		$order = $this->get_order();
		$this->set_meta( '_dpd_product', 'classic' );
		$weight = wc_get_weight( fs_calculate_order_weight( $order ), 'kg' );
		$this->set_meta( '_dpd_package_weight', $weight );
		$this->set_meta( '_dpd_ref', sprintf( __( 'Order %s', 'woocommerce-dpd-uk' ), $order->get_order_number() ) );
	}

	public function get_manifest_name() {
		$manifest_name = 'dpd_domestic';
		$order         = $this->get_order();
		if ( wpdesk_get_order_meta( $order, '_shipping_country', true ) != 'PL' ) {
			$manifest_name = 'dpd_international';
		}
		if ( $this->get_meta( '_dpd_session_type', '' ) != '' ) {
			$manifest_name = strtolower( 'dpd_' . $this->get_meta( '_dpd_session_type', '' ) );
		}

		return $manifest_name;
	}

	/**
	 * Should open label?
	 *
	 * @return bool
	 */
	protected function get_label_action() {
		if ( $this->get_shipping_method()->get_option(
			WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_FIELD_LABEL_FORMAT,
			WPDesk_WooCommerce_DPD_UK_Shipping_Method::LABEL_FORMAT_HTML
		) !== WPDesk_WooCommerce_DPD_UK_Shipping_Method::LABEL_FORMAT_HTML ) {
			return WPDesk_WooCommerce_DPD_UK_Shipping_Method::LABEL_ACTION_DOWNLOAD;
		}
		return $this->get_shipping_method()->get_option(
			WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_FIELD_LABEL_ACTION,
			WPDesk_WooCommerce_DPD_UK_Shipping_Method::LABEL_ACTION_DOWNLOAD
		);
	}


}
