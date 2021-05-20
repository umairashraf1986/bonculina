<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package BonCulina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php $viewport_content = apply_filters( 'bonculina_viewport_content', 'width=device-width, initial-scale=1' ); ?>
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>

	<!-- Start of Zendesk Widget script -->
	<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=55ba4ff1-cf23-4b1f-8c0d-dd2b7217aa74"> </script>
	<!-- End of Zendesk Widget script -->
	<style type="text/css">
		@font-face { 
			font-family:FS Lucas Bold;
			src:url(<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/FSLucas-Bold.otf) format('opentype');
			font-display: swap;
		}
		@font-face { 
			font-family:FS Lucas Medium;
			src:url(<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/FSLucas-Medium.otf) format('opentype');
			font-display: auto;
		}
		@font-face { 
			font-family:FS Lucas Regular;
			src:url(<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/FSLucas-Regular.otf) format('opentype');
			font-display: auto;
		}
		@font-face { 
			font-family:FS Lucas SemiBold;
			src:url(<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/FSLucas-SemiBold.otf) format('opentype');
			font-display: swap;
		}
	</style>
</head>
<body <?php body_class(); ?>>

<?php
bonculina_body_open();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	get_template_part( 'template-parts/header' );
}
