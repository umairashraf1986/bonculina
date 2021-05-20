<?php
/**
 * Class ParcelData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Parcel data
 */
class Parcel implements \JsonSerializable {

	/**
	 * @var int
	 */
	private $package_number;

	/**
	 * @var ParcelProduct[]
	 */
	private $parcel_product;

	/**
	 * ParcelData constructor.
	 *
	 * @param int             $package_number .
	 * @param ParcelProduct[] $parcel_product .
	 */
	public function __construct( $package_number, $parcel_product ) {
		$this->package_number = $package_number;
		$this->parcel_product = $parcel_product;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'packageNumber' => $this->package_number,
			'parcelProduct' => $this->parcel_product,
		);
	}

}
