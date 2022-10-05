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
  if ($(".reviews_block")) {
    $(".reviewWrite_form .content_send__form").on("click", function () {
      var error = [];
      var productId = $(this)
        .parents(".reviewWrite_form")
        .attr("data-productId");
      var name = $(this)
        .parents(".reviewWrite_form")
        .find('input[name="review_firstName"]');
      var rating = $(this)
        .parents(".reviewWrite_form")
        .find('input[type="radio"]:checked');
      var message = $(this)
        .parents(".reviewWrite_form")
        .find('textarea[name="review_message"]');
      if (!isValid(name, "[-a-zA-Z-а-яА-Я]+$")) {
        error.push("<p>Имя заполнено неправильно!</p>");
        name.addClass("error");
      } else {
        error.push();
        name.removeClass("error");
      }
      if (!rating) {
        error.push("<p>Выберите оценку!</p>");
        $(".starRating").addClass("error");
      } else {
        error.push();
        $(".starRating").removeClass("error");
      }
      if (!message.val()) {
        error.push("<p>Напишите отзыв!</p>");
        message.addClass("error");
      } else {
        error.push();
        message.removeClass("error");
      }
      if (!error.length) {
        $(".loader").css("display", "flex").hide().fadeIn();
        $.ajax({
          url: "https://kaifsmoke.ru/wp-admin/admin-ajax.php?",
          dataType: "json",
          method: "POST",
          data: {
            action: "send_review",
            data: {
              id: productId,
              name: name.val(),
              rating: rating.val(),
              message: message.val(),
            },
          },
          success: (data) => {
            $(".loader").css("display", "flex").hide().fadeOut();
            if (data.status === "ok") {
              $(".content_reviewSend").css("display", "flex").hide().fadeIn();
              new Noty({
                type: "alert",
                text: "Отзыв был отправлен!",
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
