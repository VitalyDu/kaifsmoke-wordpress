<?php

if (!empty($_SERVER['REDIRECT_URL'])) {
    if (
        get_current_user_id() == 0 && (in_array($_SERVER['REDIRECT_URL'], ['/my-account/', '/reservation/', '/my-orders/'])
            || strpos($_SERVER['REDIRECT_URL'], 'my-account/view-order'))
    )
        header('Location: /signin/');
    if (get_current_user_id() !== 0 && (in_array($_SERVER['REDIRECT_URL'], ['/signin/', '/signup/'])))
        header('Location: /my-account/');
    if (in_array($_SERVER['REDIRECT_URL'], ['/product-category/', '/product-category/vape/', '/product-category/vape/catalog/']))
        header('Location: /catalog/');
}
?>
<!DOCTYPE html>
<html lang="ru" style="margin: 0!important;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php //bloginfo('name'); было так
            wp_title(); ?></title>
    <!--    <meta name="description" content="--><?php //bloginfo('description') 
                                                    ?>
    <!--"/>-->
    <!-- <link rel="shortcut icon" href="<?php echo bloginfo('template_url'); ?>/assets/img/favicon.png" type="image/x-icon"> -->
    <?php
    add_filter('wpseo_metadesc', 'wp_wpseo_metadesc_filter', 10, 2);
    function wp_wpseo_metadesc_filter($meta_description, $presentation)
    {
        return $meta_description;
    }
    wp_head();
    ?>

    <?php
    VZPostViews::increase(get_the_ID());
    wp_reset_postdata(); // сброс
    ?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m, e, t, r, i, k, a) {
            m[i] = m[i] || function() {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(89959065, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/89959065" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
</head>

<body>
    <div class="loader">
        <div class="spinner"></div>
    </div>
    <div class="overlay"></div>
    <? if (!is_user_logged_in()) { ?>
        <!-- <div class="noSignIn">
    <div class="noSignIn_block">
        <span class="noSignIn_block__close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 8L16 16M16 8L12 12L8 16" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
        <p>
            Чтобы оформить заказ, а также просмотреть изображения, необходимо войти под своей учётной записью или
            зарегистрироваться
        </p>
    </div>
</div> -->
    <? } ?>
    <div class="fixedButtons">
        <a href="#" class="upPage">
            <svg class="upPage_icon" width="66" height="66" viewBox="0 0 66 66" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="33" cy="33" r="32" stroke="#1D1D1B" />
                <path d="M23.7071 38.7071C23.3166 39.0976 22.6834 39.0976 22.2929 38.7071C21.9024 38.3166 21.9024 37.6834 22.2929 37.2929L23.7071 38.7071ZM33 28L32.2929 27.2929L33 26.5858L33.7071 27.2929L33 28ZM43.7071 37.2929C44.0976 37.6834 44.0976 38.3166 43.7071 38.7071C43.3166 39.0976 42.6834 39.0976 42.2929 38.7071L43.7071 37.2929ZM22.2929 37.2929L32.2929 27.2929L33.7071 28.7071L23.7071 38.7071L22.2929 37.2929ZM33.7071 27.2929L43.7071 37.2929L42.2929 38.7071L32.2929 28.7071L33.7071 27.2929Z" fill="#1D1D1B" />
            </svg>
        </a>
        <a href="" class="chat">
            <svg class="chat_icon" width="66" height="66" viewBox="0 0 66 66" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- <circle cx="33" cy="33" r="32" stroke="#1D1D1B" />
        <path d="M19.7011 35.125L20.6785 35.3361C20.6935 35.2668 20.7011 35.196 20.7011 35.125H19.7011ZM24.1113 40.75V39.75C23.9934 39.75 23.8764 39.7708 23.7658 39.8116L24.1113 40.75ZM18 43L17.0225 42.7889L16.6385 44.5669L18.3455 43.9384L18 43ZM20.7011 31.375C20.7011 26.5093 23.7572 23 27.0515 23V21C22.2267 21 18.7011 25.8853 18.7011 31.375H20.7011ZM47 31.375C47 36.2407 43.9439 39.75 40.6496 39.75V41.75C45.4743 41.75 49 36.8647 49 31.375H47ZM40.6496 23C43.9439 23 47 26.5093 47 31.375H49C49 25.8853 45.4743 21 40.6496 21V23ZM27.0515 23H40.6496V21H27.0515V23ZM40.6496 39.75H27.0515V41.75H40.6496V39.75ZM18.7011 31.375V35.125H20.7011V31.375H18.7011ZM24.1113 41.75H27.0515V39.75H24.1113V41.75ZM23.7658 39.8116L17.6545 42.0616L18.3455 43.9384L24.4568 41.6884L23.7658 39.8116ZM18.9775 43.2111L20.6785 35.3361L18.7236 34.9139L17.0225 42.7889L18.9775 43.2111Z" fill="#1D1D1B" />
        <g filter="url(#filter0_d)">
            <path d="M31 31.5C31 32.3284 30.3284 33 29.5 33C28.6716 33 28 32.3284 28 31.5C28 30.6716 28.6716 30 29.5 30C30.3284 30 31 30.6716 31 31.5Z" fill="#1D1D1B" />
            <path d="M35.5 31.5C35.5 32.3284 34.8284 33 34 33C33.1716 33 32.5 32.3284 32.5 31.5C32.5 30.6716 33.1716 30 34 30C34.8284 30 35.5 30.6716 35.5 31.5Z" fill="#1D1D1B" />
            <path d="M40 31.5C40 32.3284 39.3284 33 38.5 33C37.6716 33 37 32.3284 37 31.5C37 30.6716 37.6716 30 38.5 30C39.3284 30 40 30.6716 40 31.5Z" fill="#1D1D1B" />
        </g>
        <defs>
            <filter id="filter0_d" x="18" y="22" width="32" height="23" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                <feOffset dy="2" />
                <feGaussianBlur stdDeviation="5" />
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
            </filter>
        </defs> -->
            </svg>
        </a>
    </div>

    <!-- POPUP ASK AGE -->
    <div class="vzpopup">
        <div class="popup__bg">
            <div class="popup__main">

                <div class="popUpTop">
                    <span>Для доступа к сайту необходимо подтвердить возраст</span>

                    <span id="introduction">
                        Вся информация на данном сайте носит исключительно ознакомительный характер, не используется в рекламных или маркетинговых целях. Материалы сайта не должны рассматриваться как предложение о покупке.
                    </span>

                    <div class="buttons">
                        <button class="ty-btn ty-btn__primary ty-btn_nbg" id="no">
                            <a href="#" class="btn__title-white">Нет</a>
                        </button>

                        <button class="ty-btn ty-btn__primary ty-btn_bg" id="yes">
                            <a href="#" class="btn__title-white">Да, мне исполнилось 18 лет</a>
                        </button>
                    </div>

                    <span id="warning">
                        В соответствии с требованиями ФЗ № 15-ФЗ «Об охране здоровья граждан от воздействия окружающего табачного дыма и последствий потребления табака» мы не осуществляем торговлю никотиносодержащей продукцией.
                    </span>



                    <div class="popup__signin show">
                        <form action="#" class="hidden">
                            <input type="text" placeholder="Логин">
                            <input type="password" placeholder="Пароль">
                        </form>
                    </div>




                    <span id="access">
                        Входя на сайт, я подтверждаю, что мне уже исполнилось 18 лет, и я являюсь потребителем табака или иной никотиносодержащей продукции.
                    </span>
                </div>


                <div class="popUpBottom">
                    <span>Войдите для получения полного доступа к сайту</span>


                    <div class="popup__signin show">
                        <form action="#" class="hidden">
                            <input type="text" placeholder="Логин">
                            <input type="password" placeholder="Пароль">
                        </form>
                    </div>


                    <div class="buttons">
                        <button class="ty-btn ty-btn__primary ty-btn_bg" id="signIn">
                            <a href="./signin/" class="btn__title-white">Вход</a>
                        </button>

                        <button class="ty-btn ty-btn__primary ty-btn_nbg" id="signUp">
                            <a href="./signup/" class="btn__title-white">Регистрация</a>
                        </button>
                    </div>

                    <span id="access">
                        Сайт vapezone.ru использует cookie c целью повышения производительностии упрощения работы с сайтом, а также в аналитических целях. Продолжая работу с сайтом, вы соглашаетесь на использование файлов cookie.
                    </span>
                </div>

            </div>
        </div>
    </div>

    <script src="/wp-content/themes/vapezone/assets/js/cookie.js"></script>

    <style>
        /* html {
            overflow: scroll;
            -webkit-overflow-scrolling: touch;
        } */

        .vzpopup {
            width: 100%;
            height: 100vh;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            display: none;
            color: #fff;


        }

        .vzpopup .popup__bg {
            width: 100%;
            height: 100vh;
            background: rgba(142, 142, 142, 0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            -moz-backdrop-filter: blur(30px);
            display: flex;
            justify-content: center;
            align-items: center;
            /* overflow: auto; */
            overflow: scroll;
            -webkit-overflow-scrolling: touch;
        }

        .vzpopup .popup__main {
            margin-top: 10vh;
            display: flex;
            flex-direction: column;
            background: #1D1D1B;
            border-radius: 10px;
            max-width: 770px;
            width: 770px;
            text-align: left;
            min-height: 200px;
            position: absolute;
            /* 	top: calc(50vh - 200px/2);
	left: calc(50% - 300px/2); */
            padding: 30px;
        }

        .popUpTop,
        .popUpBottom {
            display: flex;
            flex-direction: column;
            width: 90%;
            margin: 0 auto;
        }

        .vzpopup .popup__main b {
            color: #ff9100;
        }

        .vzpopup .popup__main input {
            width: 90%;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 11px 20px;
            display: none;
        }

        .vzpopup .popup__main .buttons {
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-basis: 95%;
            justify-content: space-between;
        }

        .vzpopup .popup__main span {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .vzpopup .ty-btn,
        .vzpopup .ty-btn__primary {
            width: 45% !important;
            height: 45px;
            font-size: 1rem !important;
            border: none;
            border-radius: 5px;

        }

        .vzpopup .ty-btn a,
        .vzpopup .ty-btn__primary a {
            text-decoration: none;
            font-size: 14px;
            display: flex;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;

        }

        #introduction,
        #access {
            font-weight: 500;
            margin: 0 auto;
            font-size: 14px;
            line-height: 20px;
            /* or 143% */
            text-align: left;
            color: #8E8E8E;
            margin-bottom: 30px;
        }

        #access {
            margin-top: 30px;
        }

        #warning {
            font-weight: 500;
            margin: 0 auto;
            font-size: 14px;
            line-height: 20px;
            /* or 143% */
            text-align: left;
            color: #F40500;
            margin-top: 30px;
        }

        .ty-btn_nbg {
            background: transparent;
            border: 1px solid #6B6B63 !important;
        }

        .ty-btn_bg {
            background-color: #ff9100;
        }


        .vzpopup .ty-btn .btn__title-white,
        .vzpopup .ty-btn__primary .btn__title-white {
            color: #fff !important;
        }


        .popUpTop {
            border-bottom: #8E8E8E 1px solid !important;
        }

        .popUpBottom {
            margin-top: 30px;
        }

        .showPopUp {
            display: flex;
        }

        @media screen and (max-width: 769px) {
            .vzpopup .popup__main {
                width: 100%;
                height: fit-content;
                border-radius: 0px;
                position: fixed;
                top: 0px;
                margin-top: 97px;
                padding-bottom: 5vh;
            }

            .vzpopup .popUpTop .buttons {
                width: 100%;
                height: 200px;
                display: flex;
                flex-basis: 95%;
                flex-direction: column;
                justify-content: space-between;
                gap: 20px;

            }

            .vzpopup .popUpTop .ty-btn__primary {
                width: 100% !important;
            }



        }

        @media screen and (max-height: 869px) {
            .vzpopup .popup__main {

                margin-top: 97px;

                top: 0px;
            }
        }

        /* OverHeader */
        .over_header {

            background: #141413;

        }

        .over_header .container {
            display: flex;
            justify-content: space-between;
            padding: 5px 0px;
        }

        .over_header .container a,
        .over_header .container span {
            color: #fff;
            text-decoration: none;
            font-size: 12px
        }

        @media screen and (max-width: 1170px) {
            .over_header {
                display: none;

            }
        }
    </style>
    <!-- /POPUP ASK AGE -->
    <!-- <div class="beforeHeader" id="beforeHeader">
<div class="container">
    <div class="beforeHeader_block">
        <div class="beforeHeader_block__geo">
            <a class="geo chooseGeo">Санкт-Петербург</a>
        </div>
        <div class="beforeHeader_block__contacts">
            <a class="call-back callBack_showModal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.4611 7.47867L7.49119 4.50883L5.79412 6.2059C4.09707 7.90296 4.52135 11.7213 8.33972 15.5397C12.1581 19.3581 15.9765 19.7823 17.6735 18.0853L19.3706 16.3882L16.4007 13.4184L15.5522 14.2669C13.8551 15.964 7.91546 10.0243 9.61251 8.32721L10.4611 7.47867Z" stroke="#D6D6D6" stroke-linejoin="round" />
                </svg>
                <span>
                    Заказать звонок
                </span>
            </a>
            <a href="tel:8 (812) 241-17-65" class="phone">8 (812) 241-17-65</a>
        </div>
    </div>
</div>
</div> -->

    <header itemscope itemtype="https://schema.org/WPHeader">
        <div class="over_header">
            <div class="container">
                <div class="city">Санкт-Петербург )</div>
                <div class="phone">
                    <span>Наш телефон:</span> <a href="tel:88122411765">8 (812) 241-17-65</a>
                </div>
            </div>

        </div>
        <div class="container desktopHeader">
            <div class="header_block">
                <ul class="header_block__catalog">
                    <li class="catalog_block">
                        <a href="/catalog" class="catalog_block__link">Каталог</a>
                        <ul class="catalog_block__dropdown">
                            <?php foreach (get_field('catalog_menu', 'option') as $menu_sub0) { ?>
                                <li class="catalog_block__category categoryHaveDropdown">
                                    <a href="<?= $menu_sub0['link'] ?>" class="category_link">
                                        <span class="category_icon"><?= $menu_sub0['icon'] ?></span>
                                        <span class="category_name"><?= $menu_sub0['name'] ?></span>
                                    </a>
                                    <?php if (!empty($menu_sub0['sub'])) { ?>
                                        <ul class="category_dropdown">
                                            <?php foreach ($menu_sub0['sub'] as $menu_sub1) { ?>
                                                <li class="category_dropdown__subcategory
                                    <?php if (!empty($menu_sub1['sub'])) { ?>subcategoryHaveDropdown<?php } ?>">
                                                    <a href="<?= $menu_sub1['link'] ?>" class="subcategory_link">
                                                        <span class="subcategory_icon"><?= $menu_sub1['icon'] ?></span>
                                                        <span class="subcategory_name"><?= $menu_sub1['name'] ?></span>
                                                    </a>
                                                    <?php if (!empty($menu_sub1['sub'])) { ?>
                                                        <ul class="subcategory_dropdown">
                                                            <?php foreach ($menu_sub1['sub'] as $menu_sub2) { ?>
                                                                <li class="subcategory_dropdown__product">
                                                                    <a href="<?= $menu_sub2['link'] ?>" class="product_link">
                                                                        <span class="subcategory_icon"><?= $menu_sub2['icon'] ?></span>
                                                                        <span class="subcategory_name"><?= $menu_sub2['name'] ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
                <div class="header_block__search">
                    <!-- <input type="text" class="search" placeholder="Поиск"> -->
                    <?php //aws_get_search_form( true ); 
                    ?>
                    <?php echo do_shortcode('[fibosearch]'); ?>
                </div>
                <a href="/" class="logo"><img src="/wp-content/themes/vapezone/assets/images/logo.png" alt="VapeZone"></a>
                <ul itemscope itemtype="https://schema.org/SiteNavigationElement" class="header_block__navigation">
                    <li class="navigation_shops"><a href="/shops/">Магазины</a></li>
                    <li class="navigation_more">
                        <a href="#" class="navigation_more__link">Ещё</a>
                        <ul class="navigation_more__dropdown">
                            <li><a itemprop="url" href="/opt">Оптовый раздел</a></li>
                            <li><a itemprop="url" href="/contacts">Контакты</a></li>
                            <li><a itemprop="url" href="/return">Возврат</a></li>
                            <li><a itemprop="url" href="/delivery">Доставка и оплата</a></li>
                            <li><a itemprop="url" href="/news">Обзоры</a></li>
                        </ul>
                    </li>
                    <li class="navigation_favorites">
                        <a itemprop="url" href="/favorites">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.2001 8.61987C19.2001 12.2199 12 18.7314 12 18.7314C12 18.7314 4.80005 12.2199 4.80005 8.61987C4.80005 4.69111 9.60005 3.16234 12 7.0911C14.4 3.16234 19.2 5.01987 19.2001 8.61987Z" stroke="#fff" />
                            </svg>
                        </a>
                        <div class="navigation_favorites__dropdown">
                            <div class="contentLoader" style="display: flex;">
                                <div class="spinner"></div>
                            </div>
                            <div class="favoritesDropdown_block__notHaveFavorites" style="display: none;">
                                <span class="favoritesDropdown_blockNotHaveFavorites__text">Список избранного пуст</span>
                            </div>
                            <div class="favoritesDropdown_block" style="display: none;">
                                <div class="favoritesDropdown_block__top allProdQuan">
                                    <span class="favoritesDropdown_productsQuantity"><span class="allProdQuanVal"></span> товара</span>
                                    <button class="favorites_clear">Очистить</button>
                                </div>
                                <div class="favoritesDropdown_block__content allProd">
                                </div>
                                <div class="favorites_block__bottom">
                                    <a itemprop="url" href="/favorites" class="btn s primary">Избранное</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="navigation_profile">
                        <a itemprop="url" href="<? if (!is_user_logged_in()) { ?>/signin<? } else { ?>my-account<? } ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 8C15 9.65685 13.6569 11 12 11C10.3431 11 9 9.65685 9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8Z" stroke="#fff" />
                                <path d="M12 13C8.68629 13 6 15.6863 6 19H18C18 15.6863 15.3137 13 12 13Z" stroke="#fff" />
                            </svg>
                        </a>
                        <div class="navigation_profile__dropdown">
                            <div class="navigation_profileDropdown__noAuthorized">
                                <? if (!is_user_logged_in()) { ?>
                                    <div class="navigation_profileDropdownNoAuthorized__top">
                                        <span class="signIn">
                                            Войти в аккаунт
                                            <br>
                                            VAPE ZONE
                                        </span>
                                    </div>
                                    <div class="navigation_profileDropdownNoAuthorized__form auth_form">
                                        <input type="text" name="phoneLogin" placeholder="Телефон" class="phoneSignIn">
                                        <input type="password" name="password" id="" placeholder="Пароль" class="passwordSignIn">
                                        <a href="/password-recovery/" class="profileDropdown_form_forgotPassword">Забыли
                                            пароль?</a>
                                        <div class="profileDropdown_form__buttons">
                                            <div class="profileDropdown_formButtons__signIn">
                                                <span class="modalSignInBtn btn primary s">Войти</span>
                                                <label class="custom-checkbox light" for="rememberMe_desktopModal">
                                                    <input type="checkbox" name="rememberMe" id="rememberMe_desktopModal">
                                                    <span class="label">Запомнить меня</span>
                                                </label>
                                            </div>
                                            <a href="/signup/" class="btn tertiary s">Регистрация</a>
                                        </div>
                                    </div>
                                <? } else { ?>
                                    <div class="navigation_profileDropdown__authorized">
                                        <div class="navigation_profileDropdownAuthorized__top">
                                            <div class="navigation_profileDropdownAuthorizedTop__icon">
                                                <img src="/wp-content/themes/vapezone/assets/images/profileIcon.png" alt="Пользователь">
                                            </div>
                                            <div class="navigation_profileDropdownAuthorizedTop__nameBonuses">
                                                <span class="navigation_profileDropdownAuthorizedTop__name"><? echo get_user_meta(get_current_user_id(), 'first_name', 1) ?> <? echo get_user_meta(get_current_user_id(), 'last_name', 1) ?>
                                                </span>
                                                <!-- <span class="navigation_profileDropdownAuthorizedTop__bonuses">150
                                        бонусов</span> -->
                                            </div>
                                        </div>
                                        <div class="navigation_profileDropdownAuthorized__navigation">
                                            <ul>
                                                <li><a href="/my-account">Управление аккаунтом</a></li>
                                                <li><a href="/my-orders">Мои заказы</a></li>
                                                <li><a href="/favorites">Избранное</a></li>
                                            </ul>
                                        </div>
                                        <div class="navigation_profileDropdownAuthorized__logout">
                                            <a class="btn s primary" href="<?php echo wp_logout_url(home_url()); ?>">Выйти</a>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </li>
                    <li class="navigation_basket">
                        <a itemprop="url" href="/reservation">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.9998 10H4M17.9998 10H18.9998V8H15.4998M17.9998 10L17.6226 13.0176M4 10L5 18H14.126M4 10H3V8H6.49976M7.5 12.5L7.99976 15.5M10.9998 15.5V12.5M14.5 12.5L13.9998 15.5M6.49976 8L8.49976 4M6.49976 8H15.4998M15.4998 8L13.4998 4M18 14.5V17H19.5M17.6226 13.0176C15.5904 13.2078 14 14.9181 14 17C14 17.3453 14.0438 17.6804 14.126 18M17.6226 13.0176C17.7468 13.0059 17.8727 13 18 13C20.2091 13 22 14.7909 22 17C22 19.2091 20.2091 21 18 21C16.1362 21 14.5701 19.7252 14.126 18" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="navigation_basket__dropdown">
                            <div class="contentLoader" style="display: flex;">
                                <div class="spinner"></div>
                            </div>
                            <div class="basketDropdown_block__notHaveProducts" style="display: none;">
                                <span class="basketDropdown_blockNotHaveProducts__text">Корзина пуста</span>
                            </div>
                            <div class="basketDropdown_block" style="display: none;">
                                <div class="basketDropdown_block__top allProdQuan">
                                    <span class="basketDropdown_productsQuantity"><span class="allProdQuanVal">1</span>
                                        товаров</span>
                                    <button class="basket_clear">Очистить</button>
                                </div>
                                <div class="basketDropdown_block__content allProd">
                                </div>
                                <div class="basket_block__bottom">
                                    <a href="/reservation" class="btn s primary">Бронирование</a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="container mobileHeader onlyMobile">
            <div class="mobileHeader_block">
                <a itemprop="url" href="/" class="mobileHeader_block__logo">
                    <img src="/wp-content/themes/vapezone/assets/images/icons/mobileFullLogo.png" alt="VapeZone">
                </a>
                <div class="mobileHeader_block__logoSearchBurger">
                    <a itemprop="url" href="/" class="mobileHeaderFixed_block__logo">
                        <img src="/wp-content/themes/vapezone/assets/images/icons/mobileLogo.png" alt="VapeZone">
                    </a>
                    <div class="mobileHeader_block__search">
                        <!-- <input type="search" placeholder="Поиск"> -->
                        <?php echo do_shortcode('[fibosearch]'); ?>
                    </div>
                    <div class="mobileHeader_block__burger">
                        <span class="circle">
                            1
                        </span>
                        <div class="burger_icon">
                            <span class="burger-line"></span>
                            <span class="burger-line"></span>
                            <span class="burger-line"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="mobileMenu mobileMenuModalBlock">
        <div class="container">
            <div class="mobileMenu_block">
                <div class="mobileMenu_block__icons">
                    <a itemprop="url" class="mobileMenu_favoritesIcon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.1998 8.61987C19.1998 12.2199 11.9998 18.7314 11.9998 18.7314C11.9998 18.7314 4.7998 12.2199 4.7998 8.61987C4.7998 4.69111 9.5998 3.16234 11.9998 7.0911C14.3998 3.16234 19.1998 5.01987 19.1998 8.61987Z" stroke="white" />
                        </svg>
                    </a>
                    <a itemprop="url" class="mobileMenu_userIcon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 8C15 9.65685 13.6569 11 12 11C10.3431 11 9 9.65685 9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8Z" stroke="white" />
                            <path d="M12 13C8.68629 13 6 15.6863 6 19H18C18 15.6863 15.3137 13 12 13Z" stroke="white" />
                        </svg>
                    </a>
                    <a itemprop="url" class="mobileMenu_basketIcon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.4002 15H18.0002L19.2002 7.19998H7.2002L8.4002 15ZM8.4002 15L6.6002 3.59998H1.2002M11.4002 18.6C11.4002 19.5941 10.5943 20.4 9.6002 20.4C8.60608 20.4 7.8002 19.5941 7.8002 18.6C7.8002 17.6059 8.60608 16.8 9.6002 16.8C10.5943 16.8 11.4002 17.6059 11.4002 18.6ZM18.6002 18.6C18.6002 19.5941 17.7943 20.4 16.8002 20.4C15.8061 20.4 15.0002 19.5941 15.0002 18.6C15.0002 17.6059 15.8061 16.8 16.8002 16.8C17.7943 16.8 18.6002 17.6059 18.6002 18.6Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
                <div class="mobileMenu_block__catalog">
                    <span class="mobileMenu_catalog__title">
                        Каталог
                    </span>
                    <ul>
                        <?php foreach (get_field('catalog_menu', 'option') as $menu_sub0) { ?>
                            <li class="mobileMenu_category haveDropDown">
                                <?= $menu_sub0['icon'] ?>
                                <a href="<?= $menu_sub0['link'] ?>" class="mobileMenu_categoryLink">
                                    <?= $menu_sub0['name'] ?>
                                </a>
                                <?php if (!empty($menu_sub0['sub'])) { ?>
                                    <span class="dropdown_arrow">
                                        <img src="/wp-content/themes/vapezone/assets/images/icons/mobileMenuHaveDropdown.png" alt="Открыть" />
                                    </span>
                                    <ul class="mobileMenu_category__subcategoryList categoriesSubcategories_block">
                                        <div class="container">
                                            <div class="mobileMenu_block__return">
                                                <li>
                                                    <a class="mobileMenuCatalog_subcategoryList__back">
                                                        <?= $menu_sub0['name'] ?>
                                                    </a>
                                                </li>
                                            </div>
                                            <?php foreach ($menu_sub0['sub'] as $menu_sub1) { ?>
                                                <li class="mobileMenu_subcategoryList__subcategory
                                    <?php if (!empty($menu_sub1['sub'])) { ?>haveDropDown<?php } ?>">
                                                    <?= $menu_sub1['icon'] ?>
                                                    <a href="<?= $menu_sub1['link'] ?>" class="mobileMenu_subcategoryList__subcategoryLink"><?= $menu_sub1['name'] ?></a>
                                                    <?php if (!empty($menu_sub1['sub'])) { ?>
                                                        <span class="dropdown_arrow">
                                                            <img src="/wp-content/themes/vapezone/assets/images/icons/mobileMenuHaveDropdown.png" alt="Открыть" />
                                                        </span>
                                                        <ul class="mobileMenu_subsubcategoryList categoriesSubcategories_block">
                                                            <div class="container">
                                                                <div class="mobileMenu_block__return">
                                                                    <li>
                                                                        <a class="mobileMenuCatalog_subsubcategoryList__back">
                                                                            <?= $menu_sub1['name'] ?>
                                                                        </a>
                                                                    </li>
                                                                </div>
                                                                <li>
                                                                    <?php foreach ($menu_sub1['sub'] as $menu_sub2) { ?>
                                                                <li class="subcategory_dropdown__product">
                                                                    <?= $menu_sub2['icon'] ?>
                                                                    <a href="<?= $menu_sub2['link'] ?>">
                                                                        <?= $menu_sub2['name'] ?>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                            </div>
                                                        </ul>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </div>
                                    </ul>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="mobileMenu_block__navigation">
                    <span class="mobileMenu_navigation__title">
                        Ещё
                    </span>
                    <ul>
                        <li>
                            <a itemprop="url" href="/shops">Магазины</a>
                        </li>
                        <li>
                            <a itemprop="url" href="/contacts">Контакты</a>
                        </li>
                        <li>
                            <a itemprop="url" href="/opt">Оптовый раздел</a>
                        </li>
                        <li>
                            <a itemprop="url" href="/return">Возврат</a>
                        </li>
                        <li>
                            <a itemprop="url" href="/delivery">Доставка и оплата</a>
                        </li>
                        <li>
                            <a itemprop="url" href="/news">Обзоры</a>
                        </li>
                    </ul>
                </div>
                <div class="mobileMenu_block__contacts">
                    <!-- <li class="mobileHeaderShowCallBackModal">
                <a>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.4614 7.47864L7.49156 4.50881L5.79449 6.20588C4.09744 7.90293 4.52171 11.7213 8.34009 15.5397C12.1585 19.358 15.9768 19.7823 17.6739 18.0853L19.3709 16.3882L16.4011 13.4184L15.5526 14.2669C13.8555 15.9639 7.91583 10.0242 9.61288 8.32718L10.4614 7.47864Z" stroke="#D6D6D6" stroke-linejoin="round" />
                    </svg>
                    <span>Заказать звонок</span>
                </a>
            </li> -->
                    <li class="mobileHeaderPhone">
                        <a href="tel:8 (812) 241-17-65">
                            8 (812) 241-17-65
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </div>
    <? if (is_user_logged_in()) { ?>
        <div class="mobileMenuUser mobileMenuModalBlock mobileUserModal">
            <div class="container">
                <div class="mobileMenuUser_block">
                    <div class="mobileMenu_block__return">
                        <li>
                            <a class="mobileMenuUser_back">
                                Аккаунт
                            </a>
                        </li>
                    </div>
                    <div class="mobileMenuUser_iconNameBonuses">
                        <div class="mobileMenuUser_iconNameBonuses_icon">
                            <img src="/wp-content/themes/vapezone/assets/images/icons/mobileMenuUserIcon.png" alt="Пользователь">
                        </div>
                        <div class="mobileMenuUser_iconNameBonuses_nameBonuses">
                            <span class="userName"><? echo get_user_meta(get_current_user_id(), 'first_name', 1) ?> <? echo get_user_meta(get_current_user_id(), 'last_name', 1) ?></span>
                            <!-- <div class="userBonuses">150 бонусов</div> -->
                        </div>
                    </div>
                    <ul class="mobileMenuUser_nav">
                        <li>
                            <a href="/my-account">Управление аккаунтом</a>
                        </li>
                        <li>
                            <a href="/my-orders">Мои заказы</a>
                        </li>
                        <li>
                            <a href="/favorites">Избранное</a>
                        </li>
                    </ul>
                    <div class="mobileMenuUser_logout">
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn s primary">Выйти</a>
                    </div>
                </div>
            </div>
        </div>
    <? } else { ?>
        <div class="mobileMenuSignIn mobileMenuModalBlock mobileUserModal">
            <div class="container">
                <div class="mobileMenuSignIn_block">
                    <div class="mobileMenu_block__return">
                        <li>
                            <a class="mobileMenuSignIn_back">
                                Аккаунт
                            </a>
                        </li>
                    </div>
                    <div class="mobileMenuSignIn_block__form auth_form">
                        <span class="mobileMenuSignIn_title">
                            Войти в аккаунт
                            <br>
                            VAPE ZONE
                        </span>
                        <div class="mobileMenuSignIn_form__fields">
                            <input type="text" name="mobile_userLogin" placeholder="Телефон">
                            <input type="password" name="mobile_userPassword" placeholder="Пароль">
                            <li>
                                <a href="/password-recovery/" class="mobileMenuSignIn_form__forgotPasswordLink">Забыли
                                    пароль?</a>
                            </li>
                        </div>
                        <div class="mobileMenuSignIn_form__buttons">
                            <div class="mobileMenuSignIn_buttons__left">
                                <button class="mobileMenuSignIn_button btn s primary">Войти</button>
                                <label class="custom-checkbox light" for="rememberMe_mobileModal">
                                    <input type="checkbox" name="rememberMe" id="rememberMe_mobileModal">
                                    <span class="label">Запомнить меня</span>
                                </label>
                            </div>
                            <div class="mobileMenuSignIn_buttons__right">
                                <a href="/signup/" class="btn s tertiary">Регистрация</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <? } ?>

    <div class="mobileMenuFavorites mobileMenuModalBlock">
        <div class="container">
            <div class="mobileMenuFavorites_block">
                <div class="mobileMenu_block__return">
                    <li>
                        <a class="mobileMenuFavorites_back">
                            Избранное
                        </a>
                    </li>
                </div>
                <div class="contentLoader" style="display: flex;">
                    <div class="spinner"></div>
                </div>
                <div class="mobileMenuFavorites_block__top allProdQuan" style="display: none;">
                    <div class="mobileMenuFavorites_top__productsQuantity">
                        <div class="mobileMenuFavorites_productsQuantity__value allProdQuanVal">
                            1
                        </div>
                        <div class="mobileMenuFavorites_productsQuantity__unit">
                            товаров
                        </div>
                    </div>
                    <div class="mobileMenuFavorites_top__clearFavorites favoritesDropdown_block">
                        <button class="favorites_clear">Очистить</button>
                    </div>
                </div>
                <div class="mobileMenuFavorites_block__products allProd" style="display: none;">
                </div>
                <div class="mobileMenuFavorites_block__goToFavorites" style="display: none;">
                    <a href="/favorites" class="btn s primary">Избранное</a>
                </div>
                <div class="mobileMenuFavorites_block__emptyProducts" style="display: none;">
                    <span class="emptyProducts_label">Список избранного пуст</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mobileMenuBasket mobileMenuModalBlock">
        <div class="container">
            <div class="mobileMenuBasket_block">
                <div class="mobileMenu_block__return">
                    <li>
                        <a class="mobileMenuBasket_back">
                            Бронирование
                        </a>
                    </li>
                </div>
                <div class="contentLoader" style="display: flex;">
                    <div class="spinner"></div>
                </div>
                <div class="mobileMenuBasket_block__top allProdQuan" style="display: none;">
                    <div class="mobileMenuBasket_top__productsQuantity">
                        <div class="mobileMenuBasket_productsQuantity__value allProdQuanVal">
                            1
                        </div>
                        <div class="mobileMenuBasket_productsQuantity__unit">
                            товаров
                        </div>
                    </div>
                    <div class="mobileMenuBasket_top__clearBasket">
                        <button>Очистить</button>
                    </div>
                </div>
                <div class="mobileMenuBasket_block__products allProd" style="display: none;">
                    <div class="productBlock prod">
                    </div>
                </div>
                <div class="mobileMenuBasket_block__buttons" style="display: none;">
                    <a href="/reservation" class="btn s primary">Бронирование</a>
                </div>
                <div class="mobileMenuBasket_block__emptyProducts" style="display: none;">
                    <span class="emptyProducts_label">Список бронирования пуст</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dgwt-wcas-suggestions-wrapp {
            position: fixed !important;
            /* margin-top: 17px !important;
        top: 17px !important; */
        }

        .dgwt-wcas-details-wrapp {
            position: fixed !important;
            /* margin-top: 17px !important;
        top: 17px !important; */
        }

        .dgwt-wcas-pd-addtc-form input {
            display: none !important;
        }

        @media (max-width: 768px) {
            .productsFilterWrapper .filterWrapper {
                display: none;
                position: fixed !important;
                top: 99px;
                left: 0;
                min-width: 100%;
                max-width: 100%;
                width: 100%;
                height: calc(100% - 99px) !important;
                z-index: 999;
                overflow-y: auto;
                padding: 0px 20px 30px;
            }




            .filter_status_container {
                position: sticky;
                top: 0;
                background: #fff;
                width: 100%;
            }

            .filterWrapper .filter .filter_close {
                width: 24px;
                padding-top: 20px;
            }
        }
    </style>