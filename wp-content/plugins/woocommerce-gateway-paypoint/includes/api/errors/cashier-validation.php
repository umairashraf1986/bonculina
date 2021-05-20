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
class Cashier_Validation implements Errors_Interface {

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
			'V111' => _x( 'UserId and Security Key must be specified. Invalid API credentials.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V112' => _x( 'Installation must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V113' => _x( 'Invalid installation / Installation ID must be numeric. Installation id must be numerics only.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V114' => _x( 'Customer must be specified / Customer details must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V115' => _x( 'merchantRef or id must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V116' => _x( 'merchantRef and id must not be specified together.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V117' => _x( 'Customer not found.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V118' => _x( 'Customer name cannot be null.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V119' => _x( 'At least one name for the customer must be set.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V123' => _x( 'paymentClass should be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V124' => _x( 'Invalid optional post code.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V125' => _x( 'Invalid URL.	Callback url does not have a valid URL format.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V100' => _x( 'Invalid Amount.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V126' => _x( 'Currency must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V127' => _x( 'Unrecognised currency {currencyType}.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V128' => _x( 'CommerceType must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V129' => _x( 'Amount must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V130' => _x( 'Amount {amountValue} cannot be negative.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V131' => _x( 'Amount {amountValue} exceeds the number of fractional digits {digitsNumber} permitted for currency {currencyType}.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V132' => _x( 'Amount {amountValue} exceeds the maximum number of non-fractional digits {digitsNumber}.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V133' => _x( 'Cannot {requestedTxnType} a transaction in a different currency ({requiredCurrency}) to the original {originalCurrency}).', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V134' => _x( 'Cannot perform a {requestedTxnType} against a {originalTxnType} transaction.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V135' => _x( 'Cannot perform a {requestedTxnType} against an incomplete transaction.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V136' => _x( 'Cannot perform a {requestedTxnType}  against a failed transaction.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V137' => _x( 'Cannot perform a {requestedTxnType} against an uncaptured transaction.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V138' => _x( 'Cannot perform a {requestedTxnType} against an already-captured transaction.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V139' => _x( 'Cannot perform a {requestedTxnType} against an already-void transaction.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V142' => _x( 'Invalid nickname length ({nicknameLength}). Max number of permitted characters is 20.	Card nickname exceeds the max number of permitted characters(20).', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V143' => _x( 'Card nickname is invalid. Only latin alphanumeric characters, spaces and . – ‘ are permitted.	Card nickname contains characters that are not permitted.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V144' => _x( 'Expiry date should be MMYY. Expiry date must match: MMYY format.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V145' => _x( 'Start date should be MMYY. Start date must match: MMYY format.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V146' => _x( 'startDate and clearStartDate can not be specified in the same request. StartDate and ClearStartDate are both specified in the same time.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V147' => _x( 'Start date after expiry. StartDate and Expiry are both specified at the same time and start date is after the expiry date.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V148' => _x( 'Issue number must be between 0 and 9. Issue number must be between 0 and 9', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V149' => _x( 'issueNumber and clearIssueNumber can not be specified in the same request. IssueNumber and ClearIssueNumber are both specified in the same time.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V150' => _x( 'One payment method should be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V151' => _x( 'token should be set / cardToken must be specified.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V152' => _x( 'Address field exceeds max length. Line 1 / Line 2 / Line 3 / Line 4 / City / Country / Postal Code / Region field length exceeds 255 characters .', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V154' => _x( 'Unrecognised address country . Address country is not a valid country in the system .', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V155' => _x( 'Unrecognised address country code . Address country code is not a valid country code in the system .', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V156' => _x( 'Cannot register a payment method for a guest transaction . A payment method cannot be registered when a customer is not also being registered.', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
			'V153' => _x( 'Invalid date of birth “{dateOfBirth}”, it must be in the format “yyyyMMdd”. The date of birth must match: yyyyMMdd format', 'cashier-validation-error', \WC_Pay360::TEXT_DOMAIN ),
		);

		if ( isset( $map[ $this->get_code() ] ) ) {
			return $map[ $this->get_code() ];
		}

		return isset( $this->response->reasonMessage ) ? $this->response->reasonMessage : '';
	}
}