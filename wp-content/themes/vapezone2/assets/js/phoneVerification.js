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
            $(this).text("Код отправлен");
            $(this).removeClass("primary").addClass("secondary");
            $(this).attr("disable", true);
            $('input[name="user_phoneVerificationCode"]').attr(
              "readonly",
              false
            );
            var code = Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000;
            $.cookie("verCode", code);
            sendAjaxForm("/smsc.php", code, phone.val());
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

  function sendAjaxForm(url, code, phoneNumber) {
    $.ajax({
      url: url, //url страницы (action_ajax_form.php)

      type: "POST", //метод отправки

      dataType: "html", //формат данных

      data: {
        phoneNumber: phoneNumber,

        code: code,
      },

      // data: $("#" + ajax_form).serialize(),  // Сеарилизуем объект

      success: function (response) {
        //Данные отправлены успешно

        // result = $.parseJSON(response);
        new Noty({
          type: "notification",
          text: `Код был отправлен на введённый номер телефона.`,
        }).show();
        // $("#result_form").html("Телефон: " + result.phone);
      },

      error: function (response) {
        console.log("error");
        // Данные не отправлены

        // $("#result_form").html("Ошибка. Данные не отправлены.");
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
