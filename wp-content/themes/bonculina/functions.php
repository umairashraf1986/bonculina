<?php
/**
 * Theme functions and definitions
 *
 * @package BonCulina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'BONCULINA_ELEMENTOR_VERSION', '1.0.0' );

// Including extra theme files
include_once 'inc/filterable-products.php';

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}
define( 'BONCULINA_THEME_URI', get_template_directory_uri() );
if ( ! function_exists( 'bonculina_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function bonculina_setup() {
		$hook_result = apply_filters_deprecated( 'elementor_bonculina_theme_load_textdomain', [ true ], '2.0', 'bonculina_load_textdomain' );
		if ( apply_filters( 'bonculina_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'bonculina', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_bonculina_theme_register_menus', [ true ], '2.0', 'bonculina_register_menus' );
		if ( apply_filters( 'bonculina_register_menus', $hook_result ) ) {
			register_nav_menus( array( 'menu-1' => __( 'Primary', 'bonculina' ) ) );
            register_nav_menus( array( 'menu-2' => __( 'Secondary', 'bonculina' ) ) );
		}

		$hook_result = apply_filters_deprecated( 'elementor_bonculina_theme_add_theme_support', [ true ], '2.0', 'bonculina_add_theme_support' );
		if ( apply_filters( 'bonculina_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
			add_theme_support(
				'custom-logo',
				array(
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				)
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'editor-style.css' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_bonculina_theme_add_woocommerce_support', [ true ], '2.0', 'bonculina_add_woocommerce_support' );
			if ( apply_filters( 'bonculina_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'bonculina_setup' );

if ( ! function_exists( 'add_header_after_body_open' ) ) {
    function add_header_after_body_open() { ?>
        <nav class="off-canvas-menu" id="menu-off-canvas-menu" role="navigation">
            <?php wp_nav_menu( array( 'theme_location' => 'menu-2' ) ); ?>
		</nav>
		<a href="#" class="toggle-nav btn btn-lg btn-success"><i class="fa fa-bars"></i></a>
    <?php }
}
add_action('wp_body_open', 'add_header_after_body_open');

if ( ! function_exists( 'bonculina_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function bonculina_scripts_styles() {		
        wp_enqueue_style( 'growl', BONCULINA_THEME_URI . '/assets/libs/growl/jquery.growl.css' );
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_bonculina_theme_enqueue_style', [ true ], '2.0', 'bonculina_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'bonculina_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'bonculina',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				BONCULINA_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'bonculina_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'bonculina-theme-style',
				get_template_directory_uri() . '/theme-style' . '.css',
				[],
				BONCULINA_ELEMENTOR_VERSION
			);
		}

        //wp_enqueue_script('quicksand-js', get_template_directory_uri() . '/assets/js/jquery.quicksand.js', array('jquery'), BONCULINA_ELEMENTOR_VERSION, true);
        wp_enqueue_script('isotope-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js', array('jquery'), BONCULINA_ELEMENTOR_VERSION, true);

		/* Growl */
        wp_enqueue_script( 'growl', BONCULINA_THEME_URI . '/assets/libs/growl/jquery.growl.js', array( 'jquery' ), null, true );

		wp_enqueue_script('bonculina-custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), BONCULINA_ELEMENTOR_VERSION, true);
		wp_localize_script( 'bonculina-custom-js', 'global_obj', array(
             'ajax_url' => admin_url( 'admin-ajax.php' ),
           )); //Define global variable for ajax url



        /* Main JS */
        if ( class_exists( 'WooCommerce' ) ) {
            $notice_cart_url = wc_get_cart_url();
        } else {
            $notice_cart_url = '/cart';
        }
		wp_enqueue_script('bonculina-custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), BONCULINA_ELEMENTOR_VERSION, true);
        wp_localize_script( 'bonculina-custom-js', 'jsVars', array(
            'ajaxUrl'                 => esc_js( admin_url( 'admin-ajax.php' ) ),
            //'popupEnable'             => esc_js( Insight::setting( 'popup_enable' ) ),
            //'popupReOpen'             => esc_js( Insight::setting( 'popup_reopen' ) ),
            'noticeCookieEnable'      => 1,
            'noticeCartUrl'           => esc_js( $notice_cart_url ),
            'noticeCartText'          => esc_js( esc_html__( 'View cart', 'bonculina' ) ),
            'noticeAddedCartText'     => esc_js( esc_html__( 'Added to cart!', 'bonculina' ) ),
           // 'noticeAddedWishlistText' => esc_js( esc_html__( 'Added to wishlist!', 'bonculina' ) ),
           'noticeCookie'            => esc_js( esc_html__( 'We use cookies to ensure that we give you the best experience on our website. If you continue to use this site, we will assume that you are happy with it. <a class="cookie_notice_ok"> OK, GOT IT </a>', 'insight-a' ) ),
            'noticeCookieOk'          => esc_js( esc_html__( 'Thank you! Hope you have the best experience on our website.', 'bonculina' ) ),
        ) );
	}
}
add_action( 'wp_enqueue_scripts', 'bonculina_scripts_styles' );

