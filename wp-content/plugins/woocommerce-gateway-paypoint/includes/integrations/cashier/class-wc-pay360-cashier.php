<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Cashier extends WC_Pay360_Abstract_Integration {

	/**
	 * WC_Pay360_Hosted_Cashier constructor.
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}

	/**
	 * Returns the integration settings
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_settings() {
		return array(
			'cashier_api_installation_id' => array(
				'title'       => __( 'Installation ID', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Installation ID setup by Pay360 for use with the Cashier or Hosted Cashier API.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => ''
			),

			'cashier_api_username' => array(
				'title'       => __( 'API Username', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'The API Username is provided by Pay360.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => ''
			),

			'cashier_api_password' => array(
				'title'       => __( 'API Password', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'password',
				'description' => __( "The API Password is provided by Pay360.", WC_Pay360::TEXT_DOMAIN ),
				'placeholder' => __( "Password won't appear here.", WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
			),

			'cashier_customer_prefix' => array(
				'title'       => __( 'Customer Prefix', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'When we create a customer in the Pay360 system we can add a prefix to the customer ID. This prefix will make sure that there are no duplicated customers and will help you identify which store is the customer from. Please set a unique Prefix for the store.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => 'WC-' . substr( uniqid(), 0, 4 ) . '-'
			),

			'cashier_transaction_type' => array(
				'title'       => __( 'Transaction Type', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'select',
				'description' => sprintf( __( "Type of the transaction. %s Capture: will authorize and capture the funds. %s Authorization only: will only authorize the funds and allow you to capture at a later date. Authorizations expire by default after 7 days. After this time, you won't be able to Capture or Cancel the request.", WC_Pay360::TEXT_DOMAIN ), '<br/>', '<br/>' ),
				'class'       => 'chosen_select',
				'css'         => 'min-width:350px;',
				'options'     => array(
					'capture' => 'Capture',
					'auth'    => 'Authorization Only',
				),
				'default'     => 'capture',
			),
		);
	}

	/**
	 * Is the gateway available on checkout
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( ! $this->get_gateway()->get_option( 'cashier_api_installation_id' ) ) {
			return false;
		}
		if ( ! $this->get_gateway()->get_option( 'cashier_api_username' ) ) {
			return false;
		}
		if ( ! $this->get_gateway()->get_option( 'cashier_api_password' ) ) {
			return false;
		}

		return true;
	}
}