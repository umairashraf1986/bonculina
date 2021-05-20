<?php
/**
 * @author Christian Johansson <christian@mediastrategi.se>
 */

/**
 *
 */
class Mediastrategi_UnifaunOnline
{

    /**
     * @var string
     */
    const API_URL = 'https://api.unifaun.com/rs-extapi/v1/';

    /**
     * @var string
     */
    const METHOD_ID = 'msunifaun_online_';

    /**
     * @var string
     */
    const METHOD_DESCRIPTION = 'Shipping method with integration to Unifaun Online.';

    /**
     * @var string
     */
    const METHOD_TITLE = 'Unifaun Online';

    /**
     * @var string
     */
    const UPLOAD_LABEL_PREFIX = '/msunifaunonline_shipping_label_';

    /**
     * @var string
     */
    const UPLOAD_ADDITIONAL_PREFIX = '/msunifaunonline_shipping_additional_';

    /**
     * @var string
     */
    const UPLOAD_LABELS_PREFIX = '/msunifaunonline_shipping_labels';

    /**
     *@var string
     */
    const UPDATE_INFO_URL = 'https://extensions.mediastrategi.se/wp.online/unifaun_online.json';

    /**
     * @var string
     */
    const UPDATE_ARCHIVE_URL = 'https://extensions.mediastrategi.se/wp.online/unifaun_online.zip';

    /**
     * @static
     * @var bool|array
     */
    private static $options = null;

    /**
     * @var array
     */
    private static $instances;

    /**
     * @var \Mediastrategi\UnifaunOnline\Rest
     */
    private static $rest;

    /**
     * @return Mediastrategi_UnifaunOnline_Woocommerce
     */
    public static function getWoocommerce()
    {
        return self::getSingletonClass('Mediastrategi_UnifaunOnline_Woocommerce');
    }

    /**
     * @return Mediastrategi_UnifaunOnline_Wordpress
     */
    public static function getWordpress()
    {
        return self::getSingletonClass('Mediastrategi_UnifaunOnline_Wordpress');
    }