if ( ! function_exists( 'bonculina_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function bonculina_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_bonculina_theme_register_elementor_locations', [ true ], '2.0', 'bonculina_register_elementor_locations' );
		if ( apply_filters( 'bonculina_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'bonculina_register_elementor_locations' );

if ( ! function_exists( 'bonculina_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function bonculina_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'bonculina_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'bonculina_content_width', 0 );

if ( ! function_exists( 'bonculina_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function bonculina_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'bonculina_page_title', 'bonculina_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'bonculina_body_open' ) ) {
	function bonculina_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
}
//show details under add to cart
add_action( 'woocommerce_after_add_to_cart_button', 'html_after_add_to_cart' );
function html_after_add_to_cart(){
    echo '<br/>';
    echo '<br/>';
    echo do_shortcode(' [sc name="usps"]');
}

/**
 * @snippet       Rename "Search" Button @ WooCommerce Checkout ALT when checking out without payment gateway
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.8
 */

add_filter( 'woocommerce_order_button_html', 'bbloomer_rename_place_order_button_alt', 9999 );

function bbloomer_rename_place_order_button_alt() {
	global $woocommerce;
	$payment_complete_button = 'Proceed to payment';	
	$total_payment = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_total() ) );
	if ($total_payment == 0 ) {	
		$payment_complete_button = 'Complete Order';	
	}
    return '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value=" ' . $payment_complete_button . ' " data-value="' . $payment_complete_button . '">' . $payment_complete_button . '</button>';
}

add_filter(
    'woocommerce_checkout_fields',

    'woocommerce_checkout_fields'
);

/**
 * Add door code to checkout.
 *
 * @param array $fields
 * @return array
 */
function woocommerce_checkout_fields($fields)
{
	$fields['billing']['door_code'] = array(
		'label' => __(
			'Port Code',
			'unifaun_online_door_code'
		),
		'placeholder' => _x(
			'Port Code',
			'placeholder',
			'unifaun_online_door_code'
		),
		'required' => false,
		'class' => array('form-row-wide'),
		'clear' => true
	);
	return $fields;
}

add_filter( 'woocommerce_account_menu_items', function($items) {
    $items['dashboard'] = __('Account Panel', 'woocommerce'); // Changing label for orders
    return $items;
}, 99, 1 );


// Disable shipping calculation on cart page
function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 );

// get shipment label after order creation and send emails
function action_woocommerce_order_details_after_order_table( $order ) {

	try{
    	//Order ID
		$order_id = $order->get_id();

		$order_status = $order->get_status();

		global $wpdb;

		$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->dbname}.{$wpdb->prefix}posts WHERE post_type='shipment' AND post_status='fs-confirmed' AND post_parent=$order_id");

		$shipment_id = get_post_meta($post_id, '_dpd_uk_shipment_id', true);

		// Get Authorized
		$geo_session = '';
		$ch = curl_init();
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization: Basic Q0xVQ0tJTjpGUkVTSEZPT0RTMjQxMTIw'
		);
		curl_setopt($ch, CURLOPT_URL, 'https://api.dpdlocal.co.uk/user/?action=login');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response = json_decode($response);
		$err = curl_error($ch);
		curl_close($ch);
		if ($err) {
			error_log("cURL Error #:" . $err);
		} else {
			if($response->error === null) {
				$geo_session = $response->data->geoSession;

				// Get shipment label
				$ch = curl_init();
				$headers = array(
					'Accept: text/vnd.eltron-epl',
					'Content-Type: application/json',
					'GeoClient: account/3007834',
					"GeoSession: $geo_session"
				);
				curl_setopt($ch, CURLOPT_URL, "https://api.dpdlocal.co.uk/shipping/shipment/$shipment_id/label/");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				$err = curl_error($ch);
				curl_close($ch);
				if ($err) {
					error_log("cURL Error #:" . $err);
				} else {
					$upload_dir = wp_upload_dir();
					$base_upload_path = $upload_dir['basedir'];
					if (!is_dir($base_upload_path.'/dpd-shipment-label')) {
						mkdir($base_upload_path.'/dpd-shipment-label', 0777, true);
					}
					file_put_contents($base_upload_path.'/dpd-shipment-label/dpd_'.$order_id.'.epl', $response);

					$pay360_settings = get_option( 'woocommerce_pay360_settings' );

					$mailer = WC()->mailer();
					$mails = $mailer->get_emails();
					if ( ! empty( $mails ) ) {
						foreach ( $mails as $mail ) {

							$email_order_meta = get_post_meta($order_id, 'email_'.$mail->id, true);

							if( $order_status === 'processing' ) {
								if( $mail->id == 'customer_processing_order' && !$email_order_meta ) {
									update_post_meta($order_id, 'email_'.$mail->id, 'sent');
									$mail->trigger( $order_id );
								} else if( $mail->id == 'wcj_custom_1' && !$email_order_meta /*&& $pay360_settings['testmode'] === 'live'*/ ) {
									update_post_meta($order_id, 'email_'.$mail->id, 'sent');
									$mail->trigger( $order_id );
								}
							}
						}
					}
				}
			}
		}

	}catch(Exception $e){}

	if( $order_status === 'processing' ) {
		$order_expected_delivery_date = get_post_meta($order_id, 'order_expected_delivery_date_'.$order_id, true);

		if(!$order_expected_delivery_date) {
			$delivery_date = getExpectedDeliveryDate($order);
			update_post_meta($order_id, 'order_expected_delivery_date_'.$order_id, $delivery_date);
			echo "<header><h2>Expected Delivery Date</h2></header><p style='font-size: 20px;'>".$delivery_date."<p>";
		} else {
			echo "<header><h2>Expected Delivery Date</h2></header><p style='font-size: 20px;'>".$order_expected_delivery_date."<p>";
		}
	}

}

