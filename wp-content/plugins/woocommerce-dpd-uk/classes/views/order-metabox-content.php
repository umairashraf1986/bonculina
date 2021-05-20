<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly */ ?>
<?php
/** @var WPDesk_WooCommerce_DPD_UK $dpd_uk */
/** @var WC_Order $order */
/** @var WPDesk_Flexible_Shipping_Shipment_dpd_uk $shipment */
/** @var boolean $label_available */
/** @var string $tracking_url */
/** @var string $dpd_uk_package_number */

$reference_max_length = WPDesk_WooCommerce_DPD_UK_Shipping_Method::REFERENCE_MAX_LENGTH;
$delivery_max_length = WPDesk_WooCommerce_DPD_UK_Shipping_Method::DELIVERY_MAX_LENGTH;
?>
<div class="dpd_uk-package">
	<?php
	try {
		$key   = 'dpd_uk_service';
		$args  = array(
			'label'       => __( 'Service', 'woocommerce-dpd-uk' ),
			'id'          => 'dpd_uk_service_' . $id,
			'type'        => 'select',
			'options'     => $dpd_uk->get_services_for_shipment( $shipment ),
			'class'       => array( 'dpd_uk-service' ),
			'input_class' => array( 'dpd_uk-service' ),
		);
		$value = '';
		if ( isset( $dpd_uk_service ) ) {
			$value = $dpd_uk_service;
		}
		if ( $disabled ) {
			$args['custom_attributes'] = array( 'disabled' => 'disabled' );
		}
		woocommerce_form_field( $key, $args, $value );
	}
	catch ( Exception $e ) {
		$disabled = true;
		?>
		<p>
			<span class="dpd_uk_error"><?php _e( 'Services not available: ', 'woocommerce-dpd-uk' ); ?><?php echo $e->getMessage(); ?></span>
		</p>
		<?php
	}

	?>

	<?php
	$key = 'dpd_uk_number_of_parcels';
	$args = array(
		'label'			=> __( 'Number of packages', 'woocommerce-dpd-uk' ),
		'id'			=>  'dpd_uk_number_of_parcels_' . $id,
		'type' 			=> 'number',
		'input_class'	=> array( 'dpd_uk-number-of-parcels' ),
		'custom_attributes' => array(
			'min' 	=> 1,
			'step'	=> '1',
		)
	);
	$value = '1';
	if ( isset( $dpd_uk_number_of_parcels ) ) {
		$value = $dpd_uk_number_of_parcels;
	}
	if ( $disabled ) {
		$args['input_class'][] = 'dpd_uk-disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<?php
	$key = 'dpd_uk_weight';
	$args = array(
		'label'			=> __( 'Total weight (kg)', 'woocommerce-dpd-uk' ),
		'id'			=>  'dpd_uk_weight_' . $id,
		'type' 			=> 'number',
		'input_class'	=> array( 'dpd_uk-weight' ),
		'custom_attributes' => array(
			'min' 	=> 0,
			'step'	=> '0.001',
		)
	);
	$value = '0';
	if ( isset( $dpd_uk_weight ) ) {
		$value = $dpd_uk_weight;
	}
	if ( $disabled ) {
		$args['input_class'][] = 'dpd_uk-disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<?php
	$key = 'dpd_uk_liability';
	$args = array(
		'label'			=> __( 'Liability', 'woocommerce-dpd-uk' ),
		'id'			=>  'dpd_uk_liability_' . $id,
		'type' 			=> 'checkbox',
		'input_class'	=> array( 'dpd_uk-liability' ),
	);
	$value = '0';
	if ( isset( $dpd_uk_liability ) ) {
		$value = $dpd_uk_liability;
	}
	if ( $disabled ) {
		$args['input_class'][] = 'dpd_uk-disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>
	<?php
	$key = 'dpd_uk_liability_value';
	$args = array(
		'label'			=> __( 'Liability value', 'woocommerce-dpd-uk' ),
		'id'			=>  'dpd_uk_liability_value_' . $id,
		'type' 			=> 'number',
		'class'			=> array( 'dpd_uk-liability-value' ),
		'custom_attributes' => array(
			'min' 	=> 0,
			'step'	=> '0.01',
		)
	);
	$value = '';
	if ( isset( $dpd_liability_value ) ) {
		$value = $dpd_liability_value;
	}
	else {
		$value = $order->get_total();
	}
	if ( $disabled ) {
		$args['custom_attributes']['disabled'] = 'disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>
	<?php
	$key = 'dpd_uk_consolidate';
	$args = array(
		'label'			=> __( 'Consolidation', 'woocommerce-dpd-uk' ),
		'id'			=>  'dpd_uk_consolidate_' . $id,
		'type' 			=> 'checkbox',
		'input_class'	=> array( 'dpd_uk-consolidate' ),
	);
	$value = null === $dpd_uk_consolidate ? 1 : (int) $dpd_uk_consolidate;
	if ( $disabled ) {
		$args['input_class'][] = 'dpd_uk-disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>
	<?php
	$key = 'dpd_uk_delivery_instructions';
	$args = array(
		'label'			        => __( 'Delivery instructions', 'woocommerce-dpd-uk' ),
		'id'			        =>  'dpd_uk_delivery_instructions_' . $id,
		'type' 			        => 'textarea',
		'custom_attributes'     => array( 'maxlength' => $delivery_max_length )
	);
	$value = '';
	if ( isset( $dpd_uk_delivery_instructions ) ) {
		$value = $dpd_uk_delivery_instructions;
	}
	if ( $disabled ) {
		$args['custom_attributes']['disabled'] = 'disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<?php
	$key = 'dpd_uk_reference1';
	$args = array(
		'label'			        => __( 'Reference 1', 'woocommerce-dpd-uk' ),
		'id'			        =>  'dpd_uk_reference1_' . $id,
		'type' 			        => 'textarea',
		'custom_attributes'     => array( 'maxlength' => $reference_max_length )
	);
	$value = '';
	if ( isset( $dpd_uk_reference1 ) ) {
		$value = $dpd_uk_reference1;
	}
	if ( $disabled ) {
		$args['custom_attributes']['disabled'] = 'disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<?php
	$key = 'dpd_uk_reference2';
	$args = array(
		'label'			        => __( 'Reference 2', 'woocommerce-dpd-uk' ),
		'id'			        =>  'dpd_uk_reference2_' . $id,
		'type' 			        => 'textarea',
		'custom_attributes'     => array( 'maxlength' => $reference_max_length )
	);
	$value = '';
	if ( isset( $dpd_uk_reference2 ) ) {
		$value = $dpd_uk_reference2;
	}
	if ( $disabled ) {
		$args['custom_attributes']['disabled'] = 'disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<?php
	$key = 'dpd_uk_reference3';
	$args = array(
		'label'			        => __( 'Reference 3', 'woocommerce-dpd-uk' ),
		'id'			        =>  'dpd_uk_reference3_' . $id,
		'type' 			        => 'textarea',
		'custom_attributes'     => array( 'maxlength' => $reference_max_length )
	);
	$value = '';
	if ( isset( $dpd_uk_reference3 ) ) {
		$value = $dpd_uk_reference3;
	}
	if ( $disabled ) {
		$args['custom_attributes']['disabled'] = 'disabled';
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<?php
	$key = 'dpd_uk_parcel_description';
	$args = array(
		'label'			=> __( 'Parcel description', 'woocommerce-dpd-uk' ),
		'id'			=>  'dpd_uk_parcel_description_' . $id,
		'type' 			=> 'textarea',
	);
	$value = '';
	if ( isset( $dpd_uk_parcel_description ) ) {
		$value = $dpd_uk_parcel_description;
	}
	if ( $disabled ) {
		$args['custom_attributes'] = array( 'disabled' => 'disabled' );
	}
	woocommerce_form_field( $key, $args, $value );
	?>

	<p class="dpd_uk_get_request">
		<a href="<?php echo admin_url( '?get_dpd_uk_request=' . $id ); ?>" target="_blank"><?php _e( 'Get request', 'woocommerce-dpd-uk' ); ?></a>
	</p>

	<?php if ( isset( $dpd_uk_status ) && $dpd_uk_status == 'ok' ) : ?>
		<p>
			<?php _e( 'Shipment: ', 'woocommerce-dpd-uk' ); ?> <a target="_blank" href="<?php echo $tracking_url; ?>"><?php echo $dpd_uk_package_number; ?></a>
		</p>
		<p>
			<a target="_blank" href='<?php echo $label_url; ?>' data-id="<?php echo $id; ?>" class="button button-primary dpd_uk-button"><?php _e( 'Get label', 'woocommerce-dpd-uk' ); ?></a>
			<span class="spinner dpd_uk-spinner shipping-spinner"></span>
		</p>
	<?php endif; ?>
    <?php if ( $label_available ) : ?>
        <?php if ( apply_filters( 'flexible_printing', false ) && isset( $settings['flexible_printing_integration'] ) && $settings['flexible_printing_integration'] == 'yes' ) : ?>
            <p>
                <?php echo apply_filters( 'flexible_printing_print_button', '', 'dpd_uk',
                    array(
                        'content' => 'print',
                        'id' => $dpd_uk_package_number,
                        'icon'  => true,
                        'label' => __( 'Print on: %s', 'woocommerce-dpd-uk' ),
                        'data' => array(
                            'shippment_id'          => $id,
                            'dpd_uk_package_number'    => $dpd_uk_package_number,
                        ),
                    )
                ); ?>
            </p>
        <?php endif; ?>
    <?php endif; ?>

	<?php if ( $label_available ) : ?>
		<?php if ( apply_filters( 'flexible_printing', false ) ) : ?>
            <p>
				<?php echo apply_filters( 'flexible_printing_print_button', '', 'dpd_uk',
					array(
						'content' => 'print',
						'id' => str_replace( ', ', '-', $dpd_uk_package_number ),
						'icon'  => true,
						'label' => __( 'Print on: %s', 'woocommerce-dpd-uk' ),
						'data' => array(
							'shippment_id'          => $id,
							'dpd_uk_package_number'    => $dpd_uk_package_number,
						),
					)
				); ?>
            </p>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( !$disabled ) : ?>
		<p>
			<button data-id="<?php echo $id; ?>" class="button button-primary dpd_uk-button dpd_uk-button-create button-shipping"><?php _e( 'Create', 'woocommerce-dpd-uk' ); ?></button>
			<button data-id="<?php echo $id; ?>" class="button dpd_uk-button dpd_uk-button-save button-shipping"><?php _e( 'Save', 'woocommerce-dpd-uk' ); ?></button>
			<span class="spinner dpd_uk-spinner shipping-spinner"></span>
		</p>
	<?php endif; ?>

</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		dpd_uk_init();
	})
</script>
