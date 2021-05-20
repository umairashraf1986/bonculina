<?php
/**
 * Class ShipmentRequestFactory
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

use WPDesk\DpdUk\Api\Request\Exception\InvalidCurrencyException;
use WPDesk\DpdUk\Api\Request\Exception\InvalidNumberOfParcelsException;

/**
 * Can create shipment request.
 */
class ShipmentRequestFactory {

	/**
	 * @param \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment .
	 * @param array                                     $settings .
	 *
	 * @return ShipmentRequest
	 *
	 * @throws \Exception .
	 * @throws InvalidCurrencyException|InvalidNumberOfParcelsException .
	 */
	public function create_for_shipment_and_settings( \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment, array $settings ) {

		$order = $shipment->get_order();

		$collection_on_delivery = false;
		$generate_customs_data  = $this->should_generate_customs_data( $order ) ? 'Y' : 'N';
		$consolidate            = 1 === (int) $shipment->get_meta( $shipment::DPD_UK_CONSOLIDATE_META_KEY, '0' );

		return new ShipmentRequest(
			$collection_on_delivery,
			$generate_customs_data,
			$this->should_generate_customs_data( $order ) ? $this->prepare_invoice_data_for_shipment_and_settings( $shipment, $settings ) : null,
			$this->prepare_collection_date_from_settings( $settings ),
			$consolidate,
			$this->prepare_consignment_request_data_for_shipment_and_settings( $shipment, $settings )
		);
	}

	/**
	 * Prepare collection date from shipping method settings.
	 *
	 * @param array $settings .
	 *
	 * @return \DateTime
	 *
	 * @throws \Exception .
	 */
	private function prepare_collection_date_from_settings( array $settings ) {
		$current_time    = current_time( 'timestamp' ); // phpcs:ignore
		$collection_date = new \DateTime( date( 'Y-m-d\TH:i:s', $current_time ) ); // phpcs:ignore

		$hour_changing_shipment_date = $settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_HOUR_CHANGING_SHIPMENT_DATE ];
		if ( '' !== $hour_changing_shipment_date && is_numeric( $hour_changing_shipment_date ) ) {
			$hour_changing_shipment_date = intval( $hour_changing_shipment_date );
			if ( $hour_changing_shipment_date <= intval( date( 'H', $current_time ) ) ) { // phpcs:ignore
				$collection_date = new \DateTime( date( 'Y-m-d\T00:00:00', $current_time ) ); // phpcs:ignore
				$collection_date->add( new \DateInterval( 'P1D' ) );
			}
		}

