<?php
/**
 * Unifaun Online Delivery Extended
 *
 * Plugin Name: Unifaun Online Delivery Extended
 * Description: Custom extended functionality for Unifaun Online Shipping Method for WooCommerce
 * Version: 1.0.0
 * Author: Ciklum Pvt. Ltd
 * Author URI: https://www.ciklum.com/
 * License: proprietary
 */

define('UNIFAUN_EXTENDED_HOST',get_option('unifaun_extended_option_host','104.248.38.184'));
define('UNIFAUN_EXTENDED_PORT',get_option('unifaun_extended_option_port',22));
define('UNIFAUN_EXTENDED_USER',get_option('unifaun_extended_option_user','synceshop.bonculina.se'));
define('UNIFAUN_EXTENDED_PASS',get_option('unifaun_extended_option_pass','fg3(325%#CTG32'));
define('UNIFAUN_EXTENDED_PATH',get_option('unifaun_extended_option_path','/To_GKL/'));

// define the woocommerce_order_details_after_order_table callback and create zpl and xml file
function action_woocommerce_order_details_after_order_table( $order ) {

    try{

        if(!is_kco_test_mode()) {
            //Order ID
            $order_id = $order->get_id();

            //Upload FTP zpl pdf file which is saved in uploads folder by unifaun API
            upload_zpl_file_to_ftp($order_id);
        }

    }catch(Exception $e){
        update_option('_unifaun_online_extended_exception'.$order_id, $e);
    }

}

// add the action
add_action( 'woocommerce_order_details_after_order_table', 'action_woocommerce_order_details_after_order_table', 100, 1 );

// define the woocommerce_update_order callback and create/update zpl and xml file
function unifaun_extended_action_woocommerce_update_order( $order_get_id ) {
    try{

        if(!is_kco_test_mode()) {

            //Upload FTP zpl pdf file which is saved in uploads folder by unifaun API
            upload_zpl_file_to_ftp($order_get_id);

        }

    }catch(Exception $e){
        update_option('_unifaun_online_extended_exception'.$order_get_id, $e);
    }

}

// add the action
add_action( 'woocommerce_update_order', 'unifaun_extended_action_woocommerce_update_order', 100, 1 );

// define the wc_customer_order_export_xml_filename callback and replace order numbers in export file
function wc_csv_export_edit_filename( $post_replace_filename, $pre_replace_filename, $order_ids ) {

    // only for orders
    if ( false !== strpos( $pre_replace_filename, '%%order_numbers%%' ) ) {

        $order_numbers = array();

        foreach ( $order_ids as $id ) {

            $order = new WC_Order( $id );

            $order_numbers[] = $order->get_order_number();
        }

        $post_replace_filename = str_replace( '%%order_numbers%%', implode( '-', $order_numbers ), $post_replace_filename );
    }

    return $post_replace_filename;
}
add_filter( 'wc_customer_order_export_xml_filename', 'wc_csv_export_edit_filename', 10, 3 );

//Settings Page Start
function unifaun_extended_register_settings() {
    add_option( 'unifaun_extended_option_host', '104.248.38.184');
    add_option( 'unifaun_extended_option_port', 22);
    add_option( 'unifaun_extended_option_user', 'synceshop.bonculina.se');
    add_option( 'unifaun_extended_option_pass', 'fg3(325%#CTG32');
    add_option( 'unifaun_extended_option_path', '/To_GKL/');
    register_setting( 'unifaun_extended_options_group', 'unifaun_extended_option_host', 'unifaun_extended_callback' );
    register_setting( 'unifaun_extended_options_group', 'unifaun_extended_option_port', 'unifaun_extended_callback' );
    register_setting( 'unifaun_extended_options_group', 'unifaun_extended_option_user', 'unifaun_extended_callback' );
    register_setting( 'unifaun_extended_options_group', 'unifaun_extended_option_pass', 'unifaun_extended_callback' );
    register_setting( 'unifaun_extended_options_group', 'unifaun_extended_option_path', 'unifaun_extended_callback' );
}
add_action( 'admin_init', 'unifaun_extended_register_settings' );

