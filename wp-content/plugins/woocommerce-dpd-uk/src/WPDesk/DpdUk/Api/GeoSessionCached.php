<?php
/**
 * Class GeoSessionCached
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api;

/**
 * Cached GeoSession
 */
class GeoSessionCached extends GeoSession {

	const TRANSIENT_NAME = 'dpd_uk_gs';

	/**
	 * @return string
	 */
	public function get_geo_session_string() {
		$geo_session = get_transient( self::TRANSIENT_NAME );
		return $geo_session ? $geo_session : '';
	}

	/**
	 * @param string $geo_session .
	 */
	public function set_geo_session_string( $geo_session ) {
		set_transient( self::TRANSIENT_NAME, $geo_session, HOUR_IN_SECONDS );
	}

	/**
	 * @return bool
	 */
	public function clear_cache() {
		return delete_transient( self::TRANSIENT_NAME );
	}

}
