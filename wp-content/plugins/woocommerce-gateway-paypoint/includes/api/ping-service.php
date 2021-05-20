<?php

namespace WcPay360\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Ping_Service {

	protected $client;

	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * Returns the ping endpoint
	 *
	 * @return string
	 */
	public function get_endpoint() {
		return '/hosted/rest/sessions/ping';
	}

	/**
	 * Pings the API to see if we have a connection
	 *
	 * @return bool
	 * @throws \WcPay360\Api\Exceptions\Exception
	 */
	public function send_ping() {
		$url_ping = $this->client->get_base_url() . $this->get_endpoint();
		$this->client->send_request( $url_ping, array(), 'GET' );

		return true;
	}
}