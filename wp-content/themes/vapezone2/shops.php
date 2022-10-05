<?php
/*
    Template Name: Магазины
*/
?>
<?php
get_header();
?>
    <section class="shops_container" style="">
        <div class="container">
            <div class="section_title">
                <h2>Магазины</h2>
            </div>
            <div class="breadcrumbs">
                <ul>
                    <li><a href="http://html.vapezone.pro/shops.html">Главная</a></li>
                    <li><a href="http://html.vapezone.pro/shops.html">Магазины</a></li>
                </ul>
            </div>

            <div class="shops-container">
                 <div class="shops shops_reservation">
                <div class="shops-top-nav">
            <span class="city">
                <div class="city-marker"><svg width="14" height="19" viewBox="0 0 14 19" fill="none"
                                              xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 6.6C13 9.91371 7 17.4 7 17.4C7 17.4 1 9.91371 1 6.6C1 3.28629 3.68629 0.599998 7 0.599998C10.3137 0.599998 13 3.28629 13 6.6Z"
                              stroke="#1D1D1B"/>
                        <path d="M9.4 6.6C9.4 7.92548 8.32548 9 7 9C5.67452 9 4.6 7.92548 4.6 6.6C4.6 5.27451 5.67452 4.2 7 4.2C8.32548 4.2 9.4 5.27451 9.4 6.6Z"
                              stroke="#1D1D1B"/>
                    </svg>
                </div>
                Санкт-Петербург
            </span>

                    <div class="sort-container">
                        <button class="sort">Сортировать
                            <div class="arrow">
                                <svg width="12" height="7" viewBox="0 0 12 7" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.55375 0.246544C1.35849 0.0512821 1.0419 0.0512821 0.846642 0.246544C0.65138 0.441806 0.65138 0.758389 0.846642 0.953651L1.55375 0.246544ZM6.0002 5.4001L5.64664 5.75365L6.0002 6.1072L6.35375 5.75365L6.0002 5.4001ZM11.1537 0.953651C11.349 0.758389 11.349 0.441806 11.1537 0.246544C10.9585 0.0512821 10.6419 0.0512821 10.4466 0.246544L11.1537 0.953651ZM0.846642 0.953651L5.64664 5.75365L6.35375 5.04654L1.55375 0.246544L0.846642 0.953651ZM6.35375 5.75365L11.1537 0.953651L10.4466 0.246544L5.64664 5.04654L6.35375 5.75365Z"
                                          fill="#1D1D1B"/>
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
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.6464 15.3536C14.8417 15.5488 15.1583 15.5488 15.3536 15.3536C15.5488 15.1583 15.5488 14.8417 15.3536 14.6464L14.6464 15.3536ZM10.8704 6.18518C10.8704 8.77274 8.77274 10.8704 6.18518 10.8704V11.8704C9.32503 11.8704 11.8704 9.32503 11.8704 6.18518H10.8704ZM6.18518 10.8704C3.59763 10.8704 1.5 8.77274 1.5 6.18518H0.5C0.5 9.32503 3.04534 11.8704 6.18518 11.8704V10.8704ZM1.5 6.18518C1.5 3.59763 3.59763 1.5 6.18518 1.5V0.5C3.04534 0.5 0.5 3.04534 0.5 6.18518H1.5ZM6.18518 1.5C8.77274 1.5 10.8704 3.59763 10.8704 6.18518H11.8704C11.8704 3.04534 9.32503 0.5 6.18518 0.5V1.5ZM9.49811 10.2052L14.6464 15.3536L15.3536 14.6464L10.2052 9.49811L9.49811 10.2052Z"
                                      fill="#1D1D1B"/>
                            </svg>
                        </div>
                        <input type="text" id='suggest' placeholder="Поиск по магазинам">
                    </div>
                </div>

                <!-- Табличка -->
                <div class="reservation-container">
                    <div class="shops-table">

                        <?php
                        $posts = get_posts(array(
                            'numberposts' => -1,
                            'category_name' => 'shops',
                            'orderby' => 'date',
                            'order' => 'DESC',
                            'post_type' => 'post',
                            'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
                        ));
                        foreach ($posts as $post) {
                            setup_postdata($post);
                            ?>
                        <div class="table-item">
                            <div class="address">
                                <span><b><?= get_post_meta(get_the_id(), 'address', true) ?></b></span>
                                <span><svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                           xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.5 7C13.5 10.5899 10.5899 13.5 7 13.5C3.41015 13.5 0.5 10.5899 0.5 7C0.5 3.41015 3.41015 0.5 7 0.5C10.5899 0.5 13.5 3.41015 13.5 7Z"
                                      fill="#E9420D" stroke="#E9420D" stroke-width="0.8"/>
                                <path d="M5.41054 5.26242C5.29062 4.88154 4.9651 4.92329 4.74254 5.10069C4.19429 5.57546 3.48042 6.572 3.9373 8.00694C4.31993 9.22259 5.91207 10 5.91207 10H4.05606C4.05606 10 3.09773 8.99303 3.01206 7.80869C2.90356 6.31131 3.54323 5.36694 4.28566 4.75131C4.98239 4.17217 5.64486 4 5.64486 4L7 8.1625L8.35514 4C8.35514 4 9.01761 4.17217 9.71434 4.75131C10.4568 5.36694 11.0964 6.31131 10.9879 7.80869C10.9023 8.99303 9.94394 10 9.94394 10H8.08793C8.08793 10 9.67994 9.22259 10.0627 8.00694C10.5196 6.57216 9.80571 5.57563 9.25746 5.10069C9.03473 4.92329 8.70921 4.88154 8.58946 5.26242C8.17827 6.47808 7.00017 9.96117 7.00017 9.96117C7.00017 9.96117 5.82207 6.47691 5.41088 5.26242H5.41054Z"
                                      fill="white"/>
                            </svg>
                            <?= $post->post_title ?></span>
                            </div>
                            <div class="info">
                        <span> <svg width="14" height="15" viewBox="0 0 14 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.00049 3.50012V7.11123H9.50049M13.5005 7.11123C13.5005 10.7011 10.5903 13.6112 7.00049 13.6112C3.41064 13.6112 0.500488 10.7011 0.500488 7.11123C0.500488 3.52138 3.41064 0.611233 7.00049 0.611233C10.5903 0.611233 13.5005 3.52138 13.5005 7.11123Z"
                                      stroke="#1D1D1B" stroke-width="0.8" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg><?= get_post_meta(get_the_id(), 'schedule', true) ?></span>
                                <span><svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                           xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.10618 2.94505L3.12628 0.965149L1.64126 2.45017C1.14611 2.94532 -0.833787 4.92522 3.62099 9.37999C8.07576 13.8348 10.0557 11.8549 10.551 11.3596L12.0357 9.87479L10.0559 7.8948C9.56118 7.39959 9.56095 7.39982 9.0662 7.89457C7.58128 9.37949 3.62148 5.4197 5.1064 3.93477C5.60115 3.44002 5.60138 3.4398 5.10618 2.94505Z"
                                      stroke="#1D1D1B" stroke-width="0.8" stroke-linejoin="round"/>
                            </svg><a href="tel:+<?= preg_replace('/[^0-9]/', '', get_post_meta(get_the_id(), 'phone', true)) ?>"><?= get_post_meta(get_the_id(), 'phone', true) ?></a></span>
                            </div>
                            <div class="avalible">
                                
                            </div>
                            <span class="shop-select">
                        <a href="<?=get_post_permalink(get_the_id())?>">Перейти к магазину</a>
                    </span>
                        </div>
                            <?php
                        }
                        wp_reset_postdata(); // сброс
                        ?>

                    </div>
                    <div class="mapContainer">
                        <div class="overlayMap"></div>
                        <div id="map" style="width: 100%; height: 250px"></div>
                    </div>
                </div>

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

        .shops-container{
            margin-top: 20px;
        }


        @media (max-width: 600px) {
            .reservation-container .shops-table {
                min-width: 0 !important;
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

            .reservation-container .shops-table {
                min-width: 0 !important;
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