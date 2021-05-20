<?php

namespace WcPay360\Api\Errors;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Hosted Cashier error messages
 *
 * @since  2.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Hosted_Cashier implements Errors_Interface {

	protected $response;

	/**
	 * Hosted_Cashier constructor.
	 *
	 * @param object $response
	 */
	public function __construct( $response ) {
		$this->response = $response;
	}

	/**
	 * Returns the error code
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	public function get_code() {
		return isset( $this->response->reasonCode ) ? $this->response->reasonCode : '';
	}

	/**
	 * Returns the message corresponding to the error code
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	public function get_message() {
		$map = array(
			'exception.authenticationFailure.noAuth'          => _x( 'Authentication Failed', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.authenticationFailure.noPriv'          => _x( 'Authorisation Failed', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.cache.invalidResourcePath'             => _x( 'Invalid Resource Path', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.config.noCustomerCardDomain'           => _x( 'No customer-card domain configured', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.config.noReturnUrl'                    => _x( 'No return url provided', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.error.amo'                             => _x( 'CSC Address match only', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.error.dcr'                             => _x( 'Daily card registrations', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.error.mcr'                             => _x( 'Max card register', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.error.ndm'                             => _x( 'CSC No data match', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.error.nvd'                             => _x( 'Net balance', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.initialiseHosted.noCustomer'           => _x( 'Customer not found', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.installation.lookup'                   => _x( 'Invalid installation', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.landing.invalidFlow'                   => _x( 'Invalid flow provided', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.manageCards.noRegisteredCards'         => _x( 'No cards registered', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.manageCards.noRegisteredCards.message' => _x( 'Currently you have no registered cards which are available for manage cards.', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.manageCards.noValidCards'              => _x( 'No cards active', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.payout.noValidCards'                   => _x( 'No valid cards for withdrawal', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.payout.noValidCards.message'           => _x( 'Currently you have no registered cards which are available for withdrawal.', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.permission.denied'                     => _x( 'Permission Denied', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.permission.denied.message'             => _x( 'You do not have permission to perform that action', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.resumeFailure.invalidSession'          => _x( 'Session invalid for resumption', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.resumeFailure.noSession'               => _x( 'No session found', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.skin.lookup'                           => _x( 'No skin configured', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.skin.merchant.lookup'                  => _x( 'No merchant configured', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.skin.request'                          => _x( 'Skin specified in request not valid', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.skin.unusable'                         => _x( 'Skin not usable', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.config.url.not.valid'                  => _x( 'Callback URL is not valid', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.request.contentType'                   => _x( 'Content type {0} not supported. Content type application/json required.', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
			'exception.invalid.email'                         => _x( 'Email address provided is not valid', 'hosted-cashier-error', \WC_Pay360::TEXT_DOMAIN ),
		);

		if ( isset( $map[ $this->get_code() ] ) ) {
			return $map[ $this->get_code() ];
		}

		return isset( $this->response->reasonMessage ) ? $this->response->reasonMessage : '';
	}
}