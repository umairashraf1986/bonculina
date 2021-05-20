<?php
/**
 * @author Christian Johansson <christian@mediastrategi.se>
 * @requires WooCommerce 3.0.0
 */

if (!function_exists('Mediastrategi_UnifaunOnline_init')) {
    function Mediastrategi_UnifaunOnline_init()
    {
        if (!class_exists('Mediastrategi_UnifaunOnline_Woocommerce')) {
            class Mediastrategi_UnifaunOnline_Woocommerce extends WC_Shipping_Method
            {

                /**
                 * @var Mediastrategi_UnifaunOnline_Woocommerce|null
                 */
                private static $instance = null;

                /**
                 * @param int [$instanceId = 0]
                 */
                public function __construct($instanceId = 0)
                {
                    $this->id = Mediastrategi_UnifaunOnline::METHOD_ID;
                    $this->instance_id = absint($instanceId);
                    $this->method_title = __(
                        Mediastrategi_UnifaunOnline::METHOD_TITLE,
                        'msunifaunonline'
                    );
                    $this->method_description = __(
                        Mediastrategi_UnifaunOnline::METHOD_DESCRIPTION,
                        'msunifaunonline'
                    );
                    $this->supports = array(
                        'settings',
                        'shipping-zones',
                        'instance-settings',
                    );
                    $this->init();
                }

                /**
                 * @param int $orderId
                 */
                public function newOrder($orderId)
                {
                    $orderExtraData = $_SESSION;
                    $selectedAgent = (!empty($orderExtraData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT])
                        ? $orderExtraData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT]
                        : false);
                    $selectedAgentService = (!empty($orderExtraData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE])
                        ? $orderExtraData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE]
                        : false);
                    if (!empty($selectedAgent)) {
                        update_post_meta(
                            $orderId,
                            Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT,
                            $selectedAgent
                        );
                    }
                    if (!empty($selectedAgentService)) {
                        update_post_meta(
                            $orderId,
                            Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE,
                            $selectedAgentService
                        );
                    }
                }

                /**
                 * @return Mediastrategi_UnifaunOnline_Woocommerce
                 */
                public static function getInstance()
                {
                    return self::$instance;
                }

                /**
                 *
                 */
                public function init()
                {
                    parent::init_settings();
                    if (!isset(self::$instance)) {
                        self::$instance = $this;
                    }
                    $this->_initFormFields();
                    parent::init_form_fields();
                    parent::init_instance_settings();
                    $this->instance_settings['available_addons'] = json_encode(Mediastrategi_UnifaunOnline::getServiceAddons(), true);
                    $this->_setDefaultSettings();
                    $this->_setDefaultInstanceSettings();
                    $this->enabled = ($this->settings['enabled'] === 'yes'
                        && $this->get_option('enabled') === 'yes'
                        ? 'yes'
                        : 'no');
                    $this->title = $this->get_option('title');
                    $this->tax_status = $this->get_option('tax_status');
                    $this->cost = $this->get_option('cost');
                    $this->type = $this->get_option('type', 'class');
                    $this->fee = $this->get_option('fee');
                    $this->minimum_fee = $this->get_option('minimum_fee');

                    add_action(
                        'woocommerce_update_options_shipping_' . $this->id,
                        array(
                            $this,
                            'process_admin_options'
                        )
                    );
                }

                /**
                 * Format JSON options here before saving to database.
                 *
                 */
                public function process_admin_options()
                {
                    if (isset($_POST, $_POST['woocommerce_msunifaunonline_extra'])) {
                        $_POST['woocommerce_msunifaunonline_extra'] = (!empty($_POST['woocommerce_msunifaunonline_extra'])
                            && json_decode(str_replace('\"', '"', $_POST['woocommerce_msunifaunonline_extra']), true) !== null
                            ? json_encode(json_decode(str_replace('\"', '"', $_POST['woocommerce_msunifaunonline_extra']), true))
                            : json_encode(array()));
                    }
                    parent::process_admin_options();
                }

                /**
                 * Allow if weight, width, height, depth, volume and cart subtotal is within range
                 * and method is enabled
                 * and at least one product in cart needs shipping.
                 *
                 * @param array $package
                 * @return bool
                 * @since Woocommerce 3.0.0
                 */
                public function is_available($package)
                {
                    $available = false;
                    if ($this->settings['enabled'] === 'yes'
                        && $this->get_option('enabled') === 'yes'
                    ) {
                        $weight = $this->get_option('weight');
                        $width = $this->get_option('width');
                        $height = $this->get_option('height');
                        $depth = $this->get_option('depth');
                        $volume = $this->get_option('volume');
                        $subtotal = $this->get_option('cart_subtotal');

                        // Check if at least one product needs shipping
                        $itemsToShip = array();
                        foreach ($package['contents'] as $cartProduct)
                        {
                            /** @var WC_Product_Simple $cartProduct */
                            if ($cartProduct['data']->needs_shipping()) {
                                if (!$available) {
                                    $available = true;
                                }
                                $itemsToShip[] = $cartProduct;
                            }
                        }

                        $size = Mediastrategi_UnifaunOnline::getOrderSize(
                            $itemsToShip,
                            $this->get_option('dimension_unit'),
                            $this->get_option('weight_unit'),
                            $this->get_option('minimum_weight'),
                            $this->get_option('package_force_largest_dimension'),
                            $this->get_option('package_force_medium_dimension'),
                            $this->get_option('package_force_smallest_dimension')
                        );

                        // Weight
                        if ($available
                            && $weight
                            && !$this->_valueIsInSpan(
                                $size['weight'],
                                $weight)
                        ) {
                            $available = false;
                        }

                        // Width
                        if ($available
                            && $width
                            && !$this->_valueIsInSpan(
                                $size['width'],
                                $width)
                        ) {
                            $available = false;
                        }

                        // Height
                        if ($available
                            && $height
                            && !$this->_valueIsInSpan(
                                $size['height'],
                                $height)
                        ) {
                            $available = false;
                        }

                        // Length
                        if ($available
                            && $depth
                            && !$this->_valueIsInSpan(
                                $size['length'],
                                $depth)
                        ) {
                            $available = false;
                        }

                        // Volume
                        if ($available
                            && $volume
                            && !$this->_valueIsInSpan(
                                $size['volume'],
                                $volume)
                        ) {
                            $available = false;
                        }

                        // Cart Subtotal
                        if ($available
                            && $subtotal
                        ) {
                            if ($cartSubtotal = WC()->cart->subtotal) {
                                /** @since Woocommerce 2.1.0 */
                                if (!$this->_valueIsInSpan(
                                    $cartSubtotal,
                                    $subtotal
                                )) {
                                    $available = false;
                                }
                            }
                        }
                    }

                    // Allow third-party customization here
                    $available = apply_filters(
                        'mediastrategi_unifaun_shipment_available',
                        $available,
                        $this,
                        $package
                    );

                    return apply_filters(
                        'woocommerce_shipping_' . $this->id . '_is_available',
                        $available,
                        $package
                    );
                }

                /**
                 * @param int $orderId
                 * @param bool [$notify = false]
                 * @since Woocommerce 3.0.0
                 */
                public function trackOrder($orderId, $notify = false)
                {
                    if (!empty($orderId)) {
                        if ($order = WC_Order_Factory::get_order($orderId)) {
                            /** @var WC_Order $order */
                            if ($shipping = $order->get_items('shipping')) {
                                $shippingMethod = reset($shipping);
                                /** @var WC_Order_Item_Shipping $shippingMethod */

                                // Is the shipping-method used for the order a Unifaun Online method?
                                if (strpos(
                                    $shippingMethod->get_method_id(),
                                    Mediastrategi_UnifaunOnline::METHOD_ID) === 0
                                ) {
                                    // Load global settings
                                    parent::init_settings();
                                    $globalSettings = $this->settings;

                                    // Get shipping method instance id
                                    $instanceId = Mediastrategi_UnifaunOnline::getMethodInstanceId(
                                        $shippingMethod
                                    );
                                    $this->instance_id = $instanceId;

                                    // Load instance settings
                                    parent::init_instance_settings();
                                    $instanceSettings = & $this->instance_settings;

                                    // Did we find instance-settings?
                                    if (!empty($instanceSettings)) {

                                        // Load extra settings
                                        $extraSettings = (!empty($instanceSettings['extra'])
                                            ? json_decode($instanceSettings['extra'], true)
                                            : array());

                                        // Get order status
                                        $unifaunStatus = get_post_meta(
                                            $orderId,
                                            Mediastrategi_UnifaunOnline::META_ORDER_STATUS,
                                            true
                                        );

                                        /**
                                         * Is the order-status as processed
                                         */
                                        if (!empty($unifaunStatus)
                                        ) {
                                            $connection = $this->getConnection();
                                            if ($connection->canTrack()) {
                                                $connection->track(
                                                    $order,
                                                    $globalSettings,
                                                    $globalSettings,
                                                    $instanceSettings,
                                                    $extraSettings,
                                                    $notify
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                /**
                 * @param int $orderId
                 * @param bool [$notify = false]
                 * @since Woocommerce 3.0.0
                 */
                public function printOrder($orderId, $notify = false)
                {
                    if (!empty($orderId)) {
                        if ($order = WC_Order_Factory::get_order($orderId)) {
                            /** @var WC_Order $order */
                            if ($shipping = $order->get_items('shipping')) {
                                $shippingMethod = reset($shipping);
                                /** @var WC_Order_Item_Shipping $shippingMethod */

                                // Is the shipping-method used for the order a Unifaun Online method?
                                if (strpos(
                                    $shippingMethod->get_method_id(),
                                    Mediastrategi_UnifaunOnline::METHOD_ID) === 0
                                ) {
                                    // Load global settings
                                    parent::init_settings();
                                    $globalSettings = $this->settings;

                                    // Get shipping method instance id
                                    $instanceId = Mediastrategi_UnifaunOnline::getMethodInstanceId(
                                        $shippingMethod
                                    );
                                    $this->instance_id = $instanceId;

                                    // Load instance settings
                                    parent::init_instance_settings();
                                    $instanceSettings = & $this->instance_settings;

                                    // Did we find instance-settings?
                                    if (!empty($instanceSettings)) {

                                        // Load extra settings
                                        $extraSettings = (!empty($instanceSettings['extra'])
                                            ? json_decode($instanceSettings['extra'], true)
                                            : array());

                                        // Get order status
                                        $unifaunStatus = get_post_meta(
                                            $orderId,
                                            Mediastrategi_UnifaunOnline::META_ORDER_STATUS,
                                            true
                                        );

                                        /**
                                         * Is the order-status as processed
                                         */
                                        if (!empty($unifaunStatus)) {
                                            $connection = $this->getConnection();
                                            if ($connection->canPrint()) {
                                                $connection->doPrint(
                                                    $order,
                                                    $globalSettings,
                                                    $globalSettings,
                                                    $instanceSettings,
                                                    $extraSettings,
                                                    $notify
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
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
                        $option = $this->get_option('options_parcel_contents');
                    }
                    if (!empty($option)
                        && $option !== 'empty'
                    ) {
                        if ($option === "categories") {
                            $categories = array();
                            foreach ($products as $item)
                            {
                                /** @var WC_Order_Item_Product $item */
                                if ($item->get_variation_id()) {
                                    $product = wc_get_product($item->get_variation_id());
                                } else {
                                    $product = wc_get_product($item->get_product_id());
                                }
                                if ($product) {
                                    /** @var \WC_Product_Simple $product */
                                    if ($terms = get_the_terms($item->get_product_id(), 'product_cat')) {
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
                                if ($item->get_variation_id()) {
                                    $product = wc_get_product($item->get_variation_id());
                                } else {
                                    $product = wc_get_product($item->get_product_id());
                                }
                                if ($product) {
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
                                if ($item->get_variation_id()) {
                                    $product = wc_get_product($item->get_variation_id());
                                } else {
                                    $product = wc_get_product($item->get_product_id());
                                }
                                if ($product) {
                                    /** @var \WC_Product_Simple $product */
                                    $productSku = $product->get_sku();
                                    if (!isset($skus[$productSku])) {
                                        $skus[$productSku] = true;
                                    }
                                }
                            }
                            if (!empty($skus)) {
                                $description = implode(', ', array_keys($skus));
                            }
                        }
                    }
                    return $description;
                }

                /**
                 * @param string $message
                 */
                public function log($message)
                {
                    if ($this->get_option('debug') == 'yes') {
                        error_log(sprintf(
                            '%s - %s - %s',
                            date('Y-m-d H:i:s'),
                            Mediastrategi_UnifaunOnline::METHOD_TITLE,
                            $message
                        ));
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
                 * @param bool [$notify = false]
                 * @since Woocommerce 3.0.0
                 */
                public function processOrder($orderId, $force = false, $notify = false)
                {
                    if (!empty($orderId)) {
                        if ($order = WC_Order_Factory::get_order($orderId)) {
                            /** @var WC_Order $order */
                            $orderStatus = 'wc-' . $order->get_status();
                            if ($shipping = $order->get_items('shipping')) {
                                $shippingMethod = reset($shipping);
                                /** @var WC_Order_Item_Shipping $shippingMethod */

                                // Is the shipping-method used for the order a Unifaun Online method?
                                if (strpos(
                                    $shippingMethod->get_method_id(),
                                    Mediastrategi_UnifaunOnline::METHOD_ID) === 0
                                ) {
                                    // Load global settings
                                    parent::init_settings();

                                    // Get shipping method instance id
                                    $instanceId = Mediastrategi_UnifaunOnline::getMethodInstanceId(
                                        $shippingMethod
                                    );
                                    $this->instance_id = $instanceId;

                                    // Load instance settings
                                    parent::init_instance_settings();
                                    $instanceSettings = & $this->instance_settings;

                                    // Did we find instance-settings?
                                    if (!empty($instanceSettings)) {

                                        $processStatus = (!empty($instanceSettings['automation_process_status'])
                                            ? $instanceSettings['automation_process_status']
                                            : '');
                                        $automationEnabled = (!empty($instanceSettings['automation_enabled'])
                                            && $instanceSettings['automation_enabled'] === 'yes');

                                        // Should we force process or is automation-enabled and order has correct status?
                                        if ($force
                                            || ($automationEnabled
                                                && $orderStatus === $processStatus)
                                        ) {

                                            // Load extra settings
                                            $extraSettings = (!empty($instanceSettings['extra'])
                                                ? json_decode($instanceSettings['extra'], true)
                                                : array());

                                            // Get order status
                                            $unifaunStatus = get_post_meta(
                                                $orderId,
                                                Mediastrategi_UnifaunOnline::META_ORDER_STATUS,
                                                true
                                            );

                                            // Is the order-status not processed or is force flagged ?
                                            if ($force
                                                || empty($unifaunStatus)
                                            ) {
                                                require_once(__DIR__ . '/includes/shipment.php');
                                                $shipment = new Mediastrategi_UnifaunOnline_Shipment(
                                                    $this,
                                                    $order,
                                                    $extraSettings
                                                );
                                                $shipment->create($notify);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                /**
                 * @return Mediastrategi_UnifaunOnline_Connection
                 * @throws Exception
                 */
                public function getConnection()
                {
                    if (isset($this->settings)
                        && !empty($this->settings['connection_type'])
                    ) {
                        $connectionType = $this->settings['connection_type'];
                        $connectionPath = dirname(__FILE__) . '/includes/connections/' . $connectionType . '.php';
                        if (file_exists($connectionPath)) {
                            $connectionClass = 'Mediastrategi_UnifaunOnline_Connection_' . $connectionType;
                            try {
                                require_once($connectionPath);
                            } catch (Exception $e) {
                                Throw new Exception(sprintf(
                                    'Including "%s" caused error: "%s"',
                                    $connectionPath,
                                    $e->getMessage()
                                ));
                            }
                            if (class_exists($connectionClass, false)) {
                                $connection = new $connectionClass();
                                return $connection;
                            } else {
                                Throw new Exception(sprintf(
                                    'Connection class "%s" not found in "%s"',
                                    $connectionClass,
                                    $connectionPath
                                ));
                            }
                        } else {
                            Throw new Exception(sprintf(
                                'Connection "%s" at "%s" not found.',
                                $connectionType,
                                $connectionPath
                            ));
                        }
                    } else {
                        Throw new Exception('No connection type specified');
                    }
                }

                /**
                 * Based on WooCommerce Flat Rate shipping method.
                 * @param mixed $package
                 * @return array
                 */
                public function findShippingClasses($package)
                {
                    $found_shipping_classes = array();
                    foreach ($package['contents'] as $item_id => $values)
                    {
                        if ($values['data']->needs_shipping())
                        {
                            $found_class = $values['data']->get_shipping_class();
                            if (!isset($found_shipping_classes[$found_class])) {
                                $found_shipping_classes[$found_class] = array();
                            }
                            $found_shipping_classes[$found_class][$item_id] = $values;
                        }
                    }
                    return $found_shipping_classes;
                }

                /**
                 * Based on WooCommerce Flat Rate shipping method.
                 *
                 * @param array [$package = array()]
                 * @see WC_Shipping_Flat_Rate->calculate_shipping()
                 * @since Woocommerce 3.0.0, Wordpress 3.1.0
                 */
                public function calculate_shipping($package = array())
                {
                    $rate = array(
                        'id' => $this->get_rate_id(),
                        'label' => $this->title,
                        'cost' => 0,
                        'package' => $package,
                    );

                    // Determine if we have a free-shipping coupon
                    $hasFreeShippingCoupon = false;
                    if ($coupons = WC()->cart->get_coupons()) {
                        foreach ($coupons as $code => $coupon) {
                            if ($coupon->is_valid()
                                && $coupon->get_free_shipping()
                            ) {
                                $hasFreeShippingCoupon = true;
                                break;
                            }
                        }
                    }

                    // Calculate the costs
                    $has_costs = false; // True when a cost is set. False if all costs are blank strings.
                    $cost = $this->get_option('cost');

                    if ($cost !== '') {
                        $has_costs = true;
                        $rate['cost'] = $this->_evaluateCost(
                            $cost,
                            array(
                                'qty'  => $this->_getPackageItemQty($package),
                                'cost' => $package['contents_cost'],
                            )
                        );
                    }

                    // Add shipping class costs.
                    $shipping_classes = WC()->shipping->get_shipping_classes();

                    if (!empty($shipping_classes))
                    {
                        $found_shipping_classes = $this->findShippingClasses($package);
                        $highest_class_cost = 0;

                        foreach ($found_shipping_classes as $shipping_class => $products)
                        {
                            // Also handles BW compatibility when slugs were used instead of ids
                            $shipping_class_term = get_term_by(
                                'slug',
                                $shipping_class,
                                'product_shipping_class'
                            );
                            $class_cost_string = $shipping_class_term
                                && $shipping_class_term->term_id
                                ? $this->get_option('class_cost_' . $shipping_class_term->term_id, $this->get_option('class_cost_' . $shipping_class, ''))
                                : $this->get_option('no_class_cost', '');

                            if ($class_cost_string === '') {
                                continue;
                            }

                            $has_costs  = true;
                            $class_cost = $this->_evaluateCost(
                                $class_cost_string,
                                array(
                                    'qty'  => array_sum(wp_list_pluck( $products, 'quantity')),
                                    'cost' => array_sum(wp_list_pluck( $products, 'line_total')),
                                )
                            );

                            if ($this->type === 'class') {
                                $rate['cost'] += $class_cost;
                            } else {
                                $highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
                            }
                        }

                        if ($this->type === 'order'
                            && $highest_class_cost
                        ) {
                            $rate['cost'] += $highest_class_cost;
                        }
                    }

                    // Add the rate
                    if ($has_costs) {

                        // Support free shipping coupons
                        if ($hasFreeShippingCoupon
                            && $this->get_option('free_shipping_support') === 'yes'
                        ) {
                            $rate['cost'] = 0;
                            $rate['taxes'] = false;
                        }

                        $this->add_rate($rate);
                    }

                    do_action(
                        'woocommerce_' . $this->id . '_shipping_add_rate',
                        $this,
                        $rate
                    );
                }

                /**
                 * @param float $value
                 * @param string $span
                 * @return bool
                 */
                private function _valueIsInSpan($value, $span)
                {
                    if (empty($span)
                        || $span == '*'
                    ) {
                        return true;
                    }
                    if (strpos($span, '-') === false) {
                        if ($value == $span) {
                            return true;
                        }
                    } else {
                        $parts = explode('-', $span);
                        $min = trim($parts[0]);
                        $max = trim($parts[1]);
                        if ((empty($min)
                            || $value >= $min)
                            && (empty($max)
                            || $value < $max)
                        ) {
                            return true;
                        }
                    }
                    return false;
                }

                /**
                 * Get items in package.
                 * Based on WooCommerce Flat Rates shipping method.
                 *
                 * @internal
                 * @param  array $package
                 * @return int
                 * @see WC_Shipping_Flat_Rate->get_package_item_qty()
                 * @since Woocommerce 3.0.0
                 */
                private function _getPackageItemQty($package)
                {
                    $total_quantity = 0;
                    foreach ($package['contents'] as $values)
                    {
                        if ($values['quantity'] > 0
                            && $values['data']->needs_shipping()
                        ) {
                            $total_quantity += $values['quantity'];
                        }
                    }
                    return $total_quantity;
                }

                /**
                 * Evaluate a cost from a sum/string.
                 * Based on WooCommerce Flat Rate shipping method.
                 *
                 * @internal
                 * @param  string $sum
                 * @param  array  $args
                 * @return string
                 * @see WC_Shipping_Flat_Rate->evaluate_cost()
                 * @since Woocommerce 3.0.0
                 */
                private function _evaluateCost($sum, $args = array())
                {
                    require_once(WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php');

                    // Allow 3rd parties to process shipping cost arguments
                    $args = apply_filters(
                        'woocommerce_evaluate_shipping_cost_args',
                        $args,
                        $sum,
                        $this
                    );
                    $locale = localeconv();
                    /** @since Woocommerce 2.3 */
                    $decimals = array(
                        wc_get_price_decimal_separator(),
                        $locale['decimal_point'],
                        $locale['mon_decimal_point'],
                        ','
                    );
                    $this->fee_cost = $args['cost'];

                    // Expand short-codes
                    add_shortcode(
                        'fee',
                        array($this, 'fee')
                    );

                    $sum = do_shortcode(str_replace(
                        array(
                            '[qty]',
                            '[cost]',
                        ),
                        array(
                            $args['qty'],
                            $args['cost'],
                        ),
                        $sum
                    ));

                    remove_shortcode(
                        'fee',
                        array($this, 'fee')
                    );

                    // Remove whitespace from string
                    $sum = preg_replace(
                        '/\s+/',
                        '',
                        $sum
                    );

                    // Remove locale from string
                    $sum = str_replace(
                        $decimals,
                        '.',
                        $sum
                    );

                    // Trim invalid start/end characters
                    $sum = rtrim(
                        ltrim($sum, "\t\n\r\0\x0B+*/"),
                        "\t\n\r\0\x0B+-*/"
                    );

                    // Do the math
                    return $sum ? WC_Eval_Math::evaluate($sum) : 0;
                }

                /**
                 * Work out fee (shortcode).
                 *
                 * @param  array $atts Attributes.
                 * @return string
                 */
                public function fee($atts) {
                    $atts = shortcode_atts(
                        array(
                            'percent' => '',
                            'min_fee' => '',
                            'max_fee' => '',
                        ),
                        $atts,
                        'fee'
                    );

                    $calculated_fee = 0;

                    if ($atts['percent']) {
                        $calculated_fee = $this->fee_cost * (floatval($atts['percent']) / 100);
                    }

                    if ($atts['min_fee']
                        && $calculated_fee < $atts['min_fee']
                    ) {
                        $calculated_fee = $atts['min_fee'];
                    }

                    if ($atts['max_fee']
                        && $calculated_fee > $atts['max_fee']
                    ) {
                        $calculated_fee = $atts['max_fee'];
                    }

                    return $calculated_fee;
                }

                /**
                 * Sets the default settings for module, URIs and more.
                 *
                 * @internal
                 */
                private function _setDefaultSettings()
                {
                    foreach ($this->_getIncludedData('default_settings') as $key => $value)
                    {
                        if (!isset($this->settings[$key])
                            && isset($value)
                        ) {
                            $this->settings[$key] = $value;
                        }
                    }
                }

                /**
                 * Sets the default instance settings for module, URIs and more.
                 *
                 * @internal
                 */
                private function _setDefaultInstanceSettings()
                {
                    foreach ($this->_getIncludedData('default_instance_settings') as $key => $value)
                    {
                        if (empty($this->instance_settings[$key])
                            && isset($value)
                        ) {
                            $this->instance_settings[$key] = $value;
                        }
                    }
                }

                /**
                 * Helper function to get PHP data from files.
                 *
                 * @internal
                 * @param string $name
                 * @return array|bool
                 * @throws Exception
                 */
                private function _getIncludedData($name)
                {
                    if (!empty($name)) {
                        $path = dirname(__FILE__) . '/includes/' . $name . '.php';
                        if (file_exists($path)) {
                            try {
                                $data = require($path);
                                if (isset($data)
                                    && is_array($data)
                                    && count($data)
                                ) {
                                    return $data;
                                }
                            } catch (Exception $e) {
                                Throw new Exception(sprintf(
                                    'Error occured while reading file "%s", error: "%s"',
                                    $path,
                                    $e->getMessage()
                                ));
                            }
                        }
                    }
                    return array();
                }

                /**
                 * Setup form fields both general and per instance.
                 *
                 * @internal
                 */
                private function _initFormFields()
                {
                    $this->form_fields = $this->_getIncludedData('form_fields');
                    $this->instance_form_fields = $this->_getIncludedData('instance_form_fields');
                }

            }
        }
        Mediastrategi_UnifaunOnline_storeOptionsInSession();
    }
}

if (!function_exists('Mediastrategi_UnifaunOnline_processOrder')) {
    /**
     * @param int $orderId
     */
    function Mediastrategi_UnifaunOnline_processOrder($orderId)
    {
        if (!class_exists(
            'Mediastrategi_UnifaunOnline_Woocommerce',
            false)
        ) {
            Mediastrategi_UnifaunOnline_init();
        }
        if (class_exists(
            'Mediastrategi_UnifaunOnline_Woocommerce',
            false)
        ) {
            $class = new Mediastrategi_UnifaunOnline_Woocommerce();
            $class->processOrder($orderId);
        }
    }
}
/** @since Woocommerce 2.2 */
add_action(
    'woocommerce_update_order',
    'Mediastrategi_UnifaunOnline_processOrder'
);
/** @since Woocommerce 3.0.0 */
add_action(
    'woocommerce_new_order',
    'Mediastrategi_UnifaunOnline_processOrder'
);
/** @since Woocommerce 2.6.0 */
add_action(
    'woocommerce_shipping_init',
    'Mediastrategi_UnifaunOnline_init'
);

// Add shipping method to list of shipping methods.
if (!function_exists('Mediastrategi_UnifaunOnlineShippingMethod_add')) {
    /**
     * @param array $methods
     * @return array
     */
    function Mediastrategi_UnifaunOnlineShippingMethod_add($methods)
    {
        $methods[Mediastrategi_UnifaunOnline::METHOD_ID] = 'Mediastrategi_UnifaunOnline_Woocommerce';
        return $methods;
    }
}

/** @since Woocommerce 2.6.0 */
add_filter(
    'woocommerce_shipping_methods',
    'Mediastrategi_UnifaunOnlineShippingMethod_add'
);

if (!function_exists('Mediastrategi_UnifaunOnline_afterShippingRate')) {
    /**
     * @param WC_Shipping_Rate $rate
     * @throws \Exception
     */
    function Mediastrategi_UnifaunOnline_afterShippingRate($rate)
    {
        if ($rate->get_method_id() === Mediastrategi_UnifaunOnline::METHOD_ID) {
            $instanceId = Mediastrategi_UnifaunOnline::getMethodInstanceId($rate);
            $instance = new Mediastrategi_UnifaunOnline_Woocommerce($instanceId);
            if (!empty($instance->instance_settings['custom_pick_up_location'])) {

                $zip = false;
                if (!empty($_POST['s_postcode'])) {
                    $zip = (string) $_POST['s_postcode'];
                } else if (!empty($_POST['data'])
                    && !empty($_POST['data']['postal_code'])
                ) {
                    $zip = (string) $_POST['data']['postal_code'];
                } else if (isset(WC()->customer)) {
                    $zip = (string) WC()->customer->get_shipping_postcode();
                }

                $countryCode =
                    (!empty($_POST['s_country']) ? (string) $_POST['s_country'] : '');
                $street =
                    (!empty($_POST['s_address']) ? (string) $_POST['s_address'] : '');

                if (!empty($zip)
                    && !empty($countryCode)
                ) {
                    $arguments = array(
                        'countryCode' => $countryCode,
                        'password' => $instance->settings['api_key_secret'],
                        'street' => $street,
                        'type' => $instance->instance_settings['custom_pick_up_location'],
                        'uri' => Mediastrategi_UnifaunOnline::API_URL,
                        'user_id' => $instance->settings['api_user_id'],
                        'username' => $instance->settings['api_key_id'],
                        'zip' => $zip,
                    );
                    $key = 'agents_' . md5(json_encode($arguments));
                    $cache = new Mediastrategi_UnifaunOnline_ApiCache();
                    $agents = false;
                    if ($cache->test($key)) {
                        $agents = $cache->load($key);
                    }
                    if (!$agents) {
                        require_once(Mediastrategi_UnifaunOnline::getLibraryLocation('Rest.php'));
                        $configuration = array(
                            'uri' => $arguments['uri'],
                            'user_id' => $arguments['user_id'],
                            'username' => $arguments['username'],
                            'password' => $arguments['password']
                        );
                        $rest =
                            new Mediastrategi\UnifaunOnline\Rest($configuration);
                        $agentArgs = array(
                            'countryCode' => $arguments['countryCode'],
//                             'street' => $arguments['street'],
                            'type' => $arguments['type'],
                            'zip' => $arguments['zip'],
                        );
                        // NOTE Sending street to agent API will give unrelated agents
                        try {
                            if ($rest->addressesAgentsGet($agentArgs)) {
                                $agents = $rest->getLastDecodedResponse();
                                // error_log('Agents args: ' . print_r($agentArgs, true) . ', agents: ' . print_r($agents, true));
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

                        // error_log('Unifaun Online zip: ' . $zip);

                        $selectedInstanceId =
                            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_INSTANCE];
                        $selectedAgent =
                            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT];

                        // Select first agent if none is selected
                        if (!$selectedAgent) {
                            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT] = reset($agents);
                            $selectedAgent = $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT];
                            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE] = $arguments['type'];
                        }

                        // echo 'session: <pre>' . print_r($_SESSION, true) . '</pre>';

                        $selectedInstance = ($selectedInstanceId == $instanceId);
                        echo '<div class="shipping-extra '
                            . ($selectedInstance ? 'selected' : 'not-selected')
                            . '">';

                        // Agents
                        echo '<label class="agents">';
                        echo '<select name="' . Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT . '['
                            . esc_attr($instanceId) . ']">';
                        foreach ($agents as $agent)
                        {
                            echo '<option '
                                . ($selectedInstance && !empty($selectedAgent) && $selectedAgent['id'] == $agent['id'] ? 'selected="selected"' : '')
                                . 'value="' . base64_encode(json_encode($agent)) . '">'
                                . sprintf(
                                    '%s, %s, %s',
                                    $agent['name'],
                                    $agent['address1'],
                                    $agent['city']
                                )
                                . '</option>';
                        }
                        echo '</select></label>';
                        echo '<input type="hidden" name="' . Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE . '[' . esc_attr($instanceId) . ']" value="' . $arguments['type'] . '" />';
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
                echo '<span class="msunifaunonline-service-id msunifaunonline-carrier-id-' . $selectedCarrierId .'"></span>';
            }
        }
    }
}
add_action(
    'woocommerce_after_shipping_rate',
    'Mediastrategi_UnifaunOnline_afterShippingRate'
);

if (!function_exists('Mediastrategi_UnifaunOnline_storeOptionsInSession')) {
    /**
     *
     */
    function Mediastrategi_UnifaunOnline_storeOptionsInSession()
    {
        $postData = false;
        if (!empty($_POST)) {

            // On checkout page, data is in post_data
            if (!empty($_POST['post_data'])) {
                parse_str(
                    $_POST['post_data'],
                    $postData
                );

                // When placing order data is instead in $_POST
            } else {
                $postData = $_POST;
            }
        }

        if (!isset($_SESSION)
            && !headers_sent()
        ) {
            session_start();
        }

        // Selected Instance Id
        if (!isset($_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_INSTANCE])) {
            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_INSTANCE] = false;
        }
        $selectedInstanceId = false;
        if (isset($_POST,
            $_POST['shipping_method'],
            $_POST['shipping_method'][0])
        ) {
            if ($explode = explode(':', $_POST['shipping_method'][0])) {
                if (isset($explode[1])) {
                    $selectedInstanceId = (int) $explode[1];
                    $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_INSTANCE] = $selectedInstanceId;
                }
            }
        }

        // Selected Agent
        if (!isset($_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT])) {
            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT] = false;
        }
        // Selected Agent Service
        if (!isset($_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE])) {
            $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE] = false;
        }
        if ($selectedInstanceId) {
            if (!empty($postData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT])
                && !empty($postData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT][$selectedInstanceId])
            ) {
                $decodedAgent = @json_decode(
                    base64_decode($postData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT][$selectedInstanceId]),
                    true
                );
                if (!empty($decodedAgent)) {
                    $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT] = $decodedAgent;
                }
            } else {
                $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT] = false;
            }

            if (!empty($postData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE][$selectedInstanceId])) {
                $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE] =
                    (string) $postData[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE][$selectedInstanceId];
            } else {
                $_SESSION[Mediastrategi_UnifaunOnline::META_ORDER_SELECTED_AGENT_SERVICE] = '';
            }
        }
    }
}

if (!function_exists('Mediastrategi_UnifaunOnline_newOrder')) {
    /**
     * @param int $orderId
     */
    function Mediastrategi_UnifaunOnline_newOrder($orderId)
    {
        $class = Mediastrategi_UnifaunOnline_Woocommerce::getInstance();
        $class->newOrder($orderId);
    }
}
add_action(
    'woocommerce_new_order',
    'Mediastrategi_UnifaunOnline_newOrder'
);

if (!function_exists('Mediastrategi_UnifaunOnline_enqueue_scripts')) {
    /**
     *
     */
    function Mediastrategi_UnifaunOnline_enqueue_scripts()
    {
        wp_register_style(
            'Mediastrategi_UnifaunOnline_style',
            plugins_url(
                'assets/css/style.css',
                __FILE__
            ),
            array(),
            '20190829'
        );
        wp_enqueue_style('Mediastrategi_UnifaunOnline_style');
        wp_enqueue_script(
            'Mediastrategi_UnifaunOnline_script',
            plugins_url(
                'assets/js/script.js',
                __FILE__
            ),
            array('jquery'),
            '20190829'
        );
    }
}
add_action(
    'wp_enqueue_scripts',
    'Mediastrategi_UnifaunOnline_enqueue_scripts'
);

if (!function_exists('Mediastrategi_UnifaunOnline_plugin_template')) {
    /**
     * @param string $template
     * @param string $templateName
     * @param string $templatePath
     */
    function Mediastrategi_UnifaunOnline_plugin_template($template, $templateName, $templatePath)
    {
        global $woocommerce;
        if (empty($templatePath)) {
            $templatePath = $woocommerce->template_url;
        }

        $pluginPath  = untrailingslashit(
            plugin_dir_path(__FILE__)
        )  . '/templates/woocommerce/';

        // Look within passed path within the theme - this is priority
        $locateTemplate = locate_template(
            array(
                $templatePath . $templateName,
                $templateName
            )
        );

        $pluginTemplatePath = $pluginPath . $templateName;
        if (!$locateTemplate
            && file_exists($pluginTemplatePath)
        ) {
            $template = $pluginTemplatePath;
        }
        return $template;
    }
}

add_filter(
    'woocommerce_locate_template',
    'Mediastrategi_UnifaunOnline_plugin_template',
    1,
    3
);

if (!function_exists('Mediastrategi_UnifaunOnline_wc_method_price')) {
    /**
     * @param WC_Shipping_Rate @method
     * @return string
     * @see wc_cart_totals_shipping_method_label
     */
    function Mediastrategi_UnifaunOnline_wc_method_price($method)
    {
        $label = '';
        if ($method->cost >= 0
            && $method->get_method_id() !== 'free_shipping'
        ) {
            if (WC()->cart->display_prices_including_tax()) {
                $label .= wc_price($method->cost + $method->get_shipping_tax());
                if ($method->get_shipping_tax() > 0
                    && ! wc_prices_include_tax()
                ) {
                    $label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            } else {
                $label .= wc_price($method->cost);
                if ($method->get_shipping_tax() > 0
                    && wc_prices_include_tax()
                ) {
                    $label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            }
        }
        return apply_filters('woocommerce_cart_shipping_method_full_label', $label, $method);
    }

}
