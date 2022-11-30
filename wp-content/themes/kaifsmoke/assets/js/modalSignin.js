$(document).ready(function () {
  if ($(".navigation_profileDropdown__noAuthorized").length) {
    $(".navigation_profileDropdownNoAuthorized__form")
      .find('input[name="phoneLogin"]')
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
  }
  if ($(".mobileMenuSignIn_block__form").length) {
    $(".mobileMenuSignIn_block__form")
      .find('input[name="mobile_userLogin"]')
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
  }
  if ($(".navigation_profileDropdown__noAuthorized").length) {
    if ($.cookie("user_login")) {
      $(".navigation_profileDropdown__noAuthorized")
        .find('input[name="phoneLogin"]')
        .val($.cookie("user_login"));
    }
    if ($.cookie("user_password")) {
      $(".navigation_profileDropdown__noAuthorized")
        .find('input[name="password"]')
        .val($.cookie("user_password"));
    }
    $(".navigation_profileDropdownNoAuthorized__form input").keypress(function (
      e
    ) {
      var key = e.which;
      if (key == 13) {
        $(".modalSignInBtn").click();
        return false;
      }
    });
    $(".modalSignInBtn").on("click", function () {
      let error = [];
      let login = $(this)
        .parents(".auth_form")
        .find('input[name="phoneLogin"]');
      let password = $(this)
        .parents(".auth_form")
        .find('input[name="password"]');
      let rememberMeCheck = $(this)
        .parents(".auth_form")
        .find('input[name="rememberMe"]');
      if (!login.val()) {
        error.push('<p>Заполните поле "Телефон"</p>');
        login.addClass("error");
      } else {
        error.push();
      }
      if (!password.val()) {
        error.push('<p>Заполните поле "Пароль"</p>');
        password.addClass("error");
      } else {
        error.push();
      }
      if (!error.length) {
        let rememberMe = rememberMeCheck.prop("checked") ? true : false;
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
              if (rememberMe) {
                $.cookie("user_login", userdata.login);
                $.cookie("user_password", userdata.password);
              }
              await Favorites.export();
              window.location.href = "/";
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
        error.map((item) => {
          new Noty({
            type: "error",
            text: `${item}`,
          }).show();
        });
      }
    });
  }
  if ($(".mobileMenuSignIn").length) {
    if ($.cookie("user_login")) {
      $(".mobileMenuSignIn")
        .find('input[name="mobile_userLogin"]')
        .val($.cookie("user_login"));
    }
    if ($.cookie("user_password")) {
      $(".mobileMenuSignIn")
        .find('input[name="mobile_userPassword"]')
        .val($.cookie("user_password"));
    }
    $(".mobileMenuSignIn_block__form input").keypress(function (e) {
      var key = e.which;
      if (key == 13) {
        $(".mobileMenuSignIn_button").click();
        return false;
      }
    });
    $(".mobileMenuSignIn_button").on("click", function () {
      let error = [];
      let login = $(this)
        .parents(".auth_form")
        .find('input[name="mobile_userLogin"]');
      let password = $(this)
        .parents(".auth_form")
        .find('input[name="mobile_userPassword"]');
      let rememberMeCheck = $(this)
        .parents(".auth_form")
        .find('input[name="rememberMe"]');
      if (!login.val()) {
        error.push('<p>Заполните поле "Телефон"</p>');
        login.addClass("error");
      } else {
        error.push();
      }
      if (!password.val()) {
        error.push('<p>Заполните поле "Пароль"</p>');
        password.addClass("error");
      } else {
        error.push();
      }
      if (!error.length) {
        let rememberMe = rememberMeCheck.prop("checked") ? true : false;
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
              if (rememberMe) {
                $.cookie("user_login", userdata.login);
                $.cookie("user_password", userdata.password);
              }
              await Favorites.export();
              window.location.href = "/";
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
