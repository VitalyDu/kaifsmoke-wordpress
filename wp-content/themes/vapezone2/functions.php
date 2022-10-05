<?php

add_action('wp_enqueue_scripts', 'vapezone_styles');
add_action('wp_enqueue_scripts', 'vapezone_jquery_script');
add_action('wp_enqueue_scripts', 'vapezone_scripts');
function vapezone_styles()
{
    wp_enqueue_style('vapezone_css', get_template_directory_uri() . '/assets/css/main.min.css');
    wp_enqueue_style('jquery_ui', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
    wp_enqueue_style('vapezone_style', get_stylesheet_uri());
}

function vapezone_jquery_script()
{
    wp_deregister_script('jquery');
    wp_register_script('jquery', get_template_directory_uri() . '/assets/js/app.min.js');
    // wp_register_script('jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js');
    wp_enqueue_script('jquery');
}

function vapezone_scripts()
{
    // wp_enqueue_script('vapezone_js', get_template_directory_uri() . '/assets/js/app.min.js', array(), null, false);
    wp_enqueue_script('noty', get_template_directory_uri() . '/assets/js/noty.js', array(), null, true);
    wp_enqueue_script('jquery_cookie', '//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', array(), null, true);
    wp_enqueue_script('phone_mask', get_template_directory_uri() . '/assets/js/maskPhone.js', array(), null, true);
    wp_enqueue_script('phoneVerification', get_template_directory_uri() . '/assets/js/phoneVerification.js', array(), null, true);
    wp_enqueue_script('signup', get_template_directory_uri() . '/assets/js/signup.js', array(), null, true);
    wp_enqueue_script('signin', get_template_directory_uri() . '/assets/js/signin.js', array(), null, true);
    wp_enqueue_script('favorites', get_template_directory_uri() . '/assets/js/favorites.js', array(), null, true);
    wp_enqueue_script('addToCart', get_template_directory_uri() . '/assets/js/addToCart.js', array(), null, true);
    wp_enqueue_script('cart', get_template_directory_uri() . '/assets/js/cart.js', array(), null, true);
    wp_enqueue_script('modalFavorites', get_template_directory_uri() . '/assets/js/modalFavorites.js', array(), null, true);
    wp_enqueue_script('favoritesPage', get_template_directory_uri() . '/assets/js/favoritesPage.js', array(), null, true);
    wp_enqueue_script('reserveSingleProduct', get_template_directory_uri() . '/assets/js/reserveSingleProduct.js', array(), null, true);
    wp_enqueue_script('editAccount', get_template_directory_uri() . '/assets/js/editAccount.js', array(), null, true);
    wp_enqueue_script('modalSignin', get_template_directory_uri() . '/assets/js/modalSignin.js', array(), null, true);
    wp_enqueue_script('passwordRecovery', get_template_directory_uri() . '/assets/js/passwordRecovery.js', array(), null, true);
    wp_enqueue_script('resetPassword', get_template_directory_uri() . '/assets/js/resetPassword.js', array(), null, true);
    wp_enqueue_script('EasyGet', get_template_directory_uri() . '/assets/js/EasyGet.js', array(), null, true);
    wp_enqueue_script('sendForm', get_template_directory_uri() . '/assets/js/sendForm.js', array(), null, true);
    wp_enqueue_script('sendReview', get_template_directory_uri() . '/assets/js/sendReview.js', array(), null, true);
    wp_enqueue_script('YandexMaps', 'https://api-maps.yandex.ru/2.1/?apikey=afafefc5-9f36-4035-9de4-b66e039abb00&lang=ru_RU', array(), null, true);
    wp_enqueue_script('shopsMap', get_template_directory_uri() . '/assets/js/shopsMap.js', array(), null, true);
}

add_action('wp_head', 'vapezone_ajaxurl');
function vapezone_ajaxurl()
{
    ?>
    <script>
        const AJAXURL = '<?= admin_url('admin-ajax.php') ?>';
        const USER_ID = '<?= get_current_user_id() ?>';
    </script>
    <?php
}

//для локальной разработки
if (file_exists('devfix.php'))
    include_once 'devfix.php';


acf_add_options_page('Доп. настройки');

foreach (glob(get_template_directory() . '/includes/classes/*.php') as $filename)
    include_once $filename;

foreach (glob(get_template_directory() . '/includes/controllers/*.php') as $filename)
    include_once $filename;

# Delete main page prefix for subpages.
# Works only if static page is set for front page.
//Delete_Base_Prefix_For_Front_Sub_Pages::init();
//
//final class Delete_Base_Prefix_For_Front_Sub_Pages
//{
//
//    // WP_Post|false if current request is front sub page
//    static $sub_post = false;
//
//    // WP_Post|false if specific page is set for front page
//    static $front_post = 0;
//
//    static $origin_REQUEST_URI = '';
//
//    static function init()
//    {
//
//        self::$front_post = ('page' === get_option('show_on_front') && $ID = get_option('page_on_front')) ? get_post($ID) : 0;
//
//        if (self::$front_post) {
//
//            // change permalink
//            add_filter('page_link', [__CLASS__, 'fix_page_link']);
//
//            // change request
//            add_filter('do_parse_request', [__CLASS__, 'replace_uri']);
//            add_filter('request', [__CLASS__, 'replace_uri_back']);
//
//            // remove `front-page.php` template file from template hierarchy
//            // if static page set for front page. Use, basic `page-*.php` template file in this case.
//            add_filter('frontpage' . '_template_hierarchy', '__return_empty_array');
//
//            // 404 for URL with main page prefix
//            add_action('pre_handle_404', [__CLASS__, 'block_wrong_url']);
//
//            // for plugin Kama Breadcrumbs
//            add_filter('kama_breadcrumbs_filter_elements', [__CLASS__, 'fix_breadcrumbs']);
//        }
//    }
//
//    static function fix_page_link($link)
//    {
//
//        // url begin with prefix
//        if (
//            self::$front_post && strpos($link, self::$front_post->post_name) &&
//            preg_match('~^/' . self::$front_post->post_name . '/([^?]+)~', parse_url($link, PHP_URL_PATH), $mm)
//        )
//            return str_replace($mm[0], "/$mm[1]", $link);
//
//        return $link;
//    }
//
//    static function replace_uri($true)
//    {
//
//        list($req_uri) = explode('?', $_SERVER['REQUEST_URI']);
//
//        self::$origin_REQUEST_URI = $_SERVER['REQUEST_URI'];
//
//        $page_real_path = self::$front_post->post_name . '/' . trim($req_uri, '/');
//        if (self::$sub_post = get_page_by_path($page_real_path))
//            $_SERVER['REQUEST_URI'] = $page_real_path;
//
//        return $true;
//    }
//
//    static function replace_uri_back($foo)
//    {
//
//        if (self::$origin_REQUEST_URI)
//            $_SERVER['REQUEST_URI'] = self::$origin_REQUEST_URI;
//
//        return $foo; // for filter
//    }
//
//    static function block_wrong_url($false)
//    {
//
//        if (preg_match('~^/' . self::$front_post->post_name . '(?:/([^?]+)|/?$)~', self::$origin_REQUEST_URI)) {
//            global $wp_query;
//            $wp_query->set_404();
//            status_header(404);
//            nocache_headers();
//
//            return true; // for hook break
//        }
//
//        return $false;
//    }
//
//    static function fix_breadcrumbs($elms)
//    {
//
//        if (self::$sub_post && !empty($elms['singular_hierar']['page__page_crumbs'])) {
//            $elms['home'] = array_shift($elms['singular_hierar']['page__page_crumbs']);
//        }
//
//        return $elms;
//    }
//}

function true_breadcrumbs()
{
    // получаем номер текущей страницы
    $page_num = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $separator = ''; //  разделяем обычным слэшем, но можете чем угодно другим

    // если главная страница сайта
    if (is_front_page()) {

        if ($page_num > 1) {
            echo '<li><a href="' . site_url() . '">Главная</a></li>' . $separator . $page_num . '-я страница';
        } else {
            echo 'Вы находитесь на главной странице';
        }
    } else { // не главная

        echo '<li><a href="' . site_url() . '">Главная</a></li>' . $separator;


        if (is_single()) { // записи

            the_category(', ');
            echo $separator;
            the_title();
        } elseif (is_page()) { // страницы WordPress
            echo '<li><a href="#">' . get_the_title() . '</a></li>' . $separator;
        } elseif (is_category()) {

            single_cat_title();
        } elseif (is_tag()) {

            single_tag_title();
        } elseif (is_day()) { // архивы (по дням)

            echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>' . $separator;
            echo '<li><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a></li>' . $separator;
            echo get_the_time('d');
        } elseif (is_month()) { // архивы (по месяцам)

            echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>' . $separator;
            echo get_the_time('F');
        } elseif (is_year()) { // архивы (по годам)

            echo get_the_time('Y');
        } elseif (is_author()) { // архивы по авторам

            global $author;
            $userdata = get_userdata($author);
            echo 'Опубликовал(а) ' . $userdata->display_name;
        } elseif (is_404()) { // если страницы не существует

            echo 'Ошибка 404';
        }

        if ($page_num > 1) { // номер текущей страницы
            echo ' (' . $page_num . '-я страница)';
        }
    }
}

add_filter(
    'single_template',
    function (
        $the_template
    ) {
        foreach ((array)get_the_category() as $cat) {
            if (file_exists(TEMPLATEPATH . "/single-{$cat->slug}.php"))
                return TEMPLATEPATH . "/single-{$cat->slug}.php";
        }
        return $the_template;
    }
);

/**
 * Load WooCommerce compatibility file.
 */
if (class_exists('WooCommerce')) {
    require get_template_directory() . '/includes/woocommerce.php';
}

add_filter('bulk_actions-edit-shop_order', function ($bulk_actions) {
    $bulk_actions['mark_processing'] = 'Изменить статус на "Открыт"';
    $bulk_actions['mark_cancelled'] = 'Изменить статус на "Отменена"';
    $bulk_actions['mark_completed'] = 'Изменить статус на "Завершено"';
    $bulk_actions['mark_on-hold'] = 'Изменить статус на "Доставка в магазин"';
    $bulk_actions['mark_pending'] = 'Изменить статус на "Ожидает покупателя"';
    return $bulk_actions;
}, 20);

add_filter('wc_order_statuses', function ($order_statuses) {
    $order_statuses['wc-cancelled'] = 'Отменена';
    $order_statuses['wc-completed'] = 'Завершено';
    $order_statuses['wc-on-hold'] = 'Доставка в магазин';
    $order_statuses['wc-pending'] = 'Ожидает покупателя';
    $order_statuses['wc-processing'] = 'Открыт';
    unset($order_statuses['wc-refunded']);
    unset($order_statuses['wc-failed']);
    return $order_statuses;
});
