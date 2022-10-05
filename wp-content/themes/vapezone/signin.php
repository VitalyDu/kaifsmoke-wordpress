<?php
/*
    Template Name: Авторизация
*/
?>
<?php
get_header();
?>
<section class="signInPage__title">
    <div class="container">
        <div class="section_title">
            <h1>Войти в аккаунт</h1>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<div class="advantages signInAdvantages">
    <div class="container">
        <div class="advantages_block">
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/vapezone/assets/images/icons/accelerated.png" alt="Оформление" class="icon">
                <span class="advantage_title">Ускоренное</ы>
                <span class="advantage_subtitle">Оформление заказов</span>
            </div>
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/vapezone/assets/images/icons/fast.png" alt="Быстро" class="icon">
                <span class="advantage_title">Быстрое</span>
                <span class="advantage_subtitle">Заполнение форм</span>
            </div>
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/vapezone/assets/images/icons/eye.png" alt="Отслеживание" class="icon">
                <span class="advantage_title">Отслеживание</span>
                <span class="advantage_subtitle">Заказов</span>
            </div>
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/vapezone/assets/images/icons/cashback.png" alt="Кэшбэк" class="icon">
                <span class="advantage_title">Начисление бонусов</span>
                <span class="advantage_subtitle">На товары без никотина</span>
            </div>
        </div>
    </div>
</div>

<div class="signInPage">
    <div class="container">
        <div class="signInPage_block">
            <form class="signInPage_block__form">
                <div class="field">
                    <label for="phoneLogin">Телефон *</label>
                    <input id="phoneLogin" name="phoneLogin" type="text" placeholder="Телефон" autocomplete="disabled">
                </div>
                <div class="field">
                    <label for="password">Пароль *</label>
                    <input id="password" name="password" type="password" placeholder="Пароль">
                    <a href="/password-recovery" class="forgotPassword">Забыли пароль?</a>
                </div>
                <div class="signInPage_form__signInSignUp">
                    <div class="signInPage_form__signIn">
                        <span class="signIn_btn btnOrange">Войти</span>
                        <label class="custom-checkbox">
                            <input type="checkbox" value="">
                            <span>Запомнить меня</span>
                        </label>
                    </div>
                    <div class="signInPage_form__signUp">
                        <a href="/signup">Регистрация</a>
                    </div>
                </div>
                <div class="signIn_errors">
                </div>
            </form>
        </div>
    </div>
</div>
<?php
get_footer();
?>