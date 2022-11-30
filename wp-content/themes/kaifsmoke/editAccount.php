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
                            <img src="/wp-content/themes/kaifsmoke/assets/images/icons/userIcon.png" alt="Пользователь">
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
                                <?php if ($userdata['phone_confirmed']) { ?><img src="/wp-content/themes/kaifsmoke/assets/images/icons/subscribeAccess.png" alt="Success" class="phoneVerificationStatus_success__icon"><?php } ?>
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
                                    <button class="phoneVerificationField_buttonField__sendCode sendCode_button btn s primary firstSend">Позвонить</button>
                                    <input type="text" maxlength="4" name="user_phoneVerificationCode" class="phoneVerificationCode" placeholder="Введите код" readonly>
                                </div>
                                <span class="description warning">Вам поступит звонок, введите 4 последних цифры входящего номера</span>
                            </div>
                            <div class="field phoneVerificationStatus success">
                                <label>Подтверждение номера</label>
                                <div class="phoneVerificationStatus_success">
                                    <img src="/wp-content/themes/kaifsmoke/assets/images/icons/subscribeAccess.png" alt="Success" class="phoneVerificationStatus_success__icon">
                                    <span class="phoneVerificationStatus_success__label">Номер подтвержден</span>
                                </div>
                            </div>

                            <!-- <div class="field phoneVerificationField">
                                <label>Подтверждение номера</label>
                                <div class="phoneVerificationField_buttonField">
                                    <button class="phoneVerificationField_buttonField__sendCode sendCode_button btn s primary firstSend">Позвонить</button>
                                    <input type="phone" maxlength="4" name="user_phoneVerificationCode" class="phoneVerificationCode" placeholder="Введите код" readonly>
                                </div>
                            </div>
                            <div class="field phoneVerificationStatus success">
                                <label>Подтверждение номера</label>
                                <div class="phoneVerificationStatus_success">
                                    <img src="/wp-content/themes/kaifsmoke/assets/images/icons/subscribeAccess.png" alt="Success" class="phoneVerificationStatus_success__icon">
                                    <span class="phoneVerificationStatus_success__label">Номер подтвержден</span>
                                </div>
                            </div> -->
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
            </div>
        </div>
    </section>
<?php } ?>
<?php
get_footer();
?>