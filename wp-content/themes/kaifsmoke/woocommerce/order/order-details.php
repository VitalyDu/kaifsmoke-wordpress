<?php

/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined('ABSPATH') || exit;

$order = wc_get_order($order_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if (!$order) {
    return;
}

$order_data = VZUser::getOrder($order_id);
if (empty($order_data['out']['orders'][0]))
    return;
$order_data = $order_data['out']['orders'][0];


?>
<section class="orderPage">
    <div class="container">
        <div class="section_title">
            <h2>Моя бронь</h2>
        </div>
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/my-account">Мой аккаунт</a></li>
                <li><a href="/my-orders">Моя бронь</a></li>
                <li><a href="#">Заказ #<?= $order_id ?></a></li>
            </ul>
        </div>
        <div class="orderPage_block">
            <div class="orderPage_shortInf">
                <div class="orderPage_shortInf__item">
					<span class="label">
						Номер
					</span>
                    <span class="value">
						#<?= $order_id ?>
					</span>
                </div>
                <div class="orderPage_shortInf__item">
					<span class="label">
						Статус
					</span>
                    <span class="value">
                    <?php
                    $status = [
                        'class' => '',
                        'name' => ''
                    ];
                    switch ($order_data['status']) {
                        case 'processing': ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
							<circle cx="10" cy="10" r="10" fill="#31A337"/>
							<path d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z"
                                  fill="white"/>
						</svg>
                            <span class="value_label wait">Открыт</span>
                            <?php
                            break;
                        case 'pending': ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
							<circle cx="10" cy="10" r="10" fill="#31A337"/>
							<path d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z"
                                  fill="white"/>
						</svg>
                            <span class="value_label wait">Ожидает покупателя</span>
                            <?php
                            break;
                        case 'cancelled': ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="10" fill="#E9420D"/>
                                <path d="M7 7L13 13M13 7L10 10L7 13" stroke="white" stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                            <span class="value_label cancel">Отменена</span>
                            <?php
                            break;
                        case 'completed': ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="10" fill="#1D1D1B"/>
                                <path d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z"
                                      fill="white"/>
                            </svg>
                            <span class="value_label">Завершено</span>
                            <?php
                            break;
                        case 'refunded': ?>

                            <?php
                            break;
                        case 'failed': ?>

                            <?php
                            break;
                        case 'on-hold': ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="10" fill="#2074C1"/>
                                <path d="M14 10L14.7071 10.7071C15.0976 10.3166 15.0976 9.68342 14.7071 9.29289L14 10ZM10.2929 12.2929C9.90237 12.6834 9.90237 13.3166 10.2929 13.7071C10.6834 14.0976 11.3166 14.0976 11.7071 13.7071L10.2929 12.2929ZM11.7071 6.29289C11.3166 5.90237 10.6834 5.90237 10.2929 6.29289C9.90237 6.68342 9.90237 7.31658 10.2929 7.70711L11.7071 6.29289ZM13.2929 9.29289L10.2929 12.2929L11.7071 13.7071L14.7071 10.7071L13.2929 9.29289ZM14.7071 9.29289L11.7071 6.29289L10.2929 7.70711L13.2929 10.7071L14.7071 9.29289Z"
                                      fill="white"/>
                                <path d="M10 10L10.7071 10.7071C11.0976 10.3166 11.0976 9.68342 10.7071 9.29289L10 10ZM6.29289 12.2929C5.90237 12.6834 5.90237 13.3166 6.29289 13.7071C6.68342 14.0976 7.31658 14.0976 7.70711 13.7071L6.29289 12.2929ZM7.70711 6.29289C7.31658 5.90237 6.68342 5.90237 6.29289 6.29289C5.90237 6.68342 5.90237 7.31658 6.29289 7.70711L7.70711 6.29289ZM9.29289 9.29289L6.29289 12.2929L7.70711 13.7071L10.7071 10.7071L9.29289 9.29289ZM10.7071 9.29289L7.70711 6.29289L6.29289 7.70711L9.29289 10.7071L10.7071 9.29289Z"
                                      fill="white"/>
                            </svg>
                            <span class="value_label delivery">Доставка в магазин</span>
                            <?php
                            break;
                    }
                    ?>

					</span>
                </div>
                <div class="orderPage_shortInf__item">
					<span class="label">
						Ожидание
					</span>
                    <span class="value">
						#<?= $order_id ?>
					</span>
                </div>
                <div class="orderPage_shortInf__item">
					<span class="label">
						Позиций
					</span>
                    <span class="value">
						<?= $order_data['product_count'] ?>
					</span>
                </div>
                <div class="orderPage_shortInf__item">
					<span class="label">
						Сумма
					</span>
                    <span class="value">
						<?= $order_data['price'] ?>
					</span>
                </div>
                <div class="orderPage_shortInf__item">
					<span class="label">
						Дата
					</span>
                    <span class="value">
						<?= $order_data['date']->date('d.m.Y') ?>
					</span>
                </div>
            </div>

            <!-- <div class="orderPage_map">
    <div class="map_information">
      <span class="address" id="shop_address">Загрузка...</span>
      
      <div class="map_item">
        <span class="min-type">Метро</span>
        <div class="map_item-name">
          <span id="metro_color">
            
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M13.5 7C13.5 10.5899 10.5899 13.5 7 13.5C3.41015 13.5 0.5 10.5899 0.5 7C0.5 3.41015 3.41015 0.5 7 0.5C10.5899 0.5 13.5 3.41015 13.5 7Z" fill="#E9420D" stroke="#E9420D" stroke-width="0.8"/>
<path d="M5.41054 5.26242C5.29062 4.88154 4.9651 4.92329 4.74254 5.10069C4.19429 5.57546 3.48042 6.572 3.9373 8.00694C4.31993 9.22259 5.91207 10 5.91207 10H4.05606C4.05606 10 3.09773 8.99303 3.01206 7.80869C2.90356 6.31131 3.54323 5.36694 4.28566 4.75131C4.98239 4.17217 5.64486 4 5.64486 4L7 8.1625L8.35514 4C8.35514 4 9.01761 4.17217 9.71434 4.75131C10.4568 5.36694 11.0964 6.31131 10.9879 7.80869C10.9023 8.99303 9.94394 10 9.94394 10H8.08793C8.08793 10 9.67994 9.22259 10.0627 8.00694C10.5196 6.57216 9.80571 5.57563 9.25746 5.10069C9.03473 4.92329 8.70921 4.88154 8.58946 5.26242C8.17827 6.47808 7.00017 9.96117 7.00017 9.96117C7.00017 9.96117 5.82207 6.47691 5.41088 5.26242H5.41054Z" fill="white"/>
</svg>

          </span>
          <span id="metro_station">Загрузка...</span>
        </div>
      </div>
      
      <div class="map_item">
        <span class="min-type">Режим работы</span>
        <div class="map_item-name">
          <span>
            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7 3.50024V7.11136H9.5M13.5 7.11136C13.5 10.7012 10.5899 13.6114 7 13.6114C3.41015 13.6114 0.5 10.7012 0.5 7.11136C0.5 3.5215 3.41015 0.611355 7 0.611355C10.5899 0.611355 13.5 3.5215 13.5 7.11136Z" stroke="#1D1D1B" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

          </span>
          <span id="time_schedule">Загрузка...</span>
        </div>
      </div>
      
      <div class="map_item">
        <span class="min-type">Телефон</span>
        <div class="map_item-name">
          <span>
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.10569 3.94523L3.12579 1.96533L1.64077 3.45035C1.14562 3.9455 -0.834276 5.9254 3.6205 10.3802C8.07527 14.8349 10.0552 12.855 10.5505 12.3597L12.0352 10.875L10.0554 8.89498C9.56069 8.39978 9.56046 8.4 9.06571 8.89475C7.58079 10.3797 3.62099 6.41988 5.10592 4.93495C5.60066 4.44021 5.60089 4.43998 5.10569 3.94523Z" stroke="#1D1D1B" stroke-width="0.8" stroke-linejoin="round"/>
            <path d="M12.8996 4.89971C12.8996 5.12062 13.0787 5.29971 13.2996 5.29971C13.5205 5.29971 13.6996 5.12062 13.6996 4.89971H12.8996ZM9.09961 0.299707C8.8787 0.299707 8.69961 0.478793 8.69961 0.699707C8.69961 0.920621 8.8787 1.09971 9.09961 1.09971V0.299707ZM9.09961 1.09971C11.1983 1.09971 12.8996 2.80102 12.8996 4.89971H13.6996C13.6996 2.3592 11.6401 0.299707 9.09961 0.299707V1.09971Z" fill="#1D1D1B"/>
            <path d="M10.8004 5.5998C10.8004 5.82072 10.9795 5.9998 11.2004 5.9998C11.4213 5.9998 11.6004 5.82072 11.6004 5.5998H10.8004ZM8.40039 2.3998C8.17948 2.3998 8.00039 2.57889 8.00039 2.7998C8.00039 3.02072 8.17948 3.1998 8.40039 3.1998V2.3998ZM8.40039 3.1998C9.72587 3.1998 10.8004 4.27432 10.8004 5.5998H11.6004C11.6004 3.83249 10.1677 2.3998 8.40039 2.3998V3.1998Z" fill="#1D1D1B"/>
            </svg>

          </span>
          <span id="shop_phone">Загрузка...</span>
        </div>
      </div>

      <a href="#" id="shop_link"></a>
      
    </div>
    <div class="mapContainer">
        <div class="overlayMap"></div>
        <div id="map" style="width: 100%; height: 270px"></div>
    </div>

    
  </div> -->
        </div>
    </div>
</section>

<section class="productsInf">
    <div class="container">
        <div class="section_title">
            <h2>Информация о товарах</h2>
        </div>
        <div class="productsInf_block">
            <?php foreach ($order_data['products'] as $product) { ?>
                <a href="<?= $product['link'] ?>" class="productsInf_block__product">
                    <div class="product_image">
                        <img src="<?= $product['image_link'] ?>" alt="">
                    </div>
                    <div class="product_nameProperty">
                        <h3 class="product_nameProperty__name">
                            <?= $product['name'] ?>
                        </h3>
                    </div>
                    <div class="product_quantity">
					<span class="product_quantity__value">
						<?= $product['quantity'] ?>
					</span>
                        <span class="product_quantity__unit">
						шт
					</span>
                    </div>
                    <div class="product_priceForUnit">
					<span class="product_priceForUnit__value">
						<?= $product['price'] ?>
					</span>
                        <span class="product_priceForUnit__currencyUnit">
						руб / шт
					</span>
                    </div>
                    <div class="product_price">
					<span class="product_price__value">
						<?= $product['price_total'] ?>
					</span>
                        <div class="product_price__currency">
                            руб
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>


<!-- Не забыть убрать -->
<style>
    .orderPage_map{
        height: 270px;
        display: grid;
        grid-template-columns: 270px 1fr;
        grid-gap: 30px;
        width: 100%;
    }

    .orderPage_map .map_information{
    height: 100%;
    background: #FDFDFD;
    }

    .orderPage_map #map, .orderPage_map #map .ymaps-2-1-79-map {
    height: 270px !important;
    max-height: 270px !important;
    }

    .orderPage_map #map :first-child, .map_information {
        border-radius: 10px;
    }

    .orderPage_map .map_information {
    padding: 20px 30px 0px 30px;
    }

    .orderPage_map .map_information .address{
    font-size: 16px;
    font-weight: 600;
    line-height: 20px;
    margin-bottom: 20px;
    display: block;
    }

    .orderPage_map .map_information .map_item {
    margin-bottom: 15px;
    }

    .orderPage_map .map_information .map_item .min-type {
    font-size: 10px; 
    color: #6B6B63;
    }

    .orderPage_map .map_information .map_item .map_item-name {
    font-size: 14px;
    }

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


</style>