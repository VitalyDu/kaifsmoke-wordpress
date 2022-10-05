$(document).ready(function () {
  if ($(".passwordRecoveryForm").length) {
    function isValid(id, pat) {
      var value = $(id).val();
      var pattern = new RegExp("^" + pat + "", "i");
      if (pattern.test(value)) {
        return true;
      } else {
        return false;
      }
    }

    $(".sendPasswordRecoveryForm").on("click", function () {
      var email = $(this)
        .parents(".passwordRecoveryForm")
        .find('input[name="user_email"]');
      var errors = [];
      if (
        !isValid(
          email,
          "[a-zA-Zа-яА-ЯёЁ_\\d][-a-zA-Zа-яА-ЯёЁ0-9_\\.\\d]*\\@[a-zA-Zа-яА-ЯёЁ\\d][-a-zA-Zа-яА-ЯёЁ\\.\\d]*\\.[a-zA-Zа-яА-Я]{2,6}$"
        )
      ) {
        errors.push("<p>Email заполнен неправильно!</p>");
        email.addClass("error");
      } else {
        email.removeClass("error");
      }
      if (!errors.length) {
        $.ajax({
          url: AJAXURL,
          dataType: "json",
          method: "post",
          data: {
            action: "send_reset_key",
            login: email.val(),
          },
          success: (data) => {
            if (data.status === "ok") {
              new Noty({
                type: "notification",
                text: `Ссылка для изменения пароля была отправлена на email`,
              }).show();
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
