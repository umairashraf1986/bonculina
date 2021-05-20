<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package BonCulina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/footer' );
}
?>

<?php wp_footer(); ?>

<script>
	jQuery(document).ready(function(){
		jQuery(window).on('load resize', function() {
			// alert('called');
			jQuery('.product-template-default.woocommerce .related ul.products li.product').each(function(){
				//debugger;
				jQuery(this).find(jQuery('.wcpb-product-badges-badge')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));
				if (jQuery(window).width() > 768 ) {
					jQuery(this).find(jQuery('.button.product_type_simple.add_to_cart_button.ajax_add_to_cart')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));
				}
			});
		});
		
		jQuery('#productcontainer .stage li').each(function(){
			//debugger;
			jQuery(this).find(jQuery('.wcpb-product-badges-badge')).insertAfter(jQuery(this).find(jQuery("figure a > img")));
			if (jQuery(window).width() > 768) {
				jQuery(this).find(jQuery('.button.product_type_simple.add_to_cart_button.ajax_add_to_cart')).insertAfter(jQuery(this).find(jQuery("figure a > img")));
			}
		});
		
		jQuery('.woocommerce .products li.product.sale').each(function(){
			//debugger;
			jQuery(this).find(jQuery('.wcpb-product-badges-badge')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));
		});
		
		fixGrid();

		$('a#grid').on('click', function() {
			setTimeout(fixGrid, 500);
		});

		fixList();

		$('a#list').on('click', function() {
			setTimeout(fixList, 500);
		});
	});

	function fixGrid() {
		jQuery('.site-main ul.products.grid li.product').each(function(){
			// debugger;
			jQuery(this).find(jQuery('.wcpb-product-badges-badge')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));

			jQuery(this).find(jQuery('.gridlist-buttonwrap a.add_to_cart_button')).insertAfter(jQuery(this).find(jQuery('.img-thumb-parent > img')));
			
			if (jQuery(window).width() >= 768) {
				jQuery(this).find(jQuery('.button.product_type_simple.add_to_cart_button.ajax_add_to_cart')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));
				jQuery(this).find(jQuery('.button.product_type_woosb.add_to_cart_button.ajax_add_to_cart')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));
			}
			
		});
	}

	function fixList() {
		jQuery('.site-main ul.products.list li.product').each(function(){
			// debugger;
			jQuery(this).find(jQuery('.wcpb-product-badges-badge')).insertAfter(jQuery(this).find(jQuery(".img-thumb-parent > img")));

			jQuery(this).find(jQuery('.img-thumb-parent a.add_to_cart_button')).prependTo(jQuery(this).find(jQuery('.gridlist-buttonwrap')));
		});
	}

</script>
</body>
</html>
