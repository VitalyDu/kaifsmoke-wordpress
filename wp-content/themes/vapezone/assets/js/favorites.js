$(document).ready(() => {
  if ($.cookie("favorites") === undefined) {
    $.cookie("favorites", "", {path: "/"});
  }

  //add active class if product in favorites

  let products = [];
  let cookieFavorites = $.cookie("favorites");

  Favorites.export_data();

  $("html").on("click", ".deleteFavoriteProduct", function () {
    Favorites.remove($(this).attr("data-id"), $(this).attr("data-productName"));
    $(
      `.favoritesPage_products__product[data-productId="${$(this).attr(
        "data-id"
      )}"]`
    ).fadeOut();
    if (!$.cookie("favorites")) {
      $(".favoritesDropdown_block").css("display", "none");
      $(".favoritesDropdown_block__notHaveFavorites").css("display", "flex");
      $(".mobileMenuFavorites_block")
        .find(".mobileMenuFavorites_block__top")
        .css("display", "none");
      $(".mobileMenuFavorites_block")
        .find(".mobileMenuFavorites_block__products")
        .css("display", "none");
      $(".mobileMenuFavorites_block")
        .find(".mobileMenuFavorites_block__goToFavorites")
        .css("display", "none");
      $(".mobileMenuFavorites_block")
        .find(".mobileMenuFavorites_block__emptyProducts")
        .css("display", "flex");
    }
    $(`div[data-productid="${$(this).attr("data-id")}"]`)
      .find(".product_addToFavorites")
      .removeClass("active");
  });

  $("html").on(
    "click",
    ".favoritesDropdown_block .favorites_clear",
    function () {
      Favorites.clear();
      $(".addToFavoritesIcon, .product_addToFavorites").removeClass("active");
    }
  );

  for (let i = 0; i < $(".product_miniCard").length; i++) {
    products.push($(".product_miniCard").eq(i).attr("data-productid"));
  }

  const filterProducts = products.filter((product) =>
    cookieFavorites.includes(product)
  );

  filterProducts.map((i) => {
    return $(`.product_miniCard[data-productid="${i}"]`)
      .find(".product_addToFavorites")
      .addClass("active");
  });

  $("html").on("click", ".product_addToFavorites", function () {
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      Favorites.remove(
        $(this).parents(".product_miniCard").attr("data-productid"),
        $(this).parents(".product_miniCard").attr("data-productName")
      );
    } else {
      $(this).addClass("active");
      Favorites.add(
        $(this).parents(".product_miniCard").attr("data-productid"),
        $(this).parents(".product_miniCard").attr("data-productName")
      );
    }
  });
});

class Favorites {
  static get() {
    let string = $.cookie("favorites");
    let array = string.split(",");
    if (string === "") {
      array = [];
    }
    return array;
  }

  static import() {
    $.ajax({
      url: AJAXURL,
      dataType: "json",
      method: "POST",
      data: {
        favorites: $.cookie("favorites"),
        action: "write_favs_action",
      },
    });
    return true;
  }