// add the action
add_action( 'woocommerce_order_details_after_order_table', 'action_woocommerce_order_details_after_order_table', 10, 1 );

function getExpectedDeliveryDate( $order ) {
	$order_data = $order->get_data();

	if( isset($order_data['billing']['postcode']) ) {
		$postcode = $order_data['billing']['postcode'];
	}

	if( isset($order_data['shipping']['postcode']) ) {
		$postcode = $order_data['shipping']['postcode'];
	}

	$timestamp = current_time('timestamp');

	$dateTime = new \DateTime(date('Y-m-d H:i:s', $timestamp));

	$delivery_date_obj = new \Unifaun_Online_Delivery_Date();

	$delivery_date = $delivery_date_obj->getDeliveryDate($dateTime, $postcode)->format('Y-m-d');

	return $delivery_date;
}

// shortcode to display expected delivery date in warehouse email
function delivery_date_shortcode( $atts, $content = null ) {

    $order = new \WC_Order($content);
	$delivery_date = getExpectedDeliveryDate($order);

	return "<header><h2 style='margin: 40px 0 18px;'>Expected Delivery Date</h2></header><p style='font-size: 20px;'>".$delivery_date."<p>";
	
}
add_shortcode( 'delivery', 'delivery_date_shortcode' );

// Stop warehouse email from going out if shipment label file does not exists
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'completed_email_recipient_customization', 10, 2 );
add_filter( 'woocommerce_email_recipient_wcj_custom_1', 'completed_email_recipient_customization', 10, 2 );
function completed_email_recipient_customization( $recipient, $order ) {

	if( $order !==  null ) {
		//Order ID
		$order_id = $order->get_id();

		$upload_dir = wp_upload_dir();
		$base_upload_path = $upload_dir['basedir'];

		// Check if EPL file exists or not
		$file_path = $base_upload_path . "/dpd-shipment-label/dpd_".$order_id.".epl";
		if(!file_exists($file_path)) {
			$recipient = '';
		}
	}

    return $recipient;
}

