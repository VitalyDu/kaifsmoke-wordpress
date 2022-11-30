// Import jQuery module (npm i jquery)
import $ from "jquery";
window.jQuery = $;
window.$ = $;
let owl_carousel = require("owl.carousel");
window.fn = owl_carousel;
require("jquery-ui/ui/widgets/slider");
// import Noty from "noty";

$(document).ready(function () {
  $(".mobileHeader_block__burger").on("click", function () {
    $(this).toggleClass("active");
    $(this).find(".burger_icon").toggleClass("active");
    if (!$(this).hasClass("active")) {
      $(
        ".mobileMenu, .mobileMenuUser, .mobileMenuSignIn, .mobileMenuFavorites, .mobileMenuBasket, .mobileMenu_category__subcategoryList"
      )
        .fadeOut(0)
        .removeClass("active");
      $(".mobileMenu").removeClass("lOne");
      $(".mobileMenu").removeClass("lTwo");
    } else {
      $(".mobileMenu").css("display", "flex");
    }
  });

  $(window).resize(function () {
    if ($(window).width() >= 1170) {
      $(".mobileHeader_block__burger").removeClass("active");
      $(".mobileHeader_block__burger")
        .find(".burger_icon")
        .removeClass("active");
      $(
        ".mobileMenu, .mobileMenuUser, .mobileMenuSignIn, .mobileMenuFavorites, .mobileMenuBasket, .mobileMenu_category__subcategoryList"
      )
        .fadeOut(0)
        .removeClass("active");
      $(".mobileMenu").removeClass("lOne");
      $(".mobileMenu").removeClass("lTwo");
    }
  });

  $(".mobileMenu_category .dropdown_arrow").on("click", function () {
    $(this)
      .siblings(".mobileMenu_category__subcategoryList")
      .css("display", "flex");
  });

  $(".mobileMenuCatalog_subcategoryList__back").on("click", function () {
    $(this).parents(".mobileMenu_category__subcategoryList").fadeOut(0);
  });

  $(".mobileMenu_subcategoryList__subcategory .dropdown_arrow").on(
    "click",
    function () {
      $(this).siblings(".mobileMenu_subsubcategoryList").css("display", "flex");
    }
  );

  $(".mobileMenuCatalog_subsubcategoryList__back").on("click", function () {
    $(this).parents(".mobileMenu_subsubcategoryList").fadeOut(0);
  });
  // // Функция ymaps.ready() будет вызвана, когда
  // // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
  // ymaps.ready(init);

  // function init() {
  //   var myMap = new ymaps.Map(
  //       "map",
  //       {
  //         center: [59.944856, 30.346849],
  //         zoom: 10,
  //       },
  //       {
  //         searchControlProvider: "yandex#search",
  //       }
  //     ),
  //     objectManager = new ymaps.ObjectManager({
  //       // Чтобы метки начали кластеризоваться, выставляем опцию.
  //       clusterize: true,
  //       // ObjectManager принимает те же опции, что и кластеризатор.
  //       gridSize: 32,
  //     });

  //   // Чтобы задать опции одиночным объектам и кластерам,
  //   // обратимся к дочерним коллекциям ObjectManager.
  //   objectManager.objects.options.set("preset", "islands#orangeDotIcon");
  //   objectManager.clusters.options.set("preset", "islands#orangeClusterIcons");
  //   myMap.geoObjects.add(objectManager);

  //   $.ajax({
  //     url: "https://kaifsmoke.ru/wp-json/controllers/v1/shops/",
  //   }).done(function (data) {
  //     const readyConst = JSON.stringify(data);
  //     objectManager.add(readyConst);
  //   });
  // }

  //Основная часть

  // $(".sort").click(function () {
  //   if ($(".sort-menu").css("display") == "block") {
  //     $(".sort-menu").css("display", "none");
  //   } else {
  //     $(".sort-menu").css("display", "block");
  //   }
  // });
  // задать начальный текст левого span
  // $("#rub-left").text($("#slider-range").slider("values", 0) + "р.");
  // задать начальный текст правого span
  // $("#rub-right").text($("#slider-range").slider("values", 1) + "р.");

  // $('.toastBtn').on('click', function(){
  //   new Noty({
  //     type: "success",
  //     text: `Товар был добавлен в корзину.`,
  //   }).show();
  // });
  // $(".addToCart_btn").on("click", function () {
  //   $(this).hide();
  //   $(this)
  //     .parents(".product_buttons")
  //     .prepend(
  //       '<a href="#" class="product_buttons__goToCart">Список бронирования</a>'
  //     )
  //     .hide()
  //     .fadeIn();
  //   var productName = $(this)
  //     .parents(".product_miniCard")
  //     .attr("data-productName");
  //   new Noty({
  //     type: "success",
  //     text: `Товар "${productName}" был добавлен в корзину.`,
  //   }).show();
  // });
  // $(".product_buttons__addInBasket").on("click", function () {
  //   $(this).hide();
  //   $(this)
  //     .parents(".product_buttons")
  //     .prepend(
  //       '<a href="#" class="product_buttons__goToCart">Список бронирования</a>'
  //     )
  //     .hide()
  //     .fadeIn();
  //   var productName = $(this)
  //     .parents(".productsBlock_carousel__item")
  //     .find(".product_name a")
  //     .text();
  //   new Noty({
  //     text: `Товар "${productName}" был добавлен в корзину.`,
  //   }).show();
  // });
  // Noty.overrideDefaults({
  //   type: 'info',
  //   layout: "topRight",
  //   theme: "relax",
  //   timeout: 3000,
  // });
  // $(".toastBtn").on("click", function () {
  //   // alert('hi');
  //   var text = $(this).text();

  //   new Noty({
  //     text: `${text} Some notification text ${text} Some notification text ${text} Some notification text ${text} Some notification text ${text} Some notification text`,
  //   }).show();
  // });
  if ($(".filterWrapper").length) {
    var min = parseInt($(".range_price").attr("data-min"));
    var max = parseInt($(".range_price").attr("data-max"));
    var minVal = parseInt($(".range_price").attr("data-minval"));
    var maxVal = parseInt($(".range_price").attr("data-maxval"));
    function rangePrice(
      min = parseInt($(".range_price").attr("data-min")),
      max = parseInt($(".range_price").attr("data-max")),
      minVal = 0,
      maxVal = 0
    ) {
      $(".range_price").slider({
        range: true,
        min: min,
        max: max,
        values: [minVal ? minVal : min, maxVal ? maxVal : max],
        step: 1,
        slide: function (event, ui) {
          // $("#rub-left").text(ui.values[0] + "р."); // текст левого span
          // $("#rub-right").text(ui.values[1] + "р."); // текст правого span
          $(".range_inputs .range_inputs__min").val(ui.values[0]);
          $(".range_inputs .range_inputs__max").val(ui.values[1]);
          if (ui.values[1] != max || ui.values[0] != min) {
            $(".range_price").addClass("active");
          } else {
            $(".range_price").removeClass("active");
          }
          activeFiltres();
          if (ui.handleIndex === 0) {
            // потянули левый ползунок - переместим левый span
            // $("#rub-left").css("margin-left", ui.handle.style.left);
            $(".range_inputs .range_inputs__min").change();
          } else {
            $(".range_inputs .range_inputs__max").change();
            // потянули правый ползунок - переместим правый span
            // $("#rub-right").css("margin-left", ui.handle.style.left);
          }
        },
      });
    }
    rangePrice(min, max, minVal, maxVal);
    $(".range_inputs .range_inputs__min").on("input", function () {
      if ($(this).val() > parseInt($(this).attr("max"))) {
        $(this).val(parseInt($(this).attr("max")));
      }
      if ($(this).val() != 0) {
        $(".range_price").addClass("active");
      } else {
        $(".range_price").removeClass("active");
      }
      rangePrice(
        min,
        max,
        $(this).val() > parseInt($(this).attr("max"))
          ? parseInt($(this).attr("max"))
          : $(this).val(),
        $(".range_inputs .range_inputs__max").val()
          ? $(".range_inputs .range_inputs__max").val()
          : null
      );
    });
    $(".range_inputs .range_inputs__max").on("input", function () {
      if ($(this).val() > parseInt($(this).attr("max"))) {
        $(this).val(parseInt($(this).attr("max")));
      }
      if ($(this).val() != parseInt($(this).attr("max"))) {
        $(".range_price").addClass("active");
      } else {
        $(".range_price").removeClass("active");
      }
      rangePrice(
        min,
        max,
        $(".range_inputs .range_inputs__min").val()
          ? $(".range_inputs .range_inputs__min").val()
          : null,
        $(this).val() > parseInt($(this).attr("max"))
          ? parseInt($(this).attr("max"))
          : $(this).val()
      );
    });
    $(".filter_status__action").on("click", function () {
      $(".filter_content input:checkbox").prop("checked", false);
      $(".filter_content input:radio").prop("checked", false);
      //   $('.filter_content input[type="number"]').val("");
      $(".range_inputs .range_inputs__min").val(0);
      $(".range_inputs .range_inputs__max").val(
        parseInt($(".range_inputs .range_inputs__max").attr("max"))
      );
      $(".range_inputs .range_inputs__min").change();
      $(".range_inputs .range_inputs__max").change();
      $(".range_price").removeClass("active");
      activeFiltres();
    });

    function activeFiltres() {
      if (
        ($(".filter_content").has(":checked").length &&
          $(".filter_content input[type='checkbox']:checked").length > 0) ||
        $(".range_price").hasClass("active")
      ) {
        $(".filter_status .filter_status__empty").hide();
        $(".filter_status .filter_status__chosen").fadeIn();
        $(".filter_status .filter_status__chosen .chosen_val").text(
          $(".range_price").hasClass("active")
            ? parseInt(
                $(".filter_content input[type='checkbox']:checked").length
              ) + 1
            : parseInt(
                $(".filter_content input[type='checkbox']:checked").length
              )
        );
        $(".filter_status .filter_status__action").fadeIn();
      } else {
        $(".filter_status .filter_status__empty").fadeIn();
        $(".filter_status .filter_status__chosen").hide();
        $(".filter_status .filter_status__action").hide();
      }
    }

    $(
      ".filter_content input[type='checkbox'], .filter_content input[type='number']"
    ).change(function () {
      activeFiltres();
    });

    $(".mobileFilterWrapper").on("click", function () {
      $(".filterWrapper").fadeIn();
    });

    $(".filter_close").on("click", function () {
      $(this).parents(".filterWrapper").fadeOut();
    });

    $(".filter_content__item .item_label").on("click", function () {
      // $(".filter_content__item .item_dropdown")
      //   .removeClass("active")
      //   .slideUp(200);
      $(this).toggleClass("active");
      $(this).siblings(".item_dropdown").toggle(200);
      // if ($(this).hasClass("active")) {
      //   $(this).siblings(".item_dropdown").slideUp(200);
      //   $(this).removeClass("active");
      // } else {
      //   $(this).siblings(".item_dropdown").slideDown(200);
      //   $(this).addClass("active");
      // }
    });
  }

  if ($(".reserveProductInShop").length) {
    $(".reserveProductInShop").on("click", function () {
      $(".overlay").fadeIn();
      $(".reserveModal").css("display", "flex").hide().fadeIn();
    });
  }

  if ($(".accManagePage").length) {
    $(".editProfile").on("click", function () {
      $(this).parents(".accManagePage_block__information").hide();
      $(this)
        .parents(".accManagePage_block__information")
        .siblings(".accManagePage_block__editInformation")
        .css("display", "flex")
        .hide()
        .fadeIn();
    });
    $(".cancelChanges").on("click", function () {
      $(this).parents(".accManagePage_block__editInformation").hide();
      $(this)
        .parents(".accManagePage_block__editInformation")
        .siblings(".accManagePage_block__information")
        .css("display", "flex")
        .hide()
        .fadeIn();
    });
  }

  // $(".accManagePage input").on("click", function () {
  //   // $(this).parents('.accManagePage').fadeOut();
  //   // $(this).parents('.accManagePage').siblings('.editProfilePage').fadeIn();
  //   if (!$(this).parents(".accManagePage_block").hasClass("editing")) {
  //     $(this).parents(".accManagePage_block").addClass("editing");
  //     $(".hideStar").show();
  //     $(this)
  //       .parents(".accManagePage_block")
  //       .find(".accManagePage_block__top, .accManagePage_editLogoutBtn")
  //       .hide();
  //     $(this)
  //       .parents(".accManagePage_block")
  //       .find(
  //         ".profileInf_block__nameLastName, .editProfilePage_block__password, .editProfilePage_block__saveChanges"
  //       )
  //       .css("display", "flex")
  //       .hide()
  //       .fadeIn();
  //   }
  // });

  // $(".accManagePage_editLogOutBtn__editProfile").on("click", function () {
  //   $(this).parents(".accManagePage_block").addClass("editing");
  //   $(".hideStar").show();
  //   $(this)
  //     .parents(".accManagePage_block")
  //     .find(
  //       ".accManagePage_block__top, .accManagePage_editLogoutBtn, .phoneField"
  //     )
  //     .hide();
  //   $(this)
  //     .parents(".accManagePage_block")
  //     .find(
  //       ".profileInf_block__nameLastName, .editProfilePage_block__password, .editProfilePage_block__saveChanges, .phoneFieldEdit"
  //     )
  //     .css("display", "flex")
  //     .hide()
  //     .fadeIn();

  //   // $(this).parents('.accManagePage').siblings('.editProfilePage').fadeIn();
  // });

  // $(".cancelChanges").on("click", function () {
  //   $(this).parents(".accManagePage_block").removeClass("editing");
  //   $(".hideStar").hide();
  //   $(this)
  //     .parents(".accManagePage_block")
  //     .find(
  //       ".profileInf_block__nameLastName, .editProfilePage_block__password, .editProfilePage_block__saveChanges, .phoneFieldEdit, .phone-verification"
  //     )
  //     .hide();
  //   $(this)
  //     .parents(".accManagePage_block")
  //     .find(
  //       ".accManagePage_block__top, .accManagePage_editLogoutBtn, .phoneField, .accManagePage_block__confirmedPhone"
  //     )
  //     .css("display", "flex")
  //     .hide()
  //     .fadeIn();
  //   // $(this).parents('.editProfilePage').fadeOut();
  //   // $(this).parents('.editProfilePage').siblings('.accManagePage').fadeIn();
  // });

  // if ($(window).width() < "1170") {
  //   $(".inBasketButton").on("click", function () {
  //     $(".mobileHeader_block__burger")
  //       .find(".circle")
  //       .css("display", "flex")
  //       .hide(1)
  //       .fadeIn(100);
  //   });
  // }

  // anchor links

  $("a.upPage").click(function () {
    var elementClick = $(this).attr("href");
    var destination = $(elementClick).offset().top;
    jQuery("html:not(:animated),body:not(:animated)").animate(
      {
        scrollTop: destination,
      },
      800
    );
    return false;
  });
  $('a[href^="#"]').click(function () {
    let anchor = $(this).attr("href");
    $("html, body").animate(
      {
        scrollTop: $(anchor).offset().top,
      },
      600
    );
  });

  // /anchor links

  // fixed buttons (Up and Chat)

  // var sectionPaddingTop = $("section").eq(0).css("padding-top");
  $(document).scroll(function () {
    var wt = $(window).scrollTop();
    var wh = $(window).scrollTop() + $(window).height();
    var ft = $("footer").offset().top;
    var fh = $("footer").offset().top + $("footer").outerHeight();
    if (wt > 200) {
      $(".fixedButtons a.upPage").css({
        opacity: 1,
        "z-index": 50,
      });
    } else {
      $(".fixedButtons a.upPage").css({
        opacity: 0,
        "z-index": -10,
      });
    }
    if (wh > ft + 50 && wt < fh) {
      $(".fixedButtons a").addClass("orange");
    } else {
      $(".fixedButtons a").removeClass("orange");
    }
  });

  // /fixed buttons (Up and Chat)

  // fixed desktop header
  // var allProductsPadding = $(".allProducts").css("padding-top");
  // var desktopFilterHeight = $(".filter").outerHeight();
  // var allProdPlusFilterHeightPadding =
  //   parseInt(allProductsPadding) + parseInt(desktopFilterHeight);
  // var scrollPos = 0;
  // if ($(window).width() >= "1170") {
  //   $(document).scroll(function () {
  //     var wt = $(window).scrollTop();
  //     var bhh =
  //       $(".beforeHeader").offset().top + $(".beforeHeader").outerHeight();
  //     if (wt > bhh) {
  //       var headerHeight = $("header").outerHeight();
  //       var paddingTopFirstSection =
  //         parseInt(sectionPaddingTop) + parseInt(headerHeight);
  //       $("header").addClass("fixed");
  //       $("section").eq(0).css("padding-top", `${paddingTopFirstSection}px`);
  //     }
  //     if (wt <= bhh) {
  //       $("header").removeClass("fixed");
  //       var pt = $("section").eq(0).css("padding-top");
  //       var ptt = parseInt(pt) - parseInt($("header").outerHeight());
  //       $("section").eq(0).css("padding-top", ``);
  //     }
  //   });
  // }
  // if ($(window).width() >= "1170" && $(document).find(".filter").length > 0) {
  //   $(document).scroll(function () {
  //     var wt = $(window).scrollTop();
  //     var allProductsTop = $(".allProducts .container").offset().top;
  //     var allProductsHeight =
  //       $(".allProducts .container").offset().top +
  //       $(".allProducts .container").outerHeight();
  //     var catalogCategoryHeight =
  //       $(".catalogCategory").offset().top +
  //       $(".catalogCategory").outerHeight();
  //     var st = $(this).scrollTop();
  //     if (wt >= allProductsTop && wt <= allProductsHeight) {
  //       if (st > scrollPos) {
  //         $(".allProducts").css(
  //           "padding-top",
  //           `${allProdPlusFilterHeightPadding}px`
  //         );
  //         $(".filter").removeClass("active").addClass("deactive");
  //       } else {
  //         $(".allProducts").css(
  //           "padding-top",
  //           `${allProdPlusFilterHeightPadding}px`
  //         );
  //         $(".filter").removeClass("deactive").addClass("active");
  //       }
  //       scrollPos = st;
  //     } else if (
  //       wt >
  //         catalogCategoryHeight -
  //           ($("header").outerHeight() + $(".beforeHeader").outerHeight()) &&
  //       wt < allProductsTop &&
  //       ($(".filter").hasClass("active") || $(".filter").hasClass("active"))
  //     ) {
  //       if (st > scrollPos) {
  //         $(".allProducts").css(
  //           "padding-top",
  //           `${allProdPlusFilterHeightPadding}px`
  //         );
  //         $(".filter").removeClass("active").addClass("deactive");
  //       } else {
  //         $(".allProducts").css(
  //           "padding-top",
  //           `${allProdPlusFilterHeightPadding}px`
  //         );
  //         $(".filter").removeClass("deactive").addClass("active");
  //       }
  //       scrollPos = st;
  //     } else if (wt <= catalogCategoryHeight) {
  //       $(".filter").removeClass("active");
  //       $(".allProducts").css("padding-top", ``);
  //     } else {
  //       $(".filter").removeClass("deactive active");
  //       $(".allProducts").css("padding-top", ``);
  //     }
  //   });
  // }

  // /fixed desktop header

  // callBack modal

  // $(".mobileHeaderShowCallBackModal").on("click", function () {
  //   $(".mobileHeader .burger_icon").removeClass("active");
  //   $(".mobileMenu").removeClass("active lOne lTwo").fadeOut(200);
  //   $(".mobileMenuBasket, .mobileMenuFavorites, .mobileMenuSignIn")
  //     .removeClass("active")
  //     .fadeOut(200);
  //   $("body").removeClass("modal-active");
  //   $(".callBack_modal")
  //     .addClass("callBack_modal__active")
  //     .css("display", "flex")
  //     .hide()
  //     .fadeIn();
  // });
  // $(".callBack_showModal").on("click", function () {
  //   $(".callBack_modal")
  //     .addClass("callBack_modal__active")
  //     .css("display", "flex")
  //     .hide()
  //     .fadeIn();
  // });

  // $(document).mouseup(function (e) {
  //   if ($(".callBack_modal").hasClass("callBack_modal__active")) {
  //     var modalBlock = $(".callBack_modal__active").find(".callBack");
  //     if (!modalBlock.is(e.target) && modalBlock.has(e.target).length === 0) {
  //       $(".callBack_modal__active")
  //         .removeClass("callBack_modal__active")
  //         .fadeOut();
  //     }
  //   }
  // });
  // $(".callBack_close").on("click", function () {
  //   $(this)
  //     .parents(".callBack_modal")
  //     .removeClass(".callBack_modal__active")
  //     .fadeOut();
  // });
  // // /callBack modal

  // // chooseGeo modal

  // $(".chooseGeo").on("click", function () {
  //   $(".chooseRegion_modal")
  //     .addClass("chooseRegion_modal__active")
  //     .css("display", "flex")
  //     .hide()
  //     .fadeIn();
  // });
  // $(document).mouseup(function (e) {
  //   if ($(".chooseRegion_modal").hasClass("chooseRegion_modal__active")) {
  //     var modalBlock = $(".chooseRegion_modal__active").find(".chooseRegion");
  //     if (!modalBlock.is(e.target) && modalBlock.has(e.target).length === 0) {
  //       $(".chooseRegion_modal__active")
  //         .removeClass("chooseRegion_modal__active")
  //         .fadeOut();
  //     }
  //   }
  // });
  // $(".chooseRegion_close").on("click", function () {
  //   $(this)
  //     .parents(".chooseRegion_modal")
  //     .removeClass(".chooseRegion_modal__active")
  //     .fadeOut();
  // });
  // /chooseGeo modal

  // callBack modal validation

  // $('button.sendForm').on('click', function () {
  // 	var form = $(this).parents('form');
  // 	var name = form.find('.userName');
  // 	var phone = form.find('.userPhone');
  // 	var email = form.find('.userEmail');
  // 	if (name.val() == '') {
  // 		name.addClass('error');
  // 	}
  // 	if (phone.val() == '') {
  // 		phone.addClass('error');
  // 	}
  // 	if (email.val() == '') {
  // 		email.addClass('error');
  // 	}
  // 	if (name.val() != '' && phone.val() != '' && email.val() != '') {
  // 		$(this).parents('.callBack_modal__block').find('.callBack_modal_blockContent').fadeOut(1000);
  // 		setTimeout(function () {
  // 			$('.callBack_modal__block').find('.callBack_modalBlock_send').css('display', 'flex').hide().fadeIn();
  // 		}, 1000);
  // 		setTimeout(function () {
  // 			$('.callBack_modal__block').parents('.callBack_modal').fadeOut();
  // 		}, 5000);
  // 	}
  // });

  // /callBack modal validation

  // reviewForm validation

  // $('button.sendForm').on('click', function () {
  // 	var form = $(this).parents('form');
  // 	var name = form.find('.userName');
  // 	var phone = form.find('.userPhone');
  // 	var email = form.find('.userEmail');
  // 	if (name.val() == '') {
  // 		name.addClass('error');
  // 	}
  // 	if (phone.val() == '') {
  // 		phone.addClass('error');
  // 	}
  // 	if (email.val() == '') {
  // 		email.addClass('error');
  // 	}
  // 	if (name.val() != '' && phone.val() != '' && email.val() != '') {
  // 		$(this).parents('.writeReview_block').find('.writeReview_content').fadeOut(1000);
  // 		setTimeout(function () {
  // 			$('.writeReview_block').find('.formSending').css('display', 'flex').hide().fadeIn();
  // 		}, 1000);
  // 	}
  // });

  // /reviewForm validation

  // $(".signInBtn").on("click", function () {
  //   var login = $(this)
  //     .parents(".navigation_profileDropdown__form")
  //     .find(".phoneSignIn");
  //   var password = $(this)
  //     .parents(".navigation_profileDropdown__form")
  //     .find(".passwordSignIn");
  //   if (login.val() == "") {
  //     login.addClass("error");
  //     $(this)
  //       .parents(".navigation_profileDropdown__form")
  //       .find("span.signInError")
  //       .fadeIn();
  //   }
  //   if (password.val() == "") {
  //     password.addClass("error");
  //     $(this)
  //       .parents(".navigation_profileDropdown__form")
  //       .find("span.signInError")
  //       .fadeIn();
  //   }
  // });
  $(".profileDropdown_form_forgotPassword").on("click", function () {
    $(this).parents(".navigation_profile__dropdown").fadeOut();
    setTimeout(function () {
      $(".navigation_profile__dropdown").css("display", "flex");
    }, 1000);
    $(".forgotPassword_block").css({
      opacity: "1",
      "z-index": "20",
    });
  });
  $(".forgotPassword_block").hover(
    function () {
      $(this).css({
        opacity: "1",
        "z-index": "20",
      });
    },
    function () {
      $(this).css({
        opacity: "0",
        "z-index": "-10",
      });
    }
  );
  // $(".navigation_more").hover(
  //   function () {
  //     $(this)
  //       .find(".navigation_more__dropdown")
  //       .css("display", "flex")
  //       .hide(1)
  //       .fadeIn(100)
  //       .addClass("active");
  //   },
  //   function () {
  //     $(this)
  //       .find(".navigation_more__dropdown")
  //       .fadeOut()
  //       .removeClass("active");
  //   }
  // );
  // $(".navigation_favorites").hover(
  //   function () {
  //     $(this)
  //       .find(".navigation_favorites__dropdown")
  //       .css("display", "flex")
  //       .hide(1)
  //       .fadeIn(100)
  //       .addClass("active");
  //   },
  //   function () {
  //     $(this)
  //       .find(".navigation_favorites__dropdown")
  //       .fadeOut()
  //       .removeClass("active");
  //   }
  // );
  // $(".navigation_profile").hover(
  //   function () {
  //     $(this)
  //       .find(".navigation_profile__dropdown")
  //       .css("display", "flex")
  //       .hide(1)
  //       .fadeIn(100)
  //       .addClass("active");
  //   },
  //   function () {
  //     $(this)
  //       .find(".navigation_profile__dropdown")
  //       .fadeOut()
  //       .removeClass("active");
  //   }
  // );
  // $(".navigation_basket").hover(
  //   function () {
  //     $(this)
  //       .find(".navigation_basket__dropdown")
  //       .css("display", "flex")
  //       .hide(1)
  //       .fadeIn(100)
  //       .addClass("active");
  //     $(this)
  //       .find(".basketDropdown_block")
  //       .css("display", "flex")
  //       .hide(1)
  //       .fadeIn(500);
  //   },
  //   function () {
  //     $(this)
  //       .find(".navigation_basket__dropdown")
  //       .fadeOut()
  //       .removeClass("active");
  //     $(this).find(".basketDropdown_block").fadeOut(100);
  //   }
  // );
  $(".fields_block__field").hover(
    function () {
      $(this)
        .find(".field_dropdown")
        .css("display", "flex")
        .hide(1)
        .fadeIn(100)
        .addClass("active");
      $(this).find(".field_dropdownPrice__range").fadeIn(500);
    },
    function () {
      $(this).find(".field_dropdown").fadeOut().removeClass("active");
      $(this).find(".field_dropdownPrice__range").fadeOut(10);
    }
  );

  $(".deleteFavoriteProduct").on("click", function () {
    $(this).parents(".favoritesDropdown_content__product").fadeOut();
  });
  $(".favorites_clear").on("click", function () {
    $(this)
      .parents(".favoritesDropdown_block")
      .find(".favoritesDropdown_content__product")
      .fadeOut();
  });
  $(".deleteBasketProduct").on("click", function () {
    $(this).parents(".prod").fadeOut();
  });
  $(".basket_clear").on("click", function () {
    $(this)
      .parents(".basketDropdown_block")
      .find(".basketDropdown_content__product")
      .fadeOut();
  });
  $(".promo_block__carousel").owlCarousel({
    loop: true,
    margin: 10,
    nav: false,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      },
    },
  });
  $(".mobileCatalog_block__carousel").owlCarousel({
    loop: true,
    margin: 10,
    nav: false,
    responsive: {
      0: {
        items: 2,
      },
      600: {
        items: 3,
      },
      1000: {
        items: 4,
      },
    },
  });
  $(".mobileShops_block__carousel").owlCarousel({
    loop: true,
    margin: 0,
    nav: false,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      },
    },
  });
  $(".noveltyBanners_block__carousel").owlCarousel({
    loop: true,
    margin: 10,
    nav: false,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      },
    },
  });
  $(".productsBlock_carousel.oneProductsBlock_carousel").owlCarousel({
    loop: true,
    margin: 10,
    slideBy: 1,
    center: false,
    nav: false,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 3,
      },
      1000: {
        items: 4,
      },
    },
  });
  $(".productsBlock_carousel.twoProductsBlock_carousel").owlCarousel({
    loop: true,
    margin: 10,
    slideBy: 1,
    center: false,
    nav: false,
    responsive: {
      0: {
        items: 2,
      },
      600: {
        items: 2,
      },
      1000: {
        items: 4,
      },
    },
  });
  $(".shop_blockImages__carousel").owlCarousel({
    loop: true,
    margin: 10,
    nav: false,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      },
    },
  });

  // hover slider arrows

  $(".owl-next").hover(
    function () {
      $(this).find("img.sliderArrowNotActive").hide();
      $(this).find("img.sliderArrowActive").show();
    },
    function () {
      $(this).find("img.sliderArrowActive").hide();
      $(this).find("img.sliderArrowNotActive").show();
    }
  );
  $(".owl-prev").hover(
    function () {
      $(this).find("img.sliderArrowNotActive").hide();
      $(this).find("img.sliderArrowActive").show();
    },
    function () {
      $(this).find("img.sliderArrowActive").hide();
      $(this).find("img.sliderArrowNotActive").show();
    }
  );

  // /hover slider arrows

  // change catalog view

  $(".allProducts_view__showList").on("click", function () {
    $(".allProducts_view__showTable").removeClass("active");
    $(".productsWrapper").removeClass("table").addClass("list");
    $(this).addClass("active");
  });
  $(".allProducts_view__showTable").on("click", function () {
    $(".allProducts_view__showList").removeClass("active");
    $(".productsWrapper").removeClass("list").addClass("table");
    $(this).addClass("active");
  });

  // /change catalog view

  $(".productCard_block__images").owlCarousel({
    loop: true,
    margin: 10,
    nav: true,
    navText: [
      `<img src="/wp-content/themes/kaifsmoke/assets/images/productsSliderArrow.png" alt="Влево" class="sliderArrowNotActive">`,
      `<img src="/wp-content/themes/kaifsmoke/assets/images/productsSliderArrow.png" alt="Вправо" class="sliderArrowNotActive">`,
    ],
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      },
    },
  });

  // send photo in form

  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function (e) {
      var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i];
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
          var file = e.target;
          $("<img></img>", {
            class: "returnImg",
            src: e.target.result,
            title: file.name,
          })
            .insertAfter("#files")
            .css("display", "none")
            .fadeIn(500);
        };
        fileReader.readAsDataURL(f);
      }
      // console.log($('.returnImg').length);
      if ($(".returnImg").length == 3) {
        $(this).siblings("label").css("display", "none");
      }
    });
  } else {
    alert("Ошибка при загрузке файла, попробуйте позже.");
  }

  // /send photo in form

  // bonuses hover
  if ($(window).width() > "1170") {
    $(".accManagePage_top__haveBonuses").hover(
      function () {
        $(this).find(".hiddenMessageBonuses").fadeIn(300);
      },
      function () {
        $(this).find(".hiddenMessageBonuses").fadeOut(300);
      }
    );
  }
  if ($(window).width() < "1170") {
    $(".accManagePage_top__haveBonuses").on("click", function () {
      if (!$(this).hasClass("active")) {
        $(this).find(".hiddenMessageBonuses").fadeIn(300);
        $(this).addClass("active");
      } else {
        $(this).find(".hiddenMessageBonuses").fadeOut(300);
        $(this).removeClass("active");
      }
    });
    $(document).mouseup(function (e) {
      if ($(".accManagePage_top__haveBonuses").hasClass("active")) {
        var modalBlock = $(".accManagePage_top__haveBonuses");
        if (!modalBlock.is(e.target) && modalBlock.has(e.target).length === 0) {
          modalBlock.removeClass("active");
          modalBlock.find(".hiddenMessageBonuses").fadeOut();
        }
      }
    });
  }
  // /bonuses hover

  // verification phone number timer

  $(".sellPhoneVerificationCode").on("click", function () {
    $(this).hide();
    $(".sellPhoneVerificationCodeRepeat")
      .css("display", "flex")
      .hide()
      .fadeIn();
    $(".PhoneVerificationCode").removeAttr("readonly");
    var timer2 = "02:00";
    var interval = setInterval(function () {
      var timer = timer2.split(":");
      //by parsing integer, I avoid all extra string processing
      var minutes = parseInt(timer[0], 10);
      var seconds = parseInt(timer[1], 10);
      --seconds;
      minutes = seconds < 0 ? --minutes : minutes;
      if (minutes < 0) clearInterval(interval);
      seconds = seconds < 0 ? 59 : seconds;
      seconds = seconds < 10 ? "0" + seconds : seconds;
      //minutes = (minutes < 10) ?  minutes : minutes;
      $(".verificatonPhoneTimer").html(minutes + ":" + seconds);
      timer2 = minutes + ":" + seconds;
      if (minutes == -1 || (minutes <= 0 && seconds <= 0)) {
        $(".sellPhoneVerificationCodeRepeat")
          .css("opacity", "1")
          .prop("disabled", false)
          .removeAttr("disabled");
        $(".verificatonPhoneTimer").html(" ");
      }
    }, 1000);
  });
  $(".PhoneVerificationCode").on("keyup", function () {
    var $this = $(this),
      val = $this.val();

    if (val == 1111) {
      $(".sellPhoneVerificationCodeRepeat").hide();
      $(".sellPhoneVerificationCode").hide();
      $(".verificatonPhoneTimer").hide();
      $(".PhoneVerificationCode").addClass("success");
      $(".VerificationCodeError").hide();
    } else if (val.length >= 4 && val != 1111) {
      $this.parents(".field_phone-verification").addClass("failed");
      $(".VerificationCodeError").css("display", "flex").hide().fadeIn();
    }
  });

  // /verification phone number timer

  // custom select
  $(".custom-select").each(function () {
    var classes = $(this).attr("class"),
      id = $(this).attr("id"),
      name = $(this).attr("name");
    var template = '<div class="' + classes + '">';
    template +=
      '<span class="custom-select-trigger">' +
      $(this).attr("placeholder") +
      "</span>";
    template += '<div class="custom-options">';
    $(this)
      .find("option")
      .each(function () {
        template +=
          '<span class="custom-option ' +
          $(this).attr("class") +
          '" data-value="' +
          $(this).attr("value") +
          '">' +
          $(this).html() +
          "</span>";
      });
    template += "</div></div>";

    $(this).wrap('<div class="custom-select-wrapper"></div>');
    $(this).hide();
    $(this).after(template);
  });
  $(".custom-option:first-of-type").hover(
    function () {
      $(this).parents(".custom-options").addClass("option-hover");
    },
    function () {
      $(this).parents(".custom-options").removeClass("option-hover");
    }
  );
  $(".custom-select-trigger").on("click", function () {
    $("html").one("click", function () {
      $(".custom-select").removeClass("opened");
    });
    $(this).parents(".custom-select").toggleClass("opened");
    event.stopPropagation();
  });
  $(".custom-option").on("click", function () {
    $(this)
      .parents(".custom-select-wrapper")
      .find("select")
      .val($(this).data("value"));
    $(this)
      .parents(".custom-options")
      .find(".custom-option")
      .removeClass("selection");
    $(this).addClass("selection");
    $(this).parents(".custom-select").removeClass("opened");
    $(this)
      .parents(".custom-select")
      .find(".custom-select-trigger")
      .text($(this).text());
  });

  // /custom select

  // choose city

  $(".showGenderDropdown").on("click", function () {
    if (!$(this).parents(".genderField").hasClass("active")) {
      $(this).siblings(".genderDropdown").show();
      $(this).parents(".genderField").addClass("active");
    } else {
      $(this).siblings(".genderDropdown").hide();
      $(this).parents(".genderField").removeClass("active");
    }
  });

  $(".genderDropdown li").on("click", function () {
    $(this)
      .parents(".genderDropdown")
      .siblings(".showGenderDropdown")
      .text($(this).text());
    $(this).parents(".genderDropdown").fadeOut();
    $(this).parents(".genderField").removeClass("active");
  });

  // mobile menu icons clicks

  $(".mobileMenu_userIcon").on("click", function () {
    $(".mobileUserModal")
      .css("display", "flex")
      .hide()
      .fadeIn(10)
      .addClass("active");
    $(".mobileMenu").removeClass("active").addClass("lOne");
  });
  $(
    ".mobileMenuUser_back, .mobileMenuSignIn_form__forgotPasswordLink, .mobileMenuBasket_back, .mobileMenuFavorites_back, .mobileMenuSignIn_back"
  ).on("click", function () {
    $(this)
      .parents(".mobileMenuModalBlock")
      .removeClass("active")
      .fadeOut(1000);
    $(".mobileMenu").addClass("active").removeClass("lOne");
  });
  $(".mobileMenu_favoritesIcon").on("click", function () {
    $(".mobileMenu").removeClass("active").addClass("lOne");
    $(".mobileMenuFavorites")
      .css("display", "flex")
      .hide()
      .fadeIn(10)
      .addClass("active");
  });
  $(".mobileMenu_basketIcon").on("click", function () {
    $(".mobileMenu").removeClass("active").addClass("lOne");
    $(".mobileMenuBasket")
      .css("display", "flex")
      .hide()
      .fadeIn(10)
      .addClass("active");
  });

  // /mobile menu icons clicks

  // mobile menu catalog view tabs
  $(".tab_oneProducts").on("click", function () {
    $(this).addClass("active");
    $(this).siblings(".tab_twoProducts").removeClass("active");
    $(this)
      .parents(".products_block")
      .find(".productsBlock_carousel.twoProductsBlock_carousel")
      .fadeOut();
    $(this)
      .parents(".products_block")
      .find(".productsBlock_carousel.oneProductsBlock_carousel")
      .fadeIn();
  });
  $(".tab_twoProducts").on("click", function () {
    $(this).addClass("active");
    $(this).siblings(".tab_oneProducts").removeClass("active");
    $(this)
      .parents(".products_block")
      .find(".productsBlock_carousel.oneProductsBlock_carousel")
      .fadeOut();
    $(this)
      .parents(".products_block")
      .find(".productsBlock_carousel.twoProductsBlock_carousel")
      .fadeIn();
  });
  // /mobile menu catalog view tabs

  // mobile touchs effects
  // $(".carousel_item__link, .productsBlock_inBasket button").on(
  //   "touchstart",
  //   function () {
  //     $(this).addClass("touchBtnActive");
  //   }
  // );
  // $(".carousel_item__link, .productsBlock_inBasket button").on(
  //   "touchend",
  //   function () {
  //     $(this).removeClass("touchBtnActive");
  //   }
  // );
  // $(
  //   ".mobileCatalog_block__carousel .carousel_item, .footer_block__social ul li a, .mobileMenu_block__icons a"
  // ).on("touchstart", function () {
  //   $(this).addClass("touchSvgTextActive");
  // });
  // $(
  //   ".mobileCatalog_block__carousel .carousel_item, .footer_block__social ul li a, .mobileMenu_block__icons a"
  // ).on("touchend", function () {
  //   $(this).removeClass("touchSvgTextActive");
  // });
  // $(".shop_blockDescription__shopLink a").on("touchstart", function () {
  //   $(this).addClass("touchSvgPathActive");
  // });
  // $(".shop_blockDescription__shopLink a").on("touchend", function () {
  //   $(this).removeClass("touchSvgPathActive");
  // });
  // $(
  //   ".product_quantity__plus, .product_quantity__minus, .footer_block__navigation ul li a, .footer_block__account ul li a"
  // ).on("touchstart", function () {
  //   $(this).addClass("touchTextActive");
  // });
  // $(
  //   ".product_quantity__plus, .product_quantity__minus, .footer_block__navigation ul li a, .footer_block__account ul li a"
  // ).on("touchend", function () {
  //   $(this).removeClass("touchTextActive");
  // });
  // $(".mobilePlus").on("click", function () {
  //   var prodQuantity = $(this).parents(".prod").find(".productQuantity").text();
  //   prodQuantity++;
  //   $(this).parents(".prod").find(".productQuantity").text(prodQuantity);
  // });
  // $(".mobileMinus").on("click", function () {
  //   var prodQuantity = $(this).parents(".prod").find(".productQuantity").text();
  //   if (prodQuantity >= 2) {
  //     prodQuantity--;
  //     $(this).parents(".prod").find(".productQuantity").text(prodQuantity);
  //   } else {
  //     prodQuantity = 1;
  //     $(this).parents(".prod").find(".productQuantity").text(prodQuantity);
  //   }
  // });
  // $(".mobileMenu_block__catalog ul li a").on("touchstart", function () {
  //   $(this).addClass("touchLinkActive");
  //   $(this).siblings("svg").addClass("touchSvgStrokeActive");
  //   $(this).parent("li").addClass("touchAfterActive");
  // });
  // $(".mobileMenu_block__catalog ul li a").on("touchend", function () {
  //   $(this).removeClass("touchLinkActive");
  //   $(this).siblings("svg").removeClass("touchSvgStrokeActive");
  //   $(this).parent("li").removeClass("touchAfterActive");
  // });
  // /mobile touchs effects

  $(".deletePhone").on("click", function () {
    $(this).siblings("input").val("");
    $(this).hide();
    $(this)
      .parents(".accManagePage_block__profileInf")
      .find(".accManagePage_block__confirmedPhone")
      .hide();
    $(this)
      .parents(".accManagePage_block__profileInf")
      .find(".phone-verification")
      .css("display", "flex")
      .hide()
      .fadeIn();
  });
  $(".unsubscribeBtn").on("click", function () {
    $(this)
      .parents(".subscribeField")
      .find(".subscribeField_status__success")
      .hide();
    $(this)
      .parents(".subscribeField")
      .find(".subscribeField_status__failed")
      .css("display", "flex")
      .hide()
      .fadeIn();
    $(this).hide();
    $(this).parents(".subscribeField").find(".subscribeBtn").show();
  });
  $(".subscribeBtn").on("click", function () {
    $(this)
      .parents(".subscribeField")
      .find(".subscribeField_status__failed")
      .hide();
    $(this)
      .parents(".subscribeField")
      .find(".subscribeField_status__success")
      .css("display", "flex")
      .hide()
      .fadeIn();
    $(this).hide();
    $(this).parents(".subscribeField").find(".unsubscribeBtn").show();
  });

  Noty.overrideDefaults({
    type: "alert",
    layout: "topRight",
    theme: "relax",
    timeout: 3000,
  });
});
