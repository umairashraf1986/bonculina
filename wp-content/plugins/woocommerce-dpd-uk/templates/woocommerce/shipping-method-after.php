<tr class="shipping dpd_uk-shipping">
	<th><?php _e( 'Delivery instructions', 'woocommerce-dpd-uk' ); ?></th>
	<td>
		<?php
		$key = 'dpd_uk_delivery_instructions';
		$args = array(
			'id'			    =>  'dpd_uk_delivery_instructions',
			'type' 			    => 'textarea',
			'class'			    => array( 'dpd_uk-delivery-instructions' ),
            'custom_attributes' => array( 'maxlength' => WPDesk_WooCommerce_DPD_UK_Shipping_Method::DELIVERY_MAX_LENGTH ),
		);
		$value = '';
		woocommerce_form_field( $key, $args, $value );
		?>
	</td>
</tr>