		return $collection_date;
	}

	/**
	 * @param string $country .
	 *
	 * @return bool
	 */
	private function is_gb( $country ) {
		return 'GB' === $country;
	}

	/**
	 * @param string $country .
	 * @param string $postcode .
	 *
	 * @return bool
	 */
	private function is_northern_ireland( $country, $postcode ) {
		return $this->is_gb( $country ) && 0 === strpos( trim( $postcode ), 'BT' );
	}

	/**
	 * @param string $country .
	 * @param string $postcode .
	 *
	 * @return bool
	 */
	private function is_jersey( $country, $postcode ) {
		return $this->is_gb( $country ) && 0 === strpos( trim( $postcode ), 'JE' );
	}

	/**
	 * @param \WC_Order $order .
	 *
	 * @return bool
	 */
	private function should_generate_customs_data( \WC_Order $order ) {
		return ! $this->is_gb( $order->get_shipping_country() )
			|| $this->is_northern_ireland( $order->get_shipping_country(), $order->get_shipping_postcode() )
			|| $this->is_jersey( $order->get_shipping_country(), $order->get_shipping_postcode() );
	}

	/**
	 * @param \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment .
	 * @param array                                     $settings .
	 *
	 * @return Consignment[]
	 * @throws InvalidCurrencyException|InvalidNumberOfParcelsException .
	 */
	private function prepare_consignment_request_data_for_shipment_and_settings( \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment, array $settings ) {
		$order = $shipment->get_order();

		$number_of_parcels = (int) $shipment->get_meta( '_dpd_uk_number_of_parcels', 1 );

		if ( $this->should_generate_customs_data( $order ) && $number_of_parcels > 1 ) {
			throw new InvalidNumberOfParcelsException(
				sprintf(
					// Translators: support link.
					__( 'This international shipment may include only 1 package. Should you require any further information please contact us directly at %1$shttps://flexibleshipping.com/support/%2$s or %3$slearn more about international shipping →%4$s', 'woocommerce-dpd-uk' ),
					'<a href="https://flexibleshipping.com/support/" target="_blank">',
					'</a>',
					'<a href="https://docs.flexibleshipping.com/article/56-woocommerce-dpd-uk-faq?utm_source=dpd-uk-metabox&utm_medium=link&utm_campaign=dpd-uk-international" target="_blank">',
					'</a>'
				)
			);
		}

		return array(
			new Consignment(
				new CollectionDetails(
					new Address(
						$settings['sender_country'],
						$settings['sender_county'],
						$settings['sender_locality'],
						$settings['sender_organisation'],
						$settings['sender_postcode'],
						$settings['sender_street'],
						$settings['sender_town']
					),
					new ContactDetails(
						$settings['sender_name'],
						$settings['sender_phone']
					)
				),
				null,
				null,
				$this->prepare_customs_value( $order ),
				new DeliveryDetails(
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
					new NotificationDetails(
						$order->get_billing_email(),
						$order->get_billing_phone()
					)
				),
				$shipment->get_meta( '_dpd_uk_delivery_instructions', '' ),
				( (int) $shipment->get_meta( '_dpd_uk_liability', '0' ) ) === 1,
				(float) $shipment->get_meta( '_dpd_uk_liability_value', null ),
				$shipment->get_meta( '_dpd_uk_service' ),
				$number_of_parcels,
				$this->prepare_parcel( $order, $settings ),
				$shipment->get_meta( '_dpd_uk_parcel_description', '' ),
				null,
				$shipment->get_meta( '_dpd_uk_reference1', '' ),
				$shipment->get_meta( '_dpd_uk_reference2', '' ),
				$shipment->get_meta( '_dpd_uk_reference3', '' ),
				$this->prepare_total_weight( $shipment ),
				null
			),
		);
	}

	/**
	 * @param \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment .
	 *
	 * @return float
	 */
	private function prepare_total_weight( \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment ) {
		// DPD UK API requires at least 0.1 for package total weight.
		return max( (float) $shipment->get_meta( '_dpd_uk_weight', 0 ), 0.1 );
	}

	/**
	 * @param \WC_Order $order .
	 * @param array     $settings .
	 *
	 * @return Parcel[]
	 * @throws InvalidCurrencyException .
	 */
	private function prepare_parcel( \WC_Order $order, array $settings ) {
		if ( $this->should_generate_customs_data( $order ) ) {

			if ( 'GBP' !== $order->get_currency() ) {
				throw new InvalidCurrencyException();
			}

			$parcel_products = array();

			/** @var \WC_Order_Item_Product $item */
			foreach ( $order->get_items() as $item ) {

				$product = $item->get_product();

				$origin_country = $product->get_attribute( $settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_PRODUCT_COUNTRY_OF_ORIGIN_ATTRIBUTE ] );
				$origin_country = empty( $origin_country ) ? $settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_PRODUCT_COUNTRY_OF_ORIGIN_DEFAULT ] : $origin_country;

				$harmonised_code = $product->get_attribute( $settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_PRODUCT_HARMONISED_CODE_ATTRIBUTE ] );
				$harmonised_code = empty( $harmonised_code ) ? $settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_PRODUCT_HARMONISED_CODE_DEFAULT ] : $harmonised_code;

				$parcel_product = new ParcelProduct(
					$origin_country,
					$item->get_quantity(),
					$item->get_product()->get_sku(),
					null,
					$harmonised_code,
					$item->get_name(),
					null,
					get_permalink( $product->get_id() ),
					$item->get_total(),
					wc_get_weight( $product->get_weight(), 'kg' )
				);

				$parcel_products[] = $parcel_product;
			}

			return array(
				new Parcel(
					1,
					$parcel_products
				),
			);
		}

		return array();
	}

	/**
	 * Prepare customs value
	 *
	 * @param \WC_Order $order Order.
	 *
	 * @return float|null
	 */
	private function prepare_customs_value( \WC_Order $order ) {
		return $this->should_generate_customs_data( $order ) ? (float) $order->get_total() : null;
	}


	/**
	 * @param \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment .
	 * @param array                                     $settings .
	 *
	 * @return Invoice
	 */
	private function prepare_invoice_data_for_shipment_and_settings( \WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment, $settings ) {
		return ( new InvoiceFactory() )->create_for_shipment(
			$shipment,
			$settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_INVOICE_COUNTRY_OF_ORIGIN ],
			$settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_INVOICE_CUSTOMS_NUMBER ],
			$settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_INVOICE_EXPORT_REASON ],
			$settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_INVOICE_TERMS_OF_DELIVERY ],
			$settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_INVOICE_VAT_NUMBER ],
			$settings[ \WPDesk_WooCommerce_DPD_UK_Shipping_Method::SETTING_INVOICE_TYPE ],
			$settings['sender_country'],
			$settings['sender_county'],
			$settings['sender_locality'],
			$settings['sender_organisation'],
			$settings['sender_postcode'],
			$settings['sender_street'],
			$settings['sender_town'],
			$settings['sender_name'],
			$settings['sender_phone']
		);
	}

}
