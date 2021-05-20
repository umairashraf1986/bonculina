<?php
/*
Plugin Name: Insight Core
Description: Core functions for WordPress theme
Author: InsightStudio
Version: 1.6.5
Author URI: http://insightstud.io
Text Domain: insight-core
Domain Path: /languages/
*/
defined( 'ABSPATH' ) || exit;

$theme = wp_get_theme();
if ( ! empty( $theme['Template'] ) ) {
	$theme = wp_get_theme( $theme['Template'] );
}
define( 'INSIGHT_CORE_SITE_URI', site_url() );
define( 'INSIGHT_CORE_PATH', plugin_dir_url( __FILE__ ) );
define( 'INSIGHT_CORE_DIR', dirname( __FILE__ ) );
define( 'INSIGHT_CORE_DS', DIRECTORY_SEPARATOR );
define( 'INSIGHT_CORE_INC_DIR', INSIGHT_CORE_DIR . '/includes' );
define( 'INSIGHT_CORE_THEME_NAME', $theme['Name'] );
define( 'INSIGHT_CORE_THEME_SLUG', $theme['Template'] );
define( 'INSIGHT_CORE_THEME_VERSION', $theme['Version'] );
define( 'INSIGHT_CORE_THEME_DIR', get_template_directory() );
define( 'INSIGHT_CORE_THEME_URI', get_template_directory_uri() );

