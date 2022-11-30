<footer id="footer">
    <div class="container">
        <div class="footer_block">
            <div class="footer_block__navigation">
                <a href="" class="logo">VAPE ZONE</a>
                <ul>
                    <li><a href="/catalog">Каталог</a></li>
                    <li><a href="/return">Возврат</a></li>
                    <li><a href="/delivery">Доставка и оплата</a></li>
                    <li><a href="/shops">Магазины</a></li>
                    <li><a href="/contacts">Контакты</a></li>
                    <li><a href="/opt">Оптовый раздел</a></li>
                </ul>
            </div>
            <div class="footer_block__account">
                <a href="/my-account" class="account">Аккаунт</a>
                <ul>
                    <li><a href="/favorites">Избранное</a></li>
                    <li><a href="
                    <?
                    if (is_user_logged_in()) {
                        echo '/my-account';
                    } else {
                        echo '/signup';
                    }
                    ?>
                    ">Управление аккаунтом</a></li>
                    <li><a href="
                    <?
                    if (is_user_logged_in()) {
                        echo '/my-orders';
                    } else {
                        echo '/signup';
                    }
                    ?>
                    ">Мои заказы</a></li>
                </ul>
            </div>
            <div class="footer_block__social">
                <span>Социальные сети</span>
                <ul>
                    <?php if (get_field('social', 'option')) : ?>
                        <?php while (has_sub_field('social', 'option')) : ?>
                            <? if (!get_sub_field('hidden')) { ?>
                                <li>
                                    <a href="<?php the_sub_field('link'); ?>">
                                        <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('name'); ?>" />
                                    </a>
                                </li>
                            <? } ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</footer>
<div class="afterFooter">
    <div class="container">
        <div class="afterFooter_block">
            <div class="afterFooter_block__textLink">
                <p>Оптовый магазин: <a href="https://ruvape.ru" target="_blank">ruvape.ru</a></p>
            </div>
            <div class="afterFooter_block__text">
                <p>Мы не реализуем продукцию лицам младше 18 лет!</p>
            </div>
            <div class="afterFooter_block__copyright">
                <span>© <?php echo date('Y'); ?> VAPE ZONE</span>
            </div>
        </div>
    </div>
    <div class="fuckTobacco onlyMobile">
        <div class="container">
            <div class="fuckTobacco_block">
                <span>F**K TOBACCO</span>
            </div>
        </div>
    </div>
</div>
<div class="callBack_modal">
    <div class="callBack form">
        <span class="callBack_close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 8L16 16M16 8L12 12L8 16" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
        <div class="callBack_block form_block">
            <div class="callBack_block__form form_block__form">
                <div class="callBackBlock_form__title formBlock_form__title">
                    <h2>Обратная связь</h2>
                    <span>Отправьте заявку для решения любых вопросов</span>
                </div>
                <div class="callBackBlock_form__fields formBlock_form__fields">
                    <div class="callBackBlockForm_fields__left formBlockForm_fields__left">
                        <input type="text" name="name" class="userName" placeholder="Имя">
                        <input type="phone" name="phone" class="userPhone" placeholder="Телефон">
                        <input type="email" name="email" class="userEmail" placeholder="Электронная почта">
                    </div>
                    <div class="callBackBlockForm_fields__right formBlockForm_fields__right">
                        <textarea name="message" id="" cols="30" rows="9" placeholder="Текст сообщения"></textarea>
                    </div>
                </div>
                <div class="callBackBlock_form_sendBtn formBlock_form_sendBtn">
                    <button type="submit" class="sendFormBtn bttOrange">Отправить</button>
                    <span class="validationMessage">Заполнены не все поля!</span>
                </div>
            </div>
            <div class="callBack_block__send form_block__send">
                <h2>Отправлено!</h2>
                <img src="/wp-content/themes/kaifsmoke/assets/images/okey.png" alt="">
                <span>Мы свяжемся с Вами в ближайшее время</span>
            </div>
        </div>
    </div>
</div>
<!-- <div class="chooseRegion_modal">
    <div class="chooseRegion">
        <span class="chooseRegion_close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 8L16 16M16 8L12 12L8 16" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
        <div class="chooseRegion_block">
            <div class="chooseRegion_block__title">
                <h3>Выбор региона</h3>
            </div>
            <div class="field">
                <label for="">Введите название вашего региона</label>
                <input type="text">
            </div>
            <span>Или выберите из списка</span>
            <div class="cities_list">
                <ul>
                    <li><a href="">Москва</a></li>
                    <li><a href="">Крым</a></li>
                    <li><a href="">Санкт-Петербург</a></li>
                    <li><a href="">Краснодар</a></li>
                    <li><a href="">Архангельск</a></li>
                    <li><a href="">Мурманск</a></li>
                    <li><a href="">Белгород</a></li>
                    <li><a href="">Омск</a></li>
                    <li><a href="">Екатеринбург</a></li>
                    <li><a href="">Самара</a></li>
                </ul>
            </div>
        </div>
    </div>
</div> -->



<!-- Стили для Leadback -->
<style>
    #lb_button-call {
        display: none;
    }

    #lb_button-chat {
        width: 0 !important;
        height: 0 !important;
    }


    #lb_button-chat::before {
        content: url('/wp-content/themes/kaifsmoke/assets/images/icons/chat.svg');
        position: absolute;
        top: 7px;
        left: 7px;

    }

    .lb-widget-panel {
        position: relative !important;
        top: 5px !important;
        left: -41px !important
    }

    .lb-widget-chat {
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
        position: relative;
        /* left: -55% !important;
        top: 15% !important; */
    }

    .lb-button__wrapper {
        width: 40px !important;
        height: 40px !important;
    }

    .lb-action-sheet__item--last {
        display: none !important;
    }

    @media (hover: none) and (pointer: coarse) {
        .upPage_icon {
            width: 72px !important;
        }

        .fixedButtons .chat {
            display: none;
        }

        .lb-widget-button {
            bottom: 54px !important;
        }

        ldiv .lb-button__wrapper {
            bottom: 34px !important
        }

        .fixedButtons a.upPage svg {
            top: 16px !important;
            position: relative !important;
        }

        .fixedButtons .chat {
            display: none;
        }
    }





    @media (max-width: 1169px) .fixedButtons a.upPage svg {
        max-width: 64px;
        width: 100%;
        height: auto;

    }

    ldiv .lb-button__wrapper {
        bottom: 34px !important
    }

    .fixedButtons a.upPage svg {
        max-width: 72px;
    }

    /* @media screen and (min-width: 1170px) {
    .lb-widget-panel{
        position: relative !important;
        top: 5px !important;
        left: -41px !important;
        width: 40px !important;
        height: 40px !important; 
    }
   } */
</style>
<?php
wp_footer();
?>
</body>

</html>