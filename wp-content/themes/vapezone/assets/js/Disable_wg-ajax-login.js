
jQuery(document).ready(function ($) {
    $(".loginBtn").on('click', function (e) {
        var admin_url = wg_ajax_login_object.ajax_url;
        var user_login = $(this).parents('form').find('.user_login').val();
        var user_password = $(this).parents('form').find('.user_password').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: admin_url,
            data: {
                'action': 'wg_ajax_login_handler', //calls wp_ajax_nopriv_ajaxlogin
                'user_login': user_login,
                'user_password': user_password,
                'nonce': $('#wg_ajax_nonce').val()
            },
            beforeSend: function () {
                // Show user a message that details are being checked
                // $('.signInPage_block__form').find('span.error').text(wg_ajax_login_object.beforeMessage);
            },
            success: function (data) {
                if (data.loggedin == true) {
                    // Show success message if user details exist
                    // $('.signInPage_block__form').find('span.error').text(wg_ajax_login_object.successMessage);
                    // Create timer to refresh page after successfull login
                    setTimeout(function () {
                        // document.location.href = wg_ajax_login_object.redirectUrl;
                        location.reload();
                    }, 100);
                } else if (data.loggedin == false) {
                    // Show failure message if user details doesn't exist
                    $('.signInPage_block__form, .signInModal_block').find('.input-field input').addClass('error');
                    $('.signInPage_block__form, .signInModal_block').find('span.error').text(wg_ajax_login_object.failureMessage);
                }
            }
        });
        e.preventDefault();
    });
});