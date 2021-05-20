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
class WC_Pay360_Hosted_Cashier extends WC_Pay360_Abstract_Integration {
	
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
				'description' => __( 'The installation ID provided by Pay360 for Hosted Cashier.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'cashier_api_username' => array(
				'title'       => __( 'API Username', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'The API Username is provided by Pay360.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'cashier_api_password' => array(
				'title'       => __( 'API Password', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'password',
				'description' => __( "The API Password is provided by Pay360.", WC_Pay360::TEXT_DOMAIN ),
				'placeholder' => __( "Password won't appear here.", WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'cashier_customer_prefix' => array(
				'title'       => __( 'Customer Prefix', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'When we create a customer in the Pay360 system we can add a prefix to the customer ID. This prefix will make sure that there are no duplicated customers and will help you identify which store is the customer from. Please set a unique Prefix for the store.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => 'WC-' . substr( uniqid(), 0, 4 ) . '-',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'cashier_use_3ds' => array(
				'title'       => __( 'Use 3D Secure', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Indicates if the transaction should be processed with 3DS. This will override account configuration for 3DS.', WC_Pay360::TEXT_DOMAIN ) . '<br/>' . __( 'Important: Initial configuration required, please contact Pay360 before attempting to use 3DS.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => 'no',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'cashier_transaction_type' => array(
				'title'       => __( 'Transaction Type', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'select',
				'description' => sprintf( __( "Type of the transaction. %s Capture: will authorize and capture the funds. %s Authorization only: will only authorize the funds and allow you to capture at a later date. Authorizations expire by default after 7 days. After this time, you won't be able to Capture or Cancel the request.", WC_Pay360::TEXT_DOMAIN ), '<br/>', '<br/>' ),
				'class'       => 'chosen_select integration_hosted_cashier pay360_integration',
				'css'         => 'min-width:350px;',
				'options'     => array(
					'capture' => 'Capture',
					'auth'    => 'Authorization Only',
				),
				'default'     => 'capture',
			),
			
			'hosted_cashier_skin_default' => array(
				'title'       => __( 'Payment Template Skin', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'select',
				'description' => sprintf( __( 'The ID of the skin used to drive look and feel for this session. Go through the %sCustomise hosted look and feel%s guide for more information.', WC_Pay360::TEXT_DOMAIN ), '<a href="http://docs.pay360.com/customise-look-and-feel/">', '</a>' ),
				'class'       => 'chosen_select integration_hosted_cashier pay360_integration',
				'css'         => 'min-width:350px;',
				'options'     => array(
					'2'     => 'v2 default skin (id: 2)',
					'1'     => 'v1 default skin (id: 1)',
					'10'    => 'v1 skin with address fields (id: 10)',
					'11'    => 'v1 skin with address fields – light version (id: 11)',
					'other' => 'Enter Your Custom Skin?',
				),
				'default'     => '2',
			),
			
			'hosted_cashier_skin' => array(
				'title'       => __( 'Enter Your Custom Skin ID', \WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter here your own uploaded skin ID.', \WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'hosted_cashier_iframe_section' => array(
				'title'       => __( 'Iframe Settings', \WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'hosted_cashier_use_iframe' => array(
				'title'       => __( 'Enable Iframe', \WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Enable iframe to have the customer payment form display in an Iframe on your site. If disabled, your customers will be redirected to Pay360 payment page.', \WC_Pay360::TEXT_DOMAIN ),
				
				'default' => 'no',
				'class'   => 'integration_hosted_cashier pay360_integration',
			),
			
			'hosted_cashier_iframe_width' => array(
				'title'       => __( 'Iframe Width', \WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Width of the iframe window. Enter only numbers in pixels (i.e. 700) or you can enter number in percentage but you need to suffix it with "%" (i.e. 55%).', \WC_Pay360::TEXT_DOMAIN ),
				'default'     => '700',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'hosted_cashier_iframe_height' => array(
				'title'       => __( 'Iframe Height', \WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Height of the iframe window. Entered can be a number in pixels (i.e. 850) or you can enter number in percentage but you need to suffix it with "%" (i.e. 55%).', \WC_Pay360::TEXT_DOMAIN ),
				'default'     => '1200',
				'class'       => 'integration_hosted_cashier pay360_integration',
			),
			
			'hosted_cashier_iframe_scroll' => array(
				'title'       => __( 'Iframe Scroll', \WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Should the iframe be scrollable or not. If scrollable, the customer will be able to scroll to the iframe to reach its borders.', \WC_Pay360::TEXT_DOMAIN ),
				
				'default' => 'no',
				'class'   => 'integration_hosted_cashier pay360_integration',
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
		if ( ! $this->get_gateway()->get_option( 'ashier_api_password' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the payment url
	 *
	 * @since 2.0
	 *
	 * @param $order
	 *
	 * @throws Exception
	 * @return mixed
	 */
	public function get_payment_url( $order ) {
		if ( ! $order instanceof \WC_Order ) {
			$order = wc_get_order( (int) $order );
		}
		
		// Load the Hosted class
		$request = new WC_Pay360_Hosted_Cashier_Request( $this->get_gateway() );
		
		// If order contains Pre-Orders and needs tokenization || Order total is 0
		if ( ( $this->order_contains_pre_order( $order ) && WC_Pre_Orders_Order::order_requires_payment_tokenization( $order ) )
		     || 0 >= $order->get_total()
		) {
			$parameters = $request->get_verification_parameters( $order );
			$url        = $request->get_verification_url( $parameters );
		} else {
			// Otherwise we need to charge the order amount
			$parameters = $request->get_payment_parameters( $order );
			$url        = $request->get_payment_url( $parameters );
		}
		
		return $url;
	}
	
	/**
	 * Retrieves the payment url and loads the iframe template
	 *
	 * @since 2.0
	 *
	 * @param $order
	 *
	 * @throws Exception
	 * @return string
	 */
	public function get_form( $order ) {
		$url = $this->get_payment_url( $order );
		
		if ( ! $this->use_iframe() ) {
			return $url;
		}
		
		return $this->load_iframe_template( $url );
	}
	
	/**
	 * Loads the iframe template
	 *
	 * @since 2.0
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function load_iframe_template( $url ) {
		ob_start();
		
		$width  = $this->get_iframe_dimension( $this->get_gateway()->get_option( 'hosted_cashier_iframe_width' ), '700' );
		$height = $this->get_iframe_dimension( $this->get_gateway()->get_option( 'hosted_cashier_iframe_height' ), '850' );
		
		wc_get_template(
			'pay360/iframe.php',
			array(
				'location' => $url,
				'width'    => $width,
				'height'   => $height,
				'scroll'   => $this->get_gateway()->get_option( 'hosted_cashier_iframe_scroll' ),
			),
			'',
			WC_Pay360::plugin_path() . '/templates/'
		);
		
		return ob_get_clean();
	}
	
	/**
	 * Performs checks on the set iframe dimensions and returns the value or the default
	 *
	 * @since 2.0
	 *
	 * @param string $dimension The string dimension in pixels (111) or in percentage (55%)
	 * @param string $default   Default value in pixels only
	 *
	 * @return int|string
	 */
	public function get_iframe_dimension( $dimension, $default = '0' ) {
		if ( substr( $dimension, - 1 ) == '%' ) {
			if ( is_numeric( substr( $dimension, 0, ( strlen( $dimension ) - 1 ) ) ) ) {
				$value = $dimension;
			} else {
				$value = $default;
			}
		} elseif ( is_numeric( $dimension ) ) {
			$value = $dimension;
		} else {
			$value = $default;
		}
		
		return $value;
	}
}