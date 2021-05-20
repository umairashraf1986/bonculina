<?php
/**
 * Class WPDesk_WooCommerce_DPD_UK_API
 *
 * @package WooCommerce DPD UK
 */

use WPDesk\DpdUk\Api\GeoSession;
use WPDesk\DpdUk\Api\GeoSessionCached;

/**
 * DPD UK API.
 */
class WPDesk_WooCommerce_DPD_UK_API {

	const API_TYPE_DPD       = 'DPD';
	const API_TYPE_DPD_LOCAL = 'DPD_LOCAL';

	const WEIGHT_PRECISION = 3;

	/** @var array */
	private $settings;

	/** @var string */
	private $username;

	/** @var string */
	private $password;

	/** @var string */
	private $account_number;

	/** @var GeoSession|null */
	private $geo_session;

	/** @var string */
	private $api_host;

	/** @var array */
	private $messages = array();

	/**
	 * @var WPDesk_WooCommerce_DPD_UK_API_Data_Interface
	 */
	private $api_data;

	/**
	 * WPDesk_WooCommerce_DPD_UK_API constructor.
	 *
	 * @param array $settings .
	 */
	public function __construct( $settings ) {
		$this->settings       = $settings;
		$api_data             = WPDesk_WooCommerce_DPD_UK_API_Data_Factory::get_api_data_for_api_type( $this->get_api_type() );
		$this->api_data       = $api_data;
		$this->api_host       = $api_data->get_api_url();
		$this->username       = $settings[ WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_USERNAME ];
		$this->password       = $settings[ WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_PASSWORD ];
		$this->account_number = $settings[ WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_ACCOUNT_NUMBER ];
	}

	/**
	 * @return string
	 */
	public function get_api_type() {
		if ( empty( $this->settings[ WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_API_TYPE ] ) ) {
			$api_type = self::API_TYPE_DPD;
		} else {
			$api_type = $this->settings[ WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_API_TYPE ];
		}

		return $api_type;
	}

	/**
	 * @return WPDesk_WooCommerce_DPD_UK_API_Data_Interface
	 */
	public function get_api_data() {
		return $this->api_data;
	}

	/**
	 * @param WPDesk_WooCommerce_DPD_UK_API_Data_Interface $api_data .
	 */
	public function set_api_data( WPDesk_WooCommerce_DPD_UK_API_Data_Interface $api_data ) {
		$this->api_data = $api_data;
	}

	/**
	 * Build and return package tracking URL
	 *
	 * @param string $tracking_number Tracking number.
	 * @param string $postcode Postal code.
	 *
	 * @return string
	 */
	public function build_package_tracking_url( $tracking_number, $postcode ) {
		$tracking_url = sprintf( $this->api_data->get_tracking_url(), $tracking_number, $postcode );

		return $tracking_url;
	}

	/**
	 * @return string
	 */
	public function get_api_host() {
		return $this->api_data->get_api_url();
	}

	/**
	 * @param string $message .
	 *
	 * @return mixed
	 */
	public function translate_messages( $message ) {
		if ( isset( $this->messages[ $message ] ) ) {
			return $this->messages[ $message ];
		}

		return $message;
	}

	/**
	 * Clear cache.
	 */
	public function clear_cache() {
		$this->get_geo_session()->clear_cache();
	}

	/**
	 * @throws Exception .
	 */
	public function ping() {
		$this->get_geo_session_string();
	}

	/**
	 * @param array  $parameters .
	 * @param string $parent_key .
	 *
	 * @return string
	 */
	public function build_parameters( array $parameters, $parent_key = '' ) {
		$parameters_string = '';
		foreach ( $parameters as $key => $value ) {
			if ( is_array( $value ) ) {
				$parameters_string .= '&' . $this->build_parameters( $value, $parent_key . $key . '.' );
			} else {
				$parameters_string .= '&' . $parent_key . $key . '=' . $value;
			}
		}

		return trim( $parameters_string, '&' );
	}

	/**
	 * @param string $url .
	 * @param array  $parameters .
	 * @param array  $headers .
	 *
	 * @return array
	 * @throws WPDesk_WooCommerce_DPD_UK_API_Exception .
	 */
	public function get( $url, array $parameters = array(), array $headers = array() ) {
		$url  = $this->api_host . $url;
		$url .= '?' . $this->build_parameters( $parameters );
		$args = array(
			'timeout' => 30,
			'headers' => array(
				'GeoClient'  => 'account/' . $this->settings['account_number'],
				'GeoSession' => $this->get_geo_session_string(),
			),
		);
		foreach ( $headers as $key => $header ) {
			$args['headers'][ $key ] = $header;
		}

		$response = wp_remote_get( rawurldecode( $url ), $args );

		$this->throw_exception_when_invalid_response( $response );

		return $response;
	}

	/**
	 * @param string $url .
	 * @param array  $data .
	 *
	 * @return array
	 */
	public function post( $url, array $data ) {
		$url  = $this->api_host . $url;
		$args = array(
			'timeout' => 30,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'GeoClient'    => 'account/' . $this->settings['account_number'],
				'GeoSession'   => $this->get_geo_session_string(),
			),
			'body'    => json_encode( $data, JSON_UNESCAPED_UNICODE ),
		);

		$response = wp_remote_post( rawurldecode( $url ), $args );

		$this->throw_exception_when_invalid_response( $response );

