<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author          WooThemes
 * @package     WooCommerce/Templates
 * @version     3.2.0
 */
if (!defined('ABSPATH')) {
    exit;
}

?>
<tr class="shipping msunifaunonline-shipping">
    <td colspan="2">
        <strong><?php echo wp_kses_post($package_name); ?></strong>

<?php if (\Mediastrategi_UnifaunOnline::getOption('custom_pick_up_show_zip_code_selector') === 'yes') { ?>
        <div class="custom-region-selector">
            <input tabindex="2" class="action" value="<?php echo __('Get options', 'msunifaunonline'); ?>" type="button" />
            <div class="wrapper">
                <input tabindex="1" class="zip-code-selector" name="msuo_zip_code_select" placeholder="<?php echo __('Enter your zip code..', 'msunifaunonline'); ?>" type="text" />
            </div>
        </div>
<?php } ?>
            
        <div class="package" data-title="<?php echo esc_attr($package_name); ?>">
            <?php if (1 < count($available_methods)) : ?>
                <ul id="shipping_method">
                    <?php foreach ($available_methods as $method) : ?>
                        <li class="<?php echo checked($method->id, $chosen_method, false) ? 'selected' : 'not-selected' ?>">
                            <?php
                            printf(
                                '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s /><label for="shipping_method_%1$d_%2$s">%5$s<span class="price">%6$s</label>',
                                $index,
                                sanitize_title($method->id),
                                esc_attr($method->id),
                                checked($method->id, $chosen_method, false),
                                $method->label,
                                Mediastrategi_UnifaunOnline_wc_method_price($method)
                            );
                            do_action(
                                'woocommerce_after_shipping_rate',
                                $method,
                                $index
                            );
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif ( 1 === count( $available_methods ) ) :  ?>
                <?php
                $method = current( $available_methods );
                printf( '<ul id="shipping_method"><li class="selected"><label>%3$s<span class="price">%4$s<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" /></label>', $index, esc_attr( $method->id ), $method->label, Mediastrategi_UnifaunOnline_wc_method_price($method) );
                do_action( 'woocommerce_after_shipping_rate', $method, $index );
                ?>
                </li></ul>
            <?php elseif ( WC()->customer->has_calculated_shipping() ) : ?>
                <?php echo apply_filters( is_cart() ? 'woocommerce_cart_no_shipping_available_html' : 'woocommerce_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) ); ?>
            <?php elseif ( ! is_cart() ) : ?>
                <?php echo wpautop( __( 'Enter your full address to see shipping costs.', 'woocommerce' ) ); ?>
            <?php endif; ?>

            <?php if ( $show_package_details ) : ?>
                <?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
            <?php endif; ?>

            <?php if ( ! empty( $show_shipping_calculator ) ) : ?>
                <?php woocommerce_shipping_calculator(); ?>
            <?php endif; ?>
        </div>
    </td>
</tr>