function unifaun_extended_register_options_page() {
    add_options_page('FTP Settings', 'FTP Settings', 'manage_options', 'unifaun_extended', 'unifaun_extended_options_page');
}
add_action('admin_menu', 'unifaun_extended_register_options_page');

function unifaun_extended_option_page()
{
    //content on page goes here
}
function unifaun_extended_options_page()
{
    ?>
    <div>
        <?php screen_icon(); ?>
        <h2>FTP Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'unifaun_extended_options_group' ); ?>
            <table>
                <tr valign="top">
                    <th scope="row"><label for="unifaun_extended_option_host">Host</label></th>
                    <td><input type="text" id="unifaun_extended_option_host" name="unifaun_extended_option_host" value="<?php echo get_option('unifaun_extended_option_host'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="unifaun_extended_option_port">Port</label></th>
                    <td><input type="text" id="unifaun_extended_option_port" name="unifaun_extended_option_port" value="<?php echo get_option('unifaun_extended_option_port'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="unifaun_extended_option_user">User</label></th>
                    <td><input type="text" id="unifaun_extended_option_user" name="unifaun_extended_option_user" value="<?php echo get_option('unifaun_extended_option_user'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="unifaun_extended_option_pass">Password</label></th>
                    <td><input type="text" id="unifaun_extended_option_pass" name="unifaun_extended_option_pass" value="<?php echo get_option('unifaun_extended_option_pass'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="unifaun_extended_option_path">Path</label></th>
                    <td><input type="text" id="unifaun_extended_option_path" name="unifaun_extended_option_path" value="<?php echo get_option('unifaun_extended_option_path'); ?>" /></td>
                </tr>
            </table>
            <?php  submit_button(); ?>
        </form>
    </div>
    <?php
}
//Settings Page End

//Callback function to upload zpl file to FTP
function upload_zpl_file_to_ftp($order_id){
    $file_pdf_path = '';

    //Get order post meta for print file path
    $unifaun_shipments_print_file = get_post_meta( $order_id, '_msunifaun_online__order_print_file');

    //Check PDF file exist or not and store in variable
    if($unifaun_shipments_print_file){
        if(isset($unifaun_shipments_print_file[0]) && $unifaun_shipments_print_file[0] != ''){
            if(isset($unifaun_shipments_print_file[0][0]) && $unifaun_shipments_print_file[0][0] != ''){
                $file_pdf_path = $unifaun_shipments_print_file[0][0];
            }
        }
    }

    //If file is available, send to FTP
    if($file_pdf_path != ''){
        upload_pdf_file_in_sftp($order_id, $file_pdf_path);
        create_order_xml($order_id);
    }
}

function upload_pdf_file_in_sftp($order_id, $file_pdf_path){
    $upload_dir = wp_upload_dir();
    $base_upload_path = $upload_dir['basedir'];
    $zpl_file_path = $file_pdf_path;

    $ch = curl_init();
    $localFile = $base_upload_path.$zpl_file_path ;
    //print_r($localFile);exit;
    $remoteFile = UNIFAUN_EXTENDED_PATH.'order-'.$order_id.'.zpl';

    $host = UNIFAUN_EXTENDED_HOST;
    $port = UNIFAUN_EXTENDED_PORT;
    $user = UNIFAUN_EXTENDED_USER;
    $pass = UNIFAUN_EXTENDED_PASS;

    $connection = ssh2_connect($host, $port);

    if ($connection){
        ssh2_auth_password($connection, $user, $pass);
        $sftp = ssh2_sftp($connection);

        $stream = fopen("ssh2.sftp://$sftp$remoteFile", 'w');
        $file = file_get_contents($localFile);
        fwrite($stream, $file);
        fclose($stream);
    }else{
        header('Location: ?post_type=shop_order');
        exit;
    }


}

