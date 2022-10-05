// $(document).ready(function () {
//   $("html").on("click", ".deleteFavoriteProduct", function () {
//     Favorites.remove($(this).attr("data-id"));
//     $(this).parents(".favoritesDropdown_content__product").fadeOut().remove();
//     if (!$.cookie("favorites")) {
//       $(".favoritesDropdown_block").css("display", "none");
//       $(".favoritesDropdown_block__notHaveFavorites").css("display", "flex");
//     }
//   });

//   renderModalFavorites();

//   let products = null;
//   const prom = Favorites.export_data();

//   function setModalFavoritesProd(data = null) {
//     products = data?.out?.products ? data?.out?.products : null;
//     renderModalFavorites(products);
//   }

//   prom.then((data) => setModalFavoritesProd(data));

//   function renderModalFavorites(data = null) {
//     if ($.cookie("favorites")) {
//       $(".navigation_favorites__dropdown")
//         .find(".favoritesDropdown_block__notHaveFavorites")
//         .css("display", "none");
//       $(".navigation_favorites__dropdown")
//         .find(".favoritesDropdown_block")
//         .css("display", "flex");
//       $(".favoritesDropdown_block__content").empty();
//       Object.keys(data).forEach(function (key) {
//         $(".favoritesDropdown_block__content").prepend(`
//         <div class="favoritesDropdown_content__product prod">
//             <div class="favoritesDropdown_product__image">
//                 <img src="${this[key].image_url}" alt="${this[key].name}">
//             </div>
//             <div class="favoritesDropdown_product__description">
//                 <a href="/product/${this[key].slug}" class="favoritesDropdown_productDescription__name">
//                 ${this[key].name}
//                 </a>
//                 <div class="favoritesDropdown_productDescription_quntityPrice">
//                     <div class="favoritesDropdown_product__price">
//                         <span class="favoritesDropdown_product__priceValue" data-value="${this[key].price}">${this[key].price}</span>руб
//                     </div>
//                 </div>
//                 <button class="deleteFavoriteProduct" data-id=${this[key].id}>
//                     Удалить
//                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
//                         <path d="M8 8L16 16M16 8L12 12L8 16" stroke="#D6D6D6" stroke-linecap="round" stroke-linejoin="round" />
//                     </svg>
//                 </button>
//             </div>
//         </div>
//       `);
//       }, data);
//     } else {
//       $(".navigation_favorites__dropdown")
//         .find(".favoritesDropdown_block")
//         .css("display", "none");
//       $(".navigation_favorites__dropdown")
//         .find(".favoritesDropdown_block__notHaveFavorites")
//         .css("display", "flex");
//     }
//   }

//   $(".navigation_favorites").mouseenter(function () {
//     const updated = Favorites.export_data();
//     updated.then((data) => {
//       products = data?.out?.products ? data?.out?.products : null;
//       renderModalFavorites(products);
//     });
//   });
// });
