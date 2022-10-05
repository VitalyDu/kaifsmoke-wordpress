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
