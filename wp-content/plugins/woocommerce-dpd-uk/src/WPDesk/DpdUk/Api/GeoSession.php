<?php
/**
 * Class GeoSession
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api;

/**
 * API GeoSession.
 */
class GeoSession {

	/**
	 * @var string|null
	 */
	private $geo_session_string;

	/**
	 * @return string
	 */
	public function get_geo_session_string() {
		return $this->geo_session_string;
	}

	/**
	 * @param string $geo_session_string .
	 */
	public function set_geo_session_string( $geo_session_string ) {
		$this->geo_session_string = $geo_session_string;
	}

}
