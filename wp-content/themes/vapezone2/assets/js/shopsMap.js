$(document).ready(() => {
  if ($(".shops")) {
    // Функция ymaps.ready() будет вызвана, когда
    // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
    const mapFunc = () => {
      ymaps.ready(init);

      function init() {
        var myMap = new ymaps.Map(
            "map",
            {
              center: [59.944856, 30.346849],
              zoom: 10,
              controls: ["zoomControl"],
            },
            {
              searchControlProvider: "yandex#search",
              iconImageHref: "images/ball.png",
            }
          ),
          objectManager = new ymaps.ObjectManager({
            // Чтобы метки начали кластеризоваться, выставляем опцию.
            clusterize: true,
            // ObjectManager принимает те же опции, что и кластеризатор.
            gridSize: 32,
          });

        myMap.behaviors.disable("scrollZoom");

        // Чтобы задать опции одиночным объектам и кластерам,
        // обратимся к дочерним коллекциям ObjectManager.

        objectManager.objects.options.set("iconLayout", "default#image");

        objectManager.objects.options.set(
          "iconImageHref",
          "../../wp-content/themes/vapezone/assets/images/icons/logoToMap.svg"
        ); //vapezone.ru/wp-content/themes/vapezone/assets

        https: objectManager.clusters.options.set(
          "preset",
          "islands#blueClusterIcons"
        );
        myMap.geoObjects.add(objectManager);

        // if (document.querySelector(".book")) {
        //   var productId = document
        //     .querySelector(".book")
        //     .attr("data-productid");
        // } else
        if (document.querySelector(".productCard")) {
          var productId = document
            .querySelector(".productCard")
            .id.split("-")[1];
        }

        //"https://vapezone.ru/wp-json/controllers/v1/shops/"

        const shopList = [];

        //Функция выбора магазина с карты
        const chooseShopFromMap = () => {
          const items = document.querySelectorAll("ymaps .chooseShop");
          items.forEach((item) => {
            console.log(item);
          });
        };

        const nowurl = document.location.pathname;
        let actionGet = "get_map_for_product_card";

        switch (nowurl) {
          case "/shops/":
            actionGet = "get_map_for_shops";
            break;
          case "/reservation/":
            actionGet = "get_map_for_cart";
            break;
        }

        $.ajax({
          url: AJAXURL,
          dataType: "json",
          method: "GET",
          data: {
            product_id: productId,
            action: actionGet,
          },
        }).done(function (data) {
          const readyConst = JSON.stringify(data);
          const pData = JSON.parse(readyConst);
          const arrData = pData.features;

          arrData.forEach((el) => {
            // console.log(
            //   el.properties.balloonContentHeader.replace(/<(.|\n)*?>/g, "")
            // );

            shopList.push(
              el.properties.balloonContentHeader.replace(/<(.|\n)*?>/g, "")
            );

            // chooseShopFromMap();
          });

          console.log();

          objectManager.add(readyConst);
        });

        //MapSearch

        if ($("#suggest").length) {
          var find = function (arr, find) {
            return arr.filter(function (value) {
              return (
                (value + "").toLowerCase().indexOf(find.toLowerCase()) != -1
              );
            });
          };

          var myProvider = {
            suggest: function (request, options) {
              var res = find(shopList, request),
                arrayResult = [],
                results = Math.min(options.results, res.length);
              for (var i = 0; i < results; i++) {
                arrayResult.push({ displayName: res[i], value: res[i] });
              }
              return ymaps.vow.resolve(arrayResult);
            },
          };

          //Функция поиска по списку
          const listSearch = (searchValue) => {
            const tableItems = document.querySelectorAll(".table-item ");

            tableItems.forEach((item) => {
              const address = item.getAttribute("data-shopaddress");
              console.log(address);

              let result = address.match(searchValue) || [];
              if (result.length > 0) {
                item.classList.add("active");

                $(".shops-table").animate(
                  {
                    scrollTop: $(item).offset().top - 20,
                  },
                  {
                    duration: 370, // по умолчанию «400»
                    easing: "linear", // по умолчанию «swing»
                  }
                );
              } else {
                item.classList.remove("active");
              }
            });
          };

          const getPlaceBySuggestView = (siggestViewGuessValue) => {
            ymaps.geocode(siggestViewGuessValue).then(
              (res) => {
                const firstGeoObject = res.geoObjects.get(0);

                // Область видимости геообъекта.
                const bounds = firstGeoObject.properties.get("boundedBy");

                // Масштабируем карту на область видимости геообъекта.
                myMap.setBounds(bounds, {
                  checkZoomRange: true,
                });

                //Проверка по регулярке для списка
              },
              (error) => {
                // Обработка ошибки
                console.log(error);
              }
            );
          };

          var suggestView = new ymaps.SuggestView("suggest", {
            provider: myProvider,
          });

          suggestView.events.add("select", (e) => {
            const chosenAddress = e.get("item").value;
            getPlaceBySuggestView(chosenAddress);
            listSearch(chosenAddress);
          });
        }

        document.querySelector(".ymaps-2-1-79-ground-pane").cssText = `
          -ms-filter: grayscale(1);
          -webkit-filter: grayscale(1);
          -moz-filter: grayscale(1);
          -o-filter: grayscale(1);
          filter: grayscale(1);`;
      }
    };
    //Основная часть

    $(".sort").click(function () {
      if ($(".sort-menu").css("display") == "block") {
        $(".sort-menu").css("display", "none");
      } else {
        $(".sort-menu").css("display", "block");
      }
    });

    //Sort of shops
    var shopSort = () => {
      var nodeList = document.querySelectorAll(".table-item");
      var trigger = document.querySelector("#reviews");

      if (nodeList && trigger) {
        console.log(nodeList);
        var textArray = [];
        var itemsArray = [];
        var parent = nodeList[0].parentNode;

        // for (var i = 0; i < nodeList.length; i++) {
        //   itemsArray.push(parent.removeChild(nodeList[i]));
        // }

        nodeList.forEach((el) => {
          var text = el.querySelector(".table-item > span").textContent;
          var num = +text.replace(/[^\d.-]/g, "");

          console.log(num);

          if (num <= 0) {
            el.style.background = "#FFEEEC";
            el.style.borderLeft = "10px solid #FF4E44";
            itemsArray.push(parent.removeChild(el));
          } else {
            el.style.background = "#EDFFEC";
            el.style.borderLeft = "10px solid #58FF6A";
          }
        });

        itemsArray.forEach((el) => {
          parent.appendChild(el);
        });
      }

      // for (var i = 0; i < nodeList.length; i++) {
      //   itemsArray.push(parent.removeChild(nodeList[i]));
      // }
      // itemsArray
      //   .sort(function (nodeA, nodeB) {
      //     var textA = nodeA.querySelector("div:nth-child(4)").textContent;
      //     var intA = parseInt(textA.match(/\d+/));
      //     console.log("intA", intA);
      //     var textB = nodeB.querySelector("div:nth-child(4)").textContent;
      //     var intB = parseInt(textB.match(/\d+/));
      //     var numberA = intA;
      //     var numberB = intB;
      //     if (numberA < numberB) return -1;
      //     if (numberA > numberB) return 1;
      //     return -1;
      //   })
      //   .forEach(function (node) {
      //     console.log("node", node);
      //     parent.appendChild(node);
      //   });
    };

    try {
      shopSort();
    } catch (e) {
      console.log(e);
    }

    const metroColor = () => {
      const metroItems = document.querySelectorAll(
        ".shops-table > .table-item > span[data-color]"
      );

      if (metroItems) {
        metroItems.forEach((el) => {
          const colorDef = el.getAttribute("data-color");

          let colorNew;

          switch (colorDef) {
            case "blue":
              colorNew = "#2074C1";
              break;
            case "orange":
              colorNew = "#EF7D00";
              break;

            case "red":
              colorNew = "#E9420D";
              break;

            case "green":
              colorNew = "#31A337";
              break;

            case "violet":
              colorNew = "#984492";
              break;
          }

          el.querySelector("svg > path").style.fill = colorNew;

          el.querySelector("svg > path").style.stroke = colorNew;
        });
      }

      console.log(metroItems);
    };

    metroColor();

    try {
      mapFunc();
    } catch (err) {
      console.log(err);
    }
  }
});

