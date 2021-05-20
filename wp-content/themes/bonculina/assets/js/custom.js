jQuery(document).ready(function( $ ){
	
	//jQuery('.products .product .size-woocommerce_thumbnail').wrap('<div class="img-thumb-parent"></div>');
	jQuery('.products .product .wp-post-image').wrap('<div class="img-thumb-parent"></div>');
	
	
	// is-checked
	jQuery('#filters .button').on('click', function(){

		jQuery('#filters .button').removeClass('is-checked'); 
		jQuery(this).addClass('is-checked'); 
	 
	 });

	   //$('.woocommerce-mini-cart__empty-message').closest('.elementor-menu-cart__main').addClass('empty_cart_sidebar');

	$('#elementor-menu-cart__toggle_button').on('click', function() {
		if($('.elementor-menu-cart__main').find('.woocommerce-mini-cart__empty-message').length > 0) {
        	$('.elementor-menu-cart__main').addClass('empty_cart_sidebar')
        } else {
        	$('.elementor-menu-cart__main').removeClass('empty_cart_sidebar')
        }
	});

	$('.product-template-default.woocommerce.prod__content .quantity input').on('input', function() {
		$(this).closest('li').find('a.ajax_add_to_cart').attr('data-quantity', $(this).val());
	});

	// Center align packaged product on home page
	if( $('.woocommerce .products li.product.sale').length == 1 && $('body').hasClass('home') ) {
		$('.woocommerce .products').css("grid-template-columns", "none");
		$('.woocommerce .products li.product.sale').css({"width": "300px", "margin": "0 auto"});
	}

	if( $('body').hasClass('home') ) {
		$('.woocommerce .products li.product.sale').each(function() {
			var element = $(this).find('.woocommerce-LoopProduct-link').find('h2');
			if(element.length) {
				var quantityHTML = '<div class="quantity"><input type="number" class="input-text qty text" step="1" min="1" value="1" title="Qty" size="4" placeholder="" inputmode="numeric"></div>';
				$(quantityHTML).insertBefore($(element));
			}
		});
	}

	$('.woocommerce .products li.product.sale .quantity input').on('input', function() {
		$(this).closest('li').find('a.ajax_add_to_cart').attr('data-quantity', $(this).val());
	});

	$('.woocommerce .products li.product.sale .quantity, .woocommerce .products li.product.sale .quantity input').on('click', function(e) {
		e.preventDefault();
	});

	if($('#wpadminbar').length) {
		$('a.toggle-nav').addClass('withAdminBar');
	}

	// Empty menu cart on order received page
	if($('body').hasClass('woocommerce-order-received')) {
		$('.widget_shopping_cart_content').html('<div class="woocommerce-mini-cart__empty-message">You have no products in the shopping cart.</div>');
	}


	// Cart popup 
	// var getCartPopupContentHeight1 = $('.widget_shopping_cart_content').height();
	// $('.elementor-menu-cart__main').css('height', getCartPopupContentHeight1 + 100 + 'px');

	// $('#elementor-menu-cart__toggle_button').on('click', function() {
	// 	var getCartPopupContentHeight2 = $('.widget_shopping_cart_content').height();
	// $('.elementor-menu-cart__main').css('height', getCartPopupContentHeight2 + 100 + 'px');
	// });

	$('.woocommerce .products li.sale a.ajax_add_to_cart').each(function() {
		var ele = this;
		$.ajax({
			type: "POST",
			url: global_obj.ajax_url,
			data: {
				action: "getProductNameById",
				productId:$(this).data('product_id')
			},
			beforeSend: function () {
				console.log('before')
			},
			success: function (data) {
				$(ele).attr('data-product_name', data);
			},
			error: function () {
				console.log('error')
			},
		});
	});

});



var $ = jQuery.noConflict();

$(window).on('load', function() {
	// init Isotope
	var $grid = $('.stage').isotope({
		itemSelector: '.stage li',
		layoutMode: 'fitRows',
		getSortData: {
			name: '.name',
			symbol: '.symbol',
			number: '.number parseInt',
			category: '[data-category]',
			weight: function( itemElem ) {
				var weight = $( itemElem ).find('.weight').text();
				return parseFloat( weight.replace( /[\(\)]/g, '') );
			}
		}
	});

	//added product name and view cart link by clicking on add to cart notification

	if (window.location.href.indexOf("product-category") > -1) {
		$('.woocommerce .products li.product a.ajax_add_to_cart').each(function() {
			var ele = this;
			$.ajax({
				type: "POST",
				url: global_obj.ajax_url,
				data: {
					action: "getProductNameById",
					productId:$(this).data('product_id')
				},
				beforeSend: function () {
					console.log('before')
				},
				success: function (data) {
					$(ele).attr('data-product_name', data);
				},
				error: function () {
					console.log('error')
				},
			});
		});
	}
});

// bind filter button click
$('#filters').on( 'click', 'a', function() {
	var filterValue = $( this ).attr('data-filter');
	// use filterFn if matches value
	//filterValue = filterFns[ filterValue ] || filterValue;
	$grid.isotope({ filter: filterValue });
});


// Off canvas Menu
// $(function() {      
//     $('.toggle-nav').click(function() {        
//         toggleNav();
//     });  
// });

