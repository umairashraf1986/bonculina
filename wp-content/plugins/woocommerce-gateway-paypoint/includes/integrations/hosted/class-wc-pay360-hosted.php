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
class WC_Pay360_Hosted extends WC_Pay360_Abstract_Integration {
	
	/**
	 * WC_Pay360_Hosted constructor.
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
			'merchant_h'        => array(
				'title'       => __( 'Merchant ID', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter here your merchant ID. This is Pay360 username (usually six letters followed by two numbers).', WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted pay360_integration',
			),
			'remotepassword_h'  => array(
				'title'       => __( 'Remote Password', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'password',
				'description' => __( "Enter here the remote password used for md5 authantication from you to Pay360. The remote password can be configured from within the Secpay Merchant Extranet (Click on 'Change Remote Passwords' and select 'Remote' from the drop down list).", WC_Pay360::TEXT_DOMAIN ),
				'placeholder' => __( "Password won't appear here.", WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted pay360_integration',
			),
			'digest_key_h'      => array(
				'title'       => __( 'Digest Key', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'password',
				'description' => __( "Enter here the digest key used for md5 authantication from Pay360 to you. The digest key can be configured from within the Secpay Merchant Extranet (Click on 'Change Remote Passwords' and select 'Digest Key' from the drop down list).", WC_Pay360::TEXT_DOMAIN ),
				'placeholder' => __( "Password won't appear here.", WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted pay360_integration',
			),
			'transaction_type'  => array(
				'title'       => __( 'Transaction Type', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'select',
				'description' => __( 'Sale - will automatically charge the amount.<br/>Authorization - will only authorize the amount, you will need to manually capture it, from your Pay360 admin panel.', WC_Pay360::TEXT_DOMAIN ),
				'options'     => array( 'auth' => 'Authorization only', 'sale' => 'Sale' ),
				'class'       => 'chosen_select integration_hosted pay360_integration',
				'css'         => 'min-width:350px;',
				'default'     => 'sale',
			),
			'pp_logo_h'         => array(
				'title'       => __( 'Payment Page Logo', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( "Your logo must be 750 pixels in width by 100 pixels in height and must be sent to support@paypoint.net with your account ID and a message to tell them to add this logo to your payment page. Once they have uploaded the image to their secure servers and let you know it is available, you can paste the absolute URL link to it. Example: https://www.secpay.com/users/abcdef01/logo.jpg", WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted pay360_integration',
			),
			'mail_customer'     => array(
				'title'       => __( 'Send Confirmation Email to Cardholder', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Enable if you want Pay360 to send an order confirmation email to the cardholder.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => 'yes',
				'class'       => 'integration_hosted pay360_integration',
			),
			'risk_title'        => array(
				'title'       => __( '3D Secure Settings', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted pay360_integration',
			),
			'enable_3d_secure'  => array(
				'title'       => __( 'Enable 3D Secure', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'default'     => 'yes',
				'description' => __( 'This option will send the required information to Pay360 in order to take advantage of its 3D Secure feature. You need to enable this feature with Pay360 as well.', WC_Pay360::TEXT_DOMAIN ),
				'class'       => 'integration_hosted pay360_integration',
			),
			'mpi_merchant_name' => array(
				'title'       => __( 'Merchant Name', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'Enter here the merchant name to be displayed on the authentication page. Up to 25 characters.', WC_Pay360::TEXT_DOMAIN ),
				'class'       => 'integration_hosted pay360_integration',
			),
			'mpi_merchant_url'  => array(
				'title'       => __( 'Merchant URL', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'Enter here a fully qualified URL of your merchant website. Please enter the URL with the http:// in front. ( Example: http://www.example.com )', WC_Pay360::TEXT_DOMAIN ),
				'class'       => 'integration_hosted pay360_integration',
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
		if ( ! $this->gateway->merchant_h ) {
			return false;
		}
		if ( ! $this->gateway->remotepassword_h ) {
			return false;
		}
		if ( ! $this->gateway->digest_key_h ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the payment form
	 *
	 * @since 2.0
	 *
	 * @param $order_id
	 *
	 * @return string
	 */
	public function get_form( $order_id ) {
		$order = wc_get_order( $order_id );
		
		// Load the Hosted class
		$request     = new WC_Pay360_Hosted_Request( $this->get_gateway() );
		$parameters  = $request->get_parameters( $order );
		$form_fields = $request->get_form_input_fields( $parameters );
		$url         = $request->get_form_url();
		
		ob_start();
		
		echo $request->get_form( $order, $form_fields, $url );
		
		return ob_get_clean();
	}
}