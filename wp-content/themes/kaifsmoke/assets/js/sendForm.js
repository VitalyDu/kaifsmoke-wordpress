$(document).ready(function () {
  function isValid(id, pat) {
    var value = $(id).val();
    var pattern = new RegExp("^" + pat + "", "i");
    if (pattern.test(value)) {
      return true;
    } else {
      return false;
    }
  }
  if ($(".form_block")) {
    $(".form_block")
      .find('input[name="callback_phone"]')
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
    $(".form_send__btn").on("click", function () {
      var error = [];
      var formTitle = $(this).parents(".form_block").find(".form_title").text();
      var name = $(this)
        .parents(".form_block")
        .find('input[name="callback_firstName"]');
      var phone = $(this)
        .parents(".form_block")
        .find('input[name="callback_phone"]');
      var email = $(this)
        .parents(".form_block")
        .find('input[name="callback_communication"]');
      var order = $(this)
        .parents(".form_block")
        .find('input[name="callback_order"]');
      var message = $(this)
        .parents(".form_block")
        .find('textarea[name="message"]');
      if (order.length && !order.val()) {
        error.push("<p>Номер заказа заполнен неправильно!</p>");
        order.addClass("error");
      } else {
        error.push();
        order.removeClass("error");
      }
      if (name.length && !isValid(name, "[-a-zA-Z-а-яА-Я]+$")) {
        error.push("<p>Имя заполнено неправильно!</p>");
        name.addClass("error");
      } else {
        error.push();
        name.removeClass("error");
      }
      if (
        email.length &&
        !isValid(
          email,
          "[a-zA-Zа-яА-ЯёЁ_\\d][-a-zA-Zа-яА-ЯёЁ0-9_\\.\\d]*\\@[a-zA-Zа-яА-ЯёЁ\\d][-a-zA-Zа-яА-ЯёЁ\\.\\d]*\\.[a-zA-Zа-яА-Я]{2,6}$"
        )
      ) {
        error.push("<p>Email заполнен неправильно!</p>");
        email.addClass("error");
      } else {
        error.push();
        email.removeClass("error");
      }
      if (phone.length && !phone.val()) {
        error.push("<p>Телефон заполнен неправильно!</p>");
        phone.addClass("error");
      } else {
        error.push();
        phone.removeClass("error");
      }
      if (message.length && !message.val()) {
        error.push("<p>Вы не написали сообщение</p>");
        message.addClass("error");
      } else {
        error.push();
        message.removeClass("error");
      }
      if (!error.length) {
        $.ajax({
          url: AJAXURL,
          dataType: "json",
          method: "POST",
          data: {
            action: "send_form",
            formTitle: formTitle,
            data: {
              name: {
                title: "Имя",
                value: name.val(),
              },
              phone: {
                title: "Телефон",
                value: phone.val(),
              },
              mail: {
                title: "Email",
                value: email.val(),
              },
              order: {
                title: "Номер заказа",
                value: order.val(),
              },
              message: {
                title: "Сообщение",
                value: message.val(),
              },
            },
          },
          success: (data) => {
            if (data.status === "ok") {
              $(".form_sent").css("display", "flex").hide().fadeIn();
              new Noty({
                type: "alert",
                text: "Сообщение отправлено!",
              }).show();
            } else {
              data.error_desc.map((item) => {
                new Noty({
                  type: "error",
                  text: `${item}`,
                }).show();
              });
            }
          },
        });
      } else {
        error.map((item) => {
          new Noty({
            type: "error",
            text: `${item}`,
          }).show();
        });
      }
    });
  }
});
