if ($.cookie("favorites")) {
  $(".favoritesEmpty").css("display", "none");
  $(".favoritesPage").css("display", "flex");
} else {
  $(".favoritesPage").css("display", "none");
  $(".favoritesEmpty").css("display", "flex");
}
$(document).ready(function () {
  $(".clearFavorites").on("click", function () {
    Favorites.clear();
    renderProducts();
    $(".favoritesPage").css("display", "none");
  });

  $("html").on("click", ".deleteProductFromFavorites", function () {
    Favorites.remove($(this).attr("data-id"));
    $(this).parents(".favoritesPage_products__product").fadeOut().remove();
    if (!$.cookie("favorites")) {
      $(".favoritesPage").css("display", "none");
      $(".favoritesEmpty").css("display", "flex");
    }
  });

  let products = null;
  const prom = Favorites.export_data();

  function setProd(data = null) {
    products = data?.out?.products ? data?.out?.products : null;
    renderProducts(products);
  }

  prom.then((data) => setProd(data));

  function renderProducts(data = null) {
    if ($.cookie("favorites")) {
      $(".favoritesLoading").css("display", "none");
      $(".favoritesEmpty").css("display", "none");
      $(".favoritesPage__title").css("display", "flex");
      $(".favoritesPage").css("display", "flex");
      Object.keys(data).forEach(function (key) {
        let productAttributes = "";
        Object.keys(this[key].attributes).forEach(function (key, i) {
          console.log(this[key]);
          if (i < 6) {
            productAttributes += `
                  <div class="product_namePropery__property">
                                      <span class="propertyName">
                                          ${key}:
                                      </span>
                                      <span class="propertyValue">
                                          ${this[key]}
                                      </span>
                                  </div>
                  `;
          }
        }, this[key].attributes);
        let averageRating = this[key].average_rating;
        $(".favoritesPage_block__products").prepend(`
      <div class="favoritesPage_products__product prod" data-productId="${
        this[key].id
      }">
                    <button class="deleteProductFromFavorites prodDelete" data-id=${
                      this[key].id
                    }>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" fill="white"></rect>
                            <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#6B6B63" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <div class="product_image">
                        <img src="${
                          this[key].image_url
                        }" alt="${this[key].name}">
                    </div>
                    <div class="product_nameProperty">
                        <a href="/product/${
                          this[key].slug
                        }" class="product_nameProperty__name">
                            ${this[key].name}
                        </a>
                        <ul class="product_rate">
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="${
                                  averageRating >= 0.5 ? "1D1D1B" : "none"
                                }" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round"></path>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="${
                                  averageRating >= 1.5 ? "1D1D1B" : "none"
                                }" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round"></path>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="${
                                  averageRating >= 2.5 ? "1D1D1B" : "none"
                                }" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round"></path>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="${
                                  averageRating >= 3.5 ? "1D1D1B" : "none"
                                }" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round"></path>
                                </svg>
                            </li>
                            <li>
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="${
                                  averageRating >= 4.5 ? "1D1D1B" : "none"
                                }" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round"></path>
                                </svg>
                            </li>
                        </ul>
                        <div class="product_properties">
                            ${productAttributes}
                        </div>
                    </div>
                    <div class="product_priceQuantityAddToBasket">
                        <div class="product_price">
                            <span class="product_price__value prodPrice" data-value="${
                              this[key].price
                            }">
                                <span class=""></span>${this[key].price}
                            </span>
                            <div class="product_price__currency">
                                руб
                            </div>
                        </div>
                        <div class="product_addToBasket">
                        ${
                          this[key].stock_quantity
                            ? `<button data-productId="${this[key].id}" data-productName="${this[key].name}" class="btn s primary addToCardBtn">Забронировать</button>`
                            : `<a href="/product/${this[key].slug}" class="secondary btn s">Посмотреть</a>`
                        }
                        </div>
                    </div>
                </div>
      `);
      }, data);
    } else {
      $(".favoritesLoading").css("display", "none");
      $(".favoritesPage").css("display", "none");
      $(".favoritesPage__title").css("display", "none");
      $(".favoritesEmpty").css("display", "flex");
    }
  }
});
