<?php
/**
 * Class CollectionDetailsData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Collection details data.
 */
class CollectionDetails implements \JsonSerializable {

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var ContactDetails
	 */
	private $contact_details;

	/**
	 * CollectionDetailsData constructor.
	 *
	 * @param Address        $address .
	 * @param ContactDetails $contact_details .
	 */
	public function __construct( Address $address, ContactDetails $contact_details ) {
		$this->address         = $address;
		$this->contact_details = $contact_details;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'address'        => $this->address,
			'contactDetails' => $this->contact_details,
		);
	}

}
