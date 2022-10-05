<?php
/*
    Template Name: Раздел каталога
*/

get_header(); ?>
<section class="allProducts allProductsCategory">
    <div class="container">
        <div class="section_title">
            <h1><?php woocommerce_page_title(); ?></h1>
        </div>
        <div class="breadcrumbs">
            <ul>
                <?php woocommerce_breadcrumb(); ?>
            </ul>
        </div>
        <div class="mobileFilterWrapper">
            <div class="mobileFilter">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 6H8.4V18H6V6Z" stroke="#D6D6D6" stroke-linejoin="round" />
                    <path d="M10.8 10.8H13.2V18H10.8V10.8Z" stroke="#D6D6D6" stroke-linejoin="round" />
                    <path d="M15.6 8.4H18V18H15.6V8.4Z" stroke="#D6D6D6" stroke-linejoin="round" />
                </svg>
                <span class="label">
                    Фильтры
                </span>
            </div>
        </div>
        <div class="allProducts_quantity" style="display: none;">
            <?
            //                $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'product_cat' => get_queried_object()->slug);
            //                $products = new WP_Query($args);
            //                echo $products->found_posts;
            ?>
            товаров
        </div>
        <div class="allProducts_view hidden-mobile">
            <ul>
                <li class="allProducts_view__showTable active">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white" />
                            <rect x="3" y="3" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round" />
                            <rect x="3" y="13" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round" />
                            <rect x="13" y="3" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round" />
                            <rect x="13" y="13" width="8" height="8" stroke="#1D1D1B" stroke-linejoin="round" />
                        </svg>
                        Таблица
                    </a>
                </li>
                <li class="allProducts_view__showList">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white" />
                            <path d="M3 3H21V11H3V3Z" stroke="#1D1D1B" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 13H21V21H3V13Z" stroke="#1D1D1B" stroke-linecap="round" stroke-linejoin="round" />
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
                            <rect width="24" height="24" fill="white" />
                            <rect x="13" y="3" width="8" height="18" stroke="#1D1D1B" stroke-linejoin="round" />
                            <rect x="3" y="3" width="8" height="18" stroke="#1D1D1B" stroke-linejoin="round" />
                        </svg>
                        Два товара
                    </a>
                </li>
                <li class="allProducts_view__showList">
                    <a>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="3" width="12" height="18" stroke="#1D1D1B" stroke-linejoin="round" />
                        </svg>
                        Один товар
                    </a>
                </li>
            </ul>
        </div>

        <?php
        $get_filters = [];
        if (!empty($_GET['filters'])) {
            $filters_temp = explode(';;', urldecode($_GET['filters']));
            foreach ($filters_temp as $filter) {
                $filter = explode('=', $filter);
                $get_filters[$filter[0]] = explode(';', $filter[1]);
            }
        }

        $filters = [];
        $max_price = 0;

        $product_ids = $wpdb->get_results("SELECT 
                    r.object_id, pm.min_price
                FROM
                    wp_wc_category_lookup as c
                INNER JOIN
                    wp_term_relationships as r ON c.category_id = r.term_taxonomy_id
                INNER JOIN
                    wp_wc_product_meta_lookup as pm ON r.object_id = pm.product_id
                WHERE c.category_tree_id = " . get_queried_object()->term_id . " LIMIT 1000");

        $posts = [];
        $attr_filters = [];
        foreach ($product_ids as $post) {
            $product_id = $post->object_id;
            if (intval($post->min_price) > $max_price) {
                $max_price = intval($post->min_price);
            }

            $attrs = $wpdb->get_results("SELECT tt.taxonomy
                FROM
                     wp_posts AS p
                INNER JOIN
                     wp_term_relationships AS tr ON p.id = tr.object_id
                INNER JOIN
                     wp_term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                INNER JOIN
                     wp_terms AS t ON t.term_id = tt.term_id
                WHERE
                     p.ID =  {$product_id} AND p.post_type = 'product'");
            $attr_keys = [];
            foreach ($attrs as $key => $value) {
                if (!in_array($value->taxonomy, ['product_type', 'product_visibility', 'product_cat', 'pa_shtrihkod']))
                    $attr_keys[] = $value->taxonomy;
            }
            $terms = wp_get_object_terms($product_id, $attr_keys);


            foreach ($terms as $term) {
                $term = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'taxonomy' => $term->taxonomy
                ];

                if (empty($attr_filters[$term['taxonomy']])) {
                    $attr_filters[$term['taxonomy']] = [];
                }
                if (!in_array($term['name'], $attr_filters[$term['taxonomy']])) {
                    $attr_filters[$term['taxonomy']][$term['id']] = $term['name'];
                }
            }
        }

        $allowed_attr_filters = explode(';', get_field('allowed_attr_filters', 'option'));
        $attr_filters_temp = $attr_filters;
        $attr_filters = [];
        foreach ($attr_filters_temp as $key => $value) {
            if (in_array(substr($key, 3), $allowed_attr_filters) && count($value) > 1) {
                $attr_filters[$key] = $value;
            }
        }
        ?>
        <div class="productsFilterWrapper">
            <div class="filterWrapper">
                <div class="filter">
                    <div class="filter_status_container">
                        <span class="filter_close">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#1D1D1B" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <div class="filter_status">
                            <span class="filter_status__icon">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1H3.4V13H1V1Z" stroke="#1D1D1B" stroke-linejoin="round" />
                                    <path d="M5.8 5.8H8.2V13H5.8V5.8Z" stroke="#1D1D1B" stroke-linejoin="round" />
                                    <path d="M10.6 3.4H13V13H10.6V3.4Z" stroke="#1D1D1B" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span class="filter_status__empty">Фильтры не выбраны</span>
                            <span class="filter_status__chosen"><span class="chosen_val">0</span> выбрано</span>
                            <span class="filter_status__action">Очистить</span>
                        </div>

                    </div>

                    <div class="filter_content">
                        <div class="filter_content__item filter_item__radio">
                            <div class="item_label">
                                <span class="label">Сортировка</span>
                                <span class="icon">
                                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.55375 1.24801C1.35849 1.05275 1.0419 1.05275 0.846642 1.24801C0.65138 1.44327 0.65138 1.75985 0.846642 1.95512L1.55375 1.24801ZM6.0002 6.40156L5.64664 6.75512L6.0002 7.10867L6.35375 6.75512L6.0002 6.40156ZM11.1537 1.95512C11.349 1.75985 11.349 1.44327 11.1537 1.24801C10.9585 1.05275 10.6419 1.05275 10.4466 1.24801L11.1537 1.95512ZM0.846642 1.95512L5.64664 6.75512L6.35375 6.04801L1.55375 1.24801L0.846642 1.95512ZM6.35375 6.75512L11.1537 1.95512L10.4466 1.24801L5.64664 6.04801L6.35375 6.75512Z" fill="#1D1D1B" />
                                    </svg>
                                </span>
                            </div>
                            <?php
                            if (empty($get_filters['sorting']))
                                $get_filters['sorting'][0] = 'default';
                            ?>
                            <ul class="item_dropdown">
                                <li class="item_dropdown__item">
                                    <input type="radio" name="sorting" value="populardesc" id="sort1" <?= ($get_filters['sorting'][0] == 'populardesc' || $get_filters['sorting'][0] == 'default') ? 'checked' : '' ?>>
                                    <label for="sort1">По популярности</label>
                                </li>
                                <li class="item_dropdown__item">
                                    <input type="radio" name="sorting" value="priceasc" id="sort2" <?= ($get_filters['sorting'][0] == 'priceasc') ? 'checked' : '' ?>>
                                    <label for="sort2">Сначала дешёвые</label>
                                </li>
                                <li class="item_dropdown__item">
                                    <input type="radio" name="sorting" value="pricedesc" id="sort3" <?= ($get_filters['sorting'][0] == 'pricedesc') ? 'checked' : '' ?>>
                                    <label for="sort3">Сначала дорогие</label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter_content__item filter_item__price">
                            <div class="item_label">
                                <span class="label">Цена</span>
                                <span class="icon">
                                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.55375 1.24801C1.35849 1.05275 1.0419 1.05275 0.846642 1.24801C0.65138 1.44327 0.65138 1.75985 0.846642 1.95512L1.55375 1.24801ZM6.0002 6.40156L5.64664 6.75512L6.0002 7.10867L6.35375 6.75512L6.0002 6.40156ZM11.1537 1.95512C11.349 1.75985 11.349 1.44327 11.1537 1.24801C10.9585 1.05275 10.6419 1.05275 10.4466 1.24801L11.1537 1.95512ZM0.846642 1.95512L5.64664 6.75512L6.35375 6.04801L1.55375 1.24801L0.846642 1.95512ZM6.35375 6.75512L11.1537 1.95512L10.4466 1.24801L5.64664 6.04801L6.35375 6.75512Z" fill="#1D1D1B" />
                                    </svg>
                                </span>
                            </div>
                            <div class="item_dropdown">
                                <div class="item_dropdown__range">
                                    <div class="range_top">
                                        <span class="range_top__min">0 руб</span>
                                        <span class="range_top__max"><?= $max_price ?> руб</span>
                                    </div>
                                    <div class="range_price" data-min="0" data-max="<?= $max_price ?>" data-minval="<?= (!empty($get_filters['pricefrom'][0])) ? $get_filters['pricefrom'][0] : '0' ?>" data-maxval="<?= (!empty($get_filters['priceto'][0])) ? $get_filters['priceto'][0] : $max_price ?>"></div>
                                    <div class="range_inputs">
                                        <div class="range_inputs__inputField">
                                            <label>От</label>
                                            <input type="number" name="pricefrom" class="range_inputs__min" value="<?= (!empty($get_filters['pricefrom'][0])) ? $get_filters['pricefrom'][0] : '0' ?>" max="<?= $max_price ?>" />
                                        </div>
                                        <div class="range_inputs__inputField">
                                            <label>До</label>
                                            <input type="number" name="priceto" class="range_inputs__max" value="<?= (!empty($get_filters['priceto'][0])) ? $get_filters['priceto'][0] : $max_price ?>" max="<?= $max_price ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        //multi_sklad
                        $multi_sklad_filter = [];
                        $posts = get_posts(array(
                            'numberposts' => -1,
                            'category_name' => 'shops',
                            'post_type' => 'post',
                        ));
                        foreach ($posts as $post) {
                            setup_postdata($post);

                            if (get_post_meta(get_the_id(), 'hidden', true)) {
                                continue;
                            }
                            $multi_sklad_filter[get_the_title()] = get_post_meta(get_the_id(), 'id_multisklad', true);
                        }
                        wp_reset_postdata();
                        ?>
                        <?php
                        foreach ($attr_filters as $filter_name => $filter_values) {
                        ?>
                            <div class="filter_content__item filter_item__checkbox">
                                <div class="item_label">
                                    <span class="label"><?= wc_attribute_label($filter_name) ?></span>
                                    <span class="icon">
                                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.55375 1.24801C1.35849 1.05275 1.0419 1.05275 0.846642 1.24801C0.65138 1.44327 0.65138 1.75985 0.846642 1.95512L1.55375 1.24801ZM6.0002 6.40156L5.64664 6.75512L6.0002 7.10867L6.35375 6.75512L6.0002 6.40156ZM11.1537 1.95512C11.349 1.75985 11.349 1.44327 11.1537 1.24801C10.9585 1.05275 10.6419 1.05275 10.4466 1.24801L11.1537 1.95512ZM0.846642 1.95512L5.64664 6.75512L6.35375 6.04801L1.55375 1.24801L0.846642 1.95512ZM6.35375 6.75512L11.1537 1.95512L10.4466 1.24801L5.64664 6.04801L6.35375 6.75512Z" fill="#1D1D1B" />
                                        </svg>
                                    </span>
                                </div>
                                <ul class="item_dropdown">
                                    <?php $k = 0;
                                    foreach ($filter_values as $filter_key => $filter_value) {
                                    ?>

                                        <li class="item_dropdown__item">
                                            <input type="checkbox" id="<?= $filter_name ?>_<?= ++$k ?>" name="<?= $filter_name ?>" value="<?= $filter_key ?>" <?= (!empty($get_filters[$filter_name]) && in_array($filter_key, $get_filters[$filter_name])) ? 'checked' : '' ?>>
                                            <label for="<?= $filter_name ?>_<?= $k ?>"><?= $filter_value ?></label>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="filter_content__item filter_item__checkbox">
                            <div class="item_label">
                                <span class="label">Наличие на складах</span>
                                <span class="icon">
                                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.55375 1.24801C1.35849 1.05275 1.0419 1.05275 0.846642 1.24801C0.65138 1.44327 0.65138 1.75985 0.846642 1.95512L1.55375 1.24801ZM6.0002 6.40156L5.64664 6.75512L6.0002 7.10867L6.35375 6.75512L6.0002 6.40156ZM11.1537 1.95512C11.349 1.75985 11.349 1.44327 11.1537 1.24801C10.9585 1.05275 10.6419 1.05275 10.4466 1.24801L11.1537 1.95512ZM0.846642 1.95512L5.64664 6.75512L6.35375 6.04801L1.55375 1.24801L0.846642 1.95512ZM6.35375 6.75512L11.1537 1.95512L10.4466 1.24801L5.64664 6.04801L6.35375 6.75512Z" fill="#1D1D1B" />
                                    </svg>
                                </span>
                            </div>
                            <ul class="item_dropdown">
                                <?php $k = 0;
                                foreach ($multi_sklad_filter as $sklad_name => $sklad_id) {
                                ?>
                                    <li class="item_dropdown__item">
                                        <input type="checkbox" id="multisklad_<?= ++$k ?>" name="multisklad" <?= (!empty($get_filters['multisklad']) && in_array($sklad_id, $get_filters['multisklad'])) ? 'checked' : '' ?> value="<?= $sklad_id ?>">
                                        <label for="multisklad_<?= $k ?>"><?= $sklad_name ?></label>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <script>
                let filterTimeout;
                $('.filter').on('change', 'input', () => {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => {
                        update_catalog();
                    }, 500);
                });

                function update_catalog() {
                    let filters = [];

                    $('.filter input:checked').each((i, e) => {
                        const $e = $(e);
                        if (!filters[$e.attr('name')]) {
                            filters[$e.attr('name')] = [$e.attr('value')];
                        } else {
                            filters[$e.attr('name')].push($e.attr('value'));
                        }
                    });

                    const pricefrom = $('.filter input[name=pricefrom]').val();
                    if (pricefrom !== '0' && pricefrom !== '') {
                        filters['pricefrom'] = [pricefrom];
                    }

                    const priceto = $('.filter input[name=priceto]').val();
                    if (priceto !== '100000' && priceto !== '') {
                        filters['priceto'] = [priceto];
                    }

                    filters_str = [];
                    for (var filter in filters) {
                        filters_str.push(filter + "=" + filters[filter].join(';'));
                    }
                    filters_str = encodeURIComponent(filters_str.join(';;'));

                    EasyGet.updateParam('filters', filters_str);

                    $.ajax({
                        url: AJAXURL,
                        method: "GET",
                        data: {
                            action: "print_catalog_inner",
                            pagination: ((EasyGet.getParam('pagination') === null) ? 1 : EasyGet.getParam('pagination')),
                            filters: ((EasyGet.getParam("filters") === null) ? "" : EasyGet.getParam("filters")),
                            slug: "<?= get_queried_object()->slug ?>"
                        },
                        success: (data) => {
                            $("#catalog_inner_ajax").html(data);
                        },
                        error: () => {
                            console.log("Ошибка обновления");
                        }
                    });
                }
            </script>
            <div class="productsWrapper table" id="catalog_inner_ajax">
                <?php VZCatalogInner::print(get_queried_object()->slug); ?>
            </div>
        </div>
        <?php VZCatalogInner::printAfter(); ?>
    </div>
</section>
<?php
get_footer();
?>