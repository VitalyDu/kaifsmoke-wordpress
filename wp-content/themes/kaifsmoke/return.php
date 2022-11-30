<?php
/*
    Template Name: Возврат
*/
?>
<?php
get_header();
?>
<section class="return">
    <div class="container">
        <div class="section_title">
            <h2>Условия обмена и возврата</h2>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<section class="returnDescription">
    <div class="container">
        <div class="returnDescription_block">
            <div class="returnDescription_block__item">
                <div class="returnDescription_item__title">
                    <h3>Уважаемые клиенты!</h3>
                </div>
                <p class="main_text">
                    Если у вас имеются претензии по качеству работы электронных сигарет, то вы можете написать (скинуть фото или видео дефекта) нашей экспертной службе в Telegram или WhatsApp на номер <b>+7 (931) 30-21-245</b>. Экспертная служба работает по будням с 10:00 до 19:00. <b>Обращения принимаются только в виде сообщений в мессенджере.</b>
                    <br />
                    <br />
                    Если вам необходимо что-то уточнить по телефону, то звоните в наш офис <a href="tel:+78122411765">+7 (812) 241-17-65</a>.
                </p>
            </div>
            <div class="returnDescription_block__item">
                <div class="returnDescription_item__title">
                    <h3>Изделия, не подлежащие обмену:</h3>
                </div>
                <ul>
                    <li>расходные материалы (спирали, испарители, картриджи, проволока и т.д.);</li>
                    <li>атомайзеры (за исключением полного производственного брака). Протечки атомайзера не являются
                        гарантийным случаем,
                        кроме случаев выявления и подтверждения производственного брака;</li>
                    <li>сменные аккумуляторы – гарантируется работа на момент приобретения;</li>
                    <li>жидкости и ароматизаторы;</li>
                    <li>аксессуары (за исключением полного производственного брака);</li>
                    <li>на USB-кабели и USB зарядные устройства. Гарантия составляет 7 дней со дня фактического
                        приобретения товара.</li>
                </ul>
            </div>
            <div class="returnDescription_block__item">
                <div class="returnDescription_item__title">
                    <h3>Изделия, подлежащие обмену:</h3>
                </div>
                <ul>
                    <li>модификационные батарейные блоки (варивольты, моды);</li>
                    <li>зарядные устройства для аккумуляторов;</li>
                    <li>механические моды (только производственный брак);</li>
                    <li>одноразовые электронные сигареты, в случае, если брак был обнаружен при проверке у кассы сразу
                        после покупки. В остальных случаях одноразовые электронные сигареты обмену и возврату не
                        подлежат, а также не имеют гарантийного срока;</li>
                    <li>одноразовые электронные сигареты не подлежат обмену и возврату в случае интернет-заказа.</li>
                </ul>
                <p>Гарантия не действует если были нарушены правила эксплуатации или в случае некорректного обращения.
                </p>
            </div>
            <div class="returnDescription_block__item">
                <div class="returnDescription_item__title">
                    <h3>Условия возврата и обмена качественного товара</h3>
                    <h4>Возврат товара надлежащего качества осуществляется в течении 14 дней со дня покупки в следующих
                        случаях:</h4>
                </div>
                <ul>
                    <li>товар не был в эксплуатации;</li>
                    <li>упаковка товара не была вскрыта и имеет товарный вид;</li>
                    <li>механические моды (только производственный брак);</li>
                    <li>сохранена полная комплектация.</li>
                </ul>
                <p>Электронные сигареты, батарейные моды и электронные кальяны являются технически сложными товарами.
                </p>
                <p>Дрипки, клиромайзеры, обслуживаемые атомайзеры являются предметом личной гигиены.
                    Возврат средств за данные товары производится только при соблюдении строгих правил, указанных выше.
                </p>
            </div>
        </div>
    </div>
</section>
<div class="defaultCallBackForm container">
    <div class="form_block">
        <span class="form_title">Заявка на возврат</span>
        <span class="form_description">Рассмотрение заявки происходит в течение трёх рабочих дней</span>
        <div class="form_fields">
            <div class="left">
                <div class="inputField">
                    <label>Номер заказа</label>
                    <input type="text" name="callback_order" />
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
        <img src="/wp-content/themes/kaifsmoke/assets/images/okey.png" alt="Okey">
        <span class="description">Мы свяжемся с Вами в ближайшее время</span>
    </div>
</div>
<!-- <div class="returnForm">
    <div class="container">
        <div class="returnForm_block">
            <div class="returnForm_block__title">
                <h2>Возврат</h2>
            </div>
            <div class="returnForm_block__description">
                <p>В случае неполной комплектации, частичного или полного производственного брака,
                    вы можете обменять или вернуть товар в течение 14 календарных дней.</p>
                <p>При возникновении любых вопросов заполните форму или свяжитесь с нами по телефону: 8 (812) 241-13-10
                </p>
            </div>
            <div class="return form">
                <div class="return_block form_block">
                    <div class="return_block__form form_block__form">
                        <div class="returnBlock_form__title formBlock_form__title">
                            <h2>Заявка на возврат</h2>
                            <span>Рассмотрение заявки происходит в течение трёх рабочих дней</span>
                        </div>
                        <div class="returnBlock_form__fields formBlock_form__fields">
                            <div class="returnBlockForm_fields__left formBlockForm_fields__left">
                                <input type="text" name="name" class="orderNumber" placeholder="Номер заказа">
                                <input type="phone" name="phone" class="userPhone" placeholder="Телефон">
                                <input type="email" name="email" class="userEmail" placeholder="Электронная почта">
                                <span class="uploadFiles">
                                    <input type="file" id="files" name="files[]" multiple hidden />
                                    <label for="files">
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 10V20M20 30V20M20 20H10H30" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </label>
                                </span>
                                <label for="">Прикрепите фотографии</label>
                            </div>
                            <div class="returnBlockForm_fields__right formBlockForm_fields__right">
                                <textarea name="message" id="" cols="30" rows="9" placeholder="Полное описание проблемы"></textarea>
                            </div>
                        </div>
                        <div class="returnBlock_form_sendBtn formBlock_form_sendBtn">
                            <button type="submit" class="sendFormBtn bttOrange">Отправить</button>
                        </div>
                    </div>
                    <div class="return_block__send form_block__send">
                        <h2>Отправлено!</h2>
                        <img src="/wp-content/themes/kaifsmoke/assets/images/okey.png" alt="">
                        <span>Мы свяжемся с Вами в ближайшее время</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<?php
get_footer();
?>