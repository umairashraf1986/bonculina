<?php
/**
 * Class WPDesk_WooCommerce_DPD_UK_Services_Verifier
 *
 * @package WooCommerce DPD UK
 */

/**
 * Can verify service.
 */
class WPDesk_WooCommerce_DPD_UK_Services_Verifier implements \DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable {

	const COUNTRY                 = 'country';
	const STATE                   = 'state';
	const POSTCODE                = 'postcode';
	const SHIPPING_NETWORK_CODES  = 'shipping_network_codes';
	const DPD_UK_SHIPPING_NETWORK = 'dpd_uk_shipping_network';
	const METHOD_INTEGRATION      = 'method_integration';
	const DPD_UK_SERVICE          = 'dpd_uk_service';

	/**
	 * @var WPDesk_WooCommerce_DPD_UK_API
	 */
	private $api;

	/**
	 * @var WC_Session
	 */
	private $session;

	/**
	 * WPDesk_WooCommerce_DPD_UK_Services_Verifier constructor.
	 *
	 * @param WPDesk_WooCommerce_DPD_UK_API $api .
	 * @param WC_Session                    $session .
	 */
	public function __construct( WPDesk_WooCommerce_DPD_UK_API $api, WC_Session $session ) {
		$this->api     = $api;
		$this->session = $session;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible_shipping_add_method', array( $this, 'verify_service' ), 10, 3 );
	}

	/**
	 * Handle flexible shipping add method filter.
	 *
	 * @param bool  $add_method Add method.
	 * @param array $shipping_method Shipping method.
	 * @param array $package Package.
	 *
	 * @return bool
	 *
	 * @internal
	 */
	public function verify_service( $add_method, $shipping_method, $package ) {
		if ( $add_method
			&& isset( $shipping_method[ self::METHOD_INTEGRATION ] )
			&& isset( $shipping_method[ self::DPD_UK_SERVICE ] )
			&& WPDesk_WooCommerce_DPD_UK::METHOD_INTEGRATION === $shipping_method[ self::METHOD_INTEGRATION ]
		) {
			$destination      = $package['destination'];
			$shipping_network = $this->get_shipping_network_for_destination( $destination[ self::COUNTRY ], $destination[ self::STATE ], $destination[ self::POSTCODE ] );
			if ( is_array( $shipping_network ) && isset( $shipping_network[ self::SHIPPING_NETWORK_CODES ] ) && is_array( $shipping_network[ self::SHIPPING_NETWORK_CODES ] ) ) {
				$add_method = in_array( $shipping_method[ self::DPD_UK_SERVICE ], $shipping_network[ self::SHIPPING_NETWORK_CODES ], true );
			}
		}

		return $add_method;
	}

	/**
	 * .
	 *
	 * @param string $country .
	 * @param string $state .
	 * @param string $postcode .
	 *
	 * @return array
	 */
	private function get_shipping_network_for_destination( $country, $state, $postcode ) {
		$shipping_network = $this->session->get( self::DPD_UK_SHIPPING_NETWORK, array() );
		if ( ! $this->shipping_network_address_equals_package_address( $shipping_network, $country, $state, $postcode ) ) {
			$shipping_network = array(
				self::COUNTRY                => $country,
				self::STATE                  => $state,
				self::POSTCODE               => $postcode,
				self::SHIPPING_NETWORK_CODES => $this->get_shipping_network_codes_from_api( $country, $state, $postcode ),
			);
			$this->session->set( self::DPD_UK_SHIPPING_NETWORK, $shipping_network );
		}

		return $shipping_network;
	}

	/**
	 * @param string $country .
	 * @param string $state .
	 * @param string $postcode .
	 *
	 * @return array
	 */
	private function get_shipping_network_codes_from_api( $country, $state, $postcode ) {
		$api_shipping_network = array();
		try {
			$shipping_network = $this->api->shipping_network(
				$country,
				$state,
				strtoupper( str_replace( array( '-', ' ' ), '', $postcode ) )
			);
			if ( ! isset( $shipping_network->data ) ) {
				$shipping_network->data = array();
			}
			if ( ! is_array( $shipping_network->data ) ) {
				$shipping_network->data = array( $shipping_network->data );
			}
			foreach ( $shipping_network->data as $data ) {
				$api_shipping_network[] = $data->network->networkCode;
			}
		} catch ( Exception $e ) {
			//TODO: write debug log
		}

		return $api_shipping_network;
	}

	/**
	 * .
	 *
	 * @param array  $shipping_network .
	 * @param string $country .
	 * @param string $state .
	 * @param string $postcode .
	 *
	 * @return bool
	 */
	private function shipping_network_address_equals_package_address( $shipping_network, $country, $state, $postcode ) {
		return is_array( $shipping_network )
			&& ( isset( $shipping_network[ self::COUNTRY ] ) ? $shipping_network[ self::COUNTRY ] : '' ) === $country
			&& ( isset( $shipping_network[ self::STATE ] ) ? $shipping_network[ self::STATE ] : '' ) === $state
			&& ( isset( $shipping_network[ self::POSTCODE ] ) ? $shipping_network[ self::POSTCODE ] : '' ) === $postcode;
	}

}