if ( ! class_exists( 'InsightCore' ) ) {
	class InsightCore {
		public static $info;

		protected static $instance = null;

		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function initialize() {
			$this->set_info();

			add_action( 'init', array( $this, 'load_textdomain' ), 99 );

			add_filter( 'widget_text', 'do_shortcode' );

			add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 12 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_action( 'wp_ajax_insight_core_patcher', array( $this, 'ajax_patcher' ) );

			add_action( 'do_meta_boxes', array( $this, 'remove_revolution_slider_meta_boxes' ) );

			add_filter( 'user_contactmethods', array( $this, 'add_extra_fields_for_contactmethods' ), 10, 1 );

			// Custom Functions
			include_once( INSIGHT_CORE_INC_DIR . '/functions.php' );

			// Register Posttypes
			include_once( INSIGHT_CORE_INC_DIR . '/register-posttypes.php' );

			// Register widgets
			include_once( INSIGHT_CORE_INC_DIR . '/register-widgets.php' );

			// Pages
			include_once( INSIGHT_CORE_INC_DIR . '/pages.php' );

			// TMG
			include_once( INSIGHT_CORE_INC_DIR . '/tgm-plugin-activation.php' );
			require_once( INSIGHT_CORE_INC_DIR . '/tgm-plugin-registration.php' );

			// Import & Export
			include_once( INSIGHT_CORE_INC_DIR . '/export/export.php' );
			include_once( INSIGHT_CORE_INC_DIR . '/import/import.php' );

			// Kirki
			include_once( INSIGHT_CORE_DIR . '/libs/kirki/kirki.php' );
			add_filter( 'kirki/config', array( $this, 'kirki_update_url' ) );

			// Update
			include_once( INSIGHT_CORE_INC_DIR . '/update/class-updater.php' );

			// Others
			include_once( INSIGHT_CORE_INC_DIR . '/customizer/io.php' );
			include_once( INSIGHT_CORE_INC_DIR . '/breadcrumb.php' );
			include_once( INSIGHT_CORE_INC_DIR . '/better-menu-widget.php' );

			// Dashboard
			include_once( INSIGHT_CORE_INC_DIR . '/dashboard/dashboard.php' );
		}

		public function set_info() {
			self::$info = array(
				'support' => 'https://thememove.ticksy.com/',
				'faqs'    => 'https://thememove.ticksy.com/articles/',
				'docs'    => 'https://document.thememove.com/',
				'api'     => 'https://api.insightstud.io/update/thememove/',
				'child'   => '',
				'icon'    => INSIGHT_CORE_PATH . '/assets/images/tm-icon.png',
				'desc'    => 'Thank you for using our theme, please reward it a full five-star &#9733;&#9733;&#9733;&#9733;&#9733; rating.',
				'tf'      => 'https://themeforest.net/user/thememove/portfolio',
			);
		}

		/**
		 * Add extra fields to Contact info section in edit profile page.
		 */
		public function add_extra_fields_for_contactmethods( $contactmethods ) {
			if ( get_theme_support( 'insight-user-social-networks' ) ) {

				$default = array(
					array(
						'name'  => 'email_address',
						'label' => esc_html__( 'Email Address', 'insight-core' ),
					),
					array(
						'name'  => 'facebook',
						'label' => esc_html__( 'Facebook', 'insight-core' ),
					),
					array(
						'name'  => 'twitter',
						'label' => esc_html__( 'Twitter', 'insight-core' ),
					),
					array(
						'name'  => 'instagram',
						'label' => esc_html__( 'Instagram', 'insight-core' ),
					),
					array(
						'name'  => 'linkedin',
						'label' => esc_html__( 'Linkedin', 'insight-core' ),
					),
					array(
						'name'  => 'pinterest',
						'label' => esc_html__( 'Pinterest', 'insight-core' ),
					),
				);

				$extra_fields = apply_filters( 'insight_core_user_contactmethods', $default );

				if ( ! empty ( $extra_fields ) ) {
					foreach ( $extra_fields as $field ) {
						if ( ! isset( $contactmethods[ $field['name'] ] ) ) {
							$contactmethods[ $field['name'] ] = $field['label'];
						}
					}
				}
			}

			return $contactmethods;
		}

		function load_textdomain() {
			load_plugin_textdomain( 'insight-core', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		public function after_setup_theme() {
			// Get info
			self::$info = apply_filters( 'insight_core_info', self::$info );

			// Detect
			require_if_theme_supports( 'insight-detect', INSIGHT_CORE_DIR . '/libs/mobile-detect/mobile.php' );

			// CMB2
			require_if_theme_supports( 'insight-cmb2', INSIGHT_CORE_DIR . '/libs/cmb2/init.php' );
			add_filter( 'cmb2_meta_box_url', array( $this, 'cmb2_meta_box_url' ) );

			// Kungfu Framework
			require_if_theme_supports( 'insight-kungfu', INSIGHT_CORE_DIR . '/libs/kungfu/kungfu-framework.php' );

			// Mega menu
			require_if_theme_supports( 'insight-megamenu', INSIGHT_CORE_INC_DIR . '/mega-menu/mega-menu.php' );

			// Popup
			require_if_theme_supports( 'insight-popup', INSIGHT_CORE_INC_DIR . '/popup/popup.php' );

			// Footer
			require_if_theme_supports( 'insight-footer', INSIGHT_CORE_INC_DIR . '/footer/footer.php' );

			// Share
			require_if_theme_supports( 'insight-share', INSIGHT_CORE_INC_DIR . '/share.php' );

			// View
			require_if_theme_supports( 'insight-view', INSIGHT_CORE_INC_DIR . '/view.php' );
		}

		public function admin_enqueue_scripts( $hook ) {
			if ( strpos( $hook, 'insight-core' ) !== false ) {
				wp_enqueue_style( 'hint', INSIGHT_CORE_PATH . 'assets/css/hint.css' );
				wp_enqueue_style( 'font-awesome', INSIGHT_CORE_PATH . 'assets/css/font-awesome.min.css' );
				wp_enqueue_style( 'pe-icon-7-stroke', INSIGHT_CORE_PATH . 'assets/css/pe-icon-7-stroke.css' );
				wp_enqueue_style( 'insight-core', INSIGHT_CORE_PATH . 'assets/css/insight-core.css' );
				wp_enqueue_script( 'insight-core', INSIGHT_CORE_PATH . 'assets/js/insight-core.js', array( 'jquery' ), INSIGHT_CORE_THEME_VERSION, true );
				wp_localize_script( 'insight-core', 'ic_vars', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'ic_nonce' => wp_create_nonce( 'ic_nonce' ),
				) );
			}
		}

		public function cmb2_meta_box_url() {
			return INSIGHT_CORE_PATH . '/libs/cmb2/';
		}

		public function kirki_update_url( $config ) {
			$config['url_path'] = INSIGHT_CORE_PATH . '/libs/kirki';

			return $config;
		}

		// Check theme support
		public static function is_theme_support() {
			if ( current_theme_supports( 'insight-core' ) ) {
				return true;
			} else {
				return false;
			}
		}

		// Check if has changelog file
		public function has_changelog() {
			$changelogs = get_transient( 'ic_changelogs' );

			if ( false === $changelogs ) {
				$request = wp_remote_get( self::$info['api'] . '/changelogs.json', array( 'timeout' => 120 ) );
				if ( ! is_wp_error( $request ) ) {
					$changelogs = json_decode( wp_remote_retrieve_body( $request ), true );
					set_transient( 'ic_changelogs', $changelogs, 24 * HOUR_IN_SECONDS );
				}
			}

			return $changelogs;
		}

		// Get changelogs file content and filter
		public function get_changelog( $table = true ) {
			$change_logs      = $this->has_changelog();
			$change_logs_html = '';

			if ( is_array( $change_logs ) && count( $change_logs ) > 0 ) {
				foreach ( $change_logs as $logkey => $logval ) {
					if ( $table ) {
						$change_logs_html .= '<tr>';
						$change_logs_html .= '<td>' . $logval['time'] . '</td>';
						$change_logs_html .= '<td>' . $logkey . '</td>';
						$change_logs_html .= '<td>';
						if ( is_array( $logval['desc'] ) ) {
							$change_logs_html .= implode( '<br/>', $logval["desc"] );
						} else {
							$change_logs_html .= $logval['desc'];
						}
						$change_logs_html .= '</td>';
						$change_logs_html .= '</tr>';
					} else {
						$change_logs_html .= '<h4>' . $logkey . ' - <span>' . $logval['time'] . '</span></h4>';
						$change_logs_html .= '<pre>';
						if ( is_array( $logval['desc'] ) ) {
							$change_logs_html .= implode( '<br/>', $logval['desc'] );
						} else {
							$change_logs_html .= $logval['desc'];
						}
						$change_logs_html .= '</pre>';
					}
				}

				if ( $table ) {
					$change_logs_html = '<table class="wp-list-table widefat striped table-change-logs changelogs"><thead><tr><th>' . esc_html__( 'Time', 'insight-core' ) . '</th><th>' . esc_html__( 'Version', 'insight-core' ) . '</th><th>' . esc_html( 'Note', 'insight-core' ) . '</th></tr></thead><tbody>' . $change_logs_html . '</tbody></table>';
				}
			} else {
				$change_logs_html = esc_html__( 'The changelogs not found!', 'insight-core' );
			}

			$change_logs_html = '<div class="changelogs-wrap">' . $change_logs_html . '</div>';

			$change_logs_html = apply_filters( 'insight_core_changelogs', $change_logs_html );

			return $change_logs_html;
		}

		// Check has patcher
		public static function check_theme_patcher() {
			if ( false === ( $patchers = get_transient( 'ic_patcher' ) ) ) {
				$request = wp_remote_get( self::$info['api'] . '/patcher.json', array( 'timeout' => 120 ) );
				if ( ! is_wp_error( $request ) ) {
					$patchers = json_decode( wp_remote_retrieve_body( $request ), true );
					set_transient( 'ic_patcher', $patchers, 24 * HOUR_IN_SECONDS );
				}
			}

			if ( isset( $patchers[ INSIGHT_CORE_THEME_VERSION ] ) && ( count( $patchers[ INSIGHT_CORE_THEME_VERSION ] ) > 0 ) ) {
				$patchers_status = (array) get_option( 'insight_core_patcher' );
				foreach ( $patchers[ INSIGHT_CORE_THEME_VERSION ] as $key => $value ) {
					if ( ! in_array( $key, $patchers_status ) ) {
						return true;
					}
				}
			}

			return false;
		}

		// Get patcher
		public static function get_patcher() {
			if ( false === ( $patchers = get_transient( 'ic_patcher' ) ) ) {
				$request = wp_remote_get( self::$info['api'] . '/patcher.json', array( 'timeout' => 120 ) );
				if ( ! is_wp_error( $request ) ) {
					$patchers = json_decode( wp_remote_retrieve_body( $request ), true );
					set_transient( 'ic_patcher', $patchers, 24 * HOUR_IN_SECONDS );
				}
			}

			return $patchers;
		}

		// AJAX patcher
		public function ajax_patcher() {
			if ( ! isset( $_POST['ic_nonce'] ) || ! wp_verify_nonce( $_POST['ic_nonce'], 'ic_nonce' ) ) {
				die( 'Permissions check failed!' );
			}
			$ic_patcher       = $_POST['ic_patcher'];
			$ic_patcher_url   = self::$info['api'] . '/' . $ic_patcher . '.zip';
			$ic_patcher_error = false;
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			WP_Filesystem();
			// create temp folder
			$_tmp = wp_tempnam( $ic_patcher_url );
			@unlink( $_tmp );
			@ob_flush();
			@flush();
			if ( is_writable( INSIGHT_CORE_THEME_DIR ) ) {
				$package = download_url( $ic_patcher_url, 18000 );
				$unzip   = unzip_file( $package, INSIGHT_CORE_THEME_DIR );
				if ( ! is_wp_error( $package ) ) {
					if ( ! is_wp_error( $unzip ) ) {
						self::update_option_array( 'insight_core_patcher', $ic_patcher );
					} else {
						$ic_patcher_error = true;
					}
				} else {
					$ic_patcher_error = true;
				}
			} else {
				$ic_patcher_error = true;
			}

			echo $ic_patcher_error ? 'Error' : 'Done';
			die();
		}

		// Check purchase code
		public static function check_purchase_code( $code ) {
			$request = wp_remote_get( 'https://api.insightstud.io/purchase/tf.php?code=' . $code, array( 'timeout' => 120 ) );
			$json    = json_decode( wp_remote_retrieve_body( $request ), true );

			return $json;
		}

		// Check theme update
		public function check_theme_update() {
			$update_data = array();
			$has_update  = false;
			if ( self::$info['api'] ) {
				if ( $updates = $this->has_changelog() ) {
					if ( is_array( $updates ) ) {
						foreach ( $updates as $ukey => $uval ) {
							if ( version_compare( $ukey, INSIGHT_CORE_THEME_VERSION ) == 1 ) {
								$update_data['new_version'] = $ukey;
								$update_data['package']     = self::$info['api'] . '/' . $ukey . '.zip';
								$update_data['time']        = $uval['time'];
								$update_data['desc']        = $uval['desc'];
								$has_update                 = true;
								break;
							}
						}
					}
				}
			}
			if ( $has_update ) {
				return $update_data;
			} else {
				return false;
			}
		}

		public static function check_valid_update() {
			if ( self::is_envato_hosted() ) {
				return true;
			}

			$can_update    = false;
			$purchase_code = get_option( 'insight_core_purchase_code' ); // Purchase code in database

			// Check purchase code still valid?
			$purchase_info = InsightCore::check_purchase_code( $purchase_code );
			if ( is_array( $purchase_info ) && count( $purchase_info ) > 0 ) {
				// Check item_id
				$tf      = explode( '/', self::$info['tf'] );
				$item_id = end( $tf );

				$p_item_id  = $purchase_info['item_id'];
				$can_update = ( $item_id == $p_item_id );
			}

			return $can_update;
		}

		// Update option count
		public static function update_option_count( $option ) {
			if ( get_option( $option ) != false ) {
				update_option( $option, get_option( $option ) + 1 );
			} else {
				update_option( $option, '1' );
			}
		}

		// Update option array
		public function update_option_array( $option, $value ) {
			if ( get_option( $option ) ) {
				$options = get_option( $option );
				if ( ! in_array( $value, $options ) ) {
					$options[] = $value;
					update_option( $option, $options );
				}
			} else {
				update_option( $option, array( $value ) );
			}
		}

		// Get action link for each plugin
		public static function plugin_action( $item ) {
			$installed_plugins        = get_plugins();
			$item['sanitized_plugin'] = $item['name'];
			$actions                  = array();
			// We have a repo plugin
			if ( ! $item['version'] ) {
				$item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
			}
			if ( ! isset( $installed_plugins[ $item['file_path'] ] ) ) {
				// Display install link
				$actions = sprintf( '<a href="%1$s" title="Install %2$s">Install</a>', esc_url( wp_nonce_url( add_query_arg( array(
					'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
					'plugin'        => urlencode( $item['slug'] ),
					'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
					'plugin_source' => urlencode( $item['source'] ),
					'tgmpa-install' => 'install-plugin',
				), TGM_Plugin_Activation::$instance->get_tgmpa_url() ), 'tgmpa-install', 'tgmpa-nonce' ) ), $item['sanitized_plugin'] );
			} elseif ( is_plugin_inactive( $item['file_path'] ) ) {
				// Display activate link
				$actions = sprintf( '<a href="%1$s" title="Activate %2$s">Activate</a>', esc_url( add_query_arg( array(
					'plugin'               => urlencode( $item['slug'] ),
					'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
					'plugin_source'        => urlencode( $item['source'] ),
					'tgmpa-activate'       => 'activate-plugin',
					'tgmpa-activate-nonce' => wp_create_nonce( 'tgmpa-activate' ),
				), admin_url( 'admin.php?page=insight-core' ) ) ), $item['sanitized_plugin'] );
			} elseif ( version_compare( $installed_plugins[ $item['file_path'] ]['Version'], $item['version'], '<' ) ) {
				// Display update link
				$actions = sprintf( '<a href="%1$s" title="Install %2$s">Update</a>', wp_nonce_url( add_query_arg( array(
					'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
					'plugin'        => urlencode( $item['slug'] ),
					'tgmpa-update'  => 'update-plugin',
					'plugin_source' => urlencode( $item['source'] ),
					'version'       => urlencode( $item['version'] ),
				), TGM_Plugin_Activation::$instance->get_tgmpa_url() ), 'tgmpa-update', 'tgmpa-nonce' ), $item['sanitized_plugin'] );
			} elseif ( is_plugin_active( $item['file_path'] ) ) {
				// Display deactivate link
				$actions = sprintf( '<a href="%1$s" title="Deactivate %2$s">Deactivate</a>', esc_url( add_query_arg( array(
					'plugin'                 => urlencode( $item['slug'] ),
					'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
					'plugin_source'          => urlencode( $item['source'] ),
					'tgmpa-deactivate'       => 'deactivate-plugin',
					'tgmpa-deactivate-nonce' => wp_create_nonce( 'tgmpa-deactivate' ),
				), admin_url( 'admin.php?page=insight-core' ) ) ), $item['sanitized_plugin'] );
			}

			return $actions;
		}

		// Remove Rev Slider Metabox
		public function remove_revolution_slider_meta_boxes() {
			remove_meta_box( 'mymetabox_revslider_0', 'page', 'normal' );
			remove_meta_box( 'mymetabox_revslider_0', 'post', 'normal' );
			remove_meta_box( 'mymetabox_revslider_0', 'ic_popup', 'normal' );
			remove_meta_box( 'mymetabox_revslider_0', 'ic_mega_menu', 'normal' );
		}

		public static function is_envato_hosted() {
			return ( defined( 'ENVATO_HOSTED_SITE' ) && defined( 'SUBSCRIPTION_CODE' ) );
		}

		/**
		 * @param $var
		 *
		 * Output anything in debug bar.
		 */
		public static function d( $var ) {
			if ( ! function_exists( 'kint_debug_ob' ) || ! class_exists( 'Debug_Bar' ) ) {
				return;
			}

			ob_start( 'kint_debug_ob' );
			d( $var );
			ob_end_flush();
		}

		/**
		 * @param mixed $log Anything to write to log.
		 *
		 * Make sure WP_DEBUG_LOG = true.
		 */
		public static function write_log( $log ) {
			if ( true === WP_DEBUG ) {
				if ( is_array( $log ) || is_object( $log ) ) {
					error_log( print_r( $log, true ) );
				} else {
					error_log( $log );
				}
			}
		}
	}

	InsightCore::instance()->initialize();

	function insight_core_activation_hook() {
		$pt_array = ( $pt_array = get_option( 'wpb_js_content_types' ) ) ? ( $pt_array ) : array( 'page' );

		if ( ! in_array( 'ic_mega_menu', $pt_array ) ) {
			$pt_array[] = 'ic_mega_menu';
		}

		if ( ! in_array( 'ic_footer', $pt_array ) ) {
			$pt_array[] = 'ic_footer';
		}

		if ( ! in_array( 'ic_popup', $pt_array ) ) {
			$pt_array[] = 'ic_popup';
		}

		// Update user roles
		$user_roles = get_option( 'wp_user_roles' );

		if ( ! empty( $user_roles ) ) {
			foreach ( $user_roles as $key => $value ) {
				$user_roles[ $key ]['capabilities']['vc_access_rules_post_types']              = 'custom';
				$user_roles[ $key ]['capabilities']['vc_access_rules_post_types/page']         = true;
				$user_roles[ $key ]['capabilities']['vc_access_rules_post_types/ic_mega_menu'] = true;
				$user_roles[ $key ]['capabilities']['vc_access_rules_post_types/ic_footer']    = true;
				$user_roles[ $key ]['capabilities']['vc_access_rules_post_types/ic_popup']     = true;
			}
		}

		update_option( 'wpb_js_content_types', $pt_array );
		update_option( 'wp_user_roles', $user_roles );
	}

	register_activation_hook( __FILE__, 'insight_core_activation_hook' );
}
