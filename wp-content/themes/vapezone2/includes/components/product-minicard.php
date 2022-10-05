<?php
$product = wc_get_product(get_the_id()); // это надеюсь временно, для некоторых функций с объектом WC_PRODUCT{}
?>
<div class="productsWrapper_product product_miniCard" data-productid="<?php the_ID(); ?>"
     data-productName="<?= $product->get_title() ?>">
    <?php
    $link = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);
    ?>
    <div class="product_addToFavorites addToFavoritesIcon">
        <button>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2001 8.61987C19.2001 12.2199 12 18.7314 12 18.7314C12 18.7314 4.80005 12.2199 4.80005 8.61987C4.80005 4.69111 9.60005 3.16234 12 7.0911C14.4 3.16234 19.2 5.01987 19.2001 8.61987Z"
                      stroke="#1D1D1B"/>
            </svg>
        </button>
    </div>
    <div class="product_image">
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_open - 10
         */
        do_action('woocommerce_before_shop_loop_item');

        /**
         * Hook: woocommerce_before_shop_loop_item_title.
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        do_action('woocommerce_before_shop_loop_item_title');
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        do_action('woocommerce_after_shop_loop_item');
        /**
         * Hook: woocommerce_after_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_close - 5
         * @hooked woocommerce_template_loop_add_to_cart - 10
         */
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        do_action('woocommerce_after_shop_loop_item');
        ?>
    </div>
    <div class="product_nameRateProperties">
        <?
        /**
         * Hook: woocommerce_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_product_title - 10
         */
        // do_action('woocommerce_shop_loop_item_title');
        ?>
        <div class="product_rate">
            <ul class="product_rate__list">
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
            </ul>
        </div>
        <a href="<?php echo $link ?>" class="product_name">
            <? the_title(); ?>
        </a>
        <div class="product_properties">
            <?php
            $product_attributes = wc_display_product_attributes($product, 1);
            $allowed_attr_filters = explode(';', get_field('allowed_product_attr', 'option'));
            if ($product_attributes) {
                foreach ($product_attributes as $product_attribute_key => $product_attribute) : ?>
                    <? if (in_array(str_replace('attribute_pa_', '', $product_attribute_key), $allowed_attr_filters)) { ?>
                        <div class="product_properties__property">
                            <span class="property_name"><?php echo wp_kses_post($product_attribute['label']); ?>:</span>
                            <span class="property_value"><?php echo wp_kses_post($product_attribute['value']); ?></span>
                        </div>
                    <? } ?>
                <?php endforeach;
            } ?>

        </div>
    </div>
    <div class="product_priceAddToCart">
        <?

        /**
         * Hook: woocommerce_after_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        do_action('woocommerce_after_shop_loop_item_title');

        /**
         * Hook: woocommerce_after_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_close - 5
         * @hooked woocommerce_template_loop_add_to_cart - 10
         */
        add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        do_action('woocommerce_after_shop_loop_item');
        ?>
    </div>
</div>