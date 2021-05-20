<?php
/**
 * Class ParcelProductData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Parcel Product Data
 */
class ParcelProduct implements \JsonSerializable {

	/**
	 * @var string
	 */
	private $country_of_origin;

	/**
	 * @var int
	 */
	private $number_of_items;

	/**
	 * @var string
	 */
	private $product_code;

	/**
	 * @var string
	 */
	private $product_fabric_content;

	/**
	 * @var string
	 */
	private $product_harmonised_code;

	/**
	 * @var string
	 */
	private $product_items_description;

	/**
	 * @var string
	 */
	private $product_type_description;

	/**
	 * @var string
	 */
	private $product_url;

	/**
	 * @var float
	 */
	private $unit_value;

	/**
	 * @var float
	 */
	private $unit_weight;

	/**
	 * ParcelProductData constructor.
	 *
	 * @param string $country_of_origin .
	 * @param int    $number_of_items .
	 * @param string $product_code .
	 * @param string $product_fabric_content .
	 * @param string $product_harmonised_code .
	 * @param string $product_items_description .
	 * @param string $product_type_description .
	 * @param string $product_url .
	 * @param float  $unit_value .
	 * @param float  $unit_weight .
	 */
	public function __construct(
		$country_of_origin,
		$number_of_items,
		$product_code,
		$product_fabric_content,
		$product_harmonised_code,
		$product_items_description,
		$product_type_description,
		$product_url,
		$unit_value,
		$unit_weight
	) {
		$this->country_of_origin         = $country_of_origin;
		$this->number_of_items           = $number_of_items;
		$this->product_code              = $product_code;
		$this->product_fabric_content    = $product_fabric_content;
		$this->product_harmonised_code   = $product_harmonised_code;
		$this->product_items_description = $product_items_description;
		$this->product_type_description  = $product_type_description;
		$this->product_url               = $product_url;
		$this->unit_value                = $unit_value;
		$this->unit_weight               = $unit_weight;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$serialized = array(
			'countryOfOrigin'         => $this->country_of_origin,
			'numberOfItems'           => $this->number_of_items,
			'productHarmonisedCode'   => $this->product_harmonised_code,
			'productItemsDescription' => $this->product_items_description,
			'unitValue'               => $this->unit_value,
			'unitWeight'              => $this->unit_weight,
		);

		if ( ! empty( $this->product_code ) ) {
			$serialized['productCode'] = $this->product_code;
		}

		if ( ! empty( $this->product_fabric_content ) ) {
			$serialized['productFabricContent'] = $this->product_fabric_content;
		}

		if ( ! empty( $this->product_type_description ) ) {
			$serialized['productTypeDescription'] = $this->product_type_description;
		}

		if ( ! empty( $this->product_type_description ) ) {
			$serialized['productTypeDescription'] = $this->product_type_description;
		}

		if ( ! empty( $this->product_url ) ) {
			$serialized['productUrl'] = $this->product_url;
		}

		return $serialized;
	}

}
