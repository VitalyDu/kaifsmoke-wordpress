<?php

add_action('wp_ajax_nopriv_print_catalog_inner', ['VZCatalogInner', 'print']);
add_action('wp_ajax_print_catalog_inner', ['VZCatalogInner', 'print']);

class VZCatalogInner
{
    public static function print($slug = '')
    {
        $page = 1;
        if (!empty($_GET['pagination'])) {
            $page = intval($_GET['pagination']);
            if ($page < 1) {
                $page = 1;
            }
        }
        if (!empty($_GET['filters'])) {
            $filters = [];
            $filters_temp = explode(';;', urldecode($_GET['filters']));
            foreach ($filters_temp as $filter) {
                $filter = explode('=', $filter);
                $filters[$filter[0]] = explode(';', $filter[1]);
            }
        }
        if (!empty($_GET['slug'])) {
            $slug = $_GET['slug'];
        }
        $posts_per_page = 18;
        $offset = ($page - 1) * $posts_per_page;


        $filter_query = [
            'meta_query' => [
                'relation' => 'AND',
                'in_stock' => [
                    'key' => 'in_stock',
                ],
            ],
            'orderby' => [
                'in_stock' => 'DESC',
            ]
        ];
        //добавлено стандартное значение сортировки по популярности
        $filter_query['meta_query']['post_views_count'] = [
            'key' => 'post_views_count',
            'type' => 'NUMERIC'
        ];
        $filter_query['orderby']['post_views_count'] = 'DESC';

        $tax_query = [
            'tax_query' => []
        ];
        if (!empty($filters)) {
            foreach ($filters as $filter_key => $filter_value) {
                switch ($filter_key) {
                    case 'sorting':
                        switch ($filter_value[0]) {
                            case 'populardesc':
                                $filter_query['meta_query']['post_views_count'] = [
                                    'key' => 'post_views_count',
                                    'type' => 'NUMERIC'
                                ];
                                $filter_query['orderby']['post_views_count'] = 'DESC';
                                break;
                            case 'priceasc':
                                $filter_query['meta_query']['_price'] = [
                                    'key' => '_price',
                                    'type' => 'NUMERIC'
                                ];
                                $filter_query['orderby']['_price'] = 'ASC';
                                unset($filter_query['meta_query']['post_views_count']);
                                unset($filter_query['orderby']['post_views_count']);
                                break;
                            case 'pricedesc':
                                $filter_query['meta_query']['_price'] = [
                                    'key' => '_price',
                                    'type' => 'NUMERIC'
                                ];
                                $filter_query['orderby']['_price'] = 'DESC';
                                unset($filter_query['meta_query']['post_views_count']);
                                unset($filter_query['orderby']['post_views_count']);
                                break;
                        }
                        break;
                    case 'pricefrom':
                        $filter_query['meta_query'][] = [
                            'key' => '_price',
                            'type' => 'NUMERIC',
                            'compare' => '>=',
                            'value' => $filter_value[0]
                        ];
                        break;
                    case 'priceto':
                        $filter_query['meta_query'][] = [
                            'key' => '_price',
                            'type' => 'NUMERIC',
                            'compare' => '<=',
                            'value' => $filter_value[0]
                        ];
                        break;
                    case 'multisklad':
                        foreach ($filter_value as $item) {
                            $filter_query['meta_query'][] = [
                                'key' => 'multi_sklad',
                                'compare' => 'LIKE',
                                'value' => $item . '&0;'
                            ];
                            $filter_query['meta_query'][] = [
                                'key' => 'multi_sklad',
                                'compare' => 'NOT LIKE',
                                'value' => $item . '&0;0'
                            ];
                        }
                        break;
                    default:
                        $tax_query['tax_query'][] = [
                            'taxonomy' => $filter_key,
                            'field' => 'term_id',
                            'terms' => $filter_value,
                            'operator' => 'IN'
                        ];
                        break;
                }
            }
        }

        $query = array_merge([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'offset' => $offset,
            'product_cat' => $slug
        ], $filter_query, $tax_query);

        $posts = new WP_Query($query);
        if ($posts->have_posts()) {
            echo '<div class="productsWrapper_block">';
            while ($posts->have_posts()) {
                $posts->the_post();
                include get_template_directory() . '/includes/components/product-minicard.php';
            }
            echo '</div>';
        } else { ?>
            <div class="productsEmpty">
                <span class="productsEmpty_label">Товары не найдены!</span>
            </div>
        <?php }

        $max_pages = ceil($posts->found_posts / $posts_per_page);
        self::printPagination($page, $max_pages);

        if (wp_doing_ajax()) {
            die();
        }
        return true;
    }

