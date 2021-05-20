<?php
$onboardingEnabled = false;

return array(

    // General
    'general' => array(
        'title' => __('General', 'woocommerce'),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'Control method here.',
            'msunifaunonline'
        ),
    ),
    'enabled' => array(
        'title' => __(
            'Enabled',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
        'default' => 'yes'
    ),
    'pacsoft' => array(
        'default' => 'no',
        'description' => __(
            'Pacsoft Online uses another format for tracking-links.',
            'msunifaunonline'
        ),
        'title' => __(
            'Pacsoft Online',
            'msunifaunonline'
        ),
        'type' => 'checkbox'
    ),
    'ignore_calculated_dimensions' => array(
        'default' => 'no',
        'description' => __(
            'Ignore sending package dimensions to Unifaun when they are calculated automatically',
            'msunifaunonline'
        ),
        'title' => __(
            'Ignore calculated dimensions',
            'msunifaunonline'
        ),
        'type' => 'checkbox'
    ),
    'debug' => array(
        'title' => __(
            'Debug',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
        'description' => __(
            'Enable verbose order shiping information in admin and information logging.',
            'msunifaunonline'
        ),
        'default' => 'no'
    ),
    'tracking_link_in_emails' => array(
        'title' => __(
            'Include tracking link in e-mails',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
        'default' => 'yes'
    ),

    // API Credentials
    'api' => array(
        'title' => __(
            'API Credentials',
            'msunifaunonline'
        ),
        'type' => 'title',
        'default' => '',
        'description' => (!$onboardingEnabled
            || \Mediastrategi_UnifaunOnline::hasAccount()
            ? __(
                "Enter your credentials to the Unifaun Online REST API.",
                'msunifaunonline'
            ) : sprintf(
                __(
                    "Enter your credentials to the Unifaun Online REST API. If you don't have an account, create one by clicking <a href=\"%s\">here</a>.",
                    'msunifaunonline'
                ),
                admin_url('admin.php') . '?page=msunifaun_setup'
        )),
    ),
    'api_key_id' => array(
        'title' => __(
            'Key Id',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'api_key_secret' => array(
        'title' => __(
            'Key Secret',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'api_user_id' => array(
        'title' => __(
            'User Id',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'api_quick_id' => array(
        'title' => __(
            'Quick Id',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),

    // Custom Region Selector
    'custom_region_selector' => array(
        'title' => __(
            'Custom Region Selector',
            'msunifaunonline'
        ),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'You can optionally show a custom region selector in checkout.',
            'msunifaunonline'
        )
    ),
    'custom_region_show_title' => array(
        'title' => __(
            'Show title',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
    ),
    'custom_region_selector_zip' => array(
        'title' => __(
            'ZIP code selector',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
    ),

    // Automatic updates
    'update' => array(
        'title' => __('Automatic Updates', 'unifaunonline'),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'Do receive automatic updates, enter your license here.',
            'msunifaunonline'
        )
    ),
    'update_username' => array(
        'title' => __(
            'Username',
            'msunifaunonline'
        ),
        'type' => 'password',
    ),
    'update_password' => array(
        'title' => __(
            'Password',
            'msunifaunonline'
        ),
        'type' => 'password',
    ),

    // PDF
    'pdf' => array(
        'title' => __('Print Options', 'woocommerce'),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'Set your printing options here.',
            'msunifaunonline'
        )
    ),
    'pdf_target1_media' => array(
        'title' => __(
            'target1Media',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target1_xoffset' => array(
        'title' => __(
            'target1XOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target1_yoffset' => array(
        'title' => __(
            'target1YOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target2_media' => array(
        'title' => __(
            'target2Media',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target2_xoffset' => array(
        'title' => __(
            'target2XOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target2_yoffset' => array(
        'title' => __(
            'target2YOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target3_media' => array(
        'title' => __(
            'target3Media',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target3_xoffset' => array(
        'title' => __(
            'target3XOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target3_yoffset' => array(
        'title' => __(
            'target3YOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target4_media' => array(
        'title' => __(
            'target4Media',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target4_xoffset' => array(
        'title' => __(
            'target4XOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'pdf_target4_yoffset' => array(
        'title' => __(
            'target4YOffset',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),

    // Options
    'options' => array(
        'title' => __('Options', 'woocommerce'),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'Set your additional options here.',
            'msunifaunonline'
        )
    ),
    'options_id' => array(
        'title' => __(
            'Id',
            'msunifaunonline'
        ),
        'type' => 'text',
        'placeholder' => '',
        'default' => '',
        'desc_tip' => true,
    ),
    'options_from_email' => array(
        'title' => __(
            'From Email',
            'msunifaunonline'
        ),
        'type' => 'text',
        'placeholder' => '',
        'default' => '',
        'desc_tip' => true,
        'description' => __(
            'Dynamic values are available here with the code: <code><ul><li>$consignee_address_1</li><li>$consignee_address_2</li><li>$consignee_city</li><li>$consignee_company</li><li>$consignee_country</li><li>$customer_mobile</li><li>$consignee_name</li><li>$consignee_postcode</li><li>$consignee_state</li><li>$customer_email</li><li>$order_id</li><li>$order_number</li><li>$order_subtotal</li></ul></code>',
            'msunifaunonline'
        ),
    ),
    'options_message' => array(
        'title' => __(
            'Message',
            'msunifaunonline'
        ),
        'type' => 'text',
        'default' => 'This is order number $order_number',
        'desc_tip' => true,
        'description' => __(
            'Dynamic values are available here with the code: <code><ul><li>$consignee_address_1</li><li>$consignee_address_2</li><li>$consignee_city</li><li>$consignee_company</li><li>$consignee_country</li><li>$customer_mobile</li><li>$consignee_name</li><li>$consignee_postcode</li><li>$consignee_state</li><li>$customer_email</li><li>$order_id</li><li>$order_number</li><li>$order_subtotal</li><li>$package_number</li></ul></code>',
            'msunifaunonline'
        ),
    ),
    'options_to_email' => array(
        'title' => __(
            'To Email',
            'msunifaunonline'
        ),
        'type' => 'text',
        'placeholder' => '',
        'default' => '',
        'desc_tip' => true,
        'description' => __(
            'Dynamic values are available here with the code: <code><ul><li>$consignee_address_1</li><li>$consignee_address_2</li><li>$consignee_city</li><li>$consignee_company</li><li>$consignee_country</li><li>$customer_mobile</li><li>$consignee_name</li><li>$consignee_postcode</li><li>$consignee_state</li><li>$customer_email</li><li>$order_id</li><li>$order_number</li><li>$order_subtotal</li></ul></code>',
            'msunifaunonline'
        ),
    ),
    'options_error_to_email' => array(
        'title' => __(
            'Error To Email',
            'msunifaunonline'
        ),
        'type' => 'text',
        'placeholder' => '',
        'default' => '',
        'desc_tip' => true,
        'description' => __(
            'Dynamic values are available here with the code: <code><ul><li>$consignee_address_1</li><li>$consignee_address_2</li><li>$consignee_city</li><li>$consignee_company</li><li>$consignee_country</li><li>$customer_mobile</li><li>$consignee_name</li><li>$consignee_postcode</li><li>$consignee_state</li><li>$customer_email</li><li>$order_id</li><li>$order_number</li><li>$order_subtotal</li></ul></code>',
            'msunifaunonline'
        ),
    ),
    'options_language_code' => array(
        'title' => __(
            'Language Code',
            'msunifaunonline'
        ),
        'type' => 'text',
        'placeholder' => '',
        'default' => '',
        'desc_tip' => true,
    ),
    'options_send_email' => array(
        'title' => __(
            'Send Email',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
    ),
    'options_sender_reference_prefix_option' => array(
        'class' => 'wc-enhanced-select',
        'title' => __(
            'Dynamic Reference Prefix 1',
            'msunifaunonline'
        ),
        'type' => 'select',
        'default' => '',
        'options' => array(
            '' => __('None', 'msunifaunonline'),
            'product_skus' => __('Product SKUs', 'msunifaunonline'),
        ),
    ),
    'options_sender_reference_prefix' => array(
        'title' => __(
            'Custom Reference Prefix 2',
            'msunifaunonline'
        ),
        'type' => 'text',
        'placeholder' => '',
        'default' => '',
        'desc_tip' => true,
    ),
    'options_parcel_contents' => array(
        'class' => 'wc-enhanced-select',
        'options' => Mediastrategi_UnifaunOnline::getParcelContentOptions(),
        'title' => __(
            'Parcel Contents',
            'msunifaunonline'
        ),
        'type' => 'select',
    ),

);
