jQuery(document).ready( function($) {

	const { __, _x, _n, _nx } = wp.i18n;

	// Add settings tab to badges page

	$('<a href="admin.php?page=wc-settings&tab=products&section=wcpb-product-badges" class="page-title-action">' + __( 'Settings', 'wcpb-product-badges' ) + '</a><span id="wcrp-rental-products-page-title-actions-info">' + __( 'If you have product badge and/or product image display issues or would like to enable multiple badges per product see settings.', 'wcrp-product-badges' ) + '</span>').insertAfter('.wp-admin.edit-php.post-type-wcpb_product_badge .page-title-action:last-of-type');

	// Field functionality

	$('.wcpb-product-badges-select2').select2();
	$('.wcpb-product-badges-color-picker').wpColorPicker();

	// Badge > Type > Expand

	function badgeTypeExpand() {

		// Image library

		if( $('input:radio[name="wcpb_product_badges_badge_type"]:checked' ).val() !== 'image_library' ) {

			$('#wcpb-product-badges-badge-image-library-expand').css( 'display', 'none' ); // Not show/hide as when displayed requires flex for content within
			$('#wcpb-product-badges-badge-image-library-filters').hide();

		} else {

			$('#wcpb-product-badges-badge-image-library-expand').css( 'display', 'flex' ); // Not show/hide as when displayed requires flex for content within

			if( $('#wcpb-product-badges-badge-image-library-filters div').html() == '' ) {

				$('#wcpb-product-badges-badge-image-library-filters-before-append').appendTo( $('#wcpb-product-badges-badge-image-library-filters div') ); // Filter selection moved due to issues around fixing positions and overflow scroll div

			}

			$('#wcpb-product-badges-badge-image-library-filters').show();

			imageLibraryScrollToSelected();

		}

		// Image custom

		if( $('input:radio[name="wcpb_product_badges_badge_type"]:checked' ).val() !== 'image_custom' ) {

			$('#wcpb-product-badges-badge-image-custom-expand').hide();

		} else {

			$('#wcpb-product-badges-badge-image-custom-expand').show();

		}

		// Text

		if( $('input:radio[name="wcpb_product_badges_badge_type"]:checked' ).val() !== 'text' ) {

			$('#wcpb-product-badges-badge-text-expand').css( 'display', 'none' ).css( 'flex-wrap', 'unset' ); // Not show/hide as when displayed requires flex for content within

		} else {

			$('#wcpb-product-badges-badge-text-expand').css( 'display', 'flex' ).css( 'flex-wrap', 'wrap' ); // Not show/hide as when displayed requires flex for content within

		}

		// Code

		if( $('input:radio[name="wcpb_product_badges_badge_type"]:checked' ).val() !== 'code' ) {

			$('#wcpb-product-badges-badge-code-expand').hide();

		} else {

			$('#wcpb-product-badges-badge-code-expand').show();

		}

	}

	badgeTypeExpand();

	$('input:radio[name="wcpb_product_badges_badge_type"]').change(function() {

		badgeTypeExpand();

	});

	// Badge > Type > Image library > Expand > Selection

	$( 'body' ).on( 'click', '#wcpb-product-badges-badge-image-library-expand .wcpb-product-badges-badge-image-library-image', function(e) {

		e.preventDefault();

		$('#wcpb-product-badges-badge-image-library-image').val( $(this).attr('data-image') );

		$('#wcpb-product-badges-badge-image-library-expand .wcpb-product-badges-badge-image-library-image').each(function() {

			$(this).removeClass('wcpb-product-badges-badge-image-library-image-selected');

		});

		$(this).addClass('wcpb-product-badges-badge-image-library-image-selected');

	});

	// Badge > Type > Image library > Expand > Selection > Scroll to selected on page load

	function imageLibraryScrollToSelected() {

		imageLibraryScrollToSelectedParent = $('#wcpb-product-badges-badge-image-library-expand');
		imageLibraryScrollToSelectedElement = $('.wcpb-product-badges-badge-image-library-image-selected');

		if( imageLibraryScrollToSelectedParent.length > 0 && imageLibraryScrollToSelectedElement.length > 0 ) {

			imageLibraryScrollToSelectedParent.scrollTop( imageLibraryScrollToSelectedParent.scrollTop() + imageLibraryScrollToSelectedElement.position().top - imageLibraryScrollToSelectedParent.height() / 2 + imageLibraryScrollToSelectedElement.height() / 2 ); // Height and width divided by 2 ensures selected is centered in the parent

		}

	}

	// Badge > Type > Image library > Expand > Filters

	$( 'body' ).on( 'change', '#wcpb-product-badges-badge-image-library-filters-before-append select', function(e) {

		e.preventDefault();

		$( '#wcpb-product-badges-badge-image-library-no-results' ).remove();

		$('#wcpb-product-badges-badge-image-library-expand .wcpb-product-badges-badge-image-library-image').hide();

		$( "#wcpb-product-badges-badge-image-library-filters-before-append select" ).each(function( index ) {

			if( $(this).attr( 'data-filter' ) == 'type' ) {
				
				filterType = $(this).val();

			}

			if( $(this).attr( 'data-filter' ) == 'color' ) {

				filterColor = $(this).val();

			}

		});

		filterResultsFound = 0;

		$( '#wcpb-product-badges-badge-image-library-expand .wcpb-product-badges-badge-image-library-image' ).each(function() {

			if( $(this).hasClass( filterType ) && $( this ).hasClass( filterColor ) ) {

				$(this).fadeIn(1000);
				filterResultsFound = filterResultsFound + 1;

			}

		});

		if( filterResultsFound == 0 ) {

			$( '#wcpb-product-badges-badge-image-library-expand' ).append( '<div id="wcpb-product-badges-badge-image-library-no-results">' + $( '#wcpb-product-badges-badge-image-library-filters-before-append' ).attr( 'data-filters-no-results-text' ) + '</div>' );

		}

	});

	// Display > Products > Specific > Expand

	function displayProductsSpecificExpand() {

		if( $('input:radio[name="wcpb_product_badges_display_products"]:checked' ).val() == 'specific' ) {

			$('#wcpb-product-badges-display-products-specific-expand').css( 'display', 'flex' );

		} else {

			$('#wcpb-product-badges-display-products-specific-expand').css( 'display', 'none' );

		}

	}

	displayProductsSpecificExpand();

	$('input:radio[name="wcpb_product_badges_display_products"]').change(function() {

		displayProductsSpecificExpand();

	});

});