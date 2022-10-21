<?php
/*
    Template Name: Избранное
*/
?>
<?php
get_header();
?>
<div class="pageLoader favoritesLoading">
    <div class="spinner"></div>
</div>

<section class="favoritesPage__title" style="display: none;">
    <div class="container">
        <div class="section_title">
            <span class="section_title__label">Избранное</span>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php true_breadcrumbs(); ?>
            </ul>
        </div>
    </div>
</section>

<section class="favoritesPage" style="display: none;">
    <div class="container">
        <div class="favoritesPage_block">
            <div class="favoritesPage_block__top">
                <a href="/catalog" class="returnToCatalog">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14.7539 7.55277C14.9492 7.35751 14.9492 7.04093 14.7539 6.84567C14.5587 6.6504 14.2421 6.6504 14.0468 6.84567L14.7539 7.55277ZM9.60039 11.9992L9.24684 11.6457L8.89328 11.9992L9.24684 12.3528L9.60039 11.9992ZM14.0468 17.1528C14.2421 17.348 14.5587 17.348 14.7539 17.1528C14.9492 16.9575 14.9492 16.6409 14.7539 16.4457L14.0468 17.1528ZM14.0468 6.84567L9.24684 11.6457L9.95394 12.3528L14.7539 7.55277L14.0468 6.84567ZM9.24684 12.3528L14.0468 17.1528L14.7539 16.4457L9.95394 11.6457L9.24684 12.3528Z"
                            fill="#000000" />
                    </svg>
                    <span>Вернуться к покупкам</span>
                </a>
                <div class="favoritesPage_top__productsQuantityClearBasket">
                    <a class="favoritesPage_top__clearBasket clearFavorites">
                        <span class="favoritesPage_clearBasket__text">Очистить всё</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white" />
                            <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#000000" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="favoritesPage_block__products">
            </div>
        </div>
    </div>
</section>

<section class="favoritesEmpty answerPage notHaveBlock" style="display: none;">
    <div class="container">
        <div class="answerPage_block">
            <h1>Список избранного пуст</h1>
            <img src="/wp-content/themes/vapezone/assets/images/icons/NotFoundHave.png" alt="Избранное">
            <a class="onHomePage  btn l primary" href="/catalog">Перейти в каталог</a>
        </div>
    </div>
</section>
<?php
get_footer();
?>