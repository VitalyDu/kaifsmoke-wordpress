<?php
/*
    Template Name: Оптовый раздел
*/
?>
<?php
get_header();
?>
<section class="opt">
    <div class="container">
        <div class="section_title">
            <span class="section_title__label">Оптовый раздел</span>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<section class="advantages">
    <div class="container">
        <div class="advantages_block">
            <div class="advantages_block__advantage">
                <div class="advantage_icon"><img src="/wp-content/themes/vapezone/assets/images/icons/deliveryCar.png" alt="Доставка"></div>
                <div class="advantage_title">Бесплатная доставка</div>
                <div class="advantage_description">по Санкт-Петербургу</div>
                <div class="advantage_fullDescription">
                    <p>Весь товар в наличии в Санкт-Петербурге</p>
                </div>
            </div>
            <div class="advantages_block__advantage">
                <div class="advantage_icon"><img src="/wp-content/themes/vapezone/assets/images/icons/card.png" alt="Оплата"></div>
                <div class="advantage_title">Заказывайте</div>
                <div class="advantage_description">без предоплаты</div>
                <div class="advantage_fullDescription">
                    <p>Вы можете оплатить товар при получении наличным или безналичным способом</p>
                </div>
            </div>
            <div class="advantages_block__advantage">
                <div class="advantage_icon"><img src="/wp-content/themes/vapezone/assets/images/icons/shop.png" alt="Магазин"></div>
                <div class="advantage_title">Нас знают</div>
                <div class="advantage_description">и нам доверяют</div>
                <div class="advantage_fullDescription">
                    <p>У нас свои розничные магазины KAIF SMOKE и склады в Санкт-Петербурге</p>
                </div>
            </div>
            <div class="advantages_block__advantage">
                <div class="advantage_icon"><img src="/wp-content/themes/vapezone/assets/images/icons/garancy.png" alt="Гарантия"></div>
                <div class="advantage_title">Гарантия</div>
                <div class="advantage_description">качества</div>
                <div class="advantage_fullDescription">
                    <p>В случае брака мы обменяем бракованный товар</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="optDescription">
    <div class="container">
        <div class="optDescription_block">
            <p>Вы можете заказать у нас одноразовые электронные сигареты и устройства оптом на сайте <b><a target="_blank" href="https://ruvape.ru">ruvape.ru</a></b></p>
            <p>С нами можно связаться по почте opt@kaifsmoke.pro и телефону: 8 (812) 509-28-55</p>
        </div>
    </div>
</section>

<div class="defaultCallBackForm container">
    <div class="form_block">
        <span class="form_title">Обратная связь</span>
        <span class="form_description">Отправьте заявку для решения любых вопросов</span>
        <div class="form_fields">
            <div class="left">
                <div class="inputField">
                    <label>Имя</label>
                    <input type="text" name="callback_firstName" />
                </div>
                <div class="inputField">
                    <label>Телефон</label>
                    <input type="text" name="callback_phone" />
                </div>
                <div class="inputField">
                    <label>Электронная почта</label>
                    <input type="email" name="callback_communication" />
                </div>
            </div>
            <div class="right">
                <div class="inputField">
                    <label>Текст сообщения</label>
                    <textarea placeholder="Текст..." name="message"></textarea>
                </div>
            </div>
        </div>
        <div class="form_send">
            <button class="form_send__btn btn primary l">Отправить</button>
        </div>
    </div>
    <div class="form_sent">
        <span class="label">Отправлено!</span>
        <img src="/wp-content/themes/vapezone/assets/images/okey.png" alt="Okey">
        <span class="description">Мы свяжемся с Вами в ближайшее время</span>
    </div>
</div>
<?php
get_footer();
?>