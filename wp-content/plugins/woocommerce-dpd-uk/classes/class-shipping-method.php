<?php
/**
 * Class WPDesk_WooCommerce_DPD_UK_Shipping_Method
 *
 * @package WooCommerce DPD UK
 */

/**
 * Shipping method settings.
 */
class WPDesk_WooCommerce_DPD_UK_Shipping_Method extends WC_Shipping_Method {

	const SETTING_API_TYPE       = 'api_type';
	const SETTING_USERNAME       = 'username';
	const SETTING_PASSWORD       = 'password';
	const SETTING_ACCOUNT_NUMBER = 'account_number';

	const REFERENCE_MAX_LENGTH = 25;
	const DELIVERY_MAX_LENGTH  = 50;

	const SETTING_FIELD_LABEL_ACTION = 'label_action';
	const SETTING_FIELD_LABEL_FORMAT = 'label_format';
	const LABEL_FORMAT_HTML          = 'HTML';
	const LABEL_ACTION_DOWNLOAD      = 'download';
	const LABEL_ACTION_OPEN          = 'open';

	const SETTING_INVOICE_COUNTRY_OF_ORIGIN = 'invoice_country_of_origin';
	const SETTING_INVOICE_CUSTOMS_NUMBER = 'invoice_customs_number';
	const SETTING_INVOICE_EXPORT_REASON = 'invoice_export_reason';
	const SETTING_INVOICE_TERMS_OF_DELIVERY = 'invoice_terms_of_delivery';
	const SETTING_INVOICE_VAT_NUMBER = 'invoice_vat_number';
	const SETTING_INVOICE_TYPE = 'invoice_type';

	const SETTING_PRODUCT_COUNTRY_OF_ORIGIN_ATTRIBUTE = 'product_country_of_origin_attribute';
	const SETTING_PRODUCT_COUNTRY_OF_ORIGIN_DEFAULT = 'product_country_of_origin_default';

	const SETTING_PRODUCT_HARMONISED_CODE_ATTRIBUTE = 'product_harmonised_code_attribute';
	const SETTING_PRODUCT_HARMONISED_CODE_DEFAULT = 'product_harmonised_code_default';

	const SETTING_HOUR_CHANGING_SHIPMENT_DATE = 'hour_changing_shipment_date';

	/**
	 * @var false|WPDesk_WooCommerce_DPD_UK_API
	 */
	private $api = false;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $account_number;


	/**
	 * Constructor for your shipping class
	 *
	 * @param int $instance_id .
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {

		parent::__construct( $instance_id );
		$this->id = 'dpd_uk';

		$this->method_title       = __( 'DPD UK', 'woocommerce-dpd-uk' );
		$this->method_description = __(
			'DPD UK WooCommerce Integration. <a href="https://docs.flexibleshipping.com/collection/1-woocommerce-dpd-uk/?utm_source=dpd-uk-settings&utm_medium=link&utm_campaign=dpd-uk-docs-link" target="_blank">Refer to the instruction manual &rarr;</a>',
			'woocommerce-dpd-uk'
		);

		$this->enabled = 'yes';
		$this->title   = __( 'DPD UK', 'woocommerce-dpd-uk' );

		$this->settings['enabled'] = 'yes';

		$this->supports[] = 'flexible-shipping';

		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

	}

	/**
	 * Init your settings
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		$this->init_settings();
		$this->init_form_fields();

		$this->username       = $this->get_option( 'username' );
		$this->password       = $this->get_option( 'password' );
		$this->account_number = $this->get_option( 'account_number' );

	}

	/**
	 * @return WPDesk_WooCommerce_DPD_UK_API
	 */
	public function get_api() {
		if ( ! $this->api ) {
			$this->api = new WPDesk_WooCommerce_DPD_UK_API( $this->settings );
		}

		return $this->api;
	}

