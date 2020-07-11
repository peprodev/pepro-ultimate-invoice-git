jQuery(document).ready(function ($) {
  var peproOnePagePurchaseRequest = null;
  const LABELOKAY = _i18n.okaylabel;
  $(document).on("click tap", "#pepro-one-page-purchase--submit-invoice", function (e) {
    e.preventDefault();
    $("#pepro-one-page-purchase--submit-invoice>span.fa").css("display", "inline-block");
    if (peproOnePagePurchaseRequest != null) { peproOnePagePurchaseRequest.abort(); }
    if ($("button[type=submit][name=update_cart]").prop("disabled") === false){
      $("button[type=submit][name=update_cart]").click();
      return false;
    }
    peproOnePagePurchaseRequest = $.ajax({
      type: "POST",
      dataType: "json",
      url: _i18n.ajax,
      data: {
        action: _i18n.td,
        nonce: _i18n.nonce,
        wparam: "place-order",
      },
      success: function (rdata) {
        $("#pepro-one-page-purchase--submit-invoice>span.fa").hide();
        // console.log(rdata);
        if (rdata.success == true) {
          $(document).trigger("order_ajax_request_success", rdata.data);
          window.location.href = rdata.data.url;
        } else {
          $(document).trigger("order_ajax_request_failed", rdata.data);
          $.alert(rdata.data.msg,"", null , LABELOKAY);
          $(".modal-button").first().focus();
        }
      }
    });
  });
});
