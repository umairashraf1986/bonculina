<?php 

require_once 'class-wonderplugin-videoembed-model.php';
require_once 'class-wonderplugin-videoembed-view.php';
require_once 'class-wonderplugin-videoembed-widgetview.php';

class WonderPlugin_Videoembed_Controller {

	private $view, $model, $update;

	function __construct() {

		$this->model = new WonderPlugin_Videoembed_Model($this);	
		$this->view = new WonderPlugin_Videoembed_View($this);
		$this->widgetview = new WonderPlugin_Videoembed_Widgetview($this);		
	}
	
	function show_widgetfront($args, $instance) {
	
		$this->widgetview->show_widgetfront($args, $instance);
	}
	
	function show_widgetform($widget, $instance) {
	
		$this->widgetview->show_widgetform($widget, $instance);
	}
	
	function shortcode_handler($atts) {
		
		return $this->widgetview->shortcode_handler($atts);
	}
	
	function get_defaults() {
		
		return $this->widgetview->get_defaults();
	}
	
	function get_boolparams() {
		
		return $this->widgetview->get_boolparams();
	}
	
	function get_htmlparams() {
	
		return $this->widgetview->get_htmlparams();
	}
	
	function double_escape_html($str) {
	
		return $this->widgetview->double_escape_html($str);
	}
	
	function add_metaboxes()
	{
		$this->view->add_metaboxes();
	}
	
	function show_overview() {
		
		$this->view->print_overview();
	}
	
	function show_quick_start() {
		
		$this->view->print_quick_start();
	}
	
	function edit_settings()
	{
		$this->view->print_edit_settings();
	}
	
	function save_settings($options)
	{
		$this->model->save_settings($options);
	}
	
	function get_settings()
	{
		return $this->model->get_settings();
	}
	
	function register()
	{
		$this->view->print_register();
	}
	
	function check_license($options)
	{
		return $this->model->check_license($options);
	}
	
	function deregister_license($options)
	{
		return $this->model->deregister_license($options);
	}
	
	function save_plugin_info($info)
	{
		return $this->model->save_plugin_info($info);
	}
	
	function get_plugin_info()
	{
		return $this->model->get_plugin_info();
	}
	
	function get_update_data($action, $key)
	{
		return $this->update->get_update_data($action, $key);
	}
}
