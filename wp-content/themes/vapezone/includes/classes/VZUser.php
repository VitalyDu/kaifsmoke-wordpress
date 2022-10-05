<?php
add_action('wp_ajax_user_get', ['VZUser', 'getAjax']);
add_action('wp_ajax_user_update', ['VZUser', 'updateAjax']);
add_action('wp_ajax_user_get_orders', ['VZUser', 'getOrdersAjax']);

class VZUser
{
    public static function get()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        $user_data = wp_get_current_user();
        if (empty($user_data) || empty($user_data->ID)) {
            $out['error_desc'] = ['Пользователь не найден.'];
            return $out;
        } 

        $user_meta = get_user_meta($user_data->ID);

        $out['out']['userdata'] = [
            'user_id' => (!isset($user_data->ID) ? '' : $user_data->ID),
            'lastname' => (!isset($user_meta['last_name'][0]) ? '' : $user_meta['last_name'][0]),
            'firstname' => (!isset($user_meta['first_name'][0]) ? '' : $user_meta['first_name'][0]),
            'email' => (!isset($user_data->data->user_email) ? '' : $user_data->data->user_email),
            'phone' => (!isset($user_meta['phone'][0]) ? '' : $user_meta['phone'][0]),
            'sex' => (!isset($user_meta['sex'][0]) ? '' : $user_meta['sex'][0]),
            'birthday' => (!isset($user_meta['birthday'][0]) ? '' : $user_meta['birthday'][0]),
            'phone_confirmed' => (!isset($user_meta['phone_confirmed'][0]) ? '' : boolval($user_meta['phone_confirmed'][0])),
            'subscribe_to_latest_products' => (!isset($user_meta['subscribe_to_latest_products'][0]) ? '' : boolval($user_meta['subscribe_to_latest_products'][0])),
        ];

