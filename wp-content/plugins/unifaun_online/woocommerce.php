<?php
/**
 * @author Christian Johansson <christian@mediastrategi.se>
 * @requires WooCommerce 3.0.0
 */

class Mediastrategi_UnifaunOnline_Woocommerce
{
    public function __construct()
    {
        add_action(
            'wp_enqueue_scripts',
            array(
                $this,
                'enqueueScripts'
            )
        );
        add_action(
            'woocommerce_new_order',
            array(
                $this,
                'newOrder'
            )
        );
        add_action(
            'woocommerce_after_shipping_rate',
            array(
                $this,
                'afterShippingRate'
            ),
            10,
            2
        );
        /** @since Woocommerce 2.6.0 */
        add_filter(
            'woocommerce_shipping_methods',
            array(
                $this,
                'addShippingMethod'
            )
        );
        /** @since Woocommerce 2.2 */
        add_action(
            'woocommerce_update_order',
            array(
                $this,
                'processOrder'
            )
        );
        /** @since Woocommerce 3.0.0 */
        add_action(
            'woocommerce_new_order',
            array(
                $this,
                'processOrder'
            )
        );
        /** @since Woocommerce 2.6.0 */
        add_action(
            'woocommerce_shipping_init',
            'Mediastrategi_UnifaunOnline_ShippingMethod_init'
        );
        add_action(
            'wp_ajax_Mediastrategi_UnifaunOnline_UpdateShipping',
            array(
                $this,
                'ajaxUpdateShipping'
            )
        );
        add_action(
            'wp_ajax_nopriv_Mediastrategi_UnifaunOnline_UpdateShipping',
            array(
                $this,
                'ajaxUpdateShipping'
            )
        );
        add_action(
            'woocommerce_email_after_order_table',
            array(
                $this,
                'emailAfterOrderTable'
            ),
            10,
            3
        );
    }

    /**
     * @param \WC_Order $order
     * @param bool $sentToAdmin
     * @param bool $plainText
     * @SuppressWarnings(PHPMD.UnusedFunctionParameter)
     * @codingStandardsIgnoreStart
     */
    public function emailAfterOrderTable($order, $sentToAdmin, $plainText)
    {
        /* @codingStandardsIgnoreEnd */
        if (\Mediastrategi_UnifaunOnline::getOption('tracking_link_in_emails') == 'yes') {
            if ($shipping = $order->get_items('shipping')) {
                $packageIndex = 0;
                $isSinglePackage = count($shipping) === 1;
                foreach ($shipping as $shippingMethod)
                {
                    if ($trackingUrl = \Mediastrategi_UnifaunOnline_Order::getTrackingUrl(
                        $order->get_id(),
                        $packageIndex
                    )) {
                        if ($plainText) {
                            if ($isSinglePackage) {
                                printf(
                                    "\n\n%s: %s\n\n",
                                    __(
                                        'Click here to track your order shipment',
                                        'msunifaunonline'
                                    ),
                                    $trackingUrl
                                );
                            } else {
                                printf(
                                    "\n\n%s: %s\n\n",
                                    sprintf(
                                        __(
                                            'Click here to track your package %d shipment',
                                            'msunifaunonline'
                                        ),
                                        $packageIndex + 1
                                    ),
                                    $trackingUrl
                                );
                            }
                        } else {
                            if ($isSinglePackage) {
                                printf(
                                    '<p><a href="%s">%s.</a></p>',
                                    $trackingUrl,
                                    __(
                                        'Click here to track your order shipment',
                                        'msunifaunonline'
                                    )
                                );
                            } else {
                                printf(
                                    '<p><a href="%s">%s.</a></p>',
                                    $trackingUrl,
                                    sprintf(
                                        __(
                                            'Click here to track your package %d shipment',
                                            'msunifaunonline'
                                        ),
                                        $packageIndex + 1
                                    )
                                );
                            }
                        }
                    }
                    $packageIndex++;
                }
            }
        }
    }

