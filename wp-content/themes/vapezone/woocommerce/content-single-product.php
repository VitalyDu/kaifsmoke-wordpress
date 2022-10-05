<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
remove_action('woocommerce_single_product_summary', 'WC_Structured_Data::generate_product_data()', 60);
?>
<? $average = $product->get_average_rating(); ?>
    <div id="product-<?php the_ID(); ?>" <?php wc_product_class('productCard', $product); ?>>
        <div class="container">
            <div class="productCard_block">
                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 *
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
                do_action('woocommerce_before_single_product_summary');
                ?>

                <div class="productCard_block_description summary entry-summary">

                    <?
                    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
                    do_action('woocommerce_single_product_summary');
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
                    ?>
                    <div class="product_rate productCard_rate">
                        <ul class="productCard_rate__list">
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11"
                                     fill="<? if ($average >= 0.5) { ?>#000000<? } else { ?>none<? } ?>"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                                          stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11"
                                     fill="<? if ($average >= 1.5) { ?>#000000<? } else { ?>none<? } ?>"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                                          stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11"
                                     fill="<? if ($average >= 2.5) { ?>#000000<? } else { ?>none<? } ?>"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                                          stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11"
                                     fill="<? if ($average >= 3.5) { ?>#000000<? } else { ?>none<? } ?>"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                                          stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11"
                                     fill="<? if ($average >= 4.5) { ?>#000000<? } else { ?>none<? } ?>"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                                          stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                                </svg>
                            </li>
                        </ul>
                        <a href="#reviews" class="productCard_rate__showReviews">смотреть отзывы</a>
                    </div>
                    <?
                    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
                    do_action('woocommerce_single_product_summary');
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
                    ?>
                    <?
                    $temp = explode('&1;', get_field('multi_sklad'));
                    $multi_sklad = [];
                    foreach ($temp as $value) {
                        $array = explode('&0;', $value);
                        $multi_sklad[$array[0]] = ($array[1] ?? 0);
                    }
                    unset($temp);

                    $posts = get_posts(array(
                        'numberposts' => -1,
                        'category_name' => 'shops',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_type' => 'post',
                        'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
                    ));

                    $main_sklad = 0;
                    if (!empty($multi_sklad['Основной склад']))
                        $main_sklad = $multi_sklad['Основной склад'];

                    $shops_count = 0;
                    foreach ($posts as $post) {
                        if (get_post_meta(get_the_id(), 'hidden', true) || empty(get_field('id_multisklad')) || empty($multi_sklad[get_field('id_multisklad')])) {
                            continue;
                        }
                        if (intval($multi_sklad[get_field('id_multisklad')]) > 0) {
                            $shops_count++;
                        }
                    }
                    ?>
                    <div class="productCard_stockStatus">
                        <img src="/wp-content/themes/vapezone/assets/images/icons/box.png" alt="">
                        <span class="productCard_stockStatus__text">
                        <?php if ($main_sklad <= 0 && $shops_count <= 0) { ?>
                            Отсутствует на складе и в магазине
                        <?php } else { ?>
                            В наличии
                        <?php } ?></span>
                        <div class="productCard_stockStatus__value" style="display: none;">
                            <?= $main_sklad ?>
                        </div>
                        <div class="productCard_stockStatus__shops"><a href="#multisklad">
                                <?php if ($shops_count > 0) { ?>
                                    в <?= $shops_count ?> магазинах
                                <?php } elseif ($main_sklad > 0) { ?>
                                    только на складе
                                <?php } ?></a></div>
                    </div>
                    <div class="productCard_inBasket">
                        <?
                        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                        do_action('woocommerce_single_product_summary');
                        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                        ?>
                        <!-- <button class="addInBasket btnOrange inBasketButton">В корзину</button> -->
                    </div>
                    <div class="productCard_description">
                        <h4>Описание</h4>
                        <p>
                            <?php the_content(); ?>
                        </p>
                    </div>
                    <? wc_display_product_attributes($product); ?>
                    <?php
                    /**
                     * Hook: woocommerce_single_product_summary.
                     *
                     * @hooked woocommerce_template_single_title - 5
                     * @hooked woocommerce_template_single_rating - 10
                     * @hooked woocommerce_template_single_price - 10
                     * @hooked woocommerce_template_single_excerpt - 20
                     * @hooked woocommerce_template_single_add_to_cart - 30
                     * @hooked woocommerce_template_single_meta - 40
                     * @hooked woocommerce_template_single_sharing - 50
                     * @hooked WC_Structured_Data::generate_product_data() - 60
                     */
                    // do_action('woocommerce_single_product_summary');
                    ?>
                </div>

                <?php
                /**
                 * Hook: woocommerce_after_single_product_summary.
                 *
                 * @hooked woocommerce_output_product_data_tabs - 10
                 * @hooked woocommerce_upsell_display - 15
                 * @hooked woocommerce_output_related_products - 20
                 */
                // do_action('woocommerce_after_single_product_summary');
                ?>
            </div>
        </div>
    </div>
    <section class="advantages">
        <div class="container">
            <div class="advantages_block">
                <div class="advantages_block__advantage">
                    <div class="advantage_icon"><img
                                src="/wp-content/themes/vapezone/assets/images/icons/deliveryCar.png" alt=""></div>
                    <div class="advantage_title">Бесплатная</div>
                    <div class="advantage_description">Доставка в магазин</div>
                    <div class="advantage_fullDescription">
                        <p>Доставим товар с основого склада в любой магазин KAIF SMOKE в течение 3-7 дней бесплатно</p>
                    </div>
                </div>
                <div class="advantages_block__advantage">
                    <div class="advantage_icon"><img src="/wp-content/themes/vapezone/assets/images/icons/bankCard.png"
                                                     alt=""></div>
                    <div class="advantage_title">Оплата</div>
                    <div class="advantage_description">В магазине при получении</div>
                    <div class="advantage_fullDescription">
                        <p>В наших магазин вы можете расплатиться наличными, банковской картой или комбооплатой</p>
                    </div>
                </div>
                <div class="advantages_block__advantage">
                    <div class="advantage_icon"><img
                                src="/wp-content/themes/vapezone/assets/images/icons/sertificates.png" alt=""></div>
                    <div class="advantage_title">Сертифицированные</div>
                    <div class="advantage_description">Товары</div>
                    <div class="advantage_fullDescription">
                        <p>Мы работаем только с проверенными поставщиками, а в случае брака обменяем товар на новый</p>
                    </div>
                </div>
                <div class="advantages_block__advantage">
                    <div class="advantage_icon"><img src="/wp-content/themes/vapezone/assets/images/icons/sale.png"
                                                     alt=""></div>
                    <div class="advantage_title">Лучшая</div>
                    <div class="advantage_description">Цена</div>
                    <div class="advantage_fullDescription">
                        <p>В нашей сети действует бонусная система – cashback на товары без никотина</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="shops" id="multisklad">
        <div class="shops-top-nav">
        <span class="city">
            <div class="city-marker">
                <svg width="14" height="19" viewBox="0 0 14 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 6.6C13 9.91371 7 17.4 7 17.4C7 17.4 1 9.91371 1 6.6C1 3.28629 3.68629 0.599998 7 0.599998C10.3137 0.599998 13 3.28629 13 6.6Z"
                          stroke="#000000"/>
                    <path d="M9.4 6.6C9.4 7.92548 8.32548 9 7 9C5.67452 9 4.6 7.92548 4.6 6.6C4.6 5.27451 5.67452 4.2 7 4.2C8.32548 4.2 9.4 5.27451 9.4 6.6Z"
                          stroke="#000000"/>
                </svg>
            </div>
            Санкт-Петербург
        </span>

            <div class="sort-container">
                <button class="sort">
                    Сортировать
                    <div class="arrow">
                        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.55375 0.246544C1.35849 0.0512821 1.0419 0.0512821 0.846642 0.246544C0.65138 0.441806 0.65138 0.758389 0.846642 0.953651L1.55375 0.246544ZM6.0002 5.4001L5.64664 5.75365L6.0002 6.1072L6.35375 5.75365L6.0002 5.4001ZM11.1537 0.953651C11.349 0.758389 11.349 0.441806 11.1537 0.246544C10.9585 0.0512821 10.6419 0.0512821 10.4466 0.246544L11.1537 0.953651ZM0.846642 0.953651L5.64664 5.75365L6.35375 5.04654L1.55375 0.246544L0.846642 0.953651ZM6.35375 5.75365L11.1537 0.953651L10.4466 0.246544L5.64664 5.04654L6.35375 5.75365Z"
                                  fill="#000000"/>
                        </svg>
                    </div>
                </button>
                <div class="sort-menu">
                    <span>От А до Я</span>
                    <span>От Я до А</span>
                </div>
            </div>

            <div class="searchbar">
                <div class="findicon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.6464 15.3536C14.8417 15.5488 15.1583 15.5488 15.3536 15.3536C15.5488 15.1583 15.5488 14.8417 15.3536 14.6464L14.6464 15.3536ZM10.8704 6.18518C10.8704 8.77274 8.77274 10.8704 6.18518 10.8704V11.8704C9.32503 11.8704 11.8704 9.32503 11.8704 6.18518H10.8704ZM6.18518 10.8704C3.59763 10.8704 1.5 8.77274 1.5 6.18518H0.5C0.5 9.32503 3.04534 11.8704 6.18518 11.8704V10.8704ZM1.5 6.18518C1.5 3.59763 3.59763 1.5 6.18518 1.5V0.5C3.04534 0.5 0.5 3.04534 0.5 6.18518H1.5ZM6.18518 1.5C8.77274 1.5 10.8704 3.59763 10.8704 6.18518H11.8704C11.8704 3.04534 9.32503 0.5 6.18518 0.5V1.5ZM9.49811 10.2052L14.6464 15.3536L15.3536 14.6464L10.2052 9.49811L9.49811 10.2052Z"
                              fill="#000000"/>
                    </svg>
                </div>
                <input type="text" id="suggest" placeholder="Поиск по магазинам"/>
            </div>
        </div>

        <div class="mapContainer">
            <div class="overlayMap"></div>
            <div id="map" style="width: 100%; height: 450px"></div>
        </div>


        <!-- Табличка -->

        <div class="shops-table" id="multisklad">
            <div class="table-header">
                <span>Кол-во</span>
                <span>Адрес</span>
                <span>Станция метро</span>
                <span>Режим работы</span>
                <span>Телефон</span>
                <span>Действие</span>
            </div>
            <?php
            $hide_multisklad = true;
            foreach ($posts as $post) {
                if (get_post_meta(get_the_id(), 'hidden', true) || empty(get_field('id_multisklad')) || empty($multi_sklad[get_field('id_multisklad')]) || $multi_sklad[get_field('id_multisklad')] < 0) {
                    continue;
                }
                $hide_multisklad = false;
                setup_postdata($post);
                ?>
                <div class="table-item" data-shopaddress="<?= get_post_meta(get_the_id(), 'address', true) ?>">
                    <span><?= $multi_sklad[get_field('id_multisklad')] ?> шт</span>
                    <span><b><?= $post->post_title ?></b></span>
                    <span data-color="<?= get_post_meta(get_the_id(), 'metro_color', true) ?>"><svg width="14"
                                                                                                    height="14"
                                                                                                    viewBox="0 0 14 14"
                                                                                                    fill="none"
                                                                                                    xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.5 7C13.5 10.5899 10.5899 13.5 7 13.5C3.41015 13.5 0.5 10.5899 0.5 7C0.5 3.41015 3.41015 0.5 7 0.5C10.5899 0.5 13.5 3.41015 13.5 7Z"
                              fill="#E9420D" stroke="#E9420D" stroke-width="0.8"/>
                        <path d="M5.41054 5.26242C5.29062 4.88154 4.9651 4.92329 4.74254 5.10069C4.19429 5.57546 3.48042 6.572 3.9373 8.00694C4.31993 9.22259 5.91207 10 5.91207 10H4.05606C4.05606 10 3.09773 8.99303 3.01206 7.80869C2.90356 6.31131 3.54323 5.36694 4.28566 4.75131C4.98239 4.17217 5.64486 4 5.64486 4L7 8.1625L8.35514 4C8.35514 4 9.01761 4.17217 9.71434 4.75131C10.4568 5.36694 11.0964 6.31131 10.9879 7.80869C10.9023 8.99303 9.94394 10 9.94394 10H8.08793C8.08793 10 9.67994 9.22259 10.0627 8.00694C10.5196 6.57216 9.80571 5.57563 9.25746 5.10069C9.03473 4.92329 8.70921 4.88154 8.58946 5.26242C8.17827 6.47808 7.00017 9.96117 7.00017 9.96117C7.00017 9.96117 5.82207 6.47691 5.41088 5.26242H5.41054Z"
                              fill="white"/>
                    </svg>
                    <?= get_post_meta(get_the_id(), 'metro', true) ?>
                </span>
                    <span><?= get_post_meta(get_the_id(), 'schedule', true) ?></span>
                    <span><a href="tel:+<?= preg_replace('/[^0-9]/', '', get_post_meta(get_the_id(), 'phone', true)) ?>"><?= get_post_meta(get_the_id(), 'phone', true) ?></a></span>
                    <?php if (get_current_user_id() > 0 && $multi_sklad[get_field('id_multisklad')] > 0) { ?>
                        <span class="book reserveProductInShop" data-productId="<?= $product->get_id() ?>"
                              data-address="<?= get_post_meta(get_the_id(), 'address', true) ?>"
                              data-multisklad="<?= get_field('id_multisklad') ?>"
                              data-quantity="<?= $multi_sklad[get_field('id_multisklad')] ?>"><svg width="21"
                                                                                                   height="19"
                                                                                                   viewBox="0 0 21 19"
                                                                                                   fill="none"
                                                                                                   xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.9998 7H2M15.9998 7H16.9998V5H13.4998M15.9998 7L15.6226 10.0176M2 7L3 15H12.126M2 7H1V5H4.49976M5.5 9.5L5.99976 12.5M8.99976 12.5V9.5M12.5 9.5L11.9998 12.5M4.49976 5L6.49976 1M4.49976 5H13.4998M13.4998 5L11.4998 1M16 11.5V14H17.5M15.6226 10.0176C13.5904 10.2078 12 11.9181 12 14C12 14.3453 12.0438 14.6804 12.126 15M15.6226 10.0176C15.7468 10.0059 15.8727 10 16 10C18.2091 10 20 11.7909 20 14C20 16.2091 18.2091 18 16 18C14.1362 18 12.5701 16.7252 12.126 15"
                                  stroke="#EF7D00" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <button>Забронировать</button>
                    </span>
                    <?php } ?>
                </div>
                <?php
            }
            wp_reset_postdata(); // сброс

            if ($hide_multisklad) { ?>
                <style>
                    #multisklad {
                        display: none;
                    }
                </style>
            <?php }
            ?>
        </div>
    </div>
    <!-- <div class="container" id="multisklad">
    <table>
        <thead>
            <th>Кол-во</th>
            <th>Адрес</th>
            <th>Станция метро</th>
            <th>Режим работы</th>
            <th>Телефон</th>
            <th>Действие</th>
        </thead>
        <tbody>
            <?php
    $temp = explode('&1;', get_field('multi_sklad'));
    $multi_sklad = [];
    foreach ($temp as $value) {
        $array = explode('&0;', $value);
        $multi_sklad[$array[0]] = ($array[1] ?? 0);
    }
    unset($temp);

    $posts = get_posts(array(
        'numberposts' => -1,
        'category_name' => 'shops',
        'orderby' => 'date',
        'order' => 'DESC',
        'post_type' => 'post',
        'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
    ));
    foreach ($posts as $post) {
        if (get_post_meta(get_the_id(), 'hidden', true) || empty(get_field('id_multisklad')) || empty($multi_sklad[get_field('id_multisklad')])) {
            continue;
        }
        setup_postdata($post);
        ?>
                <tr class="shop">
                    <td><?= $multi_sklad[get_field('id_multisklad')] ?> шт</td>
                    <td><?= get_post_meta(get_the_id(), 'address', true) ?></td>
                    <td><?= get_post_meta(get_the_id(), 'станция_метро', true) ?></td>
                    <td><?= get_post_meta(get_the_id(), 'schedule', true) ?></td>
                    <td>
                        <a href="tel:+<?= preg_replace('/[^0-9]/', '', get_post_meta(get_the_id(), 'phone', true)) ?>"><?= get_post_meta(get_the_id(), 'phone', true) ?></a>
                    </td>
                    <?php if (get_current_user_id() > 0) { ?>
                        <td>
                            <button class="reserveProductInShop" data-productId="<?= $product->get_id() ?>" data-address="<?= get_post_meta(get_the_id(), 'address', true) ?>" data-multisklad="<?= get_field('id_multisklad') ?>">
                                Забронировать
                            </button>
                        </td>
                    <?php } else { ?>
                        <td>
                            <button>Забронировать (неактивно)</button>
                        </td>
                    <?php } ?>
                </tr>
                <script>
                    // function reserve_product(product_id, address, shop_multisklad_id) {
                    //     $.ajax({
                    //         url: AJAXURL,
                    //         dataType: 'json',
                    //         method: 'POST',
                    //         data: {
                    //             action: 'vz_create_order',
                    //             product_id: product_id,
                    //             address: address,
                    //             shop_multisklad_id: shop_multisklad_id
                    //         },
                    //         success: (data) => {
                    //             alert(JSON.stringify(data));
                    //         },
                    //         error: (data) => {
                    //             alert(JSON.stringify(data));
                    //         },
                    //     });
                    // }
                    // function reserveProductInShop(productId, address, multisklad) {
                    //     $(".overlay").fadeIn();
                    //     $(".reserveModal").css("display", "flex").hide().fadeIn();
                    //     $(".reserveModal").attr("data-productid", productId);
                    //     $(".reserveModal").attr("data-address", address);
                    //     $(".reserveModal").attr("data-multisklad", multisklad);
                    // }
                    // $(".reserveModal_button__cancel").on("click", function() {
                    //     $(".overlay").fadeOut();
                    //     $(".reserveModal").fadeOut();
                    // });
                    // $(".reserveModal_button__reserve").on("click", function() {
                    //     var product_id = $(this).parents(".reserveModal").attr("data-productid");
                    //     var address = $(this).parents(".reserveModal").attr("data-address");
                    //     var shop_multisklad_id = $(this)
                    //     .parents(".reserveModal")
                    //     .attr("data-multisklad");
                    //     console.log(product_id);
                    //     console.log(address);
                    //     console.log(shop_multisklad_id);
                    //     $.ajax({
                    //         url: AJAXURL,
                    //         dataType: "json",
                    //         method: "POST",
                    //         data: {
                    //             action: "vz_create_order",
                    //             product_id: product_id,
                    //             address: address,
                    //             shop_multisklad_id: shop_multisklad_id,
                    //         },
                    //         success: (data) => {
                    //             alert(JSON.stringify(data));
                    //         },
                    //         error: (data) => {
                    //             alert(JSON.stringify(data));
                    //         },
                    //     });
                    // });
                </script>
            <?php
    }
    wp_reset_postdata(); // сброс
    ?>
        </tbody>
    </table>
