<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Kungfu_Framework_Project' ) ) {
	class Kungfu_Framework_Project {
		function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ), 1 );
		}

		function register_post_types() {

			$slug = apply_filters( 'insight_core_project_slug', 'project' );

			$labels = array(
				'name'               => __( 'Projects', KFF_TEXTDOMAIN ),
				'singular_name'      => __( 'Project Item', KFF_TEXTDOMAIN ),
				'view_item'          => __( 'View Projects', KFF_TEXTDOMAIN ),
				'add_new_item'       => __( 'Add New Project', KFF_TEXTDOMAIN ),
				'add_new'            => _x( 'Add New', KFF_TEXTDOMAIN ),
				'new_item'           => __( 'Add New Project Item', KFF_TEXTDOMAIN ),
				'edit_item'          => __( 'Edit Project Item', KFF_TEXTDOMAIN ),
				'update_item'        => __( 'Update Project', KFF_TEXTDOMAIN ),
				'all_items'          => __( 'All Projects', KFF_TEXTDOMAIN ),
				'parent_item_colon'  => __( 'Parent Project Item:', KFF_TEXTDOMAIN ),
				'search_items'       => __( 'Search Project', KFF_TEXTDOMAIN ),
				'not_found'          => __( 'No project items found', KFF_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No project items found in trash', KFF_TEXTDOMAIN )
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
				'project',
				array(
					'labels'      => $labels,
					'supports'    => $supports,
					'public'      => true,
					'has_archive' => true,
					'rewrite'     => array(
						'slug' => $slug
					),
					'can_export'  => true,
					'menu_icon'   => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-portfolio' : false,
				)
			);

			register_taxonomy(
				'project_category',
				'project',
				array(
					'hierarchical'      => true,
					'label'             => __( 'Categories', KFF_TEXTDOMAIN ),
					'query_var'         => true,
					'rewrite'           => true,
					'show_admin_column' => true,
				)
			);
		}
	}

	new Kungfu_Framework_Project;
}