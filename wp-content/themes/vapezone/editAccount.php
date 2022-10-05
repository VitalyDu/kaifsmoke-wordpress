<?php
/*
    Template Name: Управление аккаунтом
*/
?>
<?php
get_header();
?>
<section class="accManagePage__title">
    <div class="container">
        <div class="section_title">
            <span class="section_title__label">Управление аккаунтом</span>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>
<?php
$userdata = VZUser::get();
if ($userdata['status'] == 'ok') {
    $userdata = $userdata['out']['userdata'];
?>
<section class="accManagePage">
    <div class="container">
        <div class="accManagePage_block">
            <div class="accManagePage_block__top">
                <div class="accManagePage_top__left">
                    <div class="accManagePage_top__userImg">
                        <img src="/wp-content/themes/vapezone/assets/images/icons/userIcon.png" alt="Пользователь">
                    </div>
                    <div class="accManagePage_top__userNameHaveBonuses">
                        <h3 class="accManagePage_top__userName">
                            <?= $userdata['lastname'] . $userdata['firstname'] ?>
                        </h3>
                        <!-- <div class="accManagePage_top__haveBonuses">
                                <span class="accManagePage_haveBonuses__value">150 бонусов</span>
                                <span class="hiddenMessageBonuses">
                                Ваши накопленные бонусы.
                                Вы можете оплатить ими до 30% стоимости покупки!
                            </span>
                            </div> -->
                    </div>
                </div>
            </div>
            <div class="accManagePage_block__information">
                <div class="fields">
                    <div class="field textField">
                        <label for="">
                            Электронная почта
                        </label>
                        <input type="email" value="<?= $userdata['email'] ?>" disabled>
                    </div>
                    <div class="field textField">
                        <label for="">
                            Пол
                        </label>
                        <input type="text" value="<?= $userdata['sex'] ?>" disabled>
                    </div>
                    <div class="field textField">
                        <label for="">
                            Телефон
                        </label>
                        <input type="text" class="textField_phone" value="<?= $userdata['phone'] ?>" disabled>
                    </div>
                    <div class="field dateField">
                        <label for="">
                            Дата рождения
                        </label>
                        <input type="date" value="<?= $userdata['birthday'] ?>" disabled>
                    </div>
                    <div class="field phoneVerificationStatus">
                        <label>Подтверждение номера</label>
                        <div class="phoneVerificationStatus_success">
                            <?php if ($userdata['phone_confirmed']) { ?><img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="Success" class="phoneVerificationStatus_success__icon"><?php } ?>
                            <span class="phoneVerificationStatus_success__label"><?php if ($userdata['phone_confirmed']) { ?>Номер подтвержден<?php } else { ?>Номер не подтвержден<?php } ?></span>
                        </div>
                    </div>
                </div>
                <div class="information_buttons">
                    <button class="editProfile">
                        <span class="label">Редактировать профиль</span>
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.8488 3.92072C13.3834 3.39568 11.6156 1.62791 11.081 2.15297L10.6253 2.60867L12.3931 4.37643L12.8488 3.92072Z" fill="#6B6B63" />
                            <path d="M5.45741 11.3121L3.68965 9.54435L2.64431 12.367L5.45741 11.3121Z" fill="#6B6B63" />
                            <path d="M5.45741 11.3121L3.68965 9.54435M5.45741 11.3121L2.64431 12.367L3.68965 9.54435M5.45741 11.3121L12.3931 4.37643M3.68965 9.54435L10.6253 2.60867M12.3931 4.37643L12.8488 3.92072C13.3834 3.39568 11.6156 1.62791 11.081 2.15297L10.6253 2.60867M12.3931 4.37643L10.6253 2.60867" stroke="#6B6B63" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button class="btn s secondary margin">
                        Выйти
                    </button>
                </div>
            </div>
            <div class="accManagePage_block__editInformation">
                <div class="editInformation_names">
                    <div class="fields">
                        <div class="field textField">
                            <label>
                                Имя *
                            </label>
                            <input type="text" value="<?= $userdata['firstname'] ?>" name="user_firstName">
                        </div>
                        <div class="field textField">
                            <label>
                                Фамилия
                            </label>
                            <input type="text" value="<?= $userdata['lastname'] ?>" name="user_lastName">
                        </div>
                    </div>
                </div>
                <div class="editInformation_otherInf">
                    <span class="editInformation_otherInf__title">Информация</span>
                    <div class="fields">
                        <div class="field textField">
                            <label>
                                Электронная почта
                            </label>
                            <input type="email" value="<?= $userdata['email'] ?>" name="user_email">
                        </div>
                        <div class="field genderField selectField">
                            <label>Пол</label>
                            <span class="genderFieldVal showGenderDropdown selectFieldVal">
                                <?= $userdata['sex'] ?>
                            </span>
                            <div class="genderDropdown selectDropdown">
                                <li>Мужской</li>
                                <li>Женский</li>
                            </div>
                        </div>
                        <div class="field phoneField">
                            <label>Телефон *</label>
                            <input type="text" class="phone textField_phone" value="<?= $userdata['phone'] ?>" name="user_phone">
                            <span class="removePhone">Удалить</span>
                        </div>
                        <div class="field dateField">
                            <label for="">Дата рождения *</label>
                            <input type="date" name="birthdaydate" value="<?= $userdata['birthday'] ?>">
                        </div>
                        <div class="field phoneVerificationField">
                            <label>Подтверждение номера</label>
                            <div class="phoneVerificationField_buttonField">
                                <button class="phoneVerificationField_buttonField__sendCode sendCode_button">Отправить
                                    код</button>
                                <button class="phoneVerificationField_buttonField__sendCodeRepeat sendCode_button">Отправить
                                    повторно</button>
                                <input type="phone" maxlength="4" name="user_phoneVerificationCode" class="phoneVerificationCode" placeholder="Введите код" readonly>
                            </div>
                        </div>
                        <div class="field phoneVerificationStatus success">
                            <label>Подтверждение номера</label>
                            <div class="phoneVerificationStatus_success">
                                <img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="Success" class="phoneVerificationStatus_success__icon">
                                <span class="phoneVerificationStatus_success__label">Номер подтвержден</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="editInformation_passwords">
                    <div class="fields">
                        <div class="field passwordField">
                            <label>
                                Пароль
                            </label>
                            <input type="password" placeholder="******" name="user_password">
                        </div>
                        <div></div>
                        <div class="field passwordField">
                            <label>
                                Новый пароль
                            </label>
                            <input type="password" placeholder="******" name="user_passwordNew">
                        </div>
                        <div class="field passwordField">
                            <label>
                                Повторите новый пароль
                            </label>
                            <input type="password" placeholder="******" name="user_passwordNewRepeat">
                        </div>
                    </div>
                </div>
                <div class="editInformation_buttons">
                    <button class="saveChanges btn s primary margin">Сохранить</button>
                    <button class="cancelChanges btn s secondary margin">Отмена</button>
                </div>
            </div>
            <!-- <div class="accManagePage_block__profileInf">
                <div class="accManagePage_profileInf__address accManagePage_profileInf__block profileInf_block__nameLastName">
                    <div class="address_fields">
                        <div class="field">
                            <label for="">Имя *</label>
                            <input type="text" name="name" value="<?= $userdata['firstname'] ?>">
                        </div>
                        <div class="field">
                            <label for="">Фамилия</label>
                            <input type="text" name="lastname" value="<?= $userdata['lastname'] ?>">
                        </div>
                    </div>
                </div>
                <h3>Информация</h3>
                <div class="accManagePage_profileInf__user accManagePage_profileInf__block">
                    <div class="profileInfBlock__left">
                        <div class="field">
                            <label for="">Электронная почта <span class="hideStar">*</span></label>
                            <input type="email" name="email" value="<?= $userdata['email'] ?>">
                        </div>
                        <div class="field phoneField">
                            <label for="">Телефон</label>
                            <input type="phone" value="<?= $userdata['phone'] ?>">
                        </div>
                        <div class="field phoneFieldEdit">
                            <label for="">Телефон <span class="hideStar">*</span></label>
                            <input type="phone" name="phone" value="<?= $userdata['phone'] ?>">
                            <span class="deletePhone">Удалить номер</span>
                        </div>

                        <div class="field accManagePage_block__confirmedPhone">
                            <label>Подтверждение номера</label>
                            <div class="confirmedPhone">
                                <div class="confirmedPhone_status">
                                    <?php if ($userdata['phone_confirmed']) { ?>
                                        <div class="confirmedPhone_status__success">
                                            <img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="">
                                            <span>Номер подтвержден</span>
                                        </div>
                                    <?php } else { ?>
                                        <div class="confirmedPhone_status__failed">
                                            <span>Номер не подтвержден</span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="field phone-verification">
                            <label for="">Подтверждение номера <span class="verificatonPhoneTimer"></span></label>
                            <div class="field_phone-verification">
                                <button class="sellPhoneVerificationCode">Отправить код</button>
                                <button class="sellPhoneVerificationCodeRepeat" disabled>Отправить повторно</button>
                                <input type="phone" name="phonevercode" class="PhoneVerificationCode" placeholder="Введите код" readonly>
                                <span class="VerificationCodeError">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" fill="white" />
                                        <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#E9530D" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="text">Не правильный код</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="profileInfBlock__right">
                        <div class="field">
                            <label for="">Пол</label>
                            <input type="text" name="sex" value="<?= $userdata['sex'] ?>">
                        </div>
                        <div class="field">
                            <label for="">Дата рождения <span class="hideStar">*</span></label>
                            <input type="date" name="birthdaydate" value="<?= $userdata['birthday'] ?>">
                        </div>
                        <div class="field accManagePage_block__subscribe">
                            <label>Подписка на рассылку информации о новинках</label>
                            <div class="subscribeField">
                                <div class="subscribeField_status">
                                    <?php if ($userdata['subscribe_to_latest_products']) { ?>
                                        <div class="subscribeField_status__success">
                                            <img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="">
                                            <span>Вы подписаны!</span>
                                        </div>
                                    <?php } else { ?>
                                        <div class="subscribeField_status__failed">
                                            <span>Вы не подписаны</span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <button class="unsubscribeBtn">Отписаться</button>
                                <button class="subscribeBtn">Подписаться</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accManagePage_editLogoutBtn">
                <a class="accManagePage_editLogOutBtn__editProfile">
                    <span>Редактировать профиль</span>
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.8488 3.92072C13.3834 3.39568 11.6156 1.62791 11.081 2.15297L10.6253 2.60867L12.3931 4.37643L12.8488 3.92072Z" fill="#6B6B63" />
                        <path d="M5.45741 11.3121L3.68965 9.54435L2.64431 12.367L5.45741 11.3121Z" fill="#6B6B63" />
                        <path d="M5.45741 11.3121L3.68965 9.54435M5.45741 11.3121L2.64431 12.367L3.68965 9.54435M5.45741 11.3121L12.3931 4.37643M3.68965 9.54435L10.6253 2.60867M12.3931 4.37643L12.8488 3.92072C13.3834 3.39568 11.6156 1.62791 11.081 2.15297L10.6253 2.60867M12.3931 4.37643L10.6253 2.60867" stroke="#6B6B63" stroke-linejoin="round" />
                    </svg>
                </a>
                <a href="" class="accManagePage_editLogOutBtn__logout">
                    Выйти
                </a>
            </div> -->
            <!-- <div class="editProfilePage_block__password">
                <div class="field forgotPassword">
                    <label for="">Сменить пароль</label>
                    <a class="forgotPassword_link">Забыли пароль?</a>
                    <input type="password" name="oldpassword" placeholder="Старый пароль">
                </div>
                <div class="field">

                </div>
                <div class="field">
                    <label for="">Новый пароль</label>
                    <input type="password" name="password" placeholder="Новый пароль">
                </div>
                <div class="field">
                    <label for="">Повторите пароль</label>
                    <input type="password" name="repeatpass" placeholder="*********">
                </div>
            </div>
            <div class="editProfilePage_block__saveChanges">
                <div class="field fieldSave">
                    <button class="saveChanges">Сохранить</button>
                </div>
                <div class="field fieldCancel">
                    <a class="cancelChanges">Отмена</a>
                </div>
            </div> -->
            <!-- <div class="accManagePage_block__subscribe">
                    <div class="field">
                        <label>Подписка на рассылку информации о новинках</label>
                        <div class="subscribeField">
                            <div class="subscribeField_status">
                                <div class="subscribeField_status__success">
                                    <img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="">
                                    <span>Вы подписаны!</span>
                                </div>
                                <div class="subscribeField_status__failed">
                                    <span>Вы не подписаны</span>
                                </div>
                            </div>
                            <button class="subscribeBtn">Подписаться</button>
                            <button class="unsubscribeBtn">Отписаться</button>
                        </div>
                    </div>
                </div> -->
            <!-- <div class="MobileAccManagePage_block__editProfileExit onlyMobile">
                    <a class="accManagePage_top__editProfile">
                        <span>Редактировать профиль</span>
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.8488 3.92072C13.3834 3.39568 11.6156 1.62791 11.081 2.15297L10.6253 2.60867L12.3931 4.37643L12.8488 3.92072Z"
                                fill="#6B6B63" />
                            <path d="M5.45741 11.3121L3.68965 9.54435L2.64431 12.367L5.45741 11.3121Z" fill="#6B6B63" />
                            <path
                                d="M5.45741 11.3121L3.68965 9.54435M5.45741 11.3121L2.64431 12.367L3.68965 9.54435M5.45741 11.3121L12.3931 4.37643M3.68965 9.54435L10.6253 2.60867M12.3931 4.37643L12.8488 3.92072C13.3834 3.39568 11.6156 1.62791 11.081 2.15297L10.6253 2.60867M12.3931 4.37643L10.6253 2.60867"
                                stroke="#6B6B63" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <a href="" class="accManagePage_top__logout">
                        Выйти
                    </a>
                </div> -->
        </div>
    </div>
</section>
<?php } ?>
<!-- <section class="editProfilePage">
        <div class="container">
            <div class="editProfilePage_block">
                <div class="editProfilePage_block__firstLastName">
                    <div class="field">
                        <label for="">Имя *</label>
                        <input type="text" name="name" value="Даниил">
                    </div>
                    <div class="field">
                        <label for="">Фамилия</label>
                        <input type="email" name="lastname" value="Манукало">
                    </div>
                </div>
                <div class="editProfilePage_block__profileInf">
                    <div class="editProfilePage_profileInf__user editProfilePage_profileInf__block">
                        <h3>Информация</h3>
                        <div class="field">
                            <label for="">Электронная почта</label>
                            <input type="email" value="pochta@gmail.com">
                        </div>
                        <div class="field phoneNumberConfirmed">
                            <label for="">Телефон</label>
                            <input type="phone" value="8 (995) 990-77-77" readonly>
                            <button class="deletePhoneNumber">Удалить номер</button>
                        </div> -->


<!-- Подтверждён -->


<!-- <div class="field accManagePage_block__confirmedPhone">
                            <label>Подтверждение номера</label>
                            <div class="confirmedPhone">
                                <div class="confirmedPhone_status">
                                    <div class="confirmedPhone_status__success">
                                        <img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="">
                                        <span>Номер подтвержден</span>
                                    </div>
                                </div>
                            </div>
                        </div> -->


<!-- Не подтверждён -->


<!-- <div class="field phone-verification">
                            <label for="">Подтверждение номера <span class="verificatonPhoneTimer"></span></label>
                            <div class="field_phone-verification">
                                <button class="sellPhoneVerificationCode">Отправить код</button>
                                <button class="sellPhoneVerificationCodeRepeat" disabled>Отправить повторно</button>
                                <input type="phone" class="PhoneVerificationCode" placeholder="Введите код" readonly>
                                <span class="VerificationCodeError">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" fill="white" />
                                        <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#E9530D" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span class="text">Не правильный код</span>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label for="">Пол</label>
                            <select name="sex" id="sex" class="custom-select man" placeholder="Выберите пол">
                                <option value="man">Мужской</option>
                                <option value="women">Женский</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="">Дата рождения</label>
                            <input type="date" value="27.07.2000">
                        </div>
                    </div>
                    <div class="editProfilePage_profileInf__address editProfilePage_profileInf__block">
                        <h3>Адрес доставки</h3>
                        <div class="field">
                            <label for="">Город</label>
                            <input type="text" value="Санкт-Петербург">
                        </div>
                        <div class="field">
                            <label for="">Адрес</label>
                            <input type="text" value="Лиговский пр. д. 55">
                        </div>
                    </div>
                </div>
                <div class="editProfilePage_block__password">
                    <div class="field forgotPassword">
                        <label for="">Сменить пароль</label>
                        <a class="forgotPassword_link">Забыли пароль?</a>
                        <input type="password" placeholder="Старый пароль">
                    </div>
                    <div class="field">

                    </div>
                    <div class="field">
                        <label for="">Новый пароль</label>
                        <input type="password" value="Новый пароль">
                    </div>
                    <div class="field">
                        <label for="">Повторите пароль</label>
                        <input type="password" value="*********">
                    </div>
                </div>
                <div class="editProfilePage_block__saveChanges">
                    <div class="field fieldSave">
                        <button class="saveChanges">Сохранить</button>
                    </div>
                    <div class="field fieldCancel">
                        <a class="cancelChanges">Отмена</a>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
<?php
get_footer();
?>