<?php

// Create Shortcode show_filterable_products
// Shortcode: [show_filterable_products]
function create_showfilterableprod_shortcode() {

    ob_start(); ?>

    <div id="productcontainer">
        <div id="filters" class="filter-group">  <a class="button is-checked" data-filter="*">All</a>
            <?php

            $get_terms_args = array (
                'taxonomy' => 'product_cat',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => true
            );

            $get_product_cats = get_terms($get_terms_args);

            foreach ($get_product_cats as $get_product_cat) { 

                if($get_product_cat->slug !== 'package') {
            ?>

                <a class="button" data-filter=".<?php echo $get_product_cat->slug; ?>"><?php echo $get_product_cat->name; ?></a>                

            <?php 

                }
            }

            ?>
        </div>

    <?php
        $products_args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array( array(
                'key'     => '_stock_status',
                'value'   => 'outofstock',
                'compare' => '!=',
            ) ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => array('package'),
                    'operator' => 'NOT IN',
                )
            )
        );

        $products_loop = new WP_Query( $products_args );

        if ( $products_loop->have_posts() ) :
            echo '<ul class="products stage">';
            while ( $products_loop->have_posts() ) : $products_loop->the_post();
            $product_cats = get_the_terms(get_the_ID(), 'product_cat');
            $get_product_terms = '';
            foreach ($product_cats as $product_cat) {
                $get_product_terms .= $product_cat->slug.' ';
            }
            ?>

                <li class="product <?php echo trim($get_product_terms); ?>">
                    <?php do_action('woocommerce_before_shop_loop_item'); ?>
                    <figure>
                        <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="><?php the_title(); ?>" /></a>
                    </figure>
                    <a href="?add-to-cart=<?php echo get_the_ID(); ?>" data-quantity="1" data-product_id="<?php echo get_the_ID(); ?>" data-product_name="<?php the_title(); ?>" class="addtocartbtn add_to_cart_button ajax_add_to_cart">Add to cart</a>
                    <div class="prod__content product-template-default woocommerce">
                        <div class="quantity">
                            <input type="number" class="input-text qty text" step="1" min="1" value="1" title="Qty" size="4" placeholder="" inputmode="numeric">
                        </div>
                        <h3>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php $product = wc_get_product( get_the_ID() ); ?>
                        <span class="totalprice"><?php echo $product->get_price_html(); ?></span>
                    </div>

                </li>
            <?php endwhile;
            echo "</ul>";

        endif;

        wp_reset_postdata();

    ?>
    <p class="no-products" style="text-align: center; display: none;">No products to show</p>

    </div>

    <?php $content = ob_get_clean();
    return $content;

}
add_shortcode( 'show_filterable_products', 'create_showfilterableprod_shortcode' );