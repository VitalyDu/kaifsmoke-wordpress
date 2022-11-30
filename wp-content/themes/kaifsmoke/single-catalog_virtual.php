<?php
/*
    Template Name: Виртуальная категория
*/
get_header();
?>
    <section class="allProducts">
        <div class="container">
            <div class="section_title">
                <h2><?php the_title(); ?></h2>
            </div>
            <div class="allProducts_view hidden-mobile">
                <ul>
                    <li class="allProducts_view__showTable active">
                        <a>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
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
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect width="24" height="24" fill="white"/>
                                <path d="M3 3H21V11H3V3Z" stroke="#1D1D1B" stroke-linecap="round"
                                      stroke-linejoin="round"/>
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
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect width="24" height="24" fill="white"/>
                                <rect x="13" y="3" width="8" height="18" stroke="#1D1D1B" stroke-linejoin="round"/>
                                <rect x="3" y="3" width="8" height="18" stroke="#1D1D1B" stroke-linejoin="round"/>
                            </svg>
                            Два товара
                        </a>
                    </li>
                    <li class="allProducts_view__showList">
                        <a>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect x="6" y="3" width="12" height="18" stroke="#1D1D1B" stroke-linejoin="round"/>
                            </svg>
                            Один товар
                        </a>
                    </li>
                </ul>
            </div>
            <div class="productsWrapper table " id="catalog_main_ajax">
                <?php VZCatalogVirtual::print(get_field('products')); ?>
            </div>
            <?php VZCatalogVirtual::printAfter(); ?>
        </div>
    </section>
<?php
get_footer();
?>