        $out['status'] = 'ok';
        return $out;
    }

    public static function getAjax()
    {
        die(json_encode(self::get()));
    }

    public static function update()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        if (empty($_POST['user_data']) || empty($_POST['user_data']['password']) || !wp_check_password($_POST['user_data']['password'], wp_get_current_user()->data->user_pass)) {
            $out['error_desc'] = ['Неправильный пароль.'];
            return $out;
        }

        if (empty($_POST['user_data']['email']) || empty($_POST['user_data']['first_name']) || empty($_POST['user_data']['birthday']) || empty($_POST['user_data']['phone']) || empty($_POST['user_data']['sex']) || !isset($_POST['user_data']['subscribe_to_latest_products'])) {
            $out['error_desc'] = ['Wrong userdata.'];
            return $out;
        }

        if (empty($_POST['user_data']['phone_confirmed'])) {
            $out['error_desc'] = ['Подтвердите свой телефон.'];
            return $out;
        }

        $user_data = $_POST['user_data'];
        $user_data['phone'] = preg_replace('/[^0-9]/', '', $user_data['phone']);
        $user_id = get_current_user_id();

        $out['out'] = wp_update_user([
            'ID' => $user_id,
            'user_email' => $user_data['email'],
            'meta_input' => [
                'last_name' => $user_data['last_name'] ?? '',
                'first_name' => $user_data['first_name'],
                'birthday' => $user_data['birthday'],
                'phone' => $user_data['phone'],
                'sex' => $user_data['sex'],
                'phone_confirmed' => intval($user_data['phone_confirmed']),
                'subscribe_to_latest_products' => intval($user_data['subscribe_to_latest_products'])
            ],
        ]);

        if (!empty($_POST['user_data']['newpassword']) && $_POST['user_data']['repeatnewpassword'] && $_POST['user_data']['newpassword'] == $_POST['user_data']['repeatnewpassword']) {
            $out['password_was_updated'] = wp_set_password($_POST['user_data']['newpassword'], $user_id);
        }

        $out['status'] = 'ok';
        return $out;
    }

    public static function updateAjax()
    {
        die(json_encode(self::update()));
    }

    public static function getOrders()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        $out['out']['orders'] = [];
        if (empty(wp_get_current_user()->data->user_email)) {
            $out['error_desc'] = 'Email not found.';
            return $out;
        }
        $orders = wc_get_orders(array(
            'customer' => wp_get_current_user()->data->user_email
        ));
        foreach ($orders as $wcorder) {
            $order_data = $wcorder->get_data();
            $order_products_data = [];
            foreach ($wcorder->get_items() as $order_product) {
                $order_wcproduct = wc_get_product($order_product->get_data()['product_id']);
                if (empty($order_wcproduct))
                    continue;
                $order_product_data = $order_wcproduct->get_data();
                $order_products_data[] = array(
                    'id' => $order_product_data['id'] ?? '',
                    'name' => $order_product_data['name'] ?? '',
                    'image_link' => wp_get_attachment_image_src(get_post_thumbnail_id($order_product_data['id']), 'single-post-thumbnail')[0] ?? '',
                    'price' => intval($order_wcproduct->get_price()),
                    'price_total' => intval($order_wcproduct->get_price()) * intval($order_product->get_data()['quantity']),
                    'quantity' => intval($order_product->get_data()['quantity'])
                );
            }
            $posts = new WP_Query([
                'numberposts' => 1,
                'meta_key' => 'id_multisklad',
                'meta_value' => $order_data['shipping']['address_1']
            ]);
            $shop = $posts->get_posts()[0];
            $shop_meta = get_post_meta($shop->ID);

            $out['out']['orders'][] = array(
                'id' => $order_data['id'] ?? '',
                'status' => $order_data['status'] ?? '',
                'product_count' => count($wcorder->get_items()) ?? '',
                'products' => $order_products_data ?? '',
                'price' => $wcorder->get_total() ?? '',
                'date' => $order_data['date_created'] ?? '',
                'link' => $wcorder->get_view_order_url() ?? '',
                'shop' => array(
                    'address' => $order_data['shipping']['address_1'] ?? '',
                    'metro' => $shop_meta['metro'][0] ?? '',
                    'phone' => $shop_meta['phone'][0] ?? '',
                    'schedule' => $shop_meta['schedule'][0] ?? ''
                )
            );

        }

        $out['status'] = 'ok';
        return $out;
    }

    public static function getOrdersAjax()
    {
        die(json_encode(self::getOrders()));
    }

    public static function getOrder($order_id)
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        $out['out']['orders'] = [];
        if (empty(wp_get_current_user()->data->user_email)) {
            $out['error_desc'] = 'Email not found.';
            return $out;
        }
        $orders = wc_get_orders(array(
            'p' => $order_id
        ));
        foreach ($orders as $wcorder) {
            $order_data = $wcorder->get_data();
            $order_products_data = [];
            foreach ($wcorder->get_items() as $order_product) {
                $order_wcproduct = wc_get_product($order_product->get_data()['product_id']);
                if (empty($order_wcproduct))
                    continue;
                $order_product_data = $order_wcproduct->get_data();
                $order_products_data[] = array(
                    'id' => $order_product_data['id'] ?? '',
                    'name' => $order_product_data['name'] ?? '',
                    'image_link' => wp_get_attachment_image_src(get_post_thumbnail_id($order_product_data['id']), 'single-post-thumbnail')[0] ?? '',
                    'link' => $order_wcproduct->get_permalink() ?? '',
                    'price' => intval($order_wcproduct->get_price()),
                    'price_total' => intval($order_wcproduct->get_price()) * intval($order_product->get_data()['quantity']),
                    'quantity' => intval($order_product->get_data()['quantity'])
                );
            }
            $posts = new WP_Query([
                'numberposts' => 1,
                'meta_key' => 'id_multisklad',
                'meta_value' => $order_data['shipping']['address_1']
            ]);
            if (empty($posts) || empty($posts->get_posts()[0]))
                continue;
            $shop = $posts->get_posts()[0];
            $shop_meta = get_post_meta($shop->ID);

            $out['out']['orders'][] = array(
                'id' => $order_data['id'] ?? '',
                'status' => $order_data['status'] ?? '',
                'product_count' => count($wcorder->get_items()) ?? '',
                'products' => $order_products_data ?? '',
                'price' => $wcorder->get_total() ?? '',
                'date' => $order_data['date_created'] ?? '',
                'link' => $wcorder->get_view_order_url() ?? '',
                'shop' => array(
                    'address' => $order_data['shipping']['address_1'] ?? '',
                    'metro' => $shop_meta['metro'][0] ?? '',
                    'phone' => $shop_meta['phone'][0] ?? '',
                    'schedule' => $shop_meta['schedule'][0] ?? ''
                )
            );

        }

        $out['status'] = 'ok';
        return $out;
    }
}