<?php

add_action('wp_ajax_nopriv_print_catalog_main', ['VZCatalogMain', 'print']);
add_action('wp_ajax_print_catalog_main', ['VZCatalogMain', 'print']);

class VZCatalogMain
{
    public static function print()
    {
        $page = 1;
        if (!empty($_GET['pagination'])) {
            $page = intval($_GET['pagination']);
            if ($page < 1) {
                $page = 1;
            }
        }
        $posts_per_page = 16;
        $offset = ($page - 1) * $posts_per_page;

        echo '<div class="productsWrapper_block">';

        $posts = new WP_Query([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'offset' => $offset,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        if ($posts->have_posts()) {
            while ($posts->have_posts()) {
                $posts->the_post();
                include get_template_directory() . '/includes/components/product-minicard.php';
            }
        }

        echo '</div>';

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
            <svg  vg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
    function updateCatalogMainAjax(page = 1, tag_id = "") {
        $.ajax({
            url: AJAXURL,
            method: "GET",
            data: {
                action: "print_catalog_main",
                pagination: page
            },
            success: (data) => {
                $("#catalog_main_ajax").html(data);
                EasyGet.updateParam("pagination", page);
            },
            error: () => {
                console.log("Ошибка обновления");
            }
        });
    }
    //инит события на пагинаторе
    $("#catalog_main_ajax").on("click", ".vz-pagination div[data-page]", (e) => {
        const page = $(e.currentTarget).attr("data-page");
        updateCatalogMainAjax(page);
    });
</script>';

        return true;
    }
}
