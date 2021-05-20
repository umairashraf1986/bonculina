<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Custom text for 'woocommerce_product_add_to_cart_text' filter for all product types/cases.
 *
 * @param  $default is the default add to cart text
 *
 * @return string String for add to cart text.
 */

function bcfw_replace_shop_add_to_cart_text( $default, $product ) {

	$options = get_option( 'button-customizer-general' );

	// security, return the default filter.
	if ( empty( $options ) || empty( $options['change_on_filters']['shop'] ) ) {
		return $default;
	}

	if ( $options['enable'] == 'off' || $options['change_on_filters']['shop'] != 'shop' ) {
		return $default;
	}

	return bcfw_get_button_texts( $options, $default, $product );
}

add_filter( 'woocommerce_product_add_to_cart_text', 'bcfw_replace_shop_add_to_cart_text', 9999, 2 );

/**
 * Changes the $default add to cart buttons on single products
 *
 * @param string $default button text.
 *
 * @return string String for add to cart text.
 */

function bcfw_replace_single_add_to_cart_text( $default, $product ) {

	$options = get_option( 'button-customizer-general' );

	// security, return the default filter.
	if ( empty( $options ) || empty( $options['change_on_filters']['single'] ) ) {
		  return $default;
	}

	if ( empty( $options ) || $options['enable'] == 'off' || $options['change_on_filters']['single'] != 'single' ) {
			return $default;
	}

	return bcfw_get_button_texts( $options, $default, $product );

}

add_filter( 'woocommerce_product_single_add_to_cart_text', 'bcfw_replace_single_add_to_cart_text', 9999, 2 );


/**
 * Change the add to cart button for WC bookings on single.
 *
 * @param string $default button text.
 *
 * @return string button text.
 */
function bcfw_booking_single_check_availability_text( $default ) {

	$options = get_option( 'button-customizer-general' );

	// security, return the default filter.
	if ( empty( $options ) || empty( $options['change_on_filters']['single'] ) ) {
		return $default;
	}

	if ( empty( $options ) || $options['enable'] == 'off' || $options['change_on_filters']['single'] != 'single' ) {
		  return $default;
	}

	$default = ( ! empty( $options['booking_product'] ) ? $options['booking_product'] : $default );

	return $default;

}

// add_filter( 'woocommerce_booking_single_add_to_cart_text', 'bcfw_booking_single_check_availability_text', 999);
// add_filter( 'woocommerce_booking_single_check_availability_text', 'bcfw_booking_single_check_availability_text', 999);

/**
 * Get new button text from options per product type.
 *
 * @param array  $options options from settings.
 * @param string $default Default button text.
 * @global $product object.
 *
 * @return string button text.
 */
function bcfw_get_button_texts( $options, $default, $product ) {

	$product_type = $product->get_type();

	switch ( $product_type ) {
		// Grouped Products.
		case 'external':
			$external = ( ! empty( $options['external_product'] ) ? $options['external_product'] : $default );
			return $external;
		break;

		// Grouped Products.
		case 'grouped':
			$grouped = ( ! empty( $options['grouped_product'] ) ? $options['grouped_product'] : $default );
			return $grouped;
		break;

		// Simple Products.
		case 'simple':
			$simple = ( ! empty( $options['simple_product'] ) ? $options['simple_product'] : $default );
			return $simple;
		break;

		// Variable Products.
		case 'variable':
			$variable = ( ! empty( $options['variable_product'] ) ? $options['variable_product'] : $default );
			return $variable;
		break;

		// Bookable Products.
		case 'booking':
			$booking = ( ! empty( $options['booking_product'] ) ? $options['booking_product'] : $default );
			return $booking;
		break;

		// Bookable Accomondation products.
		case 'accommodation-booking':
			$accomondation = ( ! empty( $options['accomondation_product'] ) ? $options['accomondation_product'] : $default );
			return $accomondation;
		break;

		// Default fallback.
		default:
			$fallback = ( ! empty( $options['fallback'] ) ? $options['fallback'] : $default );
			return $fallback;
	}  // end switch

}

/*
 * Add a link to the settings page on the plugins.php page.
 *
 * @since 1.0.0
 *
 * @param  array  $links List of existing plugin action links.
 * @return array         List of modified plugin action links.
 */
function bcfw_settings_quick_link_plugin_page( $links ) {
	$links = array_merge(
		array(
			'<a href="' . esc_url( admin_url( 'admin.php?page=button-customizer' ) ) . '">' . __( 'Settings', 'button-customizer' ) . '</a>',
		),
		$links
	);
	return $links;
}
