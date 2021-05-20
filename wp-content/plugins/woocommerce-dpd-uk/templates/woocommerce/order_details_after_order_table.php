<?php
/**
 * Shipment tracking links
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-dpd/woocommerce/email_after_order_table.php
 *
 * @author  WP Desk
 * @version 1.0.0
 */ 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<header>
	<h2><?php _e( 'Shipment', 'woocommerce-dpd-uk' ); ?></h2>
</header>
<?php
	foreach ( $dpd_uk_packages as $dpd_uk_package ) {
		?>
		<p>
			<?php _e( 'Track shipment: ', 'woocommerce-dpd-uk' ); ?><a target="_blank" href="<?php echo $dpd_uk_package['tracking_url']; ?>"><?php echo $dpd_uk_package['shipment_id']; ?></a>
		</p>
		<?php
	}
