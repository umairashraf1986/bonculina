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
class WC_Pay360_Hosted_Ima extends WC_Pay360_Abstract_Integration {
	
	/**
	 * WC_Pay360_Hosted_Ima constructor.
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
			'intInstID'  => array(
				'title'       => __( 'Installation ID', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter here your preferred installation number, which is obtained via the Merchant Extranet: Account Management > Installations.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => '',
				'class'       => 'integration_hosted_ima pay360_integration',
			),
			'return_URL' => array(
				'title'       => __( 'Return/Response URL', WC_Pay360::TEXT_DOMAIN ),
				'label'       => sprintf( __( 'URL to copy: %s', WC_Pay360::TEXT_DOMAIN ), '<strong>' . WC()->api_request_url( WC_Pay360::get_gateway_class() ) . '</strong>' ),
				'css'         => 'display:none;',
				'type'        => 'checkbox',
				'description' => __( "The PRN is enabled and configured on a per-installation basis via the Merchant Extranet. Go to <strong>Account Management -> Installations -> select the relevant (Hosted +IMA) installation -> Copy and Paste the URL to 'Response URL' and to the 'Return URL' fields</strong>.", WC_Pay360::TEXT_DOMAIN ),
				'default'     => 'yes',
				'class'       => 'integration_hosted_ima pay360_integration',
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
		if ( ! $this->get_gateway()->intInstID ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the integration form
	 *
	 * @since 2.0
	 *
	 * @param $order_id
	 *
	 * @return string
	 */
	public function get_form( $order_id ) {
		$order = wc_get_order( $order_id );
		// Load the IMA class
		$request     = new WC_Pay360_Hosted_IMA_Request( $this->get_gateway() );
		$parameters  = $request->get_parameters( $order );
		$form_fields = $request->get_form_input_fields( $parameters );
		$url         = $request->get_form_url();
		ob_start();
		
		echo $request->get_form( $order, $form_fields, $url );
		
		return ob_get_clean();
	}
}