<?php
/*
    Template Name: Сбросить пароль
*/
?>
<?php
get_header();
?>
<section class="passwordRecoveryPage__title">
    <div class="container">
        <div class="section_title">
            <h2>Сброс пароля</h2>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<div class="advantages passwordRecoveryAdvantages">
    <div class="container">
        <div class="advantages_block">
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/vapezone/assets/images/icons/accelerated.png" alt="Оформление" class="icon">
                <span class="advantage_title">Ускоренное</span>
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

<div class="passwordRecoveryPage">
    <div class="container">
        <div class="passwordRecoveryPage_block">
            <div class="passwordRecoveryPage_block__form passwordResetForm">
                <h3>
                    Введите новый пароль для вашей учётной записи
                </h3>
                <?php if (!empty($_GET['reset_key']) && !empty($_GET['email'])) { ?>
                    <input type="hidden" name="user_resetKey" value="<?= $_GET['reset_key'] ?>">
                    <input type="hidden" name="user_email" value="<?= $_GET['email'] ?>">
                    <div class="fields">
                        <div class="field passwordField">
                            <label>
                                Введите новый пароль *
                            </label>
                            <input type="password" placeholder="******" name="user_passwordNew">
                        </div>
                        <div class="field passwordField">
                            <label>
                                Повторите пароль *
                            </label>
                            <input type="password" placeholder="******" name="user_passwordNewRepeat">
                        </div>
                    </div>
                    <div class="passwordRecoveryPage_form__sendNewPassword">
                        <button class="btn primary l sendPasswordResetForm">Отправить</button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>