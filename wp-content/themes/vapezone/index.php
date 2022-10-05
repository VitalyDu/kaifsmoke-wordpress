<?php
/*
    Template Name: Главная
*/
get_header();
?>
<section class="promo">
    <div class="container">
        <div class="promo_block">
            <?php if (get_field('main_slider', 'option')) : ?>
                <div class="promo_block__carousel owl-carousel owl-theme">
                    <?php while (has_sub_field('main_slider', 'option')) : ?>
                        <div class="carousel_item" style="background: url('<?php the_sub_field('image'); ?>'); background-size: cover; background-position: center;">
                            <div class="carousel_item__gradient"></div>
                            <div class="carousel_item__content">
                                <span class="carousel_title"><?php the_sub_field('title'); ?></span>
                                <span class="carousel_subtitle"><?php the_sub_field('subtitle'); ?></span>
                                <span class="carousel_item__address">
                                    <?php the_sub_field('desc'); ?>
                                </span>
                                <a href="<?php the_sub_field('link'); ?>" class="carousel_item__link btn l primary">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <div class="promo_block__otherLinks">
                <a href="/shops" class="otherLinks_block promoShops">
                    <div class="otherLinks_block__gradient"></div>
                    <div class="otherLinks_block__icon">
                        <img src="/wp-content/themes/vapezone/assets/images/promoShopIcon.svg" alt="магазины" lazyload>
                    </div>
                    <div class="otherLinks_block__title">
                        <span class="otherLinks_title">Розничные</span>
                        <span class="otherLinks_subtitle">Магазины</span>
                    </div>
                    <div class="otherLinks_block_arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.24654 16.4463C9.05128 16.6415 9.05128 16.9581 9.24654 17.1534C9.44181 17.3486 9.75839 17.3486 9.95365 17.1534L9.24654 16.4463ZM14.4001 11.9998L14.7537 12.3534L15.1072 11.9998L14.7537 11.6463L14.4001 11.9998ZM9.95365 6.84625C9.75839 6.65099 9.44181 6.65099 9.24654 6.84625C9.05128 7.04151 9.05128 7.3581 9.24654 7.55336L9.95365 6.84625ZM9.95365 17.1534L14.7537 12.3534L14.0465 11.6463L9.24654 16.4463L9.95365 17.1534ZM14.7537 11.6463L9.95365 6.84625L9.24654 7.55336L14.0465 12.3534L14.7537 11.6463Z" fill="white" />
                        </svg>
                    </div>
                </a>
                <a href="/opt" class="otherLinks_block promoBoxes">
                    <div class="otherLinks_block__gradient"></div>
                    <div class="otherLinks_block__icon">
                        <img src="/wp-content/themes/vapezone/assets/images/promoBoxIcon.svg" alt="Опт" lazyload>
                    </div>
                    <div class="otherLinks_block__title">
                        <span class="otherLinks_title">Оптовый</span>
                        <span class="otherLinks_subtitle">Раздел</span>
                    </div>
                    <div class="otherLinks_block_arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.24654 16.4463C9.05128 16.6415 9.05128 16.9581 9.24654 17.1534C9.44181 17.3486 9.75839 17.3486 9.95365 17.1534L9.24654 16.4463ZM14.4001 11.9998L14.7537 12.3534L15.1072 11.9998L14.7537 11.6463L14.4001 11.9998ZM9.95365 6.84625C9.75839 6.65099 9.44181 6.65099 9.24654 6.84625C9.05128 7.04151 9.05128 7.3581 9.24654 7.55336L9.95365 6.84625ZM9.95365 17.1534L14.7537 12.3534L14.0465 11.6463L9.24654 16.4463L9.95365 17.1534ZM14.7537 11.6463L9.95365 6.84625L9.24654 7.55336L14.0465 12.3534L14.7537 11.6463Z" fill="white" />
                        </svg>
                    </div>
                </a>
                <a href="/contact#socialLinks" class="otherLinks_block promoSocials">
                    <div class="otherLinks_block__gradient"></div>
                    <div class="otherLinks_block__icon">
                        <img src="/wp-content/themes/vapezone/assets/images/promoSocialIcon.svg" alt="Соц. сети" lazyload>
                    </div>
                    <div class="otherLinks_block__title">
                        <span class="otherLinks_title">Социальные</span>
                        <span class="otherLinks_subtitle">Сети</span>
                    </div>
                    <div class="otherLinks_block_arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.24654 16.4463C9.05128 16.6415 9.05128 16.9581 9.24654 17.1534C9.44181 17.3486 9.75839 17.3486 9.95365 17.1534L9.24654 16.4463ZM14.4001 11.9998L14.7537 12.3534L15.1072 11.9998L14.7537 11.6463L14.4001 11.9998ZM9.95365 6.84625C9.75839 6.65099 9.44181 6.65099 9.24654 6.84625C9.05128 7.04151 9.05128 7.3581 9.24654 7.55336L9.95365 6.84625ZM9.95365 17.1534L14.7537 12.3534L14.0465 11.6463L9.24654 16.4463L9.95365 17.1534ZM14.7537 11.6463L9.95365 6.84625L9.24654 7.55336L14.0465 12.3534L14.7537 11.6463Z" fill="white" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
