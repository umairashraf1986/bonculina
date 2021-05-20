jQuery( document ).ready( function( $ ) {

	// Changes should be replicated where equivalent included in public.js

	// Single product magnify position

	function singleProductMagnifyPosition() {

		magnify = $( '.woocommerce-product-gallery__trigger' );

		if( magnify.length > 0 ) {

			badge = magnify.parent().find( '.wcpb-product-badges-badge' );

			if ( badge.length > 1 ) { // If more than 1 badge as multiple badges enabled hide magnify as it wouldn't be possible to position magnify automatically

				magnify.hide();

			} else {

				// Any left moves the magnify to top right (bottom not used as make magnifier display down with the gallery images)

				if( badge.hasClass( 'wcpb-product-badges-badge-top-left' ) || badge.hasClass( 'wcpb-product-badges-badge-bottom-left' ) ) {

					magnify.css( 'top', '.875em' ).css( 'left', 'auto' ).css( 'bottom', 'auto' ).css( 'right', '.875em' ); // .875em is default WooCommerce position for magnify

				} else {

					// Any right moves the magnify to top left (bottom not used as make magnifier display down with the gallery images)

					if( badge.hasClass( 'wcpb-product-badges-badge-top-right' ) || badge.hasClass( 'wcpb-product-badges-badge-bottom-right' ) ) {

						magnify.css( 'top', '.875em' ).css( 'left', '.875em' ).css( 'bottom', 'auto' ).css( 'right', 'auto' ); // .875em is default WooCommerce position for magnify		

					}

				}

			}

		}

	}

	// Trigger functions
	
	$( window ).on( 'load', function() { // On window load

		singleProductMagnifyPosition(); // Has to run on window load or wouldn't position correctly

	});

});