		return $response;
	}

	/**
	 * @return string
	 */
	private function get_geo_session_string() {
		$geo_session = $this->get_geo_session();

		$geo_session_string = $geo_session->get_geo_session_string();
		if ( empty( $geo_session_string ) ) {
			$geo_session_string = $this->login_and_get_geo_session();
			$geo_session->set_geo_session_string( $geo_session_string );
		}

		return $geo_session_string;
	}

	/**
	 * @return GeoSessionCached
	 */
	private function get_geo_session() {
		if ( empty( $this->geo_session ) ) {
			$this->geo_session = new GeoSessionCached();
		}

		return $this->geo_session;
	}

	/**
	 * @return string .
	 *
	 * @throws WPDesk_WooCommerce_DPD_UK_API_Exception .
	 */
	private function login_and_get_geo_session() {
		$url      = $this->api_host . '/user/?action=login';
		$args     = array(
			'timeout' => 30,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->username . ':' . $this->password ),	// phpcs:ignore
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
			),
		);
		$response = wp_remote_post( $url, $args );

		$this->throw_exception_when_invalid_response( $response );

		$json = json_decode( $response['body'] );

		return $json->data->geoSession;
	}

	/**
	 * @param string      $country .
	 * @param string|null $state .
	 * @param string      $postcode .
	 *
	 * @return object
	 * @throws WPDesk_WooCommerce_DPD_UK_API_Exception .
	 */
	public function shipping_network( $country, $state, $postcode ) {
		$postcode   = strtoupper( preg_replace( '/(\s|-)/', '', $postcode ) );
		$url        = '/shipping/network/';
		$parameters = array(
			'deliveryDetails'   => array(
				'address' => array(
					'countryCode' => $country,
					'postcode'    => $postcode,
					'county'      => '',
				),
			),
			'collectionDetails' => array(
				'address' => array(
					'countryCode' => isset( $this->settings['sender_country'] ) ? $this->settings['sender_country'] : '',
					'county'      => isset( $this->settings['sender_county'] ) ? $this->settings['sender_county'] : '',
					'postcode'    => isset( $this->settings['sender_postcode'] ) ? $this->settings['sender_postcode'] : '',
					'town'        => isset( $this->settings['sender_town'] ) ? $this->settings['sender_town'] : '',
				),
			),
			'deliveryDirection' => 1,
		);
		if ( $state ) {
			$parameters['deliveryDetails']['address']['county'] = $this->convert_state_code_to_name( $country, $state );
		}

		$response         = $this->get( $url, $parameters );
		$shipping_network = json_decode( $response['body'] );

		if ( ! empty( $shipping_network->error ) ) {
			throw new WPDesk_WooCommerce_DPD_UK_API_Exception( $shipping_network->error->errorMessage );
		}

		return $shipping_network;
	}

	/**
	 * Returns state/county name if possible
	 *
	 * @param string $country_code .
	 * @param string $state_code .
	 *
	 * @return string
	 */
	private function convert_state_code_to_name( $country_code, $state_code ) {
		$states = WC()->countries->get_states();
		if ( isset( $states[ $country_code ][ $state_code ] ) ) {
			return $states[ $country_code ][ $state_code ];
		}

		return $state_code;
	}

	/**
	 * @param array $shipment_data .
	 *
	 * @return object
	 * @throws WPDesk_WooCommerce_DPD_UK_API_Exception .
	 */
	public function shipping_shipment( array $shipment_data ) {
		$url      = '/shipping/shipment/';
		$response = $this->post( $url, $shipment_data );

		$json = json_decode( $response['body'] );

		return $json;
	}

	/**
	 * @param string $shipment_id .
	 * @param string $label_format .
	 *
	 * @return mixed
	 * @throws WPDesk_WooCommerce_DPD_UK_API_Exception .
	 */
	public function get_label( $shipment_id, $label_format ) {
		$url     = '/shipping/shipment/' . $shipment_id . '/label/';
		$headers = array(
			'Accept' => 'text/html',
		);
		if ( 'CLP' === $label_format ) {
			$headers = array(
				'Accept' => 'text/vnd.citizen-clp',
			);
		}
		if ( 'EPL' === $label_format ) {
			$headers = array(
				'Accept' => 'text/vnd.eltron-epl',
			);
		}
		$parameters = array();
		$response   = $this->get( $url, $parameters, $headers );

		return $response['body'];

	}

	/**
	 * @param WP_Error|array $response .
	 *
	 * @throws WPDesk_WooCommerce_DPD_UK_API_Exception .
	 */
	private function throw_exception_when_invalid_response( $response ) {
		if ( is_wp_error( $response ) ) {
			$this->get_geo_session()->clear_cache();
			throw new WPDesk_WooCommerce_DPD_UK_API_Exception(
				sprintf(
					// Translators: message text.
					__( 'DPD UK API message: %1$s', 'woocommerce-dpd-uk' ),
					$response->get_error_message()
				)
			);
		}

		if ( 200 !== (int) $response['response']['code'] ) {
			$this->get_geo_session()->clear_cache();
			throw new WPDesk_WooCommerce_DPD_UK_API_Exception(
				sprintf(
					// Translators: message text.
					__( 'DPD UK API message: %1$s', 'woocommerce-dpd-uk' ),
					$response['response']['message']
				)
			);
		}
	}

}
