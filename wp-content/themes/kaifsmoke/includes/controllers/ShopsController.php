<?php
add_action('rest_api_init', ['ShopsController', 'registerRoutes']);

class ShopsController
{
    public static function registerRoutes()
    {
        $namespace = 'controllers/v1';
        $rest_base = 'shops';

        register_rest_route($namespace, '/' . $rest_base, array(
            'methods' => 'POST',
            'callback' => ['ShopsController', 'getShops'],
            'permission_callback' => ['ShopsController', 'returnTrue']
        ));
    }

    public static function getShops()
    {
        $posts = get_posts(array(
            'numberposts' => -1,
            'category_name' => 'shops',
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'post'
        ));
        $out = [
            "type" => "FeatureCollection",
            "features" => []
        ];
        foreach ($posts as $post) {
            $post_id = $post->ID;
            $meta = get_post_meta($post_id);

            $out['features'][] = [
                "type" => "Feature",
                "id" => intval($post->ID),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [
                        explode(', ', $meta['latlong'][0])[0],
                        explode(', ', $meta['latlong'][0])[1]
                    ]
                ],
                "properties" => [
                    "balloonContentHeader" => "<div class='vz-balloonContentHeader'>$post->post_title</div>",
                    "balloonContentBody" => "<div class='vz-balloonContentBody'>
    <div class='vz-balloonContentBody__phone'>" . $meta['phone'][0] . "</div>
    <div class='vz-balloonContentBody__schedule'>" . $meta['schedule'][0] . "</div>
    <a class='vz-balloonContentBody__link' href='" . get_post_permalink($post_id) . "'>Перейти к магазину</a>
    <span class='vz-balloonContentBody__book book reserveProductInShop' data-productid='".((empty($_POST['product_id'])) ? 0 : $_POST['product_id'])."' data-address='".$post->post_title."' data-multisklad='". $meta['id_multisklad'][0] ."'>
        <svg width='21' height='19' viewBox='0 0 21 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <path d='M15.9998 7H2M15.9998 7H16.9998V5H13.4998M15.9998 7L15.6226 10.0176M2 7L3 15H12.126M2 7H1V5H4.49976M5.5 9.5L5.99976 12.5M8.99976 12.5V9.5M12.5 9.5L11.9998 12.5M4.49976 5L6.49976 1M4.49976 5H13.4998M13.4998 5L11.4998 1M16 11.5V14H17.5M15.6226 10.0176C13.5904 10.2078 12 11.9181 12 14C12 14.3453 12.0438 14.6804 12.126 15M15.6226 10.0176C15.7468 10.0059 15.8727 10 16 10C18.2091 10 20 11.7909 20 14C20 16.2091 18.2091 18 16 18C14.1362 18 12.5701 16.7252 12.126 15' stroke='#F40500' stroke-linecap='round' stroke-linejoin='round'></path>
        </svg>
        <button>Забронировать</button>
    </span>
</div>",
                    "balloonContentFooter" => "",
                    "clusterCaption" => "",
                    "hintContent" => ""
                ]
            ];
        }

        return $out;
    }

    public static function returnTrue()
    {
        return true;
    }
}