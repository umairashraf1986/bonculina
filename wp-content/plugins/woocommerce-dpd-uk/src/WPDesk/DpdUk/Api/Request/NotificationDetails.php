<?php
/**
 * Class NotificationDetailsData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Notification details data.
 */
class NotificationDetails implements \JsonSerializable {

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $mobile;

	/**
	 * NotificationDetailsData constructor.
	 *
	 * @param string $email .
	 * @param string $mobile .
	 */
	public function __construct( $email, $mobile ) {
		$this->email  = $email;
		$this->mobile = $mobile;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'email'  => $this->email,
			'mobile' => $this->mobile,
		);
	}

}
