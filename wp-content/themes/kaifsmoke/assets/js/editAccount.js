$(document).ready(function () {
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

  if ($(".accManagePage").length) {
    $(".accManagePage")
      .find(".textField_phone")
      .mask("+7 (999) 999-99-99")
      .on("click", function () {
        $(this).get(0).setSelectionRange(4, 4);
      });
  }

  function isValid(id, pat) {
    var value = $(id).val();
    var pattern = new RegExp("^" + pat + "", "i");
    if (pattern.test(value)) {
      return true;
    } else {
      return false;
    }
  }

  $(".saveChanges").on("click", function () {
    let now = new Date();
    let today = new Date(
      now.getFullYear(),
      now.getMonth(),
      now.getDate()
    ).toISOString();
    var firstName = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_firstName"]');
    var lastName = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_lastName"]');
    var email = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_email"]');
    var gender = $(this)
      .parents(".accManagePage_block__editInformation")
      .find(".genderFieldVal");
    var phone = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_phone"]');
    var phoneVerification = $(this)
      .parents(".accManagePage_block__editInformation")
      .find(".phoneVerificationStatus")
      .hasClass("success");
    var birthday = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="birthdaydate"]');
    var password = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_password"]');
    var passwordNew = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_passwordNew"]');
    var passwordNewRepeat = $(this)
      .parents(".accManagePage_block__editInformation")
      .find('input[name="user_passwordNewRepeat"]');
    var errors = [];

    if (!isValid(firstName, "[-a-zA-Z-а-яА-Я]+$")) {
      errors.push("<p>Имя заполнено неправильно!</p>");
      firstName.addClass("error");
    } else {
      firstName.removeClass("error");
    }
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
    if (!phone.val() || !phoneVerification) {
      errors.push("<p>Телефон заполнен неправильно или не подтверждён!</p>");
      phone.addClass("error");
    } else {
      phone.removeClass("error");
    }
    if (!birthday.val()) {
      errors.push("<p>Дата рождения заполнена неправильно!</p>");
      birthday.addClass("error");
    } else if (new Date(birthday.val()).toISOString() > today) {
      errors.push("<p>Дата рождения заполнена неправильно!</p>");
      birthday.addClass("error");
    } else if (!getComingOfAge(birthday.val())) {
      errors.push("<p>Вам нет 18 лет!</p>");
      birthday.addClass("error");
    } else {
      birthday.removeClass("error");
    }
    if (!password.val()) {
      errors.push('<p>Поле "Пароль" должно быть заполнено!</p>');
      password.addClass("error");
    } else {
      password.removeClass("error");
    }
    // if (
    //   passwordNew.val() &&
    //   !isValid(password, "(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{8,}$")
    // ) {
    //   errors.push(
    //     "<p>Пароль должен состоять из цифр и букв латинского алфавита!</p>"
    //   );
    //   passwordNew.addClass("error");
    // } else {
    //   passwordNew.removeClass("error");
    // }
    if (passwordNew.val() && passwordNewRepeat.val() != passwordNew.val()) {
      errors.push(
        '<p>Поле "Повторите новый пароль" и "Новый пароль" должны совпадать!</p>'
      );
      passwordNewRepeat.addClass("error");
    } else {
      passwordNewRepeat.removeClass("error");
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
    if (!errors.length) {
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "POST",
        data: {
          action: "user_update",
          user_data: {
            first_name: firstName.val(),
            last_name: lastName.val(),
            phone: phone.val(),
            phone_confirmed: phoneVerification ? 1 : 0,
            email: email.val(),
            birthday: birthday.val(),
            sex: gender.text().trim(),
            subscribe_to_latest_products: 1,
            password: password.val(),
            newpassword: passwordNew.val(), //можно без новых паролей
            repeatnewpassword: passwordNewRepeat.val(),
          },
        },
        success: (data) => {
          if (data.status === "ok") {
            let userdata = {
              login: phone.val(),
              password: passwordNew.val(),
            };
            if (
              passwordNew.val().length &&
              passwordNew.val() == passwordNewRepeat.val()
            ) {
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
                    window.location.reload();
                  }
                },
              });
            } else {
              window.location.reload();
            }
          } else {
            if (data.error_desc) {
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
});