    /**
     * Save updates in agent and custom region selector zip
     */
    public function ajaxUpdateShipping()
    {
        $updateCheckout = false;
        $sessionIsAvailable = false;
        $pickupAgent = '';
        $customRegionZip = '';
        $sessionIsAvailable =
            \Mediastrategi_UnifaunOnline_Session::isAvailable();

        if ($sessionIsAvailable
            && !empty($_POST)
        ) {
            if (!empty($_POST['session'])) {
                if (!empty($_POST['session']['agents'])) {
                    $pickupAgents = $_POST['session']['agents'];
                    foreach ($pickupAgents as $package => $agentObject)
                    {
                        $agent = false;
                        if (isset(
                            $agentObject['agent'],
                            $agentObject['service'])
                        ) {
                            $agentService = (string) $agentObject['service'];
                            $agent = false;
                            try {
                                $agent = json_decode(
                                    base64_decode($agentObject['agent']),
                                    true
                                );
                            } catch (\Exception $e) {
                                $agent = false;
                            }
                            if ($agent) {
                                if ($agent !==
                                    \Mediastrategi_UnifaunOnline_Session::getAgent($package)
                                ) {
                                    \Mediastrategi_UnifaunOnline_Session::setAgent(
                                        $package,
                                        $agent
                                    );
                                }
                            }
                            if ($agentService !==
                                \Mediastrategi_UnifaunOnline_Session::getAgentService($package)
                            ) {
                                \Mediastrategi_UnifaunOnline_Session::setAgentService(
                                    $package,
                                    $agentService
                                );
                            }
                        }
                    }
                }

                if (isset($_POST['session']['zip'])) {
                    $customRegionZip =
                        (string) $_POST['session']['zip'];
                    if ($customRegionZip !==
                        \Mediastrategi_UnifaunOnline_Session::getZip()
                    ) {
                        \Mediastrategi_UnifaunOnline_Session::setZip(
                            $customRegionZip
                        );
                        $updateCheckout = true;
                    }
                }
            }
        }

        $agents = array();
        if (isset(WC()->session)) {
            if ($methods = WC()->session->get('chosen_shipping_methods')) {
                $packages = array_keys($methods);
                foreach ($packages as $package) {
                    $agents[$package] = array(
                        'agent' =>
                            \Mediastrategi_UnifaunOnline_Session::getAgent($package),
                        'service' =>
                            \Mediastrategi_UnifaunOnline_Session::getAgentService($package)
                    );
                }
            }
        }

        echo json_encode(array(
            'session_is_available' => $sessionIsAvailable,
            'session' => array(
                'agents' => Mediastrategi_UnifaunOnline_Session::getAgents(),
                'zip' => \Mediastrategi_UnifaunOnline_Session::getZip()
            ),
            'update_checkout' => $updateCheckout,
        ));
        wp_die();
    }

    /**
     * @param array $methods
     * @return array
     */
    public function addShippingMethod($methods)
    {
        $methods[Mediastrategi_UnifaunOnline::METHOD_ID] =
            'Mediastrategi_UnifaunOnline_ShippingMethod';
        return $methods;
    }

