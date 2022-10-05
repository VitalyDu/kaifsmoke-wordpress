<?php

add_action('wp_ajax_get_favs_data_action', ['VZFavorites', 'getData']);
add_action('wp_ajax_nopriv_get_favs_data_action', ['VZFavorites', 'getData']);

add_action('wp_ajax_write_favs_action', ['VZFavorites', 'write']);

add_action('wp_ajax_read_favs_action', ['VZFavorites', 'read']);

class VZFavorites
{
    public static function getData()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        if (empty($_GET['favorites'])) {
            $out['out'] = ['products' => null];
            $out['error_desc'] = 'Empty favs.';
            die(json_encode($out));
        }

        foreach (explode(',', $_GET['favorites']) as $id) {
            $result = wc_get_product($id);
            if ($result) {
                $id = $result->get_id();

                $out['out']['products'][$id] = $result->get_data();

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

    public static function write()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $userdata = [
            'ID' => get_current_user_id(),
            'meta_input' => [
                'favorites' => $_POST['favorites']
            ]
        ];
        $out['out']['userdata'] = $userdata;
        $result = wp_update_user($userdata);
        if (is_wp_error($result)) {
            foreach ($result->errors as $error) {
                foreach ($error as $value) {
                    $out['error_desc'] .= '<p class="error">' . $value . '</p>';
                }
            }
            die(json_encode($out));
        }

        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function read()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        $result = get_user_meta(get_current_user_id(), 'favorites', true);
        if ($result === false) {
            die(json_encode($out));
        }

        $out['out']['favorites'] = $result;
        $out['status'] = 'ok';
        die(json_encode($out));
    }
}