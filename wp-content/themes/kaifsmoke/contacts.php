<?php
/*
    Template Name: Контакты
*/
?>
<?php
get_header();
?>
<section class="contacts">
    <div class="container">
        <div class="section_title">
            <h1>Контакты</h1>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
        <div class="contacts_blocks">
            <div class="contacts_blocks__onlineOrders contacts_blocks__item">
                <div class="contacts_blocksItem__image">
                    <img src="/wp-content/themes/vapezone/assets/images/contactsImages/onlineOrders.png" alt="Заказы">
                </div>
                <div class="onlineOrders_content conctacts_blocksItem__content">
                    <span class="conctacts_blocksItem__title">Онлайн заказы</span>
                    <div class="onlineOrders_content__conctactInf">
                        <div class="contactInf_item">
                            <span class="contactInf_item__name">График работы</span>
                            <?php while (has_sub_field('online', 'option')) : ?>
                                <span class="contactInf_item__value"><?php the_sub_field('schedule'); ?></span>
                            <?php endwhile; ?>
                        </div>
                        <div class="contactInf_item">
                            <span class="contactInf_item__name">Электронная почта</span>
                            <?php while (has_sub_field('online', 'option')) : ?>
                                <span class="contactInf_item__value"><?php the_sub_field('email'); ?></span>
                            <?php endwhile; ?>
                        </div>
                        <div class="contactInf_item">
                            <span class="contactInf_item__name">Телефон</span>
                            <?php while (has_sub_field('online', 'option')) : ?>
                                <span class="contactInf_item__value"><?php the_sub_field('phone'); ?></span>
                            <?php endwhile; ?>
                        </div>
                        <div class="contactInf_item">
                            <span class="contactInf_item__name">По вопросам жалоб на сервис и продавцов:</span>
                            <a class="contactInf_item__value" href="mailto:prodman@vapezone.ru">prodman@vapezone.ru</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contacts_blocks__optBlock contacts_blocks__item">
                <div class="contacts_blocksItem__image">
                    <img src="/wp-content/themes/vapezone/assets/images/contactsImages/optBlock.png" alt="Опт">
                </div>
                <div class="optBlock_content conctacts_blocksItem__content">
                    <span class="conctacts_blocksItem__title">Оптовый раздел</span>
                    <div class="optBlock_content__text">
                        <p>Вы можете заказать у нас одноразовые электронные сигареты и устройства оптом.</p>
                        <a href="/opt/">Перейти в оптовый раздел</a>
                    </div>
                </div>
            </div>
            <div class="contacts_blocks__socialBlock contacts_blocks__item" id="socialLinks">
                <div class="contacts_blocksItem__image">
                    <img src="/wp-content/themes/vapezone/assets/images/contactsImages/socialBlock.png" alt="Соц. сети">
                </div>
                <div class="socialBlock_content conctacts_blocksItem__content">
                    <span class="conctacts_blocksItem__title">Социальные сети</span>
                    <div class="socialBlock_content__text">
                        <?php if (get_field('social', 'option')) : ?>
                            <?php while (has_sub_field('social', 'option')) : ?>
                                <? if (!get_sub_field('hidden')) { ?>
                                    <a href="<?php the_sub_field('link'); ?>">
                                        <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('name'); ?>" />
                                    </a>
                                <? } ?>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="contacts_blocks__vacancyBlock contacts_blocks__item">
                <div class="contacts_blocksItem__image">
                    <img src="/wp-content/themes/vapezone/assets/images/contactsImages/vacancies.png" alt="Вакансии">
                </div>
                <div class="vacancyBlock_content conctacts_blocksItem__content">
                    <span class="conctacts_blocksItem__title">Вакансии</span>
                    <div class="vacancyBlock_content__text">
                        <p>Добро пожаловать в команду!</p>
                        <a href="https://spb.hh.ru/employer/3185610">Смотреть все вакансии</a>
                    </div>
                </div>
            </div>
            <div class="contacts_blocks__rekvizitsBlock contacts_blocks__item">
                <div class="contacts_blocksItem__image">
                    <img src="/wp-content/themes/vapezone/assets/images/contactsImages/rekvizits.png" alt="Реквизиты">
                </div>
                <div class="rekvizitsBlock_content conctacts_blocksItem__content">
                    <span class="conctacts_blocksItem__title">Реквизиты</span>
                    <div class="rekvizitsBlock_content__rekvizits">
                        <div class="rekvizits_item">
                            <span class="rekvizits_item__name">ИП</span>
                            <?php while (has_sub_field('requisites', 'option')) : ?>
                                <span class="rekvizits_item__value"><?php the_sub_field('ip'); ?></span>
                            <?php endwhile; ?>
                        </div>
                        <div class="rekvizits_item">
                            <span class="rekvizits_item__name">ИНН</span>
                            <?php while (has_sub_field('requisites', 'option')) : ?>
                                <span class="rekvizits_item__value"><?php the_sub_field('inn', 'option'); ?></span>
                            <?php endwhile; ?>
                        </div>
                        <div class="rekvizits_item">
                            <span class="rekvizits_item__name">ОГРНИП</span>
                            <?php while (has_sub_field('requisites', 'option')) : ?>
                                <span class="rekvizits_item__value"><?php the_sub_field('ogrnip', 'option'); ?></span>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
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
                        <button class="form_send__btn">Отправить</button>
                    </div>
                </div>
                <div class="form_sent">
                    <span class="label">Отправлено!</span>
                    <img src="/wp-content/themes/vapezone/assets/images/okey.png" alt="Okey">
                    <span class="description">Мы свяжемся с Вами в ближайшее время</span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>