  static export() {
    return new Promise((resolve) => {
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "GET",
        data: {
          action: "read_favs_action",
        },
        success: (data) => {
          if (data.status === "error") {
            return true;
          }
          $.cookie("favorites", data.out.favorites, {path: "/"});
          resolve(data.out.favorites);
        },
        error: () => {
          resolve(null);
        },
      });
    });
  }

  static #write(array) {
    let string = array.join(",");
    $.cookie("favorites", string, {path: "/"});
    if (USER_ID !== "0") {
      this.import();
      this.export_data();
    }
    return true;
  }

  static clear() {
    this.#write([]);
    new Noty({
      type: "notification",
      text: `Список избранного очищен.`,
    }).show();
    return true;
  }

  static add(id, productName) {
    let status = "error";
    let array = this.get();
    if (!array.includes(id)) {
      array.push(id);
      status = "ok";
      this.#write(array);
      new Noty({
        type: "notification",
        text: `Товар "${productName}" был добавлен в избранное.`,
      }).show();
    }
    return status;
  }

  static remove(id, productName) {
    let status = "error";
    let array = this.get();
    if (array.includes(id)) {
      let index = array.indexOf(id);
      array.splice(index, 1);
      status = "ok";
      this.#write(array);
      $(".navigation_favorites__dropdown")
        .find(`.favoritesDropdown_content__product[data-id="${id}"]`)
        .fadeOut()
        .remove();
      new Noty({
        type: "notification",
        text: `Товар "${productName}" был удалён из избранного.`,
      }).show();
    }

    return status;
  }

  static export_data() {
    return new Promise(function (resolve) {
      $.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "GET",
        data: {
          favorites: $.cookie("favorites"),
          action: "get_favs_data_action",
        },
        success: (data) => {
          resolve(data);
          // console.log(data);
          if (!data.out.products) {
            $(".navigation_favorites__dropdown")
              .find(".favoritesDropdown_block")
              .css("display", "none");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__top")
              .css("display", "none");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__products")
              .css("display", "none");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__goToFavorites")
              .css("display", "none");
            $(".navigation_favorites__dropdown")
              .find(".favoritesDropdown_block__notHaveFavorites")
              .css("display", "flex");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__emptyProducts")
              .css("display", "flex");
          } else {
            $(".navigation_favorites__dropdown")
              .find(".favoritesDropdown_block__notHaveFavorites")
              .css("display", "none");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__emptyProducts")
              .css("display", "none");
            $(".navigation_favorites__dropdown")
              .find(".favoritesDropdown_block")
              .css("display", "flex");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__top")
              .css("display", "flex");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__products")
              .css("display", "flex");
            $(".mobileMenuFavorites_block")
              .find(".mobileMenuFavorites_block__goToFavorites")
              .css("display", "flex");
            $(".favoritesDropdown_productsQuantity")
              .find(".allProdQuanVal")
              .text(Object.keys(data.out.products).length);
            $(".favoritesDropdown_block__content").empty();
            Object.keys(data.out.products).forEach(function (key) {
              $(".favoritesDropdown_block__content").prepend(`
        <div class="favoritesDropdown_content__product prod" data-productId="${this[key].id}">
            <div class="favoritesDropdown_product__image">
                <img src="${this[key].image_url}" alt="${this[key].name}">
            </div>
            <div class="favoritesDropdown_product__description">
                <a href="/product/${this[key].slug}" class="favoritesDropdown_productDescription__name">
                ${this[key].name}
                </a>
                <div class="favoritesDropdown_productDescription_quntityPrice">
                    <div class="favoritesDropdown_product__price">
                        <span class="favoritesDropdown_product__priceValue" data-value="${this[key].price}">${this[key].price}</span>руб
                    </div>
                </div>
                <button class="deleteFavoriteProduct" data-id=${this[key].id} data-productName=${this[key].name}>
                    Удалить
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
      `);
            }, data.out.products);
            $(".mobileMenuFavorites_block__top")
              .find(".mobileMenuFavorites_productsQuantity__value")
              .text(Object.keys(data.out.products).length);
            $(".mobileMenuFavorites_block__products").empty();
            Object.keys(data.out.products).forEach(function (key) {
              $(".mobileMenuFavorites_block__products").prepend(`
      <div class="productBlock favoritesDropdown_content__product" data-productId="${this[key].id}">
        <div class="productBlock_product">
          <div class="productBlock_product__img">
              <img src="${this[key].image_url}" alt="${this[key].name}">
          </div>
          <div class="productBlock_product__nameQuantityPrice">
              <div class="product_nameQuantity__name">
                  <a href="/product/${this[key].slug}">
                  ${this[key].name}
                  </a>
              </div>
              <div class="product_nameQuantity__QuantityPrice">
                  <div class="quantityPrice_product__price">
                      <div class="value" data-value="${this[key].price}">
                      ${this[key].price}
                      </div>
                      <div class="currency">
                          руб
                      </div>
                  </div>
              </div>
          </div>
          <button class="deleteFavoriteProduct" data-id=${this[key].id} data-productName=${this[key].name}>
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
          </button>
        </div>
      </div>
      `);
            }, data.out.products);
          }
        },
        error: () => {
          resolve(null);
        },
      });
    });
  }
}
