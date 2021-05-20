<?php
/**
 * Class InvoiceDataFactory
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Invoice data factory.
 */
class InvoiceFactory {

	/**
	 * @param \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment .
	 * @param string                                    $country_of_origin .
	 * @param string                                    $customs_number .
	 * @param string                                    $export_reason .
	 * @param string                                    $terms_of_delivery .
	 * @param string                                    $vat_number .
	 * @param string                                    $invoice_type .
	 * @param string                                    $sender_country .
	 * @param string                                    $sender_county .
	 * @param string                                    $sender_locality .
	 * @param string                                    $sender_organisation .
	 * @param string                                    $sender_postcode .
	 * @param string                                    $sender_street .
	 * @param string                                    $sender_town .
	 * @param string                                    $sender_name .
	 * @param string                                    $sender_phone .
	 *
	 * @return Invoice
	 */
	public function create_for_shipment(
		\WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment,
		$country_of_origin,
		$customs_number,
		$export_reason,
		$terms_of_delivery,
		$vat_number,
		$invoice_type,
		$sender_country,
		$sender_county,
		$sender_locality,
		$sender_organisation,
		$sender_postcode,
		$sender_street,
		$sender_town,
		$sender_name,
		$sender_phone
	) {
		$order = $shipment->get_order();

		return new Invoice(
			$country_of_origin,
			$customs_number,
			new InvoiceCustomer(
				new Address(
					$order->get_shipping_country(),
					null,
					trim( $order->get_shipping_address_2() ),
					$order->get_shipping_company(),
					strtoupper( str_replace( array( '-', ' ' ), '', $order->get_shipping_postcode() ) ),
					trim( $order->get_shipping_address_1() ),
					$order->get_shipping_city()
				),
				new ContactDetails(
					$order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
					$order->get_billing_phone()
				),
				null
			),
			$export_reason,
			$terms_of_delivery,
			null,
			new InvoiceCustomer(
				new Address(
					$sender_country,
					$sender_county,
					$sender_locality,
					$sender_organisation,
					$sender_postcode,
					$sender_street,
					$sender_town
				),
				new ContactDetails(
					$sender_name,
					$sender_phone
				),
				$vat_number
			),
			$invoice_type,
			0.0
		);

	}

}
