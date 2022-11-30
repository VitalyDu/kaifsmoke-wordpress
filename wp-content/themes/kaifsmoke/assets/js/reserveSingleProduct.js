$(document).ready(function () {
  if ($(".reserveModal")) {
    $("html").on("click", ".reserveProductInShop", function () {
      var productId = this.getAttribute("data-productId");
      var address = this.getAttribute("data-address");
      var multisklad = this.getAttribute("data-multisklad");
      var quantity = this.getAttribute("data-quantity");
      console.log(productId);
      console.log(address);
      console.log(multisklad);
      console.log(quantity);
      $(".reserveModal_content__address").text(address);
      $(".overlay").fadeIn();
      $(".reserveModal").css("display", "flex").hide().fadeIn();
      $(".reserveModal").attr("data-productid", productId);
      $(".reserveModal").attr("data-address", address);
      $(".reserveModal").attr("data-multisklad", multisklad);
      $(".reserveModal").attr("data-quantity", quantity);
      $(".reserveModal").find(".reserveQuantity").attr("data-max", quantity);
    });
    $(".reserveModal_button__cancel, .reserveModal_close").on(
      "click",
      function () {
        $(".overlay").fadeOut();
        $(".reserveModal").fadeOut();
      }
    );

    $("body").on("click", ".reserveQuantity_plus", function () {
      let product_quantity = parseInt(
        $(this).siblings(".reserveQuantity").val()
      );
      let max_quantity = parseInt(
        $(this).siblings(".reserveQuantity").attr("data-max")
      );
      if (product_quantity < max_quantity) {
        product_quantity++;
        $(this).siblings(".reserveQuantity").val(product_quantity);
      }
    });

    $("body").on("click", ".reserveQuantity_minus", function () {
      let product_quantity = parseInt(
        $(this).siblings(".reserveQuantity").val()
      );
      if (product_quantity > 1 && product_quantity != 1) {
        product_quantity--;
        $(this).siblings(".reserveQuantity").val(product_quantity);
      }
    });

    $("body").on("input", ".reserveQuantity", function () {
      let product_quantity = parseInt($(this).val());
      let max_quantity = parseInt($(this).attr("data-max"));
      if (product_quantity > max_quantity) {
        $(this).val(max_quantity);
      } else if (product_quantity < 1) {
        product_quantity = 1;
        $(this).val(product_quantity);
      }
    });

    $("body").on("change", ".reserveQuantity", function () {
      let product_quantity = parseInt($(this).val());
      let max_quantity = parseInt($(this).attr("data-max"));
      if (product_quantity > max_quantity) {
        $(this).val(max_quantity);
      } else if (product_quantity < 1) {
        product_quantity = 1;
        $(this).val(product_quantity);
      }
      if (!product_quantity) {
        product_quantity = 1;
        $(this).val(product_quantity);
      }
    });

    $(".reserveModal")
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

    $("html").on("click", ".reserveModal_button__unAuth", function () {
      new Noty({
        type: "notification",
        text: `Для бронирования товара необходимо авторизоваться!`,
      }).show();
    });

    $("html").on("click", ".reserveModal_button__reserve", function () {
      var product_id = $(this).parents(".reserveModal").attr("data-productid");
      var address = $(this).parents(".reserveModal").attr("data-address");
      var shop_multisklad_id = $(this)
        .parents(".reserveModal")
        .attr("data-multisklad");
      var firstName = $(this)
        .parents(".reserveModal")
        .find("input[name='user_firstName']");
      var phone = $(this)
        .parents(".reserveModal")
        .find("input[name='user_phone']");
      var quantity = $(this)
        .parents(".reserveModal")
        .find("input.reserveQuantity");
      var errors = [];
      var products = [];
      var product = {
        id: product_id,
        quantity: quantity.val(),
        multisklad_id: shop_multisklad_id,
      };
      products.push(product);
      if (firstName.length && !isValid(firstName, "[-a-zA-Z-а-яА-Я]+$")) {
        errors.push("<p>Имя заполнено неправильно!</p>");
        firstName.addClass("error");
      } else {
        firstName.removeClass("error");
      }
      if (phone.length && !phone.val()) {
        errors.push("<p>Телефон заполнен неправильно!</p>");
        phone.addClass("error");
      } else {
        phone.removeClass("error");
      }
      if (!errors.length) {
        $(".loader").css("display", "flex").hide().fadeIn();
        $.ajax({
          url: AJAXURL,
          dataType: "json",
          method: "POST",
          data: {
            username: firstName.val(),
            phone: phone.val(),
            multisklad_id: shop_multisklad_id,
            products: products,
            action: "create_cart_order",
          },
          success: async (data) => {
            if (data.status === "ok") {
              window.location.replace(
                `/my-account/view-order/${JSON.stringify(data.out.order_id)}/`
              );
            } else {
              new Noty({
                type: "notification",
                text: `Произошла оишбка, попробуйте позже`,
              }).show();
            }
          },
          error: () => {
            $(".loader").fadeOut();
            new Noty({
              type: "notification",
              text: `Произошла ошибка, попробуйте позже.`,
            }).show();
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