// Add DPD Shipping label to Warehouse email
add_filter( 'woocommerce_email_attachments', 'attach_shipment_label_to_warehouse_email', 100, 4 );
 
function attach_shipment_label_to_warehouse_email( $attachments, $email_id, $order, $email ) {
	$isShippingEnabled = get_option('dpdShippingOption');
    $email_ids = array( 'wcj_custom_1' );
    if ( in_array ( $email_id, $email_ids ) && $isShippingEnabled === 'yes' ) {

    	try{

    		//Order ID
    		$order_id = $order->get_id();

    		$upload_dir = wp_upload_dir();
    		$base_upload_path = $upload_dir['basedir'];
        	
			// Attach EPL file
    		$file_path = $base_upload_path . "/dpd-shipment-label/dpd_".$order_id.".epl";
    		if(file_exists($file_path)) {
    			$attachments[] = $file_path;
    		} else {
    			error_log("File does not exists");
    		}

    	} catch(Exception $e){}
    }
    return $attachments;
}

// change the address placeholder

add_filter('woocommerce_default_address_fields', 'wc_override_address_fields');

function wc_override_address_fields( $fields ) {
	$fields['address_1']['placeholder'] = 'Street name and number';
	return $fields;
}

// change the html text before the payment through PAY360

add_filter('wc_pay360_before_payment_form_html', 'before_payment_form_html_change', 10, 2);

function before_payment_form_html_change() {

	?>
	<script type="text/javascript">
		jQuery( function($){

			//Targeting the woocommerce class for spinner before the payment iFrame loads
			var a = '.woocommerce';

			//Timeout for starting the spinner
			setTimeout(function() {
            // Starting spinners
            $(a).block({
            	message: null,
            	overlayCSS: {
            		background: "#fff",
            		opacity: .6
            	}
            });

            console.log('start');

            // Stop spinners after 3 seconds
            setTimeout(function() {
                // Stop spinners
                $(a).unblock();

                console.log('stop');
            }, 5000);
        }, 300);
		});
	</script>
	<?php
	
	return "Please enter card details and click the Pay Now button to pay and complete the order. <br>
	<div class = 'accepted_payment_card_options' ><img src='". get_stylesheet_directory_uri() ."/assets/images/accepted-payments.png' alt='' /></div>";
}

// Show billing and shipping addresses on order thank you page
add_action( 'woocommerce_thankyou', 'adding_customers_details_to_thankyou', 10, 1 );
function adding_customers_details_to_thankyou( $order_id ) {
    // Only for non logged in users
    if ( ! $order_id || is_user_logged_in() ) return;

    $order = wc_get_order($order_id); // Get an instance of the WC_Order object

    wc_get_template( 'order/order-details-customer.php', array('order' => $order ));
}

//change the text "Enter your address to view shipping options." on checkout page

add_filter('woocommerce_shipping_may_be_available_html', 'override_text_on_checkout_page' , 10, 2);

function override_text_on_checkout_page () {
	return "Enter your address to view shipping type & cost.";
}

add_filter( 'woocommerce_package_rates', 'hide_all_shipping_when_free_is_available' , 10, 2 );

/**
 * Hide ALL Shipping option when free shipping is available
 *
 * @param array $available_methods
 */
function hide_all_shipping_when_free_is_available( $rates, $package ) {

	// Remove all shipping methods when postcode is non-deliverable
	$postCodes = array_map('trim', explode(',', get_option('nondeliverablepostalcodes')));
	if (in_array(trim($package['destination']['postcode']), $postCodes) || substr($package['destination']['postcode'], 0, 2) == 'GY' || substr($package['destination']['postcode'], 0, 2) == 'JE') {
		unset($rates);
	}

	return $rates;
}


// Get product name by ID
add_action( 'wp_ajax_getProductNameById', 'getProductNameById' );
add_action( 'wp_ajax_nopriv_getProductNameById', 'getProductNameById' );

function getProductNameById() {

	if ( isset($_POST['productId']) ) {
		$product_id = $_POST['productId'];

		$product = wc_get_product( $product_id );

		$productname= $product->get_title();

		echo $productname;
		die();
	}
}

