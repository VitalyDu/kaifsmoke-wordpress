<?php

/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="product_inBasket product_buttons">
    <?
    global $product;
//    $multisklad = get_post_meta($product->get_id(), 'multi_sklad', true);
//    preg_match('/Основной склад&0;(\d*)&1;/', $multisklad, $matches);
//    $product_real_stock_quantity = intval($matches[1]);
    if (is_user_logged_in()) {
        if (strpos(get_user_meta(get_current_user_id(), 'cart', true), $product->get_id() . ',') !== false) {
    ?>
            <a href="/cart" class="btn tertiary active m goToCart_btn" data-productId="<? echo $product->get_id(); ?>" data-productName="<?= $product->get_title() ?>">Список бронирования</a>
        <?
        } elseif ($product->is_in_stock()) {
            // echo apply_filters(
            //     'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
            //     sprintf(
            //         '<a href="%s" data-quantity="%s" class="%s inBasketButton" %s>%s</a>',
            //         esc_url($product->add_to_cart_url()),
            //         esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
            //         esc_attr(isset($args['class']) ? $args['class'] : 'button'),
            //         isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
            //         esc_html("Забронировать")
            //     ),
            //     $product,
            //     $args
            // );
        ?>
            <button class="btn tertiary m addToCardBtn" data-productId="<? echo $product->get_id(); ?>" data-productName="<?= $product->get_title() ?>">Забронировать</button>
        <?
        } else { ?>
            <a href="<?= $product->get_permalink() ?>" class="btn tertiary m inBasketButton">Посмотреть</a>
        <?php
        }
    } else {
        ?>
        <button class="btn tertiary m addToCartNoAuth_btn">Забронировать</button>
    <?
    }
    ?>
</div>