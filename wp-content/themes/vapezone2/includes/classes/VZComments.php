<?php

add_action('wp_ajax_nopriv_send_review', ['VZComments', 'send']);
add_action('wp_ajax_send_review', ['VZComments', 'send']);

class VZComments
{
    public static function send()
    {
        $out = array(
            'status' => 'error',
            'error_desc' => []
        );

        if (empty($_POST['data']) || empty($_POST['data']['id']) || empty($_POST['data']['name']) || empty($_POST['data']['rating']) || empty($_POST['data']['message'])) {
            $out['error_desc'][] = 'Некорректная data.';
            die(json_encode($out));
        }

        $commentdata = [
            'comment_author' => $_POST['data']['name'],
            'comment_content' => $_POST['data']['message'],
            'comment_post_ID' => $_POST['data']['id'],
            'comment_approved' => 0,
            'comment_meta' => [
                    'rating' => $_POST['data']['rating']
                ]
        ];
        $out['out'] = wp_insert_comment($commentdata);

        $out['status'] = 'ok';
        unset($out['error_desc']);
        die(json_encode($out));
    }

}
