<?php
/*
Plugin Name: Wonder Video Embed 
Plugin URI: https://www.wonderplugin.com/wordpress-video-player/
Description: WordPress Video Embed Plugin & Widget
Version: 1.7
Author: Magic Hills Pty Ltd
Author URI: https://www.wonderplugin.com
*/

define('WONDERPLUGIN_VIDEOEMBED_VERSION', '1.7');
define('WONDERPLUGIN_VIDEOEMBED_URL', plugin_dir_url( __FILE__ ));
define('WONDERPLUGIN_VIDEOEMBED_PATH', plugin_dir_path( __FILE__ ));
define('WONDERPLUGIN_VIDEOEMBED_PLUGIN', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('WONDERPLUGIN_VIDEOEMBED_PLUGIN_VERSION', '1.7');

require_once 'app/class-wonderplugin-videoembed-controller.php';

class WonderPlugin_Videoembed_Plugin {
	
	function __construct() {
	
		$this->init();
	}
	
	public function init() {
		
		// init controller
		$this->wonderplugin_videoembed_controller = new WonderPlugin_Videoembed_Controller();
		
		add_action( 'admin_menu', array($this, 'register_menu') );

		add_shortcode( 'wonderplugin_video', array($this, 'shortcode_handler') );
		
		add_action( 'init', array($this, 'init_plugin') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_creator_script') );
		
		if ( is_admin() )
		{
			add_action( 'admin_init', array($this, 'admin_init_hook') );
		}
		
		add_action( 'widgets_init', array($this, 'init_widget'));
	}
	
	function register_menu()
	{
		$menu = add_menu_page(
				__('Wonder Video Embed', 'wonderplugin_videoembed'),
				__('Wonder Video Embed', 'wonderplugin_videoembed'),
				'manage_options',
				'wonderplugin_videoembed_overview',
				array($this, 'show_overview'),
				WONDERPLUGIN_VIDEOEMBED_URL . 'images/logo-16.png' );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_videoembed_overview',
				__('Overview', 'wonderplugin_videoembed'),
				__('Overview', 'wonderplugin_videoembed'),
				'manage_options',
				'wonderplugin_videoembed_overview',
				array($this, 'show_overview' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_videoembed_overview',
				__('Quick Start', 'wonderplugin_videoembed'),
				__('Quick Start', 'wonderplugin_videoembed'),
				'manage_options',
				'wonderplugin_videoembed_show_quick_start',
				array($this, 'show_quick_start' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
				
		$menu = add_submenu_page(
				'wonderplugin_videoembed_overview',
				__('Settings', 'wonderplugin_videoembed'),
				__('Settings', 'wonderplugin_videoembed'),
				'manage_options',
				'wonderplugin_videoembed_edit_settings',
				array($this, 'edit_settings' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
	}
	
	function init_plugin() 
	{
		$this->register_script();
		
		$this->init_mce_button();
	}
	
	function init_mce_button() 
	{
		
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
			return;
		
		if (get_user_option( 'rich_editing' ) == 'true') 
		{
			add_filter( 'mce_external_plugins', array($this, 'init_mce_script') );
			add_filter( 'mce_buttons', array($this, 'register_mce_button'));
		}
	}
	
	function register_mce_button($buttons)
	{
		array_push( $buttons, 'wpve_mce_button' );
		return $buttons;
	}
	
	function init_mce_script($plugin_array) 
	{
		$plugin_array['wpve_mce_button'] = WONDERPLUGIN_VIDEOEMBED_URL . 'app/wonderplugin-videoembed-mce.js';
		return $plugin_array;
	}
	
	function register_script()
	{		
		wp_register_script('wonderplugin-videoembed-creator-script', WONDERPLUGIN_VIDEOEMBED_URL . 'app/wonderplugin-videoembed-creator.js', array('jquery'), WONDERPLUGIN_VIDEOEMBED_VERSION, false);
		wp_register_style('wonderplugin-videoembed-creator-style', WONDERPLUGIN_VIDEOEMBED_URL . 'app/wonderplugin-videoembed-creator.css');
		
		wp_register_script('wonderplugin-videoembed-script', WONDERPLUGIN_VIDEOEMBED_URL . 'engine/wonderpluginvideoembed.js', array('jquery'), WONDERPLUGIN_VIDEOEMBED_VERSION, false);
		wp_register_style('wonderplugin-videoembed-admin-style', WONDERPLUGIN_VIDEOEMBED_URL . 'wonderpluginvideoembed.css');
	}
	
	function enqueue_script()
	{
		$addjstofooter = get_option( 'wonderplugin_videoembed_addjstofooter', 0 );
		if ($addjstofooter == 1)
		{
			wp_enqueue_script('wonderplugin-videoembed-script', false, array(), false, true);
		}
		else
		{
			wp_enqueue_script('wonderplugin-videoembed-script');
		}		
	}
	
	function enqueue_admin_script($hook)
	{
		wp_enqueue_script('post');
		wp_enqueue_media();
		wp_enqueue_script('wonderplugin-videoembed-script');
		wp_enqueue_style('wonderplugin-videoembed-admin-style');
	}
	
	function enqueue_admin_creator_script($hook)
	{
		wp_enqueue_media();
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style ('wp-jquery-ui-dialog');
		wp_enqueue_script('wonderplugin-videoembed-creator-script');
		wp_enqueue_style('wonderplugin-videoembed-creator-style');
		wp_localize_script('wonderplugin-videoembed-creator-script', 'WONDERPLUGIN_VIDEO_MCE_EDITOR', array(
				'pluginurl' => WONDERPLUGIN_VIDEOEMBED_URL, 
				'helpurl' => admin_url('admin.php?page=wonderplugin_videoembed_show_quick_start'), 
				'lightboxinstalled' => class_exists('WonderPlugin_Lightbox_Plugin'),
				'zindex' => get_option('wonderplugin_videoembed_zindex', '100102')
			));
	}
	
	function admin_init_hook()
	{	
		if ( !current_user_can('manage_options') )
			return;
		
		// add meta boxes
		$this->wonderplugin_videoembed_controller->add_metaboxes();
	}
	
	function show_overview() {
		
		$this->wonderplugin_videoembed_controller->show_overview();
	}

	function show_quick_start() {
	
		$this->wonderplugin_videoembed_controller->show_quick_start();
	}
	
	function edit_settings() {
	
		$this->wonderplugin_videoembed_controller->edit_settings();
	}
	
	function register() {
	
		$this->wonderplugin_videoembed_controller->register();
	}
	
	function init_widget() {
		
		register_widget( 'WonderPlugin_Videoembed_Widget' );
	}
	
	function show_widgetfront($args, $instance) {
		
		$this->wonderplugin_videoembed_controller->show_widgetfront($args, $instance);
	}
	
	function show_widgetform($widget, $instance) {
		
		$this->wonderplugin_videoembed_controller->show_widgetform($widget, $instance);
	}
	
	function get_defaults() {
		
		return $this->wonderplugin_videoembed_controller->get_defaults();
	}
	
	function get_boolparams() {
		
		return $this->wonderplugin_videoembed_controller->get_boolparams();
	}
	
	function get_htmlparams() {
	
		return $this->wonderplugin_videoembed_controller->get_htmlparams();
	}
	
	function double_escape_html($str) {
	
		return $this->wonderplugin_videoembed_controller->double_escape_html($str);
	}
	
	function shortcode_handler($atts) {
	
		return $this->wonderplugin_videoembed_controller->shortcode_handler($atts);
	}
}

class WonderPlugin_Videoembed_Widget extends WP_Widget {
	 	
	public function __construct(){
		 
		parent::__construct(
				'wonderplugin_videoembed_widget',
				__( 'Wonder Video Embed', 'wonderplugin_videoembed' ),
				array(
						'description' => __( 'Add a YouTube, Vimeo, Wistia, iFrame or MP4/WebM video.', 'wonderplugin_videoembed' )
				)
		);
	}

	public function widget( $args, $instance ) {
		
		echo $args['before_widget'];
		
		if (!empty($instance['title']))
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']). $args['after_title'];
		
		global $wonderplugin_videoembed_plugin;
		
		$wonderplugin_videoembed_plugin->show_widgetfront($args, $instance);
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		global $wonderplugin_videoembed_plugin;
		
		$wonderplugin_videoembed_plugin->show_widgetform($this, $instance);
	}

	public function update( $new_instance, $old_instance ) {

		global $wonderplugin_videoembed_plugin;
		$defaults = $wonderplugin_videoembed_plugin->get_defaults();
		
		$boolparams = $wonderplugin_videoembed_plugin->get_boolparams();
		foreach($boolparams as $key => $param)
			$new_instance[$param] = !empty($new_instance[$param]) ? 1 : 0;
		
		$htmlparams = $wonderplugin_videoembed_plugin->get_htmlparams();
		foreach($htmlparams as $key => $param)
		{
			if (!empty($new_instance[$param]))
				$new_instance[$param] = $wonderplugin_videoembed_plugin->double_escape_html($new_instance[$param]);
		}
		
		$new_instance = wp_parse_args((array) $new_instance, $defaults);
						
		$instance = $old_instance;
		
		foreach($new_instance as $key => $value)
			$instance[$key] = strip_tags( $new_instance[$key] );
		 		
		return $instance;
	}
}

/**
 * Init the plugin
 */
$wonderplugin_videoembed_plugin = new WonderPlugin_Videoembed_Plugin();

define('WONDERPLUGIN_VIDEOEMBED_VERSION_TYPE', 'L');
