<?php

$cost_desc = __('Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce')
    . '<br/><br/>'
    . __('Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce');

$settings = array(
    'title' => array(
        'description' => __(
            'This is displayed to customers in checkout',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __('Method title', 'woocommerce'),
        'type' => 'text',
        'description' => __(
            'This controls the title which the user sees during checkout.',
            'woocommerce'
        ),
        'default' => __(
            Mediastrategi_UnifaunOnline::METHOD_TITLE,
            'msunifaunonline'
        ),
    ),
    'service' => array(
        'default' => '',
        'title' => __(
            'Service',
            'msunifaunonline'
        ),
        'type' => 'hidden',
    ),
    'selected_addons' => array(
        'description' => __(
            'Dynamic values are available and can be used in expressions: <code>$customer_mobile</code> <br /><code>$customer_email</code> <br /><code>$order_subtotal</code> <br /><code>$consignee_name</code> <br /><code>$consignee_company</code> <br /><code>$consignee_address_1</code> <br /><code>$consignee_address_2</code> <br /><code>$consignee_postcode</code> <br /><code>$consignee_state</code> <br /><code>$consignee_city</code> <br /><code>$consignee_country</code>',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Add-ons',
            'msunifaunonline'
        ),
        'type' => 'hidden',
        'default' => '',
    ),
    'package_type' => array(
        'description' => __(
            'Select a package type that your transport service supports.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Package type',
            'msunifaunonline'
        ),
        'type' => 'hidden',
        'default' => ''
    ),
    'stored_shipments' => array(
        'description' => __(
            'If this is enabled, shipments will only be saved at your service-provider and you need to finalize them there.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Create Stored Shipments',
            'msunifaunonline'
        ),
        'type' => 'checkbox',
        'default' => 'no',
    ),
    'shipment_type' => array(
        'class' => 'wc-enhanced-select',
        'default' => 'turn',
        'description' => __('Note that not all shipping services support both turn and return shipments.'),
        'options' => array(
            'return' => __('Return shipment', 'msunifaunonline'),
            'turn' => __('Turn shipment', 'msunifaunonline'),
            'turn_and_return' => __('Turn and return shipment', 'msunifaunonline'),
        ),
        'title' => __(
            'Shipment Type',
            'msunifaunonline'
        ),
        'type' => 'select',
    ),
    'automation_enabled' => array(
        'title' => __(
            'Automation enabled',
            'msunifaunonline'
        ),
        'description' => __(
            'Automation will automatically process orders with the status below if enabled.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'type' => 'checkbox',
        'description' => __(
            'Automatically process shipping for orders.',
            'msunifaunonline'
        ),
        'default' => 'no',
    ),
    'automation_process_status' => array(
        'title' => __(
            'Automation orders status',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'description' => __(
            'Automatically process orders with this status.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'default' => 'wc-processing',
        'options' => wc_get_order_statuses(),
    ),
    'free_shipping_support' => array(
        'title' => __(
            'Free shipping support',
            'msunifaunonline'
        ),
        'description' => __(
            'Support free shipping coupons for this method.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'type' => 'checkbox',
        'default' => 'no',
    ),

    'custom_pick_up_location' => array(
        'title' => __(
            'Custom Pick Up Location',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => '',
        'options' => Mediastrategi_UnifaunOnline::getPickUpLocationOptions(),
    ),
    'tax_status' => array(
        'title' => __(
            'Tax status',
            'woocommerce'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => 'taxable',
        'options' => array(
            'taxable' => __(
                'Taxable',
                'woocommerce'
            ),
            'none' => _x(
                'None',
                'Tax status',
                'woocommerce'
            ),
        ),
    ),
    'cost' => array(
        'title' => __('Cost', 'woocommerce'),
        'type' => 'text',
        'description' => __(
            'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.',
            'woocommerce'
            )
            . '<br/><br/>' . __(
                'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.',
                'woocommerce'
        ),
        'desc_tip' => true,
    ),

    // Options
    'dimension_unit' => array(
        'class' => 'wc-enhanced-select',
        'default' => 'm',
        'description' => __(
            'If you product dimension unit does not match this there will be automatic conversion for packages.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'options' => array(
            'm'  => __( 'm', 'woocommerce' ),
            'cm' => __( 'cm', 'woocommerce' ),
            'mm' => __( 'mm', 'woocommerce' ),
            'in' => __( 'in', 'woocommerce' ),
            'yd' => __( 'yd', 'woocommerce' ),
        ),
        'title' => __(
            'Package dimension unit',
            'msunifaunonline'
        ),
        'type' => 'select',
    ),
    'weight_unit' => array(
        'class' => 'wc-enhanced-select',
        'default' => 'kg',
        'description' => __(
            'If you product weight unit does not match this there will be automatic conversion for packages.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'options' => array(
            'kg'  => __( 'kg', 'woocommerce' ),
            'g'   => __( 'g', 'woocommerce' ),
            'lbs' => __( 'lbs', 'woocommerce' ),
            'oz'  => __( 'oz', 'woocommerce' ),
        ),
        'title' => __(
            'Package weight unit',
            'msunifaunonline'
        ),
        'type' => 'select',
    ),
    'minimum_weight' => array(
        'default' => 1,
        'title' => __(
            'Package minimum weight',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'package_type_custom' => array(
        'description' => __(
            "If you can't find your package type in the list above enter it here and select Custom in the field above",
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Custom Package Type',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'package_force_largest_dimension' => array(
        'description' => __(
            'Some services want i.e. length to be the largest dimension, you can solve this by using this option.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Force package largest dimension',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => 'no',
        'options' => array(
            'no' => __('No', 'msunifaunonline'),
            'height' => __('Height', 'msunifaunonline'),
            'length' => __('Length', 'msunifaunonline'),
            'width' => __('Width', 'msunifaunonline'),
        ),
    ),
    'package_force_medium_dimension' => array(
        'description' => __(
            'Some services want i.e. width to be the medium dimension, you can solve this by using this option.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Force package medium dimension',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => 'no',
        'options' => array(
            'no' => __('No', 'msunifaunonline'),
            'height' => __('Height', 'msunifaunonline'),
            'length' => __('Length', 'msunifaunonline'),
            'width' => __('Width', 'msunifaunonline'),
        ),
    ),
    'package_force_smallest_dimension' => array(
        'description' => __(
            'Some services want i.e. height to be the smallest dimension, you can solve this by using this option.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Force package smallest dimension',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => 'no',
        'options' => array(
            'no' => __('No', 'msunifaunonline'),
            'height' => __('Height', 'msunifaunonline'),
            'length' => __('Length', 'msunifaunonline'),
            'width' => __('Width', 'msunifaunonline'),
        ),
    ),
    'custom_quick_id' => array(
        'description' => __(
            "If this specific method uses a different quick-id, specify it here.",
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Custom Quick Id',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'custom_quick_id_field' => array(
        'description' => __(
            'Enter field for storage of quick id on products, field above will only be used if none is found.',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Custom Quick Id Field',
            'msunifaunonline'
        ),
        'type' => 'text',
        'default' => '',
    ),
    'extra' => array(
        'description' => __(
            'This data will be merged with shipment data before sent to service-provider, you can use following variables in this code: <br /><code>$customer_mobile</code> <br /><code>$customer_email</code> <br /><code>$order_subtotal</code> <br /><code>$consignee_name</code> <br /><code>$consignee_company</code> <br /><code>$consignee_address_1</code> <br /><code>$consignee_address_2</code> <br /><code>$consignee_postcode</code> <br /><code>$consignee_state</code> <br /><code>$consignee_city</code> <br /><code>$consignee_country</code>’',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Extra shipment data in JSON format',
            'msunifaunonline'
        ),
        'type' => 'textarea',
        'default' => json_encode(array()),
    ),

    'availability_logic' => array(
        'title' => __('Availability logic', 'woocommerce'),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'Use settings below to control when this method should be available.',
            'msunifaunonline'
        ),
    ),
    'weight' => array(
        'description' => __(
            'Allows intervalls, i.e.:<br /><strong>25</strong> means only carts that is exactly 25<br /><strong>-25</strong> means only carts that is below 25<br /><strong>25-30</strong> means only carts that is 25 or higher but still below 30',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Weight',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'width' => array(
        'description' => __(
            'Allows intervalls, i.e.:<br /><strong>25</strong> means only carts that is exactly 25<br /><strong>-25</strong> means only carts that is below 25<br /><strong>25-30</strong> means only carts that is 25 or higher but still below 30',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Width',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'height' => array(
        'description' => __(
            'Allows intervalls, i.e.:<br /><strong>25</strong> means only carts that is exactly 25<br /><strong>-25</strong> means only carts that is below 25<br /><strong>25-30</strong> means only carts that is 25 or higher but still below 30',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Height',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'depth' => array(
        'description' => __(
            'Allows intervalls, i.e.:<br /><strong>25</strong> means only carts that is exactly 25<br /><strong>-25</strong> means only carts that is below 25<br /><strong>25-30</strong> means only carts that is 25 or higher but still below 30',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Length',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'volume' => array(
        'description' => __(
            'Allows intervalls, i.e.:<br /><strong>25</strong> means only carts that is exactly 25<br /><strong>-25</strong> means only carts that is below 25<br /><strong>25-30</strong> means only carts that is 25 or higher but still below 30',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Volume',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'cart_subtotal' => array(
        'description' => __(
            'Allows intervalls, i.e.:<br /><strong>25</strong> means only carts that is exactly 25<br /><strong>-25</strong> means only carts that is below 25<br /><strong>25-30</strong> means only carts that is 25 or higher but still below 30',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Cart Subtotal',
            'msunifaunonline'
        ),
        'type' => 'text',
    ),
    'customs_costs' => array(
        'title' => __('Customs', 'woocommerce'),
        'type' => 'title',
        'default' => '',
        'description' => __(
            'Select the customs documents you want to request from Unifaun. Make sure to use a service that supports this and specify what product attribute contains the product HS code.',
            'msunifaunonline'
        )
    ),
    'customs_statno_attribute' => array(
        'description' => __(
            'Select what product custom attribute contains the HS Code ',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'HS Code Attribute',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => '',
        'options' => Mediastrategi_UnifaunOnline::getWCCustomAttributes(),
    ),
    'customs_statno_field' => array(
        'description' => __(
            'Enter field for storage for product HS Code ',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'HS Code Field',
            'msunifaunonline'
        ),
        'type' => 'text',
        'default' => '',
    ),
    'customs_origin_country' => array(
        'description' => __(
            'Select what product custom attribute contains the origin country ',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Product Origin Country Attribute',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => '',
        'options' => Mediastrategi_UnifaunOnline::getWCCustomAttributes(),
    ),
    'customs_origin_country_field' => array(
        'description' => __(
            'Enter field for storage of product origin country ',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Product Origin Country Field',
            'msunifaunonline'
        ),
        'type' => 'text',
        'default' => '',
    ),
    'customs_value' => array(
        'description' => __(
            'Select the custom product-attribute used to specify customs value',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Product Customs Value Attribute',
            'msunifaunonline'
        ),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => '',
        'options' => Mediastrategi_UnifaunOnline::getWCCustomAttributes(),
    ),
    'customs_value_field' => array(
        'description' => __(
            'Enter field for storage of product customs value',
            'msunifaunonline'
        ),
        'desc_tip' => true,
        'title' => __(
            'Product Customs Value Field',
            'msunifaunonline'
        ),
        'type' => 'text',
        'default' => '',
    ),

);

// Customs Documents
if ($customsDocuments = Mediastrategi_UnifaunOnline::getCustomDeclarationDocumentOptions()) {
    foreach ($customsDocuments as $code => $title) {
        if (!empty($code)
            && !empty($title)
        ) {
            $settings['customs_documents_' . $code] = array(
                'title' => __(
                    $title,
                    'msunifaunonline'
                ),
                'type' => 'checkbox',
                'default' => 'no',
            );
        }
    }

}

// Below is copied from WooCommerce Flat Rate module
$shipping_classes = WC()->shipping->get_shipping_classes();
if (!empty($shipping_classes)) {
    $settings['class_costs'] = array(
        'title' => __('Shipping class costs', 'woocommerce'),
        'type' => 'title',
        'default' => '',
        'description' => sprintf(
            __('These costs can optionally be added based on the <a href="%s">product shipping class</a>.', 'woocommerce'),
            admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes')
        ),
    );
    foreach ($shipping_classes as $shipping_class)
    {
        if (!isset($shipping_class->term_id)) {
            continue;
        }
        $settings['class_cost_' . $shipping_class->term_id] = array(
            /* translators: %s: shipping class name */
            'title' => sprintf(
                __('"%s" shipping class cost', 'woocommerce'),
                esc_html($shipping_class->name)
            ),
            'type' => 'text',
            'placeholder' => __('N/A', 'woocommerce'),
            'description' => $cost_desc,
            'default' => $this->get_option('class_cost_' . $shipping_class->slug), // Before 2.5.0, we used slug here which caused issues with long setting names
            'desc_tip' => true,
        );
    }
    $settings['no_class_cost'] = array(
        'title' => __('No shipping class cost', 'woocommerce'),
        'type' => 'text',
        'placeholder' => __('N/A', 'woocommerce'),
        'description' => $cost_desc,
        'default' => '',
        'desc_tip' => true,
    );
    $settings['type'] = array(
        'title' => __('Calculation type', 'woocommerce'),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => 'class',
        'options' => array(
            'class' => __('Per class: Charge shipping for each shipping class individually', 'woocommerce'),
            'order' => __('Per order: Charge shipping for the most expensive shipping class', 'woocommerce'),
        ),
    );
}

$product_all_categories_list = get_terms(
    'product_cat',
    array(
        'orderby' => 'name',
        'order' => 'asc',
        'hide_empty' => true
    )
);
if ($product_all_categories_list) {
    $categoryFields = array();
    foreach ($product_all_categories_list as $productCategory) {
        if (isset($productCategory->term_id)) {
            $categoryFields['category_' . $productCategory->term_id] = array(
                'default' => 'no',
                'title' => $productCategory->name,
                'type' => 'checkbox'
            );
        }
    }
    if (!empty($categoryFields)) {
        $settings += array(
            'category_logic' => array(
                'title' => __('Limit to product categories', 'woocommerce'),
                'type' => 'title',
                'default' => '',
                'description' => __(
                    'Select the product categories that every product in package needs to belong to make this method available.',
                    'msunifaunonline'
                )
            )
        );
        $settings += $categoryFields;
    }
}

if ($shipping_classes = WC()->shipping->get_shipping_classes()) {
    $shippingClassFields = array();
    foreach ($shipping_classes as $shipping_class) {
        if (isset($shipping_class->term_id)) {
            $shippingClassFields['shipping_class_' . $shipping_class->term_id] = array(
                'default' => 'no',
                'title' => $shipping_class->name,
                'type' => 'checkbox'
            );
        }
    }
    if (!empty($shippingClassFields)) {
        $settings += array(
            'shipping_class_logic' => array(
                'title' => __('Limit to shipping classes', 'woocommerce'),
                'type' => 'title',
                'default' => '',
                'description' => __(
                    'Select the shipping classes that every product in package needs to belong to make this method available.',
                    'msunifaunonline'
                )
            )
        );
        $settings += $shippingClassFields;
    }
}

return $settings;
