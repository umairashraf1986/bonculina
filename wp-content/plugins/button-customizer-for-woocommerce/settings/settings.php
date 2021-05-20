<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin screen and settings for Button_Customizer_Admin_Settings
 *
 * @author Morgan Hvidt
 * @version 1.0.0
 */

if ( ! class_exists( 'Button_Customizer_Admin_Settings' ) ) :
	class Button_Customizer_Admin_Settings {

		private $settings_api;


		function __construct() {
			$this->settings_api = new Button_Customizer_Settings_API();

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 70 );

		}

		function admin_init() {

			// set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );
			$this->settings_api->set_sidebar( $this->get_settings_sidebar() );

			// initialize settings
			$this->settings_api->admin_init();
		}

		function admin_menu() {
				 add_submenu_page( 'woocommerce', 'Button Customizer', 'Button Customizer', 'manage_options', 'button-customizer', array( $this, 'plugin_page' ) );
		}

		function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'button-customizer-general',
					'title' => __( 'Basic Settings', 'button-customizer' ),

				),
			);
			return $sections;
		}

		function get_settings_sidebar() {
			$sidebar = array(
				array(
					'id'      => 'button_customizer_sidebar_1',
					'title'   => __( 'Reviews keeps us going', 'button-customizer' ),
					'content' => __( '<p>We are always excited about reviews.<br /> Leave us a <a href="https://https://profiles.wordpress.org/morganhvidt#content-plugins" target="_blank">nice review</a> to keep us motivated!</p><a class="button-primary" href="https://wordpress.org/support/plugin/button-customizer-for-woocommerce/reviews/" target="_blank">Review this plugin</a>', 'button-customizer' ),
				),
				array(
					'id'      => 'button_customizer_sidebar_2',
					'title'   => __( 'Want to change more?', 'button-customizer' ),
					'content' => __(
						'Our Button Customizer has plenty of room to grow. Do you have an idea or feature request? <a href="https://puri.io/support/" target="_blank"> Reach out to us.</a>
					<p>We also have exciting WooCommerce plugins in our shop <a href="https://puri.io/" target="_blank">puri.io</a></p>
					<a class="button-primary" href="https://puri.io/" target="_blank">Get WooCommerce Plugins</a>',
						'button-customizer'
					),
				),
			);
			return $sidebar;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields() {
			$settings_fields = array(

				'button-customizer-general' => array(
					array(
						'name'  => 'quick_settings',
						'label' => __( 'Quick Settings', 'button-customizer' ),
						'desc'  => __( 'Quickly turn on or disable the whole plugin.' ),
						'type'  => 'subheading',
						'class' => 'subheading',
					),
					array(
						'name'  => 'enable',
						'label' => __( 'Enable Customizer', 'button-customizer' ),
						'desc'  => __( '', 'button-customizer' ),
						'type'  => 'checkbox',
					),

					array(
						'name'    => 'change_on_filters',
						'label'   => __( 'Change Buttons on', 'button-customizer' ),
						'desc'    => '',
						'type'    => 'multicheck',
						'default' => array(
							'single' => 'Single Product Pages',
							'shop'   => 'four',
						),
						'options' => array(
							'single' => 'Single Products',
							'shop'   => 'Shop & Archive Pages',
						),
					),

					array(
						'name'  => 'change_woocommerce_buttons',
						'label' => __( 'Change WooCommerce buttons', 'button-customizer' ),
						'desc'  => __( 'Change default text for product buttons. These settings change the "add to cart" buttons in the shop loop and on single products. Leave blank for default.' ),
						'type'  => 'subheading',
						'class' => 'subheading',
					),

					array(
						'name'              => 'simple_product',
						'label'             => __( 'Simple Product', 'button-customizer' ),
						'placeholder'       => __( 'Add to cart', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'              => 'variable_product',
						'label'             => __( 'Variable Product', 'button-customizer' ),
						'placeholder'       => __( 'Select Options', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'              => 'external_product',
						'label'             => __( 'External Product', 'button-customizer' ),
						'placeholder'       => __( 'Buy Product', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'              => 'grouped_product',
						'label'             => __( 'Grouped Product', 'button-customizer' ),
						'placeholder'       => __( 'View Products', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'              => 'booking_product',
						'label'             => __( 'Bookable Product', 'button-customizer' ),
						'desc'              => __( 'Bookable Products via WooCommerce Bookings', 'button-customizer' ),
						'placeholder'       => __( 'Book now', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'              => 'accomondation_product',
						'label'             => __( 'Accomondation Product', 'button-customizer' ),
						'placeholder'       => __( 'Book Nights', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'              => 'fallback',
						'label'             => __( 'Fallback', 'button-customizer' ),
						'desc'              => __( 'Fallback text will replace the default add to cart button text, which is usually "read more" only on products that aren\'t specified. E.g custom product types. ', 'button-customizer' ),
						'placeholder'       => __( 'Read more', 'button-customizer' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					),

				),

			);

			return $settings_fields;
		}

		function plugin_page() {
			echo '<div class="wrap">';
			echo '<h2>Custom Buttons for WooCommerce</h2>';
			$this->settings_api->show_navigation();
			$this->settings_api->show_sidebar();
			$this->settings_api->show_forms();
			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}

	}
endif;