    /**
     * @param WC_Shipping_Rate $rate
     * @param int $packageIndex
     * @throws \Exception
     */
    public function afterShippingRate($rate, $packageIndex)
    {
        static $updatedShipping;
        if (!isset($updatedShipping)) {
            /** 
             * We send flag that shipping has been updated
             * But only once per request
             * To be received client-side by JavaScript
             */
            echo '<script type="text/javascript" id="msunifaun_online_updated_shipping">1</script>';
            $updatedShipping = true;
        }
        if ($rate->get_method_id() === Mediastrategi_UnifaunOnline::METHOD_ID) {
            $instanceId = $rate->get_instance_id();
            $instance = new Mediastrategi_UnifaunOnline_ShippingMethod($instanceId);
            if ($instance->instance_settings['custom_pick_up_location']) {
                $zip = false;
                $countryCode = '';
                $countryCode =
                    (!empty($_POST['s_country']) ? (string) $_POST['s_country'] : '');
                $street =
                    (!empty($_POST['s_address']) ? (string) $_POST['s_address'] : '');
                
                if (Mediastrategi_UnifaunOnline
                    ::getOption('custom_region_selector_zip') === 'yes'
                    && Mediastrategi_UnifaunOnline_Session::getZip()
                ) {
                    $zip = Mediastrategi_UnifaunOnline_Session::getZip();
                } else if (!empty($_POST['s_postcode'])) {
                    $zip = (string) $_POST['s_postcode'];
                } else if (!empty($_POST['data'])
                    && !empty($_POST['data']['postal_code'])
                ) {
                    $zip = (string) $_POST['data']['postal_code'];
                } else if (isset(WC()->customer)) {
                    $zip = (string) WC()->customer->get_shipping_postcode();
                }

                if (!empty($zip)
                    && !empty($countryCode)
                ) {
                    $arguments = array(
                        'countryCode' => $countryCode,
                        'password' => Mediastrategi_UnifaunOnline::getOption('api_key_secret'),
                        'street' => $street,
                        'type' => $instance->instance_settings['custom_pick_up_location'],
                        'uri' => Mediastrategi_UnifaunOnline::API_URL,
                        'user_id' => Mediastrategi_UnifaunOnline::getOption('api_user_id'),
                        'username' => Mediastrategi_UnifaunOnline::getOption('api_key_id'),
                        'zip' => $zip,
                    );
                    $key = 'agents_' . md5(json_encode($arguments));
                    $cache = new Mediastrategi_UnifaunOnline_ApiCache();
                    $agents = false;
                    if ($cache->test($key)) {
                        $agents = $cache->load($key);
                    }
                    if (!$agents) {
                        $rest =
                            \Mediastrategi_UnifaunOnline::getRestApi();
                        $agentArgs = array(
                            'countryCode' => $arguments['countryCode'],
                            // 'street' => $arguments['street'],
                            'type' => $arguments['type'],
                            'zip' => $arguments['zip'],
                        );

                        // NOTE Sending street to agent API will give unrelated agents
                        try {
                            if ($rest->addressesAgentsGet($agentArgs)) {
                                $agents = $rest->getLastDecodedResponse();
                                if (!empty($agents)
                                    && is_array($agents)
                                    && isset($agents[0])
                                    && isset($agents[0]['id'])
                                    && $agents[0]['id'] == -1
                                ) {
                                    // DB Schenker sometimes returns a single agent with id -1 for invalid zip-codes
                                    $agents = false;
                                }
                            }
                        } catch (Exception $e) {
                            $agents = false;
                        }
                        $cache->save(
                            $agents,
                            $key
                        );
                    }
                    if ($agents) {
                        // Only check selected agent if this is the selected method
                        $isSelectedRate = \Mediastrategi_UnifaunOnline_Session
                            ::getSelectedShippingMethod($packageIndex)
                            === $rate->id;
                        if ($isSelectedRate) {
                            $selectedAgent =
                                Mediastrategi_UnifaunOnline_Session::getAgent($packageIndex);

                            // Clear selected agent if it's not available anymore
                            $selectedAgentExists = false;
                            if ($selectedAgent) {
                                foreach ($agents as $agent) {
                                    if ($agent['id'] == $selectedAgent['id']) {
                                        $selectedAgentExists = true;
                                        break;
                                    }
                                }
                            }

                            // Select first agent if none is selected
                            if (!$selectedAgent
                                || !$selectedAgentExists
                            ) {
                                Mediastrategi_UnifaunOnline_Session::setAgent(
                                    $packageIndex,
                                    reset($agents)
                                );
                                $selectedAgent =
                                    Mediastrategi_UnifaunOnline_Session::getAgent($packageIndex);
                                Mediastrategi_UnifaunOnline_Session::setAgentService(
                                    $packageIndex,
                                    $arguments['type']
                                );
                            }
                        }

                        $selectedInstance =
                            Mediastrategi_UnifaunOnline_Session::isSelected($packageIndex);
                        echo '<div class="shipping-extra '
                            . ($selectedInstance ? 'selected' : 'not-selected')
                            . '">';

                        // Agents
                        echo '<label class="agents">';
                        echo '<select data-service="' . $arguments['type']
                            . '" data-package="' . $packageIndex . '">';
                        foreach ($agents as $agent)
                        {
                            echo '<option '
                                . ($isSelectedRate && $selectedInstance && !empty($selectedAgent)
                                    && $selectedAgent['id'] == $agent['id'] ? 'selected="selected"' : '')
                                . 'value="' . base64_encode(json_encode($agent)) . '">'
                                . sprintf(
                                    '%s, %s',
                                    $agent['name'],
                                    $agent['city']
                                )
                                . '</option>';
                        }
                        echo '</select></label>';
                        echo '</div>';
                    }
                }
            }

            if (!empty($instance->instance_settings['service'])) {
                $orderServices = explode(
                    '_',
                    $instance->instance_settings['service']
                );
                $selectedCarrierId = reset($orderServices);
                echo '<span class="msunifaunonline-service-id msunifaunonline-carrier-id-'
                    . $selectedCarrierId .'"></span>';
            }
        }
    }

