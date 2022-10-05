$(document).ready(function () {
  $("html").on("click", ".addToCartNoAuth_btn", function () {
    new Noty({
      type: "notification",
      text: `Для бронирования товара необходимо авторизоваться!`,
    }).show();
  });

  if (USER_ID) {
    let productsData = null;
    let prom = Cart.get_data();
    prom.then((data) => setModalCartProd(data));

    $("html").on("click", ".addToCardBtn", function () {
      var productId = $(this).attr("data-productId");
      var productName = $(this).attr("data-productName");
      $(this)
        .after(
          `<a href="/reservation" class="btn tertiary active m goToCart_btn" data-productId="${productId}" data-productName="${productName}">Список бронирования</a>`
        )
        .hide()
        .fadeIn();
      $(this).hide().remove();
      addToCart(productName, productId);
    });

    $("body").on("click", ".cartProduct_delete", function () {
      let productId = $(this).attr("data-productId");
      let productName = $(this).attr("data-productName");
      let productIdArray = [];
      productIdArray.push(productId);
      removeFromCart(productName, productIdArray);
      $(`.goToCart_btn[data-productId="${productId}"]`)
        .after(
          `<button class="btn tertiary m addToCardBtn" data-productId="${productId}" data-productName="${productName}">Забронировать</button>`
        )
        .hide()
        .fadeIn();
      $(`.goToCart_btn[data-productId="${productId}"]`).hide().remove();
    });

    $("body").on("click", ".basket_clear", function () {
      clearCart();
      if ($(".goToCart_btn").length) {
        let name = null;
        let id = null;
        for (var i = 0; i <= $(".goToCart_btn").length; i++) {
          name = $(".goToCart_btn").eq(i).attr("data-productname");
          id = $(".goToCart_btn").eq(i).attr("data-productid");
          $(".goToCart_btn")
            .eq(i)
            .after(
              `<button class="btn tertiary m addToCardBtn" data-productId="${id}" data-productName="${name}">Забронировать</button>`
            )
            .hide()
            .fadeIn();
          $(".goToCart_btn").eq(i).hide().remove();
        }
        for (var i = 0; i <= $(".goToCart_btn").length; i++) {
          name = $(".goToCart_btn").eq(i).attr("data-productname");
          id = $(".goToCart_btn").eq(i).attr("data-productid");
          $(".goToCart_btn")
            .eq(i)
            .after(
              `<button class="btn tertiary m addToCardBtn" data-productId="${id}" data-productName="${name}">Забронировать</button>`
            )
            .hide()
            .fadeIn();
          $(".goToCart_btn").eq(i).hide().remove();
        }
      }
      $(".navigation_basket__dropdown")
        .find(".basketDropdown_block")
        .css("display", "none");
      $(".mobileMenuBasket_block")
        .find(".mobileMenuBasket_block__top")
        .css("display", "none");
      $(".mobileMenuBasket_block")
        .find(".mobileMenuBasket_block__products")
        .css("display", "none");
      $(".mobileMenuBasket_block")
        .find(".mobileMenuBasket_block__buttons")
        .css("display", "none");
      $(".navigation_basket__dropdown")
        .find(".basketDropdown_block__notHaveProducts")
        .css("display", "flex");
      $(".mobileMenuBasket_block")
        .find(".mobileMenuBasket_block__emptyProducts")
        .css("display", "flex");
    });

    function addToCart(productName, productId) {
      Cart.add(productName, productId, 1, updateCartModal);
    }

    function removeFromCart(productName, productId) {
      Cart.remove(productName, productId, updateCartModal, rerenderCart);
    }

    function clearCart() {
      Cart.clear(updateCartModal);
    }

    function updateCartModal() {
      prom = Cart.get_data();
      prom.then((data) => setModalCartProd(data));
    }

    function setModalCartProd(data = null) {
      productsData = data?.out?.products ? data?.out?.products : null;
      if (!productsData) {
        $(".navigation_basket__dropdown")
          .find(".basketDropdown_block")
          .css("display", "none");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__top")
          .css("display", "none");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__products")
          .css("display", "none");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__buttons")
          .css("display", "none");
        $(".navigation_basket__dropdown")
          .find(".basketDropdown_block__notHaveProducts")
          .css("display", "flex");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__emptyProducts")
          .css("display", "flex");
      } else {
        $(".navigation_basket__dropdown")
          .find(".basketDropdown_block__notHaveProducts")
          .css("display", "none");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__emptyProducts")
          .css("display", "none");
        $(".navigation_basket__dropdown")
          .find(".basketDropdown_block")
          .css("display", "flex");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__top")
          .css("display", "flex");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__products")
          .css("display", "flex");
        $(".mobileMenuBasket_block")
          .find(".mobileMenuBasket_block__buttons")
          .css("display", "flex");
        renderModalCart(productsData);
      }
    }

    function renderModalCart(data = null) {
      $(".basketDropdown_productsQuantity")
        .find(".allProdQuanVal")
        .text(Object.keys(data).length);
      if (Object.keys(data).length > 0) {
        $(".mobileHeader_block__burger")
          .find(".circle")
          .css("display", "flex")
          .text(Object.keys(data).length);
      } else {
        $(".mobileHeader_block__burger")
          .find(".circle")
          .css("display", "none")
          .text(0);
      }
      $(".basketDropdown_block__content").empty();
      Object.keys(data).forEach(function (key) {
        $(".basketDropdown_block__content").prepend(`
            <div class="basketDropdown_content__product productFromCart cartProduct" data-productId="${
              this[key].id
            }">
              <div class="basketDropdown_product__image">
                  <img src="${
                    this[key].image_url
                      ? this[key].image_url
                      : "/wp-content/themes/vapezone/assets/images/placeholder-image.png"
                  }" alt="${this[key].name}">
              </div>
              <div class="basketDropdown_product__description">
                  <a href="/product/${
                    this[key].slug
                  }" class="basketDropdown_productDescription__name">
                    ${this[key].name}
                  </a>
                  <div class="basketDropdown_productDescription_quntityPrice">
                      <div class="basketDropdown_product__price">
                          <span class="basketDropdown_product__priceValue prodPrice" data-value="${
                            this[key].price
                          }">${this[key].price}</span><span class="prodCurrency">руб/шт</span>
                      </div>
                  </div>
                  <button class="deleteBasketProduct cartProduct_delete" data-productId="${
                    this[key].id
                  }" data-productName='${this[key].name}'>
                      Удалить
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                  </button>
              </div>
            </div>
          `);
      }, data);

      $(".mobileMenuBasket_block__top")
        .find(".mobileMenuBasket_productsQuantity__value")
        .text(Object.keys(data).length);
      $(".mobileMenuBasket_block__products").empty();
      Object.keys(data).forEach(function (key) {
        $(".mobileMenuBasket_block__products").prepend(`
            <div class="productBlock productFromCart cartProduct" data-productId="${
              this[key].id
            }">
              <div class="productBlock_product">
                  <div class="productBlock_product__img">
                      <img src="${
                        this[key].image_url
                          ? this[key].image_url
                          : "/wp-content/themes/vapezone/assets/images/placeholder-image.png"
                      }" alt="${this[key].name}">
                  </div>
                  <div class="productBlock_product__nameQuantityPrice">
                      <div class="product_nameQuantity__name">
                          <a href="/product/${this[key].slug}">
                            ${this[key].name}
                          </a>
                      </div>
                      <div class="product_nameQuantity__QuantityPrice">
                          <div class="quantityPrice_product__price">
                              <div class="value" data-value="${
                                this[key].price
                              }">
                                ${this[key].price}
                              </div>
                              <div class="currency">
                                  руб/шт
                              </div>
                          </div>
                      </div>
                  </div>
                  <button class="deleteBasketProduct cartProduct_delete" data-productId="${
                    this[key].id
                  }" data-productName='${this[key].name}'>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </button>
              </div>
            </div>
          `);
      }, data);
    }

    if ($(".cartPage_block").length) {
      if ($(".cartPage_block__customer").length) {
        $(".cartPage_block__customer")
          .find('input[name="customer_phone"]')
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
      let productsData = null;
      let mapList = null;
      let prom = $.cookie("multisklad")
        ? Cart.get_cart_products($.cookie("multisklad"))
        : Cart.get_data();
      prom.then((data) => setCart(data));

      renderMapForCart();
      function renderMapForCart() {
        let map = Cart.get_shops();
        map.then((data) => {
          mapList = data?.out?.shops ? data?.out?.shops : null;
          $(".cartPage_map .shops-table").empty();
          mapList.map((item) => {
            $.cookie("multisklad") == item.id_multisklad
              ? $(".order_total .choosenShop .value").text(item.address)
              : null;
            $(".cartPage_map .shops-table").append(`
                <div class="table-item ${
                  $.cookie("multisklad") == item.id_multisklad ? "active" : ""
                }" data-idMultiSklad="${
              item.id_multisklad
            }" data-shopAddress="${item.address}">
                  <div class="address">
                      <span><b class="address_label">${item.address}</b></span>
                      <span><svg width="14" height="14" viewBox="0 0 14 14" fill="${setMetroColor(
                        item.metro_color
                      )}" xmlns="http://www.w3.org/2000/svg">
                              <path d="M13.5 7C13.5 10.5899 10.5899 13.5 7 13.5C3.41015 13.5 0.5 10.5899 0.5 7C0.5 3.41015 3.41015 0.5 7 0.5C10.5899 0.5 13.5 3.41015 13.5 7Z" fill="#E9420D" stroke="#E9420D" stroke-width="0.8"></path>
                              <path d="M5.41054 5.26242C5.29062 4.88154 4.9651 4.92329 4.74254 5.10069C4.19429 5.57546 3.48042 6.572 3.9373 8.00694C4.31993 9.22259 5.91207 10 5.91207 10H4.05606C4.05606 10 3.09773 8.99303 3.01206 7.80869C2.90356 6.31131 3.54323 5.36694 4.28566 4.75131C4.98239 4.17217 5.64486 4 5.64486 4L7 8.1625L8.35514 4C8.35514 4 9.01761 4.17217 9.71434 4.75131C10.4568 5.36694 11.0964 6.31131 10.9879 7.80869C10.9023 8.99303 9.94394 10 9.94394 10H8.08793C8.08793 10 9.67994 9.22259 10.0627 8.00694C10.5196 6.57216 9.80571 5.57563 9.25746 5.10069C9.03473 4.92329 8.70921 4.88154 8.58946 5.26242C8.17827 6.47808 7.00017 9.96117 7.00017 9.96117C7.00017 9.96117 5.82207 6.47691 5.41088 5.26242H5.41054Z" fill="white"></path>
                          </svg>
                          ${item.metro}</span>
                  </div>
      
      
                  <div class="info">
      
                      <span> <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M7.00049 3.50012V7.11123H9.50049M13.5005 7.11123C13.5005 10.7011 10.5903 13.6112 7.00049 13.6112C3.41064 13.6112 0.500488 10.7011 0.500488 7.11123C0.500488 3.52138 3.41064 0.611233 7.00049 0.611233C10.5903 0.611233 13.5005 3.52138 13.5005 7.11123Z" stroke="#1D1D1B" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>${item.schedule}</span>
      
                      <span><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5.10618 2.94505L3.12628 0.965149L1.64126 2.45017C1.14611 2.94532 -0.833787 4.92522 3.62099 9.37999C8.07576 13.8348 10.0557 11.8549 10.551 11.3596L12.0357 9.87479L10.0559 7.8948C9.56118 7.39959 9.56095 7.39982 9.0662 7.89457C7.58128 9.37949 3.62148 5.4197 5.1064 3.93477C5.60115 3.44002 5.60138 3.4398 5.10618 2.94505Z" stroke="#1D1D1B" stroke-width="0.8" stroke-linejoin="round"></path>
                          </svg><a href="tel:${item.phone}">${
              item.phone
            }</a></span>
                  </div>
      
                  <div class="avalible">
                      ${shopProductsStatus(item.products_status)}
                      <span>${item.products_status}</span>
                  </div>
      
      
                  <span class="shop-select">
                      <a class="chooseShop" data-idMultiSklad="${
                        item.id_multisklad
                      }" data-shopAddress="${item.address}">Выбрать</a>
                  </span>
                </div>
                `);
          });
        });
      }

      function shopProductsStatus(status) {
        if (status == "Не все товары в наличии") {
          return `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="10" fill="#2074C1"/>
            <path d="M8.70456 5.2H11.3086L10.8746 11.598H9.13856L8.70456 5.2ZM10.0066 15.112C9.6239 15.112 9.3019 14.9907 9.04056 14.748C8.78856 14.496 8.66256 14.1927 8.66256 13.838C8.66256 13.4833 8.78856 13.1893 9.04056 12.956C9.29256 12.7133 9.61456 12.592 10.0066 12.592C10.3986 12.592 10.7206 12.7133 10.9726 12.956C11.2246 13.1893 11.3506 13.4833 11.3506 13.838C11.3506 14.1927 11.2199 14.496 10.9586 14.748C10.7066 14.9907 10.3892 15.112 10.0066 15.112Z" fill="white"/>
            </svg>
            `;
        } else if (status == "Товаров нет в наличии") {
          return `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="10" fill="#E9420D"/>
            <path d="M7 7L13 13M13 7L10 10L7 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            `;
        } else if (status == "Все товары в наличии") {
          return `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="10" fill="#31A337"/>
            <path d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z" fill="white"/>
            </svg>
            `;
        }
      }

      function setMetroColor(color) {
        if (color == "blue") {
          return "#2074C1";
        } else if (color == "orange") {
          return "#EF7D00";
        } else if (color == "red") {
          return "#E9420D";
        } else if (color == "green") {
          return "#31A337";
        } else if (color == "violet") {
          return "#984492";
        }
      }

      $("body").on("click", ".chooseShop", function () {
        let multisklad = $(this).attr("data-idMultiSklad");
        let shopAddress = $(this).attr("data-shopAddress");
        $.cookie("multisklad", multisklad);
        $.cookie("shop_address", shopAddress);
        prom = Cart.get_cart_products(multisklad, shopAddress);
        prom.then((data) => setCart(data));
        $(".table-item").removeClass("active");
        $(this).parents(".table-item").addClass("active");
      });

      function setCart(data = null) {
        productsData = data?.out?.products ? data?.out?.products : null;
        if (!productsData) {
          $(".cartLoading").css("display", "none");
          $(".answerPage").css("display", "flex");
          $(".cartPage").css("display", "none");
          $(".cartPage__title").css("display", "none");
          $(".cartPage_map").css("display", "none");
        } else {
          $(".cartLoading").css("display", "none");
          $(".cartPage").css("display", "flex");
          $(".cartPage__title").css("display", "flex");
          $(".cartPage_map").css("display", "flex");
          $(".answerPage").css("display", "none");
          renderCart(productsData);
        }
      }
      var t = "";
      $("body").on("click", ".product_quantityPlus", function () {
        let product_name = $(this)
          .siblings(".product_quantity")
          .attr("data-productName");
        let product_id = parseInt(
          $(this).siblings(".product_quantity").attr("data-productId")
        );
        let product_quantity = parseInt(
          $(this).siblings(".product_quantity").val()
        );
        let max_quantity = parseInt(
          $(this).siblings(".product_quantity").attr("max")
        );
        let multisklad = $(this)
          .siblings(".product_quantity")
          .attr("data-idMultiSklad");
        let shopAddress = $(this)
          .siblings(".product_quantity")
          .attr("data-shopAddress");
        product_quantity++;
        $(this).siblings(".product_quantity").val(product_quantity);
        clearTimeout(t);
        t = setTimeout(function () {
          Cart.set_product_quantity(
            product_name,
            product_id,
            product_quantity,
            multisklad,
            shopAddress,
            rerenderCart,
            renderMapForCart
          );
          productTotalPrice(product_quantity, $(this));
        }, 1000);
      });

      $("body").on("click", ".product_quantityMinus", function () {
        let product_name = $(this)
          .siblings(".product_quantity")
          .attr("data-productName");
        let product_id = parseInt(
          $(this).siblings(".product_quantity").attr("data-productId")
        );
        let product_quantity = parseInt(
          $(this).siblings(".product_quantity").val()
        );
        let max_quantity = parseInt(
          $(this).siblings(".product_quantity").attr("max")
        );
        let multisklad = $(this)
          .siblings(".product_quantity")
          .attr("data-idMultiSklad");
        let shopAddress = $(this)
          .siblings(".product_quantity")
          .attr("data-shopAddress");
        if (product_quantity > 0) {
          product_quantity--;
          $(this).siblings(".product_quantity").val(product_quantity);
          clearTimeout(t);
          t = setTimeout(function () {
            Cart.set_product_quantity(
              product_name,
              product_id,
              product_quantity,
              multisklad,
              shopAddress,
              rerenderCart,
              renderMapForCart
            );
            productTotalPrice(product_quantity, $(this));
          }, 1000);
        }
        // }
      });

      $("body").on("input", ".product_quantity", function () {
        let product_name = $(this).attr("data-productName");
        let product_id = parseInt($(this).attr("data-productId"));
        let product_quantity = parseInt($(this).val());
        let max_quantity = parseInt($(this).attr("max"));
        let multisklad = $(this).attr("data-idMultiSklad");
        // let shopAddress = $(this).attr("data-shopAddress");
        // if (product_quantity > max_quantity) {
        //   $(this).val(max_quantity);
        // } else if (product_quantity < 1) {
        //   product_quantity = 1;
        //   $(this).val(product_quantity);
        // }
        if (product_quantity < 1) {
          product_quantity = 1;
          $(this).val(product_quantity);
        }
        clearTimeout(t);
        t = setTimeout(function () {
          Cart.set_product_quantity(
            product_name,
            product_id,
            product_quantity,
            multisklad,
            shopAddress,
            rerenderCart,
            renderMapForCart
          );
          productTotalPrice(product_quantity, $(this));
        }, 1000);
      });

      $("body").on("change", ".product_quantity", function () {
        let product_name = $(this).attr("data-productName");
        let product_id = parseInt($(this).attr("data-productId"));
        let product_quantity = parseInt($(this).val());
        let max_quantity = parseInt($(this).attr("max"));
        let multisklad = $(this).attr("data-idMultiSklad");
        let shopAddress = $(this).attr("data-shopAddress");
        // if (product_quantity > max_quantity) {
        //   $(this).val(max_quantity);
        // } else if (product_quantity < 1) {
        //   product_quantity = 1;
        //   $(this).val(product_quantity);
        // }
        // if (!product_quantity) {
        //   product_quantity = 1;
        //   $(this).val(product_quantity);
        // }

        if (product_quantity < 1) {
          product_quantity = 1;
          $(this).val(product_quantity);
        }
        clearTimeout(t);
        t = setTimeout(function () {
          Cart.set_product_quantity(
            product_name,
            product_id,
            product_quantity,
            multisklad,
            shopAddress,
            rerenderCart,
            renderMapForCart
          );
          productTotalPrice(product_quantity, $(this));
        }, 1000);
      });

      function productTotalPrice(quantity, element) {
        let product_priceField = element
          .parents(".products_content__product")
          .find(".product_summ__value");
        let product_priceForOne = parseInt(
          product_priceField.attr("data-priceForOne")
        );
        let product_priceTotal = product_priceForOne * quantity;
        product_priceField.text(
          product_priceTotal
            ? product_priceTotal + " ₽"
            : product_priceForOne + " ₽"
        );
        product_priceField.attr("data-totalPrice", product_priceTotal);
      }

      function rerenderCart(multisklad, shopAddress) {
        $(`.table-item[data-idMultiSklad="${multisklad}"]`).addClass("active");
        prom = Cart.get_cart_products(multisklad);
        prom.then((data) => setCart(data));
      }

      function renderCart(data = null) {
        $(".cartPage_block__products .products_content").empty();
        $(".cartPage_block__order .totalReserveProducts .value").empty();
        $(".cartPage_block__order .choosenShop .value").empty();
        $(".cartPage_block__order .deliveryTime .value").empty();
        $(".cartPage_block__order .totalSumm .value").empty();
        let productSumm = 0;
        let totalSumm = 0;
        let totalProducts = 0;
        // if ($.cookie("multisklad")) {
        //   if (
        //     $(".noReserveProductsWithExpectation").hasClass("active") ||
        //     $(".reserveProductsWithExpectation").hasClass("active")
        //   ) {
        //     $(".cartPage_block__order").css("display", "flex");
        //   }
        // }
        Object.keys(data).forEach(function (key) {
          if ($.cookie("multisklad")) {
            $(".cartPage_block__products .products_content").append(`
                <div class="products_content__product cartProduct" data-productId="${
                  this[key].id
                }" data-stockStatus="${
              this[key].in_chosen_shop_stock_status
            }" data-multiskladId="${productDataMultisklad(
              this[key].in_chosen_shop_stock_status
            )}" data-quantity="${
              this[key].in_cart_quantity
            }" data-totalPrice="${
              this[key].in_cart_quantity * this[key].price
            }">
                  <a href="/product/${this[key].slug}" class="product_image">
                      <img src="${
                        this[key].image_url
                          ? this[key].image_url
                          : "/wp-content/themes/vapezone/assets/images/placeholder-image.png"
                      }" alt="${this[key].name}" />
                  </a>
                  <div class="product_namePrice">
                      <a href="/product/${
                        this[key].slug
                      }" class="product_name">${this[key].name}</a>
                      <span class="product_price">${
                        this[key].price
                      } ₽ <span class="light small">/ шт</span></span>
                  </div>
                  <div class="product_quantityWrapper">
                      <span class="product_quantityMinus">-</span>
                      <input type="number" maxlength="4" value="${
                        this[key].in_cart_quantity
                      }" max="${
              this[key].stock_quantity > this[key].in_chosen_shop_stock
                ? this[key].stock_quantity
                : this[key].in_chosen_shop_stock
            }" data-productId="${this[key].id}" data-productName="${
              this[key].name
            }" data-idMultiSklad="${$.cookie(
              "multisklad"
            )}" data-shopAddress="${$.cookie(
              "shop_address"
            )}" class="product_quantity">
                      <span class="product_quantityPlus">+</span>
                  </div>
                  <div class="product_summ">
                      <span class="product_summ__label">Сумма</span>
                      <span class="product_summ__value" data-priceForOne="${
                        this[key].price
                      }" data-totalPrice="${
              this[key].in_cart_quantity * this[key].price
            }">${this[key].in_cart_quantity * this[key].price} ₽</span>
                  </div>
                  <div class="product_stock">
                      <span class="product_stock__label">Наличие в выбранном магазине</span>
                      <span class="product_stock__value">
                          ${productStatus(
                            this[key].in_chosen_shop_stock_status
                          )}
                          ${
                            this[key].in_chosen_shop_stock_status == 1
                              ? `
                            <div class="stock_value__description">
                                          <span class="description_label">
                                              ?
                                          </span>
                                          <div class="description_content">
                                            Товар можно будет забрать в выбранном магазине через 2 дня.
                                          </div>
                                      </div>
                            `
                              : ""
                          }
                      </span>
                  </div>
                  <button class="product_delete cartProduct_delete" data-productId="${
                    this[key].id
                  }" data-productName='${this[key].name}'>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </button>
                </div>
            `);
          } else {
            $(".cartPage_block__products .products_content").prepend(`
                <div class="products_content__product cartProduct" data-productId="${
                  this[key].id
                }" data-totalPrice="${
              this[key].in_cart_quantity * this[key].price
            }">
                  <a href="/product/${this[key].slug}" class="product_image">
                      <img src=${
                        this[key].image_url
                          ? this[key].image_url
                          : "/wp-content/themes/vapezone/assets/images/placeholder-image.png"
                      }" alt="${this[key].name}" />
                  </a>
                  <div class="product_namePrice">
                      <a href="/product/${
                        this[key].slug
                      }" class="product_name">${this[key].name}</a>
                      <span class="product_price">${
                        this[key].price
                      } ₽ <span class="light small">/ шт</span></span>
                  </div>
                  <div class="product_quantityWrapper">
                      <span class="product_quantityMinus">-</span>
                      <input type="number" maxlength="4" value="${
                        this[key].in_cart_quantity
                      }" max="${this[key].stock_quantity}" data-productId="${
              this[key].id
            }" data-productName="${this[key].name}" class="product_quantity">
                      <span class="product_quantityPlus">+</span>
                  </div>
                  <div class="product_summ">
                      <span class="product_summ__label">Сумма</span>
                      <span class="product_summ__value" data-priceForOne="${
                        this[key].price
                      }" data-totalPrice="${
              this[key].in_cart_quantity * this[key].price
            }">${this[key].in_cart_quantity * this[key].price} ₽</span>
                  </div>
                  <div class="product_stock">
                      <span class="product_stock__label">Наличие в выбранном магазине</span>
                      <span class="product_stock__value">
                          Выберите магазин
                      </span>
                  </div>
                  <button class="product_delete cartProduct_delete" data-productId="${
                    this[key].id
                  }" data-productName='${this[key].name}'>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </button>
                </div>
            `);
          }
          if (
            this[key].in_chosen_shop_stock_status == 1 ||
            this[key].in_chosen_shop_stock_status == 3
          ) {
            productSumm = this[key].in_cart_quantity * this[key].price;
            totalSumm += productSumm;
            totalProducts++;
          }
        }, data);
        let address = $(".shops-table .table-item.active")
          .find(".address_label")
          .text();
        $(".order_total .choosenShop .value").text(address);
        $(".cartPage_block__order .totalSumm .value").text(totalSumm + "₽");
        $(".cartPage_block__order .totalReserveProducts .value").text(
          totalProducts
        );
        if (
          !$('.cartProduct[data-stockStatus="3"]').length &&
          !$('.cartProduct[data-stockStatus="1"]').length
        ) {
          $(".cartPage_block__customer").css("display", "none");
          $(".cartPage_block__order").css("display", "none");
        } else {
          $(".cartPage_block__customer").css("display", "flex");
          $(".cartPage_block__order").css("display", "flex");
        }
        if (!$('.cartProduct[data-stockStatus="1"]').length) {
          $(".deliveryTime").css("display", "none");
        } else {
          $(".deliveryTime").css("display", "flex");
          $(".deliveryTime .value").text("2 Дня");
        }
        if (
          $('.cartProduct[data-stockStatus="3"]').length &&
          !$('.cartProduct[data-stockStatus="1"]').length
        ) {
          $(".cartPage_block__order").css("display", "flex");
        } else if ($('.cartProduct[data-stockStatus="1"]').length) {
          $(".cartPage_block__order .deliveryTime .value").text("2 Дня");
        }
      }

      function productStatus(in_chosen_shop_stock_status) {
        if (in_chosen_shop_stock_status == 0) {
          return `
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="10" cy="10" r="10" fill="#E9420D"/>
                <path d="M7 7L13 13M13 7L10 10L7 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>        
              Отсутствует
            `;
        } else if (in_chosen_shop_stock_status == 1) {
          return `
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="10" cy="10" r="10" fill="#2074C1" />
                  <path d="M14 10L14.7071 10.7071C15.0976 10.3166 15.0976 9.68342 14.7071 9.29289L14 10ZM10.2929 12.2929C9.90237 12.6834 9.90237 13.3166 10.2929 13.7071C10.6834 14.0976 11.3166 14.0976 11.7071 13.7071L10.2929 12.2929ZM11.7071 6.29289C11.3166 5.90237 10.6834 5.90237 10.2929 6.29289C9.90237 6.68342 9.90237 7.31658 10.2929 7.70711L11.7071 6.29289ZM13.2929 9.29289L10.2929 12.2929L11.7071 13.7071L14.7071 10.7071L13.2929 9.29289ZM14.7071 9.29289L11.7071 6.29289L10.2929 7.70711L13.2929 10.7071L14.7071 9.29289Z" fill="white" />
                  <path d="M10 10L10.7071 10.7071C11.0976 10.3166 11.0976 9.68342 10.7071 9.29289L10 10ZM6.29289 12.2929C5.90237 12.6834 5.90237 13.3166 6.29289 13.7071C6.68342 14.0976 7.31658 14.0976 7.70711 13.7071L6.29289 12.2929ZM7.70711 6.29289C7.31658 5.90237 6.68342 5.90237 6.29289 6.29289C5.90237 6.68342 5.90237 7.31658 6.29289 7.70711L7.70711 6.29289ZM9.29289 9.29289L6.29289 12.2929L7.70711 13.7071L10.7071 10.7071L9.29289 9.29289ZM10.7071 9.29289L7.70711 6.29289L6.29289 7.70711L9.29289 10.7071L10.7071 9.29289Z" fill="white" />
              </svg>
              Доставят за 2 дня
            `;
        } else if (in_chosen_shop_stock_status == 3) {
          return `
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="10" cy="10" r="10" fill="#31A337"/>
                <path d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z" fill="white"/>
              </svg>        
              В наличии
            `;
        }
      }

      function productDataMultisklad(in_chosen_shop_stock_status) {
        if (in_chosen_shop_stock_status == 0) {
          return "";
        } else if (in_chosen_shop_stock_status == 1) {
          return "Основной склад";
        } else if (in_chosen_shop_stock_status == 3) {
          return $.cookie("multisklad");
        }
      }

      $(".order_button").on("click", function () {
        let userName = $(".cartPage_block__customer").find(
          'input[name="customer_name"]'
        );
        let userPhone = $(".cartPage_block__customer").find(
          'input[name="customer_phone"]'
        );
        let productsNotAvailable = $('.cartProduct[data-stockStatus="0"]');
        let productsWithDelivery = $('.cartProduct[data-stockStatus="1"]');
        let productsInStock = $('.cartProduct[data-stockStatus="3"]');
        let errors = [];
        let productsForRemove = [];
        let productsForOrder = [];
        if (userName.length && !isValid(userName, "[-a-zA-Z-а-яА-Я]+$")) {
          errors.push("<p>Имя заполнено неправильно!</p>");
          userName.addClass("error");
        } else {
          userName.removeClass("error");
        }
        if (userPhone.length && !userPhone.val()) {
          errors.push("<p>Телефон заполнен неправильно!</p>");
          userPhone.addClass("error");
        } else {
          userPhone.removeClass("error");
        }
        if (!$.cookie("multisklad")) {
          errors.push("<p>Выберите магазин!</p>");
        }
        if (productsNotAvailable.length) {
          errors.push(
            "<p>Не все товары есть в наличии, для оформления бронирования выберите магазин, где данный товары есть в наличии, либо удалите из списка бронирования.</p>"
          );
          errors.map((item) => {
            new Noty({
              type: "error",
              text: `${item}`,
            }).show();
          });
        } else {
          if (errors.length) {
            errors.map((item) => {
              new Noty({
                type: "error",
                text: `${item}`,
              }).show();
            });
          } else {
            productsWithDelivery.map((item) => {
              productsForOrder.push({
                id: productsWithDelivery.eq(item).attr("data-productId"),
                quantity: productsWithDelivery.eq(item).attr("data-quantity"),
                multisklad_id: productsWithDelivery
                  .eq(item)
                  .attr("data-multiskladId"),
              });
            });
            productsInStock.map((item) => {
              productsForOrder.push({
                id: productsInStock.eq(item).attr("data-productId"),
                quantity: productsInStock.eq(item).attr("data-quantity"),
                multisklad_id: productsInStock
                  .eq(item)
                  .attr("data-multiskladId"),
              });
            });
            Cart.create_cart_order(
              userName.val(),
              userPhone.val(),
              $.cookie("multisklad"),
              productsForOrder
            );
          }
        }
      });
    }
  }
});

class Cart {
  static clear(updateCartModal) {
    $(".loader").css("display", "flex").hide().fadeIn();
    $.ajax({
      url: AJAXURL,
      dataType: "json",
      method: "POST",
      data: {
        action: "clear_cart",
      },
      success: async (data) => {
        $(".loader").fadeOut();
        updateCartModal();
        if (data.status === "ok") {
          new Noty({
            type: "notification",
            text: `Список бронирования был очищен.`,
          }).show();
        } else {
          new Noty({
            type: "notification",
            text: `Список бронирования не был очищен.`,
          }).show();
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
    return true;
  }

  static remove(productName, id, updateCartModal = null, rerenderCart = null) {
    $(".loader").css("display", "flex").hide().fadeIn();
    $.ajax({
      url: AJAXURL,
      dataType: "json",
      method: "POST",
      data: {
        products_id: id,
        action: "remove_cart",
      },
      success: async (data) => {
        $(".loader").fadeOut();
        if (updateCartModal) {
          updateCartModal();
        }
        if (rerenderCart) {
          rerenderCart();
        }
        if (id.length <= 1) {
          if (data.status === "ok") {
            new Noty({
              type: "notification",
              text: `Товар "${productName}" был удалён из списка бронирования.`,
            }).show();
            id.map((item) => {
              return $(`.cartProduct[data-productId="${item}"]`)
                .hide()
                .remove();
            });
          } else {
            new Noty({
              type: "notification",
              text: `Товар "${productName}" не был удалён из списка бронирования.`,
            }).show();
          }
        } else {
          if (data.status === "ok") {
            new Noty({
              type: "notification",
              text: `Товары были удалёны из списка бронирования.`,
            }).show();
            id.map((item) => {
              return $(`.cartProduct[data-productId="${item}"]`)
                .hide()
                .remove();
            });
          } else {
            new Noty({
              type: "notification",
              text: `Товары не были удалёны из списка бронирования.`,
            }).show();
          }
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
    return true;
  }

  static create_cart_order(userName, userPhone, multisklad, products) {
    $(".loader").css("display", "flex").hide().fadeIn();
    $.ajax({
      url: AJAXURL,
      dataType: "json",
      method: "POST",
      data: {
        username: userName,
        phone: userPhone,
        multisklad_id: multisklad,
        products: products,
        action: "create_cart_order",
      },
      success: async (data) => {
        $(".loader").fadeOut();
        if (data.status === "ok") {
          this.clear();
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
    return true;
  }

  static add(productName, id, quantity, updateCartModal = null) {
    $(".loader").css("display", "flex").hide().fadeIn();
    $.ajax({
      url: AJAXURL,
      dataType: "json",
      method: "POST",
      data: {
        product_id: id,
        product_quantity: quantity,
        action: "update_cart",
      },
      success: async (data) => {
        $(".loader").fadeOut();
        if (updateCartModal) {
          updateCartModal();
        }
        if (data.status === "ok") {
          new Noty({
            type: "notification",
            text: `Товар "${productName}" был добавлен в список бронирования.`,
          }).show();
        } else {
          new Noty({
            type: "notification",
            text: `Товар "${productName}" не был добавлен в список бронирования.`,
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
    return true;
  }

  static set_product_quantity(
    productName,
    id,
    quantity,
    multisklad,
    shopAddress,
    rerenderCart,
    renderMapForCart
  ) {
    $.ajax({
      url: AJAXURL,
      dataType: "json",
      method: "POST",
      data: {
        action: "set_product_quantity",
        product_id: id,
        product_quantity: quantity,
      },
      success: async (data) => {
        rerenderCart(multisklad, shopAddress);
        renderMapForCart();
        // updateCartModal();
        if (data.status === "ok") {
          new Noty({
            type: "notification",
            text: `Количество "${productName}" в списке бронирования было изменено на ${quantity}.`,
          }).show();
        } else {
          new Noty({
            type: "notification",
            text: `Количество "${productName}" в списке бронирования не было изменено на ${quantity}.`,
          }).show();
        }
      },
      error: () => {
        new Noty({
          type: "notification",
          text: `Произошла ошибка, попробуйте позже.`,
        }).show();
      },
    });
    return true;
  }

  static get_data() {
    return new Promise(function (resolve) {
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "GET",
        data: {
          action: "get_cart_data",
        },
        success: (data) => {
          if (data.status === "ok") {
            resolve(data);
          }
        },
        error: () => {
          resolve(null);
        },
      });
    });
  }

  static get_cart_products(shop_id = null, shop_address = null) {
    return new Promise(function (resolve) {
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "GET",
        data: {
          action: "get_cart_products",
          shop_id: $.cookie("multisklad") ? $.cookie("multisklad") : shop_id,
        },
        success: (data) => {
          if (data.status === "ok") {
            if (shop_address) {
              new Noty({
                type: "notification",
                text: `Пунктом самовывоза был выбран магазин по адресу: ${shop_address}`,
              }).show();
            }
            resolve(data);
          }
        },
        error: () => {
          resolve(null);
        },
      });
    });
  }

  static get_shops() {
    return new Promise(function (resolve) {
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "GET",
        data: {
          action: "get_cart_shops",
        },
        success: (data) => {
          if (data.status === "ok") {
            resolve(data);
          }
        },
        error: () => {
          resolve(null);
        },
      });
    });
  }
}