    /**
     * @param string $class
     * @return $class
     */
    private static function getSingletonClass($class)
    {
        if (!isset(self::$instances)) {
            self::$instances = array();
        }
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] =
                new $class();
        }
        return self::$instances[$class];
    }

    /**
     * @return bool
     */
    public static function hasAccount()
    {
        return self::getOption('api_key_id')
            && self::getOption('api_key_secret');
    }


    /**
     * @param \WC_Order $order
     * @param \WC_Order_Item $rate
     * @return array
     * @see includes/abstracts/abstract-wc-shipping-method.php:323
     */
    public static function getOrderRateItems(
        $order,
        $rate
    ) {
        $items = array();

        // Collect name and quantity for all packages in shipping rate
        $packageItems = array();
        $metaData = $rate->get_formatted_meta_data('');
        foreach ($metaData as $item)
        {
            if (stripos(
                $item->value,
                ' &times; ') !== false
            ) {
                $explode = explode(
                    ' &times; ',
                    $item->value
                );
                if (!empty($explode)
                    && is_array($explode)
                ) {
                    $matches = array();
                    $i = 0;
                    $lastIndex = count($explode) - 1;
                    $lastName = '';
                    $lastQuantity = 0;
                    foreach ($explode as $exploded)
                    {
                        if ($i == 0) {
                            $lastName = trim($exploded);
                        } elseif ($i == $lastIndex) {
                            $lastQuantity = (int) $exploded;
                            $matches[] = array(
                                'name' => $lastName,
                                'quantity' => $lastQuantity
                            );
                        } else {
                            $nameAndQuantity = explode(
                                ', ',
                                $exploded,
                                2
                            );
                            if (!empty($nameAndQuantity)
                                && is_array($nameAndQuantity)
                                && isset($nameAndQuantity[0],
                                    $nameAndQuantity[1])
                            ) {
                                $lastQuantity = (int) $nameAndQuantity[0];
                                $matches[] = array(
                                    'name' => $lastName,
                                    'quantity' => $lastQuantity
                                );
                                $lastName = trim($nameAndQuantity[1]);
                            }
                        }
                        $i++;
                    }

                    if (!empty($matches)) {
                        foreach ($matches as $match)
                        {
                            if (isset(
                                $match['name'],
                                $match['quantity']
                            )) {
                                if (!isset($packageItems[$match['name']])) {
                                    $packageItems[$match['name']] = array(
                                        'name' => $match['name'],
                                        'quantity' => $match['quantity']
                                    );
                                } else {
                                    $packageItems[$match['name']]['quantity'] +=
                                        $quantity;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($packageItems) {
            // Find product ids for all package items
            $orderItems = $order->get_items();
            if ($orderItems) {
                foreach ($orderItems as $orderItem)
                {
                    $orderItemName = $orderItem->get_name();
                    if (isset($packageItems[$orderItemName])) {
                        $items[] = array(
                            'data' => $orderItem,
                            'quantity' => $packageItems[$orderItemName]['quantity']
                        );
                    }
                }
            }
        }
        // die('<pre>items: ' . print_r($items, true) . ', package-items: ' . print_r($packageItems, true) . ', order: ' . print_r($order, true));
        return $items;
    }

    /**
     * @param mixed $array
     * @return bool
     */
    public static function isNumericArray($array)
    {
        if (isset($array)
            && is_array($array)
            && !empty($array)
        ) {
            foreach (array_keys($array) as $key) {
                if (!is_numeric($key)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function getPackageTypes()
    {
        return array(
            '' => __(
                'None',
                'msunifaunonline'
            ),
            '-' => __(
                'Custom',
                'msunifaunonline'
            ),
            'PK' => __(
                'Unspecified package (PK)',
                'msunifaunonline'
            ),
            'CT' => __(
                'Carton (CT)',
                'msunifaunonline'
            ),
            'Z01' => __(
                'Pallet (Z01)',
                'msunifaunonline'
            ),
            'Z02' => __(
                'Half pallet (Z02)',
                'msunifaunonline'
            ),
            'BX' => __(
                'Box (BX)',
                'msunifaunonline'
            ),
            'EP' => __(
                'Europallet (EP)',
                'msunifaunonline'
            ),
            'PA' => __(
                'Packet (PA)',
                'msunifaunonline'
            ),
            'PLL' => __(
                'Pallet (PLL)',
                'msunifaunonline'
            ),
            'PE' => __(
                'Pallet (PE)',
                'msunifaunonline'
            ),
            'PC' => __(
                'Parcel (PC)',
                'msunifaunonline'
            ),
            'XP' => __(
                'Other Pallets (XP)',
                'msunifaunonline'
            ),
            'CS' => __(
                'Case (CS)',
                'msunifaunonline'
            ),
            'CT' => __(
                'Carton (CT)',
                'msunifaunonline'
            ),
            'NE' => __(
                'Unpacked or unpackaged (NE)',
                'msunifaunonline'
            ),
            'OP' => __(
                'One-way pallet (OP)',
                'msunifaunonline'
            ),
            'PAL' => __(
                'Pallet or CP Pallet (PAL)',
                'msunifaunonline'
            ),
            'BX' => __(
                'Box (BX)',
                'msunifaunonline'
            ),
            '701' => __(
                'DHL Pall (701)',
                'msunifaunonline'
            ),
            '702' => __(
                'DHL Halvpall (702)',
                'msunifaunonline'
            ),
            'ZZ' => __(
                'Other (ZZ)',
                'msunifaunonline'
            ),
        );
    }

    /**
     * @static
     * @return array|bool
     */
    public static function getOptions()
    {
        if (!isset(self::$options)) {
            self::$options = get_option(
                sprintf(
                    'woocommerce_%s_settings',
                    self::METHOD_ID
                ),
                true
            );
        }
        return self::$options;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public static function setOption(
        $key,
        $value
    ) {
        if ($settings = self::getOptions()) {
            self::$options[$key] = $value;
            update_option(
                sprintf(
                    'woocommerce_%s_settings',
                    self::METHOD_ID
                ),
                self::$options
            );
        }
    }

    /**
     * @static
     * @param string $key
     * @param string [$default = '']
     * @return mixed
     */
    public static function getOption($key, $default = '')
    {
        if ($settings = self::getOptions()) {
            if (!empty($key)
                && isset($settings[$key])
            ) {
                return $settings[$key];
            }
        }
        return $default;
    }

    /**
     * @static
     * @param array $consignmentIds
     * @return string
     */
    public static function getMultipleLabelsPath($consignmentIds)
    {
        sort($consignmentIds);
        return self::UPLOAD_LABELS_PREFIX . '_' . md5(implode(
            '_',
            $consignmentIds
        ));
    }

    /**
     * @return string
     */
    public static function getCustomDeclarationDocumentOptions()
    {
        require_once(self::getLibraryLocation('Helper.php'));
        return \Mediastrategi\UnifaunOnline\Helper::getCustomDeclarationDocumentOptions();
    }

    /**
     * @return array|bool
     */
    public static function getWCCustomAttributes()
    {
        if ($attributeTaxonomies = wc_get_attribute_taxonomies()) {
            $attributes = array(
                '' => __('None', 'msunifaunonline')
            );
            foreach ($attributeTaxonomies as $attribute)
            {
                $name = wc_attribute_taxonomy_name($attribute->attribute_name);
                $label = $attribute->attribute_label ?: $attribute->attribute_name;
                if (!empty($name)
                    && !empty($label)
                ) {
                    $attributes[$name] = $label;
                }
            }
            if (count($attributes) > 1) {
                return $attributes;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function getPickUpLocationOptions()
    {
        require_once(self::getLibraryLocation('Helper.php'));
        $services = \Mediastrategi\UnifaunOnline\Helper::getCarriersWithPickUpLocation();
        $services[''] = __('None', 'msunifaunonline');
        return $services;
    }

    /**
     * @param string [$path = '']
     * @return string
     */
    public static function getLibraryLocation($path = '')
    {
        return __DIR__ . '/includes/Unifaun_Online/src/' . $path;
    }

    /** @return string */
    public static function getOnboardingLanguage() { return 'sv'; }

    /**
     * @static
     * @return \Mediastrategi\UnifaunOnboarding\Rest
     */
    public static function getOnboarding()
    {
        require_once(__DIR__ . '/includes/Unifaun_Onboarding/src/Rest.php');
        require_once(__DIR__ . '/includes/Unifaun_Onboarding/src/Transaction.php');
        return new \Mediastrategi\UnifaunOnboarding\Rest(array(
            'language' => self::getOnboardingLanguage(),
            'password' => base64_decode('UVpRVVJTN1o2TllXWUVRRUwyQkpHVDZG'),
            'url' => self::API_URL,
            'userid' => base64_decode('MDAyMDAxNTExMQ=='),
            'username' => base64_decode('VkhINk1aTVdBSEhJWUY1RQ==')
        ));
    }

    /**
     * @return array
     */
    public static function getRestApiCredentials()
    {
        return array(
            'uri' => self::API_URL,
            'user_id' => self::getOption('api_user_id'),
            'username' => self::getOption('api_key_id'),
            'pacsoft' => (self::getOption('pacsoft') == 'yes'),
            'password' => self::getOption('api_key_secret'),
        );
    }

    /**
     * @return \Mediastrategi\UnifaunOnline\Rest|bool
     */
    public static function getRestApi()
    {
        if (!isset(self::$rest)) {
            require_once(self::getLibraryLocation('Rest.php'));
            try {
                self::$rest = new \Mediastrategi\UnifaunOnline\Rest(
                    self::getRestApiCredentials()
                );
            } catch (Exception $e) {
                self::$rest = false;
            }
        }
        return self::$rest;
    }

    /**
     * @return array
     */
    public static function getPartners()
    {
        $partners = array();

        // Use file-system cache here
        $cacheKey = sprintf(
            'partners_%s',
            md5(json_encode(self::getRestApiCredentials()))
        );
        $cache =
            \Mediastrategi_UnifaunOnline_ApiCache::getInstance();
        if ($cache->test($cacheKey)) {
            $partners = $cache->load($cacheKey);
        }

        if (!empty($partners)) {
            self::log('Loaded partners from cache');
        } else if (empty($partners)) {
            self::log('Failed to load partners from cache, loading from API');
            if ($rest = self::getRestApi()) {
                try {
                    if ($rest->metaListsPartnersGet()) {
                        $partners = $rest->getLastDecodedResponse();
                        $cache->save(
                            $partners,
                            $cacheKey
                        );
                    }
                } catch (\Exception $e) {
                    self::log(sprintf(
                        'Failed to load partners from API, response-code: %d, response: %s',
                        $rest->getLastResponseCode(),
                        $rest->getLastResponse()
                    ));
                    $partners = array();
                }
            }
        }

        return $partners;
    }

    /**
     * @param string $message
     */
    public static function log($message)
    {
        if (self::getOption('debug') == 'yes') {
            error_log(sprintf(
                '%s - %s',
                'msunifaun',
                $message
            ));
        }
    }

    /**
     * @return array
     */
    public static function getCarrierServices()
    {
        require_once(self::getLibraryLocation('Helper.php'));
        $newServices = array();
        if ($services = \Mediastrategi\UnifaunOnline\Helper::getCarrierServices()) {
            // die('<pre>' . print_r($services, true));
            foreach ($services as $carrierId => $carrier) {
                if (!empty($carrier['services'])) {
                    foreach ($carrier['services'] as $serviceId => $service) {
                        if (!empty($service['title'])) {
                            $code = sprintf(
                                '%s_%s',
                                $carrierId,
                                $serviceId
                            );
                            $title = sprintf(
                                '%s (%s)',
                                $service['title'],
                                $serviceId
                            );
                            if (empty($newServices[$code])) {
                                $newServices[$code] = $title;
                            }
                        }
                    }
                }
            }
        }
        return $newServices;
    }

    /**
     * @return array
     */
    public static function getServiceAddons()
    {
        return \Mediastrategi\UnifaunOnline\Helper::getServicesAddons();
    }

    /**
     * @return array
     */
    public static function getParcelContentOptions()
    {
        return array(
            'categories' => __(
                'Product Categories',
                'msunifaunonline'
            ),
            'products' => __(
                'Product Names',
                'msunifaunonline'
            ),
            'skus' => __(
                'Product SKUs',
                'msunifaunonline'
            ),
            'empty' => __(
                'Empty',
                'msunifaunonline'
            ),
        );
    }

    /**
     * @param mixed $item
     * @return array(WC_Product,int)|false
     */
    public static function getProductObject($item)
    {
        if ((is_array($item)
            && !empty($item['data'])
            && isset($item['quantity'])
            && (is_a($item['data'], 'WC_Product')
                || is_a($item['data'], 'WC_Order_Item_Product')))
            || is_a($item, 'WC_Order_Item_Product')
        ) {
            $product = false;
            $quantity = 0;
            if (is_a($item['data'], 'WC_Product')) {
                $product = wc_get_product($item['data']->get_id());
                $quantity = $item['quantity'];
            } elseif (is_a($item['data'], 'WC_Order_Item_Product')) {
                $product = wc_get_product($item['data']->get_variation_id()
                    ? $item['data']->get_variation_id()
                    : $item['data']->get_product_id());
                $quantity = $item['quantity'];
            } else {
                $product = wc_get_product($item->get_variation_id()
                    ? $item->get_variation_id()
                    : $item->get_product_id()
                );
                $quantity = $item->get_quantity();
            }
            if ($product
                && $quantity
                && is_a($product, 'WC_Product')
            ) {
                /** @var WC_Product $product */
                return array($product, $quantity);
            }
        }
        return false;
    }

    /**
     * This method is used in two different scenarios
     * 1. Calculating size of products in order
     * 2. Calculating size of products in cart
     *
     * @param array $items
     * @param string [$dimensionUnit = 'm']
     * @param string [$weightUnit = 'kg']
     * @param int [$minimumWeight = 0]
     * @param string [$largestDimension = 'no']
     * @param string [$mediumDimension = 'no']
     * @param string [$smallestDimension= 'no']
     * @return string
     */
    public static function getOrderSize(
        $items,
        $toDimensionUnit = 'm',
        $toWeightUnit = 'kg',
        $minimumWeight = 0,
        $largestDimension = 'no',
        $mediumDimension = 'no',
        $smallestDimension = 'no'
    ) {
        if (!isset($toDimensionUnit)) {
            $toDimensionUnit = 'm';
        }
        if (!isset($toWeightUnit)) {
            $toWeightUnit = 'kg';
        }
        if (!isset($minimumWeight)) {
            $minimumWeight = 0;
        }
        if (!isset($largestDimension)) {
            $largestDimension = 'no';
        }
        if (!isset($mediumDimension)) {
            $mediumDimension = 'no';
        }
        if (!isset($smallestDimension)) {
            $smallestDimension = 'no';
        }
        $fromWeightUnit = get_option('woocommerce_weight_unit');
        $fromDimensionUnit = get_option('woocommerce_dimension_unit');
        $size = array(
            'height' => 0.,
            'length' => 0.,
            'volume' => 0.,
            'weight' => 0.,
            'width' => 0.,
        );
        if (!empty($items)
            && is_array($items)
        ) {
            foreach ($items as $item)
            {
                if ($productAndQuantity = self::getProductObject($item)) {
                    $product = $productAndQuantity[0];
                    $quantity = $productAndQuantity[1];
                    $convertedItemHeight = 0;
                    $convertedItemLength = 0;
                    $convertedItemWidth = 0;

                    // Height
                    if (method_exists($product, 'get_height')
                        && is_numeric($product->get_height())
                    ) {
                        $itemHeight = $product->get_height();
                        if ($itemHeight) {
                            $convertedItemHeight = self::getConvertedDimensionUnitValue(
                                $itemHeight,
                                $fromDimensionUnit,
                                $toDimensionUnit
                            );
                            $size['height'] += $convertedItemHeight * $quantity;
                        }
                    }

                    // Length
                    if (method_exists($product, 'get_length')
                        && is_numeric($product->get_length())
                    ) {
                        $itemLength = $product->get_length();
                        if ($itemLength) {
                            $convertedItemLength = self::getConvertedDimensionUnitValue(
                                $itemLength,
                                $fromDimensionUnit,
                                $toDimensionUnit
                            );
                            $size['length'] += $convertedItemLength * $quantity;
                        }
                    }

                    // Weight
                    if (method_exists($product, 'get_weight')
                        && is_numeric($product->get_weight())
                    ) {
                        $itemWeight = $product->get_weight() * $quantity;
                        if ($itemWeight) {
                            $size['weight'] += self::getConvertedWeightUnitValue(
                                $itemWeight,
                                $fromWeightUnit,
                                $toWeightUnit
                            );
                        }
                    }

                    // Width
                    if (method_exists($product, 'get_width')
                        && is_numeric($product->get_width())
                    ) {
                        $itemWidth = $product->get_width();
                        if ($itemWidth) {
                            $convertedItemWidth = self::getConvertedDimensionUnitValue(
                                $itemWidth,
                                $fromDimensionUnit,
                                $toDimensionUnit
                            );
                            $size['width'] += $convertedItemWidth * $quantity;
                        }
                    }

                    // Add to volume
                    if ($convertedItemHeight
                        && $convertedItemLength
                        && $convertedItemWidth
                    ) {
                        $size['volume'] +=
                            ($convertedItemHeight * $convertedItemLength * $convertedItemWidth) * $quantity;
                    }
                }
            }
        }

        // Minimum weight logic
        if ($size['weight'] < $minimumWeight) {
            $size['weight'] = round((float) $minimumWeight, 2);
        }

        // Force dimension priority logic
        if ($largestDimension != 'no'
            || $mediumDimension != 'no'
            || $smallestDimension != 'no'
        ) {

            // Determine largest, medium and smallest values
            $dimensions = array($size['height'], $size['length'], $size['width']);
            $largestValue = reset($dimensions);
            $mediumValue = reset($dimensions);
            $smallestValue = reset($dimensions);
            foreach ($dimensions as $value)
            {
                if ($value > $largestValue) {
                    $largestValue = $value;
                } elseif ($value < $smallestValue) {
                    $smallestValue = $value;
                } else {
                    $mediumValue = $value;
                }
            }

            // Force dimensions by settings
            if ($largestDimension != 'no') {
                $size[$largestDimension] = $largestValue;
            }
            if ($mediumDimension != 'no') {
                $size[$mediumDimension] = $mediumValue;
            }
            if ($smallestDimension != 'no') {
                $size[$smallestDimension] = $smallestValue;
            }
        }

        $size = apply_filters(
            'mediastrategi_online_order_size',
            $size,
            func_get_args()
        );
        return $size;
    }

    /**
     * @param float $value
     * @param string $fromUnit
     * @param string [$toUnit = 'm']
     * @return float
     */
    public static function getConvertedDimensionUnitValue($value, $fromUnit, $toUnit = 'm')
    {
        if (!isset($toUnit)) {
            $toUnit = 'm';
        }
        $converted = $value;
        if (isset($value)
            && !empty($fromUnit)
            && !empty($toUnit)
            && $fromUnit !== $toUnit
        ) {
            if ($toUnit === 'm') {
                if ($fromUnit === 'cm') {
                    $converted = $value / 100;
                } else if ($fromUnit === 'mm') {
                    $converted = $value / 1000;
                } else if ($fromUnit === 'in') {
                    $converted = $value / 39.370;
                } else if ($fromUnit === 'yd') {
                    $converted = $value / 1.0936;
                }

            } else if ($toUnit === 'cm') {
                if ($fromUnit === 'm') {
                    $converted = $value * 100;
                } else if ($fromUnit === 'mm') {
                    $converted = $value / 10;
                } else if ($fromUnit === 'in') {
                    $converted = ($value / 39.370) * 100;
                } else if ($fromUnit === 'yd') {
                    $converted = ($value / 1.0936) * 100;
                }

            } else if ($toUnit === 'mm') {
                if ($fromUnit === 'm') {
                    $converted = $value * 1000;
                } else if ($fromUnit === 'cm') {
                    $converted = $value * 10;
                } else if ($fromUnit === 'in') {
                    $converted = ($value / 39.370) * 1000;
                } else if ($fromUnit === 'yd') {
                    $converted = ($value / 1.0936) * 1000;
                }

            } else if ($toUnit === 'in') {
                if ($fromUnit === 'm') {
                    $converted = $value * 39.370;
                } else if ($fromUnit === 'cm') {
                    $converted = ($value * 39.370) * 100;
                } else if ($fromUnit === 'mm') {
                    $converted = ($value * 39.370) * 1000;
                } else if ($fromUnit === 'yd') {
                    $converted = $value * 36;
                }

            } else if ($toUnit === 'yd') {
                if ($fromUnit === 'm') {
                    $converted = $value * 1.0936;
                } else if ($fromUnit === 'cm') {
                    $converted = ($value / 100) * 1.0936;
                } else if ($fromUnit === 'mm') {
                    $converted = ($value / 1000) * 1.0936;
                } else if ($fromUnit === 'in') {
                    $converted = $value / 36;
                }

            }

        }
        return $converted;
    }

    /**
     * @param float $value
     * @param string $fromUnit
     * @param string [$toUnit = 'kg']
     * @return float
     */
    public static function getConvertedWeightUnitValue($value, $fromUnit, $toUnit = 'kg')
    {
        if (!isset($toUnit)) {
            $toUnit = 'kg';
        }
        $converted = $value;
        if (isset($value)
            && !empty($fromUnit)
            && !empty($toUnit)
            && $fromUnit !== $toUnit
        ) {

            if ($toUnit === 'kg') {
                if ($fromUnit === 'g') {
                    $converted = $value / 1000;
                } else if ($fromUnit === 'lbs') {
                    $converted = $value / 2.204623;
                } else if ($fromUnit === 'oz') {
                    $converted = $value / 35.27396;
                }

            } else if ($toUnit === 'g') {
                if ($fromUnit === 'kg') {
                    $converted = $value * 1000;
                } else if ($fromUnit === 'lbs') {
                    $converted = ($value / 2.204623) * 1000;
                } else if ($fromUnit === 'oz') {
                    $converted = ($value / 35.27396) * 1000;
                }

            } else if ($toUnit === 'lbs') {
                if ($fromUnit === 'kg') {
                    $converted = $value * 2.204623;
                } else if ($fromUnit === 'g') {
                    $converted = ($value * 2.204623) / 1000;
                } else if ($fromUnit === 'oz') {
                    $converted = $value * 0.0625;
                }

            } else if ($toUnit === 'oz') {
                if ($fromUnit === 'kg') {
                    $converted = $value * 35.27396;
                } else if ($fromUnit === 'g') {
                    $converted = ($value / 1000) * 35.27396;
                } else if ($fromUnit === 'lbs') {
                    $converted = $value / 0.0625;
                }

            }

        }
        return $converted;
    }

}
