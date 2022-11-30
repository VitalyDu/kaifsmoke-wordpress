$(document).ready(function () {
  if ($(".accManagePage").length) {
    $(".accManagePage")
      .find(".textField_phone")
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
  }

  if ($(".removePhone").length) {
    $(".removePhone").on("click", function () {
      $(this).siblings("input").val("").attr("readonly", false);
      $(this).hide();
      $(".phoneVerificationStatus").hide();
      $(".phoneVerificationField").css("display", "flex").hide().fadeIn();
      $(".phoneVerificationStatus").removeClass("success");
    });
  }

  $(".sendCode_button").on("click", function () {
    var phone = $(this).parents(".user_form").find('input[name="user_phone"]');
    if (phone.val()) {
      $(".loader").css("display", "flex").hide().fadeIn();
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "get",
        data: {
          action: "check_phone",
          phone: phone.val(),
        },
        success: (data) => {
          $(".loader").fadeOut();
          if (!data.out.user_found) {
            $(this).attr("disabled", true);
            $(this).removeClass("primary").addClass("secondary");
            $('input[name="user_phoneVerificationCode"]').attr(
              "readonly",
              false
            );
            var code = Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000;
            $.cookie("verCode", code);
            if ($(this).hasClass("firstSend")) {
              sendCallAjaxForm(code, phone.val().replace(/\D/g, ""));
            } else {
              sendSmsAjaxForm("/smsc.php", code, phone.val());
            }
            var _Seconds = 30,
              int;
            var that = $(this);
            int = setInterval(function () {
              if (_Seconds > 0) {
                _Seconds--;
                that
                  .parents(".field")
                  .find("label")
                  .text(`Отправить повторно, через 00:${_Seconds}`);
              } else {
                clearInterval(int);
                that
                  .parents(".field")
                  .find("label")
                  .text(`Подтверждение номера`);
                that
                  .parents(".field")
                  .find(".description")
                  .text(`Вам поступит смс, введите код для подтверждения телефона`);
                that
                  .text(`Подтвердить`)
                  .removeClass("firstSend")
                  .attr("disabled", false);
              }
            }, 1000);
          } else {
            new Noty({
              type: "error",
              text: `Данный номер телефона уже зарегистрирован!`,
            }).show();
            // if (typeof data.error_desc == "string") {
            //   new Noty({
            //     type: "error",
            //     text: `${data.error_desc}`,
            //   }).show();
            // } else {
            //   data.error_desc.map((item) => {
            //     new Noty({
            //       type: "error",
            //       text: `${item}`,
            //     }).show();
            //   });
            // }
          }
        },
        error: () => {
          $(".loader").fadeOut();
          new Noty({
            type: "error",
            text: `Произошла ошибка, попробуйте позже.`,
          }).show();
        },
      });
    } else {
      new Noty({
        type: "error",
        text: `Поле "Телефон" пустое, заполните его!`,
      }).show();
    }
  });

  function sendSmsAjaxForm(url, code, phoneNumber) {
    $.ajax({
      url: url,
      type: "POST",
      dataType: "html",
      data: {
        phoneNumber: phoneNumber,
        code: code,
      },
      success: function (response) {
        // result = $.parseJSON(response);
        new Noty({
          type: "notification",
          text: `Код был отправлен на введённый номер телефона.`,
        }).show();
      },
      error: function (response) {
        console.log(response);
      },
    });
  }

  function sendCallAjaxForm(code, phoneNumber) {
    $.ajax({
      url: "/ucall.php",
      type: "POST",
      dataType: "application/json",
      data: {
        phone: phoneNumber,
        code: code,
      },
      success: function (response) {
        // result = $.parseJSON(response);
        new Noty({
          type: "notification",
          text: `Вам поступит звонок, введите 4 последних цифры входящего номера в поле "Подтверждения телефона".`,
        }).show();
      },
      error: function (response) {
        console.log(response);
      },
    });
  }

  $('input[name="user_phoneVerificationCode"]').on("input", function () {
    if ($(this).val().length == 4 && $(this).val() == $.cookie("verCode")) {
      $(this)
        .parents(".user_form")
        .find('input[name="user_phone"]')
        .attr("readonly", true);
      $(this).parents(".phoneVerificationField").hide();
      $(this)
        .parents(".phoneVerificationField")
        .siblings(".phoneVerificationStatus")
        .addClass("success")
        .css("display", "flex")
        .hide()
        .fadeIn();
      //   $(this)
      //     .parents(".editInformation_otherInf")
      //     .find(".removePhone")
      //     .css("display", "flex")
      //     .hide()
      //     .fadeIn();
    } else if (
      $(this).val().length == 4 &&
      $(this).val() != $.cookie("verCode")
    ) {
      $(this).addClass("error");
      new Noty({
        type: "error",
        text: `Код подтверждения номера телефона неправильный!`,
      }).show();
    }
  });
});
