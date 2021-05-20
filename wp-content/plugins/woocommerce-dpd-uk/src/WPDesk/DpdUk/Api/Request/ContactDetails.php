<?php
/**
 * Class ContactDetailsData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Contact details data.
 */
class ContactDetails implements \JsonSerializable {

	/**
	 * @var string
	 */
	private $contact_name;

	/**
	 * @var string
	 */
	private $telephone;

	/**
	 * ContactDetailsData constructor.
	 *
	 * @param string $contact_name .
	 * @param string $telephone .
	 */
	public function __construct( $contact_name, $telephone ) {
		$this->contact_name = $contact_name;
		$this->telephone    = $telephone;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'contactName' => $this->contact_name,
			'telephone'   => $this->telephone,
		);
	}

}
