<?php
/*
    Template Name: Корзина
*/
?>
<?php
get_header();
?>
<div class="pageLoader cartLoading">
    <div class="spinner"></div>
</div>

<section class="cartPage__title" style="display: none;">
    <div class="container">
        <div class="section_title">
            <h2>Бронирование товаров</h2>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<div class="cartPage_map" style="display: none;">
    <!-- MAP -->
    <div class="shops shops_reservation">
        <div class="shops-top-nav">
            <span class="city">
                <div class="city-marker"><svg width="14" height="19" viewBox="0 0 14 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 6.6C13 9.91371 7 17.4 7 17.4C7 17.4 1 9.91371 1 6.6C1 3.28629 3.68629 0.599998 7 0.599998C10.3137 0.599998 13 3.28629 13 6.6Z" stroke="#000000" />
                        <path d="M9.4 6.6C9.4 7.92548 8.32548 9 7 9C5.67452 9 4.6 7.92548 4.6 6.6C4.6 5.27451 5.67452 4.2 7 4.2C8.32548 4.2 9.4 5.27451 9.4 6.6Z" stroke="#000000" />
                    </svg>
                </div>
                Санкт-Петербург
            </span>

            <div class="sort-container">
                <button class="sort">Сортировать <div class="arrow"><svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.55375 0.246544C1.35849 0.0512821 1.0419 0.0512821 0.846642 0.246544C0.65138 0.441806 0.65138 0.758389 0.846642 0.953651L1.55375 0.246544ZM6.0002 5.4001L5.64664 5.75365L6.0002 6.1072L6.35375 5.75365L6.0002 5.4001ZM11.1537 0.953651C11.349 0.758389 11.349 0.441806 11.1537 0.246544C10.9585 0.0512821 10.6419 0.0512821 10.4466 0.246544L11.1537 0.953651ZM0.846642 0.953651L5.64664 5.75365L6.35375 5.04654L1.55375 0.246544L0.846642 0.953651ZM6.35375 5.75365L11.1537 0.953651L10.4466 0.246544L5.64664 5.04654L6.35375 5.75365Z" fill="#000000" />
                        </svg>
                    </div></button>

                <div class="sort-menu">
                    <span>От А до Я</span>
                    <span>От Я до А</span>
                </div>
            </div>

            <div class="searchbar">
                <div class="findicon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.6464 15.3536C14.8417 15.5488 15.1583 15.5488 15.3536 15.3536C15.5488 15.1583 15.5488 14.8417 15.3536 14.6464L14.6464 15.3536ZM10.8704 6.18518C10.8704 8.77274 8.77274 10.8704 6.18518 10.8704V11.8704C9.32503 11.8704 11.8704 9.32503 11.8704 6.18518H10.8704ZM6.18518 10.8704C3.59763 10.8704 1.5 8.77274 1.5 6.18518H0.5C0.5 9.32503 3.04534 11.8704 6.18518 11.8704V10.8704ZM1.5 6.18518C1.5 3.59763 3.59763 1.5 6.18518 1.5V0.5C3.04534 0.5 0.5 3.04534 0.5 6.18518H1.5ZM6.18518 1.5C8.77274 1.5 10.8704 3.59763 10.8704 6.18518H11.8704C11.8704 3.04534 9.32503 0.5 6.18518 0.5V1.5ZM9.49811 10.2052L14.6464 15.3536L15.3536 14.6464L10.2052 9.49811L9.49811 10.2052Z" fill="#000000" />
                    </svg>
                </div>
                <input type="text" id='suggest' placeholder="Поиск по магазинам">
            </div>
        </div>

        <!-- Табличка -->
        <div class="reservation-container">
            <div class="shops-table">
                <!--         <div class="table-header">
                <span>Кол-во</span>
                <span>Адрес</span>
                <span>Станция метро</span>
                <span>Режим работы</span>
                <span>Телефон</span>
                <span>Действие</span>
                </div> -->

                <!-- <div class="table-item">
                    <div class="address">
                        <span><b>Лиговский пр., д. 47</b></span>
                        <span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.5 7C13.5 10.5899 10.5899 13.5 7 13.5C3.41015 13.5 0.5 10.5899 0.5 7C0.5 3.41015 3.41015 0.5 7 0.5C10.5899 0.5 13.5 3.41015 13.5 7Z" fill="#E9420D" stroke="#E9420D" stroke-width="0.8" />
                                <path d="M5.41054 5.26242C5.29062 4.88154 4.9651 4.92329 4.74254 5.10069C4.19429 5.57546 3.48042 6.572 3.9373 8.00694C4.31993 9.22259 5.91207 10 5.91207 10H4.05606C4.05606 10 3.09773 8.99303 3.01206 7.80869C2.90356 6.31131 3.54323 5.36694 4.28566 4.75131C4.98239 4.17217 5.64486 4 5.64486 4L7 8.1625L8.35514 4C8.35514 4 9.01761 4.17217 9.71434 4.75131C10.4568 5.36694 11.0964 6.31131 10.9879 7.80869C10.9023 8.99303 9.94394 10 9.94394 10H8.08793C8.08793 10 9.67994 9.22259 10.0627 8.00694C10.5196 6.57216 9.80571 5.57563 9.25746 5.10069C9.03473 4.92329 8.70921 4.88154 8.58946 5.26242C8.17827 6.47808 7.00017 9.96117 7.00017 9.96117C7.00017 9.96117 5.82207 6.47691 5.41088 5.26242H5.41054Z" fill="white" />
                            </svg>
                            Площадь Восстания</span>
                    </div>


                    <div class="info">

                        <span> <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.00049 3.50012V7.11123H9.50049M13.5005 7.11123C13.5005 10.7011 10.5903 13.6112 7.00049 13.6112C3.41064 13.6112 0.500488 10.7011 0.500488 7.11123C0.500488 3.52138 3.41064 0.611233 7.00049 0.611233C10.5903 0.611233 13.5005 3.52138 13.5005 7.11123Z" stroke="#000000" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>10:00 – 23:00</span>

                        <span><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.10618 2.94505L3.12628 0.965149L1.64126 2.45017C1.14611 2.94532 -0.833787 4.92522 3.62099 9.37999C8.07576 13.8348 10.0557 11.8549 10.551 11.3596L12.0357 9.87479L10.0559 7.8948C9.56118 7.39959 9.56095 7.39982 9.0662 7.89457C7.58128 9.37949 3.62148 5.4197 5.1064 3.93477C5.60115 3.44002 5.60138 3.4398 5.10618 2.94505Z" stroke="#000000" stroke-width="0.8" stroke-linejoin="round" />
                            </svg><a href="tel:+79006384394">+7 (900) 638-43-94</a></span>
                    </div>

                    <div class="avalible">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="10" fill="#31A337" />
                        </svg>
                        <span>Все товары
                            в наличии</span>
                    </div>


                    <span class="shop-select">
                        <a href="#">Выбрать</a>
                    </span>
                </div> -->
            </div>
            <div class="mapContainer">
                <div class="overlayMap"></div>
                <div id="map" style="width: 100%; height: 250px"></div>
            </div>
        </div>

    </div>
    <!-- MAP -->