    public static function printPagination($page, $max_pages)
    {
        $pagination = [];
        $page = intval($page);
        $pagination[1] = '<div data-page="1">1</div>';
        for ($i = $page - 2; $i <= $page + 2; $i++) {
            if ($i > 1 && $i < $max_pages) {
                if ($i == $page - 2 || $i == $page + 2) {
                    $pagination[$i] = '<div>...</div>';
                } else {
                    $pagination[$i] = '<div data-page="' . $i . '">' . $i . '</div>';
                }
            }
        }
        if ($max_pages > 1) {
            $pagination[$max_pages] = '<div data-page="' . $max_pages . '">' . $max_pages . '</div>';
        }
        if ($page <= $max_pages) {
            $pagination[$page] = '<div class="current">' . $page . '</div>';
        }
        echo '<div class="vz-pagination">';
        echo '<div ' . (($page - 1 > 0) ? 'data-page="' . ($page - 1) . '"' : 'class="disabled"') . '>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M14.7535 7.55375C14.9487 7.35849 14.9487 7.0419 14.7535 6.84664C14.5582 6.65138 14.2416 6.65138 14.0463 6.84664L14.7535 7.55375ZM9.5999 12.0002L9.24635 11.6466L8.8928 12.0002L9.24635 12.3537L9.5999 12.0002ZM14.0463 17.1537C14.2416 17.349 14.5582 17.349 14.7535 17.1537C14.9487 16.9585 14.9487 16.6419 14.7535 16.4466L14.0463 17.1537ZM14.0463 6.84664L9.24635 11.6466L9.95346 12.3537L14.7535 7.55375L14.0463 6.84664ZM9.24635 12.3537L14.0463 17.1537L14.7535 16.4466L9.95346 11.6466L9.24635 12.3537Z"
                    fill="#1D1D1B" />
            </svg>
        </div>';
        foreach ($pagination as $p) {
            echo $p;
        }
        echo '<div ' . (($page + 1 <= $max_pages) ? 'data-page="' . ($page + 1) . '"' : 'class="disabled"') . '>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M9.24654 7.55375C9.05128 7.35849 9.05128 7.0419 9.24654 6.84664C9.44181 6.65138 9.75839 6.65138 9.95365 6.84664L9.24654 7.55375ZM14.4001 12.0002L14.7537 11.6466L15.1072 12.0002L14.7537 12.3537L14.4001 12.0002ZM9.95365 17.1537C9.75839 17.349 9.44181 17.349 9.24654 17.1537C9.05128 16.9585 9.05128 16.6419 9.24654 16.4466L9.95365 17.1537ZM9.95365 6.84664L14.7537 11.6466L14.0465 12.3537L9.24654 7.55375L9.95365 6.84664ZM14.7537 12.3537L9.95365 17.1537L9.24654 16.4466L14.0465 11.6466L14.7537 12.3537Z"
                    fill="#1D1D1B" />
            </svg>
         </div>';
        echo '</div>';

        return true;
    }

    public static function printAfter()
    {
        echo '<script>
    function updateCatalogInnerAjax(page = 1) {
        $.ajax({
            url: AJAXURL,
            method: "GET",
            data: {
                action: "print_catalog_inner",
                pagination: page,
                filters: ((EasyGet.getParam("filters") === null) ? "" : EasyGet.getParam("filters")),
                slug: "' . get_queried_object()->slug . '"
            },
            success: (data) => {
                $("#catalog_inner_ajax").html(data);
                EasyGet.updateParam("pagination", page);
            },
            error: () => {
                console.log("Ошибка обновления");
            }
        });
    }
    //инит события на пагинаторе
    $("#catalog_inner_ajax").on("click", ".vz-pagination div[data-page]", (e) => {
        const page = $(e.currentTarget).attr("data-page");
        updateCatalogInnerAjax(page);
    });
</script>';

        return true;
    }
}
