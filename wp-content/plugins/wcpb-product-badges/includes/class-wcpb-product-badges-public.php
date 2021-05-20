<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPB_Product_Badges_Public' ) ) {

	class WCPB_Product_Badges_Public {

		public function __construct() {

			add_filter( 'woocommerce_sale_flash', '__return_false' );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueues' ) );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'add_badge_loop_product' ) );
			add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'add_badge_loop_product_block' ), 0, 3 );
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'add_badge_single_product' ), 0 );
			add_filter( 'safe_style_css', array( $this, 'add_safe_style_css' ) );

		}

		public function enqueues() {

			$compatibility_mode = get_option( 'wcpb_product_badges_compatibility_mode' );

			if ( 'yes' == $compatibility_mode ) {

				wp_enqueue_script(
					'wcpb-product-badges-public',
					plugins_url( 'wcpb-product-badges/assets/js/public-compatibility-mode.js' ),
					array(
						'jquery',
					),
					WCPB_PRODUCT_BADGES_VERSION
				);

			} else {

				wp_enqueue_script(
					'wcpb-product-badges-public',
					plugins_url( 'wcpb-product-badges/assets/js/public.js' ),
					array(
						'jquery',
					),
					WCPB_PRODUCT_BADGES_VERSION
				);

			}

			wp_enqueue_style(
				'wcpb-product-badges-public',
				plugins_url( 'wcpb-product-badges/assets/css/public.css' ),
				array(),
				WCPB_PRODUCT_BADGES_VERSION,
				'all'
			);

		}

		public function add_badge_loop_product() {

			global $product;

			if ( !empty( $product ) ) {

				$this->badge( $product, true );

			}

		}

		public function add_badge_loop_product_block( $html, $data, $product ) {

			if ( !empty( $product ) ) {

				$html = str_replace( '<div class="wc-block-grid__product-image">', '<div class="wc-block-grid__product-image">' . $this->badge( $product, false ), $html );

			}

			return $html;

		}

		public function add_badge_single_product( $html ) {

			if ( !is_admin() ) {

				global $product;

				if ( !empty( $product ) ) {

					$html = str_replace( '</div>', $this->badge( $product, false ) . '</div>', $html ); // This makes the badge sit within the div of the image, however this means the badge is displayed on each image when in a gallery. We have looked into having the badges concatanated after the </div> instead however when this is done it would require some JS to position the badge within a parent container correctly and there are so many permatations outside our control on the parent container so it is not feasible, such as the parent container identifier to target is dependant on if single/is part of gallery/any other theme specific settings, different CSS rules on the container like zero height (such as in twentytwenty when a gallery present), etc

				}

			}

			return $html;

		}

		public function add_safe_style_css( $styles ) {

			// Styles needed for text badges which aren't included in safe_style_css by default (may be in future https://core.trac.wordpress.org/ticket/52310 but this would still need to remain for a while for older WordPress versions until this extension drops support for versions without them)

			$styles[] = 'border-top-left-radius';
			$styles[] = 'border-top-right-radius';
			$styles[] = 'border-bottom-left-radius';
			$styles[] = 'border-bottom-right-radius';

			return $styles;

		}

		public function badge( $product, $echo ) {

			$markup = '';

			if ( !empty( $product ) ) {

				$product_id = $product->get_id();
				$product_is_on_sale = $product->is_on_sale();
				$product_is_on_backorder = $product->is_on_backorder();
				$product_category_ids = $product->get_category_ids();
				$product_stock_status = $product->get_stock_status();
				$product_featured = $product->is_featured();

				$badges = get_posts(
					array(
						'numberposts'	=> -1,
						'post_type'		=> 'wcpb_product_badge',
						'post_status'	=> 'publish',
						'order'			=> 'DESC',
						'orderby'		=> 'menu_order',
						'fields'		=> 'ids',
					)
				);

				if ( !empty( $badges ) ) {

					$multiple_badges_per_product = get_option( 'wcpb_product_badges_multiple_badges_per_product' );

					foreach ( $badges as $badge_id ) {

						$is_visible = false;
						$display = false;
						$visibility = get_post_meta( $badge_id, '_wcpb_product_badges_display_visibility', true );
						$products = get_post_meta( $badge_id, '_wcpb_product_badges_display_products', true );

						if ( 'all' == $visibility ) {

							$is_visible = true;

						} elseif ( 'product_pages' == $visibility ) {

							if ( 'woocommerce_single_product_image_thumbnail_html' == current_filter() ) {

								$is_visible = true;

							}

						} elseif ( 'product_loops' == $visibility ) {

							if ( 'woocommerce_before_shop_loop_item' == current_action() || 'woocommerce_blocks_product_grid_item_html' == current_filter() ) {

								$is_visible = true;

							}

						}

						if ( true == $is_visible ) {

							if ( 'sale' == $products ) {

								if ( true == $product_is_on_sale ) {

									$display = true;

								}

							} elseif ( 'non_sale' == $products ) {

								if ( false == $product_is_on_sale ) {

									$display = true;

								}

							} elseif ( 'out_of_stock' == $products ) {

								if ( 'outofstock' == $product_stock_status ) {

									$display = true;

								}

							} elseif ( 'on_backorder' == $products ) {

								if ( true == $product_is_on_backorder ) {

									$display = true;

								}

							} elseif ( 'featured' == $products ) {

								if ( true == $product_featured ) {

									$display = true;

								}

							} elseif ( 'specific' == $products ) {

								$products_specific_categories = get_post_meta( $badge_id, '_wcpb_product_badges_display_products_specific_categories', true );
								$products_specific_products = get_post_meta( $badge_id, '_wcpb_product_badges_display_products_specific_products', true );

								if ( !empty( $products_specific_categories ) || !empty( $products_specific_products ) ) {

									foreach ( $products_specific_categories as $products_specific_category ) {

										if ( in_array( $products_specific_category, $product_category_ids ) ) {

											$display = true;
											break;

										}

									}

									if ( in_array( $product_id, $products_specific_products ) ) {

										$display = true;

									}

								}

							} else {

								$display = true;

							}

							if ( true == $display ) {

								$type = get_post_meta( $badge_id, '_wcpb_product_badges_badge_type', true );

								if ( !empty( $type ) ) {

									$classes = array();
									$position = get_post_meta( $badge_id, '_wcpb_product_badges_badge_position', true );
									$offset = get_post_meta( $badge_id, '_wcpb_product_badges_badge_offset_pixels', true );
									$width = get_post_meta( $badge_id, '_wcpb_product_badges_badge_size_width', true );

									if ( 'top_left' == $position ) {

										$classes[] = 'wcpb-product-badges-badge-top-left';

									} elseif ( 'top_right' == $position ) {

										$classes[] = 'wcpb-product-badges-badge-top-right';

									} elseif ( 'bottom_left' == $position ) {

										$classes[] = 'wcpb-product-badges-badge-bottom-left';

									} elseif ( 'bottom_right' == $position ) {

										$classes[] = 'wcpb-product-badges-badge-bottom-right';

									}

									$classes = implode( ' ', $classes );

									$markup .= '<div class="wcpb-product-badges-badge' . ( !empty( $classes ) ? ' ' . $classes : '' ) . '" style="width: ' . $width . 'px; margin: ' . $offset . 'px;">';

									if ( 'image_library' == $type ) {

										$library_image = get_post_meta( $badge_id, '_wcpb_product_badges_badge_image_library_image', true );

										if ( !empty( $library_image ) ) { // If no library image selected do not display or causes a broken image

											$markup .= '<img class="wcpb-product-badges-badge-img" src="' . esc_url( WCPB_PRODUCT_BADGES_BADGES_URL . $library_image ) . '">';

										}

									} elseif ( 'image_custom' == $type ) {

										$custom_image = get_the_post_thumbnail_url( $badge_id );

										if ( !empty( $custom_image ) ) { // If no custom image selected do not display or causes a broken image

											$markup .= '<img class="wcpb-product-badges-badge-img" src="' . $custom_image . '">';

										}

									} elseif ( 'text' == $type ) {

										$text = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_text', true );
										$text_align = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_text_align', true );
										$text_color = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_text_color', true );
										$background_color = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_background_color', true );
										$font_size = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_font_size', true );
										$font_style = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_font_style', true );
										$font_weight = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_font_weight', true );
										$padding_top = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_padding_top', true );
										$padding_right = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_padding_right', true );
										$padding_bottom = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_padding_bottom', true );
										$padding_left = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_padding_left', true );
										$border_radius_top_left = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_border_radius_top_left', true );
										$border_radius_top_right = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_border_radius_top_right', true );
										$border_radius_bottom_left = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_border_radius_bottom_left', true );
										$border_radius_bottom_right = get_post_meta( $badge_id, '_wcpb_product_badges_badge_text_border_radius_bottom_right', true );

										if ( !empty( $text ) ) {

											// We don't put spaces after ; as wp_kses_post strips this anyway

											$markup .= '<div style="' . ( '' !== $text_align ? 'text-align: ' . $text_align . ';' : '' ) . ( '' !== $text_color ? 'color: ' . $text_color . ';' : '' ) . ( '' !== $background_color ? 'background-color: ' . $background_color . ';' : '' ) . ( '' !== $font_size ? 'font-size: ' . $font_size . 'px;' : '' ) . ( '' !== $font_style ? 'font-style: ' . $font_style . ';' : '' ) . ( '' !== $font_weight ? 'font-weight: ' . $font_weight . ';' : '' ) . ( '' !== $padding_top ? 'padding-top: ' . $padding_top . 'px;' : '' ) . ( '' !== $padding_right ? 'padding-right: ' . $padding_right . 'px;' : '' ) . ( '' !== $padding_bottom ? 'padding-bottom: ' . $padding_bottom . 'px;' : '' ) . ( '' !== $padding_left ? 'padding-left: ' . $padding_left . 'px;' : '' ) . ( '' !== $border_radius_top_left ? 'border-top-left-radius: ' . $border_radius_top_left . 'px;' : '' ) . ( '' !== $border_radius_top_right ? 'border-top-right-radius: ' . $border_radius_top_right . 'px;' : '' ) . ( '' !== $border_radius_bottom_right ? 'border-bottom-right-radius: ' . $border_radius_bottom_right . 'px;' : '' ) . ( '' !== $border_radius_bottom_left ? 'border-bottom-left-radius: ' . $border_radius_bottom_left . 'px;' : '' ) . '">' . $text . '</div>';

										}

									} elseif ( 'code' == $type ) {

										$code = get_post_meta( $badge_id, '_wcpb_product_badges_badge_code_code', true );

										if ( !empty( $code ) ) {

											$markup .= $code;

										}

									}

									$markup .= '</div>';

								}

								if ( 'yes' !== $multiple_badges_per_product ) {

									break;	

								}

							}

						}						

					}

				}			

			}

			if ( true == $echo ) {

				echo wp_kses_post( $markup ); // Note that if considering using wp_kses_post here in future it would strip border-radius from text badges and if using elsewhere such as single product page would strip the <style> tag included conditionally in there

			} else {

				return wp_kses_post( $markup );

			}

		}

	}

}