<section class="catalog">
    <div class="container">
        <div class="section_title">
            <span class="section_title__label">Каталог</span>
        </div>
        <div class="catalog_block">
            <div class="catalog_block__items">
                <?php if (get_field('main_catalog', 'option')) : ?>
                    <?php while (has_sub_field('main_catalog', 'option')) : ?>
                        <? if (!get_sub_field('hidden')) { ?>
                            <a href="<?php the_sub_field('link'); ?>" class="catalog_items__item">
                                <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('name'); ?>" lazyload />
                                <span class="itemName">
                                    <?php the_sub_field('name'); ?>
                                </span>
                            </a>
                        <? } ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <div class="catalog_block__showMore">
                <a href="/catalog" class="btn primary xl">Перейти ко всем категориям</a>
            </div>
        </div>
        <div class="mobileCatalog_block">
            <div class="mobileCatalog_block__carousel owl-carousel owl-theme">
                <?php if (get_field('main_catalog', 'option')) : ?>
                    <?php while (has_sub_field('main_catalog', 'option')) : ?>
                        <? if (!get_sub_field('hidden')) { ?>
                            <a href="<?php the_sub_field('link'); ?>" class="carousel_item">
                                <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('name'); ?>" lazyload />
                                <span class="itemName">
                                    <?php the_sub_field('name'); ?>
                                </span>
                            </a>
                        <? } ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section class="newProducts">
    <div class="container">
        <div class="section_title">
            <span class="section_title__label">Новинки</span>
        </div>
        <div class="newProducts_block products_block">
            <div class="productsBlock_tabs">
                <ul>
                    <li class="tab_twoProducts">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white"></rect>
                            <rect x="13" y="3" width="8" height="18" stroke="#000000" stroke-linejoin="round">
                            </rect>
                            <rect x="3" y="3" width="8" height="18" stroke="#000000" stroke-linejoin="round"></rect>
                        </svg>
                        <span>Два товара</span>
                    </li>
                    <li class="tab_oneProducts active">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="3" width="12" height="18" stroke="#000000" stroke-linejoin="round">
                            </rect>
                        </svg>
                        <span>Один товар</span>
                    </li>
                </ul>
            </div>
            <div class="productsBlock_carousel oneProductsBlock_carousel newProducts_block__carousel owl-carousel owl-theme">
                <?php
                $numberposts = 8;
                $posts = wc_get_products(array(
                    'numberposts' => $numberposts,
                    'meta_key' => 'new',
                    'meta_value' => '1'
                ));
                if (count($posts) != $numberposts) {
                    $excluded_ids = [];
                    foreach ($posts as $post) {
                        $excluded_ids[] = $post->get_id();
                    }
                    $posts = array_merge(
                        $posts,
                        wc_get_products(array(
                            'numberposts' => $numberposts - count($posts),
                            'post__not_in' => $excluded_ids,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ))
                    );
                }
                foreach ($posts as $post) {
                    wc_setup_product_data($post);
                    include get_template_directory() . '/includes/components/product-minicard-slider.php';
                }
                wp_reset_postdata(); // сброс
                ?>
            </div>
            <div class="productsBlock_carousel twoProductsBlock_carousel newProducts_block__carousel onlyMobile owl-carousel owl-theme">
                <?php
                $numberposts = 8;
                $posts = wc_get_products(array(
                    'numberposts' => $numberposts,
                    'meta_key' => 'new',
                    'meta_value' => '1'
                ));
                if (count($posts) != $numberposts) {
                    $excluded_ids = [];
                    foreach ($posts as $post) {
                        $excluded_ids[] = $post->get_id();
                    }
                    $posts = array_merge(
                        $posts,
                        wc_get_products(array(
                            'numberposts' => $numberposts - count($posts),
                            'post__not_in' => $excluded_ids,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ))
                    );
                }
                foreach ($posts as $post) {
                    wc_setup_product_data($post);
                    include get_template_directory() . '/includes/components/product-minicard-slider.php';
                }
                wp_reset_postdata(); // сброс
                ?>
            </div>
        </div>
    </div>