// Change Add to cart button on shop page
add_filter( 'woocommerce_loop_add_to_cart_link', 'replace_loop_add_to_cart_button', 30, 2 );
function replace_loop_add_to_cart_button( $button, $product  ) {

	$product_id = $product->get_id();
	$product_name = $product->get_title();

    if( is_shop() || is_product() ){
        $button_text = __( "Add to Cart", "woocommerce" );
        $button = '<a class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id = "' . $product_id . '" data-product_name = "' . $product_name . '" data-product_sku="'.$product->get_sku().'" href="?add-to-cart=' . $product_id . '">' . $button_text . '</a>';
    }

    return $button;
}

// Validate UK phone number and non deliverable postal codes on checkout page
add_action( 'woocommerce_after_checkout_validation', 'validateShippingPostcode',  1, 1  );
function validateShippingPostcode( $data ){

	$data['billing_phone'] = preg_replace('/\s+/', '', $data['billing_phone']);

	if(strlen($data['billing_phone']) > 15) {
		wc_add_notice( __( 'Enter phone number having maximum 15 digits.', 'woocommerce' ), 'error' );
	}

	if(isset($data['shipping_address_1'])) {
		$address = $data['shipping_address_1'].$data['shipping_address_2'];
		$postcode = $data['shipping_postcode'];
	} else {
		$address = $data['billing_address_1'].$data['billing_address_1'];
		$postcode = $data['billing_postcode'];
	}

	if(strlen($address) > 35) {
		wc_add_notice( __( 'Street address must not exceed 35 characters.', 'woocommerce' ), 'error' );
	}

	$postCodes = array_map('trim', explode(',', get_option('nondeliverablepostalcodes')));
	if (in_array(trim($postcode), $postCodes) || substr($postcode, 0, 2) === 'GY' || substr($postcode, 0, 2) === 'JE') {
		if ( $data['shipping_postcode'] == '' || $data['billing_postcode'] == '' ) {
		} else {
			wc_add_notice( __( 'Sorry, we do not deliver to your address. Double check your address or contact us if you need help', 'woocommerce' ), 'error' );
		}
	}

}

// Change Shipment column title for orders in admin panel
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns) {

	if(isset($columns['flexible_shipping'])) {
		$columns['flexible_shipping'] = 'Shipment label/Shipment tracking';
	}

	if(isset($columns['shipment_tracking'])) {
		unset($columns['shipment_tracking']);
	}

    return $columns;
}

add_filter('woocommerce_return_to_shop_text', 'prefix_store_button');
/**
 * Change 'Return to Shop' text on button
 */
function prefix_store_button() {
        $store_button = "Go back to store"; // Change text as required

        return $store_button;
}

// Change terms and conditions checkbox text on checkout page
function ca_terms_and_conditions_checkbox_text( $option ){
	$translation = "I have read and accept the terms of purchase";
	$option = __($translation, 'woocommerce');
	return $option;
}

add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', 'ca_terms_and_conditions_checkbox_text' );

// Remove trailing zero decimals from product prices
add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

// Add delivery instructions row in order details table
add_filter( 'woocommerce_get_order_item_totals', 'add_delivery_instructions', 10, 2 );

function add_delivery_instructions( $total_rows, $order_obj ) {

	global $wpdb;

	$order_id = $order_obj->get_id();

	$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->dbname}.{$wpdb->prefix}posts WHERE post_type='shipment' AND post_status='fs-confirmed' AND post_parent=$order_id");

	$delivery_instructions = get_post_meta($post_id, '_dpd_uk_delivery_instructions', true);

	if(!empty($delivery_instructions)) {
		$total_rows['delivery_instructions'] = array(
			'label' => __( 'Delivery instructions:', 'woocommerce' ),
			'value'   => $delivery_instructions
		);
	}

	return $total_rows;
}

// Fix order items count in Orders page under My Account
add_action( 'woocommerce_my_account_my_orders_column_order-total', 'additional_my_account_orders_column_content', 10, 1 );
function additional_my_account_orders_column_content( $order ) {

    $details = array();

    $total_quantity = 0; // Initializing

	// Loop through order items 
    foreach ( $order->get_items() as $item ) {
    	if( empty($item->get_meta('_woosb_ids')) ) {
    		$total_quantity += $item->get_quantity();
    	}
    }

    $label = 'item';

    if( $total_quantity > 1 ) {
    	$label = 'items';
    }

    echo wc_price($order->get_total()).'&nbsp;for&nbsp;'. $total_quantity . '&nbsp;' . $label;
}

