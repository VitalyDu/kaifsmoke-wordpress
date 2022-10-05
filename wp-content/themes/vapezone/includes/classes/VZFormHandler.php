<?php

add_action('wp_ajax_nopriv_send_form', ['VZFormHandler', 'send']);
add_action('wp_ajax_send_form', ['VZFormHandler', 'send']);

class VZFormHandler
{
    public static function send()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => []
        );

        $subject = 'Обратная связь с сайта ' . $_SERVER['SERVER_NAME'];
        if (!empty($_POST['formTitle'])) {
            $subject = 'Ответ с формы ' . $_POST['formTitle'];
        }

        $headers = 'From: ' . $_SERVER['SERVER_ADMIN'] . "\r\n" .
            'Reply-To: ' . $_SERVER['SERVER_ADMIN'] . "\r\n" .
            'Content-type: text/html; charset=utf-8' . "\r\n";

        $to = get_field('callback_emails', 'option');

        if (empty($_POST['data'])) {
            $out['error_desc'][] = 'Empty data';
            die(json_encode($out));
        }

        $message = '';

        foreach ($_POST['data'] as $key => $value) {
            if (empty($value['value'])) {
                continue;
            }

            $validation = self::validateField($key, $value);
            if ($validation['status'] !== 'ok') {
                $out['error_desc'][] = $validation['error_desc'];
            }

            $message .= '<b>' . $value['title'] . '</b>: ' . $value['value'] . '<br>';
        }
        if (!empty($out['error_desc'])) {
            die(json_encode($out));
        }

        $out['out']['send_status'] = [];
        foreach ($to as $email) {
            $out['out']['send_status'][] = wp_mail($email['email'], $subject, $message, $headers);
        }

        $out['status'] = 'ok';
        unset($out['error_desc']);
        die(json_encode($out));
    }

    public static function validateField($key, $value)
    {
        $out = array(
            'status' => 'ok',
            'error_desc' => []
        );

        switch ($key) {
            case 'phone':
                if (!preg_match('/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', $value['value'])) {
                    $out['status'] = 'error';
                    $out['error_desc'] = 'Поле "' . $value['title'] . '" заполнено некорректно';
                }
                break;
            case 'email':
                if (!preg_match('/[a-zA-Zа-яА-ЯёЁ_\\d][-a-zA-Zа-яА-ЯёЁ0-9_\\.\\d]*\\@[a-zA-Zа-яА-ЯёЁ\\d][-a-zA-Zа-яА-ЯёЁ\\.\\d]*\\.[a-zA-Zа-яА-Я]{2,6}$/', $value['value'])) {
                    $out['status'] = 'error';
                    $out['error_desc'] = 'Поле "' . $value['title'] . '" заполнено некорректно';
                }
                break;
        }

        return $out;
    }
}
