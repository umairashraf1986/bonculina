<?php
/**
 * Plugin Name: WooCommerce Create Customer on Order
 * Plugin URI: https://codecanyon.net/item/create-customer-on-order-for-woocommerce/6395319?ref=cxThemes&utm_source=create%20customer%20on%20order&utm_campaign=commercial%20plugin%20upsell&utm_medium=plugins%20page%20view%20details
 * Description: Save time and simplify your life by having the ability to create a new Customer directly on the WooCommerce Order screen
 * Author: cxThemes
 * Author URI: https://codecanyon.net/item/create-customer-on-order-for-woocommerce/6395319?ref=cxThemes&utm_source=create%20customer%20on%20order&utm_campaign=commercial%20plugin%20upsell&utm_medium=plugins%20page%20view%20details
 * Version: 1.36
 *
 * WC requires at least: 2.5
 * WC tested up to: 3.5.5
 *
 * Text Domain: create-customer-order
 * Domain Path: /languages/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    cxThemes
 * @category  WooCommerce, WordPress
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define Constants
 */
define( 'WC_CREATE_CUSTOMER_ON_ORDER_VERSION', '1.36' );
define( 'WC_CREATE_CUSTOMER_ON_ORDER_REQUIRED_WOOCOMMERCE_VERSION', 2.5 );
define( 'WC_CREATE_CUSTOMER_ON_ORDER_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WC_CREATE_CUSTOMER_ON_ORDER_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'WC_CREATE_CUSTOMER_ON_ORDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // woocommerce-email-control/ec-email-control.php

/**
 * Update Check
 */
require 'includes/updates/cxthemes-plugin-update-checker.php';
$wc_create_customer_order_update = new CX_Create_Customer_On_Order_Plugin_Update_Checker( __FILE__,	'woocommerce-create-customer-on-order' );

/**
 * Check if WooCommerce is active, and is required WooCommerce version.
 */
if ( ! WC_Create_Customer_On_Order::is_woocommerce_active() || version_compare( get_option( 'woocommerce_version' ), WC_CREATE_CUSTOMER_ON_ORDER_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ){
	add_action( 'admin_notices', array( 'WC_Create_Customer_On_Order', 'woocommerce_inactive_notice' ) );
	return;
}

/**
 * Includes
 */
include_once( 'includes/settings.php' );

/**
 * Main Class.
 */
class WC_Create_Customer_On_Order {

	private $id = 'woocommerce_create_customer_order';

	private static $instance;

	/**
	* Get Instance creates a singleton class that's cached to stop duplicate instances
	*/
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	* Construct empty on purpose
	*/
	private function __construct() {}

	/**
	* Init behaves like, and replaces, construct
	*/
	public function init(){

		// Check if WooCommerce is active, and is required WooCommerce version.
		if ( ! class_exists( 'WooCommerce' ) || version_compare( get_option( 'woocommerce_db_version' ), WC_CREATE_CUSTOMER_ON_ORDER_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_inactive_notice' ) );
			return;
		}
		
		// Localization
		add_action( 'init',    array( $this, 'load_translation' ) );
		
		// Enqueue Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// Render `Create Customer` Form.
		// add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'create_customer_on_order_form' ) ); // WC Order Page
		// add_action( 'woocommerce_bookings_after_create_booking_page', array( $this, 'create_customer_on_order_form' ) ); // WC Booking Plugin Page
		add_action( 'admin_footer', array( $this, 'create_customer_on_order_form' ) ); // All Pages - New Method.
		add_action( 'wp_footer', array( $this, 'create_customer_on_order_form' ) ); // All Pages - New Method.
		
		// Render `Disabled Notice` on Order Pages (DISABLED - NOT USED).
		// add_action( 'admin_footer', array( $this, 'create_customer_on_order_disabled_notice' ) );
		
		// `Create Customer` Form Ajax.
		add_action( 'wp_ajax_woocommerce_order_create_user', array( $this, 'woocommerce_create_customer_on_order' ) );
		add_action( 'wp_ajax_nopriv_woocommerce_order_create_user', array( $this, 'woocommerce_create_customer_on_order' ) );
		
		// WC Order page - Save address's to customer
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'update_customer_on_order_page' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_address_from_order_to_customer') );
		
		// WC Lost Password Page - Change it to behave like a first-time-login page.
		add_action( 'loop_start', array( $this, 'change_lost_password_title' ) ); // Change Lost Password Title
		add_filter( 'woocommerce_reset_password_message', array( $this, 'change_lost_password_text' ) ); // Change Lost Password Text.
		add_action( 'woocommerce_customer_reset_password', array( $this, 'update_customer_password_state' ) ); // Record that the user set their password.
		add_action( 'init', array( $this, 'successfully_activated_account' ) ); // Display notice that the account activation successful.
		
		// Fix old option.
		add_filter('option_cxccoo_user_can_create_customers', array( $this, 'fix_option_user_can_create_customers' ), 10, 2 );
	}

	/**
	 * Localization
	 */
	public function load_translation() {
		
		// Domain ID - used in eg __( 'Text', 'pluginname' )
		$domain = 'create-customer-order';
		
		// get the languages locale eg 'en_US'
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		
		// Look for languages here: wp-content/languages/pluginname/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . "/{$domain}/{$domain}-{$locale}.mo" ); // Don't mention this location in the docs - but keep it for legacy.
		
		// Look for languages here: wp-content/languages/plugins/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . "/plugins/{$domain}-{$locale}.mo" );
		
		// Look for languages here: wp-content/languages/pluginname-en_US.mo
		load_textdomain( $domain, WP_LANG_DIR . "/{$domain}-{$locale}.mo" );
		
		// Look for languages here: wp-content/plugins/pluginname/languages/pluginname-en_US.mo
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . "/languages/" );
	}
	
	/**
	 *
	 */
	public function can_current_user_role_create_customers() {
		
		// Get all the wp roles.
		$roles = wp_get_current_user()->roles;
		
		// Get the setting 'cxccoo_user_can_create_customers'.
		$setting = cxccoo_get_option( 'cxccoo_user_can_create_customers' );
		
		// Make sure setting is not string (old version) or FALSE (not set with no default).
		if ( is_array( $setting ) ) {
			
			// Multiselect, multiple role selections.
			foreach ( $setting as $value ) {
				
				if ( in_array( $value, $roles ) )
					return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Helper to check if Create Customer functionality should be active for current user.
	 */
	public function is_create_customer_on_order_form_active() {
		global $screen;
		
		// Make sure $screen->id is always available, front or back end.
		$screen = ( function_exists( 'get_current_screen' ) ) ? get_current_screen() : (object) array( 'id' => '' ) ;
		
		// Debugging.
		/*if ( ! $this->can_current_user_role_create_customers() )
			s( "CAN'T CREATE CUSTOMERS!!" );
		else
			s( "CAN CREATE CUSTOMERS!!" );*/
		
		// Bail if user is not allowed to Create Customers.
		if (
				is_user_logged_in() && // Must be logged-in.
				(
					$this->can_current_user_role_create_customers() || // Current user capability check.
					'settings_page_create_customer_settings' == $screen->id // Settings page.
				)
			) {
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 *
	 */
	public function create_customer_on_order_disabled_notice() {
		
		// Check if we should display this form.
		if ( $this->is_create_customer_on_order_form_active() ) return;
		
		?>
		<script id="cxccoo-create-user-notice-template" type="text/template">
			<div class="cxccoo-create-user-notice-container">
				<?php _e( 'Create Customer Disabled', 'create-customer-order' ); ?>
			</div>
		</script>
		
		<script>
		jQuery( function($){
			$( document ).ready( function() {
				
				$customer_search_notices = $( '.post-type-shop_order #order_data input.wc-customer-search, .post-type-shop_order #order_data select.wc-customer-search, .post-type-wc_booking .form-table input.wc-customer-search, .post-type-wc_booking .form-table select.wc-customer-search, .post-type-wc_booking #booking_data input.wc-customer-search, .post-type-wc_booking #booking_data select.wc-customer-search, .post-type-shop_subscription #order_data input.wc-customer-search, .post-type-shop_subscription #order_data select.wc-customer-search');
				$( $customer_search_notices ).each( function(){
					
					// Cache elements.
					var $wc_search_input = $(this);
					var $wc_search_input_container = $wc_search_input.parent();
					var $create_customer_container = $( $('#cxccoo-create-user-notice-template').html() );
					var $create_customer_modal = $create_customer_container.find('.cxccoo-create-user-notice-container');

					// Add `Create New Customer` interface to after any of the `Search Customer` interfaces (with some specificity/restrictions so we only add them where we know it will work).
					$wc_search_input_container.append( $create_customer_container );
				});
			
			});
		});
		</script>
		<?php
	}

	/**
	 * Add create customer form to Order Page
	 */
	public function create_customer_on_order_form() {
		
		// Check if we should display this form.
		if ( ! $this->is_create_customer_on_order_form_active() ) return;
		
		?>
		<script id="cxccoo-create-user-form-template" type="text/template">
			
			<div class="cxccoo-create-user-form-container">
				
				<a class="cxccoo-button cxccoo-create-user-main-button">
					<?php _e( 'Create New Customer', 'create-customer-order' ); ?>
					<span class="cxccoo-create-user-icon">&nbsp;</span>
				</a>
				
				<div class="cxccoo-create-form cxccoo-modal-form component-modal-hard-hide">
				
					<div class="cxccoo-modal-form-title">
						<?php _e( 'Create Customer', 'shop-as-customer' ); ?>
					</div>
					
					<div class="cxccoo-modal-form-content-inner">
						
						<a class="cxccoo-settings-icon dashicons dashicons-admin-generic" title="<?php echo esc_attr( __( 'Edit Settings', 'create-customer-order' ) ); ?>" href="<?php echo esc_url( admin_url( 'options-general.php?page=create_customer_settings' ) ); ?>">
							<!-- Settings -->
						</a>
						
						<form id="cxccoo-create-customer-form" action="" method="post">
							
							<div class="cxccoo-modal-form-row">
								<label for="cxccoo_first_name">
									<?php _e( 'First Name', 'create-customer-order' ); ?>
								</label>
								<input type="text" name="cxccoo_first_name" id="cxccoo_first_name" value="" />
							</div>
							
							<div class="cxccoo-modal-form-row">
								<label for="cxccoo_last_name">
									<?php _e( 'Last Name', 'create-customer-order' ); ?>
								</label>
								<input type="text" name="cxccoo_last_name" id="cxccoo_last_name" value="" />
							</div>
							
							<div class="cxccoo-modal-form-row">
								<label for="cxccoo_email_address">
									<?php _e( 'Email Address', 'create-customer-order' ); ?>
									<span class="cxccoo-required-field">*</span>
								</label>
								<input type="text" name="cxccoo_email_address" id="cxccoo_email_address" value="" />
								
							</div>
							
							<?php if ( 'yes' == cxccoo_get_option( 'cxccoo_user_name_selection' ) ) { ?>
								<div class="cxccoo-modal-form-row">
									<label for="cxccoo_username">
										<?php _e( 'Set Username (optional)', 'create-customer-order' ); ?>
									</label>
									<input type="text" name="cxccoo_username" id="cxccoo_username" value="" />
								</div>
							<?php } ?>
							
							<?php
							if ( 'yes' == cxccoo_get_option( 'cxccoo_user_role_selection' ) ) {
								
								$roles = WC_Create_Customer_On_Order::get_all_user_roles( 'names' );
								
								// If empty then make sure at least Customer is available.
								if ( empty( $roles ) ) $roles = array( 'customer' => 'Customer' );
								
								// Get the default role selection.
								$role_default = cxccoo_get_option( 'cxccoo_user_role_default' );
								if ( ! array_key_exists( $role_default, $roles ) ) $role_default = 'customer';
								
								?>
								<div class="cxccoo-modal-form-row">
									<label for="cxccoo_user_role"><?php _e( 'Role', 'create-customer-order' ); ?></label>
									<select name="cxccoo_user_role" id="cxccoo_user_role">
										
										<?php
										foreach ( $roles as $role_key => $role_name ) {
											
											$status = 'passed';
											if ( -1 == WC_Create_Customer_On_Order::get_role_relative_hierarchy_position( $role_key ) ) {
												$status = 'role-not-set';
											}
											else if ( WC_Create_Customer_On_Order::get_role_relative_hierarchy_position( $role_key ) < WC_Create_Customer_On_Order::get_role_relative_hierarchy_position() ) {
												$status = 'less-than-current-user';
											}
											?>
											<option
												class="<?php echo "cxccoo-status-{$status}"; ?>"
												value="<?php echo $role_key; ?>"
												<?php if ( $role_default == $role_key ) { echo 'selected="selected"'; } ?>
											>
												<?php
												// Display role name.
												echo $role_name;
												
												// Display capability status.
												if ( 'role-not-set' == $status ) {
													echo ' ' . __( '(Your user role capability prevents this)', 'create-customer-order' );
												}
												else if ( 'less-than-current-user' == $status ) {
							                    	echo ' ' . __( '(Your user role capability prevents this)', 'create-customer-order' );
							                    }
												?>
											</option>
											<?php
										}
										?>
										
										<option class="<?php echo "cxccoo-user-capability-restricted"; ?>" value="">
											<?php
											// Display a notice if capabilities setting is required.
											_e( 'Capabilities are set by Admin (Settings > Create Customer on Order > User Role Hierarchy)', 'create-customer-order' );
											?>
										</option>
										
									</select>
								</div>
								<?php
							}
							else {
								
								// Get the default role selection.
								$role_default = cxccoo_get_option( 'cxccoo_user_role_default' );
								?>
								<input type="hidden" name="cxccoo_user_role" id="cxccoo_user_role" value="<?php echo esc_attr( $role_default ); ?>" />
								<?php
							}
							?>
							
							
							<?php $this->output_settings(); ?>
							
							
							<?php
							/*
							 * To auto pretick the disable registration email checkbox, add the following line to your theme functions.php
							 * add_filter( 'woocommerce_create_customer_disable_email', '__return_true' );
							 */
							$disable_email = apply_filters( 'woocommerce_create_customer_disable_email', false );
							?>
							<div class="cxccoo-modal-form-row cxccoo-modal-form-row-tall">
								<label for="cxccoo_disable_email">
									<input type="checkbox" <?php echo ( $disable_email ) ? 'checked="checked"' : ''; ?> name="cxccoo_disable_email" id="cxccoo_disable_email" value="yes" />
									<?php _e( 'Disable customer registration email', 'create-customer-order' ); ?>
								</label>
							</div>
							
							<div class="cxccoo-modal-form-row cxccoo-modal-form-row-buttons">
								
								<a class="cxccoo-button cxccoo-create-user-form-cancel">
									<?php _e( 'Cancel', 'create-customer-order' ); ?>
								</a>
								
								<a class="cxccoo-button cxccoo-button-primary cxccoo-button-align-right cxccoo-create-user-form-submit">
									<?php _e( 'Create Customer', 'create-customer-order' ); ?>
								</a>
								
							</div>
						
						</form>
					
					</div>
					
				</div>
				
			</div>
		
		</script>

		<?php
		// Old Method - is rather done in the .js now.
		/*wc_enqueue_js("
			
			// Move on WooCommerce Order page.
			// jQuery('.cxccoo-modal-form').insertAfter( jQuery('#order_data #customer_user').parents('.form-field:eq(0)') );
			
			// Move on WooCommerce Booking Plugin Order page.
			// jQuery('.wc_booking_page_create_booking .cxccoo-modal-form').insertAfter( jQuery('.wc_booking_page_create_booking .wc-customer-search.enhanced') );
			
			// jQuery('.cxccoo-create-user-form-container').insertBefore( '.wc-customer-search' );
			// jQuery('.wc-customer-search').parent().append( jQuery('.cxccoo-create-user-form-container') );
		");*/
		
	}
	
	public function get_settings() {
		
		$settings = array(
			/*array(
				'name'        => __( 'Phone Number', 'email-control' ),
				'id'          => 'phone_number',
				'placeholder' => '',
				'default'     => '',
			),*/
		);
		
		// Filter - Allows users to add their own fields to the Create Customer form.
		$settings = apply_filters( 'woocommerce_create_customer_form_fields', $settings );
		
		return $settings;
	}
	
	public function output_settings() {
		
		$settings = $this->get_settings();
		
		foreach ( $settings as $setting ) {
			?>
			<div class="cxccoo-modal-form-row">
				<label for="<?php echo $setting['id']; ?>">
					<?php echo $setting['name']; ?>
				</label>
				<input type="text" name="<?php echo $setting['id']; ?>" id="<?php echo $setting['id']; ?>" placeholder="<?php echo $setting['placeholder']; ?>" value="<?php echo $setting['default']; ?>" />
			</div>
			<?php
		}
	}

	/**
	 * Add Save to customer checkboxes above Billing and Shipping Details on Order page
	 */
	public function update_customer_on_order_page() {
		?>
		<label class="cxccoo-order-save-actions cxccoo-save-billing-address">
			<?php _e( "Save to Customer", 'create-customer-order' ); ?>
			<input type="checkbox" name="cxccoo-save-billing-address-input" id="cxccoo-save-billing-address-input" value="true" />
		</label>
		<label class="cxccoo-order-save-actions cxccoo-save-shipping-address">
			<?php _e( "Save to Customer", 'create-customer-order' ); ?>
			<input type="checkbox" name="cxccoo-save-shipping-address-input" id="cxccoo-save-shipping-address-input" value="true" />
		</label>
		<?php
	}

	/**
	 * Include admin scripts
	 */
	public function enqueue_scripts() {
		
		if ( $this->is_create_customer_on_order_form_active() ) {
			
			wp_enqueue_style(
				'woocommerce-create-customer-order',
				WC_CREATE_CUSTOMER_ON_ORDER_URI . '/assets/css/create-user-on-order.css',
				array(),
				WC_CREATE_CUSTOMER_ON_ORDER_VERSION,
				'screen'
			);
			
			wp_enqueue_script(
				'woocommerce-create-customer-order',
				WC_CREATE_CUSTOMER_ON_ORDER_URI . '/assets/js/create-user-on-order.js',
				array( 'jquery', 'jquery-blockui' ),
				WC_CREATE_CUSTOMER_ON_ORDER_VERSION
			);
			wp_localize_script( 'woocommerce-create-customer-order', 'woocommerce_create_customer_order_params', array(
				'plugin_url'                => WC()->plugin_url(),
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'create_customer_nonce'     => wp_create_nonce( 'create-customer' ),
				'home_url'                  => get_home_url(),
				'msg_email_exists'          => __( 'Email Address already exists', 'create-customer-order'),
				'msg_email_empty'           => __( 'Please enter an email address', 'create-customer-order'),
				'msg_email_invalid'         => __( 'Invalid Email Address', 'create-customer-order'),
				'msg_email_exists_username' => __( 'This email address already exists as another users Username', 'create-customer-order'),
				'msg_username_invalid'      => __( 'Invalid Username', 'create-customer-order'),
				'msg_success'               => __( 'User created and linked to this order', 'create-customer-order'),
				'msg_email_valid'           => __( 'Please enter a valid email address', 'create-customer-order'),
				'msg_successful'            => __( 'Success', 'create-customer-order'),
				'msg_error'                 => __( 'Error', 'create-customer-order'),
				'msg_role'                  => __( 'Your user role does not have the capability to create a user with this role', 'create-customer-order'),
				'allow_role_selection'      => cxccoo_get_option( 'cxccoo_user_role_selection' ),
				'select_search_inputs'      => implode(
					', ',
					apply_filters( 'cxccoo_customer_search_inputs', array(
						
						// WooCommerce.
						'.post-type-shop_order #order_data input.wc-customer-search',
						'.post-type-shop_order #order_data select.wc-customer-search',
						
						// WooCommerce Bookings.
						'.post-type-wc_booking .form-table input.wc-customer-search',
						'.post-type-wc_booking .form-table select.wc-customer-search',
						'.post-type-wc_booking #booking_data input.wc-customer-search',
						'.post-type-wc_booking #booking_data select.wc-customer-search',
						
						// WooCommerce Subscriptions.
						'.post-type-shop_subscription #order_data input.wc-customer-search',
						'.post-type-shop_subscription #order_data select.wc-customer-search',
						
						// Shop as Customer for WooCommerce.
						'.cxsac-switch-form input.wc-customer-search',
						'.cxsac-switch-form select.wc-customer-search',
					) )
				),
			) );
			
			/**
			 * Fontello.
			 */
			wp_enqueue_style(
				'cxccoo-icon-font',
				WC_CREATE_CUSTOMER_ON_ORDER_URI . '/assets/fontello/css/cxccoo-icon-font.css',
				array(),
				WC_CREATE_CUSTOMER_ON_ORDER_VERSION
			);
		}
	}

	/**
	* Create customer via ajax on Order page
	*
	* @access public
	* @return void
	*/
	public function woocommerce_create_customer_on_order() {
		global $wpdb;

		check_ajax_referer( 'create-customer', 'security' );

		$email_address = ( isset( $_POST['cxccoo_email_address'] ) ) ? sanitize_text_field( trim( $_POST['cxccoo_email_address'] ) ) : '';
		$first_name    = ( isset( $_POST['cxccoo_first_name'] ) ) ? sanitize_text_field( trim( $_POST['cxccoo_first_name'] ) ) : '';
		$last_name     = ( isset( $_POST['cxccoo_last_name'] ) ) ? sanitize_text_field( trim( $_POST['cxccoo_last_name'] ) ) : '';
		$username      = ( isset( $_POST['cxccoo_username'] ) ) ? sanitize_text_field( trim( $_POST['cxccoo_username'] ) ) : '';
		$user_role     = ( isset( $_POST['cxccoo_user_role'] ) ) ? sanitize_text_field( trim( $_POST['cxccoo_user_role'] ) ) : '';
		$disable_email = isset( $_POST['cxccoo_disable_email'] );
		
		if ( '' == $first_name && '' == $last_name ) {
			$display_name = substr( $email_address, 0, strpos( $email_address, '@' ) );
		}
		else {
			$display_name = trim( $first_name . " " . $last_name );
		}

		$error = false;
		
		// Email validation
		if ( empty( $email_address ) ) {
			echo json_encode( array( "error_message" => "email_empty" ) );
			die();
		}
		if ( ! is_email( $email_address ) ) {
			echo json_encode( array( "error_message" => "email_invalid" ) );
			die();
		}
		if ( email_exists( $email_address ) ) {
			echo json_encode( array( "error_message" => "email_exists" ) );
			die();
		}
		
		// Username validation
		
		if ( empty( $username ) ) {
			
			// If no username then use the email address.
			$username = $email_address;
			
			if ( ! validate_username( $username ) ) {
				
				// The email is not valid username e.g. test!@test.com
				// so sanitise it and grab the first part e.g. test.
				$username = sanitize_user( $username, TRUE );
				$username = substr( $username, 0, strpos( $username, '@' ) );
			}
			
			if ( username_exists( $username ) ) {
				
				// The previous username exists so try combine Firstname Lastname.
				if ( '' != $first_name || '' != $last_name ) {
					$username = trim( $first_name . ' ' . $last_name );
				}
			}
			
			if ( '' == $username || username_exists( $username ) ){
				
				// The previous username is empty or exists.
				// so grab all we have - the beginning and middle part of the email
				// e.g. testtest
				$username = sanitize_user( $email_address, TRUE );
				$username = substr( $username, 0, strrpos( $username, '.' ) );
				$username = str_replace( '@', '', $username );
			}
			
			// We have no more options so proceed with what we have.
		}
		
		// echo json_encode( array( "error_message" => "email_empty", "error_message_testyyy" => $username ) );
		// die();
		
		if ( ! validate_username( $username ) ) {
			echo json_encode( array( "error_message" => "username_invalid" ) );
			die();
		}
		if ( username_exists( $username ) ) {
			echo json_encode( array( "error_message" => "username_exists" ) );
			die();
		}
		
		// Role validation
		if ( 'yes' == cxccoo_get_option( 'cxccoo_user_role_selection' ) ) {
			
			if ( $this->get_role_relative_hierarchy_position( $user_role ) < $this->get_role_relative_hierarchy_position() ) {
				
				echo json_encode( array( 'error_message' => 'role_unable' ) );
				die();
			}
		}
		else {
			
			$user_role = cxccoo_get_option( 'cxccoo_user_role_default' );
		}
		
		// Generate password.
		$password = wp_generate_password();
		
		// Create the new user.
		$user_id = wp_create_user( $username, $password, $email_address );

		// Update the new user.
		wp_update_user( array (
			'ID'           => $user_id,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
			'role'         => ( 'super_admin' == $user_role ) ? 'administrator' : $user_role,
			'display_name' => $display_name,
			'nickname'     => $display_name,
		) ) ;
		
		// Super Admin is not a role, it must be set this way, using `grant_super_admin()`.
		/*if ( 'super_admin' == $user_role ) {
			grant_super_admin( $user_id );
		}*/

		// Set the password.
		update_user_meta( $user_id, "create_customer_on_order_password", true );

		// Set the other info - billing
		update_user_meta( $user_id, "billing_first_name", $first_name );
		update_user_meta( $user_id, "billing_last_name", $last_name );
		update_user_meta( $user_id, "billing_email", $email_address );

		// Set the other info  - shipping
		update_user_meta( $user_id, "shipping_first_name", $first_name );
		update_user_meta( $user_id, "shipping_last_name", $last_name );
		
		
		/**
		 * From WooCommerce: class-wc-shortcode-my-account.php
		 */
		
		// Re-get the user data.
		$user_data = get_user_by( 'login', $username );
		
		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		
		do_action( 'retrieve_password', $user_login );
		
		// Save the 'user_activation_key' that the user uses to set/reset their password.
		if ( version_compare( get_option( 'woocommerce_version' ), '2.6.5', '>=' ) ) {
			
			// Get password reset key (function introduced in WordPress 4.4, WC started using in 2.6.5).
			$key = get_password_reset_key( $user_data );
		}
		else {
			
			// redefining user_login ensures we return the right case in the email
			$user_login = $user_data->user_login;

			$key = wp_generate_password( 20, false );

			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . 'wp-includes/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}

			$hashed = $wp_hasher->HashPassword( $key );
			
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
		}
		
		
		/**
		 * From WooCommerce: customer-reset-password.php
		 */
		
		// Generate lost-password link.
		$lost_password_link = esc_url_raw( add_query_arg(
			array(
				'key' => $key,
				'id' => $user_id,
				'login' => $user_login,
			),
			wc_get_endpoint_url(
				'lost-password',
				'',
				wc_get_page_permalink( 'myaccount' )
			)
		) );
		
		
		
		// Send email to customer.
		if ( ! $disable_email ) {
			$this->send_register_email( $email_address, $lost_password_link, $username );
		}
		
		// Filter - Allows users to save custom fields.
		apply_filters( 'woocommerce_create_customer_form_save', $user_id );
		
		// Echo JSON return.
		echo json_encode( array(
			'user_id'  => $user_id,
			'username' => $username,
		) );
		
		die();
	}
	
	/**
	 * Get the current/submitted roles position, as a number from -1 upwards.
	 */
	public static function get_role_relative_hierarchy_position( $role_check = FALSE ) {
		
		// Default to lowest position for safety.
		$hierarchy_position = -1;
		
		// Get all user roles positions.
		$hierarchy = self::get_all_user_roles( 'hierarchy' );
		
		if ( FALSE === $role_check ) {
			
			/**
			 * If no role submitted for checking, then default to checking the current logged in users hierarchy position.
			 */
			
			// Get the current user info so we can get the roles.
			$user_info = wp_get_current_user();
			
			foreach ( $user_info->roles as $role ) {
				
				// If one of the logged in user's roles exists in our Role Setting, and is higher, then record it.
				if ( isset( $hierarchy[$role] ) && $hierarchy[$role] > $hierarchy_position ) {
					$hierarchy_position = $hierarchy[$role];
				}
			}
		}
		else {
			
			/**
			 * Else check the submitted role's hierarchy position.
			 */
			
			if ( isset( $hierarchy[$role_check] ) ) {
				
				$hierarchy_position = $hierarchy[$role_check];
			}
		}
		
		// Special check for Super Admin.
		/*if ( 'super_admin' == $role_check && current_user_can( 'manage_network' ) ) {
			$passed = TRUE;
		}*/
		
		return $hierarchy_position;
	}
	
	/**
	 * Returns an array of All the user roles we've saved, in order, with a hierarchical number as a value.
	 *
	 * @param string $format hierarchy|names.
	 * @param string $roles (optional) String of the role hierarchy so we can do custom/side checks.
	 * @return array Roles in either of the above formats.
	 */
	public static function get_all_user_roles( $format = 'hierarchy', $roles = FALSE ) {
		
		// Get wp_roles() for later use.
		$wp_roles = wp_roles();
		
		// Allow us to pass a string of the role hierarchy so we can do custom/side checks.
		if ( FALSE == $roles ) {
			
			// Get the saved settings.
			$roles = cxccoo_get_option( 'cxccoo_user_role_heirarchy' );
			
			if ( '' == trim( $roles ) ) {
				$roles = cxccoo_get_default( 'cxccoo_user_role_heirarchy' );
			}
		}
		
		// Convert to array, by each line.
		$roles = explode( "\r\n", $roles );
		
		// Explode each line in the textarea into an array, noting the hierarchy as a number.
		$index = 0;
		$new_roles = array();
		foreach ( $roles as $role_key => $role_value ) {
			$new_roles[$role_value] = $index;
			$index++;
		}
		$roles = $new_roles;
		
		// Explode the lines that have `|`, allowing the user to have roles that are on the same level.
		$index = 0;
		$new_roles = array();
		foreach ( $roles as $role_key => $role_value ) {
			$new_keys = explode( '|', $role_key );
			foreach ( $new_keys as $new_key_value ) {
				$new_key_value = trim( $new_key_value ); // Trim the value in-case the user typed spaces around the `|`
				$new_roles[$new_key_value] = $index;
			}
			$index++;
		}
		$roles = $new_roles;
		
		// Add the left-over/remaining roles and add them to the array, all with the weakest -1 position.
		$remaining_roles = array();
		foreach ( $wp_roles->role_names as $key => $role_name ) {
			if ( ! array_key_exists( $key, $new_roles ) ) {
				$remaining_roles[$key] = -1;
			}
		}
		$roles = $remaining_roles + $roles;
		
		if ( 'hierarchy' == $format ) {
			
			/**
			 * Get `role_key => Role Name` format.
			 */
			
			return $roles;
		}
		else {
			
			/**
			 * Get `role_key => role_hierarchy` format.
			 */
			
			$role_names = $wp_roles->role_names;
			
			// If multisite then make this option available.
			/*if ( is_multisite() )
				$role_names = array( 'super_admin' => 'Super Admin' ) + $role_names;*/
			
			$new_roles = array();
			foreach ( $roles as $role_key => $role_hierarchy ) {
				if ( isset( $role_names[$role_key] ) )
					$new_roles[$role_key] = $role_names[$role_key];
			}
			
			return $new_roles;
		}
	}
	
	/**
	 * Change Lost Password page title.
	 */
	function change_lost_password_title( $array ){
		global $wp_query;
		
		if ( $array === $wp_query ) {
			
			// Is main loop, so add title.
			add_filter( 'the_title', array( $this, 'change_lost_password_title_text' ) );
		}
		else {
			
			// Is not main loop, so remove title.
			remove_filter( 'the_title', array( $this, 'change_lost_password_title_text' ) );
		}
	}
	public function change_lost_password_title_text( $page_title ) {

		$is_lost_pass_page = false;

		// Check if is lost pass page
		if ( function_exists( 'wc_get_endpoint_url' ) ) { // Above WC 2.1
			if ( is_wc_endpoint_url( 'lost-password' ) ) $is_lost_pass_page = true;
		}
		else {
			if ( is_page( wc_get_page_id( 'lost_password' ) ) ) $is_lost_pass_page = true;
		}

		// Only do this is lost pass page, and that we have a login to check against
		if ( $is_lost_pass_page && ( $user = $this->get_login_user() ) ){
			
			$password_not_changed = get_user_meta( $user->ID, 'create_customer_on_order_password', true );
			
			if ( $password_not_changed ) {
				
				$page_title = __( 'Set your Password', 'create-customer-order' );
			}
		}

		return $page_title;
	}

	/**
	 * Change Lost Password page text.
	 */
	public function change_lost_password_text( $msg ) {
		
		if ( $user = $this->get_login_user() ) {
			
			$password_not_changed = get_user_meta( $user->ID, 'create_customer_on_order_password', true );
			
			if ( $password_not_changed ) {
				
				$msg = __( 'As this is your first time logging in, please set your password.', 'create-customer-order');
			}
		}

		return $msg;
	}

	/**
	 * After customer submits reset-password is redirect to my-accounts page, and record that the user has set his new password.
	 */
	public function update_customer_password_state( $user ) {
		
		$password_not_changed = get_user_meta( $user->ID, 'create_customer_on_order_password', true );

		if ( $password_not_changed ) {
			
			delete_user_meta( $user->ID, 'create_customer_on_order_password' );
			?>
			<script type="text/javascript">
				window.location = '<?php echo add_query_arg( array( 'successfully-activated-account' => 'yes' ), get_permalink( wc_get_page_id ( 'myaccount' ) ) ); ?>';
			</script>
			<?php

			die;
		}
	}
	
	/**
	 * Disiplay notice after successfully activated account.
	 */
	public function successfully_activated_account() {
		
		if ( isset( $_GET['successfully-activated-account'] ) && 'yes' == $_GET['successfully-activated-account'] ) {
			
			wc_add_notice( __( 'You have successfully activated your account. Please login with your email address and new password', 'create-customer-order' ) );
		}
	}
	
	/**
	 * After customer submits reset-password is redirect to my-accounts page and account set to standard behaviour.
	 */
	public function get_login_user() {
		
		// Set default.
		$user = FALSE;
		
		if (
				isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) &&
				0 < strpos( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ], ':' )
			) {
			
			/**
			 * From WooCommerce: class-wc-shortcode-my-account.php
			 */
			list( $rp_id, $rp_key ) = array_map( 'wc_clean', explode( ':', wp_unslash( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ), 2 ) );
			$userdata = get_userdata( absint( $rp_id ) );
			$rp_login = $userdata ? $userdata->user_login : '';
			$user     = check_password_reset_key( $rp_key, $rp_login );
			
			/*$login = FALSE;
			if ( $rp_id == (int)( $rp_id ) ){
				$check_user = get_user_by( 'id', $rp_id );
				if ( isset( $check_user->data->user_login ) ){
					$login = $check_user->data->user_login;
				}
			}
			else {
				$check_user = get_user_by( 'login', $rp_id );
				if ( isset( $check_user->data->user_login ) ){
					$login = $check_user->data->user_login;
				}
			}
			
			if ( $rp_login ){
				$user = WC_Shortcode_My_Account::check_password_reset_key( $rp_key, $login );
			}*/
		}
		elseif ( isset( $_GET['login'] ) ) {
			
			$username = esc_attr( $_GET['login'] );
			$user = get_user_by( 'login', $username );
		}
		
		return $user;
	}

	/**
	 * Send custom register email with lost password reset link
	 */
	public function send_register_email( $email_address, $link, $username ) {

		// Email Heading
		$email_heading = __("Your account has been created", 'create-customer-order');
		apply_filters( "woocommerce_create_customer_order_email_title", $email_heading );

		// Email Subject
		$email_subject = __("Your account on %s", 'create-customer-order');
		$email_subject = sprintf( $email_subject, get_bloginfo("name") );
		apply_filters("woocommerce_create_customer_order_email_subject", $email_subject);

		// Email Headers
		$headers[] = 'From: '.get_option( 'woocommerce_email_from_name' ).' <'.get_option( 'woocommerce_email_from_address' ).'>';
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		// Email Message
		$email_message = __("Hi, we've created an account for you on our site.

<strong>Username:</strong> %s
<strong>Password:</strong> Please %s to set your new password

Copy and paste this link into your browser if you are having trouble with the above password link %s

Thank-you
%s", 'create-customer-order');

		$email_message = nl2br( sprintf(
			$email_message,
			//get_bloginfo( 'name' ),
			$username,
			'<a href="' . $link . '">' . __( "click here", 'create-customer-order' ) . '</a>',
			$link,
			get_bloginfo("name")
		) );

		// Email - Start
		ob_start();

		// This is necessary so that the following actions are hooked by WooCommerce.
		$mailer = WC()->mailer();
		
		// The best way to call WC header
		do_action( 'woocommerce_email_header', $email_heading, NULL );

		echo $email_message;
		
		// The best way to call WC footer
		do_action( 'woocommerce_email_footer', $email_heading, NULL );

		// Email Message - End
		$email_message = ob_get_clean();
		
		// Allow people to filter our message.
		apply_filters( 'woocommerce_create_customer_order_email_msg', $email_message );

		// Send Email
		$status = wc_mail( $email_address, $email_subject, $email_message, $headers );

		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
	}

	/**
	 * WP Mail Filter - Set email body as HTML
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Save Billing and Shipping details to the customer when checkboxes are checked on Order page
	 */
	public function save_address_from_order_to_customer( $post_id, $post=null ) {
		$user_id = absint( $_POST['customer_user'] );

		$save_to_billing_address = ( isset( $_POST['cxccoo-save-billing-address-input'] ) ) ? $_POST['cxccoo-save-billing-address-input'] : '';
		$save_to_shipping_address = ( isset( $_POST['cxccoo-save-shipping-address-input'] ) ) ? $_POST['cxccoo-save-shipping-address-input'] : '';

		if ($save_to_billing_address == 'true') {
			update_user_meta( $user_id, 'billing_first_name', woocommerce_clean( $_POST['_billing_first_name'] ) );
			update_user_meta( $user_id, 'billing_last_name', woocommerce_clean( $_POST['_billing_last_name'] ) );
			update_user_meta( $user_id, 'billing_company', woocommerce_clean( $_POST['_billing_company'] ) );
			update_user_meta( $user_id, 'billing_address_1', woocommerce_clean( $_POST['_billing_address_1'] ) );
			update_user_meta( $user_id, 'billing_address_2', woocommerce_clean( $_POST['_billing_address_2'] ) );
			update_user_meta( $user_id, 'billing_city', woocommerce_clean( $_POST['_billing_city'] ) );
			update_user_meta( $user_id, 'billing_postcode', woocommerce_clean( $_POST['_billing_postcode'] ) );
			update_user_meta( $user_id, 'billing_country', woocommerce_clean( $_POST['_billing_country'] ) );
			update_user_meta( $user_id, 'billing_state', woocommerce_clean( $_POST['_billing_state'] ) );
			update_user_meta( $user_id, 'billing_email', woocommerce_clean( $_POST['_billing_email'] ) );
			update_user_meta( $user_id, 'billing_phone', woocommerce_clean( $_POST['_billing_phone'] ) );
		}

		if ($save_to_shipping_address == 'true') {
			update_user_meta( $user_id, 'shipping_first_name', woocommerce_clean( $_POST['_shipping_first_name'] ) );
			update_user_meta( $user_id, 'shipping_last_name', woocommerce_clean( $_POST['_shipping_last_name'] ) );
			update_user_meta( $user_id, 'shipping_company', woocommerce_clean( $_POST['_shipping_company'] ) );
			update_user_meta( $user_id, 'shipping_address_1', woocommerce_clean( $_POST['_shipping_address_1'] ) );
			update_user_meta( $user_id, 'shipping_address_2', woocommerce_clean( $_POST['_shipping_address_2'] ) );
			update_user_meta( $user_id, 'shipping_city', woocommerce_clean( $_POST['_shipping_city'] ) );
			update_user_meta( $user_id, 'shipping_postcode', woocommerce_clean( $_POST['_shipping_postcode'] ) );
			update_user_meta( $user_id, 'shipping_country', woocommerce_clean( $_POST['_shipping_country'] ) );
			update_user_meta( $user_id, 'shipping_state', woocommerce_clean( $_POST['_shipping_state'] ) );
		}
	}
	
	/**
	 * Fix old option if it was set/changed.
	 */
	public function fix_option_user_can_create_customers( $setting, $key ) {
		
		// If it's a string like 'translator' then it's an old setting and will not work.
		// Delete it and return FALSE this time round. Next time round it wil be back to unset - FALSE.
		if ( is_string( $setting ) ) {
			delete_option( $key );
			$setting = FALSE;
		}
		return $setting;
	}
	
	/**
	 * Is WooCommerce active.
	 */
	public static function is_woocommerce_active() {
		
		$active_plugins = (array) get_option( 'active_plugins', array() );
		
		if ( is_multisite() )
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}

	/**
	 * Display Notifications on specific criteria.
	 *
	 * @since	2.14
	 */
	public static function woocommerce_inactive_notice() {
		if ( current_user_can( 'activate_plugins' ) ) :
			if ( !class_exists( 'WooCommerce' ) ) :
				?>
				<div id="message" class="error">
					<p>
						<?php
						printf(
							__( '%sCreate Customer on Order for WooCommerce needs WooCommerce%s %sWooCommerce%s must be active for Create Customer on Order to work. Please install & activate WooCommerce.', 'create-customer-order' ),
							'<strong>',
							'</strong><br>',
							'<a href="http://wordpress.org/extend/plugins/woocommerce/" target="_blank" >',
							'</a>'
						);
						?>
					</p>
				</div>
				<?php
			elseif ( version_compare( get_option( 'woocommerce_db_version' ), WC_CREATE_CUSTOMER_ON_ORDER_REQUIRED_WOOCOMMERCE_VERSION, '<' ) ) :
				?>
				<div id="message" class="error">
					<!--<p style="float: right; color: #9A9A9A; font-size: 13px; font-style: italic;">For more information <a href="http://cxthemes.com/plugins/update-notice.html" target="_blank" style="color: inheret;">click here</a></p>-->
					<p>
						<?php
						printf(
							__( '%sCreate Customer on Order for WooCommerce is inactive%s This version of Create Customer on Order requires WooCommerce %s or newer. For more information about our WooCommerce version support %sclick here%s.', 'create-customer-order' ),
							'<strong>',
							'</strong><br>',
							WC_CREATE_CUSTOMER_ON_ORDER_REQUIRED_WOOCOMMERCE_VERSION,
							'<a href="https://helpcx.zendesk.com/hc/en-us/articles/202241041/" target="_blank" style="color: inheret;" >',
							'</a>'
						);
						?>
					</p>
					<div style="clear:both;"></div>
				</div>
				<?php
			endif;
		endif;
	}

}

/**
 * Instantiate plugin.
 */

if( !function_exists( 'init_wc_create_customer_on_order' ) ) {
	function init_wc_create_customer_on_order() {

		global $wc_create_customer_on_order;

		$wc_create_customer_on_order = WC_Create_Customer_On_Order::get_instance();
	}
}
add_action( 'plugins_loaded', 'init_wc_create_customer_on_order' );
