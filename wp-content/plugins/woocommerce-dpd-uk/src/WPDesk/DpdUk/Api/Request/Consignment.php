<?php
/**
 * Class ConsignmentData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Consignment Data
 */
class Consignment implements \JsonSerializable {

	/**
	 * @var CollectionDetails
	 */
	private $collection_details;

	/**
	 * @var string|null
	 */
	private $consignment_number;

	/**
	 * @var string|null
	 */
	private $consignment_ref;

	/**
	 * @var float|null
	 */
	private $customs_value;

	/**
	 * @var DeliveryDetails
	 */
	private $delivery_details;

	/**
	 * @var string
	 */
	private $delivery_instructions;

	/**
	 * @var bool
	 */
	private $liability;

	/**
	 * @var float|null
	 */
	private $liability_value;

	/**
	 * @var string
	 */
	private $network_code;

	/**
	 * @var int
	 */
	private $number_of_parcels;

	/**
	 * @var Parcel[]
	 */
	private $parcel;

	/**
	 * @var string
	 */
	private $parcel_description;

	/**
	 * @var string|null
	 */
	private $shippers_destination_tax_id;

	/**
	 * @var string|null
	 */
	private $shipping_ref1;

	/**
	 * @var string|null
	 */
	private $shipping_ref2;

	/**
	 * @var string|null
	 */
	private $shipping_ref3;

	/**
	 * @var float
	 */
	private $total_weight;

	/**
	 * @var string|null
	 */
	private $vat_paid;

	/**
	 * ConsignmentData constructor.
	 *
	 * @param CollectionDetails $collection_details .
	 * @param string|null       $consignment_number .
	 * @param string|null       $consignment_ref .
	 * @param float|null        $customs_value .
	 * @param DeliveryDetails   $delivery_details .
	 * @param string            $delivery_instructions .
	 * @param bool              $liability .
	 * @param float|null        $liability_value .
	 * @param string            $network_code .
	 * @param int               $number_of_parcels .
	 * @param Parcel[]          $parcel .
	 * @param string            $parcel_description .
	 * @param string|null       $shippers_destination_tax_id .
	 * @param string|null       $shipping_ref1 .
	 * @param string|null       $shipping_ref2 .
	 * @param string|null       $shipping_ref3 .
	 * @param float             $total_weight .
	 * @param string|null       $vat_paid .
	 */
	public function __construct(
		CollectionDetails $collection_details,
		$consignment_number,
		$consignment_ref,
		$customs_value,
		DeliveryDetails $delivery_details,
		$delivery_instructions,
		$liability,
		$liability_value,
		$network_code,
		$number_of_parcels,
		array $parcel,
		$parcel_description,
		$shippers_destination_tax_id,
		$shipping_ref1,
		$shipping_ref2,
		$shipping_ref3,
		$total_weight,
		$vat_paid
	) {
		$this->collection_details          = $collection_details;
		$this->consignment_number          = $consignment_number;
		$this->consignment_ref             = $consignment_ref;
		$this->customs_value               = $customs_value;
		$this->delivery_details            = $delivery_details;
		$this->delivery_instructions       = $delivery_instructions;
		$this->liability                   = $liability;
		$this->liability_value             = $liability_value;
		$this->network_code                = $network_code;
		$this->number_of_parcels           = $number_of_parcels;
		$this->parcel                      = $parcel;
		$this->parcel_description          = $parcel_description;
		$this->shippers_destination_tax_id = $shippers_destination_tax_id;
		$this->shipping_ref1               = $shipping_ref1;
		$this->shipping_ref2               = $shipping_ref2;
		$this->shipping_ref3               = $shipping_ref3;
		$this->total_weight                = $total_weight;
		$this->vat_paid                    = $vat_paid;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$serialized = array(
			'collectionDetails'        => $this->collection_details,
			'consignmentNumber'        => $this->consignment_number,
			'consignmentRef'           => $this->consignment_ref,
			'customsValue'             => $this->customs_value,
			'deliveryDetails'          => $this->delivery_details,
			'deliveryInstructions'     => $this->delivery_instructions,
			'liability'                => $this->liability,
			'liabilityValue'           => $this->liability ? $this->liability_value : null,
			'networkCode'              => $this->network_code,
			'numberOfParcels'          => $this->number_of_parcels,
			'parcelDescription'        => $this->parcel_description,
			'shippersDestinationTaxId' => $this->shippers_destination_tax_id,
			'shippingRef1'             => $this->shipping_ref1,
			'shippingRef2'             => $this->shipping_ref2,
			'shippingRef3'             => $this->shipping_ref3,
			'totalWeight'              => $this->total_weight,
			'vatPaid'                  => $this->vat_paid,
		);

		if ( ! empty( $this->parcel ) ) {
			$serialized['parcel'] = $this->parcel;
		}

		return $serialized;
	}

}
