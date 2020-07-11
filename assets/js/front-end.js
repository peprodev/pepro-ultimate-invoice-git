jQuery(document).ready(function($) {
  const SECONDS = 1000;
  const PEOPCA_MOTHER = $(".pepro-one-page-purchase---top-parent");
  const PEOPCA_CART_BODY = $(".pepro-one-page-purchase--cart-body");
  const LABELOKAY = _i18n.okaylabel;
  var peproOnePagePurchaseRequest = null;
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
      this.oldValue = this.value;
      this.oldSelectionStart = this.selectionStart;
      this.oldSelectionEnd = this.selectionEnd;
    } else if (this.hasOwnProperty("oldValue")) {
      this.value = this.oldValue;
      this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
    } else {
      this.value = "";
    }
    });
  };
  $.fn.animate_placeholder = function(inputFilter) {
    var txt = $(this).attr("placeholder") || "Search here ...";
    var timeOut; var txtLen = txt.length; var char = 0;
    var domid = $(this);
    $(domid).attr('placeholder', '|');
    (function typeIt() {
        var humanize = Math.round(Math.random() * (200 - 30)) + 30;
        timeOut = setTimeout(function () {
            char++;
            var type = txt.substring(0, char);
            $(domid).attr('placeholder', type + '|');
            typeIt();
            if (char == txtLen) {
                $(domid).attr('placeholder', $(domid).attr('placeholder').slice(0, -1)) // remove the '|'
                clearTimeout(timeOut);
            }
        }, humanize);
    }());
  };
  window.onload = function() {
    $("a").each(function(e) {
      $(this).attr("target", "_blank");
    });
  };
  setTimeout(function () {
    $(PEOPCA_MOTHER).trigger("startup");
  }, 200);
  $(document).on("keyup click change", "input#pepro-one-page-purchase--search-input", function(e) {
    e.preventDefault();
    var filter = $(this).val();
    $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item.catfiltered").each(function () {
        if ($(this).find(".pepro-one-page-purchase--product-item-info-primary").text().search(new RegExp(filter, "i")) < 0) {
            $(this).hide().addClass("hidebysrch");
        } else {
            $(this).show().removeClass("hidebysrch");
        }
    });
    if ($("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item:visible").length < 1){
      $("ul.pepro-one-page-purchase--product-list").addClass("empty");
    }else{
      $("ul.pepro-one-page-purchase--product-list").removeClass("empty");
    }
  });
  $(document).on("keyup click change", "select#pepro-one-page-purchase--select-categories", function(e) {
    e.preventDefault();
    var filter = $("select#pepro-one-page-purchase--select-categories option:selected").text();
    if($(this).val() === "[ALL]"){
      $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").show().removeClass("hidebysrch").addClass("catfiltered");
    }else{
      $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").each(function () {
        if ($(this).find(".pepro-one-page-purchase--product-item-info-secondary").text().search(new RegExp(filter, "i")) < 0) {
            $(this).hide().addClass("hidebysrch").removeClass("catfiltered");
        } else {
            $(this).show().removeClass("hidebysrch").addClass("catfiltered");
        }
    });
    }

    if ($("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item:visible").length < 1){
      $("ul.pepro-one-page-purchase--product-list").addClass("empty");
    }else{
      $("ul.pepro-one-page-purchase--product-list").removeClass("empty");
    }
  });
  $(document).on("click tap", ".pepro-one-page-purchase-filter", function(e) {
    e.preventDefault();
    $(".pepro-one-page-purchase-filter").removeClass("active");
    $(this).addClass("active");
    switch ($(this).data("query")) {
      case "popularity":
        $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").sort(sort_li_popularity).appendTo('ul.pepro-one-page-purchase--product-list');
        break;
      case "total_sales":
        $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").sort(sort_li_total_sales).appendTo('ul.pepro-one-page-purchase--product-list');
        break;
      case "latest":
        $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").sort(sort_li_latest).appendTo('ul.pepro-one-page-purchase--product-list');
        break;
      case "price_asc":
        $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").sort(sort_li_price_asc).appendTo('ul.pepro-one-page-purchase--product-list');
        break;
      case "price_desc":
        $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").sort(sort_li_price_desc).appendTo('ul.pepro-one-page-purchase--product-list');
        break;

      default: /* sort alphabetically */
        $("ul.pepro-one-page-purchase--product-list>li.pepro-one-page-purchase--product-item").sort(sort_li_alphabetically).appendTo('ul.pepro-one-page-purchase--product-list');
        break;
    }
  });
  $(document).on("click tap", ".pepro-one-page-purchase--cart-total--third.proceedToCheckout", function(e) {
    e.preventDefault();
    var PEOPCA_CART_HOLDER = $(".pepro-one-page-purchase--cart-item-holder");
    var CARTDATA = PEOPCA_CART_HOLDER.find(`.pepro-one-page-purchase--product-item`);
    var JSSDATA = {}; if (CARTDATA.length < 1) { $.alert(`${_i18n.emptycartSubmit}`, `${_i18n.thisIsAnError}`, null , LABELOKAY); $(".modal-button").first().focus(); return false; }
    CARTDATA.each(function (x) {
      var item = $(this); var pid = item.data("pid");
      var qty = item.find(".pepro-one-page-purchase--cart-item-qty").val() || 1;
      if (peproOnePagePurchaseAndInvoice[pid] !== undefined){
        JSSDATA[pid] = qty;
      }
    });
    if (JSSDATA.length < 1) { return false; }
    if (peproOnePagePurchaseRequest != null) {
      peproOnePagePurchaseRequest.abort();
    }
    $(".pepro-one-page-purchase--cart-total--third.proceedToCheckout>span").addClass("active");
    peproOnePagePurchaseRequest = $.ajax({
      type: "POST",
      dataType: "json",
      url: _i18n.ajax,
      data: {
        action: _i18n.td,
        nonce: _i18n.nonce,
        wparam: "add-cart",
        lparam: JSSDATA,
      },
      success: function (r) {
        $(".pepro-one-page-purchase--cart-total--third.proceedToCheckout>span").removeClass("active");
        if (r.success == true) {
          // console.info(r.data.message);
          $(PEOPCA_MOTHER).trigger("ajax_request_success",r);
          window.location.href = r.data.url;
        } else {
          // console.error(r.data.message);
          $(PEOPCA_MOTHER).trigger("ajax_request_failed",r);
        }
      }
    });
  });
  $(document).on("click tap", ".pepro-one-page-purchase--remove2cart", function(e) {
    e.preventDefault();
    var pid = $(this).data("pid");
    var PEOPCA_CART_HOLDER = $(".pepro-one-page-purchase--cart-item-holder");
    var ITEM = PEOPCA_CART_HOLDER.find(`li[data-pid="${pid}"]`);
    var TITLE = ITEM.find(`.pepro-one-page-purchase--product-title a`).text();
    if (ITEM.length > 0) {
      $.confirm(_i18n.removeFrombasketConfirmation, `${_i18n.removeFrombasketTitle} ${TITLE}`,
        function() {
          ITEM.remove();
          $(PEOPCA_MOTHER).trigger("update_cart");
        },
        function() {}, _i18n.confirmYes, _i18n.confirmNo
      );
      $(".modal-button").first().focus();
      return;
    }
  });
  $(document).on("click tap", ".pepro-one-page-purchase--remove2cart.removeall", function(e) {
    e.preventDefault();
    var PEOPCA_CART_HOLDER = $(".pepro-one-page-purchase--cart-item-holder");
    $.confirm(_i18n.removeFrombasketConfirmation2, `${_i18n.removeFrombasketTitle2}`,
      function() {
        var PEOPCA_CART_HOLDER = $(".pepro-one-page-purchase--cart-item-holder");
        PEOPCA_CART_HOLDER.parent().empty();
        $(PEOPCA_MOTHER).trigger("update_cart");
      },
      function() {}, _i18n.confirmYes, _i18n.confirmNo
    );
    $(".modal-button").first().focus();
    return;
  });
  $(document).on("click tap", ".pepro-one-page-purchase--add2cart", function(e) {
    e.preventDefault();
    var pid = $(this).data("pid");
    if (peproOnePagePurchaseAndInvoice[pid] !== undefined) {

      if (PEOPCA_CART_BODY.is(":empty")) {
        PEOPCA_CART_BODY.append(`<ul class="pepro-one-page-purchase--cart-item-holder"></ul>`);
      }
      var PEOPCA_CART_HOLDER = $(".pepro-one-page-purchase--cart-item-holder");


      if (PEOPCA_CART_HOLDER.find(`li[data-pid="${pid}"]`).length > 0) {
        qty = PEOPCA_CART_HOLDER.find(`li[data-pid="${pid}"] .pepro-one-page-purchase--cart-item-qty`);
        qty.val(parseInt(qty.val()) + 1);
        $(PEOPCA_MOTHER).trigger("update_cart");
        return;
      }


      var $product = peproOnePagePurchaseAndInvoice[pid];
      PEOPCA_CART_HOLDER.append(`
              <li class="pepro-one-page-purchase--product-item" data-pid="${$product["get_id"]}">
                <div class="pepro-one-page-purchase--product-image">
                  <img src="${$product["get_image"]}" style="height: 96px;" />
                </div>
                <div class="pepro-one-page-purchase--product-item-info">
                  <div class="pepro-one-page-purchase--product-item-info-primary">
                    <div class="pepro-one-page-purchase--product-title">
                      <a target='_blank' href="${$product["get_permalink"]}">${$product["get_name"]}</a>
                    </div>
                    <div class="pepro-one-page-purchase--cart-qty">
                      <input class="pepro-one-page-purchase--cart-item-qty" prev-value="1" value="1" />
                      <a class="pepro-one-page-purchase--cart-item-qty-sub">-</a>
                      <a class="pepro-one-page-purchase--cart-item-qty-add">+</a>
                    </div>
                  </div>
                  <div class="pepro-one-page-purchase--product-item-pruchase">
                    <div class="pepro-one-page-purchase--product-item-pricelist">
                      <div class="pepro-one-page-purchase--product-regular_price" >${$product["get_regular_price"]}</div>
                      <div class="pepro-one-page-purchase--product-sale_price" >${$product["get_price"]}</div>
                    </div>
                    <div class="pepro-one-page-purchase--product-item-pricesymbol">
                      <div class="pepro-one-page-purchase--product-currency_symbol">${_i18n.currencySymbol}</div>
                    </div>
                  </div>
                  <div class="pepro-one-page-purchase--product-add2cart" >
                    <a href="#" data-pid="${pid}" class="pepro-one-page-purchase--remove2cart" title="${_i18n.removefromcart}"><i class="fa fa-trash"></i></a>
                  </div>
                </div>
              </li>
            `);
      $(PEOPCA_MOTHER).trigger("update_cart",false);
      setTimeout(function() {
        $.toptip(`<p style="font-size: 1.2rem;"><i class="fa fa-shopping-bag" style="margin: 0 1rem;"></i><strong>${peproOnePagePurchaseAndInvoice[pid]["get_name"]}</strong> ${_i18n.addedtocard}</p>`, 1 * SECONDS, "w3-green", 80);
      }, 100);
    } else {
      $.alert(`${_i18n.unknownError}`, `${_i18n.thisIsAnError}`, null , LABELOKAY);
      $(".modal-button").first().focus();
    }
  });
  $(document).on("click tap", ".pepro-one-page-purchase--cart-item-qty-sub", function(e) {
    e.preventDefault();
    var inp = $(this).parent().find(".pepro-one-page-purchase--cart-item-qty");
    var val = parseInt(inp.val());
    if (val > 1) {
      inp.val(val - 1);
      $(PEOPCA_MOTHER).trigger("update_cart");
    }
  });
  $(document).on("click tap", ".pepro-one-page-purchase--cart-item-qty-add", function(e) {
    e.preventDefault();
    var inp = $(this).parent().find(".pepro-one-page-purchase--cart-item-qty");
    var val = parseInt(inp.val());
    inp.val(val + 1);
    $(PEOPCA_MOTHER).trigger("update_cart");
  });
  $(document).on("keyup change", "input.pepro-one-page-purchase--cart-item-qty", function(e) {
    e.preventDefault();
    if ($(this).attr("prev-value") !== $(this).val()){
      if ($(this).val() < 1){
        $(this).val("1");
      }
      $(this).attr("prev-value",$(this).val());
      $(PEOPCA_MOTHER).trigger("update_cart");
    }
  });
  $(PEOPCA_MOTHER).on("startup", function(e) {
    $("input#pepro-one-page-purchase--search-input").val("");
    $("select#pepro-one-page-purchase--select-categories").val("[ALL]").trigger("change");
    $("select#pepro-one-page-purchase--select-categories").select2();
    $("input#pepro-one-page-purchase--search-input").animate_placeholder();
  });
  $(PEOPCA_MOTHER).on("update_cart", function(e,silence=true) {
    if (silence === true){
      $(".m-toptip.active").remove();
      $.toptip(`<p style="font-size: 1.2rem;"><i class="fa fa-shopping-bag" style="margin: 0 1rem;"></i><strong>${_i18n.instantShoppingBasketUpated}</strong>`, 1 * SECONDS, "w3-blue", 80);
    }
    $("input.pepro-one-page-purchase--cart-item-qty").inputFilter(function(value) { return /^\d*$/.test(value);/*Allow digits only, using a RegExp*/ });
    var PEOPCA_CART_HOLDER = $(".pepro-one-page-purchase--cart-item-holder");
    PEOPCA_CART_HOLDER.find(`.pepro-one-page-purchase--product-total`).remove();
    var CARTDATA = PEOPCA_CART_HOLDER.find(`.pepro-one-page-purchase--product-item`);
    var total_products = parseInt(0);
    var total_items = parseInt(0);
    var total_price = parseInt(0);
    var priceBeforeOff = parseInt(0);
    if (CARTDATA.length < 1) {
      PEOPCA_CART_HOLDER.parent().empty();
      return;
    }
    $.each(CARTDATA, function(i, x) {
      total_items++;
      var item = $(x);
      var pid = item.data("pid");
      var qty = item.find(".pepro-one-page-purchase--cart-item-qty").val() || 1;
      total_products += parseInt(qty);
      if (peproOnePagePurchaseAndInvoice[pid] !== undefined) {
        var product = peproOnePagePurchaseAndInvoice[pid];
        var price = product["get_price_raw"];
        var priceoff = product["get_regular_price_raw"];
        priceBeforeOff += parseInt(priceoff * qty);
        total_price += parseInt(price * qty);
      } else {
        $.alert(`${_i18n.unknownError}`, `${_i18n.thisIsAnError}`, null , LABELOKAY);
        $(".modal-button").first().focus();
      }
    });
    PEOPCA_CART_HOLDER.append(`<li class="pepro-one-page-purchase--product-total"></li>`);
    var totalContainer = PEOPCA_CART_HOLDER.find(`.pepro-one-page-purchase--product-total`).first();
    priceBeforeOff= numberWithCommas(priceBeforeOff);
    total_price= numberWithCommas(total_price);
    totalContainer.append(`<div class="pepro-one-page-purchase--cart-total-container"><div class="pepro-one-page-purchase--cart-total--body"><div class="pepro-one-page-purchase--cart-total--firstw"><span class="totalProducts"><span>${_i18n.ttp}</span> ${total_items}</span><span class="totalItems"><span>${_i18n.tti}</span> ${total_products}</span></div><div class="pepro-one-page-purchase--cart-total--first"><span class="pricetag">${priceBeforeOff}</span><span class="totalprice">${total_price}</span></div><span class="currencySymbol">${_i18n.currencySymbol}</span><div class="pepro-one-page-purchase--cart-total--third proceedToCheckout"><span class="fa fa-spin fa-cog"></span>${_i18n.proceedToCheckout}</div></div></div>`);
    return;
  });
  $(PEOPCA_MOTHER).on("ajax_request_success", function (e, param) {
    // $.alert(param.msg);
    // $(".modal-button").first().focus();
  });
  $(PEOPCA_MOTHER).on("ajax_request_failed", function (e, param) {
    $.alert(param.data.message, "", null , LABELOKAY);
    $(".modal-button").first().focus();
  });
  function numberWithCommas(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
  function sort_li_alphabetically(a, b) {
    var str1 = $(b).find(".pepro-one-page-purchase--product-title>a").text();
    var str2 = $(a).find(".pepro-one-page-purchase--product-title>a").text();
    var n = str1.localeCompare(str2);
    return -n;
  }
  function sort_li_popularity(a, b) {
    var str1 = $(b).find(".pepro-one-page-purchase--product-item-info-primary").first().data("av-rating");
    var str2 = $(a).find(".pepro-one-page-purchase--product-item-info-primary").first().data("av-rating");
    return (str1 > str2) ? 1 : -1;
  }
  function sort_li_total_sales(a, b) {
    var str1 = $(b).find(".pepro-one-page-purchase--product-item-info-primary").first().data("total-sales");
    var str2 = $(a).find(".pepro-one-page-purchase--product-item-info-primary").first().data("total-sales");
    return (str1 > str2) ? 1 : -1;
  }
  function sort_li_latest(a, b) {
    var str1 = Date.parse($(b).data("last-update"));
    var str2 = Date.parse($(a).data("last-update"));
    return (str1 > str2) ? 1 : -1;
  }
  function sort_li_price_asc(a, b) { // small to large
    var str1 = $(b).find(".pepro-one-page-purchase--product-sale_price").data("raw");
    var str2 = $(a).find(".pepro-one-page-purchase--product-sale_price").data("raw");
    return (str1 > str2) ? -1 : 1;
  }
  function sort_li_price_desc(a, b) { // large to small
    var str1 = $(b).find(".pepro-one-page-purchase--product-sale_price").data("raw");
    var str2 = $(a).find(".pepro-one-page-purchase--product-sale_price").data("raw");
    return (str1 > str2) ? 1 : -1;

  }
});
