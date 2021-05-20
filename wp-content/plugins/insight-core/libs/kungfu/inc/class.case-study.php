<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Kungfu_Case_Study' ) ) {
	class Kungfu_Case_Study {

		function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ), 1 );
		}

		function register_post_types() {

			$labels = array(
				'name'                  => esc_html__( 'Case Studies', 'insight-core' ),
				'singular_name'         => esc_html__( 'Case Study', 'insight-core' ),
				'all_items'             => esc_html__( 'All Case Studies', 'insight-core' ),
				'menu_name'             => _x( 'Case Studies', 'Admin menu name', 'insight-core' ),
				'add_new'               => esc_html__( 'Add New', 'insight-core' ),
				'add_new_item'          => esc_html__( 'Add new case study', 'insight-core' ),
				'edit'                  => esc_html__( 'Edit', 'insight-core' ),
				'edit_item'             => esc_html__( 'Edit case study', 'insight-core' ),
				'new_item'              => esc_html__( 'New case study', 'insight-core' ),
				'view'                  => esc_html__( 'View case study', 'insight-core' ),
				'view_item'             => esc_html__( 'View case study', 'insight-core' ),
				'search_items'          => esc_html__( 'Search case studies', 'insight-core' ),
				'not_found'             => esc_html__( 'No case studies found', 'insight-core' ),
				'not_found_in_trash'    => esc_html__( 'No case studies found in trash', 'insight-core' ),
				'parent'                => esc_html__( 'Parent case study', 'insight-core' ),
				'featured_image'        => esc_html__( 'Case Study image', 'insight-core' ),
				'set_featured_image'    => esc_html__( 'Set case study image', 'insight-core' ),
				'remove_featured_image' => esc_html__( 'Remove case study image', 'insight-core' ),
				'use_featured_image'    => esc_html__( 'Use as case study image', 'insight-core' ),
				'uploaded_to_this_item' => esc_html__( 'Uploaded to this case study', 'insight-core' ),
				'filter_items_list'     => esc_html__( 'Filter case studies', 'insight-core' ),
				'items_list_navigation' => esc_html__( 'Case studies navigation', 'insight-core' ),
				'items_list'            => esc_html__( 'Case study list', 'insight-core' ),
			);

			$supports = array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'comments',
				'author',
				'revisions',
				'custom-fields',
			);

			register_post_type(
				'case_study',
				apply_filters( 'insight_core_register_post_type_case_study', array(
					'labels'      => $labels,
					'supports'    => $supports,
					'public'      => true,
					'has_archive' => true,
					'rewrite'     => array(
						'slug' => apply_filters( 'insight_core_case_study_slug', 'case_study' ),
					),
					'can_export'  => true,
					'menu_icon'   => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-portfolio' : false,
				) )
			);

			register_taxonomy(
				'case_study_category',
				'case_study',
				apply_filters( 'insight_core_taxonomy_args_case_study_category', array(
					'hierarchical'      => true,
					'label'             => __( 'Categories', 'insight-core' ),
					'labels'            => array(
						'name'              => _x( 'Case Study Categories', 'taxonomy general name', 'insight-core' ),
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
					'rewrite'           => array( 'slug' => apply_filters( 'insight_core_case_study_category_slug', 'case-study-category' ) ),
					'show_admin_column' => true,
				) )
			);

			register_taxonomy( 'case_study_tags', 'case_study', apply_filters( 'insight_core_taxonomy_args_case_study_tags', array(
				'hierarchical'      => false,
				'label'             => esc_html__( 'Tags', 'insight-core' ),
				'labels'            => array(
					'name' => _x( 'Case Study Tags', 'taxonomy general name', 'insight-core' ),
				),
				'query_var'         => true,
				'rewrite'           => array( 'slug' => apply_filters( 'insight_core_case_study_tag_slug', 'case-study-tag' ) ),
				'show_admin_column' => true,
			) ) );
		}
	}

	new Kungfu_Case_Study;
}