// Remove "Invoice" button for failed orders on Orders page under My Account
function sv_add_my_account_order_actions( $actions, $order ) {

	if( $order->get_status() === "failed" && array_key_exists("invoice", $actions) ) {
		unset($actions['invoice']);
	}

    return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'sv_add_my_account_order_actions', 10, 2 );

// Add expected delivery date in processing order email
add_action( 'woocommerce_email_after_order_table', 'woocommerce_email_after_order_table', 20, 4 );

function woocommerce_email_after_order_table($order, $sent_to_admin, $plain_text, $email) {

	$order_data = $order->get_data();

	$order_status = $order->get_status();

	if( $order_status === 'processing' ) {

		if( isset($order_data['billing']['postcode']) ) {
			$postcode = $order_data['billing']['postcode'];
		}

		if( isset($order_data['shipping']['postcode']) ) {
			$postcode = $order_data['shipping']['postcode'];
		}

		$timestamp = current_time('timestamp');

		$dateTime = new \DateTime(date('Y-m-d H:i:s', $timestamp));

		$delivery_date_obj = new \Unifaun_Online_Delivery_Date();

		$delivery_date = $delivery_date_obj->getDeliveryDate($dateTime, $postcode)->format('Y-m-d');

		echo "<header><h2>Expected Delivery Date</h2></header><p style='font-size: 20px;'>".$delivery_date."<p>";

	}
}

// Hide SKU from product page
function sv_remove_product_page_skus( $enabled ) {
    if ( ! is_admin() && is_product() ) {
        return false;
    }

    return $enabled;
}
add_filter( 'wc_product_sku_enabled', 'sv_remove_product_page_skus' );

// Hide category from product page
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Remove additional information tab from product page
add_filter( 'woocommerce_product_tabs', 'remove_additional_information_product_tabs', 9999 );
  
function remove_additional_information_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] ); 
    return $tabs;
}

// Change already registered message on my account page
add_filter( 'woocommerce_registration_error_email_exists', function( $html ) {
	$html = 'An account is already registered with your email address. Please log in.';
	return $html;
});

// alter min quantity error on checkout page and hide on home, shop, product category and product detail pages
function my_woocommerce_add_error( $error ) {
    if( '<span class="berocket_minmax" style="display:none;"></span>Quantity of products in cart must be <strong>6</strong> or more' == $error && is_checkout() ) {
        $error = '<span class="berocket_minmax" style="display:none;"></span>Quantity of products in this order must be <strong>6</strong> or more to complete the order';
    } else if( '<span class="berocket_minmax" style="display:none;"></span>Quantity of products in cart must be <strong>6</strong> or more' == $error && !(is_cart() || is_checkout()) ) {
    	$error = '';
    }
    return $error;
}
add_filter( 'woocommerce_add_error', 'my_woocommerce_add_error' );

// change the dropdown text on shop page

function woocommerce_product_sort( $woocommerce_sort_orderby ) {
	$woocommerce_sort_orderby = str_replace("Sort by popularity", "Sort by bestseller", $woocommerce_sort_orderby);
	$woocommerce_sort_orderby = str_replace("Sort by Latest", "Sort by latest", $woocommerce_sort_orderby);
	$woocommerce_sort_orderby = str_replace("Sort by price: low to high", "Sort by lowest price", $woocommerce_sort_orderby);
	$woocommerce_sort_orderby = str_replace("Sort by price: high to low", "Sort by highest price", $woocommerce_sort_orderby);
	
   return $woocommerce_sort_orderby;
}
add_filter( 'woocommerce_catalog_orderby', 'woocommerce_product_sort' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'woocommerce_product_sort' );


// Add information text to the thankyou page

add_filter('woocommerce_thankyou_order_received_text', 'woo_change_order_received_text', 20, 2 );
function woo_change_order_received_text( $thankyou_text, $order ) {

    $billing_email = $order->get_billing_email();

    return sprintf( __("Your purchase has been completed. A confirmation will be sent to %s", "woocommerce") , $billing_email);
}