function upload_xml_file_in_sftp($order_id, $file_pdf_path){
    $upload_dir = wp_upload_dir();
    $base_upload_path = $upload_dir['basedir'];
    $zpl_file_path = $file_pdf_path;

    $ch = curl_init();
    $localFile = $base_upload_path.$zpl_file_path ;
    $remoteFile = UNIFAUN_EXTENDED_PATH.'order-'.$order_id.'.xml';

    $host = UNIFAUN_EXTENDED_HOST;
    $port = UNIFAUN_EXTENDED_PORT;
    $user = UNIFAUN_EXTENDED_USER;
    $pass = UNIFAUN_EXTENDED_PASS;

    $connection = ssh2_connect($host, $port);

    if ($connection){
        ssh2_auth_password($connection, $user, $pass);
        $sftp = ssh2_sftp($connection);

        $stream = fopen("ssh2.sftp://$sftp$remoteFile", 'w');
        $file = file_get_contents($localFile);
        fwrite($stream, $file);
        fclose($stream);
    }else{
        header('Location: ?post_type=shop_order');
        exit;
    }


}

add_action('init','_online_msunifaunonline_process');
function _online_msunifaunonline_process(){
    if(!is_kco_test_mode()) {
        if(isset($_GET['process_done']) && isset($_GET['post_type'])){
            if(($_GET['process_done'] != '') && ($_GET['post_type'] == 'shop_order')){
                upload_zpl_file_to_ftp((int)$_GET['process_done']);
                // Redirect to get rid of argument
                header('Location: ?post_type=shop_order');
                exit;
            }
        }
    }
}

function create_order_xml($order_id = ''){

    $upload_dir = wp_upload_dir();
    $base_upload_path = $upload_dir['basedir'];
    $file =  $base_upload_path . '/wp-orders-exports/order-'.$order_id.'.xml';
    /*if (!is_dir($base_upload_path . '/wp-orders-exports/'.date('Y'))) {
        if (!mkdir($base_upload_path . '/wp-orders-exports/'.date('Y'), 0777, true) && !is_dir($base_upload_path . '/wp-orders-exports/'.date('Y'))) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', 'path/to/directory'));
        }
    }*/
    $order = WC_Order_Factory::get_order($order_id);
    $line_items_arr = array();

    if($order){
        $order_data = $order->get_data();
        $order_id = $order->get_id();

        if(isset($order_data['shipping'])){
            $shipping = $order_data['shipping'];
            $full_name = $shipping['first_name'].' '.$shipping['last_name'];
            $address_1 = $shipping['address_1'];
            $address_2 = $shipping['address_2'];
            $city = $shipping['city'];
            $postcode = $shipping['postcode'];
        }

        if(isset($order_data['line_items'])){
            $line_items = $order_data['line_items'];

            $count = 0;
            foreach($line_items as $id => $line_item){

                $line_item_data = $line_item->get_data();
                $product_variation_id = $line_item_data['variation_id'];
                if ($product_variation_id) {
                    $product = wc_get_product($line_item['variation_id']);
                } else {
                    $product = wc_get_product($line_item['product_id']);
                }
                $sku = $price = '';
                if($product){
                    $sku = $product->get_sku();
                    $price = $product->get_price();
                }

                $line_items_arr[$count]['id'] = $id;
                $line_items_arr[$count]['name'] = $line_item_data['name'];
                $line_items_arr[$count]['product_id'] = $line_item_data['product_id'];
                $line_items_arr[$count]['sku'] = $sku;
                $line_items_arr[$count]['quantity'] = $line_item_data['quantity'];
                $line_items_arr[$count]['price'] = $price;
                $line_items_arr[$count]['subtotal'] = $line_item_data['subtotal'];
                $line_items_arr[$count]['subtotal_tax'] = $line_item_data['subtotal_tax'];
                $line_items_arr[$count]['total'] = $line_item_data['total'];
                $line_items_arr[$count]['total_tax'] = $line_item_data['total_tax'];
                $order_item_metadata = wc_get_order_item_meta( $id, '_woosb_parent_id');
                if($order_item_metadata){
                    $line_items_arr[$count]['bundled_item'] = true;
                }
                else{
                    $line_items_arr[$count]['bundled_item'] = false;
                }
                $count++;


            }
        }

        $unifaun_shipment_response = get_post_meta( $order_id, '_msunifaun_online__order_shipment_response_body_decoded');
        $shipment_number = '';
        if($unifaun_shipment_response){
            if(isset($unifaun_shipment_response[0])){
                if(isset($unifaun_shipment_response[0][0])){
                    if(isset($unifaun_shipment_response[0][0][0]['shipmentNo'])){
                        $shipment_number = $unifaun_shipment_response[0][0][0]['shipmentNo'];
                    }
                }
            }
        }

        $order_date_obj = new DateTime($order_data['date_created']);
        $order_date = $order_date_obj->format('Y-m-d');
        $order_time = $order_date_obj->format('H:i:s');

        $newsXML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" '
            . 'standalone="yes"?><Order></Order>');
        $orderTag = $newsXML->addChild('OrderId',$order_data['id']);
        $newsXML->addChild('OrderTime',$order_time);
        $newsXML->addChild('OrderDate',$order_date);
        $newsXML->addChild('ShippingFullName',$full_name);
        $newsXML->addChild('ShippingAddress1',$address_1);
        $newsXML->addChild('ShippingAddress2',$address_2);
        $newsXML->addChild('ShippingCity',$city);
        $newsXML->addChild('ShippingPostcode',$postcode);

        $OrderLineItemsTag = $newsXML->addChild('OrderLineItems');

        foreach ($line_items_arr as $line_items_single){
            if(!$line_items_single['bundled_item']){
                $OrderLineItemTag = $OrderLineItemsTag->addChild('OrderLineItem');
                $OrderLineItemTag->addChild('Id',$line_items_single['id']);
                $OrderLineItemTag->addChild('Name',$line_items_single['name']);
                $OrderLineItemTag->addChild('ProductId',$line_items_single['product_id']);
                $OrderLineItemTag->addChild('SKU',$line_items_single['sku']);
                $OrderLineItemTag->addChild('Quantity',$line_items_single['quantity']);
                $OrderLineItemTag->addChild('Price',$line_items_single['price']);
                $OrderLineItemTag->addChild('Subtotal',$line_items_single['subtotal']);
                $OrderLineItemTag->addChild('SubtotalTax',$line_items_single['subtotal_tax']);
                $OrderLineItemTag->addChild('Total',$line_items_single['total']);
                $OrderLineItemTag->addChild('TotalTax',$line_items_single['total_tax']);
            }
        }

        $newsXML->addChild('ShipmentNumber',$shipment_number);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($newsXML->asXML());

        $domXML = $dom->saveXML();
        $dom->save($file);
        upload_xml_file_in_sftp($order_id,'/wp-orders-exports/order-'.$order_id.'.xml');
    }

}

