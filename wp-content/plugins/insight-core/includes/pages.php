<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_action( 'admin_menu', 'insight_core_admin_menu' );

function insight_core_admin_menu() {
	$last_update           = get_site_transient( 'update_themes' );
	$new_version_available = isset( $last_update->response[ INSIGHT_CORE_THEME_SLUG ] ) && ( version_compare( $last_update->response[ INSIGHT_CORE_THEME_SLUG ]['new_version'], INSIGHT_CORE_THEME_VERSION ) == 1 );
	$update_count          = 1;

	if ( $new_version_available && InsightCore::check_theme_patcher() ) {
		$update_count = 2;
	}

	add_menu_page(
		'Insight Core',
		( $new_version_available || ( InsightCore::check_theme_patcher() != false ) ? sprintf( 'Insight Core %s', '<span class="update-plugins"><span class="plugin-count">' . $update_count . '</span></span>' ) : 'Insight Core' ),
		'manage_options',
		'insight-core',
		'insight_core_welcome',
		INSIGHT_CORE_PATH . '/assets/images/icon.png',
		6
	);
	add_submenu_page( 'insight-core', 'Welcome', 'Welcome', 'manage_options', 'insight-core' );
	add_submenu_page( 'insight-core', 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
	add_submenu_page( 'insight-core', 'Update', ( $new_version_available || ( InsightCore::check_theme_patcher() != false ) ? sprintf( 'Update %s', '<span class="update-plugins"><span class="plugin-count">' . $update_count . '</span></span>' ) : 'Update' ), 'manage_options', 'insight-core-update', 'insight_core_update' );
	add_submenu_page( 'insight-core', 'System', 'System', 'manage_options', 'insight-core-system', 'insight_core_system' );

	if ( ! empty( InsightCore::$info['child'] ) ) {
		add_submenu_page( 'insight-core', 'Child Theme', 'Child Theme', 'manage_options', 'insight-core-child', 'insight_core_child' );
	}
}

function insight_core_welcome() {
	include_once( INSIGHT_CORE_INC_DIR . '/pages-welcome.php' );
}

function insight_core_update() {
	include_once( INSIGHT_CORE_INC_DIR . '/pages-update.php' );
}

function insight_core_system() {
	include_once( INSIGHT_CORE_INC_DIR . '/pages-system.php' );
}

function insight_core_child() {
	include_once( INSIGHT_CORE_INC_DIR . '/pages-child.php' );
}
