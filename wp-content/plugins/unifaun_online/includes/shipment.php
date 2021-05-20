<?php
/**
 *
 */

/**
 *
 */
class Mediastrategi_UnifaunOnline_Shipment
{

    /**
     * @var \Mediastrategi\UnifaunOnline\Rest|bool
     */
    private $rest;

    /**
     * @var \WC_Order
     */
    private $order;

    /**
     * @var \WC_Shipping_Method
     */
    private $method;

    /**
     *
     */
    private $rate;

    /**
     * @var string
     */
    private $package;

    /**
     * @var array
     */
    private $extraSettings;

    /**
     * @param \WC_Shipping_Method $method
     * @param $rate
     * @param \WC_Order $order
     * @param string $package
     * @param array|null [$extraSettings = null]
     */
    public function __construct(
        $method,
        $rate,
        $order,
        $package,
        $extraSettings = null
    ) {
        $this->rest = \Mediastrategi_UnifaunOnline::getRestApi();
        $this->method = $method;
        $this->rate = $rate;
        $this->order = $order;
        $this->package = $package;
        $this->extraSettings = $extraSettings;
    }

    /**
     * @param bool [$notify = false]
     * @return bool
     */
    public function create($notify = false)
    {
        if ($this->rest) {
            $orderServices = explode(
                '_',
                $this->method->instance_settings['service']
            );
            $selectedCarrierId = reset($orderServices);
            $selectedServiceId = end($orderServices);

            // Set add-ons if we have any
            $selectedAddons = array();
            $dataAddons = array();
            if (!empty($this->method->instance_settings['selected_addons'])) {

                try {
                    $selectedAddons = json_decode(
                        $this->method->instance_settings['selected_addons'],
                        true
                    );
                    foreach (array_keys($selectedAddons) as $addonKey)
                    {
                        $dataAddons[] = array(
                            'id' => $addonKey,
                        );
                    }
                } catch (Exception $e) {
                    $this->log(sprintf(
                        'Failed to decode JSON %s',
                        $this->method->instance_settings['selected_addons']
                    ));
                }
            }

            $serviceArray = ($dataAddons ? array(
                'id' => $selectedServiceId,
                'addons' => $dataAddons
                ) : array(
                'id' => $selectedServiceId,
            ));

            // Add support for turn, return and turn and return shipments
            if (!empty($this->method->instance_settings['shipment_type'])) {
                if ($this->method->instance_settings['shipment_type'] === 'turn'
                    || $this->method->instance_settings['shipment_type'] === 'turn_and_return'
                ) {
                    $serviceArray['normalShipment'] = true;
                } else {
                    $serviceArray['normalShipment'] = false;
                }

                if ($this->method->instance_settings['shipment_type'] === 'return'
                    || $this->method->instance_settings['shipment_type'] === 'turn_and_return'
                ) {
                    $serviceArray['returnShipment'] = true;
                } else {
                    $serviceArray['returnShipment'] = false;
                }
            }

            // Determine quick id
            $quickId = $this->method->settings['api_quick_id'];
            $productQuckId = false;
            if (!empty($this->method->instance_settings['custom_quick_id_field'])) {

                // Iterate products, stop at first found quick id
                $quickIdField =
                    $this->method->instance_settings['custom_quick_id_field'];
                $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                    $this->order,
                    $this->rate
                );
                if (!empty($packageItems)) {
                    foreach ($packageItems as $orderItem)
                    {
                        $orderItem = $orderItem['data'];
                        /** @var WC_Order_Item_Product $orderItem */

                         // Get product
                         $referenceId = $orderItem->get_variation_id()
                            ? $orderItem->get_variation_id()
                            : $orderItem->get_product_id();

                        $productQuckId = get_post_meta(
                            $referenceId,
                            $quickIdField,
                            true
                        );
                        if (!empty($productQuckId)) {
                            $quickId = $productQuckId;
                            break;
                        }
                    }
                }
            }

            // Use method custom quick id only if no quick id on products could be found
            if (empty($productQuckId)
                && !empty($this->method->instance_settings['custom_quick_id'])
            ) {
                $quickId = $this->method->instance_settings['custom_quick_id'];
            }

            $shipment = array(
                'pdfConfig' => array(
                    'target1Media' => $this->method->settings['pdf_target1_media'],
                    'target1XOffset' => $this->method->settings['pdf_target1_xoffset'],
                    'target1YOffset' => $this->method->settings['pdf_target1_yoffset'],
                    'target2Media' => $this->method->settings['pdf_target2_media'],
                    'target2XOffset' => $this->method->settings['pdf_target2_xoffset'],
                    'target2YOffset' => $this->method->settings['pdf_target2_yoffset'],
                    'target3Media' => $this->method->settings['pdf_target3_media'],
                    'target3XOffset' => $this->method->settings['pdf_target3_xoffset'],
                    'target3YOffset' => $this->method->settings['pdf_target3_yoffset'],
                    'target4Media' => $this->method->settings['pdf_target4_media'],
                    'target4XOffset' => $this->method->settings['pdf_target4_xoffset'],
                    'target4YOffset' => $this->method->settings['pdf_target4_yoffset'],
                ),
                'shipment' => array(
                    'sender' => array(
                        'quickId' => $quickId,
                    ),
                    'parcels' => $this->getRequestParcels(),
                    'orderNo' => $this->order->get_order_number(),
                    'receiver' => array(
                        'address1' => $this->order->get_shipping_address_1(),
                        'address2' => $this->order->get_shipping_address_2(),
                        'city' => $this->order->get_shipping_city(),
                        'contact' => $this->order->get_shipping_first_name() . ' ' . $this->order->get_shipping_last_name(),
                        'country' => $this->order->get_shipping_country(),
                        'email' => $this->order->get_billing_email(),
                        'mobile' => $this->order->get_billing_phone(),
                        'name' => $this->order->get_shipping_first_name() . ' ' . $this->order->get_shipping_last_name(),
                        'phone' => $this->order->get_billing_phone(),
                        'zipcode' => $this->order->get_shipping_postcode(),
                    ),
                    'senderReference' => $this->getSenderReference(),
                    'service' => $serviceArray,
                    'receiverReference' => $this->getSenderReference(),
                    'options' => array(array(
                        'errorTo' => (string) $this->processDynamicReplacements($this->method->settings['options_error_to_email']),
                        'from' => (string) $this->processDynamicReplacements($this->method->settings['options_from_email']),
                        'id' => (string) $this->method->settings['options_id'],
                        'languageCode' => (string) $this->method->settings['options_language_code'],
                        'message' => (string) $this->processDynamicReplacements($this->method->settings['options_message']),
                        'sendEmail' => (!empty($this->method->settings['options_send_email'])
                            && $this->method->settings['options_send_email'] === 'yes'),
                        'to' => (string) $this->processDynamicReplacements($this->method->settings['options_to_email']),
                    ))
                )
            );

            // State logic
            if ($this->order->get_shipping_state()) {
                $countries = new WC_Countries();
                if ($countries->get_states($this->order->get_shipping_country())) {
                    $shipment['shipment']['receiver']['state'] =
                        $this->order->get_shipping_state();
                } else {
                    if (!empty($shipment['shipment']['receiver']['address2'])) {
                        $shipment['shipment']['receiver']['address2'] .= ', ';
                    }
                    $shipment['shipment']['receiver']['address2'] .=
                        $this->order->get_shipping_state();
                }
            }

            // Customs
            $customsStatNoAttribute = $this->method->instance_settings['customs_statno_attribute'] ?: '';
            $customsStatNoField = $this->method->instance_settings['customs_statno_field'] ?: '';
            $customsOriginCountryAttribute = $this->method->instance_settings['customs_origin_country'] ?: '';
            $customsOriginCountryField = $this->method->instance_settings['customs_origin_country_field'] ?: '';
            $customsValueAttribute = $this->method->instance_settings['customs_value'] ?: '';
            $customsValueField = $this->method->instance_settings['customs_value_field'] ?: '';
            $customsDocuments = array();
            $useCustoms = $customsStatNoAttribute
                || $customsStatNoField
                || $customsOriginCountryAttribute
                || $customsOriginCountryField;

            if ($useCustoms) {

                // Customs Documents
                foreach (array_keys(
                    Mediastrategi_UnifaunOnline::getCustomDeclarationDocumentOptions()) as $code
                ) {
                    if (!empty($code)
                        && !empty($this->method->instance_settings['customs_documents_' . $code])
                        && $this->method->instance_settings['customs_documents_' . $code] === 'yes'
                    ) {
                        $customsDocuments[] = $code;
                    }
                }

                // Customs Declaration
                $customsDeclarations = $this->getCustomsDeclaration(
                    $customsDocuments,
                    $customsStatNoAttribute,
                    $customsStatNoField,
                    $customsOriginCountryAttribute,
                    $customsOriginCountryField,
                    $customsValueAttribute,
                    $customsValueField
                );

                if (!empty($customsDeclarations)) {
                    $shipment['shipment']['customsDeclaration'] = $customsDeclarations;
                }
            }

            // Set pick-up-location agent if we have any
            if ($selectedAgent = Mediastrategi_UnifaunOnline_Order::getAgent(
                $this->order->get_id(),
                $this->package
            )) {
                if (!empty($selectedAgent['id'])) {
                    $shipment['shipment']['agent'] = [
                        'quickId' => $selectedAgent['id'],
                    ];
                } elseif (!empty($selectedAgent['quickId'])) {
                    $shipment['shipment']['agent'] = [
                        'quickId' => $selectedAgent['quickId'],
                    ];
                }
            }

            // Apply custom carrier logic here
            if ($selectedCarrierId) {
                $this->applyCarrier(
                    $selectedCarrierId,
                    $shipment,
                    $selectedAgent,
                    $selectedAddons
                );
            }

            // Apply add-ons here (if any)
            if (!empty($selectedAddons)) {
                $this->applyAddon(
                    $shipment,
                    $selectedAddons
                );
            }

            // Apply extra settings (if any)
            if ($extra = $this->method->instance_settings['extra']) {
                $extra = $this->processDynamicReplacements($extra);

                try {
                    $jsonDecodedExtra = json_decode($extra, true);
                } catch (\Exception $e) {
                    $jsonDecodedExtra = false;
                }
                if ($jsonDecodedExtra !== null
                    && is_array($jsonDecodedExtra)
                    && count($jsonDecodedExtra) > 0
                ) {
                    $shipment = array_merge_recursive(
                        $shipment,
                        $jsonDecodedExtra
                    );
                }
            }

            $error = false;
            $request = '';
            $responseCode = 0;
            $response = '';
            $decodedResponse = '';
            $shipmentNumber = '';
            $labelFilename = '';
            $additionalDocumentFilenames = '';
            $orderShipments = array();
            $trackingUrl = '';
            $createdStoredShipment = (!empty($this->method->instance_settings['stored_shipments'])
                && $this->method->instance_settings['stored_shipments'] === 'yes');

            // Allow for third-party customization here
            $shipment = apply_filters(
                'mediastrategi_unifaun_api_shipment',
                $shipment,
                $this->method,
                $this->order
            );

            // die('shipment: <pre>' . print_r($shipment, true) . print_r($selectedAddons, true));

            try {
                $test = $createdStoredShipment
                    ? $this->rest->storedShipmentsPost($shipment['shipment'])
                    : $this->rest->shipmentsPost($shipment);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            if ($test) {
                $request = $this->rest->getLastRequest();
                $responseCode = $this->rest->getLastResponseCode();
                $response = $this->rest->getLastResponse();
                $shipmentNumber = $this->rest->getLastShipmentNumber();
                $lastShipments = $this->rest->getLastShipments();
                $trackingUrl = $this->rest->getLastTrackingUrl();
                $decodedResponse = $this->rest->getLastDecodedResponse();

                $wp_upload_dir = wp_upload_dir();
                $extension = '.txt';

                if (!empty($lastShipments)
                    && is_array($lastShipments)
                ) {
                    $orderShipmentIndex = 0;
                    foreach ($lastShipments as $orderShipment)
                    {
                        /** @var \Mediastrategi\UnifaunOnline\Shipment $orderShipment */

                        // Collect labels
                        $labels = array();
                        $labelIndex = 0;
                        if ($byteLabels = $orderShipment->getLabels()) {
                            foreach ($byteLabels as $byteLabel)
                            {
                                $documentFilename = $wp_upload_dir['subdir']
                                    . \Mediastrategi_UnifaunOnline::UPLOAD_LABEL_PREFIX
                                    . $this->order->get_order_number()
                                    . '_' . $this->package
                                    . '_' . $orderShipmentIndex
                                    . '_' . $labelIndex
                                    . $extension;
                                $documentPath = $wp_upload_dir['basedir']
                                    . $documentFilename;
                                file_put_contents(
                                    $documentPath,
                                    $byteLabel
                                );
                                $labels[] = $documentFilename;

                                if (!$orderShipmentIndex) {
                                    if ($labelIndex) {
                                        if (!is_array($additionalDocumentFilenames)) {
                                            $additionalDocumentFilenames = array();
                                        }
                                        $additionalDocumentFilenames[] = $documentFilename;
                                    } else {
                                        $labelFilename = $documentFilename;
                                    }
                                }

                                $labelIndex++;
                            }
                        }

                        $orderShipments[] = array(
                            'lbl' => $labels,
                            'lnk' => $orderShipment->getTrackingLink(),
                            'nr' => $orderShipment->getNumber()
                        );
                        $orderShipmentIndex++;
                    }

                    if (is_array($additionalDocumentFilenames)) {
                        $additionalDocumentFilenames = implode(
                            ',',
                            $additionalDocumentFilenames
                        );
                    }
                }

                if (!empty($notify)) {
                    Mediastrategi_UnifaunOnline_Session::setMessageSuccess(
                        $this->package,
                        sprintf(
                            __(
                                'Succeeded with processing shipment for package %d (%d).',
                                'msunifaunonline'
                            ),
                            $this->package + 1,
                            $responseCode
                    ));
                }

            } else {
                $request = $this->rest->getLastRequest();
                $responseCode = $this->rest->getLastResponseCode();
                $response = $this->rest->getLastResponse();
                $error .= $this->rest->getLastErrorMessage();
                if ($decodedResponse = $this->rest->getLastDecodedResponse()) {
                    $newError = '<ul>';
                    if (!empty($decodedResponse)) {
                        $newErrors = 0;
                        foreach ($decodedResponse as $errorItem)
                        {
                            if (!empty($errorItem['message'])
                                && !empty($errorItem['location'])
                            ) {
                                $newError .= sprintf(
                                    '<li>%s (%s)</li>',
                                    $errorItem['message'],
                                    $errorItem['location']
                                );
                                $newErrors++;
                            }
                        }
                        $newError .= '</ul>';
                        if (!empty($newErrors)) {
                            $error = $newError;
                        }
                    }
                }
                if ($responseCode == 401) {
                    if (!empty($newError)) {
                        $error .= 'Invalid or expired token.';
                    } else {
                        $error = 'Invalid or expired token.';
                    }
                } else if ($responseCode == 403) {
                    if (!empty($newError)) {
                        $error .= "The token is valid but it doesn't grant access to the operation attempted.";
                    } else {
                        $error = "The token is valid but it doesn't grant access to the operation attempted.";
                    }
                }

                if (!empty($notify)) {
                    Mediastrategi_UnifaunOnline_Session::setMessageError(
                        $this->package,
                        sprintf(
                            __(
                                'Failed with processing shipment for package %d "/shipments POST". Status: (%d), Errors: %s',
                                'msunifaunonline'
                            ),
                            $this->package + 1,
                            $responseCode,
                            $error
                    ));
                }
            }

            Mediastrategi_UnifaunOnline_Order::setStatus(
                $this->order->get_id(),
                $this->package,
                $test
            );
            Mediastrategi_UnifaunOnline_Order::setShipment(
                $this->order->get_id(),
                $this->package,
                true
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentRequest(
                $this->order->get_id(),
                $this->package,
                $request
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentResponseBodyRaw(
                $this->order->get_id(),
                $this->package,
                $response
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentResponseBodyDecoded(
                $this->order->get_id(),
                $this->package,
                $decodedResponse
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentResponseCode(
                $this->order->get_id(),
                $this->package,
                $responseCode
            );
            Mediastrategi_UnifaunOnline_Order::setTrackingUrl(
                $this->order->get_id(),
                $this->package,
                $trackingUrl
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentPrintFile(
                $this->order->get_id(),
                $this->package,
                $labelFilename
            );
            Mediastrategi_UnifaunOnline_Order::setShippingAdditionalLabels(
                $this->order->get_id(),
                $this->package,
                $additionalDocumentFilenames
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentNumber(
                $this->order->get_id(),
                $this->package,
                $shipmentNumber
            );
            Mediastrategi_UnifaunOnline_Order::setShipments(
                $this->order->get_id(),
                $this->package,
                $orderShipments
            );
            Mediastrategi_UnifaunOnline_Order::setShipmentErrors(
                $this->order->get_id(),
                $this->package,
                $error
            );
            return $test;
        } else {

            if (!empty($notify)) {
                Mediastrategi_UnifaunOnline_Session::setMessageError(
                    $this->package,
                    __(
                        'Missing API Key Id, API Key Secret, User Id or Quick Id. Check your plug-in settings.',
                        'msunifaunonline'
                ));
            }
            return false;
        }
    }

    /**
     * @param array $shipment
     * @param array $addons
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    private function applyAddon(& $shipment, $addons)
    {
        if (isset($shipment['shipment']['service']['addons'])) {
            foreach ($shipment['shipment']['service']['addons'] as &$addon)
            {
                if (!empty($addon['id'])
                    && isset($addons[$addon['id']])
                    && is_array($addons[$addon['id']])
                    && count($addons[$addon['id']])
                ) {
                    foreach ($addons[$addon['id']] as $elementId => $value)
                    {
                        $value = $this->processDynamicReplacements($value);

                        // Remove spaces and replace commas with dots in numbers
                        if (preg_match(
                            '/^[0-9,\ \.\+\-\*\/]+$/',
                            $value) === 1
                        ) {
                            $value = str_replace(
                                array(' ', ','),
                                array('', '.'),
                                $value
                            );
                        }

                        // Evaluate math expressions
                        if (preg_match(
                            '/^[0-9]+[\+\-\*\/][0-9]+$/',
                            $value) === 1
                        ) {
                            // Remove spaces first
                            $value = str_replace(
                                ' ',
                                '',
                                $value
                            );
                            $value = eval(sprintf(
                                'return %s;',
                                $value
                            ));
                        }

                        $addon[$elementId] = (string) trim($value);
                    }
                }
            }
        }
    }

    /**
     * @param string $carrier
     * @param array $shipment
     * @param array $agent
     * @param array $addons
     * @throws \Exception
     */
    private function applyCarrier($carrier, & $shipment, $agent, $addons)
    {
        $carrier = strtoupper($carrier);
        $path = dirname(__FILE__) . '/Carrier/' . $carrier . '.php';
        if (file_exists($path)) {
            $this->log(sprintf(
                __(
                    'Specific carrier logic file "%s" exists',
                    'msunifaunonline'
                ),
                $path
            ));
            require_once($path);
            $className = '\\Mediastrategi\\UnifaunOnline\\Libraries\\Carrier\\' . $carrier;
            if (class_exists($className, false)) {
                $this->log(sprintf(
                    __(
                        'Class for specific carrier logic exists "%s"',
                        'msunifaunonline'
                    ),
                    $className
                ));
                $class = new $className();
                if (method_exists($class, 'apply')) {
                    $class->apply($shipment, $agent, $addons);
                    $this->log(sprintf(
                        __(
                            '%s - data after applying specific carrier logic "%s" and shipment "%s"',
                            'msunifaunonline'
                        ),
                        __METHOD__,
                        $carrier,
                        print_r($shipment, true)
                    ));
                }
            } else {
                throw new \Exception(sprintf(
                    __(
                        'Class for specific carrier logic does not exists "%s" in "%s"',
                        'msunifaunonline'
                    ),
                    $className,
                    $path
                ));
            }
        }
    }

    /**
     * @return string
     */
    private function getSenderReference()
    {
        $prefix = $this->method->get_option(
            'options_sender_reference_prefix'
        );
        if (empty($prefix)) {
            $prefix = '';
        }
        $prefix2 = $this->method->get_option(
            'options_sender_reference_prefix_option'
        );
        if (!empty($prefix2)) {
            if ($prefix2 == 'product_skus') {
                $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                    $this->order,
                    $this->rate
                );
                $prefix2 = Mediastrategi_UnifaunOnline_Woocommerce::getProductsDescription(
                    $packageItems,
                    'skus'
                );
            } else {
                $prefix2 = '';
            }
        }
        if (empty($prefix2)) {
            $prefix2 = '';
        }
        return $prefix . $prefix2 . $this->order->get_order_number()
            . '_' . ($this->package + 1);
    }

    /**
     * @param array $printSet
     * @param string [$statNoAttribute = '']
     * @param string [$statNoField = '']
     * @param string [$customsOriginCountryAttribute = '']
     * @param string [$customsOriginCountryField = '']
     * @param string [$customsValueAttribute = '']
     * @param string [$customsValueField = '']
     * @return array
     */
    private function getCustomsDeclaration(
        $printSet,
        $statNoAttribute = '',
        $statNoField = '',
        $customsOriginCountryAttribute = '',
        $customsOriginCountryField = '',
        $customsValueAttribute = '',
        $customsValueField = ''
    ) {
        $customsDeclarations = array();
        if (!empty($printSet)) {
            $customsDeclarations['printSet'] = $printSet;
        }

        if (!empty($statNoAttribute)
            || !empty($statNoField)
        ) {
            $lines = array();
            $statNoData = array();
            $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                $this->order,
                $this->rate
            );
            if ($packageItems) {
                foreach ($packageItems as $orderItem)
                {
                    $orderItem = $orderItem['data'];
                    /** @var WC_Order_Item_Product $orderItem */

                    // Get product
                    $referenceId = $orderItem->get_variation_id()
                        ? $orderItem->get_variation_id()
                        : $orderItem->get_product_id();

                    $singleProduct = wc_get_product(
                        $referenceId
                    );

                    if (!$singleProduct) {
                        continue;
                    }

                    // Set basic data
                    $copies = method_exists($orderItem, 'get_quantity')
                        ? $orderItem->get_quantity()
                        : $orderItem->quantity;
                    $netWeight = $singleProduct->get_weight() * $copies;
                    $statNo = '';
                    $originCountry = '';
                    $customsValue = $singleProduct->get_price();

                    // HS Code
                    if (!empty($statNoAttribute)) {
                        if (!empty($singleProduct->attributes[$statNoAttribute])) {
                            $attribute = $singleProduct->attributes[$statNoAttribute];
                            if ($attributeOptions = $attribute->get_options()) {
                                if ($attributeOption = get_term(
                                    reset($attributeOptions),
                                    $attribute->get_taxonomy()
                                )) {
                                    if (!empty($attributeOption->name)) {
                                        $statNo = $attributeOption->name;
                                    }
                                }
                            }
                        }
                    } elseif (!empty($statNoField)) {
                        if ($postMeta = get_post_meta(
                            $referenceId,
                            $statNoField,
                            true
                        )) {
                            $statNo = (string) $postMeta;
                        }
                    }

                    // Product Origin Country
                    if (!empty($customsOriginCountryAttribute)
                        && !empty($singleProduct->attributes[$customsOriginCountryAttribute])
                    ) {
                        $attribute = $singleProduct->attributes[$customsOriginCountryAttribute];
                        if ($attributeOptions = $attribute->get_options()) {
                            if ($attributeOption = get_term(
                                reset($attributeOptions),
                                $attribute->get_taxonomy()
                            )) {
                                if (!empty($attributeOption->name)) {
                                    $originCountry = $attributeOption->name;
                                }
                            }
                        }
                    } elseif (!empty($customsOriginCountryField)) {
                        if ($postMeta = get_post_meta(
                            $referenceId,
                            $customsOriginCountryField,
                            true
                        )) {
                            $originCountry = (string) $postMeta;
                        }
                    }

                    // Customs Value
                    if (!empty($customsValueAttribute)
                        && !empty($singleProduct->attributes[$customsValueAttribute])
                    ) {
                        $attribute = $singleProduct->attributes[$customsValueAttribute];
                        if ($attributeOptions = $attribute->get_options()) {
                            if ($attributeOption = get_term(
                                reset($attributeOptions),
                                $attribute->get_taxonomy()
                            )) {
                                if (!empty($attributeOption->name)) {
                                    $customsValue = $attributeOption->name;
                                }
                            }
                        }
                    } elseif (!empty($customsValueField)) {
                        if ($postMeta = get_post_meta(
                            $referenceId,
                            $customsValueField,
                            true
                        )) {
                            $customsValue = (string) $postMeta;
                        }
                    }

                    // Did we find a HS Code?
                    if (!empty($statNo)) {
                        if (!isset($statNoData[$statNo])) {
                            $statNoData[$statNo] = array(
                                'products' => array(),
                                'copies' => 0,
                                'netWeight' => 0,
                                'value' => 0,
                            );
                        }

                        $statNoData[$statNo]['products'][] = $orderItem;
                        $statNoData[$statNo]['copies'] += $copies;
                        $statNoData[$statNo]['netWeight'] += $netWeight;
                        $statNoData[$statNo]['value'] += $customsValue;
                        if (!empty($originCountry)) {
                            $statNoData[$statNo]['sourceCountryCode'] = $originCountry;
                        }
                    }
                }

                // Did we find customs information?
                if (!empty($statNoData)) {
                    foreach ($statNoData as $statNo => $data)
                    {
                        $line = array(
                            'contents' => Mediastrategi_UnifaunOnline_Woocommerce::getProductsDescription($data['products']),
                            'copies' => $data['copies'],
                            'netWeight' => $data['netWeight'],
                            'statNo' => $statNo,
                            'value' => $data['value'],
                            'valuesPerItem' => true,
                        );
                        if (!empty($data['sourceCountryCode'])) {
                            $line['sourceCountryCode'] = $data['sourceCountryCode'];
                        }
                        $lines[] = $line;
                    }
                }
            }

            // Did we find lines for customs?
            if (!empty($lines)) {
                $customsDeclarations['lines'] = $lines;
            }
        }

        return $customsDeclarations;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getRequestParcels()
    {
        $parcels = array();
        $customPackages = Mediastrategi_UnifaunOnline_Order::getCustomPackages(
            $this->order->get_id(),
            $this->package
        );

        // Do we have custom packages?
        if ($customPackages) {
            foreach ($customPackages as $rawPackage)
            {
                if (!empty($rawPackage['copies'])
                    && !empty($rawPackage['packageCode'])
                    && !empty($rawPackage['weight'])
                ) {
                    $parcel = array(
                        'copies' => (string) $rawPackage['copies'],
                        'packageCode' => (string) $rawPackage['packageCode'],
                        'valuePerParcel' => true, // TODO: Make this dynamic?
                        'weight' => (string) $rawPackage['weight'],
                    );
                    if (!empty($rawPackage['contents'])) {
                        $parcel['contents'] = (string) $rawPackage['contents'];
                    }
                    if (!empty($rawPackage['height'])) {
                        $parcel['height'] = (string) $rawPackage['height'];
                    }
                    if (!empty($rawPackage['length'])) {
                        $parcel['length'] = (string) $rawPackage['length'];
                    }
                    if (!empty($rawPackage['width'])) {
                        $parcel['width'] = (string) $rawPackage['width'];
                    }
                    $parcels[] = $parcel;
                }
            }
        }

        if (empty($parcels)) {

            $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                $this->order,
                $this->rate
            );

            // Calculate order size
            $dimensions = Mediastrategi_UnifaunOnline::getOrderSize(
                $packageItems,
                $this->method->get_option('dimension_unit'),
                $this->method->get_option('weight_unit'),
                $this->method->get_option('minimum_weight'),
                $this->method->get_option('package_force_largest_dimension'),
                $this->method->get_option('package_force_medium_dimension'),
                $this->method->get_option('package_force_smallest_dimension')
            );

            // Shoud we ignore calculated dimensions?
            if ($this->method->get_option('ignore_calculated_dimensions') === 'yes') {
                $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                    $this->order,
                    $this->rate
                );
                $parcels[] = array(
                    'contents' => Mediastrategi_UnifaunOnline_Woocommerce::getProductsDescription($packageItems),
                    'copies' => 1,
                    'packageCode' => ($this->method->get_option('package_type') === '-'
                        ? $this->method->get_option('package_type_custom')
                        : $this->method->get_option('package_type')),
                    'valuePerParcel' => true, // TODO: Make this dynamic?
                    'weight' => $dimensions['weight'],
                );
            } else {
                $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                    $this->order,
                    $this->rate
                );
                $parcels[] = array(
                    'contents' => Mediastrategi_UnifaunOnline_Woocommerce::getProductsDescription($packageItems),
                    'copies' => 1,
                    'height' => $dimensions['height'],
                    'length' => $dimensions['length'],
                    'packageCode' => ($this->method->get_option('package_type') === '-'
                        ? $this->method->get_option('package_type_custom')
                        : $this->method->get_option('package_type')),
                    'valuePerParcel' => true, // TODO: Make this dynamic?
                    'weight' => $dimensions['weight'],
                    'width' => $dimensions['width'],
                );
            }
        }
        if (empty($parcels)) {
            $packageItems = Mediastrategi_UnifaunOnline::getOrderRateItems(
                $this->order,
                $this->rate
            );
            throw new \Exception(sprintf(
                __(
                    'Request generated no parcels, parcels: %s, order-items: %s',
                    'msunifaunonline'
                ),
                print_r($parcels, true),
                print_r($packageItems, true)
            ));
        }
        return $parcels;
    }

    /**
     * @param string $message
     */
    private function log($message)
    {
        $this->method->log($message);
    }

    /**
     * @param string $value
     * @return string
     */
    private function processDynamicReplacements($value)
    {
        if (!empty($value)
            && !empty($this->order)
        ) {
            // Support variable replacements here
            $replacements = array(
                '$consignee_address_1' => $this->order->get_shipping_address_1(),
                '$consignee_address_2' => $this->order->get_shipping_address_2(),
                '$consignee_name' => $this->order->get_shipping_first_name() . ' ' . $this->order->get_shipping_last_name(),
                '$consignee_city' => $this->order->get_shipping_city(),
                '$consignee_company' => $this->order->get_shipping_company(),
                '$consignee_country' => $this->order->get_shipping_country(),
                '$consignee_postcode' => $this->order->get_shipping_postcode(),
                '$consignee_state' => $this->order->get_shipping_state(),
                '$customer_email' => $this->order->get_billing_email(),
                '$customer_mobile' => $this->order->get_billing_phone(),
                '$order_id' => $this->order->get_id(),
                '$order_number' => $this->order->get_order_number(),
                '$order_subtotal' => $this->order->get_subtotal(),
                '$package_number' => ($this->package + 1)
            );

            $value = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $value
            );
        }
        return $value;
    }

}
