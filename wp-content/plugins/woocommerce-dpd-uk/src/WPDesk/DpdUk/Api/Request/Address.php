<?php
/**
 * Class AddressData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Address data.
 */
class Address implements \JsonSerializable {

	/**
	 * @var string
	 */
	private $country_code;

	/**
	 * @var string
	 */
	private $county;

	/**
	 * @var string
	 */
	private $locality;

	/**
	 * @var string
	 */
	private $organisation;

	/**
	 * @var string
	 */
	private $postcode;

	/**
	 * @var string
	 */
	private $street;

	/**
	 * @var string
	 */
	private $town;

	/**
	 * AddressData constructor.
	 *
	 * @param string $country_code .
	 * @param string $county .
	 * @param string $locality .
	 * @param string $organisation .
	 * @param string $postcode .
	 * @param string $street .
	 * @param string $town .
	 */
	public function __construct( $country_code, $county, $locality, $organisation, $postcode, $street, $town ) {
		$this->country_code = $country_code;
		$this->county       = $county;
		$this->locality     = $locality;
		$this->organisation = $organisation;
		$this->postcode     = $postcode;
		$this->street       = $street;
		$this->town         = $town;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'organisation' => $this->organisation,
			'countryCode'  => $this->country_code,
			'postcode'     => $this->postcode,
			'street'       => $this->street,
			'town'         => $this->town,
			'county'       => ! empty( $this->county ) ? $this->county : ' ',
			'locality'     => ! empty( $this->locality ) ? $this->locality : ' ',
		);
	}

}
