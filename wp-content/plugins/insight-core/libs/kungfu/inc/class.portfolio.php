<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Kungfu_Portfolio' ) ) {
	class Kungfu_Portfolio {

		function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ), 1 );
		}

		function register_post_types() {

			$slug = apply_filters( 'insight_core_portfolio_slug', 'portfolio' );

			$labels = array(
				'name'                  => esc_html__( 'Portfolios', 'insight-core' ),
				'singular_name'         => esc_html__( 'Portfolio', 'insight-core' ),
				'all_items'             => esc_html__( 'All Portfolios', 'insight-core' ),
				'menu_name'             => _x( 'Portfolios', 'Admin menu name', 'insight-core' ),
				'add_new'               => esc_html__( 'Add New', 'insight-core' ),
				'add_new_item'          => esc_html__( 'Add new portfolio', 'insight-core' ),
				'edit'                  => esc_html__( 'Edit', 'insight-core' ),
				'edit_item'             => esc_html__( 'Edit portfolio', 'insight-core' ),
				'new_item'              => esc_html__( 'New portfolio', 'insight-core' ),
				'view'                  => esc_html__( 'View portfolio', 'insight-core' ),
				'view_item'             => esc_html__( 'View portfolio', 'insight-core' ),
				'search_items'          => esc_html__( 'Search portfolios', 'insight-core' ),
				'not_found'             => esc_html__( 'No portfolios found', 'insight-core' ),
				'not_found_in_trash'    => esc_html__( 'No portfolios found in trash', 'insight-core' ),
				'parent'                => esc_html__( 'Parent portfolio', 'insight-core' ),
				'filter_items_list'     => esc_html__( 'Filter portfolios', 'insight-core' ),
				'items_list_navigation' => esc_html__( 'Portfolios navigation', 'insight-core' ),
				'items_list'            => esc_html__( 'Portfolio list', 'insight-core' ),
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

			register_post_type( 'portfolio', array(
				'labels'      => $labels,
				'supports'    => $supports,
				'public'      => true,
				'has_archive' => true,
				'rewrite'     => array(
					'slug' => $slug,
				),
				'can_export'  => true,
				'menu_icon'   => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-portfolio' : false,
			) );

			//flush_rewrite_rules( false );

			register_taxonomy( 'portfolio_category', 'portfolio', apply_filters( 'insight_core_taxonomy_args_portfolio_category', array(
				'hierarchical'      => true,
				'label'             => esc_html__( 'Categories', 'insight-core' ),
				'labels'            => array(
					'name' => _x( 'Portfolio Categories', 'taxonomy general name', 'insight-core' ),
				),
				'query_var'         => true,
				'rewrite'           => array( 'slug' => apply_filters( 'insight_core_portfolio_category_slug', 'portfolio-category' ) ),
				'show_admin_column' => true,
			) ) );

			register_taxonomy( 'portfolio_tags', 'portfolio', apply_filters( 'insight_core_taxonomy_args_portfolio_tags', array(
				'hierarchical'      => false,
				'label'             => esc_html__( 'Tags', 'insight-core' ),
				'labels'            => array(
					'name' => _x( 'Portfolio Tags', 'taxonomy general name', 'insight-core' ),
				),
				'query_var'         => true,
				'rewrite'           => array( 'slug' => apply_filters( 'insight_core_portfolio_tags_slug', 'portfolio-tags' ) ),
				'show_admin_column' => true,
			) ) );
		}
	}

	new Kungfu_Portfolio;
}
