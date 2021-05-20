<?php
/**
 * Class Shipment
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Shipment.
 */
class ShipmentRequest implements \JsonSerializable {

	/**
	 * @var int
	 */
	private $job_id = null;

	/**
	 * @var bool
	 */
	private $collection_on_delivery;

	/**
	 * @var string
	 */
	private $generate_customs_data;

	/**
	 * @var Invoice
	 */
	private $invoice;

	/**
	 * @var \DateTime
	 */
	private $collection_date;

	/**
	 * @var bool
	 */
	private $consolidate;

	/**
	 * @var Consignment[]
	 */
	private $consignment;

	/**
	 * ShipmentRequest constructor.
	 *
	 * @param bool               $collection_on_delivery .
	 * @param string             $generate_customs_data .
	 * @param Invoice|null       $invoice .
	 * @param \DateTimeInterface $collection_date .
	 * @param bool               $consolidate .
	 * @param Consignment[]      $consignment .
	 */
	public function __construct(
		$collection_on_delivery,
		$generate_customs_data,
		$invoice,
		\DateTimeInterface $collection_date,
		$consolidate,
		array $consignment
	) {
		$this->collection_on_delivery = $collection_on_delivery;
		$this->generate_customs_data  = $generate_customs_data;
		$this->invoice                = $invoice;
		$this->collection_date        = $collection_date;
		$this->consolidate            = $consolidate;
		$this->consignment            = $consignment;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'jobId'                => $this->job_id,
			'collectionOnDelivery' => $this->collection_on_delivery,
			'generateCustomsData'  => $this->generate_customs_data,
			'invoice'              => ( null === $this->invoice ) ? null : $this->invoice->jsonSerialize(),
			'collectionDate'       => $this->collection_date->format( 'Y-m-d\TH:i:s' ),
			'consolidate'          => $this->consolidate,
			'consignment'          => $this->consignment,
		);
	}

}