</div>

<section class="cartPage" style="display: none;">
    <div class="container">
        <div class="cartPage_block">

            <div class="cartPage_block__products">
                <span class="products_label">Товары</span>
                <div class="products_content">
                    <!-- <div class="products_content__product">
                        <a href="" class="product_image">
                            <img src="" alt="Товар" />
                        </a>
                        <div class="product_namePrice">
                            <a href="" class="product_name">Жидкость Bad Drip Cereal Trip Ванильная сахарная вата с
                                малиновым вареньем</a>
                            <span class="product_price">1 090 ₽ <span class="light small">/ шт</span></span>
                        </div>
                        <div class="product_quantityWrapper">
                            <span class="product_quantityMinus">-</span>
                            <input type="number" value="1" class="product_quantity">
                            <span class="product_quantityPlus">+</span>
                        </div>
                        <div class="product_summ">
                            <span class="product_summ__label">Сумма</span>
                            <span class="product_summ__value">1 090 ₽</span>
                        </div>
                        <div class="product_stock">
                            <span class="product_stock__label">Наличие в выбранном магазине</span>
                            <span class="product_stock__value">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#2074C1" />
                                    <path d="M14 10L14.7071 10.7071C15.0976 10.3166 15.0976 9.68342 14.7071 9.29289L14 10ZM10.2929 12.2929C9.90237 12.6834 9.90237 13.3166 10.2929 13.7071C10.6834 14.0976 11.3166 14.0976 11.7071 13.7071L10.2929 12.2929ZM11.7071 6.29289C11.3166 5.90237 10.6834 5.90237 10.2929 6.29289C9.90237 6.68342 9.90237 7.31658 10.2929 7.70711L11.7071 6.29289ZM13.2929 9.29289L10.2929 12.2929L11.7071 13.7071L14.7071 10.7071L13.2929 9.29289ZM14.7071 9.29289L11.7071 6.29289L10.2929 7.70711L13.2929 10.7071L14.7071 9.29289Z" fill="white" />
                                    <path d="M10 10L10.7071 10.7071C11.0976 10.3166 11.0976 9.68342 10.7071 9.29289L10 10ZM6.29289 12.2929C5.90237 12.6834 5.90237 13.3166 6.29289 13.7071C6.68342 14.0976 7.31658 14.0976 7.70711 13.7071L6.29289 12.2929ZM7.70711 6.29289C7.31658 5.90237 6.68342 5.90237 6.29289 6.29289C5.90237 6.68342 5.90237 7.31658 6.29289 7.70711L7.70711 6.29289ZM9.29289 9.29289L6.29289 12.2929L7.70711 13.7071L10.7071 10.7071L9.29289 9.29289ZM10.7071 9.29289L7.70711 6.29289L6.29289 7.70711L9.29289 10.7071L10.7071 9.29289Z" fill="white" />
                                </svg>
                                Доставят за 2 дня
                            </span>
                        </div>
                        <div class="product_deliveryWait">
                            <span class="product_deliveryWait__label">Бронировать товары с ожиданием?</span>
                            <div class="product_deliveryWait__checkboxWrapper">
                                <span class="label">Нет</span>
                                <input type="checkbox" name="wait_product" id="wait_product_1">
                                <label for="wait_product_1"></label>
                                <span class="label">Да</span>
                            </div>
                        </div>
                    </div>
                    <div class="products_content__product">
                        <a href="" class="product_image">
                            <img src="" alt="Товар" />
                        </a>
                        <div class="product_namePrice">
                            <a href="" class="product_name">Жидкость Bad Drip Cereal Trip Ванильная сахарная вата с
                                малиновым вареньем</a>
                            <span class="product_price">1 090 ₽ <span class="light small">/ шт</span></span>
                        </div>
                        <div class="product_quantityWrapper">
                            <span class="product_quantityMinus">-</span>
                            <input type="number" value="1" class="product_quantity">
                            <span class="product_quantityPlus">+</span>
                        </div>
                        <div class="product_summ">
                            <span class="product_summ__label">Сумма</span>
                            <span class="product_summ__value">1 090 ₽</span>
                        </div>
                        <div class="product_stock">
                            <span class="product_stock__label">Наличие в выбранном магазине</span>
                            <span class="product_stock__value">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#2074C1" />
                                    <path d="M14 10L14.7071 10.7071C15.0976 10.3166 15.0976 9.68342 14.7071 9.29289L14 10ZM10.2929 12.2929C9.90237 12.6834 9.90237 13.3166 10.2929 13.7071C10.6834 14.0976 11.3166 14.0976 11.7071 13.7071L10.2929 12.2929ZM11.7071 6.29289C11.3166 5.90237 10.6834 5.90237 10.2929 6.29289C9.90237 6.68342 9.90237 7.31658 10.2929 7.70711L11.7071 6.29289ZM13.2929 9.29289L10.2929 12.2929L11.7071 13.7071L14.7071 10.7071L13.2929 9.29289ZM14.7071 9.29289L11.7071 6.29289L10.2929 7.70711L13.2929 10.7071L14.7071 9.29289Z" fill="white" />
                                    <path d="M10 10L10.7071 10.7071C11.0976 10.3166 11.0976 9.68342 10.7071 9.29289L10 10ZM6.29289 12.2929C5.90237 12.6834 5.90237 13.3166 6.29289 13.7071C6.68342 14.0976 7.31658 14.0976 7.70711 13.7071L6.29289 12.2929ZM7.70711 6.29289C7.31658 5.90237 6.68342 5.90237 6.29289 6.29289C5.90237 6.68342 5.90237 7.31658 6.29289 7.70711L7.70711 6.29289ZM9.29289 9.29289L6.29289 12.2929L7.70711 13.7071L10.7071 10.7071L9.29289 9.29289ZM10.7071 9.29289L7.70711 6.29289L6.29289 7.70711L9.29289 10.7071L10.7071 9.29289Z" fill="white" />
                                </svg>
                                Доставят за 2 дня
                            </span>
                        </div>
                        <div class="product_deliveryWait">
                            <span class="product_deliveryWait__label">Бронировать товары с ожиданием?</span>
                            <div class="product_deliveryWait__checkboxWrapper">
                                <span class="label">Нет</span>
                                <input type="checkbox" name="wait_product" id="wait_product_1">
                                <label for="wait_product_1"></label>
                                <span class="label">Да</span>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="cartPage_block__customer">
                <span class="label">Информация о покупателе</span>
                <div class="customer_fields">
                    <div class="inputField">
                        <label for="">Имя</label>
                        <input type="text" name="customer_name" value="<? echo get_user_meta(get_current_user_id(), 'first_name', 1) ?>" placeholder="Иван Иванов">
                    </div>
                    <div class="inputField">
                        <label for="">Телефон</label>
                        <input type="text" name="customer_phone" value="<? echo get_user_meta(get_current_user_id(), 'phone', 1) ?>" placeholder="Телефон">
                    </div>
                </div>
                <!-- <div class="customer_agreeWait">
                    <span class="customer_agreeWait__label">Бронировать товары с ожиданием?</span>
                    <div class="customer_agreeWait__checkboxWrapper">
                        <span class="label">Нет</span>
                        <input type="checkbox" name="wait_product" id="wait_all_products">
                        <label for="wait_all_products"></label>
                        <span class="label">Да</span>
                    </div>
                    <div class="customer_agreeWait__buttons">
                        <button class="reserveProductsWithExpectation">
                            Да, бронировать
                        </button>
                        <button class="noReserveProductsWithExpectation">Нет</button>
                    </div>
                </div> -->
            </div>
            <div class="cartPage_block__order">
                <div class="order_total">
                    <div class="row totalReserveProducts">
                        <span class="label">Итого бронируемых товаров</span>
                        <span class="value">2</span>
                    </div>
                    <div class="row choosenShop">
                        <span class="label">Выбранный магазин</span>
                        <span class="value">Пр. Просвещения д. 36/1</span>
                    </div>
                    <div class="row deliveryTime">
                        <span class="label">Время ожидания товаров</span>
                        <span class="value">2 дня</span>
                    </div>
                    <div class="row totalSumm">
                        <span class="label">Сумма</span>
                        <span class="value">2 994 ₽</span>
                    </div>
                </div>
                <button class="order_button">Забронировать</button>
            </div>
        </div>
    </div>
