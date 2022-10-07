<?php
/*
    Template Name: Доставка и оплата
*/
?>
<?php
get_header();
?>
<section class="delivery">
    <div class="container">
        <div class="section_title">
            <h1>Доставка и оплата</h1>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<section class="deliveryDescription">
    <div class="container">
        <div class="deliveryDescription_block">
            <p>Мы осуществляем доставку продукции, не содержащей никотин - устройства и их комплектующие.</p>
            <p>Минимальная сумма заказа для почтового отправления - 1000 руб.</p>
            <p>Доставка жидкостей, одноразовых электронных сигарет и предзаправленных картриджей не осуществляется в
                связи с поправками в законодательстве от 18 января. </p>
            <p>Если в корзине есть никотиносодержащая продукция, заказ можно оформить ТОЛЬКО с самовывозом и оплатой в
                нашем магазине! </p>
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