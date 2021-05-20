<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Kungfu_Framework_Testimonial' ) ) {
	class Kungfu_Framework_Testimonial {
		function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ), 1 );
		}

		function register_post_types() {

			$slug = apply_filters( 'insight_core_testimonial_slug', 'testimonial' );

			$labels = array(
				'name'               => __( 'Testimonials', KFF_TEXTDOMAIN ),
				'singular_name'      => __( 'Testimonial Item', KFF_TEXTDOMAIN ),
				'view_item'          => __( 'View Testimonials', KFF_TEXTDOMAIN ),
				'add_new_item'       => __( 'Add New Testimonial', KFF_TEXTDOMAIN ),
				'add_new'            => _x( 'Add New', KFF_TEXTDOMAIN ),
				'new_item'           => __( 'Add New Testimonial Item', KFF_TEXTDOMAIN ),
				'edit_item'          => __( 'Edit Testimonial Item', KFF_TEXTDOMAIN ),
				'update_item'        => __( 'Update Testimonial', KFF_TEXTDOMAIN ),
				'all_items'          => __( 'All Testimonials', KFF_TEXTDOMAIN ),
				'parent_item_colon'  => __( 'Parent Testimonial Item:', KFF_TEXTDOMAIN ),
				'search_items'       => __( 'Search Testimonial', KFF_TEXTDOMAIN ),
				'not_found'          => __( 'No testimonial items found', KFF_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No testimonial items found in trash', KFF_TEXTDOMAIN )
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
				'testimonial',
				array(
					'labels'             => $labels,
					'supports'           => $supports,
					'public'             => false,
					'has_archive'        => false,
					'can_export'         => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'publicly_queryable' => false,
					'rewrite'            => false,
					'menu_icon'          => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-format-quote' : false,
				)
			);

			register_taxonomy(
				'testimonial_category',
				'testimonial',
				array(
					'hierarchical'      => false,
					'label'             => __( 'Categories', KFF_TEXTDOMAIN ),
					'query_var'         => false,
					'rewrite'           => false,
					'show_admin_column' => true,
				)
			);
		}
	}

	new Kungfu_Framework_Testimonial;
}