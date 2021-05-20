<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hosted integration request class. Generate and submit the Hosted form
 *
 * @since  1.2
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2012 - 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Pay360_Hosted_Request extends WC_Pay360_Abstract_Request_Legacy {
	
	/**
	 * URL the form should be submitted to
	 * @var string
	 */
	public $form_url;
	
	/**
	 * WC_Pay360_Hosted_Request constructor.
	 *
	 * @param WC_Pay360_Gateway $gateway
	 */
	public function __construct( $gateway ) {
		$this->form_url = 'https://www.secpay.com/java-bin/ValCard';
		
		parent::__construct( $gateway );
	}
	
	/**
	 * Generated the Hosted form parameters
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function get_parameters( WC_Order $order ) {
		
		// Get formatted order number
		$order_number = WC_Pay360::get_order_number( $order );
		
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Integration method: Hosted. Generating parameter...' );
		
		$amount   = number_format( $order->get_total(), 2, '.', '' );
		$currency = $this->get_currency( $order );
		
		$args = array(
			'merchant'  => $this->get_gateway()->merchant_h,
			'trans_id'  => $order_number,
			'amount'    => $amount,
			'callback'  => $this->get_return_url(),
			'currency'  => $currency,
			'order_key' => WC_Pay360_Compat::get_prop( $order, 'order_key' ),
			'order_id'  => WC_Pay360_Compat::get_order_id( $order ),
			
			'bill_name'      => WC_Pay360_Compat::get_order_billing_first_name( $order ) . ' ' . WC_Pay360_Compat::get_order_billing_last_name( $order ),
			'bill_addr_1'    => WC_Pay360_Compat::get_order_billing_address_1( $order ),
			'bill_addr_2'    => WC_Pay360_Compat::get_order_billing_address_2( $order ),
			'bill_city'      => WC_Pay360_Compat::get_order_billing_city( $order ),
			'bill_state'     => WC_Pay360_Compat::get_order_billing_state( $order ),
			'bill_post_code' => WC_Pay360_Compat::get_order_billing_postcode( $order ),
			'bill_country'   => WC_Pay360_Compat::get_order_billing_country( $order ),
			'bill_tel'       => WC_Pay360_Compat::get_order_billing_phone( $order ),
			'bill_email'     => WC_Pay360_Compat::get_order_billing_email( $order )
		);
		
		if ( '' != WC_Pay360_Compat::get_order_shipping_address_1( $order ) ) {
			$args = array_merge(
				$args,
				array(
					'ship_name'      => WC_Pay360_Compat::get_order_shipping_first_name( $order ) . ' ' . WC_Pay360_Compat::get_order_shipping_last_name( $order ),
					'ship_addr_1'    => WC_Pay360_Compat::get_order_shipping_address_1( $order ),
					'ship_addr_2'    => WC_Pay360_Compat::get_order_shipping_address_2( $order ),
					'ship_city'      => WC_Pay360_Compat::get_order_shipping_city( $order ),
					'ship_state'     => WC_Pay360_Compat::get_order_shipping_state( $order ),
					'ship_post_code' => WC_Pay360_Compat::get_order_shipping_postcode( $order ),
					'ship_country'   => WC_Pay360_Compat::get_order_shipping_country( $order ),
				)
			);
		}
		
		list( $desc, $order_desc ) = $this->get_detailed_description( $order );
		
		// Add the order details parameter
		$args['order'] = $order_desc;
		
		if ( 'yes' == $this->get_gateway()->enable_3d_secure ) {
			
			// Add the brief order description.
			$args['mpi_description'] = $desc;
			
			// Add merchant name
			if ( empty( $this->get_gateway()->mpi_merchant_name ) ) {
				$args['mpi_merchant_name'] = substr( get_bloginfo( 'name' ), 0, 25 );
			} else {
				$args['mpi_merchant_name'] = substr( $this->get_gateway()->mpi_merchant_name, 0, 25 );
			}
			
			// Add the merchant url.
			if ( empty( $this->get_gateway()->mpi_merchant_url ) ) {
				$args['mpi_merchant_url'] = home_url();
			} else {
				if ( 0 !== strpos( $this->get_gateway()->mpi_merchant_url, 'http://' ) && 0 !== strpos( $this->get_gateway()->mpi_merchant_url, 'https://' ) ) {
					$args['mpi_merchant_url'] = 'http://' . $this->get_gateway()->mpi_merchant_url;
				}
			}
			
			if ( 'test_success' == $this->get_testmode_status() || 'test_fail' == $this->get_testmode_status() ) {
				$args['test_mpi_status'] = 'true';
			}
		}
		
		if ( 'yes' == $this->get_gateway()->mail_customer ) {
			$args['mail_customer'] = 'true';
		} else {
			$args['mail_customer'] = 'false';
		}
		
		$args['backcallback'] = $order->get_cancel_order_url();
		$args['show_back']    = __( 'Cancel', WC_Pay360::TEXT_DOMAIN );
		
		if ( 'auth' == $this->get_gateway()->transaction_type_h ) {
			$args['deferred'] = 'true';
		}
		
		$image = $this->get_gateway()->pp_logo_h;
		if ( ! empty( $image ) ) {
			$args['merchant_logo'] = "<img src='$image' height='100px' width='750px' class='floatright'>";
		}
		
		if ( is_ssl() ) {
			$args['ssl_cb'] = 'true';
		}
		
		$args['cb_flds']     = 'order_key:order_id';
		$args['test_status'] = $this->gateway_status();
		
		if ( 'live' != $this->get_testmode_status() ) {
			$args['dups'] = 'false';
		}
		
		// Allow for parameters modification
		$args = apply_filters( 'pay360_hosted_get_parameters', $args, $order, $this->get_gateway() );
		
		$hash = md5( $args['trans_id'] . $amount . $this->get_gateway()->remotepassword_h );
		
		$args['digest'] = $hash;
		
		return $args;
	}
	
	/**
	 * Return detailed description of the order.
	 * Returns two types of description
	 * 1. To be used for the 3D secure
	 * 2. To be used for the order details parameter
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	protected function get_detailed_description( WC_Order $order ) {
		$desc       = '';
		$order_desc = '';
		if ( 0 < count( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( WC_Pay360_Compat::get_item_quantity( $item ) ) {
					$item_name = $this->get_order_item_name( $item );
					$desc      .= WC_Pay360_Compat::get_item_quantity( $item ) . ' x ' . $item_name . ', ';
				}
			}
			
			// Remove the last delimiters
			$desc = substr( $desc, 0, - 2 );
			
			// Limit the description to 125 characters
			$desc = substr( $desc, 0, 125 );
			
			// Now we are going to add the detailed breakdown of the order line items
			$order_desc = '';
			$this->prepare_line_items( $order, $order_desc );
			if ( '' == $order_desc ) {
				// Remove the generated line items
				$this->delete_line_items( $order_desc );
				
				// Add order total - shipping
				$this->add_line_item(
					$desc,
					number_format( $order->get_total() - round( $order->get_total_shipping() + $order->get_shipping_tax(), 2 ), 2, '.', '' ),
					$order_desc, 1
				);
				
				// Add shipping
				$this->add_line_item(
					'SHIPPING',
					number_format( $order->get_total_shipping() + $order->get_shipping_tax(), 2, '.', '' ),
					$order_desc
				);
			}
			
			// Remove the last delimiters
			$order_desc = substr( $order_desc, 0, - 1 );
		}
		
		return array( $desc, $order_desc );
	}
	
	/**
	 * Return the correct form URL,
	 * depending on the integration
	 *
	 * @since 2.0
	 *
	 * @return mixed
	 */
	public function get_form_url() {
		return $this->form_url;
	}
	
	/**
	 * Return the form input fields
	 *
	 * @param array $parameters Key->Value parameters to be converted to html input fields
	 *
	 * @return string
	 */
	public function get_form_input_fields( $parameters ) {
		
		//Debug log
		WC_Pay360_Debug::add_debug_log( 'Submitted parameters: ' . print_r( $parameters, true ) );
		
		$options    = '';
		$form_array = array();
		
		foreach ( $parameters as $key => $value ) {
			if ( 'merchant_logo' == $key ) {
				$options .= $key . '=' . $value . ',';
				continue;
			}
			if ( 'cb_flds' == $key ) {
				$options .= $key . '=' . $value . ',';
				continue;
			}
			
			$form_array[] = '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
		}
		
		if ( ! empty( $options ) ) {
			$options      = substr( $options, 0, - 1 );
			$form_array[] = '<input type="hidden" name="options" value="' . $options . '" />';
		}
		
		return implode( '', $form_array );
	}
	
	/**
	 * Get the Hosted Gateway request status.
	 * 1. Test mode (success or fail)
	 * 2. Live mode
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	private function gateway_status() {
		if ( 'test_success' == $this->get_testmode_status() ) {
			$status = 'true';
		} elseif ( 'test_fail' == $this->get_testmode_status() ) {
			$status = 'false';
		} else {
			$status = 'live';
		}
		
		return $status;
	}
	
	/**
	 * Add all order items in a separate details line item
	 *
	 * @since 2.0
	 *
	 * @param WC_Order $order
	 * @param string   $order_desc Referenced items description
	 *
	 * @return string
	 */
	public function prepare_line_items( $order, &$order_desc ) {
		$order_desc       = '';
		$calculated_total = 0;
		
		// Products
		$order_items = $order->get_items( array( 'line_item', 'fee' ) );
		if ( 0 == count( $order_items ) ) {
			return $order_desc;
		}
		
		foreach ( $order_items as $item ) {
			// TODO: Check all methods here
			$prod_name  = $this->get_order_item_name( $item );
			$total_item = 'yes' == WC_Pay360_Compat::get_prop( $order, 'prices_include_tax' ) ? $order->get_item_subtotal( $item, true ) : $order->get_item_subtotal( $item );
			$this->add_line_item( $prod_name, number_format( $total_item, 2, '.', '' ), $order_desc, $item['qty'] );
			$calculated_total += $total_item * $item['qty'];
		}
		
		if ( 0 < WC_Pay360_Compat::get_total_shipping( $order ) ) {
			$total_shipping = number_format( WC_Pay360_Compat::get_total_shipping( $order ), 2, '.', '' );
			$this->add_line_item( 'SHIPPING', $total_shipping, $order_desc );
			$calculated_total += $total_shipping;
		}
		
		if ( 0 < $order->get_total_tax() ) {
			$total_tax = number_format( $order->get_total_tax(), 2, '.', '' );
			$this->add_line_item( 'TAX', $total_tax, $order_desc );
			$calculated_total += $total_tax;
		}
		
		if ( 0 < $order->get_total_discount() ) {
			$total_discount = number_format( $order->get_total_discount(), 2, '.', '' );
			$this->add_line_item( 'DISCOUNT', $total_discount, $order_desc );
			$calculated_total = $calculated_total - $total_discount;
		}
		
		// Check for mismatched totals
		$calculated_total = number_format( $calculated_total, 2, '.', '' );
		$total            = number_format( $order->get_total(), 2, '.', '' );
		
		if ( $calculated_total != $total ) {
			// Remove the generated string
			$order_desc = '';
		}
	}
	
	/**
	 * Add line item details to the order description string
	 *
	 * @since 2.0
	 *
	 * @param string $name
	 * @param float  $amount
	 * @param string $order_desc Referenced order description string
	 * @param int    $quantity
	 */
	public function add_line_item( $name, $amount, &$order_desc, $quantity = 0 ) {
		$order_desc .= 'prod=' . $name . ',';
		$order_desc .= 'item_amount=' . $amount;
		if ( 0 < $quantity ) {
			$order_desc .= 'x' . $quantity;
		}
		$order_desc .= ';';
	}
	
	/**
	 * Remove the generated string from order description
	 *
	 * @since 2.0
	 *
	 * @param $order_desc
	 */
	public function delete_line_items( &$order_desc ) {
		$order_desc = '';
	}
}