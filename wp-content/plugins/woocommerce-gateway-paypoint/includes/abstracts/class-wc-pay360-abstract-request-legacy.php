<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Request class to serve as a basis for legacy (PayPoint) integration requests.
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WC_Pay360_Abstract_Request_Legacy extends WC_Pay360_Abstract_Request {

	/**
	 * Init the class
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
	}

	/**
	 * Return the request form html input fields
	 *
	 * @since 2.0
	 *
	 * @param $parameters
	 *
	 * @return string
	 */
	public function get_form_input_fields( $parameters ) {
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Submitted parameters: ' . print_r( $parameters, true ) );

		$form_input_fields = array();
		foreach ( $parameters as $key => $value ) {
			$form_input_fields[] = '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
		}

		return implode( '', $form_input_fields );
	}

	/**
	 * Generate the Pay360 form
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $form_fields
	 * @param string   $url
	 *
	 * @return string
	 */
	public function get_form( $order, $form_fields, $url ) {
		$this->load_inline_js();

		return $this->get_form_output( $order, $url, $form_fields );
	}

	/**
	 * Load inline JS to automatically submit the form to Pay360
	 *
	 * @since 2.0
	 */
	public function load_inline_js() {
		wc_enqueue_js(
			'
			jQuery("body").block({
				message: "<img src=\"' . esc_url( WC_HTTPS::force_https_url( WC()->plugin_url() ) ) . '/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Pay360 to make payment.', WC_Pay360::TEXT_DOMAIN ) . '",
				overlayCSS:
				{
				    background: "#fff",
				    opacity: 0.6
				},
				css: {
				    padding:        20,
				    textAlign:      "center",
				    color:          "#555",
				    border:         "3px solid #aaa",
				    backgroundColor:"#fff",
				    cursor:         "wait",
				    lineHeight:		"32px"
				}
			    });
			jQuery("#submit_pay360_payment_form").click();
		'
		);
	}

	/**
	 * Return the gateway testmode status
	 *
	 * @since 2.0
	 */
	public function get_testmode_status() {
		return $this->get_gateway()->testmode;
	}

	/**
	 * Return the complete payment html form
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order       WC order object
	 * @param string   $url         URL to be submitted to
	 * @param string   $form_fields The form fields
	 *
	 * @return string
	 */
	public function get_form_output( $order, $url, $form_fields ) {
		return '<form action="' . esc_url( $url ) . '" method="post" id="pay360_payment_form" target="_top">
			' . $form_fields . '
			<input type="submit" class="button-alt" id="submit_pay360_payment_form" value="' . __( 'Pay via Pay360', WC_Pay360::TEXT_DOMAIN ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', WC_Pay360::TEXT_DOMAIN ) . '</a>
		    </form>';
	}
}