//__________________________________________________
//Функционал ограничения длины поиска

const seachControl = () => {
  $("body").on("input", ".dgwt-wcas-search-input", function () {
    this.value = this.value.replace(/[^a-zа-яё0-9\s]/gi, "");
  });

  $("body").on("paste", ".dgwt-wcas-search-input", function () {
    this.value = this.value.replace(/[^a-zа-яё0-9\s]/gi, "");
  });
};

seachControl();

//___________________________________________________
//Модальное окно возраста

// $.getScript("./cookie.js", () => {
var cookieTest = getCookie("statusPopUp");
var timeOfLife = "session";

var addPopUp = () => {
  var popup = document.querySelector(".vzpopup");

  //Блокирую body для прокрутки
  var bdy = document.querySelector("body");
  bdy.style.overflow = "hidden";

  //Проверяю статус куки и если он 0, то задаю вопрос
  //Иначе просто удалю PopUp из DOM

  if (popup) {
    popup.classList.add("showPopUp");
    var yes = document.getElementById("yes");
    var no = document.getElementById("no");

    no.addEventListener(
      "click",
      (ev = () => {
        var redirect = "https://yandex.ru/";
        window.location.replace(redirect);
      })
    );

    yes.addEventListener(
      "click",
      (ev = () => {
        //Кардинальное удаление из DOM
        if (timeOfLife === "session") {
          setCookie("statusPopUp", 1);
        } else {
          setCookie("statusPopUp", 1, {
            "max-age": timeOfLife,
          });
        }
        bdy.style.overflow = "auto";
        popup.remove();
      })
    );
  } else {
    //popup.remove();
    console.log("Удаление при куки");
  }

  return status;
};

//Установка куки в зависимости от состояния PopUp
var checkCookieForUser = (status) => {
  var checkProfile = document.querySelector(
    ".navigation_profileDropdownAuthorized__navigation"
  );

  //? Проверка на юрлы входа и регистрации
  var checkUrlsLogin = () => {
    var url = window.location.href;
    var needsUrls = [
      "https://vapezone.ru/signin/",
      "https://vapezone.ru/signup/",
    ];
    if (
      url.indexOf("https://vapezone.ru/signin/") != -1 ||
      url.indexOf("https://vapezone.ru/signup/") != -1 ||
      url.indexOf("https://test.vapezone.ru/signin/") != -1 ||
      url.indexOf("https://test.vapezone.ru/signup/") != -1
    ) {
      return;
    } else {
      if (!status && !checkProfile) {
        addPopUp();
      }
    }
  };
  checkUrlsLogin();
};

checkCookieForUser(cookieTest);
