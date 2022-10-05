<?php
/*
    Template Name: Регистрация
*/
?>
<?php
get_header();
?>
<section class="signUpPage__title">
    <div class="container">
        <div class="section_title">
            <h2>Регистрация</h2>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<div class="advantages">
    <div class="container">
        <div class="advantages_block">
            <div class="advantages_block__item">
                <img src="/wp-content/themes/vapezone/assets/images/icons/accelerated.png" alt="Оформление" class="icon">
                <span class="advantage_title">Ускоренное</span>
                <span class="advantage_subtitle">Оформление заказов</span>
            </div>
            <div class="advantages_block__item">
                <img src="/wp-content/themes/vapezone/assets/images/icons/fast.png" alt="Быстро" class="icon">
                <span class="advantage_title">Быстрое</span>
                <span class="advantage_subtitle">Заполнение форм</span>
            </div>
            <div class="advantages_block__item">
                <img src="/wp-content/themes/vapezone/assets/images/icons/eye.png" alt="Отслеживание" class="icon">
                <span class="advantage_title">Отслеживание</span>
                <span class="advantage_subtitle">Заказов</span>
            </div>
            <div class="advantages_block__item">
                <img src="/wp-content/themes/vapezone/assets/images/icons/cashback.png" alt="Кэшбэк" class="icon">
                <span class="advantage_title">Начисление бонусов</span>
                <span class="advantage_subtitle">На товары без никотина</span>
            </div>
        </div>
    </div>
</div>

<section class="signUpPage">
    <div class="container">
        <div class="signUpPage_block user_form">
            <input name="action" type="hidden" value="register_action">
            <div class="signUpPage_block__profileInf">
                <h3>Информация</h3>
                <div class="signUpPage_profileInf__user signUpPage_profileInf__block">
                    <div class="field textField">
                        <label>
                            Имя *
                        </label>
                        <input type="text" name="user_firstname" placeholder="Имя *">
                    </div>
                    <div class="field textField">
                        <label>
                            Фамилия
                        </label>
                        <input type="text" name="user_lastname" placeholder="Фамилия">
                    </div>
                    <div class="field textField">
                        <label>
                            Электронная почта *
                        </label>
                        <input type="email" name="user_email" placeholder="pochta@gmail.com">
                    </div>
                    <div class="field genderField selectField">
                        <label>Пол</label>
                        <span class="genderFieldVal showGenderDropdown selectFieldVal">
                            Выберите пол
                        </span>
                        <div class="genderDropdown selectDropdown">
                            <li>Мужской</li>
                            <li>Женский</li>
                        </div>
                    </div>
                    <div class="field phoneField">
                        <label>Телефон *</label>
                        <input type="text" class="phone textField_phone" name="user_phone" placeholder="Телефон">
                    </div>
                    <div class="field dateField">
                        <label for="">Дата рождения *</label>
                        <input type="date" name="user_birthday" id="date" min="<?php $date = new DateTime('-90 years');
                                                                                echo $date->format('Y-m-d'); ?>" max="<?php $date = new DateTime();
                                                                                                                        echo $date->format('Y-m-d'); ?>">
                    </div>
                    <div class="field phoneVerificationField active">
                        <label>Подтверждение номера</label>
                        <div class="phoneVerificationField_buttonField">
                            <button class="phoneVerificationField_buttonField__sendCode sendCode_button btn s primary">Отправить
                                код</button>
                            <button class="phoneVerificationField_buttonField__sendCodeRepeat sendCode_button btn s primary">Отправить
                                повторно</button>
                            <input type="text" maxlength="4" name="user_phoneVerificationCode" class="phoneVerificationCode" placeholder="Введите код" readonly>
                        </div>
                    </div>
                    <div class="field phoneVerificationStatus">
                        <label>Подтверждение номера</label>
                        <div class="phoneVerificationStatus_success">
                            <img src="/wp-content/themes/vapezone/assets/images/icons/subscribeAccess.png" alt="Success" class="phoneVerificationStatus_success__icon">
                            <span class="phoneVerificationStatus_success__label">Номер подтвержден</span>
                        </div>
                    </div>
                    <!-- <div class="field phone-verification">
                        <label for="">Подтверждение номера <span class="verificatonPhoneTimer"></span></label>
                        <div class="field_phone-verification">
                            <button class="sellPhoneVerificationCode">Отправить код</button>
                            <button class="sellPhoneVerificationCodeRepeat" disabled>Отправить повторно</button>
                            <input type="phone" name="confirmCode" class="PhoneVerificationCode" placeholder="Введите код" readonly>
                            <span class="VerificationCodeError">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" fill="white" />
                                    <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#E9530D" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="text">Неправильный код</span>
                            </span>
                        </div>
                    </div> -->
                </div>
                <!-- <h3>Адрес доставки</h3>
                <div class="signUpPage_profileInf__address signUpPage_profileInf__block">
                    <div class="field">
                        <label for="">Город</label>
                        <input type="text" name="city" placeholder="Город" autocomplete="disabled">
                    </div>
                    <div class="field">
                        <label for="">Адрес</label>
                        <input type="text" name="address" value="" placeholder="Адрес" autocomplete="disabled">
                    </div>
                </div> -->
            </div>
            <h3>Пароль</h3>
            <div class="signUpPage_block__password">
                <div class="field passwordField">
                    <label>
                        Придумайте пароль*
                    </label>
                    <input type="password" placeholder="******" name="user_password">
                </div>
                <div class="field passwordField">
                    <label>
                        Повторите пароль*
                    </label>
                    <input type="password" placeholder="******" name="user_passwordRepeat">
                </div>
            </div>
            <div class="signUpPage_block__signUp">
                <div class="field fieldSignUp">
                    <span type="button" class="signUp_btn btn s primary margin">Зарегистрироваться</span>
                    <label class="custom-checkbox">
                        <input id="soglObrPersData" type="checkbox" name="dataProcessing">
                        <span>Я согласен на обработку персональных данных</span>
                    </label>
                    <label class="custom-checkbox">
                        <input type="checkbox" name="subscribition">
                        <span>Я хочу получать информацию о новинках</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>