	/**
	 * @return string
	 */
	public function get_api_type() {
		return $this->get_api()->get_api_type();
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields() {
		$sender_country_options = array();

		$countries                    = WC()->countries->get_countries();
		$sender_country_options['GB'] = $countries['GB'];
		$sender_country_options['IE'] = $countries['IE'];

		$order_statuses = wc_get_order_statuses();

		$flexible_printing = apply_filters( 'flexible_printing', false );

		$auto_print_description       = '';
		$auto_print_custom_attributes = array();

		if ( $flexible_printing ) {
			if ( 'yes' === $this->get_option( 'auto_print', '' ) ) {
				$flexible_printing_integration_url = apply_filters( 'flexible_printing_integration_url', 'dpd_uk' );
				$auto_print_description            = sprintf(
					// Translators: Flexible Printing integration setting link.
					__(
						'Automatically print on the thermal printer with %1$sPrint Node%2$s using CLP or EPL format. To change printing settings %3$sclick here%4$s.',
						'woocommerce-dpd-uk'
					),
					'<a href="https://www.printnode.com/en/docs/introduction" target="_blank">',
					'</a>',
					'<a target="_blank" href="' . $flexible_printing_integration_url . '">',
					'</a>'
				);
			} else {
				$auto_print_description = __(
					'Printing settings will be available after enabling and saving settings.',
					'woocommerce-dpd-uk'
				);
			}
		} else {
			$this->settings['auto_print'] = 'no';
			$flexible_printing_buy_url    = 'https://flexibleshipping.com/products/flexible-printing-woocommerce/?utm_source=dpd-uk-settings&utm_medium=link&utm_campaign=dpd-uk-flexible-printing';
			$auto_print_description       = sprintf(
				// Translators: Flexible Printing link.
				__(
					'Automatically print on the thermal printer with %1$sPrint Node%2$s using CLP or EPL format. Buy %3$sFlexible Printing &rarr;%4$s',
					'woocommerce-dpd-uk'
				),
				'<a href="https://www.printnode.com/en/docs/introduction" target="_blank">',
				'</a>',
				'<a href="' . $flexible_printing_buy_url . '" target="_blank">',
				'</a>'
			);
			$auto_print_custom_attributes = array( 'disabled' => 'disabled' );
		}

		$api_type_default = WPDesk_WooCommerce_DPD_UK_API::API_TYPE_DPD_LOCAL;
		if ( ! empty( $this->settings['username'] ) ) {
			$api_type_default = WPDesk_WooCommerce_DPD_UK_API::API_TYPE_DPD;
		}

		$this->form_fields = array(
			array(
				'title'       => __( 'Account', 'woocommerce-dpd-uk' ),
				'type'        => 'title',
				'description' => __( 'Select DPD API and enter MyDPD account details.', 'woocommerce-dpd-uk' ),
			),
			'api_type'                       => array(
				'title'   => __( 'API', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'default' => $api_type_default,
				'options' => array(
					WPDesk_WooCommerce_DPD_UK_API::API_TYPE_DPD_LOCAL => __( 'DPD Local', 'woocommerce-dpd-uk' ),
					WPDesk_WooCommerce_DPD_UK_API::API_TYPE_DPD       => __( 'DPD', 'woocommerce-dpd-uk' ),
				),
			),
			'username'                       => array(
				'title'             => __( 'Username', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'password'                       => array(
				'title'             => __( 'Password', 'woocommerce-dpd-uk' ),
				'type'              => 'password',
				'custom_attributes' => array(
					'required'     => 'required',
					'autocomplete' => 'new-password',
				),
			),
			'account_number'                 => array(
				'title'             => __( 'Account number', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'connection_status'               => array(
				'title'       => __( 'Connection status', 'woocommerce-dpd-uk' ),
				'type'        => 'connection_status',
				'description' => __( 'Connection status.', 'woocommerce-dpd-uk' ),
				'class'       => 'dpd_uk_connection_status',
			),
			array(
				'title' => __( 'Creating Shipments', 'woocommerce-dpd-uk' ),
				'type'  => 'title',
				'class' => 'dpd_uk_settings_shipments',
			),
			self::SETTING_HOUR_CHANGING_SHIPMENT_DATE => array(
				'title'       => __( 'Shipment Date Switch', 'woocommerce-dpd-uk' ),
				'type'        => 'select',
				'class'       => 'dpd_uk_settings_shipments',
				'default'     => '',
				'options'     => array(
					''   => __( 'Select hour', 'woocommerce-dpd-uk' ),
					'10' => __( '10 am', 'woocommerce-dpd-uk' ),
					'11' => __( '11 am', 'woocommerce-dpd-uk' ),
					'12' => __( '12 pm', 'woocommerce-dpd-uk' ),
					'13' => __( '1 pm', 'woocommerce-dpd-uk' ),
					'14' => __( '2 pm', 'woocommerce-dpd-uk' ),
					'15' => __( '3 pm', 'woocommerce-dpd-uk' ),
					'16' => __( '4 pm', 'woocommerce-dpd-uk' ),
					'17' => __( '5 pm', 'woocommerce-dpd-uk' ),
					'18' => __( '6 pm', 'woocommerce-dpd-uk' ),
					'19' => __( '7 pm', 'woocommerce-dpd-uk' ),
					'20' => __( '8 pm', 'woocommerce-dpd-uk' ),
					'21' => __( '9 pm', 'woocommerce-dpd-uk' ),
					'22' => __( '10 pm', 'woocommerce-dpd-uk' ),
					'23' => __( '11 pm', 'woocommerce-dpd-uk' ),
				),
				'description' => __( 'Select the hour after which the date of shipment will be set for the next day.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
			'auto_create'                    => array(
				'title'       => __( 'Create shipments', 'woocommerce-dpd-uk' ),
				'type'        => 'select',
				'class'       => 'dpd_uk_settings_shipments',
				'default'     => 'manual',
				'options'     => array(
					'manual' => __( 'Manually', 'woocommerce-dpd-uk' ),
					'auto'   => __( 'Automatically', 'woocommerce-dpd-uk' ),
				),
				'description' => __( 'Choose to create shipments manually or automatically based on the order status.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
			'order_status'                   => array(
				'title'       => __( 'Order status', 'woocommerce-dpd-uk' ),
				'type'        => 'select',
				'class'       => 'dpd_uk_settings_shipments',
				'default'     => 'wc-completed',
				'options'     => $order_statuses,
				'description' => __( 'Select order status for automatic shipment creation.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
			'complete_order'                 => array(
				'title'       => __( 'Complete order', 'woocommerce-dpd-uk' ),
				'type'        => 'checkbox',
				'class'       => 'dpd_uk_settings_shipments',
				'label'       => __( 'Enable', 'woocommerce-dpd-uk' ),
				'default'     => 'no',
				'description' => __( 'Automatically change order status to completed after creating shipment.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
			array(
				'title' => __( 'Printing options', 'woocommerce-dpd-uk' ),
				'type'  => 'title',
				'class' => 'dpd_uk_settings_printing',
			),
			self::SETTING_FIELD_LABEL_FORMAT => array(
				'title'   => __( 'Label format', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_printing',
				'options' => array(
					self::LABEL_FORMAT_HTML => __( 'HTML', 'woocommerce-dpd-uk' ),
					'CLP'                   => __( 'CLP', 'woocommerce-dpd-uk' ),
					'EPL'                   => __( 'EPL', 'woocommerce-dpd-uk' ),
				),
				'default' => self::LABEL_FORMAT_HTML,
			),
			self::SETTING_FIELD_LABEL_ACTION => array(
				'title'   => __( '"Get label" button behavior', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_printing',
				'options' => array(
					self::LABEL_ACTION_DOWNLOAD => __( 'Download a label file', 'woocommerce-dpd-uk' ),
					self::LABEL_ACTION_OPEN     => __( 'Open in a new tab', 'woocommerce-dpd-uk' ),
				),
				'default' => self::LABEL_ACTION_DOWNLOAD,
			),
			'auto_print'                     => array(
				'title'             => __( 'Printing', 'woocommerce-dpd-uk' ),
				'label'             => __( 'Enable automatic printing', 'woocommerce-dpd-uk' ),
				'type'              => 'checkbox',
				'class'             => 'dpd_uk_settings_printing',
				'description'       => $auto_print_description,
				'custom_attributes' => $auto_print_custom_attributes,
				'default'           => 'no',
			),
			array(
				'title' => __( 'Email notifications', 'woocommerce-dpd-uk' ),
				'type'  => 'title',
				'class' => 'dpd_uk_settings_email_notifications',
			),
			'email_tracking_number'          => array(
				'title'       => __( 'Tracking number', 'woocommerce-dpd-uk' ),
				'label'       => __( 'Add tracking link to customer emails', 'woocommerce-dpd-uk' ),
				'type'        => 'checkbox',
				'class'       => 'dpd_uk_settings_email_notifications',
				'description' => __( 'Add DPD tracking number and link to the WooCommerce confirmation emails.', 'woocommerce-dpd-uk' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Sender details', 'woocommerce-dpd-uk' ),
				'type'        => 'title',
				'class'       => 'dpd_uk_settings_sender_details',
				'description' => __( 'Sender details are required to create shipments and list services.', 'woocommerce-dpd-uk' ),
			),
			'sender_organisation'            => array(
				'title'             => __( 'Organisation', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'class'             => 'dpd_uk_settings_sender_details',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'sender_name'                    => array(
				'title'             => __( 'Name and Surname', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'class'             => 'dpd_uk_settings_sender_details',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'sender_street'                  => array(
				'title'             => __( 'Street', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'class'             => 'dpd_uk_settings_sender_details',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'sender_postcode'                => array(
				'title'             => __( 'Postcode', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'class'             => 'dpd_uk_settings_sender_details',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'sender_town'                    => array(
				'title' => __( 'Town', 'woocommerce-dpd-uk' ),
				'type'  => 'text',
				'class' => 'dpd_uk_settings_sender_details',
			),
			'sender_locality'                => array(
				'title' => __( 'Locality', 'woocommerce-dpd-uk' ),
				'type'  => 'text',
				'class' => 'dpd_uk_settings_sender_details',
			),
			'sender_county'                  => array(
				'title' => __( 'County', 'woocommerce-dpd-uk' ),
				'type'  => 'text',
				'class' => 'dpd_uk_settings_sender_details',
			),
			'sender_country'                 => array(
				'title'             => __( 'Country', 'woocommerce-dpd-uk' ),
				'type'              => 'select',
				'class'             => 'dpd_uk_settings_sender_details',
				'options'           => $sender_country_options,
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'sender_phone'                   => array(
				'title'             => __( 'Phone', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'class'             => 'dpd_uk_settings_sender_details',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'sender_email'                  => array(
				'title'             => __( 'E-mail', 'woocommerce-dpd-uk' ),
				'type'              => 'text',
				'class'             => 'dpd_uk_settings_sender_details',
				'default'           => get_option( 'admin_email' ),
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			array(
				'title'       => __( 'Invoice details', 'woocommerce-dpd-uk' ),
				'type'        => 'title',
				'class'       => 'dpd_uk_settings_invoice_details',
				'description' => __( 'Invoice details are required to create international shipments.', 'woocommerce-dpd-uk' ),
			),
			self::SETTING_INVOICE_COUNTRY_OF_ORIGIN => array(
				'title'       => __( 'Country of Origin', 'woocommerce-dpd-uk' ),
				'type'        => 'select',
				'class'       => 'dpd_uk_settings_invoice_details select2',
				'options'     => WC()->countries->get_countries(),
				'description' => __( 'Declare which country your products were manufactured or produced in.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
			self::SETTING_INVOICE_CUSTOMS_NUMBER            => array(
				'title'       => __( 'FDA Registration No.', 'woocommerce-dpd-uk' ),
				'type'        => 'text',
				'class'       => 'dpd_uk_settings_invoice_details',
				'desc_tip'    => true,
				'description' => __( 'Only required for Food and Drugs shipments to USA.', 'woocommerce-dpd-uk' ),
			),
			self::SETTING_INVOICE_EXPORT_REASON => array(
				'title'   => __( 'Reason for Export', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_invoice_details',
				'options' => array(
					'01' => __( 'Sale', 'woocommerce-dpd-uk' ),
					'02' => __( 'Return/Replacement', 'woocommerce-dpd-uk' ),
					'03' => __( 'Gift', 'woocommerce-dpd-uk' ),
				),
				'default' => '01',
			),
			self::SETTING_INVOICE_TERMS_OF_DELIVERY            => array(
				'title'       => __( 'Terms of Delivery', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_invoice_details',
				'options' => array(
					'DAP' => __( 'DAP', 'woocommerce-dpd-uk' ),
					'DT1' => __( 'DT1 (DDP)', 'woocommerce-dpd-uk' ),
				),
				'default' => 'DAP',
				'desc_tip'    => false,
				'description' => sprintf(
					// Translators: docs link.
					__( 'Choose the form of international tax and duty settlement.%1$sLearn more about the %2$sdifference between DAP and DT1 (DDP) →%3$s', 'woocommerce-dpd-uk' ),
					'<br/>',
					'<a target="_blank" href="https://docs.flexibleshipping.com/article/58-woocommerce-dpd-uk-configuration#terms-of-delivery">',
					'</a>'
				),
			),
			self::SETTING_INVOICE_VAT_NUMBER            => array(
				'title'       => __( 'Shipper\'s EORI No', 'woocommerce-dpd-uk' ),
				'type'        => 'text',
				'class'       => 'dpd_uk_settings_invoice_details',
				'desc_tip'    => true,
				'description' => __( 'Shipper\'s VAT No/EORI number - a mandatory requirement to fill in. If not EORI-registered, type in GBUNREG.', 'woocommerce-dpd-uk' ),
				'default'     => 'GBUNREG',
			),
			self::SETTING_INVOICE_TYPE                        => array(
				'title'       => __( 'Invoice Type', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_invoice_details',
				'options' => array(
					'1' => __( 'Proforma', 'woocommerce-dpd-uk' ),
					'2' => __( 'Commercial', 'woocommerce-dpd-uk' ),
				),
				'default' => '1',
			),
			array(
				'title'       => __( 'Products\' details', 'woocommerce-dpd-uk' ),
				'type'        => 'title',
				'class'       => 'dpd_uk_settings_product_details',
				'description' => __( 'Products\' details are required to create international shipments.', 'woocommerce-dpd-uk' ),
			),
			self::SETTING_PRODUCT_COUNTRY_OF_ORIGIN_ATTRIBUTE => array(
				'title'       => __( 'Country of Manufacture attribute', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_product_details',
				'options' => array(),
				'default' => '1',
				'desc_tip'    => false,
				'description' => sprintf(
					// Translators: docs link.
					__( 'Select your products\' \'Country of Manufacture\' product attribute.%1$sDPD UK API accepts only the countries\' ISO codes as the products\'%2$s\'Country of Manufacture\' attribute\'s values.%3$sCheck %4$show to configure them properly →%5$s', 'woocommerce-dpd-uk' ),
					'<br/>',
					'<br/>',
					'<br/>',
					'<a target="_blank" href="https://docs.flexibleshipping.com/article/58-woocommerce-dpd-uk-configuration#country-of-manufacture-attribute">',
					'</a>'
				),
			),
			self::SETTING_PRODUCT_COUNTRY_OF_ORIGIN_DEFAULT   => array(
				'title'       => __( 'Default Country of Manufacture', 'woocommerce-dpd-uk' ),
				'type'        => 'select',
				'class'       => 'dpd_uk_settings_product_details select2',
				'options'     => WC()->countries->get_countries(),
				'description' => __( 'Country used by default when the product has no \'Country of Manufacture\' attribute assigned.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
			self::SETTING_PRODUCT_HARMONISED_CODE_ATTRIBUTE   => array(
				'title'       => __( 'Commodity Code attribute', 'woocommerce-dpd-uk' ),
				'type'    => 'select',
				'class'   => 'dpd_uk_settings_product_details',
				'options' => array(),
				'default' => '1',
				'desc_tip'    => true,
				'description' => __( 'Select your products\' \'Commodity Code\' product attribute.', 'woocommerce-dpd-uk' ),
			),
			self::SETTING_PRODUCT_HARMONISED_CODE_DEFAULT     => array(
				'title'       => __( 'Default Commodity Code', 'woocommerce-dpd-uk' ),
				'type'        => 'text',
				'class'       => 'dpd_uk_settings_product_details',
				'description' => __( 'Code used by default when the product has no \'Commodity Code\' attribute assigned.', 'woocommerce-dpd-uk' ),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * @return bool
	 */
	private function is_api_required_data_provided() {
		return isset( $this->username ) && isset( $this->password ) && '' !== $this->username && '' !== $this->password;
	}

	/**
	 * @param array $form_fields .
	 * @param bool  $echo .
	 *
	 * @return string|void
	 */
	public function generate_settings_html( $form_fields = array(), $echo = false ) {
		$api_user_data         = $this->is_api_required_data_provided();
		$api_message           = $this->get_api_error_connection_message();
		$api_connection_exists = '' === $api_message && $api_user_data;

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		$form_fields[ self::SETTING_PRODUCT_COUNTRY_OF_ORIGIN_ATTRIBUTE ]['options'] = $this->get_attributes_as_options_array();
		$form_fields[ self::SETTING_PRODUCT_HARMONISED_CODE_ATTRIBUTE ]['options'] = $this->get_attributes_as_options_array();

		echo '<div id="dpd_uk_settings">';
		parent::generate_settings_html( $form_fields );
		echo '</div>';

		$api_connection_error_description = sprintf( __( 'If you can not connect to the API, contact with your DPD UK account manager.', 'woocommerce-dpd-uk' ) );

		$api_connection_no_user_data_description = sprintf(
			// Translators: link.
			__( 'If you do not have access to the API check our guide %1$sHow to get API access?%2$s', 'woocommerce-dpd-uk' ),
			'<a href="https://docs.flexibleshipping.com/article/106-woocommerce-dpd-uk-how-to-get-api-access/" target="_blank">',
			'</a>'
		);

		include( 'views/settings-script.php' );
	}

	/**
	 * @return array
	 */
	private function get_attributes_as_options_array() {
		$options = array( '' => __( 'No attribute', 'woocommerce-dpd-uk' ) );
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $attribute_taxonomies as $tax ) {
			$options[ wc_attribute_taxonomy_name( $tax->attribute_name ) ] = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
		}

		return $options;
	}

	/**
	 * @param string $key .
	 * @param array  $data .
	 *
	 * @return string
	 */
	public function generate_connection_status_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		include __DIR__ . '/views/settings-field-connection-status_html.php';

		return ob_get_clean();
	}

	/**
	 * @return string returns '' if there was no error
	 */
	public function get_api_error_connection_message() {
		if ( $this->is_api_required_data_provided() ) {
			$api = $this->get_api();
			try {
				$api->ping();
			} catch ( Exception $e ) {
				return $api->translate_messages( $e->getMessage() );
			}
		}

		return '';
	}

	/**
	 * @param string $message .
	 */
	public function display_connection_error_notice( $message ) {
		include __DIR__ . '/views/settings-notice-api-connection.php';
	}

	/**
	 * @param array $package .
	 */
	public function calculate_shipping( $package = array() ) {
	}

	/**
	 * .
	 */
	public function process_admin_options() {
		parent::process_admin_options();
		$api = $this->get_api();
		$api->clear_cache();
	}

}
