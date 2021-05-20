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
class Cashier_Payments implements Errors_Interface {

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
		if ( isset( $this->response->reasonCode ) ) {
			return $this->response->reasonCode;
		} elseif ( isset( $this->outcome ) ) {
			return $this->outcome->reasonCode;
		}

		return '';
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
			'V301' => _x( 'No PAN provided. Card number is not provided.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V302' => _x( 'Invalid pan format: {maskedPan} / Card contains non-numeric characters / Invalid card number	Card number must be numeric only.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V303' => _x( 'Invalid pan length: {panLength} / Invalid card length ({panLength})	Card number must be between 13 and 19 characters.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V304' => _x( 'expiryDate is null or empty', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V305' => _x( 'Unknown card type, valid types are {listOfValidTypes}. Payment card type must be one of: ELECTRON, MAESTRO, MC_DEBIT,SWITCH, SOLO,  MC_CREDIT,  VISA_DEBIT,  VISA_CREDIT, AMEX,  JCB,  LASER, DISCOVER,  DINERS.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V306' => _x( 'CV2 missing or invalid / CV2 missing, and mandatory in your configuration / CV2 missing, but
mandatory in this transaction CV2 is either missing or not a valid format if present. CV2 must be numeric only.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V307' => _x( 'Only one payment method can be used at the same time . Trying to use card as well.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V308' => _x( 'Only one payment method can be used at the same time. Trying to use cardToken as well.	', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V309' => _x( 'Only one payment method can be used at the same time. Trying to use fromCustomer as well.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V310' => _x( 'Only one payment method can be used at the same time. Trying to use payCash as well.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V311' => _x( 'No pan or card lock token provided. No valid card lock token or card number specified.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V312' => _x( 'Either card lock token OR card number should be set. Both card lock token and card number are specified. Only one is required.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V313' => _x( 'Card lock not available for you. CardLock is not enabled on the account.', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
			'V314' => _x( 'Merchant has invalid credentials', 'cashier-payments-error', \WC_Pay360::TEXT_DOMAIN ),
		);

		if ( isset( $map[ $this->get_code() ] ) ) {
			return $map[ $this->get_code() ];
		}

		if ( isset( $this->response->reasonMessage ) ) {
			return $this->response->reasonMessage;
		} elseif ( isset( $this->outcome ) ) {
			return $this->outcome->reasonMessage;
		}

		return '';
	}
}