</div> -->
<?php
/**
 * Hook: woocommerce_after_single_product_summary.
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 */
// remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
// remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
// do_action('woocommerce_after_single_product_summary');
?>
<?php
$product_tabs = apply_filters('woocommerce_product_tabs', array());
?>
<?php foreach ($product_tabs as $key => $product_tab) : ?>
    <?php
    if (isset($product_tab['callback']) && $key == 'reviews') {
        call_user_func($product_tab['callback'], $key, $product_tab);
    }
    ?>
<?php endforeach; ?>
    <div class="reserveModal" data-productid="" data-address="" data-multisklad="">
    <span class="reserveModal_close">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 8L16 16M16 8L12 12L8 16" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
        <div class="reserveModal_content">
            <span class="title">Вы уверены?</span>
            <p>
                Забронировать <?php the_title(); ?> по адресу <b class="reserveModal_content__address"></b>?
            </p>
        </div>
        <div class="reserveModal_fields">
            <div class="inputField">
                <label>Имя</label>
                <input type="text" name="user_firstName">
            </div>
            <div class="inputField">
                <label>Телефон</label>
                <input type="text" name="user_phone">
            </div>
            <div class="inputField quantity">
                <label>Количество</label>
                <div class="field">
                    <span class="reserveQuantity_minus">-</span>
                    <input type="number" value="1" class="reserveQuantity" data-max="1">
                    <span class="reserveQuantity_plus">+</span>
                </div>
            </div>
        </div>
        <div class="reserveModal_buttons">
            <button class="reserveModal_button__cancel">
                Отмена
            </button>
            <button class="reserveModal_button__reserve">
                Да, я хочу забронировать
            </button>
        </div>
        <div class="reserveModal_errors">
        </div>
    </div>

    <style>
        .ymaps-2-1-79-ground-pane {
            -ms-filter: grayscale(1);
            -webkit-filter: grayscale(1);
            -moz-filter: grayscale(1);
            -o-filter: grayscale(1);
            filter: grayscale(1);
        }

        .mapContainer {
            position: relative;
        }

        .searchbar ymaps {
            width: 270px !important;
            left: auto !important;
        }

        .searchbar ymaps:nth-child(1) {
            margin-top: 20px;
        }

        .ymaps-2-1-79-search__suggest-item {
            white-space: normal;
        }


        .shop-table .active {
            background: #f3f3f3 !important;
        }

        .shops-top-nav,
        .mapContainer {
            padding-left: 20px;
            padding-right: 20px;
        }

        .overlayMap {
            width: 100%;
            height: 100%;
            background: #fff;
            opacity: .9;
            position: absolute;
            top: 0;
            left: 0;
            position: absolute;
            mix-blend-mode: difference;
            z-index: 1 !important;
            pointer-events: none;
        }

        .ymaps-2-1-79-map-copyrights-promo,
        .vz-balloonContentBody__link {
            /* display: block; */
        }

        .ymaps-2-1-79-balloon__layout {
            z-index: 3000;
        }

        .ymaps-2-1-79-balloon__layout,
        .ymaps-2-1-79-balloon__tail {

            filter: invert(1);
        }

        .vz-balloonContentBody__book {
            display: flex;
            align-items: center;

        }

        .vz-balloonContentBody__book button {
            text-decoration: underline;
            border: none;

            padding: 0;
            color: #ff6900;

        }

        .shops-table .table-item span {
            white-space: normal;
            width: 90%;
        }
    </style>
<?php do_action('woocommerce_after_single_product'); ?>