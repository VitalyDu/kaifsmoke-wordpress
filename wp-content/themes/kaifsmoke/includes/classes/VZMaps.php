<?php

add_action('wp_ajax_get_map_for_cart', ['VZMaps', 'getForCartAjax']);
add_action('wp_ajax_nopriv_get_map_for_cart', ['VZMaps', 'getForCartAjax']);

add_action('wp_ajax_get_map_for_product_card', ['VZMaps', 'getForProductCardAjax']);
add_action('wp_ajax_nopriv_get_map_for_product_card', ['VZMaps', 'getForProductCardAjax']);

add_action('wp_ajax_get_map_for_shops', ['VZMaps', 'getForShopsAjax']);
add_action('wp_ajax_nopriv_get_map_for_shops', ['VZMaps', 'getForShopsAjax']);

add_action('wp_ajax_get_map_for_order', ['VZMaps', 'getForOrderAjax']);
add_action('wp_ajax_nopriv_get_map_for_order', ['VZMaps', 'getForOrderAjax']);

class VZMaps
{
    public static function getForCartAjax()
    {
        die(json_encode(self::getForCart()));
    }

    public static function getForCart()
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

            $latlong = explode(', ', $meta['latlong'][0]);
            if (count($latlong) < 2)
                continue;

            $out['features'][] = [
                "type" => "Feature",
                "id" => intval($post->ID),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [
                        $latlong[0],
                        $latlong[1]
                    ]
                ],
                "properties" => [
                    "balloonContentHeader" => "<div class='vz-balloonContentHeader'>$post->post_title</div>",
                    "balloonContentBody" => "<div class='vz-balloonContentBody'>
    <div class='vz-balloonContentBody__phone'>" . $meta['phone'][0] . "</div>
    <div class='vz-balloonContentBody__schedule'>" . $meta['schedule'][0] . "</div>
    <a class='vz-balloonContentBody__link chooseShop' data-idmultisklad='" . $meta['id_multisklad'][0] . "' data-shopaddress='" . $meta['address'][0] . "'>Выбрать магазин</a>
</div>",
                    "balloonContentFooter" => "",
                    "clusterCaption" => "",
                    "hintContent" => ""
                ]
            ];
        }

        return $out;
    }

    public static function getForShopsAjax()
    {
        die(json_encode(self::getForShops()));
    }

    public static function getForShops()
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

            $latlong = explode(', ', $meta['latlong'][0]);
            if (count($latlong) < 2)
                continue;

            $out['features'][] = [
                "type" => "Feature",
                "id" => intval($post->ID),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [
                        $latlong[0],
                        $latlong[1]
                    ]
                ],
                "properties" => [
                    "balloonContentHeader" => "<div class='vz-balloonContentHeader'>$post->post_title</div>",
                    "balloonContentBody" => "<div class='vz-balloonContentBody'>
    <div class='vz-balloonContentBody__phone'>" . $meta['phone'][0] . "</div>
    <div class='vz-balloonContentBody__schedule'>" . $meta['schedule'][0] . "</div>
    <a class='vz-balloonContentBody__link' href='" . get_post_permalink($post_id) . "'>Перейти к магазину</a>
</div>",
                    "balloonContentFooter" => "",
                    "clusterCaption" => "",
                    "hintContent" => ""
                ]
            ];
        }

        return $out;
    }

    public static function getForProductCardAjax()
    {
        die(json_encode(self::getForProductCard()));
    }

    public static function getForProductCard()
    {
        if (!empty($_GET['product_id'])) {
            $multi_sklad = get_post_meta($_GET['product_id'], 'multi_sklad', true);
            if (!empty($multi_sklad)) {
                $temp = explode('&1;', $multi_sklad);
                $multi_sklad = [];
                foreach ($temp as $value) {
                    $array = explode('&0;', $value);
                    if (count($array) >= 2)
                        $multi_sklad[$array[0]] = intval($array[1]);
                }
            }
        }

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

            if (empty($multi_sklad[$meta['id_multisklad'][0]]) || $multi_sklad[$meta['id_multisklad'][0]] <= 0)
                continue;

            $latlong = explode(', ', $meta['latlong'][0]);
            if (count($latlong) < 2)
                continue;

            $out['features'][] = [
                "type" => "Feature",
                "id" => intval($post->ID),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [
                        $latlong[0],
                        $latlong[1]
                    ]
                ],
                "properties" => [
                    "balloonContentHeader" => "<div class='vz-balloonContentHeader'>$post->post_title</div>",
                    "balloonContentBody" => "<div class='vz-balloonContentBody'>
    <div class='vz-balloonContentBody__phone'>" . $meta['phone'][0] . "</div>
    <div class='vz-balloonContentBody__schedule'>" . $meta['schedule'][0] . "</div>
    <a class='vz-balloonContentBody__link' href='" . get_post_permalink($post_id) . "'>Перейти к магазину</a>
    <span class='vz-balloonContentBody__book book reserveProductInShop' data-productid='" . ($_GET['product_id'] ?? 0) . "' data-address='" . $post->post_title . "' data-multisklad='" . $meta['id_multisklad'][0] . "' data-quantity='" . $multi_sklad[$meta['id_multisklad'][0]] . "'>
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


    public static function getForOrderAjax()
    {
        die(json_encode(
            self::getForOrder(
                $_GET['order_id'] ?? ''
            )));
    }

    public static function getForOrder($order_id)
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        if (empty($order_id)) {
            $out['error_desc'] = 'Empty order_id.';
            return $out;
        }
        $order = wc_get_order($order_id);

        if (empty($order)) {
            $out['error_desc'] = 'Order not found.';
            return $out;
        }
        if (empty($order->get_shipping_address_1())) {
            $out['error_desc'] = 'Bad shipping address';
            return $out;
        }
        $posts = get_posts([
            'numberposts' => 1,
            'category_name' => 'shops',
            'post_type' => 'post',
            'meta_query' => [
                'id_multisklad' => [
                    'key' => 'id_multisklad',
                    'value' => $order->get_shipping_address_1()
                ],
            ],
        ]);

        if (empty($posts[0])) {
            $out['error_desc'] = 'Shop not found.';
            return $out;
        }
        $shop = $posts[0];

        $out['out']['ymap'] = [
            "type" => "FeatureCollection",
            "features" => []
        ];

        $shop_meta = get_post_meta($shop->ID);
        $latlong = explode(', ', $shop_meta['latlong'][0]);

        $out['out']['data'] = [
            'address' => ($shop_meta['address'][0] ?? ''),
            'metro' => ($shop_meta['metro'][0] ?? ''),
            'schedule' => ($shop_meta['schedule'][0] ?? ''),
            'phone' => ($shop_meta['phone'][0] ?? ''),
            'link' => (get_post_permalink($shop->ID) ?? '')
        ];
        $out['out']['ymap']['features'][] = [
            "type" => "Feature",
            "id" => intval($shop->ID),
            "geometry" => [
                "type" => "Point",
                "coordinates" => [
                    $latlong[0] ?? '',
                    $latlong[1] ?? ''
                ]
            ],
            "properties" => [
                "balloonContentHeader" => "<div class='vz-balloonContentHeader'>($shop->post_title ?? '')</div>",
                "balloonContentBody" => "<div class='vz-balloonContentBody'>
    <div class='vz-balloonContentBody__phone'>" . ($shop_meta['phone'][0] ?? '') . "</div>
    <div class='vz-balloonContentBody__schedule'>" . ($shop_meta['schedule'][0] ?? '') . "</div>
    <a class='vz-balloonContentBody__link' href='" . (get_post_permalink($shop->ID) ?? '') . "'>Перейти к магазину</a>
</div>",
                "balloonContentFooter" => "",
                "clusterCaption" => "",
                "hintContent" => ""
            ]
        ];

        $out['status'] = 'success';
        return $out;
    }
}