    /**
     * @param int $orderId
     */
    public function newOrder($orderId)
    {
        if (isset(WC()->session)) {
            if ($methods =
                WC()->session->get('chosen_shipping_methods')
            ) {
                $savedMetaData = 0;
                $packages = array_keys($methods);
                foreach ($packages as $package)
                {
                    if (\Mediastrategi_UnifaunOnline_Session
                        ::isSelected($package)
                    ) {
                        $selectedAgent =
                            Mediastrategi_UnifaunOnline_Session::getAgent($package);
                        if (!empty($selectedAgent)) {
                            Mediastrategi_UnifaunOnline_Order::setAgent(
                                $orderId,
                                $package,
                                $selectedAgent
                            );
                            $savedMetaData++;
                        }
                        $selectedAgentService =
                            Mediastrategi_UnifaunOnline_Session::getAgentService($package);
                        if (!empty($selectedAgentService)) {
                            Mediastrategi_UnifaunOnline_Order::setAgentService(
                                $orderId,
                                $package,
                                $selectedAgentService
                            );
                            $savedMetaData++;
                        }
                    }
                    Mediastrategi_UnifaunOnline_Session::clear($package);
                }
            }
        }
    }

    /**
     *
     */
    public function enqueueScripts()
    {
        wp_register_style(
            'Mediastrategi_UnifaunOnline_style',
            plugins_url(
                'assets/css/style.css',
                __FILE__
            ),
            array(),
            '200528'
        );
        wp_enqueue_style('Mediastrategi_UnifaunOnline_style');
        wp_enqueue_script(
            'Mediastrategi_UnifaunOnline_script',
            plugins_url(
                'assets/js/script.js',
                __FILE__
            ),
            array('jquery'),
            '1912161533'
        );

        if (is_checkout()) {
            $settings = \Mediastrategi_UnifaunOnline::getOptions();
            wp_localize_script(
                'Mediastrategi_UnifaunOnline_script',
                'Mediastrategi_UnifaunOnline_AjaxObject',
                array(
                    'AjaxUrl' => admin_url('admin-ajax.php'),
                    'Config' => array(
                        'CustomRegionSelector' => array(
                            'buttonLabel' => __('Get options', 'msunifaunonline'),
                            'showTitle' => (!empty($settings['custom_region_show_title'])
                                && $settings['custom_region_show_title'] === 'yes'),
                            'title' => __('Customize pick-up', 'msunifaunonline'),
                            'zipCode' => (!empty($settings['custom_region_selector_zip'])
                                && $settings['custom_region_selector_zip'] === 'yes'),
                            'zipCodePlaceholder' =>
                                __('Enter your zip code..', 'msunifaunonline'),
                        ),
                        'Debug' => ($settings['debug'] === 'yes'),
                    ),
                    'Session' => array(
                        'agents' => Mediastrategi_UnifaunOnline_Session::getAgents(),
                        'zip' => Mediastrategi_UnifaunOnline_Session::getZip()
                    )
                )
            );
        }
    }

    /**
     * @param array $products
     * @param string|null [$option = null]
     * @return string
     */
    public function getProductsDescription($products, $option = null)
    {
        $description = '';
        if (!isset($option)) {
            $option = \Mediastrategi_UnifaunOnline::getOption('options_parcel_contents');
        }
        if (!empty($option)
            && $option !== 'empty'
        ) {
            if ($option === "categories") {
                $categories = array();
                foreach ($products as $item)
                {
                    /** @var WC_Order_Item_Product $item */
                    $product = \Mediastrategi_UnifaunOnline::getProductObject($item);
                    if ($product) {
                        $product = $product[0];
                        $id = $product->get_id();
                        if (is_a($product, 'WC_Product_Variation')) {
                            $id = $product->get_parent_id();
                        }
                        if ($terms = get_the_terms(
                            $id,
                            'product_cat'
                        )) {
                            foreach ($terms as $term) {
                                $productCategory = $term->name;
                                if (!isset($categories[$productCategory])) {
                                    $categories[$productCategory] = true;
                                }
                            }
                        }
                    }
                }
                if (!empty($categories)) {
                    $description = implode(', ', array_keys($categories));
                }

            } else if ($option === "products") {
                $names = array();
                foreach ($products as $item)
                {
                    /** @var WC_Order_Item_Product $item */
                    $product = \Mediastrategi_UnifaunOnline::getProductObject($item);
                    if ($product) {
                        $product = $product[0];
                        /** @var \WC_Product_Simple $product */
                        $productName = $product->get_name();
                        if (!isset($names[$productName])) {
                            $names[$productName] = true;
                        }
                    }
                }
                if (!empty($names)) {
                    $description = implode(', ', array_keys($names));
                }

            } else if ($option === 'skus') {
                $skus = array();
                foreach ($products as $item)
                {
                    /** @var WC_Order_Item_Product $item */
                    $product = \Mediastrategi_UnifaunOnline::getProductObject($item);
                    if ($product) {
                        $product = $product[0];
                        /** @var \WC_Product_Simple $product */
                        $productSku = $product->get_sku();
                        if (!isset($skus[$productSku])) {
                            $skus[$productSku] = true;
                        }
                    }
                }
                if (!empty($skus)) {
                    $description = implode(
                        ', ',
                        array_keys($skus)
                    );
                }
            }
        }
        return $description;
    }

