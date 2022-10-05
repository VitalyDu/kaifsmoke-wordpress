$(document).ready(function () {
  if ($(".signUpPage").length) {
    function getComingOfAge(birthday) {
      let now = new Date(); //Текущя дата
      let today = new Date(now.getFullYear(), now.getMonth(), now.getDate()); //Текущя дата без времени
      let dob = new Date(birthday); //Дата рождения
      let dobnow = new Date(today.getFullYear(), dob.getMonth(), dob.getDate()); //ДР в текущем году
      let age; //Возраст
      let comingOfAge = false;
      //Возраст = текущий год - год рождения
      age = today.getFullYear() - dob.getFullYear();
      //Если ДР в этом году ещё предстоит, то вычитаем из age один год
      if (today < dobnow) {
        age = age - 1;
      }
      if (age >= 18) {
        comingOfAge = true;
      }
      return comingOfAge;
    }
    $(".signUpPage_block")
      .find('input[name="user_phone"]')
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
    function isValid(id, pat) {
      var value = $(id).val();
      var pattern = new RegExp("^" + pat + "", "i");
      if (pattern.test(value)) {
        return true;
      } else {
        return false;
      }
    }
    // $('input[name="birthday"]').change(function () {
    //   let now = new Date();
    //   var today = new Date(
    //     now.getFullYear(),
    //     now.getMonth(),
    //     now.getDate()
    //   ).toISOString();
    //   var value = new Date($(this).val()).toISOString();
    //   if (value.length >= 10 && value > today) {
    //     $(this).val($(this).attr('max'));
    //   }
    // });
    $(".signUp_btn").on("click", function () {
      let now = new Date();
      let today = new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate()
      ).toISOString();
      let error = [];
      let firstname = $(this)
        .parents(".user_form")
        .find('input[name="user_firstname"]');
      let lastname = $(this)
        .parents(".user_form")
        .find('input[name="user_lastname"]');
      let email = $(this)
        .parents(".user_form")
        .find('input[name="user_email"]');
      let sex = $(this).parents(".user_form").find(".genderFieldVal");
      let phone = $(this)
        .parents(".user_form")
        .find('input[name="user_phone"]');
      var phoneVerification = $(this)
        .parents(".user_form")
        .find(".phoneVerificationStatus")
        .hasClass("success");
      let birthday = $(this)
        .parents(".user_form")
        .find('input[name="user_birthday"]');
      let password = $(this)
        .parents(".user_form")
        .find('input[name="user_password"]');
      let passwordRepeat = $(this)
        .parents(".user_form")
        .find('input[name="user_passwordRepeat"]');

      let dataProcessing = $(this)
        .parents(".user_form")
        .find('input[name="dataProcessing"]');

      if (!isValid(firstname, "[-a-zA-Z-а-яА-Я]+$")) {
        error.push("<p>Имя заполнено неправильно!</p>");
        firstname.addClass("error");
      } else {
        firstname.removeClass("error");
      }
      if (lastname.val() && !isValid(lastname, "[a-zA-Zа-яА-Я]+$")) {
        error.push("<p>Фамилия заполнена неправильно!</p>");
        lastname.addClass("error");
      } else {
        lastname.removeClass("error");
      }
      if (
        !isValid(
          email,
          "[a-zA-Zа-яА-ЯёЁ_\\d][-a-zA-Zа-яА-ЯёЁ0-9_\\.\\d]*\\@[a-zA-Zа-яА-ЯёЁ\\d][-a-zA-Zа-яА-ЯёЁ\\.\\d]*\\.[a-zA-Zа-яА-Я]{2,6}$"
        )
      ) {
        error.push("<p>Email заполнен неправильно!</p>");
        email.addClass("error");
      } else {
        email.removeClass("error");
      }
      if (!phone.val() || !phoneVerification) {
        error.push("<p>Телефон заполнен неправильно или не подтверждён!</p>");
        phone.addClass("error");
      } else {
        phone.removeClass("error");
      }
      if (!birthday.val()) {
        error.push("<p>Дата рождения заполнена неправильно!</p>");
        birthday.addClass("error");
      } else if (new Date(birthday.val()).toISOString() > today) {
        error.push("<p>Дата рождения заполнена неправильно!</p>");
        birthday.addClass("error");
      } else if (!getComingOfAge(birthday.val())) {
        error.push("<p>Вам нет 18 лет!</p>");
        birthday.addClass("error");
      } else {
        birthday.removeClass("error");
      }
      if (!password.val()) {
        error.push("<p>Пароль заполнен неправильно!</p>");
        password.addClass("error");
      } else {
        password.removeClass("error");
      }
      if (!isValid(password, "(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{4,}$")) {
        error.push(
          "<p>Длина пароля должна составлять минимум 4 символа! Пароль должен состоять из цифр и букв латинского алфавита!</p>"
        );
        password.addClass("error");
      } else {
        password.removeClass("error");
      }
      if (passwordRepeat.val() !== password.val()) {
        error.push(
          "<p>Поля 'Пароль' и 'Повторите пароль' должны совпадать!</p>"
        );
        passwordRepeat.addClass("error");
      } else {
        passwordRepeat.removeClass("error");
      }
      if (!dataProcessing.prop("checked")) {
        error.push(
          "<p>Подтвердите согласие на обработку персональных данных!</p>"
        );
        dataProcessing.addClass("error");
        dataProcessing.siblings("span").addClass("error");
      } else {
        dataProcessing.removeClass("error");
        dataProcessing.siblings("span").removeClass("error");
      }
      if (!error.length) {
        let userdata = {
          firstname: firstname.val(),
          lastname: lastname.val(),
          phone: phone.val(),
          birthday: birthday.val(),
          password: password.val(),
          passwordRepeat: passwordRepeat.val(),
          email: email.val(),
          sex: sex.text(),
          favorites: $.cookie("favorites"),
        };
        $(".loader").css("display", "flex").hide().fadeIn();
        $.ajax({
          url: AJAXURL,
          method: "POST",
          dataType: "json",
          data: Object.assign(
            {
              action: "register_action",
            },
            userdata
          ),
          success: async (data) => {
            $(".loader").fadeOut();
            if (data.status === "ok") {
              await Favorites.import();
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
          error: () => {
            $(".loader").fadeOut();
            new Noty({
              type: "error",
              text: `Произошла ошибка, попробуйте позже.`,
            }).show();
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
