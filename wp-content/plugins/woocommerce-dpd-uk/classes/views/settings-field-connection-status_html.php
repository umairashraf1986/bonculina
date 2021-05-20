<tr valign="top" class="dpd_uk_connection_status_row">
	<th scope="row" class="titledesc" valign="top">
		<?php echo $this->get_tooltip_html( $data ); ?>
		<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
			<span class="connection_status"></span>
			<?php echo $this->get_description_html( $data ); ?>
		</fieldset>
	</td>
</tr>