jQuery(document).ready(function($) {
  $(document).on("click tap", "#pepro-one-page-purchase--submit-invoice", function(e) {
    e.preventDefault();
    LABELOKAY = _i18n.okaylabel;
    $.alert(`${_i18n.msg}`, `${_i18n.title}`, null , LABELOKAY);
    $(".modal-button").first().focus();
  });
});
