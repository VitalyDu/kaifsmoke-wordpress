<?php
/*
    Template Name: Каталог
*/
get_header();
?>
<section class="catalogPage">
    <div class="container">
        <div class="section_title">
            <h1><?php the_title(); ?></h1>
        </div>
        <div class="catalogPage_block">
            <div class="catalogPage_block__items">
                <?php if (get_field('main_catalog', 'option')) : ?>
                    <?php while (has_sub_field('main_catalog', 'option')) : ?>
                        <a href="<?php the_sub_field('link'); ?>" class="catalogPage_items__item">
                            <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('name'); ?>"/>
                            <span class="itemName">
                                <?php the_sub_field('name'); ?>
                            </span>
                        </a>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section class="allProducts">
    <div class="container">
        <div class="section_title">
            <h2>Все товары</h2>
        </div>
        <div class="allProducts_quantity">
            <?
//            $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'id');
//            $products = new WP_Query($args);
//            echo $products->found_posts;
            //товаров ?>

        </div>
        <div class="allProducts_view hidden-mobile">
            <ul>
                <li class="allProducts_view__showTable active">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white"/>
                            <rect x="3" y="3" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round"/>
                            <rect x="3" y="13" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round"/>
                            <rect x="13" y="3" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round"/>
                            <rect x="13" y="13" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round"/>
                        </svg>
                        Таблица
                    </a>
                </li>
                <li class="allProducts_view__showList">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white"/>
                            <path d="M3 3H21V11H3V3Z" stroke="#1D1D1B" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 13H21V21H3V13Z" stroke="#1D1D1B" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                        Список
                    </a>
                </li>
            </ul>
        </div>
        <div class="allProducts_view hidden-desktop">
            <ul>
                <li class="allProducts_view__showTable active">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white"/>
                            <rect x="13" y="3" width="8" height="18" stroke="#1D1D1B" stroke-linejoin="round"/>
                            <rect x="3" y="3" width="8" height="18" stroke="#1D1D1B" stroke-linejoin="round"/>
                        </svg>
                        Два товара
                    </a>
                </li>
                <li class="allProducts_view__showList">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="3" width="12" height="18" stroke="#1D1D1B" stroke-linejoin="round"/>
                        </svg>
                        Один товар
                    </a>
                </li>
            </ul>
        </div>
        <div class="productsWrapper table " id="catalog_main_ajax">
            <?php VZCatalogMain::print(); ?>
        </div>
        <?php VZCatalogMain::printAfter(); ?>
    </div>
</section>
<?php
get_footer();
?>