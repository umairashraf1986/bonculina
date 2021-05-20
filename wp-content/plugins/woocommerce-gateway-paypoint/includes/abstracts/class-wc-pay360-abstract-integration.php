<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WC_Pay360_Abstract_Integration {
	
	protected $api_client;
	public $countries_alpha_3;
	
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}
	
	/**
	 * @return WC_Pay360_Gateway
	 */
	protected function get_gateway() {
		return $this->gateway;
	}
	
	/**
	 * Loads and returns the main Pay360 API class
	 *
	 * @since 2.0
	 *
	 * @return \WcPay360\Api\Client
	 */
	public function get_api_client() {
		if ( null == $this->api_client ) {
			$this->api_client = new \WcPay360\Api\Client(
				$this->get_gateway()->get_option( 'cashier_api_installation_id' ),
				$this->get_gateway()->get_option( 'cashier_api_username' ),
				$this->get_gateway()->get_option( 'cashier_api_password' ),
				$this->is_testmode()
			);
		}
		
		return $this->api_client;
	}
	
	/**
	 * Returns whether the gateway is in test mode.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function is_testmode() {
		if ( 'test_success' == $this->get_gateway()->get_option( 'testmode' )
		     || 'test_fail' == $this->get_gateway()->get_option( 'testmode' )
		) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Return whether the transaction should be deferred or not.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function is_deferred() {
		if ( 'capture' == $this->get_gateway()->get_option( 'cashier_transaction_type' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns whether the transaction should use 3D Secure or not
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function maybe_do_3d_secure() {
		if ( 'no' == $this->get_gateway()->get_option( 'cashier_use_3ds' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns whether the transaction should use 3D Secure or not
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function use_iframe() {
		if ( 'no' == $this->get_gateway()->get_option( 'hosted_cashier_use_iframe' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Merchant customer id that we created the Pay360 profile with.
	 *
	 * @since 2.1
	 *
	 * @param $user_id
	 *
	 * @return mixed|string
	 */
	public function get_merchant_customer_id( $user_id ) {
		// If we have a guest, we will just use a unique ID
		if ( 0 == (int) $user_id ) {
			return $this->get_guest_merchant_customer_id();
		}
		
		$meta_name   = $this->get_customer_merchant_reference_meta_name();
		$merchant_id = get_user_meta( $user_id, $meta_name, true );
		if ( ! $merchant_id ) {
			// Create an id and save it to the customer meta
			$merchant_id = $this->get_gateway()->get_option( 'cashier_customer_prefix' ) . $user_id;
			update_user_meta( $user_id, $meta_name, wc_clean( $merchant_id ) );
		}
		
		return $merchant_id;
	}
	
	/**
	 * Returns the customer merchant reference meta field name
	 *
	 * @since 2.1
	 *
	 * @return string
	 */
	public function get_customer_merchant_reference_meta_name() {
		$prefix = '';
		if ( $this->is_testmode() ) {
			$prefix = 'testmode_pay360_';
		}
		
		return $prefix . 'wc_pay360_customer_merchant_ref';
	}
	
	/**
	 * Returns the guest merchant reference number.
	 * We add a uniqid and a suffix to make sure that we have a unique value
	 *
	 * @since 2.1
	 *
	 * @return string
	 */
	public function get_guest_merchant_customer_id() {
		return $this->get_gateway()->get_option( 'cashier_customer_prefix' ) . uniqid() . $this->get_guest_customers_suffix();
	}
	
	/**
	 * Returns an incremented number suffix value.
	 *
	 * @since 2.1
	 *
	 * @return int
	 */
	public function get_guest_customers_suffix() {
		$suffix = (int) get_option( 'wc_pay360_guest_customers_suffix', 0 );
		
		$suffix = $suffix + 1;
		update_option( 'wc_pay360_guest_customers_suffix', $suffix );
		
		return $suffix;
	}
	
	public function order_contains_pre_order( $order ) {
		return WC_Pay360::is_pre_orders_active() && WC_Pre_Orders_Order::order_contains_pre_order( $order );
	}
	
	/**
	 * Convert country code to alpha 3 ISO
	 *
	 * @since 2.1.4
	 *
	 * @param $country
	 *
	 * @return mixed
	 */
	public function convert_country_code_to_alpha_3( $country ) {
		if ( 2 != strlen( $country ) ) {
			return $country;
		}
		
		if ( empty( $this->countries_alpha_3 ) ) {
			$this->countries_alpha_3 = apply_filters( 'wc_pay360_countries_alpha_3', include WC_Pay360::plugin_path() . '/includes/countries-alpha-3.php' );
		}
		
		return isset( $this->countries_alpha_3[ $country ] ) ? $this->countries_alpha_3[ $country ] : $country;
	}
	
	/**
	 * Convert the state code to the actual state name
	 *
	 * @since 2.1.4
	 *
	 * @param $state
	 * @param $country
	 *
	 * @return mixed
	 */
	public function convert_state_code_to_name( $state, $country ) {
		$states = WC()->countries->get_states( $country );
		
		if ( empty( $states ) ) {
			return $state;
		}
		
		if ( is_array( $states ) ) {
			if ( isset( $states[ $state ] ) ) {
				return $states[ $state ];
			}
		}
		
		return $state;
	}
}