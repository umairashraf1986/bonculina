<?php
/**
 * Class InvoiceRequestData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Invoice data.
 */
class Invoice implements \JsonSerializable {

	/**
	 * @var string
	 */
	private $country_of_origin;

	/**
	 * @var string
	 */
	private $invoice_customs_number;

	/**
	 * @var InvoiceCustomer
	 */
	private $invoice_delivery_details;

	/**
	 * @var string
	 */
	private $invoice_export_reason;

	/**
	 * @var string
	 */
	private $invoice_terms_of_delivery;

	/**
	 * @var string
	 */
	private $invoice_reference;

	/**
	 * @var InvoiceCustomer
	 */
	private $invoice_shipper_details;

	/**
	 * @var string
	 */
	private $invoice_type;

	/**
	 * @var float
	 */
	private $shipping_cost;

	/**
	 * InvoiceData constructor.
	 *
	 * @param string          $country_of_origin .
	 * @param string          $invoice_customs_number .
	 * @param InvoiceCustomer $invoice_delivery_details .
	 * @param string          $invoice_export_reason .
	 * @param string          $invoice_terms_of_delivery .
	 * @param string          $invoice_reference .
	 * @param InvoiceCustomer $invoice_shipper_details .
	 * @param string          $invoice_type .
	 * @param float           $shipping_cost .
	 */
	public function __construct(
		$country_of_origin,
		$invoice_customs_number,
		$invoice_delivery_details,
		$invoice_export_reason,
		$invoice_terms_of_delivery,
		$invoice_reference,
		$invoice_shipper_details,
		$invoice_type,
		$shipping_cost
	) {
		$this->country_of_origin         = $country_of_origin;
		$this->invoice_customs_number    = $invoice_customs_number;
		$this->invoice_delivery_details  = $invoice_delivery_details;
		$this->invoice_export_reason     = $invoice_export_reason;
		$this->invoice_terms_of_delivery = $invoice_terms_of_delivery;
		$this->invoice_reference         = $invoice_reference;
		$this->invoice_shipper_details   = $invoice_shipper_details;
		$this->invoice_type              = $invoice_type;
		$this->shipping_cost             = $shipping_cost;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$serialized = array(
			'countryOfOrigin'        => $this->country_of_origin,
			'invoiceExportReason'    => $this->invoice_export_reason,
			'invoiceTermsOfDelivery' => $this->invoice_terms_of_delivery,
			'invoiceType'            => $this->invoice_type,
			'shippingCost'           => $this->shipping_cost,
		);

		if ( ! empty( $this->invoice_customs_number ) ) {
			$serialized['invoiceCustomsNumber'] = $this->invoice_customs_number;
		}

		if ( ! empty( $this->invoice_delivery_details ) ) {
			$serialized['invoiceDeliveryDetails'] = $this->invoice_delivery_details;
		}

		if ( ! empty( $this->invoice_reference ) ) {
			$serialized['invoiceReference'] = $this->invoice_reference;
		}

		if ( ! empty( $this->invoice_shipper_details ) ) {
			$serialized['invoiceShipperDetails'] = $this->invoice_shipper_details;
		}

		return $serialized;
	}

}