    /**
     * @param int $orderId
     * @param int $packageId
     * @param bool [$force = false]
     * @param bool [$reprocess = false]
     * @param bool [$notify = false]
     */
    public function processOrderPackage($orderId, $packageId, $force = false, $reprocess = false, $notify = false)
    {
        if ($order = WC_Order_Factory::get_order($orderId)) {
            /** @var WC_Order $order */
            if ($shipping = $order->get_items('shipping')) {
                $packageIndex = 0;
                foreach ($shipping as $shippingMethod)
                {
                    /** @var WC_Order_Item_Shipping $shippingMethod */
                    // Is the shipping-method used for the order a Unifaun Online method?
                    if (strpos(
                        $shippingMethod->get_method_id(),
                        Mediastrategi_UnifaunOnline::METHOD_ID) === 0
                        && $packageIndex === $packageId
                    ) {
                        // Get shipping method instance id
                        $instanceId = $shippingMethod->get_instance_id();
                        if (!class_exists('Mediastrategi_UnifaunOnline_ShippingMethod')) {
                            Mediastrategi_UnifaunOnline_ShippingMethod_init();
                        }
                        $instance = new \Mediastrategi_UnifaunOnline_ShippingMethod($instanceId);
                        $orderStatus = 'wc-' . $order->get_status();
                        $processStatus = (!empty($instance->instance_settings['automation_process_status'])
                            ? $instance->instance_settings['automation_process_status']
                            : '');
                        $automationEnabled = (!empty($instance->instance_settings['automation_enabled'])
                            && $instance->instance_settings['automation_enabled'] === 'yes');

                        // Should we force process or is automation-enabled and order has correct status?
                        if ($force
                            || ($automationEnabled
                                && $orderStatus === $processStatus)
                        ) {
                            // Load extra settings
                            $extraSettings = (!empty($instance->instance_settings['extra'])
                                ? json_decode($instance->instance_settings['extra'], true)
                                : array());

                            // Get order status
                            $unifaunStatus = Mediastrategi_UnifaunOnline_Order::getStatus(
                                $orderId,
                                $packageId
                            );

                            // Is the order-status not processed or should we reprocess?
                            if (empty($unifaunStatus)
                                || $reprocess
                            ) {
                                require_once(__DIR__ . '/includes/shipment.php');
                                $shipment = new Mediastrategi_UnifaunOnline_Shipment(
                                    $instance,
                                    $shippingMethod,
                                    $order,
                                    $packageId,
                                    $extraSettings
                                );
                                $shipment->create($notify);
                            }
                        }
                        break;
                    }
                    $packageIndex++;
                }
            }
        }
    }

    /**
     * If automation is enabled or if $force = true
     * and an order has changed status to a specific status (defined in settings) or if $force = true
     * and order is made with a Unifaun Online shipping method
     * and shipping-label have not previously been generated.
     *
     * Then generate shipping-label and store on order.
     *
     * @static
     * @param int $orderId
     * @param bool [$force = false]
     * @param bool [$reprocess = false]
     * @param bool [$notify = false]
     * @since Woocommerce 3.0.0
     */
    public function processOrder($orderId, $force = false, $reprocess = false, $notify = false)
    {
        if (!empty($orderId)) {
            if ($order = WC_Order_Factory::get_order($orderId)) {
                /** @var WC_Order $order */
                $packageId = 0;
                if ($shipping = $order->get_items('shipping')) {
                    foreach ($shipping as $shippingMethod)
                    {
                        /** @var WC_Order_Item_Shipping $shippingMethod */
                        $this->processOrderPackage(
                            $orderId,
                            $packageId,
                            $force,
                            $reprocess,
                            $notify
                        );
                        $packageId++;
                    }
                }
            }
        }
    }
}

