<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPB_Product_Badges_Admin' ) ) {

	class WCPB_Product_Badges_Admin {

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueues_block_editor' ) );
			add_action( 'admin_notices', array( $this, 'notices') );
			add_action( 'init', array( $this, 'post_type' ), 0 );
			add_action( 'manage_edit-wcpb_product_badge_columns', array( $this, 'post_type_columns' ) );
			add_action( 'pre_get_posts', array( $this, 'post_type_columns_default_orderby' ) ); 
			add_action( 'manage_edit-wcpb_product_badge_sortable_columns', array( $this, 'post_type_columns_sortable' ) );
			add_action( 'manage_wcpb_product_badge_posts_custom_column', array( $this, 'post_type_columns_values' ) );
			add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ) );
			add_action( 'save_post_wcpb_product_badge', array( $this, 'save_badge' ) );
			add_filter( 'woocommerce_get_sections_products', array( $this, 'settings_section' ) );
			add_filter( 'woocommerce_get_settings_products', array( $this, 'settings_fields' ), 10, 2 );

		}

		public function enqueues() {

			if ( 'wcpb_product_badge' == get_post_type() ) {

				// Admin

				wp_enqueue_script(
					'wcpb-product-badges-admin',
					plugins_url( 'wcpb-product-badges/assets/js/admin.js' ),
					array(
						'jquery',
						'wp-color-picker',
						'wp-i18n',
					),
					WCPB_PRODUCT_BADGES_VERSION
				);

				wp_enqueue_style(
					'wcpb-product-badges-admin',
					plugins_url( 'wcpb-product-badges/assets/css/admin.css' ),
					array(),
					WCPB_PRODUCT_BADGES_VERSION,
					'all'
				);

				// Select2

				wp_enqueue_script(
					'wcpb-product-badges-select2',
					plugins_url( 'wcpb-product-badges/libraries/select2/dist/js/select2.min.js' ),
					array(
						'jquery',
					),
					WCPB_PRODUCT_BADGES_VERSION
				);

				wp_enqueue_style(
					'wcpb-product-badges-select2',
					plugins_url( 'wcpb-product-badges/libraries/select2/dist/css/select2.min.css' ),
					array(),
					WCPB_PRODUCT_BADGES_VERSION,
					'all'
				);

				// Color Picker

				wp_enqueue_style( 'wp-color-picker' );

			}

		}

		public function enqueues_block_editor() {

			// Enqueue the public styles within the block editor so badge postioning is same as frontend within the editor

			wp_enqueue_style(
				'wcpb-product-badges-public',
				plugins_url( 'wcpb-product-badges/assets/css/public.css' ),
				array(),
				WCPB_PRODUCT_BADGES_VERSION,
				'all'
			);

		}

		public function notices() {

			if ( 'wcpb_product_badge' == get_post_type() ) {

				$compatibility_mode = get_option( 'wcpb_product_badges_compatibility_mode' );
				$multiple_badges_per_product = get_option( 'wcpb_product_badges_multiple_badges_per_product' );

				$notices = array();
				$settings_link = 'admin.php?page=wc-settings&tab=products&section=wcpb-product-badges';

				if ( 'yes' == $compatibility_mode ) {

					$notices[] = '<a href="' . $settings_link . '">Compatibility mode</a> is enabled.';

				}

				if ( 'yes' == $multiple_badges_per_product ) {

					$notices[] = '<a href="' . $settings_link . '">Multiple badges per product</a> is enabled.';

				}

				if ( !empty( $notices ) ) {

					echo '<div class="notice notice-info"><p>' . implode( '<br>', map_deep( $notices, 'wp_kses_post' ) ) . '</p></div>';

				}

			}

		}

		public function post_type() {

			$labels = array(
				'name'						=> __( 'Product Badges', 'wcpb-product-badges' ),
				'singular_name'				=> __( 'Product Badge', 'wcpb-product-badges' ),
				'menu_name'					=> __( 'Badges', 'wcpb-product-badges' ),
				'name_admin_bar'			=> __( 'Product Badge', 'wcpb-product-badges' ),
				'archives'					=> __( 'Archives', 'wcpb-product-badges' ),
				'attributes'				=> __( 'Attributes', 'wcpb-product-badges' ),
				'parent_item_colon'			=> __( 'Parent Product Badge:', 'wcpb-product-badges' ),
				'all_items'					=> __( 'Badges', 'wcpb-product-badges' ), // Would be "All Product Badges" but would mean the dashboard menu would carry that name, so renamed to Badges
				'add_new_item'				=> __( 'Add New Product Badge', 'wcpb-product-badges' ),
				'add_new'					=> __( 'Add New', 'wcpb-product-badges' ),
				'new_item'					=> __( 'New Product Badge', 'wcpb-product-badges' ),
				'edit_item'					=> __( 'Edit Product Badge', 'wcpb-product-badges' ),
				'update_item'				=> __( 'Update Product Badge', 'wcpb-product-badges' ),
				'view_item'					=> __( 'View Product Badge', 'wcpb-product-badges' ),
				'view_items'				=> __( 'View Product Badges', 'wcpb-product-badges' ),
				'search_items'				=> __( 'Search Product Badges', 'wcpb-product-badges' ),
				'not_found'					=> __( 'Not found', 'wcpb-product-badges' ),
				'not_found_in_trash'		=> __( 'Not found in Trash', 'wcpb-product-badges' ),
				'featured_image'			=> __( 'Badge image custom', 'wcpb-product-badges' ),
				'set_featured_image'		=> __( 'Set custom badge image', 'wcpb-product-badges' ),
				'remove_featured_image'		=> __( 'Remove image', 'wcpb-product-badges' ),
				'use_featured_image'		=> __( 'Use as image', 'wcpb-product-badges' ),
				'insert_into_item'			=> __( 'Insert into product badge', 'wcpb-product-badges' ),
				'uploaded_to_this_item'		=> __( 'Uploaded to this product badge', 'wcpb-product-badges' ),
				'items_list'				=> __( 'Product badges list', 'wcpb-product-badges' ),
				'items_list_navigation'		=> __( 'Product badges list navigation', 'wcpb-product-badges' ),
				'filter_items_list'			=> __( 'Filter product badges list', 'wcpb-product-badges' ),
				'item_published'			=> __( 'Product badge published', 'wcpb-product-badges' ),
				'item_published_privately'	=> __( 'Product badge published privately', 'wcpb-product-badges' ),
				'item_reverted_to_draft'	=> __( 'Product badge reverted to draft', 'wcpb-product-badges' ),
				'item_scheduled'			=> __( 'Product badge scheduled', 'wcpb-product-badges' ),
				'item_updated'				=> __( 'Product badge updated', 'wcpb-product-badges' ),
			);
			
			$args = array(
				'description'			=> '',
				'labels'				=> $labels,
				'supports'				=> array( 'title', 'thumbnail', 'page-attributes' ),
				'hierarchical'			=> false,
				'public'				=> false,
				'show_ui'				=> true,
				'show_in_menu'			=> 'edit.php?post_type=product',
				'menu_position'			=> 0,
				'menu_icon'				=> '',
				'show_in_admin_bar'		=> true,
				'show_in_nav_menus'		=> false,
				'can_export'			=> true,
				'has_archive'			=> false,
				'exclude_from_search'	=> true,
				'publicly_queryable'	=> false,
				'show_in_rest'			=> false,
			);

			register_post_type( 'wcpb_product_badge', $args );

		}

		public function post_type_columns( $post_columns ) {

			$date_label = $post_columns['date'];
			unset( $post_columns['date'] );
			$post_columns['type'] = __( 'Type', 'wcpb-product-badges' );
			$post_columns['position'] = __( 'Position', 'wcpb-product-badges' );
			$post_columns['visibility'] = __( 'Visiblity', 'wcpb-product-badges' );
			$post_columns['products'] = __( 'Products', 'wcpb-product-badges' );
			$post_columns['order'] = __( 'Order', 'wcpb-product-badges' );
			$post_columns['date'] = $date_label;
			return $post_columns;

		}

		public function post_type_columns_default_orderby( $query ) {

			if ( is_admin() && 'wcpb_product_badge' == $query->get( 'post_type' ) ) {

				$query->set( 'orderby', 'menu_order' ); // We do not set the orderby as this would stop clicking the order column heading sorting from working

			}

		}

		public function post_type_columns_sortable( $columns ) {

			$columns['type'] = 'type';
			$columns['position'] = 'position';
			$columns['visibility'] = 'visibility';
			$columns['products'] = 'products';
			$columns['order'] = 'order';
			return $columns;

		}

		public function post_type_columns_values( $name ) {

			global $post;

			switch ( $name ) {
				case 'type':
					$type = get_post_meta( $post->ID, '_wcpb_product_badges_badge_type', true );
					if ( 'image_library' == $type ) {
						$type = __( 'Image library', 'wcpb-product-badges' );
					} elseif ( 'image_custom' == $type ) {
						$type = __( 'Image custom', 'wcpb-product-badges' );
					} elseif ( 'text' == $type ) {
						$type = __( 'Text', 'wcpb-product-badges' );
					} elseif ( 'code' == $type ) {
						$type = __( 'Code', 'wcpb-product-badges' );
					} else {
						$type = '';
					}
					echo esc_html( $type );
					break;
				case 'position':
					$position = get_post_meta( $post->ID, '_wcpb_product_badges_badge_position', true );
					if ( 'top_left' == $position ) {
						$position = __( 'Top left', 'wcpb-product-badges' );
					} elseif ( 'top_right' == $position ) {
						$position = __( 'Top right', 'wcpb-product-badges' );
					} elseif ( 'bottom_left' == $position ) {
						$position = __( 'Bottom left', 'wcpb-product-badges' );
					} elseif ( 'bottom_right' == $position ) {
						$position = __( 'Bottom right', 'wcpb-product-badges' );
					} else {
						$position = '';
					}
					echo esc_html( $position );
					break;
				case 'visibility':
					$visibility = get_post_meta( $post->ID, '_wcpb_product_badges_display_visibility', true );
					if ( 'all' == $visibility ) {
						$visibility = __( 'All', 'wcpb-product-badges' );
					} elseif ( 'product_pages' == $visibility ) {
						$visibility = __( 'Product pages', 'wcpb-product-badges' );
					} elseif ( 'product_loops' == $visibility ) {
						$visibility = __( 'Product loops', 'wcpb-product-badges' );
					} else {
						$visibility = '';
					}
					echo esc_html( $visibility );
					break;
				case 'products':
					$products = get_post_meta( $post->ID, '_wcpb_product_badges_display_products', true );
					if ( 'all' == $products ) {
						$products = __( 'All', 'wcpb-product-badges' );
					} elseif ( 'sale' == $products ) {
						$products = __( 'Sale', 'wcpb-product-badges' );
					} elseif ( 'non_sale' == $products ) {
						$products = __( 'Non-sale', 'wcpb-product-badges' );
					} elseif ( 'out_of_stock' == $products ) {
						$products = __( 'Out of stock', 'wcpb-product-badges' );
					} elseif ( 'on_backorder' == $products ) {
						$products = __( 'On backorder', 'wcpb-product-badges' );
					} elseif ( 'featured' == $products ) {
						$products = __( 'Featured', 'wcpb-product-badges' );
					} elseif ( 'specific' == $products ) {
						$products = __( 'Specific', 'wcpb-product-badges' );
					} else {
						$products = '';
					}
					echo esc_html( $products );
					break;
				case 'order':
					echo esc_html( $post->menu_order );
					break;
				default:
					break;
			}

		}

		public function meta_boxes() {

			add_meta_box(
				'wcpb-product-badges-badge',
				__( 'Badge', 'wcpb-product-badges' ),
				array( $this, 'meta_box_badge' ),
				'wcpb_product_badge',
				'normal',
				'default'
			);

			add_meta_box(
				'wcpb-product-badges-display',
				__( 'Display', 'wcpb-product-badges' ),
				array( $this, 'meta_box_display' ),
				'wcpb_product_badge',
				'normal',
				'default'
			);
		}

		public function meta_box_badge() {

			global $_wp_admin_css_colors;
			global $post;

			$post_id = $post->ID;
			$badge_type = get_post_meta( $post_id, '_wcpb_product_badges_badge_type', true );
			$badge_position = get_post_meta( $post_id, '_wcpb_product_badges_badge_position', true );
			$badge_offset_pixels = get_post_meta( $post_id, '_wcpb_product_badges_badge_offset_pixels', true );
			$badge_offset_pixels = ( empty( $badge_offset_pixels ) ? '0' : $badge_offset_pixels ); // Default
			$badge_size_width = get_post_meta( $post_id, '_wcpb_product_badges_badge_size_width', true );
			$badge_size_width = ( empty( $badge_size_width ) ? '150' : $badge_size_width ); // Default
			$badge_image_library_image = get_post_meta( $post_id, '_wcpb_product_badges_badge_image_library_image', true );
			$badge_text_text = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_text', true );
			$badge_text_text_align = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_text_align', true );
			$badge_text_text_align = ( empty( $badge_text_text_align ) ? 'center' : $badge_text_text_align ); // Default
			$badge_text_text_color = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_text_color', true );
			$badge_text_background_color = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_background_color', true );
			$badge_text_font_weight = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_font_weight', true );
			$badge_text_font_weight = ( empty( $badge_text_font_weight ) ? 'normal' : $badge_text_font_weight ); // Default
			$badge_text_font_style = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_font_style', true );
			$badge_text_font_style = ( empty( $badge_text_font_style ) ? 'normal' : $badge_text_font_style ); // Default
			$badge_text_font_size = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_font_size', true );
			$badge_text_font_size = ( empty( $badge_text_font_size ) ? '12' : $badge_text_font_size ); // Default
			$badge_text_padding_top = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_padding_top', true );
			$badge_text_padding_top = ( empty( $badge_text_padding_top ) ? '0' : $badge_text_padding_top ); // Default
			$badge_text_padding_right = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_padding_right', true );
			$badge_text_padding_right = ( empty( $badge_text_padding_right ) ? '0' : $badge_text_padding_right ); // Default
			$badge_text_padding_bottom = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_padding_bottom', true );
			$badge_text_padding_bottom = ( empty( $badge_text_padding_bottom ) ? '0' : $badge_text_padding_bottom ); // Default
			$badge_text_padding_left = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_padding_left', true );
			$badge_text_padding_left = ( empty( $badge_text_padding_left ) ? '0' : $badge_text_padding_left ); // Default
			$badge_text_border_radius_top_left = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_border_radius_top_left', true );
			$badge_text_border_radius_top_left = ( empty( $badge_text_border_radius_top_left ) ? '0' : $badge_text_border_radius_top_left ); // Default
			$badge_text_border_radius_top_right = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_border_radius_top_right', true );
			$badge_text_border_radius_top_right = ( empty( $badge_text_border_radius_top_right ) ? '0' : $badge_text_border_radius_top_right ); // Default
			$badge_text_border_radius_bottom_left = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_border_radius_bottom_left', true );
			$badge_text_border_radius_bottom_left = ( empty( $badge_text_border_radius_bottom_left ) ? '0' : $badge_text_border_radius_bottom_left ); // Default
			$badge_text_border_radius_bottom_right = get_post_meta( $post_id, '_wcpb_product_badges_badge_text_border_radius_bottom_right', true );
			$badge_text_border_radius_bottom_right = ( empty( $badge_text_border_radius_bottom_right ) ? '0' : $badge_text_border_radius_bottom_right ); // Default
			$badge_code_code = get_post_meta( $post_id, '_wcpb_product_badges_badge_code_code', true );

			wp_nonce_field( 'wcpb_product_badge_save', 'wcpb_product_badge_save_nonce' );

			?>

			<div>
				<p>
					<strong><?php esc_html_e( 'Type', 'wcpb-product-badges' ); ?></strong>
					<input type="radio" id="wcpb-product-badges-badge-type-image-library" name="wcpb_product_badges_badge_type" value="image_library"<?php echo ( '' == $badge_type || 'image_library' == $badge_type ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-type-image-library"><?php esc_html_e( 'Image library', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-badge-type-image-custom" name="wcpb_product_badges_badge_type" value="image_custom"<?php echo ( 'image_custom' == $badge_type ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-type-image-custom"><?php esc_html_e( 'Image custom', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-badge-type-text" name="wcpb_product_badges_badge_type" value="text"<?php echo ( 'text' == $badge_type ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-type-text"><?php esc_html_e( 'Text', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-badge-type-code" name="wcpb_product_badges_badge_type" value="code"<?php echo ( 'code' == $badge_type ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-type-code"><?php esc_html_e( 'Code', 'wcpb-product-badges' ); ?></label>
				</p>
				<p>
					<strong><?php esc_html_e( 'Position', 'wcpb-product-badges' ); ?></strong>
					<input type="radio" id="wcpb-product-badges-badge-position-top-left" name="wcpb_product_badges_badge_position" value="top_left"<?php echo ( 'top_left' == $badge_position ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-position-top-left"><?php esc_html_e( 'Top left', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-badge-position-top-right" name="wcpb_product_badges_badge_position" value="top_right"<?php echo ( '' == $badge_position || 'top_right' == $badge_position ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-position-top-right"><?php esc_html_e( 'Top right', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-badge-position-bottom-left" name="wcpb_product_badges_badge_position" value="bottom_left"<?php echo ( 'bottom_left' == $badge_position ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-position-bottom-left"><?php esc_html_e( 'Bottom left', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-badge-position-bottom-right" name="wcpb_product_badges_badge_position" value="bottom_right"<?php echo ( 'bottom_right' == $badge_position ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-badge-position-bottom-right"><?php esc_html_e( 'Bottom right', 'wcpb-product-badges' ); ?></label>
				</p>
				<p>
					<strong><?php esc_html_e( 'Offset', 'wcpb-product-badges' ); ?></strong>
					<label>
						<?php esc_html_e( 'Pixels', 'wcpb-product-badges' ); ?><br>
						<input type="number" id="wcpb-product-badges-badge-offset-pixels" name="wcpb_product_badges_badge_offset_pixels" value="<?php echo esc_html( $badge_offset_pixels ); ?>" step="1" min="0" required><br>
					</label>
					<p><small><?php esc_html_e( 'Offsets the badge from the edge.', 'wcpb-product-badges' ); ?></small></p>
				</p>
				<p>
					<strong><?php esc_html_e( 'Size', 'wcpb-product-badges' ); ?></strong>
					<label>
						<?php esc_html_e( 'Width', 'wcpb-product-badges' ); ?><br>
						<input type="number" id="wcpb-product-badges-badge-size-width" name="wcpb_product_badges_badge_size_width" value="<?php echo esc_html( $badge_size_width ); ?>" step="1" min="30" required><br>
					</label>
					<p><small><?php esc_html_e( 'Height is automatically calculated.', 'wcpb-product-badges' ); ?></small></p>
				</p>
				<div id="wcpb-product-badges-badge-image-library-filters">
					<strong><?php esc_html_e( 'Image library filters', 'wcpb-product-badges' ); ?></strong>
					<div><?php // Options added dynamically, do not indent as JS condition will not be met ?></div>
				</div>
			</div>

			<div id="wcpb-product-badges-badge-image-library-expand">
				<style>
					.wcpb-product-badges-badge-image-library-image-selected > div {
						border: 3px solid <?php echo esc_html( $_wp_admin_css_colors[get_user_option('admin_color')]->colors[2] ); ?> !important;
					}
				</style>
				<input type="hidden" name="wcpb_product_badges_badge_image_library_image" id="wcpb-product-badges-badge-image-library-image" value="<?php echo esc_html( $badge_image_library_image ); ?>">
				<?php
				$images = $this->badge_image_library_images();
				$filters = array();
				if ( !empty( $images ) ) {

					foreach ( $images as $image_file => $image_data ) {

						$filters['type'][] = $image_data['type'];
						$filters['color'][] = $image_data['color'];

						?>
						
						<div data-image="<?php echo esc_html( $image_file ); ?>" class="wcpb-product-badges-badge-image-library-image wcpb-product-badges-badge-image-library-image-filter-all-type wcpb-product-badges-badge-image-library-image-filter-all-color <?php echo 'wcpb-product-badges-badge-image-library-image-filter-type-' . esc_html( $image_data['type'] ) . ' wcpb-product-badges-badge-image-library-image-filter-color-' . esc_html( $image_data['color'] ); ?><?php echo ( $image_file == $badge_image_library_image ? ' wcpb-product-badges-badge-image-library-image-selected' : '' ); ?>">
							<div style="background-image: url(<?php echo esc_url( WCPB_PRODUCT_BADGES_BADGES_URL ); ?><?php echo esc_html( $image_file ); ?>)"></div>
						</div>

						<?php

					}

					$filters['type'] = array_unique( $filters['type'] );
					$filters['color'] = array_unique( $filters['color'] );

					?>

					<div id="wcpb-product-badges-badge-image-library-filters-before-append" data-filters-no-results-text="<?php esc_html_e( 'No library images available for your selected filters, try changing the filters set.', 'wcpb-product-badges' ); ?>">
						<?php
						foreach ( $filters as $filter_name => $filter_data ) {
							asort( $filter_data );
							?>
							<label>
								<?php echo esc_html( ucfirst( $filter_name ) ); ?><br>
								<select data-filter="<?php echo esc_html( $filter_name ); ?>">
									<option value="wcpb-product-badges-badge-image-library-image-filter-all-<?php echo esc_html( $filter_name ); ?>"><?php esc_html_e( 'All', 'wcpb-product-badges' ); ?></option>
									<?php
									foreach ( $filter_data as $k => $v ) {
										$v_changed = ( 'valentines' == $v ? 'valentine\'s' : $v );
										?>
										<option value="wcpb-product-badges-badge-image-library-image-filter-<?php echo esc_html( $filter_name ); ?>-<?php echo esc_html( $v ); ?>"><?php echo esc_html( ucfirst( str_replace( '-', ' ', ( '' == $v ? $v : $v_changed ) ) ) ); ?></option>
										<?php
									}
									?>
								</select>
							</label>
							<?php
						}
						?>
					</div>

				<?php } ?>

			</div>

			<div id="wcpb-product-badges-badge-image-custom-expand">
				<p><?php esc_html_e( 'Select a custom image using the "Badge image custom" meta box.', 'wcpb-product-badges' ); ?></p>
			</div>

			<div id="wcpb-product-badges-badge-text-expand">
				<div>
					<p>
						<label>
							<?php esc_html_e( 'Text', 'wcpb-product-badges' ); ?><br>
							<input type="text" name="wcpb_product_badges_badge_text_text" value="<?php echo esc_html( $badge_text_text ); ?>">
						</label>
					</p>
					<p>
						<label>
							<?php esc_html_e( 'Text align', 'wcpb-product-badges' ); ?><br>
							<select name="wcpb_product_badges_badge_text_text_align">
								<option value="center"<?php echo ( 'center' == $badge_text_text_align ? ' selected' : '' ); ?>><?php esc_html_e( 'Center', 'wcpb-product-badges' ); ?></option>
								<option value="left"<?php echo ( 'left' == $badge_text_text_align ? ' selected' : '' ); ?>><?php esc_html_e( 'Left', 'wcpb-product-badges' ); ?></option>
								<option value="right"<?php echo ( 'right' == $badge_text_text_align ? ' selected' : '' ); ?>><?php esc_html_e( 'Right', 'wcpb-product-badges' ); ?></option>
							</select>
						</label>
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-text-color"><?php esc_html_e( 'Text color', 'wcpb-product-badges' ); ?></label><br>
						<input type="text" name="wcpb_product_badges_badge_text_text_color" id="wcpb-product-badges-badge-text-text-color" class="wcpb-product-badges-color-picker" value="<?php echo esc_html( $badge_text_text_color ); ?>">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-background-color"><?php esc_html_e( 'Background color', 'wcpb-product-badges' ); ?></label><br>
						<input type="text" name="wcpb_product_badges_badge_text_background_color" id="wcpb-product-badges-badge-text-background-color" class="wcpb-product-badges-color-picker" value="<?php echo esc_html( $badge_text_background_color ); ?>">
					</p>
				</div>
				<div>
					<p>
						<label for="wcpb-product-badges-badge-text-font-size"><?php esc_html_e( 'Font size', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_font_size" id="wcpb-product-badges-badge-text-font-size" value="<?php echo esc_html( $badge_text_font_size ); ?>" min="1">
					</p>
					<p>
						<label>
							<?php esc_html_e( 'Font style', 'wcpb-product-badges' ); ?><br>
							<select name="wcpb_product_badges_badge_text_font_style">
								<option value="normal"<?php echo ( 'normal' == $badge_text_font_style ? ' selected' : '' ); ?>><?php esc_html_e( 'Normal', 'wcpb-product-badges' ); ?></option>
								<option value="italic"<?php echo ( 'italic' == $badge_text_font_style ? ' selected' : '' ); ?>><?php esc_html_e( 'Italic', 'wcpb-product-badges' ); ?></option>
							</select>
						</label>
					</p>
					<p>
						<label>
							<?php esc_html_e( 'Font weight', 'wcpb-product-badges' ); ?><br>
							<select name="wcpb_product_badges_badge_text_font_weight">
								<option value="normal"<?php echo ( 'normal' == $badge_text_font_weight ? ' selected' : '' ); ?>><?php esc_html_e( 'Normal', 'wcpb-product-badges' ); ?></option>
								<option value="bold"<?php echo ( 'bold' == $badge_text_font_weight ? ' selected' : '' ); ?>><?php esc_html_e( 'Bold', 'wcpb-product-badges' ); ?></option>
							</select>
						</label>
					</p>
				</div>
				<div>
					<p>
						<label for="wcpb-product-badges-badge-text-padding-top"><?php esc_html_e( 'Padding top', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_padding_top" id="wcpb-product-badges-badge-text-padding-top" value="<?php echo esc_html( $badge_text_padding_top ); ?>" min="0">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-padding-right"><?php esc_html_e( 'Padding right', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_padding_right" id="wcpb-product-badges-badge-text-padding-right" value="<?php echo esc_html( $badge_text_padding_right ); ?>" min="0">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-padding-bottom"><?php esc_html_e( 'Padding bottom', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_padding_bottom" id="wcpb-product-badges-badge-text-padding-bottom" value="<?php echo esc_html( $badge_text_padding_bottom ); ?>" min="0">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-padding-left"><?php esc_html_e( 'Padding left', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_padding_left" id="wcpb-product-badges-badge-text-padding-left" value="<?php echo esc_html( $badge_text_padding_left ); ?>" min="0">
					</p>
				</div>
				<div>
					<p>
						<label for="wcpb-product-badges-badge-text-border-radius-top-left"><?php esc_html_e( 'Border radius top left', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_border_radius_top_left" id="wcpb-product-badges-badge-text-border-radius-top-left" value="<?php echo esc_html( $badge_text_border_radius_top_left ); ?>" min="0">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-border-radius-top-right"><?php esc_html_e( 'Border radius top right', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_border_radius_top_right" id="wcpb-product-badges-badge-text-border-radius-top-right" value="<?php echo esc_html( $badge_text_border_radius_top_right ); ?>" min="0">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-border-radius-bottom-left"><?php esc_html_e( 'Border radius bottom left', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_border_radius_bottom_left" id="wcpb-product-badges-badge-text-border-radius-bottom-left" value="<?php echo esc_html( $badge_text_border_radius_bottom_left ); ?>" min="0">
					</p>
					<p>
						<label for="wcpb-product-badges-badge-text-border-radius-bottom-right"><?php esc_html_e( 'Border radius bottom right', 'wcpb-product-badges' ); ?></label><br>
						<input type="number" name="wcpb_product_badges_badge_text_border_radius_bottom_right" id="wcpb-product-badges-badge-text-border-radius-bottom-right" value="<?php echo esc_html( $badge_text_border_radius_bottom_right ); ?>" min="0">
					</p>
				</div>
			</div>

			<div id="wcpb-product-badges-badge-code-expand">
				<p>
					<label><?php esc_html_e( 'Code', 'wcpb-product-badges' ); ?><br>
						<textarea type="text" name="wcpb_product_badges_badge_code_code" placeholder="<?php esc_html_e( 'Add your code here, it is recommended you use a block element for the contents of your badge to ensure it fits to the badge size width.', 'wcpb-product-badges' ); ?>"><?php echo esc_html( $badge_code_code ); ?></textarea>
					</label>
				</p>
				<p><?php echo sprintf( wp_kses_post( 'Any code entered is output through <a href="%s" target="_blank">wp_kses_post()</a> which sanitizes the code for allowed tags.', 'wcpb-product-badges' ), esc_url( 'https://developer.wordpress.org/reference/functions/wp_kses_post/' ) ); ?></p>
			</div>

			<?php

		}

		public function meta_box_display() { 

			global $post;
			$post_id = $post->ID;
			$display_visibility = get_post_meta( $post_id, '_wcpb_product_badges_display_visibility', true );
			$display_products = get_post_meta( $post_id, '_wcpb_product_badges_display_products', true );
			$display_products_specific_categories = get_post_meta( $post_id, '_wcpb_product_badges_display_products_specific_categories', true );
			$display_products_specific_categories = ( !empty( $display_products_specific_categories ) ? $display_products_specific_categories : array() );
			$display_products_specific_products = get_post_meta( $post_id, '_wcpb_product_badges_display_products_specific_products', true );
			$display_products_specific_products = ( !empty( $display_products_specific_products ) ? $display_products_specific_products : array() );

			?>

			<div>
				<p>
					<strong><?php esc_html_e( 'Visibility', 'wcpb-product-badges' ); ?></strong>
					<input type="radio" id="wcpb-product-badges-display-visibility-all" name="wcpb_product_badges_display_visibility" value="all"<?php echo ( '' == $display_visibility || 'all' == $display_visibility ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-visibility-all"><?php esc_html_e( 'All', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-visibility-product-pages" name="wcpb_product_badges_display_visibility" value="product_pages"<?php echo ( 'product_pages' == $display_visibility ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-visibility-product-pages"><?php esc_html_e( 'Product pages', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-visibility-product-loops" name="wcpb_product_badges_display_visibility" value="product_loops"<?php echo ( 'product_loops' == $display_visibility ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-visibility-product-loops"><?php esc_html_e( 'Product loops', 'wcpb-product-badges' ); ?></label>
				</p>
				<p><small><?php esc_html_e( 'Product pages are individual product pages, product loops are the display of products on the shop, category and search pages in addition to areas such as related products and WooCommerce product blocks (except "All Products" block).', 'wcpb-product-badges' ); ?></small></p>
				<p>
					<strong><?php esc_html_e( 'Products', 'wcpb-product-badges' ); ?></strong>
					<input type="radio" id="wcpb-product-badges-display-products-all" name="wcpb_product_badges_display_products" value="all"<?php echo ( '' == $display_products || 'all' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-all"><?php esc_html_e( 'All', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-products-sale" name="wcpb_product_badges_display_products" value="sale"<?php echo ( 'sale' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-sale"><?php esc_html_e( 'Sale', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-products-non-sale" name="wcpb_product_badges_display_products" value="non_sale"<?php echo ( 'non_sale' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-non-sale"><?php esc_html_e( 'Non-sale', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-products-out-of-stock" name="wcpb_product_badges_display_products" value="out_of_stock"<?php echo ( 'out_of_stock' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-out-of-stock"><?php esc_html_e( 'Out of stock', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-products-on-backorder" name="wcpb_product_badges_display_products" value="on_backorder"<?php echo ( 'on_backorder' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-on-backorder"><?php esc_html_e( 'On backorder', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-products-featured" name="wcpb_product_badges_display_products" value="featured"<?php echo ( 'featured' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-featured"><?php esc_html_e( 'Featured', 'wcpb-product-badges' ); ?></label><br>
					<input type="radio" id="wcpb-product-badges-display-products-specific" name="wcpb_product_badges_display_products" value="specific"<?php echo ( 'specific' == $display_products ? ' checked' : '' ); ?>>
					<label for="wcpb-product-badges-display-products-specific"><?php esc_html_e( 'Specific', 'wcpb-product-badges' ); ?></label>
				</p>
				<p>
					<small>
						<?php
						// translators: settings URL
						echo sprintf( wp_kses_post( 'Where there are multiple product badges assigned to a product then only one product badge will display and is prioritised by the order number set from high to low (see "Attributes" meta box). If you wish to assign multiple badges per product instead of one see <a href="%s">settings</a>.', 'wcpb-product-badges' ), 'admin.php?page=wc-settings&tab=products&section=wcpb-product-badges' );
						?>
					</small>
				</p>
			</div>

			<div id="wcpb-product-badges-display-products-specific-expand">
				<div>
					<label><?php esc_html_e( 'Categories', 'wcpb-product-badges' ); ?><br>
						<select class="wcpb-product-badges-select2" name="wcpb_product_badges_display_products_specific_categories[]" multiple="multiple">
							<?php
							$categories = get_terms(
								'product_cat',
								array(
									'orderby'    => 'name',
									'order'      => 'asc',
									'hide_empty' => false,
								)
							);
							foreach ( $categories as $category ) {
								?>
								<option value="<?php echo esc_html( $category->term_id ); ?>"<?php echo ( in_array( $category->term_id, $display_products_specific_categories ) ? ' selected' : '' ); ?>><?php echo esc_html( $category->name ); ?></option>
								<?php
							}
							?>
						</select>
					</label>
				</div>
				<div>
					<label>
						<?php esc_html_e( 'Products', 'wcpb-product-badges' ); ?><br>
						<select class="wcpb-product-badges-select2" name="wcpb_product_badges_display_products_specific_products[]" multiple="multiple">
							<?php
							$products = get_posts(
								array(
									'orderby'			=> 'name',
									'order'				=> 'asc',
									'fields'			=> 'ids',
									'post_type'			=> 'product',
									'posts_per_page'	=> -1,
								)
							);
							foreach ( $products as $product ) {
								?>
								<option value="<?php echo esc_html( $product ); ?>"<?php echo ( in_array( $product, $display_products_specific_products ) ? ' selected' : '' ); ?>><?php echo esc_html( get_the_title( $product ) ); ?></option>
								<?php
							}
							?>
						</select>
					</label>
				</div>
			</div>

			<?php

		}

		public function save_badge( $post_id ) {

			if ( isset( $_POST['wcpb_product_badge_save_nonce'] ) ) {

				if ( wp_verify_nonce( sanitize_key( $_POST['wcpb_product_badge_save_nonce'] ), 'wcpb_product_badge_save' ) ) {

					if ( !empty( $_POST ) ) {

						foreach ( $_POST as $key => $value ) {

							if ( strpos( $key, 'wcpb_product_badges_' ) === 0 ) {

								update_post_meta( $post_id, '_' . $key, $value );

							}

							// If the select2 fields are empty set them to empty (if they have nothing selected they aren't in $_POST so if trying to remove existing items from them they wouldn't get emptied without these conditions), it's set to an empty array as the conditions based off this meta use in_array functions which will throw a warning if not an empty array

							if ( !isset( $_POST['wcpb_product_badges_display_products_specific_categories'] ) ) {

								update_post_meta( $post_id, '_wcpb_product_badges_display_products_specific_categories', array() );

							}

							if ( !isset( $_POST['wcpb_product_badges_display_products_specific_products'] ) ) {

								update_post_meta( $post_id, '_wcpb_product_badges_display_products_specific_products', array() );

							}

						}

					}

				}

			}

		}

		public function settings_section( $sections ) {

			$sections['wcpb-product-badges'] = __( 'Product badges', 'wcpb-product-badges' );
			return $sections;

		}

		public function settings_fields( $settings, $current_section ) {

			// ID used as field ID and becomes option_name

			if ( 'wcpb-product-badges' == $current_section ) {

				$product_badges_settings[] = array(
					'id'	=> 'wcpb_product_badges',
					'name'	=> esc_html__( 'Product badges', 'wcpb-product-badges' ),
					'type'	=> 'title',
				);

				$product_badges_settings[] = array(
					'name'     => esc_html__( 'Compatibility mode', 'wcrp-rental-products' ),
					'id'       => 'wcpb_product_badges_compatibility_mode',
					'type'     => 'checkbox',
					'desc'     => esc_html__( 'Enable compatibility mode', 'wcrp-rental-products' ),
					'desc_tip' => esc_html__( 'If you have product badge and/or product image display issues it is likely that your theme, plugins/extensions and/or development changes have amended the standard WooCommerce product display functionality. Enabling this setting will attempt to display product badges using an alternative display method. When enabled you may find badges are displayed outside product images in some areas of your website, this is most likely to occur with bottom positioned badges. In this scenario if you wish to attempt to position them within your product images we recommend using either the offset option when adding/editing a badge or by applying custom CSS to position as required. Default is disabled.', 'wcrp-rental-products' ),
					'checkboxgroup' => 'start',
				);

				$product_badges_settings[] = array(
					'name'     => esc_html__( 'Multiple badges per product', 'wcrp-rental-products' ),
					'id'       => 'wcpb_product_badges_multiple_badges_per_product',
					'type'     => 'checkbox',
					'desc'     => esc_html__( 'Enable multiple badges per product', 'wcrp-rental-products' ),
					'desc_tip' => esc_html__( 'When enabled if more than one badge is assigned to a product then all assigned badges will be displayed instead of one. In this scenario the order attribute (used for prioritising badges for display over others) is ignored. It may cause clashes between badges and therefore is not recommended. In addition, when it is enabled (and when more than one badge is assigned to a product) the standard WooCommerce magnify icon will be hidden to avoid clashes (additionally your theme, plugins/extensions and/or development changes may have added other icons to product images which you may need to hide using custom CSS). Default is disabled.', 'wcrp-rental-products' ),
					'checkboxgroup' => 'start',
				);

				return $product_badges_settings;

			} else {

				return $settings;

			}

		}

		public function badge_image_library_images() {

			// All fonts (see fonts) used are sourced from Google Fonts licensed under the Open Font License, this excludes any fonts already included on an image taken from the source URL shown
			
			// Parts of the image maybe sourced from openclipart.org (see sources) which is a directory of entirely public domain imagery

			// All images should be in .svg format with fonts converted to curves

			// All inner array values should A-Z/0-9 lower case with hyphens as spaces only (any other could cause filtering to work incorrectly due to class identifiers), if requires apostrophe (e.g. valentine's) then set this on the filter output using the condition included

			$images = array();

			$images['000001.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'green',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/189026/snowman',
				),
			);

			$images['000002.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/178125/circle-tag',
				),
			);

			$images['000003.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/178123/horizontal-tag',
				),
			);

			$images['000004.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'orange',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/178124/vertical-tag',
				),
			);

			$images['000005.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/21143/heart-glossy-two',
				),
			);

			$images['000006.svg'] = array(
				'type'		=> 'easter',
				'color'		=> 'pink',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/191066/blue-bunny',
				),
			);

			$images['000007.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/312118/santa-claus',
				),
			);

			$images['000008.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'gray',
				'fonts'		=> array(
					'mountains-of-christmas',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/163945/ghost',
				),
			);

			$images['000009.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'gray',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/34567/tango-input-mouse',
				),
			);

			$images['000010.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000011.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'gray',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/103309/skull',
				),
			);

			$images['000012.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/312063/santa-claus',
				),
			);

			$images['000013.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'yellow',
				'fonts'		=> array(
					'calligraffiti',
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/235645/blonde-cartoon-cupid',
				),
			);

			$images['000014.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/275136/keyboard',
				),
			);

			$images['000015.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/173812/christmas-ribbon',
				),
			);

			$images['000016.svg'] = array(
				'type'		=> 'easter',
				'color'		=> 'pink',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/192661/pink-rabbit-lapin-rose',
				),
			);

			$images['000017.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/139003/price-tag',
				),
			);

			$images['000018.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(),
			);

			$images['000019.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'orange',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/86065/halloween-pumpkin-smile',
				),
			);

			$images['000020.svg'] = array(
				'type'		=> 'easter',
				'color'		=> 'blue',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/240214/easter-egg-7',
				),
			);

			$images['000021.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(),
				'sources'	=> array(
					'https://openclipart.org/detail/319600/grungy-sale-stencil-text',
				),
			);

			$images['000022.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'press-start-2p',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/169900/circuit-board',
				),
			);

			$images['000023.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/189339/snowman',
				),
			);

			$images['000024.svg'] = array(
				'type'		=> 'easter',
				'color'		=> 'yellow',
				'fonts'		=> array(),
				'sources'	=> array(
					'https://openclipart.org/detail/298980/happy-easter-greeting-card',
				),
			);

			$images['000025.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/295082/perfect-heart',
				),
			);

			$images['000026.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000027.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'blue',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/22511/hand-cursor',
				),
			);

			$images['000028.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'pink',
				'fonts'		=> array(
					'lobster',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/21617/pink-lace-heart',
				),
			);

			$images['000029.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'lobster',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/166352/snow-man',
				),
			);

			$images['000030.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'gray',
				'fonts'		=> array(
					'mountains-of-christmas',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/12031/halloween-pumpkins',
				),
			);

			$images['000031.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'yellow',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/297220/male-computer-user-1',
				),
			);

			$images['000032.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(),
			);

			$images['000033.svg'] = array(
				'type'		=> 'easter',
				'color'		=> 'yellow',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/216717/3-easter-eggs',
				),
			);

			$images['000034.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'red',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/304119/heart-9',
				),
			);

			$images['000035.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'orange',
				'fonts'		=> array(
					'lato',
					'mountains-of-christmas',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/306877/broom-riding-witch-silhouette',
				),
			);

			$images['000036.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(),
				'sources'	=> array(
					'https://openclipart.org/detail/291736/black-friday-sign',
				),
			);

			$images['000037.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000038.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'pink',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/221719/price-tag',
				),
			);

			$images['000039.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/86689/plain-black-bat',
				),
			);

			$images['000040.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'mountains-of-christmas',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/288849/christmas-tree-silhouette',
				),
			);

			$images['000041.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'red',
				'fonts'		=> array(
					'calligraffiti',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/269956/cupid-silhouette',
				),
			);

			$images['000042.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'press-start-2p',
				),
				'sources'	=> array(),
			);

			$images['000043.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'white',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/199524/primary-folder-binary',
				),
			);

			$images['000044.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'gray',
				'fonts'		=> array(),
				'sources'	=> array(
					'https://openclipart.org/detail/228164/happy-halloween',
				),
			);

			$images['000045.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/90949/christmas-tree',
				),
			);

			$images['000046.svg'] = array(
				'type'		=> 'halloween',
				'color'		=> 'gray',
				'fonts'		=> array(
					'caveat-brush',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/91135/jack-o-lantern-randy',
				),
			);

			$images['000047.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'yellow',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000048.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'white',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000049.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000050.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'mountains-of-christmas',
				),
				'sources'	=> array(),
			);

			$images['000051.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'purple',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000052.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000053.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'orange',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000054.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000055.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'white',
				'fonts'		=> array(
					'lobster',
				),
				'sources'	=> array(),
			);

			$images['000056.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000057.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'red',
				'fonts'		=> array(
					'nothing-you-could-do',
				),
				'sources'	=> array(),
			);

			$images['000058.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'pink',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000059.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'purple',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000060.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000061.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/12591/alarm-clock',
				),
			);

			$images['000062.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'lobster',
				),
				'sources'	=> array(),
			);

			$images['000063.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'white',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000064.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'blue',
				'fonts'		=> array(
					'lobster',
				),
				'sources'	=> array(),
			);

			$images['000065.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000066.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'blue',
				'fonts'		=> array(
					'nothing-you-could-do',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/171043/valentines-day-gift-box',
				),
			);

			$images['000067.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000068.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'pink',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000069.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/237996/package',
				),
			);

			$images['000070.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000071.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'orange',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000072.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(
					'https://openclipart.org/detail/7315/flames',
				),
			);

			$images['000073.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000074.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000075.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000076.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000077.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000078.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000079.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000080.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000081.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lobster',
				),
				'sources'	=> array(),
			);

			$images['000082.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000083.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000084.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000085.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000086.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000087.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000088.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000089.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000090.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000091.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000092.svg'] = array(
				'type'		=> 'black-friday',
				'color'		=> 'black',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000093.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'black',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000094.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000095.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'pink',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000096.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'blue',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000097.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000098.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'black',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000099.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000100.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'green',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000101.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'pink',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000102.svg'] = array(
				'type'		=> 'cyber-monday',
				'color'		=> 'black',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000103.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000104.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000105.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'mountains-of-christmas',
				),
				'sources'	=> array(),
			);

			$images['000106.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000107.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000108.svg'] = array(
				'type'		=> 'christmas',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000109.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'purple',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000110.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'blue',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000111.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000112.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000113.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000114.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'pink',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000115.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'blue',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000116.svg'] = array(
				'type'		=> 'valentines',
				'color'		=> 'white',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000117.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'white',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000118.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'purple',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000119.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000120.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000121.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000122.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000123.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'lato',
				),
				'sources'	=> array(),
			);

			$images['000124.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'black',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000125.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000126.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000127.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000128.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'anton',
				),
				'sources'	=> array(),
			);

			$images['000129.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000130.svg'] = array(
				'type'		=> 'availability',
				'color'		=> 'red',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000131.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000132.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			$images['000133.svg'] = array(
				'type'		=> 'general',
				'color'		=> 'green',
				'fonts'		=> array(
					'roboto',
				),
				'sources'	=> array(),
			);

			return $images;

		}

	}

}
