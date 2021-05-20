<?php
class Mediastrategi_UnifaunOnline_Order
{

    const CUSTOM_PACKAGES = '_msunifaun_online__order_custom_packages';
    const AGENT = '_msunifaun_online__order_selected_agent';
    const AGENT_SERVICE = '_msunifaun_online__order_agent_service';
    const SHIPMENT = '_msunifaun_online__order_shipment';
    const SHIPMENT_ERRORS = '_msunifaun_online__order_shipment_errors';
    const SHIPMENT_NUMBER = '_msunifaun_online__order_shipment_number';
    const SHIPMENT_PRINT_FILE = '_msunifaun_online__order_print_file';
    const SHIPMENT_REQUEST = '_msunifaun_online__order_shipment_request';
    const SHIPMENT_RESPONSE_BODY_RAW = '_msunifaun_online__order_shipment_response_body';
    const SHIPMENT_RESPONSE_BODY_DECODED = '_msunifaun_online__order_shipment_response_body_decoded';
    const SHIPMENT_RESPONSE_CODE = '_msunifaun_online__order_shipment_response_code';
    const SHIPMENTS = '_msunifaun_online__shipments';
    const SHIPPING_ADDITIONAL_LABELS = '_msunifaun_online__order_shipping_additional_labels';
    const SHIPPING_LABEL = '_msunifaun_online__order_shipping_label';
    const STATUS = '_msunifaun_online__order_status';
    const TRACKING_URL = '_msunifaun_online__order_tracking_url';
    const USE_CUSTOM_PACKAGES = '_msunifaun_online__order_use_custom_packages';

    public static function getCustomPackages($id, $package) { return self::get($id, $package, self::CUSTOM_PACKAGES); }
    public static function getAgent($id, $package) { return self::get($id, $package, self::AGENT); }
    public static function getAgentService($id, $package) { return self::get($id, $package, self::AGENT_SERVICE); }
    public static function getShipment($id, $package) { return self::get($id, $package, self::SHIPMENT); }
    public static function getShipmentErrors($id, $package) { return self::get($id, $package, self::SHIPMENT_ERRORS); }
    public static function getShipmentNumber($id, $package) { return self::get($id, $package, self::SHIPMENT_NUMBER); }
    public static function getShipmentPrintFile($id, $package) { return self::get($id, $package, self::SHIPMENT_PRINT_FILE); }
    public static function getShipmentRequest($id, $package) { return self::get($id, $package, self::SHIPMENT_REQUEST); }
    public static function getShipmentResponseBodyRaw($id, $package) { return self::get($id, $package, self::SHIPMENT_RESPONSE_BODY_RAW); }
    public static function getShipmentResponseBodyDecoded($id, $package) { return self::get($id, $package, self::SHIPMENT_RESPONSE_BODY_DECODED); }
    public static function getShipmentResponseCode($id, $package) { return self::get($id, $package, self::SHIPMENT_RESPONSE_CODE); }
    public static function getShipments($id, $package) { return self::get($id, $package, self::SHIPMENTS); }
    public static function getShippingAdditionalLabels($id, $package) { return self::get($id, $package, self::SHIPPING_ADDITIONAL_LABELS); }
    public static function getShippingLabel($id, $package) { return self::get($id, $package, self::SHIPPING_LABEL); }
    public static function getStatus($id, $package) { return self::get($id, $package, self::STATUS); }
    public static function getTrackingUrl($id, $package) { return self::get($id, $package, self::TRACKING_URL); }
    public static function getUseCustomPackages($id, $package) { return self::get($id, $package, self::USE_CUSTOM_PACKAGES); }

    public static function setCustomPackages($id, $package, $value) { return self::set($id, $package, self::CUSTOM_PACKAGES, $value); }
    public static function setAgent($id, $package, $value) { return self::set($id, $package, self::AGENT, $value); }
    public static function setAgentService($id, $package, $value) { return self::set($id, $package, self::AGENT_SERVICE, $value); }
    public static function setShipment($id, $package, $value) { return self::set($id, $package, self::SHIPMENT, $value); }
    public static function setShipmentErrors($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_ERRORS, $value); }
    public static function setShipmentNumber($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_NUMBER, $value); }
    public static function setShipmentPrintFile($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_PRINT_FILE, $value); }
    public static function setShipmentRequest($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_REQUEST, $value); }
    public static function setShipmentResponseBodyRaw($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_RESPONSE_BODY_RAW, $value); }
    public static function setShipmentResponseBodyDecoded($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_RESPONSE_BODY_DECODED, $value); }
    public static function setShipmentResponseCode($id, $package, $value) { return self::set($id, $package, self::SHIPMENT_RESPONSE_CODE, $value); }
    public static function setShipments($id, $package, $value) { return self::set($id, $package, self::SHIPMENTS, $value); }
    public static function setShippingAdditionalLabels($id, $package, $value) { return self::set($id, $package, self::SHIPPING_ADDITIONAL_LABELS, $value); }
    public static function setShippingLabel($id, $package, $value) { return self::set($id, $package, self::SHIPPING_LABEL, $value); }
    public static function setStatus($id, $package, $value) { return self::set($id, $package, self::STATUS, $value); }
    public static function setTrackingUrl($id, $package, $value) { return self::set($id, $package, self::TRACKING_URL, $value); }
    public static function setUseCustomPackages($id, $package, $value) { return self::set($id, $package, self::USE_CUSTOM_PACKAGES, $value); }


    /**
     * @param int $id
     * @param string|null [$package = null]
     * @param string $key
     * @return mixed
     */
    private static function get($id, $package = null, $key)
    {
        $value = get_post_meta(
            $id,
            $key,
            true
        );
        if (isset($package)) {
            if (isset($value)
                && is_array($value)
                && isset($value[$package])
            ) {
                $value = $value[$package];
            } elseif (isset($value)
                && (!is_array($value)
                    || !\Mediastrategi_UnifaunOnline::isNumericArray($value))
            ) {
                $value = $value;
            } else {
                $value = null;
            }
        }
        return $value;
    }

    /**
     * @param int $id
     * @param string|null [$package = null]
     * @param string $key
     * @param mixed $value
     */
    private static function set($id, $package = null, $key, $value)
    {
        if (isset($package)) {
            $oldValue = self::get(
                $id,
                null,
                $key
            );
            if (!is_array($oldValue)) {
                $oldValue = array($oldValue);
            }
            $oldValue[$package] = $value;
            update_post_meta(
                $id,
                $key,
                $oldValue
            );
        } else {
            update_post_meta(
                $id,
                $key,
                $value
            );
        }
    }

}