add_action( 'rest_api_init', function () {
  register_rest_route( 'bonculina/v1', '/xml/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'bonculina_xml_callback',

  ) );
} );

function bonculina_xml_callback( WP_REST_Request $request ) {
  //if( current_user_can('administrator') ) {
    $order_id = $request->get_param( 'id' );
    $order_xml = create_order_xml_test($order_id);
    print_r($order_xml);exit;
  //}
}

function create_order_xml_test($order_id = ''){

    $upload_dir = wp_upload_dir();
    $base_upload_path = $upload_dir['basedir'];
    $file =  $base_upload_path . '/wp-orders-exports/order-'.$order_id.'.xml';
    /*if (!is_dir($base_upload_path . '/wp-orders-exports/'.date('Y'))) {
        if (!mkdir($base_upload_path . '/wp-orders-exports/'.date('Y'), 0777, true) && !is_dir($base_upload_path . '/wp-orders-exports/'.date('Y'))) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', 'path/to/directory'));
        }
    }*/
    $order = WC_Order_Factory::get_order($order_id);
    $line_items_arr = array();

    if($order){
        $order_data = $order->get_data();
        $order_id = $order->get_id();

        if(isset($order_data['shipping'])){
            $shipping = $order_data['shipping'];
            $full_name = $shipping['first_name'].' '.$shipping['last_name'];
            $address_1 = $shipping['address_1'];
            $address_2 = $shipping['address_2'];
            $city = $shipping['city'];
            $postcode = $shipping['postcode'];
        }

        if(isset($order_data['line_items'])){
            $line_items = $order_data['line_items'];

            $count = 0;
            foreach($line_items as $id => $line_item){

                $line_item_data = $line_item->get_data();
                $product_variation_id = $line_item_data['variation_id'];
                if ($product_variation_id) {
                    $product = wc_get_product($line_item['variation_id']);
                } else {
                    $product = wc_get_product($line_item['product_id']);
                }
                $sku = $price = '';
                if($product){
                    $sku = $product->get_sku();
                    $price = $product->get_price();
                }
                $order_item_metadata = wc_get_order_item_meta( $id, '_woosb_parent_id');

                $line_items_arr[$count]['id'] = $id;
                $line_items_arr[$count]['name'] = $line_item_data['name'];
                $line_items_arr[$count]['product_id'] = $line_item_data['product_id'];
                $line_items_arr[$count]['sku'] = $sku;
                $line_items_arr[$count]['quantity'] = $line_item_data['quantity'];
                $line_items_arr[$count]['price'] = $price;
                $line_items_arr[$count]['subtotal'] = $line_item_data['subtotal'];
                $line_items_arr[$count]['subtotal_tax'] = $line_item_data['subtotal_tax'];
                $line_items_arr[$count]['total'] = $line_item_data['total'];
                $line_items_arr[$count]['total_tax'] = $line_item_data['total_tax'];
                if($order_item_metadata){
                    $line_items_arr[$count]['bundled_item'] = true;
                }
                else{
                    $line_items_arr[$count]['bundled_item'] = false;
                }
                $count++;


            }
        }

        $unifaun_shipment_response = get_post_meta( $order_id, '_msunifaun_online__order_shipment_response_body_decoded');
        $shipment_number = '';
        if($unifaun_shipment_response){
            if(isset($unifaun_shipment_response[0])){
                if(isset($unifaun_shipment_response[0][0])){
                    if(isset($unifaun_shipment_response[0][0][0]['shipmentNo'])){
                        $shipment_number = $unifaun_shipment_response[0][0][0]['shipmentNo'];
                    }
                }
            }
        }

        $order_date_obj = new DateTime($order_data['date_created']);
        $order_date = $order_date_obj->format('Y-m-d');
        $order_time = $order_date_obj->format('H:i:s');

        $newsXML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" '
            . 'standalone="yes"?><Order></Order>');
        $orderTag = $newsXML->addChild('OrderId',$order_data['id']);
        $newsXML->addChild('OrderTime',$order_time);
        $newsXML->addChild('OrderDate',$order_date);
        $newsXML->addChild('ShippingFullName',$full_name);
        $newsXML->addChild('ShippingAddress1',$address_1);
        $newsXML->addChild('ShippingAddress2',$address_2);
        $newsXML->addChild('ShippingCity',$city);
        $newsXML->addChild('ShippingPostcode',$postcode);

        $OrderLineItemsTag = $newsXML->addChild('OrderLineItems');

        foreach ($line_items_arr as $line_items_single){
            //print_r($line_items_single);
            if(!$line_items_single['bundled_item']){
                $OrderLineItemTag = $OrderLineItemsTag->addChild('OrderLineItem');
                $OrderLineItemTag->addChild('Id',$line_items_single['id']);
                $OrderLineItemTag->addChild('Name',$line_items_single['name']);
                $OrderLineItemTag->addChild('ProductId',$line_items_single['product_id']);
                $OrderLineItemTag->addChild('SKU',$line_items_single['sku']);
                $OrderLineItemTag->addChild('Quantity',$line_items_single['quantity']);
                $OrderLineItemTag->addChild('Price',$line_items_single['price']);
                $OrderLineItemTag->addChild('Subtotal',$line_items_single['subtotal']);
                $OrderLineItemTag->addChild('SubtotalTax',$line_items_single['subtotal_tax']);
                $OrderLineItemTag->addChild('Total',$line_items_single['total']);
                $OrderLineItemTag->addChild('TotalTax',$line_items_single['total_tax']);
            }
        }

        $newsXML->addChild('ShipmentNumber',$shipment_number);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($newsXML->asXML());

        return $domXML = $dom->saveXML();
        $dom->save($file);
        upload_xml_file_in_sftp($order_id,'/wp-orders-exports/order-'.$order_id.'.xml');
    }

}

function is_kco_test_mode() {
    $woocommerce_kco_settings = get_option('woocommerce_kco_settings');

    if($woocommerce_kco_settings['testmode'] == 'yes') {
        return true;
    } else {
        return false;
    }
}