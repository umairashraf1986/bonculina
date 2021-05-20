<?php
/**
 * Class DeliveryDetailsData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Delivery details data.
 */
class DeliveryDetails implements \JsonSerializable {

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var ContactDetails
	 */
	private $contact_details;

	/**
	 * @var NotificationDetails
	 */
	private $notification_details;

	/**
	 * CollectionDetailsData constructor.
	 *
	 * @param Address             $address .
	 * @param ContactDetails      $contact_details .
	 * @param NotificationDetails $notification_details .
	 */
	public function __construct( Address $address, ContactDetails $contact_details, NotificationDetails $notification_details ) {
		$this->address              = $address;
		$this->contact_details      = $contact_details;
		$this->notification_details = $notification_details;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'address'             => $this->address,
			'contactDetails'      => $this->contact_details,
			'notificationDetails' => $this->notification_details,
		);
	}

}
