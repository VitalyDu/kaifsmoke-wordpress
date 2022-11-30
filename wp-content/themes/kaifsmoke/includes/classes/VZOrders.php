<?php

add_action('wp_ajax_nopriv_vz_create_order', ['VZOrders', 'createOrder']);
add_action('wp_ajax_vz_create_order', ['VZOrders', 'createOrder']);

class VZOrders
{
    public static function createOrder()
    {
        //заказ корзины
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $out['error_desc'] = 'Пользователь не авторизован';
            die(json_encode($out));
        }
        if (empty($_POST['product_id'])) {
            $out['error_desc'] = 'Товар не найден';
            die(json_encode($out));
        }
        $product_id = intval($_POST['product_id']);
        if (empty($_POST['address'])) {
            $out['error_desc'] = 'Адрес не найден';
            die(json_encode($out));
        }
        $address = $_POST['address'];

        $shop_multisklad_id = $_POST['shop_multisklad_id'];

        $order_data = array('status' => 'processing', 'customer_id' => $user_id, 'customer_note' => 'Бронирование');
        $order = new WC_Order();
        $order->set_status( $order_data['status'] );
        $order->set_customer_note( $order_data['customer_note'] );
        $order->set_customer_id( is_numeric( $order_data['customer_id'] ) ? absint( $order_data['customer_id'] ) : 0 );
        //$order = wc_create_order($order_data);

        $product = wc_get_product($product_id);
        $order->add_product($product, 1);
        if (!empty($shop_multisklad_id)) {
            $data = explode('&1;', $product->get_meta('multi_sklad'));
            foreach ($data as $key => $value) {
                $value = explode('&0;', $value);
                if ($value[0] === $shop_multisklad_id) {
                    if (intval($value[1]) - 1 < 0) {
                        $out['error_desc'] = 'Недостаточно товара на складе';
                        die(json_encode($out));
                    }
                    $value[1] = intval($value[1]) - 1;
                    $data[$key] = implode('&0;', $value);
                }
            }
            wc_update_product_stock($product->get_id(), 1, 'decrease');
            update_post_meta($product->get_id(), 'multi_sklad', implode('&1;', $data));

        }

        $order->set_address(['address_1' => $shop_multisklad_id], 'shipping');

        $shipping_rate = new WC_Shipping_Rate('local_pickup', 'Самовывоз', 0, [], '', 1);
        $order_item_shipping = new WC_Order_Item_Shipping();
        $order_item_shipping->set_shipping_rate($shipping_rate);
        $order->add_item($order_item_shipping);
        $order->calculate_totals();
        $order->save();

        $out['status'] = 'ok';
        $out['data']['order_id'] = $order->get_id();
        $out['data']['product_id'] = $product_id;
        $out['data']['address'] = $shop_multisklad_id;
        die(json_encode($out));
    }
}