<?php
/**
 * Plugin Name: WooCommerce Delivery Area Pro Extension
 * Description: Custom plugin to extend WooCommerce Delivery Area Pro plugin functionality
 * Version: 1.0.0
 * Author: Ciklum Pvt. Ltd.
 * Author URI: https://www.ciklum.com/
 * License: proprietary
 */

if ( is_plugin_active('woocommerce-delivery-area-pro/woocommerce-delivery-area-pro.php') && ! class_exists( 'WDAP_Delivery_Area' ) ) {
    $pluginClass = plugin_dir_path( __DIR__ ) . 'woocommerce-delivery-area-pro/woocommerce-delivery-area-pro.php';
    if ( file_exists( $pluginClass ) ) {
        include( $pluginClass );
    }
}

if( ! class_exists( 'WDAP_Delivery_Area_Extension' ) && class_exists('WDAP_Delivery_Area') ) {
    /**
     * Class acts as a natural name-space
     */
    class WDAP_Delivery_Area_Extension extends WDAP_Delivery_Area {

        private $collections;
        private $dboptions;

        public function __construct() {
            add_action( 'init', array( $this, 'wpdap_get_collections' ) );
            remove_all_actions('wp_ajax_wdap_ajax_call');
            remove_all_actions('wp_ajax_nopriv_wdap_ajax_call');
            add_action( 'wp_ajax_wdap_ajax_call', array($this, 'wdap_ajax_call_parent') );
            add_action( 'wp_ajax_nopriv_wdap_ajax_call', array($this, 'wdap_ajax_call_parent') );

            $this->dboptions = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'load_plugin_frontend_resources' ) );
        }

        function load_plugin_frontend_resources() {
            wp_deregister_script( 'wdap-frontend.js' );
            wp_enqueue_script('wdap-frontend.js', plugin_dir_url( __FILE__ ) . 'assets/scripts/wdap-frontend.js', array(), true);
            if ( method_exists( $this, 'frontend_script_localisation' ) ) {
                 $this->frontend_script_localisation();
            }
        }

        function frontend_script_localisation() {

            global $post;
            $wdap_js_lang = $this->wdap_localisation_parameter();
            wp_localize_script( 'wdap-frontend.js', 'wdap_settings_obj', $wdap_js_lang );

        }

        function wdap_localisation_parameter() {

            global $post;
            $wdap_js_lang = array();
            $wdap_js_lang['ajax_url'] = admin_url( 'admin-ajax.php' );
            $wdap_js_lang['nonce'] = wp_create_nonce( 'wdap-call-nonce' );
            $wdap_js_lang['exclude_countries'] = apply_filters( 'wdap_exclude_countries', array() );
            $wdap_js_lang['marker_country_restrict'] = apply_filters( 'wdap_enable_marker_country_restrict', true );
            $wdap_js_lang['is_api_key'] = ! empty( $this->dboptions['wdap_googleapikey'] ) ? 'yes' : '';
            $range_circle_ui = array(
                            'strokeColor'=>'#FF0000',
                            'strokeOpacity'=>1,
                            'strokeWeight'=>1,
                            'fillColor'=>'#FF0000',
                            'fillOpacity'=>0.5,
                        );

            $wdap_js_lang['range_circle_ui'] = apply_filters('range_circle_ui',$range_circle_ui);
            if ( ! empty( $this->dboptions['wdap_country_restriction_listing'] ) && isset( $this->dboptions['enable_places_to_retrict_country_only'] ) && ( $this->dboptions['enable_places_to_retrict_country_only'] == 'true' ) ) {
                $wdap_js_lang['autosuggest_country_restrict'] = $this->dboptions['wdap_country_restriction_listing'][0];
            }

            if ( ! empty( $this->dboptions['wdap_country_restriction_listing'] ) && isset( $this->dboptions['restrict_places_of_country_checkout'] ) && ( $this->dboptions['restrict_places_of_country_checkout'] == 'true' ) ) {
                $wdap_js_lang['autosuggest_country_restrict_checkout'] = $this->dboptions['wdap_country_restriction_listing'][0];
            }

            if ( is_checkout() ) {

                $wdap_js_lang['wdap_checkout_avality_method'] = ! empty( $this->dboptions['wdap_checkout_avality_method'] ) ? $this->dboptions['wdap_checkout_avality_method'] : '';

                $is_shipping = ( ! empty( $this->dboptions['wdap_checkout_avality_method'] ) && $this->dboptions['wdap_checkout_avality_method'] == 'via_shipping' ) ? true : false;

                $is_billing = ( ! empty( $this->dboptions['wdap_checkout_avality_method'] ) && $this->dboptions['wdap_checkout_avality_method'] == 'via_billing' ) ? true : false;

                $is_shipping_address = ( $is_shipping && ( ( ! empty( $this->dboptions['wdap_checkout_avality_shipping'] ) ) && $this->dboptions['wdap_checkout_avality_shipping'] == 'via_address' ) ) ? true : false;

                $is_billing_address = ( $is_billing && ( ( ! empty( $this->dboptions['wdap_checkout_avality_billing'] ) ) && $this->dboptions['wdap_checkout_avality_billing'] == 'via_address' ) ) ? true : false;

                $is_shipping_zipcode = ( $is_shipping && ( ( ! empty( $this->dboptions['wdap_checkout_avality_shipping'] ) ) && $this->dboptions['wdap_checkout_avality_shipping'] == 'via_zipcode' ) ) ? true : false;

                $is_billing_zipcode = ( $is_billing && ( ( ! empty( $this->dboptions['wdap_checkout_avality_billing'] ) ) && $this->dboptions['wdap_checkout_avality_billing'] == 'via_zipcode' ) ) ? true : false;

                if ( $is_shipping_address || $is_billing_address ) {
                    $wdap_js_lang['wdap_checkout_avality_method'] = 'via_address';
                }
                if ( $is_shipping_zipcode || $is_billing_zipcode ) {
                    $wdap_js_lang['wdap_checkout_avality_method'] = 'via_zipcode';
                }
            }

            if ( $this->dboptions ) {

                $wdap_js_lang['mapsettings']['zoom']      = ! empty( $this->dboptions['wdap_map_zoom_level'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_zoom_level'] ) ) : '';
                
                $wdap_js_lang['mapsettings']['centerlat'] = ! empty( $this->dboptions['wdap_map_center_lat'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_center_lat'] ) ) : '';
                
                $wdap_js_lang['mapsettings']['sicon_url'] =  !empty($this->dboptions['marker_img']) ?$this->dboptions['marker_img'] : WDAP_IMAGES . '/pin_blue.png';

                $wdap_js_lang['mapsettings']['centerlng'] = ! empty( $this->dboptions['wdap_map_center_lng'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_center_lng'] ) ) : '';
                $wdap_js_lang['mapsettings']['style']     = ! empty( $this->dboptions['wdap_map_style'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_style'] ) ) : '';
                $wdap_js_lang['mapsettings']['enable_restrict']     = ! empty( $this->dboptions['enable_retrict_country'] ) ? true : '';
                if ( ! empty( $this->dboptions['enable_markers_on_map'] ) ) {
                    $wdap_js_lang['mapsettings']['enable_markers_on_map'] = ! empty( $this->dboptions['enable_markers_on_map'] ) ? $this->dboptions['enable_markers_on_map'] : 'no';
                } elseif ( WDAP_VERSION == '1.0.3' ) {
                    $wdap_js_lang['mapsettings']['enable_markers_on_map'] = true;
                }
                if ( ! empty( $this->dboptions['enable_map_bound'] ) ) {
                    $wdap_js_lang['mapsettings']['enable_bound']     = ! empty( $this->dboptions['enable_map_bound'] ) ? $this->dboptions['enable_map_bound'] : 'no';
                } elseif ( WDAP_VERSION == '1.0.3' ) {
                    $wdap_js_lang['mapsettings']['enable_map_bound'] = true;
                }
                if ( ! empty( $this->dboptions['enable_polygon_on_map'] ) ) {
                    $wdap_js_lang['mapsettings']['enable_polygon_on_map']     = ! empty( $this->dboptions['enable_polygon_on_map'] ) ? $this->dboptions['enable_polygon_on_map'] : 'no';
                } elseif ( WDAP_VERSION == '1.0.3' ) {
                    $wdap_js_lang['mapsettings']['enable_polygon_on_map'] = true;
                }
                $wdap_js_lang['mapsettings']['restrict_country']     = ! empty( $this->dboptions['wdap_country_restriction_listing'][0] ) ? $this->dboptions['wdap_country_restriction_listing'][0] : '';
            }
            $shop_error_message = array(
                'na'=> !empty( $this->dboptions['wdap_shop_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_shop_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
                'a'=> !empty( $this->dboptions['wdap_shop_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_shop_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
                'invld' => ! empty( $this->dboptions['wdap_shop_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_shop_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),

            );

            $category_error_message = array(
                'na'=> !empty( $this->dboptions['wdap_category_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_category_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
                'a'=> !empty( $this->dboptions['wdap_category_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_category_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
                'invld' => ! empty( $this->dboptions['wdap_category_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_category_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),

            );
            $product_error_message = array(
                'na'=> !empty( $this->dboptions['wdap_product_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_product_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
                'a'=> !empty( $this->dboptions['wdap_product_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_product_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
                'invld' => ! empty( $this->dboptions['wdap_product_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_product_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
            );

            $cart_error_message = array(
                'na'=> !empty( $this->dboptions['wdap_cart_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
                'a'=> !empty( $this->dboptions['wdap_cart_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
                'invld' => ! empty( $this->dboptions['wdap_cart_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
                'th' => ! empty( $this->dboptions['wdap_cart_error_th'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_th'] ) ) : esc_html__( ' Availability Status ', 'woo-delivery-area-pro' ),
                'summary' => ! empty( $this->dboptions['wdap_cart_error_summary'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_summary'] ) ) : esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' ),

            );

            $checkout_error_message = array(
                'na'=> !empty( $this->dboptions['wdap_checkout_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
                'a'=> !empty( $this->dboptions['wdap_checkout_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
                'invld' => ! empty( $this->dboptions['wdap_checkout_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
                'th' => ! empty( $this->dboptions['wdap_checkout_error_th'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_th'] ) ) : esc_html__( ' Availability Status ', 'woo-delivery-area-pro' ),
                'summary' => ! empty( $this->dboptions['wdap_checkout_error_summary'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_summary'] ) ) : esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' ),

            );

            $errormessage = array(
                'empty' => ! empty( $this->dboptions['wdap_empty_zip_code'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_empty_zip_code'] ) ) : esc_html__( ' Please enter zip code. ', 'woo-delivery-area-pro' ),
                'na' => esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
                'a' =>  esc_html__( ' Product Available', 'woo-delivery-area-pro' ),
                'invld' =>  esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
                'p' => esc_html__( 'Products are ', 'woo-delivery-area-pro' ),
                'th' => esc_html__( 'Availability Status ', 'woo-delivery-area-pro' ),
                'pr' => esc_html__( 'Products ', 'woo-delivery-area-pro' ),
                'summary' => esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable ', 'woo-delivery-area-pro' ),
                'error_msg_color' => ! empty( $this->dboptions['error_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['error_msg_color'] ) ) : '#ff0000',

                'success_msg_color' => ! empty( $this->dboptions['success_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['success_msg_color'] ) ) : '#77a464',
            );

            if(is_shop() ){
                $errormessage = array_merge($errormessage, $shop_error_message);
            }

            if(is_product_category()){
                $errormessage = array_merge($errormessage, $category_error_message);
            }

            if ( is_cart() ) {
                $errormessage = array_merge($errormessage, $cart_error_message);
            }

            if ( is_checkout() ) {
                $errormessage = array_merge($errormessage, $checkout_error_message);
            }
            if ( is_single() && $post->post_type == 'product' ) {
                $errormessage = array_merge($errormessage, $product_error_message);
            }

            $wdap_js_lang['errormessages'] = $errormessage;

            if ( ! empty( $this->dboptions['enable_order_restriction'] ) && is_checkout() ) {
                $wdap_js_lang['order_restriction'] = ! empty( $this->dboptions['enable_order_restriction'] ) ? $this->dboptions['enable_order_restriction'] : '';
            }
            if ( ! empty( $this->dboptions['wdap_checkout_avality'] ) && ( $this->dboptions['wdap_checkout_avality'] == 'via_address' ) ) {
                $wdap_js_lang['wdap_checkout_avality'] = $this->dboptions['wdap_checkout_avality'];
            }
            $shortcode_settings = array(
                'wdap_address_empty'     => ! empty( $this->dboptions['wdap_address_empty'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_address_empty'] ) ) : esc_html__( 'Please enter your address', 'woo-delivery-area-pro' ),

                'address_not_shipable'   => ! empty( $this->dboptions['address_not_shipable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['address_not_shipable'] ) ) : esc_html__( 'Sorry, We do not provide shipping in this area.', 'woo-delivery-area-pro' ),

                'address_shipable'       => ! empty( $this->dboptions['address_shipable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['address_shipable'] ) ) : esc_html__( 'Yes, We provide shipping in this area.', 'woo-delivery-area-pro' ),

                'prlist_error'       => ! empty( $this->dboptions['product_listing_error'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['product_listing_error'] ) ) : esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),

                'form_success_msg_color' => ! empty( $this->dboptions['form_success_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['form_success_msg_color'] ) ) : '',

                'form_error_msg_color'   => ! empty( $this->dboptions['form_error_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['form_error_msg_color'] ) ) : '',
            );
            $wdap_js_lang['shortcode_settings'] = $shortcode_settings;
            $wdap_js_lang['shortcode_map']['enable'] = true;
            $wdap_js_lang['shortcode_map']['zoom'] = ! empty( $this->dboptions['shortcode_map_zoom_level'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_zoom_level'] ) ) : '';
            $wdap_js_lang['shortcode_map']['centerlat'] = ! empty( $this->dboptions['shortcode_map_center_lat'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_center_lat'] ) ) : '';
            $wdap_js_lang['shortcode_map']['centerlng'] = ! empty( $this->dboptions['shortcode_map_center_lng'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_center_lng'] ) ) : '';
            $wdap_js_lang['shortcode_map']['style']  = ! empty( $this->dboptions['shortcode_map_style'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_style'] ) ) : '';

            if ( isset( $this->dboptions['enable_product_listing'] ) ) {
                $wdap_js_lang['shortcode_settings']['check_product'] = isset( $this->dboptions['enable_product_listing'] ) ? $this->dboptions['enable_product_listing'] : '';
            }

            $wdap_js_lang['can_be_delivered_redirect_url'] = isset( $this->dboptions['can_be_delivered_redirect_url'] ) ? esc_url( $this->dboptions['can_be_delivered_redirect_url'] ) : '';
            $wdap_js_lang['cannot_be_delivered_redirect_url'] = isset( $this->dboptions['cannot_be_delivered_redirect_url'] ) ? esc_url( $this->dboptions['cannot_be_delivered_redirect_url'] ) : '';
            $wdap_js_lang['loader_image'] = esc_url( WDAP_IMAGES . 'loader.gif' );
            
            $wdap_js_lang['enable_autosuggest_checkout']     = ! empty( $this->dboptions['enable_auto_suggest_checkout'] ) ? $this->dboptions['enable_auto_suggest_checkout'] : false;
            
            $wdap_js_lang['disable_availability_status']     = !empty( $this->dboptions['disable_availability_status'] ) ? false : true ;

            return $wdap_js_lang;
        }

        function wpdap_get_collections() {

            global $wpdb;
            $this->collections = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wdap_collection' );

        }

        function wdap_ajax_call_parent() {

            // Nonce Verificatoin
            if ( isset( $_POST['noncevalue'] ) && ! wp_verify_nonce( $_POST['noncevalue'], 'wdap-call-nonce' ) ) {
                return;
            }

            $operation = isset( $_POST['operation'] ) ? sanitize_text_field( $_POST['operation'] ) : '';
            if ( empty( $operation ) ) {
                return;
            }

            if( isset($_POST['zipcode']) &&  $_POST['zipcode'] !== '') {
                $_POST['zipcode'] = preg_replace('/\s+/', '', $_POST['zipcode']);
            }

            $response = $this->$operation( $_POST );
            echo json_encode( $response );
            exit;
        }

        function Check_for_zipmatch( $data ) {

            $zip_response = isset( $data['zip_response'] ) ? $data['zip_response'] : '';
            $tempData = str_replace( '\\', '', $zip_response );
            $decoded = json_decode( $tempData );
            if ( ! empty( $decoded ) ) {
                $json  = json_encode( $decoded );
                $array = json_decode( $json, true );
                $this->current_request_response = $array;
            }           
            $this->ajax_params = $data;
            $shortcode = isset( $data['shortcode'] ) ? $data['shortcode'] : '';
            $pagetype = isset( $data['pagetype'] ) ? $data['pagetype'] : '';
            $response = array();
            $cartproductidcheck = array();
            if ( $pagetype == 'single' || $pagetype == 'shop' || $pagetype=='category' ) {
                $zipcode = isset( $data['zipcode'] ) ? $data['zipcode'] : '';
                $ch = curl_init();
                $headers = array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                );
                curl_setopt($ch, CURLOPT_URL, 'https://api.postcodes.io/postcodes/'.$zipcode);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $json_response = curl_exec($ch);
                $json_response = json_decode($json_response);
                $err = curl_error($ch);
                curl_close($ch);
                if ($err) {
                    error_log("cURL Error #:" . $err);
                }
                if($json_response->status == 200) {
                    $postCodes = array_map('trim', explode(',', get_option('nondeliverablepostalcodes')));
                    if (!in_array(trim($zipcode), $postCodes) && substr($zipcode, 0, 2) !== 'GY' && substr($zipcode, 0, 2) !== 'JE') {
                        $response['status'] = 'found';
                        $response['pagetype'] = $pagetype;
                    } else {
                        $response = array(
                            'status' => 'notfound',
                            'coordinatematch' => array(),
                            'pagetype' => $pagetype
                        );
                    }
                    unset( $this->current_request_response );
                    $this->ajax_params = array();
                    return $response;
                } else {
                    $response['status'] = 'invalid';
                    $response['pagetype'] = $pagetype;
                    unset( $this->current_request_response );
                    $this->ajax_params = array();
                    return $response;
                }
            }
            if ( $pagetype == 'cart' || $pagetype == 'checkout' ) {
                $is_local_pickup_enable = false;
                $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
                if ( ! empty( $chosen_methods ) ) {
                    preg_match( '/(local_pickup)/', $chosen_methods[0], $is_local_pickup, PREG_OFFSET_CAPTURE );
                    if ( ! empty( $is_local_pickup ) ) {
                        $is_local_pickup_enable = true;
                    }
                }

                $productsid = isset( $data['productid'] ) ? $data['productid'] : array();
                foreach ( $productsid as  $productid ) {

                    $data['productid'] = $productid;
                    $dataToStore = array();
                    
                    $_product =  wc_get_product($productid);

                    if ( $is_local_pickup_enable ||  ($_product->is_type( 'virtual' )) ) {
                        $dataToStore['id'] = $productid;
                        $dataToStore['status'] = 'found';
                        $dataToStore['coordinatematch'] = array();
                    } else {
                        $responsecart = $this->wdap_get_zipcodematch( $data );
                        $dataToStore['id'] = $productid;
                        $dataToStore['status'] = $responsecart['status'];
                        $dataToStore['coordinatematch'] = isset($responsecart['coordinatematch']) ? $responsecart['coordinatematch'] :array();
                    }
                    $cartproductidcheck[] = $dataToStore;
                }
                $response['cartdata'] = $cartproductidcheck;
            }
            if ( $shortcode ) {
                $this->is_via_shortcode = true;
                $response = $this->wdap_get_zipcodematch( $data );
                if ( $response['status'] == 'found' ) {
                    return $response;
                } else {
                    $zipcode1 = $this->get_zip_code_from_response();
                    if ( ! empty( $data['zipcode'] ) && ! ( $data['zipcode'] == $zipcode1 ) && ! empty( $zipcode1 ) ) {
                        $data['zipcode'] = $zipcode1;
                        $response = $this->wdap_get_zipcodematch( $data );
                    }
                }
            }
            $response['zipcodestring'] = $this->wpdap_get_lat_lng_without_restrict();
            $response['pagetype'] = $pagetype;
            unset( $this->current_request_response );
            $this->ajax_params = array();
            $this->is_via_shortcode = false;
            return $response;

        }

        // Search function for zip code match in all collections
        function wdap_get_zipcodematch( $data ) {

            global $wpdb;
            $retrictcountrydata = array();
            $retrictziplatlng   = array();
            $zipcode = isset( $data['zipcode'] ) ? $data['zipcode'] : '';
            if ( isset( $zipcode ) ) {
                $collections  = $this->collections;
                $startsearch  = false;
                $match        = false;
                $collectionid = array();
                $productmatch = false;
                $collectioncoodinates = array();

                if ( is_array( $collections ) && count( $collections ) > 0 ) {

                    foreach ( $collections as $collection ) {
                        // Loop for testing every collection
                        $map_region = isset( $collection->wdap_map_region ) ? $collection->wdap_map_region : '';
                        $map_region_value = isset( $collection->wdap_map_region_value ) ? $collection->wdap_map_region_value : '';
                        $c_id = isset( $collection->id ) ? $collection->id : '';
                        $applyon = isset( $collection->applyon ) ? $collection->applyon : '';

                        $assignploygons = isset( $collection->assignploygons ) ? $collection->assignploygons : '';

                        $checkinmapregiondata = array(
                            'mapregion' => $map_region,
                            'mapregionvalue' => $map_region_value,
                            'zipcode' => $zipcode,
                            'id' => $c_id,
                        );
                        $getmatch = false;
                        if ( $applyon == 'All Products' ) {
                            if ( $map_region == 'country' || $map_region == 'zipcode' ) {
                                $getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
                            }
                            if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
                                $getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
                            }
                            if ( $getmatch ) {
                                $startsearch = true;
                                $collectionid[] = $c_id;
                                 break;
                            } else {
                                 $collectioncoodinates[] = $assignploygons;
                            }
                        } else if ( $applyon == 'Selected Products' ) {

                            $productid = isset( $data['productid'] ) ? $data['productid'] : '';
                            if ( is_array( $productid ) && count( $productid ) > 0 ) {
                                $productid = $productid['0'];
                            }
                            $product_match = true;

                            $saved_products = isset( $collection->chooseproducts ) ? maybe_unserialize( $collection->chooseproducts ) : array();

                            if ( ! empty( $data['shortcode'] ) ) {
                                if ( ! empty( $productid ) && is_array( $saved_products ) && count( $saved_products ) > 0 ) {
                                    $product_match  = ( in_array( $productid, $saved_products ) ) ? true : false;
                                } else {
                                    $product_match = true;
                                }
                            } else {
                                if ( is_array( $saved_products ) && count( $saved_products ) > 0 ) {
                                    $product_match = in_array( $productid, $saved_products );
                                }
                            }
                            if ( $product_match ) {
                                if ( $map_region == 'country' || $map_region == 'zipcode' ) {
                                    $getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
                                }
                                if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
                                    $getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
                                }
                                if ( $getmatch ) {
                                    $startsearch = true;
                                    $collectionid[] = $c_id;
                                    break;
                                } else {
                                    $collectioncoodinates[] = $assignploygons;
                                }
                            }
                        } else if ( $applyon == 'all_products_excluding_some' ) {
                            $productid = isset( $data['productid'] ) ? $data['productid'] : '';
                            if ( is_array( $productid ) && count( $productid ) > 0 ) {
                                $productid = $productid['0'];
                            }
                            $product_match;
                            $exclude_products = isset( $collection->exclude_products ) ? maybe_unserialize( $collection->exclude_products ) : array();
                            if ( ! empty( $data['shortcode'] ) ) {

                                if ( ! empty( $productid ) && is_array( $exclude_products ) && count( $exclude_products ) > 0 ) {
                                    $product_match  = ( in_array( $productid, $exclude_products ) ) ? true : false;
                                } else {
                                    $product_match = true;
                                }
                            } else {
                                if ( is_array( $exclude_products ) && count( $exclude_products ) > 0 ) {
                                    $product_match = in_array( $productid, $exclude_products );
                                }
                            }
                            if ( ! ( $product_match ) ) {
                                if ( $map_region == 'country' || $map_region == 'zipcode' ) {
                                    $getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
                                }
                                if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
                                    $getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
                                }
                                if ( $getmatch ) {
                                    $startsearch = true;
                                    $collectionid[] = $c_id;
                                } else {
                                    $collectioncoodinates[] = $assignploygons;
                                }
                            }
                        } else {
                                $matched_category = array();
                                $productid = isset( $data['productid'] ) ? $data['productid'] : '';
                                if ( is_array( $productid ) && count( $productid ) > 0 ) {
                                    $productid = $productid['0'];
                                }

                                $terms = get_the_terms($productid, 'product_cat' );


                                $products_category = array();
                                if ( ! empty( $terms ) && is_array( $terms ) ) {
                                    foreach ( $terms as $key => $term ) {
                                        $products_category[] = isset( $term->term_id ) ? $term->term_id : '';
                                    }
                                }
                                $collection_category = isset( $collection->selectedcategories ) ? unserialize( $collection->selectedcategories ) : '';
                                $matched_category = array_intersect( $products_category, $collection_category );
                                $product_match = true;

                                if ( ! empty( $data['shortcode'] ) ) {

                                    if(!empty($productid)){

                                        if(count($matched_category)>0){
                                            $product_match = true;
                                        }else{
                                            $product_match = false;
                                        }

                                    }else{
                                        $product_match = true;
                                    }

                                } else {

                                    if(count( $matched_category )>0) {
                                        $product_match = true;
                                    }else{
                                        $product_match = false;
                                    }
                                }

                            if($product_match){

                                if ( $map_region == 'country' || $map_region == 'zipcode' ) {
                                    $getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
                                }
                                if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
                                    $getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
                                }
                                if($getmatch){

                                    if(count($matched_category)>0){
                                        $matched_category = array_values( $matched_category );
                                        $matched_id = $matched_category[0];
                                        $matched_category_obj = get_term_by( 'id', $matched_id, 'product_cat' );
                                        $matched_category_name = $matched_category_obj->name;
                                    }

                                    $startsearch = true;
                                    $collectionid[] = $c_id;
                                    break;
                                } else {
                                    $collectioncoodinates[] = $assignploygons;
                                }
                            }
                        }
                    }
                }
            }

            if ( $startsearch ) {
                    $response = array(
                        'status' => 'found',
                        'collectionid' => $collectionid,
                    );
            } else {
                if ( ! empty( $collectioncoodinates ) ) {
                    $allcoordinates = $this->wdap_search_in_coordinate( $collectioncoodinates );
                    $response = array(
                        'status' => 'notfound',
                        'coordinatematch' => $allcoordinates,
                    );
                } else {
                    $response = array(
                        'status' => 'notfound',
                        'coordinatematch' => array(),
                    );
                }
            }
            return $response;
        }

        function wpdap_match_in_zip_country( $data ) {

            $zip = isset( $data['zipcode'] ) ? $data['zipcode'] : '';

            $mapregion = isset( $data['mapregion'] ) ? $data['mapregion'] : '';
            $countrylistfromdb = isset( $data['mapregionvalue'] ) ? maybe_unserialize( $data['mapregionvalue'] ) : array();
            if ( $mapregion == 'country' ) {
                $countrylistfromzip = $this->wpdap_get_country_list( $zip );
                if ( isset( $countrylistfromzip ) && isset($countrylistfromdb['country']) ) {
                    $result = array_intersect( $countrylistfromzip, $countrylistfromdb['country'] );
                    if ( count( $result ) > 0 ) {
                        return true;
                    }
                }
            } else {
                $partial_code_array = array();
                if ( count( $countrylistfromdb ) > 0 ) {

                    if ( in_array( $zip, $countrylistfromdb ) ) {
                        return true;
                    }
                    foreach ( $countrylistfromdb as $key => $dbzip ) {
                        if ( stristr( $dbzip, '*' ) ) {
                            $dbzip = str_replace( '*', '', $dbzip );
                            $partial_code_array[] = $dbzip;
                        }
                    }
                }

                if ( ! empty( $partial_code_array ) ) {
                    $matches = $this->partial_zip_find( $zip, $partial_code_array );
                    return $matches;
                }
            }
            return false;
        }

    }

    new \WDAP_Delivery_Area_Extension();
} else {
    add_action( 'admin_notices', 'wdap_woocommerce_delivery_area_pro_missing' );
    function wdap_woocommerce_delivery_area_pro_missing() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e( 'WooCommerce Delivery Area Pro is required for Woocommerce Delivery Area Pro Extension to work. Please install and configure WooCommerce Delivery Area Pro first.', 'woo-delivery-area-pro' ); ?>
            </p>
        </div>
        <?php
    }
}