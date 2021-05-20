<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly */ ?>
<?php
    /** @var string $api_connection_error_description */
    /** @var string $api_connection_no_user_data_description */
    /** @var string $api_message */
    // TODO: refactor.
?>
<script type="text/javascript">
    var dpd_uk_api_user_data = <?php echo json_encode( $api_user_data ); ?>;
    var dpd_uk_api_connection_exists = <?php echo json_encode( $api_connection_exists ); ?>;
    jQuery(document).ready(function($) {
        function dpd_order_status() {
            var auto_create = $('#woocommerce_dpd_uk_auto_create').val() === 'auto';
            $('#woocommerce_dpd_uk_order_status').closest('tr').toggle(auto_create);
        }

        dpd_order_status();

        $('#woocommerce_dpd_uk_auto_create').change(function () {
            dpd_order_status();
        });

        if ( !dpd_uk_api_user_data ) {
            $('.dpd_uk_connection_status').closest('tr').hide();
        }

        var $connection_status_element = $('.connection_status');
        var $description_element = $connection_status_element.parent().find('p.description');

        if ( !dpd_uk_api_connection_exists ) {

            if ( dpd_uk_api_user_data ) {
                $connection_status_element.html('<span class="dpd_uk_connection_error">' + <?php echo json_encode( $api_message ); ?> +'</span>');
                $description_element.html('<?php echo $api_connection_error_description; ?>');
            }
            else {
                $description_element.html('<?php echo $api_connection_no_user_data_description; ?>');
            }
        }
        else {
            $connection_status_element.html( '<span class="dpd_uk_connection_ok">' +  'OK' + '</span>' );
            $description_element.html('');
        }
        if ( !dpd_uk_api_user_data || !dpd_uk_api_connection_exists ) {
            $(
                'input.dpd_uk_settings_sender_details,select.dpd_uk_settings_sender_details,'+
                'input.dpd_uk_settings_printing,select.dpd_uk_settings_printing,'+
                'input.dpd_uk_settings_email_notifications,select.dpd_uk_settings_email_notifications,'+
                'input.dpd_uk_settings_shipments,select.dpd_uk_settings_shipments,'+
				'input.dpd_uk_settings_invoice_details,select.dpd_uk_settings_product_details'
            ).each(function(){
                $(this).closest('table').hide();
                $(this).prop('required',false);
            });
            $('h3.dpd_uk_settings_sender_details,h3.dpd_uk_settings_shipments,h3.dpd_uk_settings_printing,h3.dpd_uk_settings_email_notifications,h3.dpd_uk_settings_invoice_details,h3.dpd_uk_settings_product_details').each(function(){
                $(this).hide()
                    .next().hide();
            });
        }
        $('#dpd_uk_settings').show();

        function dpd_uk_label_action() {
        	$('#woocommerce_dpd_uk_label_action').closest('tr').toggle(($('#woocommerce_dpd_uk_label_format').val() === 'HTML'));
        }

        $('#woocommerce_dpd_uk_label_format').change(function(){
			dpd_uk_label_action();
        });

		dpd_uk_label_action();

		jQuery('select.select2').selectWoo();

    });
</script>
