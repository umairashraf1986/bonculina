<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Kungfu_Service' ) ) {
	class Kungfu_Service {

		function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ), 1 );
		}

		function register_post_types() {

			$labels = array(
				'name'                  => esc_html__( 'Services', 'insight-core' ),
				'singular_name'         => esc_html__( 'Service', 'insight-core' ),
				'all_items'             => esc_html__( 'All Services', 'insight-core' ),
				'menu_name'             => _x( 'Services', 'Admin menu name', 'insight-core' ),
				'add_new'               => esc_html__( 'Add New', 'insight-core' ),
				'add_new_item'          => esc_html__( 'Add new service', 'insight-core' ),
				'edit'                  => esc_html__( 'Edit', 'insight-core' ),
				'edit_item'             => esc_html__( 'Edit service', 'insight-core' ),
				'new_item'              => esc_html__( 'New service', 'insight-core' ),
				'view'                  => esc_html__( 'View service', 'insight-core' ),
				'view_item'             => esc_html__( 'View service', 'insight-core' ),
				'search_items'          => esc_html__( 'Search services', 'insight-core' ),
				'not_found'             => esc_html__( 'No services found', 'insight-core' ),
				'not_found_in_trash'    => esc_html__( 'No services found in trash', 'insight-core' ),
				'parent'                => esc_html__( 'Parent service', 'insight-core' ),
				'featured_image'        => esc_html__( 'Service image', 'insight-core' ),
				'set_featured_image'    => esc_html__( 'Set service image', 'insight-core' ),
				'remove_featured_image' => esc_html__( 'Remove service image', 'insight-core' ),
				'use_featured_image'    => esc_html__( 'Use as service image', 'insight-core' ),
				'uploaded_to_this_item' => esc_html__( 'Uploaded to this service', 'insight-core' ),
				'filter_items_list'     => esc_html__( 'Filter services', 'insight-core' ),
				'items_list_navigation' => esc_html__( 'Services navigation', 'insight-core' ),
				'items_list'            => esc_html__( 'Service list', 'insight-core' ),
			);

			$supports = array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'comments',
				'author',
				'revisions',
				'custom-fields'
			);

			register_post_type(
				'service',
				apply_filters( 'insight_core_register_post_type_service', array(
					'labels'      => $labels,
					'supports'    => $supports,
					'public'      => true,
					'has_archive' => true,
					'rewrite'     => array(
						'slug' => apply_filters( 'insight_core_service_slug', 'service' )
					),
					'can_export'  => true,
					'menu_icon'   => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-portfolio' : false,
				) )
			);

			register_taxonomy(
				'service_category',
				'service',
				apply_filters( 'insight_core_taxonomy_args_service_category', array(
					'hierarchical'      => true,
					'label'             => __( 'Categories', 'insight-core' ),
					'labels'            => array(
						'name'              => _x( 'Service Categories', 'taxonomy general name', 'insight-core' ),
						'singular_name'     => _x( 'Category', 'taxonomy singular name', 'insight-core' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'insight-core' ),
						'search_items'      => esc_html__( 'Search categories', 'insight-core' ),
						'all_items'         => esc_html__( 'All categories', 'insight-core' ),
						'parent_item'       => esc_html__( 'Parent category', 'insight-core' ),
						'parent_item_colon' => esc_html__( 'Parent category:', 'insight-core' ),
						'edit_item'         => esc_html__( 'Edit category', 'insight-core' ),
						'update_item'       => esc_html__( 'Update category', 'insight-core' ),
						'add_new_item'      => esc_html__( 'Add new category', 'insight-core' ),
						'new_item_name'     => esc_html__( 'New category name', 'insight-core' ),
						'not_found'         => esc_html__( 'No categories found', 'insight-core' ),
					),
					'show_ui'           => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => apply_filters( 'insight_core_service_category_slug', 'service-category' ) ),
					'show_admin_column' => true,
				) )
			);
		}
	}

	new Kungfu_Service;
}