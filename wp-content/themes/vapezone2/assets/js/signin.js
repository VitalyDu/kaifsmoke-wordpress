$(document).ready(function () {
  if ($(".signInPage_block").length) {
    $(".signInPage_block")
      .find('input[name="phoneLogin"]')
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
    $(".signInPage_block__form input").keypress(function (e) {
      var key = e.which;
      if (key == 13) {
        $(".signIn_btn").click();
        return false;
      }
    });
    $(".signIn_btn").on("click", function () {
      let error = [];
      let login = $(this).parents("form").find('input[name="phoneLogin"]');
      let password = $(this).parents("form").find('input[name="password"]');
      if (!login.val()) {
        error.push('<p>Заполните поле "Телефон"</p>');
        login.addClass("error");
        login.siblings("label").addClass("error");
      } else {
        login.removeClass("error");
        login.siblings("label").removeClass("error");
      }
      if (!password.val()) {
        error.push('<p>Заполните поле "Пароль"</p>');
        password.addClass("error");
        password.siblings("label").addClass("error");
      } else {
        password.removeClass("error");
        password.siblings("label").removeClass("error");
      }
      //   if (!dataProcessing.prop('checked')) {
      //     error += "<p>Подтвердите согласие на обработку персональных данных!</p>";
      //     dataProcessing.addClass("error");
      //     dataProcessing.siblings("span").addClass("error");
      //   } else {
      //     error += "";
      //   }
      if (!error.length) {
        let userdata = {
          login: login.val(),
          password: password.val(),
        };
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
              await Favorites.export();
              window.location.href = "/";
            } else {
              // $(".signIn_errors").fadeIn().html(data.error_desc);
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
        // $(".signIn_errors").fadeIn().html(error);
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
