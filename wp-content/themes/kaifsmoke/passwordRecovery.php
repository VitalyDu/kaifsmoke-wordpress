<?php
/*
    Template Name: Восстановить пароль
*/
?>
<?php
get_header();
?>
<section class="passwordRecoveryPage__title">
    <div class="container">
        <div class="section_title">
            <h2>Восстановление пароля</h2>
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
                <img src="/wp-content/themes/kaifsmoke/assets/images/icons/accelerated.png" alt="Оформление" class="icon">
                <span class="advantage_title">Ускоренное</span>
                <span class="advantage_subtitle">Оформление заказов</span>
            </div>
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/kaifsmoke/assets/images/icons/fast.png" alt="Быстро" class="icon">
                <span class="advantage_title">Быстрое</span>
                <span class="advantage_subtitle">Заполнение форм</span>
            </div>
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/kaifsmoke/assets/images/icons/eye.png" alt="Отслеживание" class="icon">
                <span class="advantage_title">Отслеживание</span>
                <span class="advantage_subtitle">Заказов</span>
            </div>
            <div class="advantages_block__item">
                <div class="backdrop"></div>
                <img src="/wp-content/themes/kaifsmoke/assets/images/icons/cashback.png" alt="Кэшбэк" class="icon">
                <span class="advantage_title">Начисление бонусов</span>
                <span class="advantage_subtitle">На товары без никотина</span>
            </div>
        </div>
    </div>
</div>

<div class="passwordRecoveryPage">
    <div class="container">
        <div class="passwordRecoveryPage_block">
            <div class="passwordRecoveryPage_block__form passwordRecoveryForm">
                <h3>
                    Вам на почту будет отправлена информация
                    по восстановлению пароля
                </h3>
                <div class="field textField">
                    <label>
                        Электронная почта
                    </label>
                    <input type="email" placeholder="pochta@gmail.com" name="user_email">
                </div>
                <div class="passwordRecoveryPage_form__sendNewPassword">
                    <button class="btn primary l sendPasswordRecoveryForm">Отправить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>