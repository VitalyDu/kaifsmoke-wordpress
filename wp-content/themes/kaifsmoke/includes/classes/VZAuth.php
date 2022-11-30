<?php

add_action('wp_ajax_nopriv_register_action', ['VZAuth', 'register']);
add_action('wp_ajax_nopriv_auth_action', ['VZAuth', 'authorize']);
add_action('wp_ajax_current_user_data_action', ['VZAuth', 'getUserData']);
add_action('wp_ajax_logout_action', ['VZAuth', 'logout']);
add_action('wp_ajax_send_reset_key', ['VZAuth', 'sendResetKey']);
add_action('wp_ajax_reset_password', ['VZAuth', 'resetPassword']);
add_action('wp_ajax_nopriv_send_reset_key', ['VZAuth', 'sendResetKey']);
add_action('wp_ajax_nopriv_reset_password', ['VZAuth', 'resetPassword']);
add_action('wp_ajax_check_phone', ['VZAuth', 'checkPhone']);
add_action('wp_ajax_nopriv_check_phone', ['VZAuth', 'checkPhone']);

class VZAuth
{
    public static function register()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        if ($_POST['action'] !== 'register_action') {
            die(json_encode($out));
        }

        if (empty($_POST['firstname']))
            $out['error_desc'] .= '<p>Не заполнено имя</p>';
        if (empty($_POST['phone'])) {
            $out['error_desc'] .= '<p>Не заполнен телефон</p>';
        } else {
            $_POST['phone'] = preg_replace('/[^0-9]/', '', $_POST['phone']);
            $result = get_users([
                'meta_query' => [
                    [
                        'key' => 'phone',
                        'value' => $_POST['phone']
                    ]
                ]
            ]);
            if (!empty($result)) {
                $out['error_desc'] .= '<p>Пользователь с таким телефоном уже зарегистрирован</p>';
            }
        }
        //        if (empty($_POST['confirmCode']) || $_POST['confirmCode'] != $_POST['code'])
        //            $out['error_desc'] .= '<p>Не заполнен code</p>';
        if (empty($_POST['birthday']))
            $out['error_desc'] .= '<p>Не заполнена дата рождения</p>';
        elseif (strtotime($_POST['birthday'] . ' +18 years') > time())
            $out['error_desc'] .= '<p>Вам должно быть больше 18-ти</p>';
        if (empty($_POST['password']))
            $out['error_desc'] .= '<p>Не заполнен пароль</p>';
        if (empty($_POST['email']))
            $out['error_desc'] .= '<p>Не заполнен e-mail</p>';
        elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            $out['error_desc'] .= '<p>E-mail недействителен</p>';
        if (empty($_POST['passwordRepeat']))
            $out['error_desc'] .= '<p>Не заполнен повтор пароля</p>';
        elseif ($_POST['passwordRepeat'] !== $_POST['password'])
            $out['error_desc'] .= '<p>Пароли не совпадают</p>';

        if ($out['error_desc'] !== '') {
            die(json_encode($out));
        }

        $userdata = [
            'user_login' => 'user_' . microtime(true),
            'user_email' => $_POST['email'],
            'user_pass' => $_POST['password'],
            'first_name' => $_POST['firstname'],
            'last_name' => $_POST['lastname'],
            'nickname' => $_POST['firstname'] . ' ' . $_POST['lastname'],
            'user_registered' => date('Y-m-d H:i:s'),
            'show_admin_bar_front' => 'false',
            'role' => 'customer',
            'meta_input' => [
                'shipping_address_1' => (empty($_POST['address'])) ? '' : $_POST['address'],
                'birthday' => $_POST['birthday'],
                'favorites' => $_POST['favorites'] ?? '',
                'sex' => $_POST['sex'] ?? '',
                'phone' => $_POST['phone'],
                'shipping_city' => (empty($_POST['city'])) ? '' : $_POST['city'],
                'subscribition' => (empty($_POST['subscribition'])) ? '' : $_POST['subscribition']
            ]
        ];
        $result = wp_insert_user($userdata);

        if (is_wp_error($result)) {
            foreach ($result->errors as $error) {
                foreach ($error as $value) {
                    $out['error_desc'] .= '<p>' . $value . '</p>';
                }
            }
            die(json_encode($out));
        }

        $credentials = [
            'user_login' => $_POST['email'],
            'user_password' => $_POST['password'],
            'remember' => true
        ];
        $result = wp_signon($credentials);

        if (is_wp_error($result)) {
            foreach ($result->errors as $error) {
                foreach ($error as $value) {
                    $out['error_desc'] .= '<p>' . $value . '</p>';
                }
            }
            die(json_encode($out));
        }

        wp_set_auth_cookie($result->ID);

        $out['status'] = 'ok';
        $out['out'] = [
            'email' => $_POST['email']
        ];

