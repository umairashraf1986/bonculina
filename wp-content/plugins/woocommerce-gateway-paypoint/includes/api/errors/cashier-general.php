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
class Cashier_General implements Errors_Interface {

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
			'V100' => _x( 'The API request was not valid.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V101' => _x( 'Unauthorised access. The client address was not configured in the client firewall.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V102' => _x( '–The server-to-server address was not valid (Internal).', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V103' => _x( 'Access Denied. The connection API credentials do not have permission to use the specified installation.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V104' => _x( 'Unsupported transaction type {transactionType}. The specified operation is not supported on this channel.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V105' => _x( 'Missing related transaction / Related transaction was not found. Related transaction was not provided / not found / processed by other merchant.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V106' => _x( 'Suspended transaction has expired. The suspended transaction has expired, so it could not be resumed.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V107' => _x( 'Wrong transaction status for resume – transaction status = {transactionStatus}. This transaction cannot be resumed.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V108' => _x( 'PaRes is not valid	This 3DS transaction cannot be resumed, the provided PaRes is not valid.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V109' => _x( 'CV2 specified as a standalone field is not consistent with the value from the card lock token. CV2 is received as a standalone field and in card lock token and the values are not consistent.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'V306' => _x( 'CV2 is requested on domain / CV2 is requested on Merchant. CV2 is configured as a required field and has not been provided.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),

			'A102' => _x( 'No account capable of processing this transaction. No suitable external account.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A103' => _x( 'The related transaction for this operation was not valid for this operation.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A104' => _x( 'Card brand not found. Details of the card could not be determined from the card data source.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A107' => _x( 'The customer does not have an active default card registered. Customer does not have a default card.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A108' => _x( 'Specified customer not found. Card has no customer / Customer belongs to different Org Unit / Invalid
merchant reference.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A109' => _x( 'Specified card not found	The merchant card token is not found into the system. Used when updating/removing customers.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A110' => _x( 'Transaction cancelled because of POST_AUTH Callback failure. Transaction was abandoned because the post-auth callback failed with a technical error.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A111' => _x( 'Transaction cancelled because of pre AUTH Callback failure. Transaction was abandoned because the pre-auth callback failed with a technical error.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A112' => _x( 'invalid configuration expression	Invalid configuration in regards to expressions (Invalid expression supplied to the global 3D secure).', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A113' => _x( 'Transaction rejected after Three D Secure enrolment. The ThreeDSecure enrolment failed but the process is enforcing 3d secure enrollment.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A114' => _x( 'The transaction was rejected because 3DS authentication failed. The ThreeDSecure progressed but the transaction was not authenticated.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A115' => _x( 'Card operation no longer valid after resume. The CCS has identified that a card operation is no longer valid after the transaction is resumed.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A116' => _x( 'Card fraud checks failed. The transaction has failed due to the failure of the fraud checks.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A117' => _x( 'Card brand is not supported on this account. The card type could not be determined. It is not supported by the route.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A118' => _x( 'The customer name is missing and is a required field. The customer name is not provided.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A119' => _x( 'The card has already been registered to a customer. The card to be registered already exists.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A120' => _x( 'Invalid gaming territories country conditions. The transaction has failed Gaming territory management rules.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A121' => _x( 'A valid MCC could not be determined.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A122' => _x( 'Max limit exceeded for active cards. The max limit of active cards is reached and no new card can be registered.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A123' => _x( 'Card is in deleted status. The card is in a deleted status.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A124' => _x( 'The custom field value is invalid for the configured type.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A125' => _x( 'Cannot process two concurrent secondary transactions that refer the same primary transaction	A secondary transaction is already processing for this transaction.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A126' => _x( 'Duplicate transaction submitted', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A127' => _x( 'Invalid or unknown card lock token. Specified card lock token is not recognized by the Tokenization Service.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A128' => _x( 'Tokenization Service temporarily not available. The Tokenization Service is temporarily unable to process tokenized payments.
                                                                                                                                      ', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A129' => _x( 'Merchant not configured for PayPal. The merchant is not configured for PayPal transaction processing.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A130' => _x( 'Installation not configured for PayPal. The installation is not configured for PayPal transaction processing.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A131' => _x( 'PayPal merchant authentication failure. Invalid merchant credentials.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A134' => _x( 'PayPal merchant authorisation failure. You do not have the appropriate permissions to perform this transaction.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A135' => _x( 'PayPal configuration issue', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A136' => _x( 'Rejected by fraud rules service. The transaction request has been rejected due to a Fraud rule.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A137' => _x( 'Failed to communicate with fraud rules service', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A138' => _x( 'The card submitted for referral payout has not been used previously for an authorised payment. This functionality is currently not enabled on your account.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'A400' => _x( 'No such transaction. Provided transaction Id does not exist.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'C101' => _x( 'Cancelled by preauth callback. The Preauth callback returned a CANCEL response.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'C102' => _x( 'Cancelled by postauth callback. The post auth callback returned a CANCEL response.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'C103' => _x( 'Cancelled due to postauth callback error', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'C104' => _x( 'Cancelled due to preauth callback error', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'C105' => _x( 'Cancelled by request	Merchant cancelled PayPal request', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X201' => _x( 'No processing routes are configured for this type of transaction. Internal error within the platform. Failed to communicate with destination transaction processor. Exception from destination processor.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X202' => _x( 'Post Auth transaction void failed. The transaction mandated a VOID but this failed at the destination platform. The authoriser response contains an unsuccessful void response or does not contain a void response at all.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X203' => _x( 'The destination platform is not available.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X205' => _x( 'Downstream PayCash authoriser error. A general error communicating with PayCash authoriser.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X208' => _x( 'Could not perform Three D Secure enrolment check. A general error occurred with the 3DS enrolment check.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X210' => _x( 'PayPal service currently unavailable. Unable to communicate with the PayPal service.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X211' => _x( 'PayPal returned error. Communication with PayPal returned an error.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X212' => _x( 'PayPal timeout. The Express Checkout session has expired. Token value is no longer valid.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'X214' => _x( 'PayPal user has not authorised/confirmed/completed. Payment has not been authorised by the user.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'E500' => _x( 'Unknown error returned from card check', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'D100' => _x( 'Declined by upstream processor', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'U100' => _x( 'Suspended pending Three D Secure process	Transaction was suspended by 3DS enrolment processing. See redirect
message in response, can only be resumed with a PARes message.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'U101' => _x( 'Suspended by preauth callback. Transaction was suspended by Preauth Callback. Transaction can be resumed to continue processing.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'U102' => _x( 'Suspended for replay by preauth callback. Transaction was suspended by Preauth Callback. Transaction can be resumed to continue processing.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'U103' => _x( 'Suspended by PayPal. The transaction was suspended by PayPal processing', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'U104' => _x( 'Suspended for PayPal redirect retry. The transaction was suspended for PayPal session retry.', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'R100' => _x( 'Report Failed', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'T100' => _x( 'Transaction expired by timeout', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'B100' => _x( 'Batch failed, see individual items for reasons', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'B101' => _x( 'Partial batch failure, see individual items for reasons', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
			'L100' => _x( 'Cannot obtain lock within configured timeout', 'cashier-general-error', \WC_Pay360::TEXT_DOMAIN ),
		);

		if ( isset( $map[ $this->get_code() ] ) ) {
			return $map[ $this->get_code() ];
		}

		return isset( $this->response->reasonMessage ) ? $this->response->reasonMessage : '';
	}
}