<?php
/**
 * @author Christian Johansson <christian@mediastrategi.se>
 * @requires WooCommerce 3.0.0
 */
function Mediastrategi_UnifaunOnline_ShippingMethod_init()
{
    if (!class_exists('Mediastrategi_UnifaunOnline_ShippingMethod')) {
        class Mediastrategi_UnifaunOnline_ShippingMethod
            extends WC_Shipping_Method
        {

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
             *
             */
            public function init()
            {
                parent::init_settings();
                $this->_initFormFields();
                parent::init_form_fields();
                parent::init_instance_settings();
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
             * @todo Add check for shipping classes
             * @todo Add check for product categories
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

                    if ($available) {
                        $product_all_categories_list = get_terms(
                            'product_cat',
                            array(
                                'orderby' => 'name',
                                'order' => 'asc',
                                'hide_empty' => true
                            )
                        );
                        if ($product_all_categories_list) {
                            $productCategories = array();
                            foreach ($product_all_categories_list as $productCategory)
                            {
                                if (isset($productCategory->term_id)) {
                                    $optionValue = $this->get_option(
                                        'category_' . $productCategory->term_id
                                    );
                                    if ($optionValue
                                        && $optionValue === 'yes'
                                    ) {
                                        $productCategories[$productCategory->term_id] = true;
                                    }
                                }
                            }
                            if ($productCategories) {
                                $foundCategory = false;
                                foreach ($itemsToShip as $itemToShip)
                                {
                                    $foundCategory = false;
                                    if ($productAndQuantity = \Mediastrategi_UnifaunOnline::getProductObject($itemToShip)) {
                                        $product = $productAndQuantity[0];
                                        $id = $product->get_id();
                                        if (is_a($product, 'WC_Product_Variation')) {
                                            $id = $product->get_parent_id();
                                        }
                                        if ($terms = get_the_terms(
                                            $id,
                                            'product_cat'
                                        )) {
                                            foreach ($terms as $term)
                                            {
                                                if (isset($term->term_id)) {
                                                    if (isset($productCategories[$term->term_id])) {
                                                        $foundCategory = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (!$foundCategory) {
                                        break;
                                    }
                                }
                                if (!$foundCategory) {
                                    $available = false;
                                }
                            }
                        }
                    }

                    if ($available) {
                        if ($shipping_classes =
                            WC()->shipping->get_shipping_classes()
                        ) {
                            $shippingClasses = array();
                            foreach ($shipping_classes as $shipping_class)
                            {
                                if (isset($shipping_class->term_id)) {
                                    $optionValue = $this->get_option(
                                        'shipping_class_' . $shipping_class->term_id
                                    );
                                    if ($optionValue
                                        && $optionValue === 'yes'
                                    ) {
                                        $shippingClasses[$shipping_class->slug] = true;
                                    }
                                }
                            }
                            if ($shippingClasses) {
                                $foundClass = false;
                                foreach ($itemsToShip as $itemToShip)
                                {
                                    $foundClass = false;
                                    if ($class = $itemToShip['data']->get_shipping_class()) {
                                        if (isset($shippingClasses[$class])) {
                                            $foundClass = true;
                                        }
                                    }
                                    if (!$foundClass) {
                                        break;
                                    }
                                }
                                if (!$foundClass) {
                                    $available = false;
                                }
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
}