// function toggleNav() {
//     if ($('body').hasClass('show-nav')) {        
//         $('body').removeClass('show-nav');
//     } else {
        
//         $('body').addClass('show-nav');
//     }  
// }


jQuery(document).ready(function( $ ){
	jQuery('.products .product .size-woocommerce_thumbnail').wrap('<div class="img-thumb-parent"></div>');
	jQuery('.products .product .wp-post-image').wrap('<div class="img-thumb-parent"></div>');
	// is-checked
	jQuery('#filters .button').on('click', function(){

		jQuery('#filters .button').removeClass('is-checked'); 
		jQuery(this).addClass('is-checked'); 
	 
	 });

	// Cookie notice
	var tmOrganikCookieOk = insightGetCookie( 'tm_organik_cookie_ok' );
	console.log(jsVars);
	if ( (
		     jsVars.noticeCookieEnable == 1
	     ) && (
		     tmOrganikCookieOk != 'true'
	     ) ) {
		if ( jsVars.noticeCookie != '' ) {
			jQuery.growl( {
				location: 'br',
				fixed: true,
				duration: 3600000,
				title: '',
				message: jsVars.noticeCookie
			} );
			jQuery( '.cookie_notice_ok' ).on( 'click', function() {
				jQuery( this ).parent().parent().find( '.growl-close' ).trigger( 'click' );
				insightSetCookie( 'tm_organik_cookie_ok', 'true', 365 );
				jQuery.growl.notice( {location: 'br', title: '', message: jsVars.noticeCookieOk} );
			} );
		}
	}
});

function insightSetCookie( cname, cvalue, exdays ) {
	var d = new Date();
	d.setTime( d.getTime() + (
		exdays * 24 * 60 * 60 * 1000
	) );
	var expires = 'expires=' + d.toUTCString();
	document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/';
}

function insightGetCookie( cname ) {
	var name = cname + '=';
	var ca = document.cookie.split( ';' );
	for ( var i = 0; i < ca.length; i ++ ) {
		var c = ca[i];
		while ( c.charAt( 0 ) == ' ' ) {
			c = c.substring( 1 );
		}
		if ( c.indexOf( name ) == 0 ) {
			return c.substring( name.length, c.length );
		}
	}
	return '';
}

var $ = jQuery.noConflict();

// init Isotope
var $grid = $('.stage').isotope({
	itemSelector: '.stage li',
	layoutMode: 'fitRows',
	getSortData: {
		name: '.name',
		symbol: '.symbol',
		number: '.number parseInt',
		category: '[data-category]',
		weight: function( itemElem ) {
			var weight = $( itemElem ).find('.weight').text();
			return parseFloat( weight.replace( /[\(\)]/g, '') );
		}
	}
});

// bind filter button click
$('#filters').on( 'click', 'a', function() {
	var filterValue = $( this ).attr('data-filter');
	// use filterFn if matches value
	//filterValue = filterFns[ filterValue ] || filterValue;
	// if ( !$('#filters').data('isotope').filteredItems.length ) {
	// console.log($('#filters').data('isotope').filteredItems.length);
	$grid.isotope({ filter: filterValue });
});

$grid.on( 'arrangeComplete', function( event, filteredItems ) {   
	if( filteredItems.length > 0 ) {
		$('.no-products').hide();
	} else {
		$('.no-products').show();
	}
});


// Off canvas Menu
$(function() {      
    $('.toggle-nav').click(function() {        
        toggleNav();
    });  
});

function toggleNav() {
    if ($('body').hasClass('show-nav')) {        
        $('body').removeClass('show-nav');
    } else {
        
        $('body').addClass('show-nav');
    }  
}

	// Add to cart notification
jQuery( 'a.add_to_cart_button' ).on( 'click', function() {
	jQuery( 'a.add_to_cart_button' ).removeClass( 'recent-added' );
	jQuery( this ).removeClass('added');
	jQuery( this ).addClass( 'recent-added loading' );

} );
jQuery( 'body' ).on( 'added_to_cart', function() {
	var productName = jQuery( '.recent-added' ).attr( 'data-product_name' );
	if ( jsVars.noticeAddedCartText != undefined ) {
		if ( productName != undefined ) {
			jQuery.growl.notice( {
				location: 'br',
				title: '',
				message: productName + ' ' + jsVars.noticeAddedCartText.toLowerCase() + ' <a href="' + jsVars.noticeCartUrl + '">' + jsVars.noticeCartText + '</a>'
			} );
			jQuery( this ).removeClass('loading');
			jQuery( this ).addClass('added');

		} else {
			jQuery.growl.notice( {location: 'br', fixed: true, title: '', message: jsVars.noticeAddedCartText} );
		}
	}
} );

jQuery( '.elementor-widget-container' ).on( 'click', function() {
	var isFirefox = typeof InstallTrigger !== 'undefined';
	if (isFirefox == true) {
		var height = $('.widget_shopping_cart_content:visible').height();
		var set_height_of_widget = height + 25;
		$('.elementor-menu-cart__main').height(set_height_of_widget);
	}
} );

$("body").on("input", ".quantity input.qty", function() {
   if($(this).val() <= 0 ) {
	   $(this).val(1);
   } 
});