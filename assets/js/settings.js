(function ($) {
  $(document).ready(function () {
    $(document).on("click tap", "#devopt", function (e) {
      e.preventDefault();
      if (!$("dev").is(":visible")){
        $("dev").fadeIn();
        scroll($("dev h4"), 30);
      }else{
        scroll($("body"),100);
        setTimeout(() => {
          $("dev").fadeOut();
        }, 550);
      }

    });
    function scroll(e, of = 0) {
      $('html, body').animate({
        scrollTop: e.offset().top - of
      }, 500);
    }
  });
})(jQuery);
