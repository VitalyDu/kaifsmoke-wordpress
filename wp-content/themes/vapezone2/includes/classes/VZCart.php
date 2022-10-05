<?php

add_action('wp_ajax_update_cart', ['VZCart', 'update']);
add_action('wp_ajax_set_product_quantity', ['VZCart', 'set']);
add_action('wp_ajax_get_cart', ['VZCart', 'get']);
add_action('wp_ajax_clear_cart', ['VZCart', 'clear']);
add_action('wp_ajax_remove_cart', ['VZCart', 'remove']);
add_action('wp_ajax_get_cart_data', ['VZCart', 'getData']);
add_action('wp_ajax_nopriv_get_cart_data', ['VZCart', 'getData']);
add_action('wp_ajax_get_cart_shops', ['VZCart', 'getShops']);
add_action('wp_ajax_get_cart_products', ['VZCart', 'getProducts']);
add_action('wp_ajax_create_cart_order', ['VZCart', 'createOrder']);

class VZCart 
{
    public static function createOrder()
    {
        //быстрый заказ
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        $user_id = get_current_user_id();
        if (empty($_POST['products']) && empty($_POST['multisklad_id'])) {
            $out['error_desc'] = 'Wrong data.';
            die(json_encode($out));
        }

        $shop_multisklad_id = $_POST['multisklad_id'];

        $order_data = array('status' => 'processing', 'customer_id' => $user_id, 'customer_note' => 'Бронирование');
        $order = new WC_Order();
        $order->set_status( $order_data['status'] );
        $order->set_customer_note( $order_data['customer_note'] );
        $order->set_customer_id( is_numeric( $order_data['customer_id'] ) ? absint( $order_data['customer_id'] ) : 0 );
        //$order = wc_create_order($order_data);

        foreach ($_POST['products'] as $product) {
            $product_id = $product['id'];
            $product_quantity = $product['quantity'];
            $product_multisklad_id = $product['multisklad_id'];
            $product = wc_get_product($product_id);
            $order->add_product($product, $product_quantity);
            if (!empty($product_multisklad_id)) {
                $data = explode('&1;', $product->get_meta('multi_sklad'));
                foreach ($data as $key => $value) {
                    $value = explode('&0;', $value);
                    if ($value[0] === $product_multisklad_id) {
                        if (intval($value[1]) - $product_quantity < 0) {
                            $out['error_desc'] = 'Недостаточно товара на складе';
                            wp_delete_post($order->ID, true);
                            die(json_encode($out));
                        }
                        $value[1] = intval($value[1]) - $product_quantity;
                        $data[$key] = implode('&0;', $value);
                    }
                }
                wc_update_product_stock($product->get_id(), $product_quantity, 'decrease');
                update_post_meta($product->get_id(), 'multi_sklad', implode('&1;', $data));
            }
        }

        $order->set_address(['address_1' => $shop_multisklad_id], 'shipping');
        $order->set_billing_first_name($_POST['username']);
        $order->set_billing_phone($_POST['phone']);

        $shipping_rate = new WC_Shipping_Rate('local_pickup', 'Самовывоз', 0, [], '', 1);
        $order_item_shipping = new WC_Order_Item_Shipping();
        $order_item_shipping->set_shipping_rate($shipping_rate);
        $order->add_item($order_item_shipping);
        $order->calculate_totals();

        $out['out']['order_id'] = $order->save();
        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function getShops()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $shops = get_posts(array(
            'numberposts' => -1,
            'category_name' => 'shops',
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'post'
        ));
        $out['out']['shops'] = [];
        foreach ($shops as $shop) {
            $shop_id = $shop->ID;
            $shop_meta = get_post_meta($shop_id);
            foreach ($shop_meta as $key => &$value) {
                if (strpos($key, '_') === 0) {
                    unset($shop_meta[$key]);
                } else {
                    if (is_array($value)) {
                        $value = $value[0];
                    }
                }
            }
            $out['out']['shops'][] = array_merge(['title' => $shop->post_title], $shop_meta);
        }

        $cart = get_user_meta(get_current_user_id(), 'cart', true);
        if (empty($cart)) {
            $out['status'] = 'ok';
            $out['error_desc'] = 'Empty cart.';
            die(json_encode($out));
        }
        $cart = explode(';', $cart);
        $cart_count = count($cart);
        foreach ($cart as $cart_item) {
            $product_id = explode(',', $cart_item)[0];
            $product_count = explode(',', $cart_item)[1];
            $meta = get_post_meta($product_id);
            if (!is_array($meta) && empty($meta['multi_sklad'][0]))
                continue;
            $multi_sklad = explode('&1;', $meta['multi_sklad'][0]);
            foreach ($multi_sklad as $sklad) {
                $sklad = explode('&0;', $sklad);
                foreach ($out['out']['shops'] as &$shop) {
                    if ($shop['id_multisklad'] == $sklad[0] && $sklad[1] >= $product_count) {
                        if (empty($shop['products_status'])) {
                            $shop['products_status'] = 0;
                        }
                        $shop['products_status']++;
                        break;
                    }
                }
            }
        }

        foreach ($out['out']['shops'] as &$shop) {
            if (empty($shop['products_status'])) {
                $shop['products_status'] = 0;
            }
            switch ($shop['products_status']) {
                case 0:
                    $shop['products_status'] = 'Товаров нет в наличии';
                    $shop['products_status_number'] = 0;
                    break;
                case $cart_count:
                    $shop['products_status'] = 'Все товары в наличии';
                    $shop['products_status_number'] = 2;
                    break;
                default:
                    $shop['products_status'] = 'Не все товары в наличии';
                    $shop['products_status_number'] = 1;
                    break;
            }
        }
        unset($shop);

        usort($out['out']['shops'], function ($a, $b) {
            //$out['count']++;
            return intval($a['products_status_number'] < $b['products_status_number']);
        });

        $out['user'] = get_user_meta(get_current_user_id(), 'last_name', 1);
        unset($out['error_desc']);
        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function getProducts()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $user_id = get_current_user_id();
        if (empty($user_id) || empty($_GET['shop_id'])) {
            $out['error_desc'] = 'Bad data.';
            die(json_encode($out));
        }

        $cart = get_user_meta($user_id, 'cart', true);
        if (empty($cart)) {
            $out['status'] = 'ok';
            $out['error_desc'] = 'Empty cart.';
            die(json_encode($out));
        }

        foreach (explode(';', $cart) as $data) {
            $data = explode(',', $data);
            $id = $data[0];
            $quantity = $data[1];
            if (empty($out['out']['cart_items']))
                $out['out']['cart_items'] = 0;
            $out['out']['cart_items']++;
            if (empty($out['out']['cart_items_all']))
                $out['out']['cart_items_all'] = 0;
            $out['out']['cart_items_all'] += $quantity;
            $result = wc_get_product($id);
            if ($result) {
                $id = $result->get_id();
                $out_item =& $out['out']['products'][$id];
                $out_item = $result->get_data();
                $out_item['in_cart_quantity'] = intval($quantity);
                $out_item['image_url'] = wp_get_attachment_image_url($out_item['image_id'], 'full');
                $attributes = [];
                foreach ($out_item['attributes'] as $key => $value) {
                    $name = wc_attribute_label($key, $id);
                    $attributes[$name] = $result->get_attribute($key);
                }
                $out_item['attributes'] = $attributes;

                $multi_sklad = get_post_meta($id, 'multi_sklad', 1);
                preg_match('/Основной склад&0;([0-9]*)/', $multi_sklad, $matches);
                $out_item['stock_quantity'] = intval($matches[1]);
                preg_match('/' . $_GET['shop_id'] . '&0;([0-9]*)/', $multi_sklad, $matches);
                if (empty($matches[1]))
                    $matches[1] = 0;
                $out_item['in_chosen_shop_stock'] = intval($matches[1]);
                if (intval($matches[1]) >= $quantity) {
                    $out_item['in_chosen_shop_stock_desc'] = 'В наличии';
                    $out_item['in_chosen_shop_stock_status'] = 3;
                } else {
                    if ($out_item['stock_quantity'] >= $quantity) {
                        $out_item['in_chosen_shop_stock_desc'] = 'Доставят за 2 дня';
                        $out_item['in_chosen_shop_stock_status'] = 1;
                    } else {
                        $out_item['in_chosen_shop_stock_desc'] = 'Не в наличии';
                        $out_item['in_chosen_shop_stock_status'] = 0;
                    }
                }
            }
        }

        $out['out']['products'] = array_values($out['out']['products']);
        usort($out['out']['products'], function ($a, $b) {
//            if ($a['in_chosen_shop_stock_status'] == $b['in_chosen_shop_stock_status'])
//                return 0;
//            if ($a['in_chosen_shop_stock_status'] == 2 || $b['in_chosen_shop_stock_status'] == 1)
//                return -1;
//            if ($a['in_chosen_shop_stock_status'] == 1 || $a['in_chosen_shop_stock_status'] == 2)
//                return 1;
            return $a['in_chosen_shop_stock_status'] < $b['in_chosen_shop_stock_status'];
        });

        unset($out['error_desc']);
        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function remove()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $user_id = get_current_user_id();
        if (empty($user_id) || empty($_POST['products_id'])) {
            $out['error_desc'] = 'Bad data.';
            die(json_encode($out));
        }

        $userdata = [
            'ID' => $user_id,
            'meta_input' => [
                'cart' => ''
            ]
        ];
        $cart = get_user_meta($user_id, 'cart', true);
        if (empty($cart)) {
            $out['error_desc'] = 'Cart is empty.';
            die(json_encode($out));
        }
        $cart = explode(';', $cart);
        foreach ($_POST['products_id'] as $product_id) {
            $is_removed = false;
            foreach ($cart as $key => $value) {
                if (strpos($value, $product_id . ',') === 0) {
                    unset($cart[$key]);
                    $is_removed = true;
                    break;
                }
            }
        }
        if (!$is_removed) {
            $out['error_desc'] = 'Product not found.';
            $userdata['meta_input']['cart'] = implode(';', $cart);
            die(json_encode($out));
        }

        $userdata['meta_input']['cart'] = implode(';', $cart);
        wp_update_user($userdata);
        $out['out'] = $userdata['meta_input']['cart'];
        unset($out['error_desc']);
        $out['status'] = 'ok';

        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function clear()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $user_id = get_current_user_id();
        $userdata = [
            'ID' => $user_id,
            'meta_input' => [
                'cart' => ''
            ]
        ];
        $out['out'] = wp_update_user($userdata);
        unset($out['error_desc']);
        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function get()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $cart = explode(';', get_user_meta(get_current_user_id(), 'cart', true));
        $temp_cart = [];
        foreach ($cart as $product) {
            $product = explode(',', $product);
            $temp_cart[$product[0]] = $product[1];
        }

        $out['out']['cart'] = $temp_cart;
        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function getData()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $cart = get_user_meta(get_current_user_id(), 'cart', true);
        if (empty($cart)) {
            $out['status'] = 'ok';
            $out['error_desc'] = 'Empty cart.';
            die(json_encode($out));
        }

        foreach (explode(';', $cart) as $data) {
            $data = explode(',', $data);
            $id = $data[0];
            $quantity = $data[1];
            $out['out']['cart_items'] = (empty($out['out']['cart_items'])) ? 1 : $out['out']['cart_items']++;
            $out['out']['cart_items_all'] = (empty($out['out']['cart_items_all'])) ? $quantity : intval($out['out']['cart_items_all']) + $quantity;
            $result = wc_get_product($id);
            if ($result) {
                $id = $result->get_id();
                $out['out']['products'][$id] = $result->get_data();
                $out['out']['products'][$id]['in_cart_quantity'] = intval($quantity);
                $out['out']['products'][$id]['image_url'] = wp_get_attachment_image_url($out['out']['products'][$id]['image_id'], 'full');
                $attributes = [];
                foreach ($out['out']['products'][$id]['attributes'] as $key => $value) {
                    $name = wc_attribute_label($key, $id);
                    $attributes[$name] = $result->get_attribute($key);
                }
                $out['out']['products'][$id]['attributes'] = $attributes;
            }
        }

        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function update()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $user_id = get_current_user_id();
        if (empty($user_id) || empty($_POST['product_id']) || empty($_POST['product_quantity'])) {
            $out['error_desc'] = 'Bad data.';
            die(json_encode($out));
        }

        $userdata = [
            'ID' => $user_id,
            'meta_input' => [
                'cart' => ''
            ]
        ];
        $cart = get_user_meta($user_id, 'cart', true);
        if (empty($cart)) {
            $cart = [];
        } else {
            $cart = explode(';', $cart);
        }
        $is_new_product_set = false;
        if (!empty($cart)) {
            foreach ($cart as $key => $product) {
                $product = explode(',', $product);
                if ($product[0] == $_POST['product_id']) {
                    if (intval($_POST['product_quantity'] + intval($product[1]) <= 0)) {
                        unset($cart[$key]);
                    } else {
                        $cart[$key] = $_POST['product_id'] . ',' . (intval($_POST['product_quantity']) + intval($product[1]));
                    }
                    $is_new_product_set = true;
                    break;
                }
            }
        }
        if (!$is_new_product_set && intval($_POST['product_quantity']) > 0) {
            $cart[] = $_POST['product_id'] . ',' . intval($_POST['product_quantity']);
        }

        $userdata['meta_input']['cart'] = implode(';', $cart);
        $result = wp_update_user($userdata);
        if (is_wp_error($result)) {
            foreach ($result->errors as $error) {
                foreach ($error as $value) {
                    $out['error_desc'] .= '<p class="error">' . $value . '</p>';
                }
            }
            die(json_encode($out));
        }

        $temp_cart = [];
        foreach ($cart as $product) {
            $product = explode(',', $product);
            $temp_cart[$product[0]] = $product[1];
        }
        $out['out']['cart'] = $temp_cart;
        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function set()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $user_id = get_current_user_id();
        if (empty($user_id) || empty($_POST['product_id']) || empty($_POST['product_quantity'])) {
            $out['error_desc'] = 'Bad data.';
            die(json_encode($out));
        }

        $userdata = [
            'ID' => $user_id,
            'meta_input' => [
                'cart' => ''
            ]
        ];
        $cart = get_user_meta($user_id, 'cart', true);
        if (empty($cart)) {
            $cart = [];
        } else {
            $cart = explode(';', $cart);
        }
        $is_new_product_set = false;
        if (!empty($cart)) {
            foreach ($cart as $key => $product) {
                $product = explode(',', $product);
                if ($product[0] == $_POST['product_id']) {
                    $cart[$key] = $_POST['product_id'] . ',' . $_POST['product_quantity'];
                    $is_new_product_set = true;
                    break;
                }
            }
        }
        if (!$is_new_product_set && intval($_POST['product_quantity']) > 0) {
            $cart[] = $_POST['product_id'] . ',' . intval($_POST['product_quantity']);
        }

        $userdata['meta_input']['cart'] = implode(';', $cart);
        $result = wp_update_user($userdata);
        if (is_wp_error($result)) {
            foreach ($result->errors as $error) {
                foreach ($error as $value) {
                    $out['error_desc'] .= '<p class="error">' . $value . '</p>';
                }
            }
            die(json_encode($out));
        }

        $temp_cart = [];
        foreach ($cart as $product) {
            $product = explode(',', $product);
            $temp_cart[$product[0]] = $product[1];
        }
        $out['out']['cart'] = $temp_cart;
        $out['status'] = 'ok';
        die(json_encode($out));
    }
}