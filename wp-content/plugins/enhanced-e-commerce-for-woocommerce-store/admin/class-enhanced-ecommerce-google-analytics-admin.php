<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 * @author     Chiranjiv Pathak <chiranjiv@tatvic.com>
 */
//require __DIR__ . '../includes/setup/CustomerClient.php';

class Enhanced_Ecommerce_Google_Analytics_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since      1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    protected $ga_id;
    protected $ga_LC;
    protected $ga_eeT;
    protected $customerClient;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')  {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $server_name = $_SERVER['SERVER_NAME'];
        $domain = $protocol.$server_name;
        $woo_country = $this->woo_country();
        $country = (!empty($woo_country)) ? $woo_country[0] : 'US';
        $currency_code = $this->get_currency_code();
        $timezone = get_option('timezone_string');
        //sigin with google
        $this->returnUrl = "estorenew.tatvic.com/tat_ee/ads-analytics-form.php?domain=".$domain."&country=".$country."&user_currency=".$currency_code."&timezone".$timezone;
        $this->accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : '';
        $this->refreshToken = isset($_GET['refresh_token']) ? $_GET['refresh_token'] : '';
        $this->email = isset($_GET['email']) ? $_GET['email'] : '';

        if(isset($_GET['property_id']) && isset($_GET['ads_id'])) {
            $data = unserialize(get_option('ee_options'));
            if(isset($data['ga_id']) && ($data['ga_id'] != $_GET['property_id'])) {
                $_POST['ga_id'] = $_GET['property_id'];
                $_POST['ads_id'] = $_GET['ads_id'];
            } else if(isset($data['ga_id']) && ($data['ga_id'] == $_GET['property_id'])) {
                if($_GET['ads_id'] != '') {
                    $_POST['ga_id'] = $_GET['property_id'];
                    $_POST['ads_id'] = $_GET['ads_id'];
                } else {
                    $_POST['ga_id'] = $_GET['property_id'];
                }
            } else {
                $_POST['ga_id'] = $_GET['property_id'];
                $_POST['ads_id'] = $_GET['ads_id'];
            }

            Enhanced_Ecommerce_Google_Settings::update_analytics_options('ee_options');
        }

        $this->url = "https://estorenew.tatvic.com/tat_ee/ga_rdr_ee.php?return_url=" . $this->returnUrl;
    }

    /**
     * @return array
     * Get woocommerce default set country
     */
    private function woo_country(){
        // The country/state
        $store_raw_country = get_option( 'woocommerce_default_country' );
        // Split the country/state
        $split_country = explode( ":", $store_raw_country );
        return $split_country;
    }
    /**
     * @return mixed
     */
    private function get_currency_code(){
        $woo_country = $this->woo_country();
        $country = (!empty($woo_country)) ? $woo_country[0] : 'US';
        $getCurrency = file_get_contents(ENHANCAD_PLUGIN_DIR . 'includes/json/currency.json');
        $contData = json_decode($getCurrency);
        return $contData->{$country};
    }
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_enhanced-ecommerce-google-analytics-admin-display' || (isset($_GET['page']) && $_GET['page'] == 'enhanced-ecommerce-google-analytics-admin-display')) {
            wp_register_style('font_awesome', '//use.fontawesome.com/releases/v5.0.13/css/all.css');
            wp_enqueue_style('font_awesome');
            wp_register_style('aga_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
            wp_enqueue_style('aga_bootstrap');
            wp_register_style('aga_confirm', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css');
            wp_enqueue_style('aga_confirm');
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/enhanced-ecommerce-google-analytics-admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_enhanced-ecommerce-google-analytics-admin-display' || (isset($_GET['page']) && $_GET['page'] == 'enhanced-ecommerce-google-analytics-admin-display')) {
            wp_register_script('popper_bootstrap', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
            wp_enqueue_script('popper_bootstrap');
            wp_register_script('aga_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js');
            wp_enqueue_script('aga_bootstrap');
            wp_register_script('aga_confirm_js', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js');
            wp_enqueue_script('aga_confirm_js');
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/enhanced-ecommerce-google-analytics-admin.js', array('jquery'), $this->version, false);
        }
    }

    /**
     * Display Admin Page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        add_menu_page(
                'Tatvic EE Plugin', 'Tatvic EE Plugin', 'manage_options', "enhanced-ecommerce-google-analytics-admin-display", array($this, 'showPage'), plugin_dir_url(__FILE__) . 'images/tatvic_logo.png', 26
        );
    }
    /**
     * Display Tab page.
     *
     * @since    1.0.0
     */
    public function showPage() {
        require_once( 'partials/enhanced-ecommerce-google-analytics-admin-display.php');
        if (!empty($_GET['tab'])) {
            $get_action = $_GET['tab'];
        } else {
            $get_action = "general_settings";
        }
        if (method_exists($this, $get_action)) {
            $this->$get_action();
        }
    }

    public function general_settings() {
        require_once( 'partials/general-fields.php');
    }

    public function conversion_tracking() {
        require_once( 'partials/conversion-tracking.php');
    }

    public function google_optimize() {
        require_once( 'partials/google-optimize.php');
    }

    public function about_plugin() {
        require_once( 'partials/about-plugin.php');
    }
    public function country_location() {
        // date function to hide 30% off sale after certain date
        return date_default_timezone_set('Australia/Sydney'); // Change this depending on what timezone your in
    }

    public function today() {
        $this->country_location();
        return strtotime(date('Y-m-d'));
    }

    public function current_time() {
        $this->country_location();
        return strtotime(date('h:i A'));
    }

    public function start_date() {
        $this->country_location();
        return strtotime(date('Y') . '-09-01');
    }

    public function end_date() {
        $this->country_location();
        return strtotime(date('Y') . '-09-08');
    }

    public function end_time() {
        $this->country_location();
        return strtotime('11:59 PM');
    }
}