// Enable/Disable DPD UK Shipping
if ( is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('woocommerce-dpd-uk/woocommerce-dpd-uk.php') && ! class_exists( 'WPDesk_WooCommerce_DPD_UK_Extension' ) ) {

	class WPDesk_WooCommerce_DPD_UK_Extension {
		public function __construct() {
			$this->addEnableDisableShippingOption();
			$instance = WPDesk_WooCommerce_DPD_UK::$instance;
			remove_action( 'woocommerce_order_status_changed', array( $instance, 'woocommerce_order_status_changed' ), 10, 3 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );
		}

		public function addEnableDisableShippingOption() {
			add_filter('woocommerce_get_sections_shipping', array($this, 'enableDisableShippingTab'));

			add_filter('woocommerce_get_settings_shipping', array($this, 'enableDisableShippingTabSettings'), 10, 2);
		}

		public function enableDisableShippingTab($sections)	{
			$sections['dpd_shipping_option'] = __('Enable/Disable DPD shipping', 'woocommerce');

			return $sections;
		}

		public function enableDisableShippingTabSettings($settings, $currentSection) {
			if ($currentSection == 'dpd_shipping_option') {
				$postCodesSettings = [];
            	// Add Title to the Settings
				$postCodesSettings[] = [
					'name' => __('Enable/Disable DPD UK shipping', 'woocommerce'),
					'type' => 'title',
					'desc' => __(''),
					'id' => 'dpd-shipping-option'
				];
            	// Add first checkbox option
				$postCodesSettings[] = array(
					'name' => __('Enable DPD UK Shipping', 'text-domain'),
	                'desc_tip' => '',
	                'id' => 'dpdShippingOption',
	                'type' => 'checkbox',
	                'default' => 'yes',
	                'css' => 'min-width:300px;min-height:300px',
	                'desc' => ''
	            );

				$postCodesSettings[] = array('type' => 'sectionend', 'id' => 'dpd-shipping-option');
				return $postCodesSettings;

	            /**
	             * If not, return the standard settings
	             **/
	        } else {
	        	return $settings;
	        }

	    }

		public function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
			$dpd_uk = $all_shipping_methods['dpd_uk'];
			$settings = $dpd_uk->settings;
			if ( isset( $settings['auto_create'] ) && $settings['auto_create'] == 'auto' ) {
				if ( isset( $settings['order_status'] ) && 'wc-' . $new_status == $settings['order_status'] ) {
					$order = wc_get_order( $order_id );
					$shipments = fs_get_order_shipments( $order_id, 'dpd_uk' );
					foreach ( $shipments as $shipment ) {
						try {
							$isShippingEnabled = get_option('dpdShippingOption');
							if( $isShippingEnabled === 'yes' ) {
								$shipment->api_create();
							}
						}
						catch ( Exception $e ) {}
					}
				}
			}
		}
	}
	new \WPDesk_WooCommerce_DPD_UK_Extension();
}

add_filter('gettext', 'change_lost_password' );
function change_lost_password($translated) {
	if ($translated == "Lost your password?") {
		$translated = "Forgot your Password?";
	}
	if(is_order_received_page() && $translated == "Shipment") {
		$translated = "Shipment tracking";
	}
	if(is_order_received_page() && $translated == "Track shipment: ") {
		$translated = "Please click on tracking number in order to track your shipment: ";
	}
	return $translated;
}

add_filter( 'woocommerce_package_rates', 'disable_shipping_method_based_on_postcode', 10, 2 );
function disable_shipping_method_based_on_postcode( $rates, $package ) {

	$postcode = $package['destination']['postcode'];

	$ch = curl_init();
	$headers = array(
		'Accept: application/json',
		'Content-Type: application/json',
	);
	curl_setopt($ch, CURLOPT_URL, 'https://api.postcodes.io/postcodes/'.$postcode);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$json_response = curl_exec($ch);
	$json_response = json_decode($json_response);
	$err = curl_error($ch);
	curl_close($ch);
	if ($err) {
		error_log("cURL Error #:" . $err);
	}

	if ($json_response->status == 200) {
	} else {
		foreach ( $rates as $rate_id => $rate ) {
            unset( $rates[$rate_id] );
        }
	}
	return $rates;
}