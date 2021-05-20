<?php
/**
 * Iframe HTML template.
 *
 * Don't edit this template directly as it will be overwritten with every plugin update.
 * Override this template by copying it to yourtheme/woocommerce/pay360/iframe.php
 *
 * @since  3.0
 * @author VanboDevelops
 */
?>
<iframe
	name="pay360-iframe"
	id="pay360-iframe"
	src="<?php echo $location; ?>"
	frameborder="0"
	width="<?php echo $width; ?>"
	height="<?php echo $height; ?>"
	scrolling="<?php echo $scroll; ?>"
>
	<p>
		<?php echo sprintf(
			__(
				'Your browser does not support iframes.
			 %sClick Here%s to get redirected to Pay360 payment page. ', \WC_Pay360::TEXT_DOMAIN
			),
			'<a href="' . $location . '">',
			'</a>'
		); ?>
	</p>
</iframe>