</section>

<section class="answerPage notHaveBlock" style="display: none;">
    <div class="container">
        <div class="answerPage_block">
            <h1>Список бронирования пуст</h1>
            <img src="/wp-content/themes/vapezone/assets/images/icons/notFoundHave.png" alt="">
            <a class="onHomePage btn l primary" href="/catalog">Перейти в каталог</a>
        </div>
    </div>
</section>

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
        display: block;
    }

    .ymaps-2-1-79-map{
        width: auto !important;
    }

    .sort-menu span:hover {
        cursor: pointer;
    }

    .searchbar ymaps {
        width: 270px !important;
        left: auto !important;
    }

    .searchbar ymaps:nth-child(1) {
        margin-top: 20px;
    }

    .shops-table .table-item {
        height: auto !important;
    }


    .ymaps-2-1-79-search__suggest-item {
        white-space: normal;
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

    .reservation-container .shops-table {
        max-height: 315px;
        overflow-y: scroll;
    }


    @media (max-width: 600px) {
        .reservation-container .shops-table {
            min-width: auto;
        }
    }

    @media (max-width: 1120px) {
        .shops-top-nav {
            width: 100%;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: start;
            align-items: flex-start;
        }

        .shops-top-nav .searchbar {
            margin-left: 0;
            width: 100%;
        }

        .city-marker svg {
            margin: 0 !important;
            margin-right: 5px !important;
        }
    }
</style>
<?php
get_footer();
?>