        die(json_encode($out));
    }

    public static function authorize()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => ''
        );

        if ($_POST['action'] !== 'auth_action') {
            $out['error_desc'] .= 'Метод GET для авторизации не поддерживается';
            die(json_encode($out));
        }

        if (empty($_POST['password']))
            $out['error_desc'] .= '<p>Не заполнен пароль</p>';
        if (empty($_POST['login']))
            $out['error_desc'] .= '<p>Не заполнен login</p>';

        if ($out['error_desc'] !== '') {
            die(json_encode($out));
        }

        $credentials = [
            'user_login' => $_POST['login'],
            'user_password' => $_POST['password'],
            'remember' => true
        ];
        $result = wp_signon($credentials);

        if (is_wp_error($result)) {
            //если вместо мыла или логина может быть телефон
            $phone = $_POST['login'];
            $_POST['login'] = preg_replace('/[^0-9]/', '', $_POST['login']);
            $users = get_users([
                'meta_query' => [
                    [
                        'key' => 'phone',
                        'value' => $_POST['login']
                    ]
                ]
            ]);
            if (count($users) === 1 && !empty($users[0]->data->user_login)) {

                $credentials = [
                    'user_login' => $users[0]->data->user_login,
                    'user_password' => $_POST['password'],
                    'remember' => true
                ];
                $result = wp_signon($credentials);
            }

            if (is_wp_error($result)) {
                $out['error_desc'] .= '<p>Номер телефона или пароль были введены неправильно!</p>';
                die(json_encode($out));
            }
        }

        wp_set_auth_cookie($result->ID);

        $out['status'] = 'ok';
        $out['out'] = [
            'login' => $_POST['login']
        ];

        die(json_encode($out));
    }

    public static function getUserData()
    {
        $result = wp_get_current_user();

        $out['status'] = 'ok';
        $out['out'] = $result;

        die(json_encode($out));
    }

    public static function logout()
    {
        wp_logout();

        $out['status'] = 'ok';
        $out['out'] = [];

        die(json_encode($out));
    }

    public static function sendResetKey()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        if (empty($_POST['login'])) {
            $out['error_desc'] = 'Bad login.';
            die(json_encode($out));
        }
        if (!empty(wp_get_current_user()->data->user_email) && $_POST['login'] !== wp_get_current_user()->data->user_email) {
            $out['error_desc'] = 'Это не ваш email.';
            die(json_encode($out));
        }

        $user = get_user_by('email', $_POST['login']);
        if (empty($user)) {
            $out['error_desc'] = 'Пользователь с данным email не найден!';
            die(json_encode($out));
        }

        $hello = '';
        if (!empty($user->data->user_nicename))
            $hello = 'Здравствуйте, ' . $user->data->user_nicename . '!<br>';

        $reset_key = get_password_reset_key($user);
        $subject = 'Восстановление пароля на сайте ' . $_SERVER['SERVER_NAME'];
        $headers = 'From: ' . get_bloginfo('admin_email') . "\r\n" .
            'Reply-To: ' . get_bloginfo('admin_email') . "\r\n" .
            'Content-type: text/html; charset=utf-8' . "\r\n";
        $message = $hello . 'Для восстановления пароля перейдите, пожалуйста, по ссылке: https://kaifsmoke.ru/password-reset/?reset_key=' . $reset_key . '&email=' . $_POST['login'];

        $out['out']['send_status'] = wp_mail($_POST['login'], $subject, $message, $headers);

        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function resetPassword()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        if (empty($_POST['login']) || empty($_POST['reset_key']) || empty($_POST['password']) || empty($_POST['password_repeat']) || $_POST['password'] !== $_POST['password_repeat']) {
            $out['error_desc'] = 'Bad data.';
            die(json_encode($out));
        }

        $user = get_user_by('email', $_POST['login']);
        if (empty($user)) {
            $out['error_desc'] = 'Пользователь с данным email не найден!';
            die(json_encode($out));
        }

        if (is_wp_error(check_password_reset_key($_POST['reset_key'], $user->data->user_login))) {
            $out['error_desc'] = 'Неверный ключ сброса пароля.';
            die(json_encode($out));
        }
        reset_password($user, $_POST['password']);

        $hello = '';
        if (!empty($user->data->user_nicename))
            $hello = 'Здравствуйте, ' . $user->data->user_nicename . '!<br>';

        $subject = 'Восстановление пароля на сайте ' . $_SERVER['SERVER_NAME'];
        $headers = 'From: ' . get_bloginfo('admin_email') . "\r\n" .
            'Reply-To: ' . get_bloginfo('admin_email') . "\r\n" .
            'Content-type: text/html; charset=utf-8' . "\r\n";
        $message = $hello . 'Новый пароль от вашего аккуанта на kaifsmoke.ru:  ' . $_POST['password'] . '<br>По любым вопросам пишите нам на адрес: ' . get_bloginfo('admin_email');

        $out['out']['send_status'] = wp_mail($_POST['login'], $subject, $message, $headers);

        $out['status'] = 'ok';
        die(json_encode($out));
    }

    public static function checkPhone()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => null,
            'out' => null
        );

        if (empty($_GET['phone'])) {
            $out['error_desc'] = 'Empty phone.';
            die(json_encode($out));
        }

        $_GET['phone'] = preg_replace('/[^0-9]/', '', $_GET['phone']);
        $users = get_users([
            'meta_query' => [
                [
                    'key' => 'phone',
                    'value' => $_GET['phone']
                ]
            ]
        ]);

        $out['out']['user_found'] = (count($users) === 1) ? true : false;
        $out['status'] = 'ok';
        die(json_encode($out));
    }
}