</section>
<?php if (get_field('inner_slider', 'option')) : ?>
    <section class="noveltyBanners">
        <div class="container">
            <div class="noveltyBanners_block">
                <div class="noveltyBanners_block__carousel owl-carousel owl-theme">
                    <?php while (has_sub_field('inner_slider', 'option')) : ?>
                        <div class="carousel_item" style="background: url('<?php the_sub_field('image'); ?>'); background-size: cover; background-position: center;">
                            <div class="carousel_item__gradient"></div>
                            <div class="carousel_item__content">
                                <span class="carousel_title"><?php the_sub_field('title'); ?></span>
                                <span class="carousel_subtitle"><?php the_sub_field('subtitle'); ?></span>
                                <span class="carousel_item__address">
                                    <?php the_sub_field('desc'); ?>
                                </span>
                                <a href="<?php the_sub_field('link'); ?>" class="carousel_item__link btn l primary">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<section class="popularProducts">
    <div class="container">
        <div class="section_title">
            <span class="section_title__label">Популярные товары</span>
        </div>
        <div class="popularProducts_block products_block">
            <div class="productsBlock_tabs">
                <ul>
                    <li class="tab_twoProducts">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white"></rect>
                            <rect x="13" y="3" width="8" height="18" stroke="#000000" stroke-linejoin="round">
                            </rect>
                            <rect x="3" y="3" width="8" height="18" stroke="#000000" stroke-linejoin="round"></rect>
                        </svg>
                        <span>Два товара</span>
                    </li>
                    <li class="tab_oneProducts active">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="3" width="12" height="18" stroke="#000000" stroke-linejoin="round">
                            </rect>
                        </svg>
                        <span>Один товар</span>
                    </li>
                </ul>
            </div>
            <div class="productsBlock_carousel oneProductsBlock_carousel popularProducts_block__carousel owl-carousel owl-theme">
                <?php
                $posts = wc_get_products(array(
                    'numberposts' => 8,
                    'meta_key' => 'popular',
                    'meta_value' => '1'
                ));
                if (count($posts) != $numberposts) {
                    $excluded_ids = [];
                    foreach ($posts as $post) {
                        $excluded_ids[] = $post->get_id();
                    }
                    $posts = array_merge(
                        $posts,
                        wc_get_products(array(
                            'numberposts' => $numberposts - count($posts),
                            'meta_key' => 'post_views_count',
                            'orderby' => 'meta_value_num',
                            'order' => 'desc',
                        ))
                    );
                }
                foreach ($posts as $post) {
                    wc_setup_product_data($post);
                    include get_template_directory() . '/includes/components/product-minicard-slider.php';
                }
                wp_reset_postdata(); // сброс
                ?>
            </div>
            <div class="productsBlock_carousel twoProductsBlock_carousel popularProducts_block__carousel onlyMobile owl-carousel owl-theme">
                <?php
                $posts = wc_get_products(array(
                    'numberposts' => 8,
                    'meta_key' => 'popular',
                    'meta_value' => '1'
                ));
                if (count($posts) != $numberposts) {
                    $excluded_ids = [];
                    foreach ($posts as $post) {
                        $excluded_ids[] = $post->get_id();
                    }
                    $posts = array_merge(
                        $posts,
                        wc_get_products(array(
                            'numberposts' => $numberposts - count($posts),
                            'meta_key' => 'post_views_count',
                            'orderby' => 'meta_value_num',
                            'order' => 'desc',
                        ))
                    );
                }
                foreach ($posts as $post) {
                    wc_setup_product_data($post);
                    include get_template_directory() . '/includes/components/product-minicard-slider.php';
                }
                wp_reset_postdata(); // сброс
                ?>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>