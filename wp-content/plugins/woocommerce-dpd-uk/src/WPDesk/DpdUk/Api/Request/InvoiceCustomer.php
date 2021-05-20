<?php
/**
 * Class InvoiceCustomerData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Invoice customer data.
 */
class InvoiceCustomer implements \JsonSerializable {

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var ContactDetails
	 */
	private $contact_details;

	/**
	 * @var string
	 */
	private $vat_number;

	/**
	 * InvoiceCustomerData constructor.
	 *
	 * @param Address        $address .
	 * @param ContactDetails $contact_details .
	 * @param string         $vat_number .
	 */
	public function __construct( Address $address, ContactDetails $contact_details, $vat_number ) {
		$this->address         = $address;
		$this->contact_details = $contact_details;
		$this->vat_number      = $vat_number;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'address'        => $this->address,
			'contactDetails' => $this->contact_details,
			'vatNumber'      => $this->vat_number,
		);
	}

}
