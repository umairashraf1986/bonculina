<?php
/*
 * Main Pay360 Payment class
 *
 * Author: VanboDevelops
 * Author URI: http://www.vanbodevelops.com
 *
 *	Copyright: (c) 2012 - 2017 VanboDevelops
 *	License: GNU General Public License v3.0
 *	License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

class WC_Pay360_Gateway extends WC_Payment_Gateway {
	
	public $integration;
	public $testmode;
	public $intInstID;
	public $return_URL;
	public $merchant_h;
	public $remotepassword_h;
	public $digest_key_h;
	public $transaction_type_h;
	public $pp_logo_h;
	public $mail_customer;
	public $debug;
	public $enable_3d_secure;
	public $mpi_merchant_name;
	public $mpi_merchant_url;
	public $hosted_cashier_installation_id;
	public $hosted_cashier_api_username;
	public $hosted_cashier_api_password;
	protected static $loaded = false;
	
	public function __construct() {
		
		$this->id                 = 'pay360';
		$this->icon               = apply_filters( 'woocommerce_pay360_icon', WC_Pay360::plugin_url() . '/pay360-logo.png' );
		$this->has_fields         = false;
		$this->method_title       = __( 'Pay360', WC_Pay360::TEXT_DOMAIN );
		$this->method_description = __( 'Take a payment using Pay360', WC_Pay360::TEXT_DOMAIN );
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->integration = $this->get_option( 'integration', 'hosted_ima' );
		$this->title       = $this->get_option( 'title', 'Pay360' );
		$this->description = $this->get_option( 'description', 'Pay360 descriptions here.' );
		$this->testmode    = $this->get_option( 'testmode', 'test_success' );
		$this->enabled     = $this->get_option( 'enabled', 'yes' );
		$this->debug       = $this->get_option( 'debug', 'no' );
		
		// Hosted + IMA settings
		$this->intInstID  = $this->get_option( 'intInstID' );
		$this->return_URL = $this->get_option( 'return_URL', 'yes' );
		
		// Hosted settings
		$this->merchant_h         = $this->get_option( 'merchant_h' );
		$this->remotepassword_h   = $this->get_option( 'remotepassword_h' );
		$this->digest_key_h       = $this->get_option( 'digest_key_h' );
		$this->transaction_type_h = $this->get_option( 'transaction_type', 'sale' );
		$this->pp_logo_h          = $this->get_option( 'pp_logo_h' );
		$this->mail_customer      = $this->get_option( 'mail_customer' );
		$this->enable_3d_secure   = $this->get_option( 'enable_3d_secure', 'no' );
		$this->mpi_merchant_name  = $this->get_option( 'mpi_merchant_name' );
		$this->mpi_merchant_url   = $this->get_option( 'mpi_merchant_url' );
		
		// Hosted Cashier API settings
		$this->hosted_cashier_installation_id = $this->get_option( 'hosted_cashier_installation_id' );
		$this->hosted_cashier_api_username    = $this->get_option( 'hosted_cashier_api_username' );
		$this->hosted_cashier_api_password    = $this->get_option( 'hosted_cashier_api_password' );
		
		if ( 'hosted_cashier' == $this->integration ) {
			$this->supports[] = 'refunds';
			$this->supports[] = 'subscriptions';
			$this->supports[] = 'subscription_cancellation';
			$this->supports[] = 'subscription_reactivation';
			$this->supports[] = 'subscription_suspension';
			$this->supports[] = 'subscription_amount_changes';
			$this->supports[] = 'subscription_payment_method_change_customer';
			$this->supports[] = 'subscription_payment_method_change_admin';
			$this->supports[] = 'subscription_date_changes';
			$this->supports[] = 'multiple_subscriptions';
			$this->supports[] = 'pre-orders';
		}
		
		// Load
		$this->hooks();
	}
	
	/**
	 * Load the admin options.
	 * Loads the admin script as well
	 */
	public function admin_options() {
		wp_enqueue_script( 'pay360_admin' );
		parent::admin_options();
	}
	
	/**
	 * Load the hooks of the gateway
	 */
	public function hooks() {
		if ( true === self::$loaded ) {
			return;
		}
		
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			)
		);
		
		// Payment/Redirect page
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		
		if ( 'hosted' == $this->integration ) {
			$response = new WC_Pay360_Hosted_Response( $this );
			$response->hooks();
		} elseif ( 'hosted_ima' == $this->integration ) {
			$response = new WC_Pay360_Hosted_IMA_Response( $this );
			$response->hooks();
		} elseif ( 'hosted_cashier' == $this->integration ) {
			$response = new WC_Pay360_Hosted_Cashier_Response( $this );
			$response->hooks();
		}
		
		self::$loaded = true;
	}
	
	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
	public function is_available() {
		if ( $this->enabled == 'yes' ) {
			if ( 'hosted' == $this->integration ) {
				$integration = new WC_Pay360_Hosted( $this );
				
				return $integration->is_available();
			} elseif ( 'hosted_ima' == $this->integration ) {
				$integration = new WC_Pay360_Hosted_Ima( $this );
				
				return $integration->is_available();
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * @since 2.2.0
	 *
	 * @return array
	 */
	public function get_integration_options() {
		return array(
			'hosted_cashier' => __( 'Pay360 Hosted Cashier', WC_Pay360::TEXT_DOMAIN ),
			'hosted'         => __( 'Pay360 Hosted(PayPoint)', WC_Pay360::TEXT_DOMAIN ),
			'hosted_ima'     => __( 'Pay360 Hosted + IMA(PayPoint)', WC_Pay360::TEXT_DOMAIN ),
		);
	}
	
	/**
	 * Returns the method title
	 *
	 * @return string
	 */
	public function get_method_title() {
		return \WC_Pay360::get_field( $this->integration, $this->get_integration_options(), $this->method_title );
	}
	
	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		
		$options     = get_option( 'woocommerce_' . $this->id . '_settings', array() );
		$integration = \WC_Pay360::get_field( 'integration', $options, 'hosted_cashier' );
		
		// General settings
		$this->form_fields = array(
			'integration' => array(
				'title'             => __( 'Integration Method', WC_Pay360::TEXT_DOMAIN ),
				'type'              => 'select',
				'description'       => __( 'Choose your integration method and Save. You will be presented with the integration settings.', WC_Pay360::TEXT_DOMAIN ) . '<br/> <div class="pay360_warning"><p></p></div>',
				'options'           => $this->get_integration_options(),
				'class'             => 'chosen_select',
				'css'               => 'min-width:350px;',
				'default'           => 'hosted_cashier',
				'custom_attributes' => array(
					'data-initial-type' => $integration
				),
			),
			
			'enabled' => array(
				'title'   => __( 'Enable/Disable', WC_Pay360::TEXT_DOMAIN ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Pay360', WC_Pay360::TEXT_DOMAIN ),
				'default' => 'yes'
			),
			
			'testmode' => array(
				'title'       => __( 'Test Mode', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'select',
				'description' => '',
				'options'     => array(
					'live'         => 'Live',
					'test_success' => 'Testmode-Success',
					'test_fail'    => 'Testmode-Fail'
				),
				'class'       => 'chosen_select',
				'css'         => 'min-width:350px;',
				'default'     => 'test_success'
			),
			
			'title' => array(
				'title'       => __( 'Method Title', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => __( 'Pay360', WC_Pay360::TEXT_DOMAIN )
			),
			
			'description' => array(
				'title'       => __( 'Description', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', WC_Pay360::TEXT_DOMAIN ),
				'default'     => __( "Pay with credit or debit card.", WC_Pay360::TEXT_DOMAIN )
			),
		);
		
		if ( 'hosted' == $integration ) {
			$hosted            = new WC_Pay360_Hosted( $this );
			$this->form_fields += $hosted->get_settings();
		} elseif ( 'hosted_ima' == $integration ) {
			$hosted_ima        = new WC_Pay360_Hosted_Ima( $this );
			$this->form_fields += $hosted_ima->get_settings();
		} elseif ( 'hosted_cashier' == $integration ) {
			$hosted_ima        = new WC_Pay360_Hosted_Cashier( $this );
			$this->form_fields += $hosted_ima->get_settings();
		}
		
		// Debug settings
		$this->form_fields += array(
			'debug_title' => array(
				'title'       => __( 'Debug Settings', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'title',
				'description' => '',
			),
			
			'debug' => array(
				'title'       => __( 'Debug mode', WC_Pay360::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Debug Logging', WC_Pay360::TEXT_DOMAIN ),
				'default'     => 'no',
				'description' => sprintf( __( 'Debug log will provide you with most of the data and events generated by the payment process. Logged inside %s.' ), '<code>' . wc_get_log_file_path( 'pay360' ) . '</code>' ),
			),
		);
	} // End init_form_fields()
	
	/**
	 * Process the payment
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		$url   = $order->get_checkout_payment_url( true );
		if ( WC_Pay360::get_field( 'change_payment_method', $_GET, false ) ) {
			$url = add_query_arg( array( 'pay360_cpm' => true ), $url );
		}
		
		if ( 'hosted_cashier' == $this->integration && 'no' == $this->get_option( 'hosted_cashier_use_iframe' ) ) {
			$integration = new WC_Pay360_Hosted_Cashier( $this );
			$url         = $integration->get_payment_url( $order );
		}
		
		return array(
			'result'   => 'success',
			'redirect' => $url
		);
	}
	
	/**
	 * Generate the redirect form on the Pay Page
	 *
	 * @param int $order_id
	 */
	public function receipt_page( $order_id ) {
		try {
			echo apply_filters( 'wc_pay360_before_payment_form_html', '<p>' . __( 'Thank you for your order, please click the button below to pay.', WC_Pay360::TEXT_DOMAIN ) . '</p>', $this->integration, $this );
			
			$form = '';
			if ( 'hosted' == $this->integration ) {
				$integration = new WC_Pay360_Hosted( $this );
				$form        = $integration->get_form( $order_id );
			} elseif ( 'hosted_ima' == $this->integration ) {
				$integration = new WC_Pay360_Hosted_Ima( $this );
				$form        = $integration->get_form( $order_id );
			} elseif ( 'hosted_cashier' == $this->integration && 'yes' == $this->get_option( 'hosted_cashier_use_iframe' ) ) {
				$integration = new WC_Pay360_Hosted_Cashier( $this );
				$form        = $integration->get_form( $order_id );
			}
			
			echo $form;
		}
		catch ( Exception $e ) {
			wc_print_notice( $e->getMessage() . ' ' . __( 'Please reload the page or contact administrator.', WC_Pay360::TEXT_DOMAIN ), 'error' );
		}
	}
	
	/**
	 * Adds capture payment action to the order actions
	 *
	 * @param $actions
	 *
	 * @return mixed
	 */
	public function add_order_capture_action( $actions ) {
		
		/**
		 * @var WC_Order $theorder
		 */
		global $theorder;
		
		$method = $theorder->get_payment_method();
		if ( $this->id != $method ) {
			return $actions;
		}
		
		$pay360_order           = new \WcPay360\Pay360_Order( $theorder );
		$is_captured            = $pay360_order->get_is_payment_captured();
		$allowed_order_statuses = \WcPay360\Admin\Capture::get_capture_allowed_order_statuses();
		
		if ( $pay360_order->is_subscription() || true == $is_captured || ! in_array( $theorder->get_status(), $allowed_order_statuses ) ) {
			return $actions;
		}
		
		$authorized_amount = wc_format_decimal( $pay360_order->get_order_amount_authorized() );
		
		$actions['pay360_capture_payment'] = sprintf( __( 'Capture Payment (%s)', WC_Pay360::TEXT_DOMAIN ), get_woocommerce_currency_symbol( WC_Pay360_Compat::get_order_currency( $theorder ) ) . $authorized_amount );
		
		return $actions;
	}
	
	/**
	 * @param \WC_Order $order
	 */
	public function capture_payment( $order ) {
		
		$pay360_order = new \WcPay360\Pay360_Order( $order );
		$is_captured  = $pay360_order->get_is_payment_captured();
		
		// Bail, if we captured the amount already
		if ( true == $is_captured ) {
			return;
		}
		
		try {
			if ( 'hosted_cashier' == $this->get_option( 'integration' ) ) {
				$pay360_order = new \WcPay360\Pay360_Order( $order );
				
				$integration = new WC_Pay360_Cashier_Request( $this );
				$capture     = $integration->capture_payment( $order );
				
				// 1. See if the capture is successful and leave a note to the order that we did capture the amount
				if ( 'SUCCESS' == $capture->transaction->status ) {
					$pay360_order->save_transaction_id( $capture->transaction->transactionId );
					
					$order->add_order_note( sprintf( __( 'Transaction capture processed successfully. Transaction ID: %s.', WC_Pay360::TEXT_DOMAIN ), $capture->transaction->transactionId ) );
					
					// 2. If successful update the 'wc_pay360_is_payment_captured' order meta to 'true'
					$pay360_order->save_is_payment_captured( true );
					$pay360_order->save_order_amount_captured( $order->get_total() );
				} else {
					$error_message = isset( $capture->processing->authResponse->message ) ? $capture->processing->authResponse->message : '';
					$error_code    = isset( $capture->processing->authResponse->statusCode ) ? $capture->processing->authResponse->statusCode : '';
					
					throw new Exception( $error_message . ': ' . $error_code );
				}
			}
			
			return true;
		}
		catch ( Exception $e ) {
			$order->add_order_note( sprintf( __( 'Transaction capture failed. Error Message: %s', WC_Pay360::TEXT_DOMAIN ), $e->getMessage() ) );
			
			return $e->getMessage();
		}
	}
	
	/**
	 * Process automatic refunds
	 *
	 * @since 1.3
	 *
	 * @param int    $order_id
	 * @param null   $amount
	 * @param string $reason
	 *
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		
		if ( 'hosted_cashier' != $this->integration ) {
			return new WP_Error( 'pay360-not-supported-error', __( 'Refunds are not supported in you Pay360 integration type', WC_Pay360::TEXT_DOMAIN ) );
		}
		
		try {
			$order = wc_get_order( $order_id );
			
			$transaction_id = $order->get_transaction_id();
			
			// Bail, if there is no reference ID
			if ( '' == $transaction_id ) {
				throw new Exception( __( 'Error: Missing Transaction ID. The order does not have all required information to process a refund.', WC_Pay360::TEXT_DOMAIN ) );
			}
			
			// Load the api class
			$integration = new WC_Pay360_Cashier_Request( $this );
			$refund      = $integration->refund_payment( $order, $amount );
			
			// Run refund process
			// Process the refund response
			
			// 1. See if the capture is successful and leave a note to the order that we did capture the amount
			if ( 'SUCCESS' == $refund->transaction->status && 'REFUND' == $refund->transaction->type ) {
				// Debug log
				WC_Pay360_Debug::add_debug_log( 'Refund completed.' );
				
				// Add order note
				$order->add_order_note(
					sprintf(
						__(
							'Refunded %s. Refund ID: %s. %s',
							WC_Pay360::TEXT_DOMAIN
						),
						$amount,
						$refund->transaction->transactionId,
						( '' != $reason ) ? sprintf(
							__(
								'Credit Note: %s.'
							), $reason
						) : ''
					)
				);
				
				return true;
			} else {
				$error_message = isset( $refund->processing->authResponse->message ) ? $refund->processing->authResponse->message : '';
				$error_code    = isset( $refund->processing->authResponse->statusCode ) ? $refund->processing->authResponse->statusCode : '';
				
				// Add order note
				$order->add_order_note( sprintf( __( 'Refund declined. Message: %s. Code: %s', WC_Pay360::TEXT_DOMAIN ), $error_message, $error_code ) );
			}
		}
		catch ( Exception $ex ) {
			return new WP_Error( 'pay360-error', $ex->getMessage() );
		}
		
		return false;
	}
	
	/**
	 * Validate Password Field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 * We are not showing the password value to the front end,
	 * so we will overwrite the password validation, so we can update the password only when it is not empty.
	 * If left empty the password will be saved with the old value.
	 *
	 * @since 1.2
	 *
	 * @param mixed $key
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function validate_password_field( $key, $value = '' ) {
		$text      = $this->get_option( $key );
		$gen_field = $this->plugin_id . $this->id . '_' . $key;
		
		if ( isset( $_POST[ $gen_field ] ) && '' !== $_POST[ $gen_field ] ) {
			$text = wc_clean( stripslashes( trim( $_POST[ $gen_field ] ) ) );
		}
		
		return $text;
	}
	
	/**
	 * Generate Text Input HTML.
	 * Modify the text html to remove the password value from the front end.
	 *
	 * @since 1.2
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function generate_text_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => __( 'Password', WC_Pay360::TEXT_DOMAIN ),
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);
		
		$data  = wp_parse_args( $data, $defaults );
		$value = $this->get_option( $key );
		
		// Passwords will not have the exact password displayed,
		// so lets add a placeholder letting the user know that the password is set or not.
		$is_password_field = 'password' == $data['type'];
		If ( $is_password_field ) {
			$data['placeholder'] = sprintf( __( '%s is not set', WC_Pay360::TEXT_DOMAIN ), $data['title'] );
			if ( '' != $value ) {
				$data['placeholder'] = sprintf( __( '%s is set', WC_Pay360::TEXT_DOMAIN ), $data['title'] );
			}
			$value = '';
		}
		
		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>"
					       type="<?php echo esc_attr( $data['type'] ); ?>"
					       name="<?php echo esc_attr( $field ); ?>"
					       id="<?php echo esc_attr( $field ); ?>"
					       style="<?php echo esc_attr( $data['css'] ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					       placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>"
						<?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
} // End WC_Gateway_Pay360 class
