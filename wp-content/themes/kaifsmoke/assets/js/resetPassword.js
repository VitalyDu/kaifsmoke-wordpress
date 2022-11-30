$(document).ready(function () {
  if ($(".passwordResetForm").length) {
    function isValid(id, pat) {
      var value = $(id).val();
      var pattern = new RegExp("^" + pat + "", "i");
      if (pattern.test(value)) {
        return true;
      } else {
        return false;
      }
    }

    $(".sendPasswordResetForm").on("click", function () {
      var key = $(this)
        .parents(".passwordResetForm")
        .find('input[name="user_resetKey"]');
      var email = $(this)
        .parents(".passwordResetForm")
        .find('input[name="user_email"]');
      var passwordNew = $(this)
        .parents(".passwordResetForm")
        .find('input[name="user_passwordNew"]');
      var passwordNewRepeat = $(this)
        .parents(".passwordResetForm")
        .find('input[name="user_passwordNewRepeat"]');
      var errors = [];
      if (!passwordNew.val()) {
        errors.push('<p>Заполните поле "Новый пароль"!</p>');
        passwordNew.addClass("error");
      } else {
        passwordNew.removeClass("error");
      }
      if (
        passwordNew.val() &&
        !isValid(passwordNew, "(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{4,}$")
      ) {
        errors.push(
          "<p>Длина пароля должна составлять минимум 4 символа! Пароль должен состоять из цифр и букв латинского алфавита!</p>"
        );
        passwordNew.addClass("error");
      } else {
        passwordNew.removeClass("error");
      }
      if (passwordNew.val() && passwordNewRepeat.val() != passwordNew.val()) {
        errors.push(
          '<p>Поле "Повторите новый пароль" и "Новый пароль" должны совпадать!</p>'
        );
        passwordNewRepeat.addClass("error");
      } else {
        passwordNewRepeat.removeClass("error");
      }
      if (!errors.length) {
        $.ajax({
          url: AJAXURL,
          dataType: "json",
          method: "post",
          data: {
            action: "reset_password",
            login: email.val(),
            reset_key: key.val(),
            password: passwordNew.val(),
            password_repeat: passwordNewRepeat.val(),
          },
          success: (data) => {
            let userdata = {
              login: email.val(),
              password: passwordNew.val(),
            };
            if (data.status === "ok") {
              $.ajax({
                url: AJAXURL,
                method: "POST",
                dataType: "json",
                data: Object.assign(
                  {
                    action: "auth_action",
                  },
                  userdata
                ),
                success: async (data) => {
                  if (data.status === "ok") {
                    window.location.href = "/";
                  }
                },
              });
            } else {
              if (typeof data.error_desc == "string") {
                new Noty({
                  type: "error",
                  text: `${data.error_desc}`,
                }).show();
              } else {
                data.error_desc.map((item) => {
                  new Noty({
                    type: "error",
                    text: `${item}`,
                  }).show();
                });
              }
            }
          },
        });
      } else {
        errors.map((item) => {
          new Noty({
            type: "error",
            text: `${item}`,
          }).show();
        });
      }
    });
  }
});
