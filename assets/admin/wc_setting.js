/**
 * @Author: Amirhosseinhpv
 * @Date:   2020/10/20 22:23:23
 * @Email:  its@hpv.im
 * @Last modified by:   Amirhosseinhpv
 * @Last modified time: 2021/07/12 20:48:49
 * @License: GPLv2
 * @Copyright: Copyright © 2020 Amirhosseinhpv, All rights reserved.
 */
(function($) {
  $(document).ready(function() {
    if (_peproUltimateInvoice.zephyrfix) {
      $("link#us-core-admin-css").remove();
      $(".usof-colpick").remove();
    }
    var imagePreviewInlineStyle = 'height: 2rem;border-radius: 4px;cursor: zoom-in;';
    var imagePreviewRemoveInlineStyle = 'position: absolute;margin-inline: -0.5rem; margin-block: -0.2rem; text-decoration: none;color: white;background: red;border-radius: 100%;display: inline-block;width: 15px;height: 15px;text-align: center;line-height: 13px;';
    if ("yes" == _peproUltimateInvoice.darkmode) {
      $("html").addClass("dark");
    }

    // $=jQuery; $("table [id]").each(function(i,j){$(this).parents("tr").find("th").append($("<id>"+$(j).attr("id")+"</id>").css({"display": "block","font-family": "Consolas","background": "#dad9d9"}));})

    $(".wc-select-uploader").each(function(n, i) {
      $this = $(this);
      if ($(this).val() !== "") {
        imgurl = $(this).val();
        $(this).parent().append($(`
           <img src="${imgurl}" rel="wppopup" style="${imagePreviewInlineStyle}" />
           <a href="#" class="clearmedia"
            style="${imagePreviewRemoveInlineStyle}"
            title="${_peproUltimateInvoice.clear}" data-ref="#${$this.attr("id")}">&times;</a>
           `));
      }
      $(this).after(`<a class='button button-primary labelforinput' >${_peproUltimateInvoice.selectbtn}</a>`);
    });

    if ($(".puiw_swatch_one").length) {
      $parent = $("input.puiw_swatch_one").parents("tr").first();
      $parent_two = $("input.puiw_swatch_two").parents("td").first();
      $parent_two_p = $("input.puiw_swatch_two").parents("tr").first();
      $parent_three = $("input.puiw_swatch_three").parents("td").first();
      $parent_three_p = $("input.puiw_swatch_three").parents("tr").first();
      $parent_two.appendTo($parent);
      $parent_three.appendTo($parent);
      $parent_two_p.remove();
      $parent_three_p.remove();
    }

    if ($(".puiw_preinvoice_swatch_one").length) {
      $parent = $("input.puiw_preinvoice_swatch_one").parents("tr").first();
      $parent_two = $("input.puiw_preinvoice_swatch_two").parents("td").first();
      $parent_two_p = $("input.puiw_preinvoice_swatch_two").parents("tr").first();
      $parent_three = $("input.puiw_preinvoice_swatch_three").parents("td").first();
      $parent_three_p = $("input.puiw_preinvoice_swatch_three").parents("tr").first();
      $parent_two.appendTo($parent);
      $parent_three.appendTo($parent);
      $parent_two_p.remove();
      $parent_three_p.remove();
    }

    $(".wc-color-picker").wpColorPicker();

    var $this = $("#puiw_template");
    if ($this.length > 0) {
      $this.parent().find("img, .clearmedia").remove();
      image_url = ``;
      $.each(_peproUltimateInvoice.themeData, function(index, val) {
        if (val.path == $this.val()) {
          image_url = val.icon;
        }
      });
      $this.parent().append($(`<img src="${image_url}" rel="wppopup" style="${imagePreviewInlineStyle}" />`));
    }
    var $this = $("#puiw_preinvoice_template");
    if ($this.length > 0) {
      $this.parent().find("img, .clearmedia").remove();
      image_url = ``;
      $.each(_peproUltimateInvoice.themeData, function(index, val) {
        if (val.path == $this.val()) {
          image_url = val.icon;
        }
      });
      $this.parent().append($(`<img src="${image_url}" rel="wppopup" style="${imagePreviewInlineStyle}" />`));
    }

    $manual = $("input[name=puiw_send_invoices_via_email]:checked").val();
    if ($manual && "automatic" == $manual) {
      $("#puiw_send_invoices_via_email_opt").parents("tr").last().show();
    } else {
      $("#puiw_send_invoices_via_email_opt").parents("tr").last().hide();
    }
    $(document).on("change", "input[name=puiw_send_invoices_via_email]", function(e) {
      $manual = $("input[name=puiw_send_invoices_via_email]:checked").val();
      if ($manual && "automatic" == $manual) {
        $("#puiw_send_invoices_via_email_opt").parents("tr").last().show();
      } else {
        $("#puiw_send_invoices_via_email_opt").parents("tr").last().hide();
      }
    });


    $manual = $("input[name=puiw_send_invoices_via_email_admin]:checked").val();
    if ($manual && "automatic" == $manual) {
      $("#puiw_send_invoices_via_email_shpmngrs").parents("tr").last().show();
      $("#puiw_send_invoices_via_email_opt_admin").parents("tr").last().show();
    } else {
      $("#puiw_send_invoices_via_email_shpmngrs").parents("tr").last().hide();
      $("#puiw_send_invoices_via_email_opt_admin").parents("tr").last().hide();
    }
    $(document).on("change", "input[name=puiw_send_invoices_via_email_admin]", function(e) {
      $manual = $("input[name=puiw_send_invoices_via_email_admin]:checked").val();
      if ($manual && "automatic" == $manual) {
        $("#puiw_send_invoices_via_email_shpmngrs").parents("tr").last().show();
        $("#puiw_send_invoices_via_email_opt_admin").parents("tr").last().show();
      } else {
        $("#puiw_send_invoices_via_email_shpmngrs").parents("tr").last().hide();
        $("#puiw_send_invoices_via_email_opt_admin").parents("tr").last().hide();
      }
    });


    $manual = $("input#puiw_allow_users_use_invoices").prop("checked");
    if ($manual) {
      $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().show();
    } else {
      $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().hide();
    }
    $(document).on("change", "input#puiw_allow_users_use_invoices", function(e) {
      $manual = $(this).prop("checked");
      if ($manual) {
        $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().show();
      } else {
        $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().hide();
      }
    });


    $manual = $("input#puiw_allow_guest_users_view_invoices").prop("checked");
    if ($manual) {
      $("#puiw_allow_pdf_guest").parents("tr").last().show();
    } else {
      $("#puiw_allow_pdf_guest").parents("tr").last().hide();
    }
    $(document).on("change", "input#puiw_allow_guest_users_view_invoices", function(e) {
      $manual = $(this).prop("checked");
      if ($manual) {
        $("#puiw_allow_pdf_guest").parents("tr").last().show();
      } else {
        $("#puiw_allow_pdf_guest").parents("tr").last().hide();
      }
    });


    $manual = $("input#puiw_allow_preorder_invoice").prop("checked");
    if ($manual) {
      $("#puiw_allow_preorder_emptycart").parents("tr").last().show();
      $("#puiw_preorder_shopmngr_extra_note").parents("tr").last().show();
      $("#puiw_preorder_customer_extra_note").parents("tr").last().show();
    } else {
      $("#puiw_allow_preorder_emptycart").parents("tr").last().hide();
      $("#puiw_preorder_shopmngr_extra_note").parents("tr").last().hide();
      $("#puiw_preorder_customer_extra_note").parents("tr").last().hide();
    }
    $(document).on("change", "input#puiw_allow_preorder_invoice", function(e) {
      $manual = $(this).prop("checked");
      if ($manual) {
        $("#puiw_allow_preorder_emptycart").parents("tr").last().show();
        $("#puiw_preorder_shopmngr_extra_note").parents("tr").last().show();
        $("#puiw_preorder_customer_extra_note").parents("tr").last().show();
      } else {
        $("#puiw_allow_preorder_emptycart").parents("tr").last().hide();
        $("#puiw_preorder_shopmngr_extra_note").parents("tr").last().hide();
        $("#puiw_preorder_customer_extra_note").parents("tr").last().hide();
      }
    });


    $manual = $("input#puiw_allow_users_have_invoices").prop("checked");
    if ($manual) {
      $("#puiw_allow_pdf_customer").parents("tr").last().show();
      $("#puiw_allow_users_use_invoices").parents("tr").last().show();
      $manual = $("input#puiw_allow_users_use_invoices").prop("checked");
      if ($manual) {
        $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().show();
      } else {
        $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().hide();
      }
    } else {
      $("#puiw_allow_pdf_customer").parents("tr").last().hide();
      $("#puiw_allow_users_use_invoices").parents("tr").last().hide();
      $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().hide();
    }
    $(document).on("change", "input#puiw_allow_users_have_invoices", function(e) {
      $manual = $(this).prop("checked");
      if ($manual) {
        $("#puiw_allow_pdf_customer").parents("tr").last().show();
        $("#puiw_allow_users_use_invoices").parents("tr").last().show();
        $manual = $("input#puiw_allow_users_use_invoices").prop("checked");
        if ($manual) {
          $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().show();
        } else {
          $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().hide();
        }
      } else {
        $("#puiw_allow_pdf_customer").parents("tr").last().hide();
        $("#puiw_allow_users_use_invoices").parents("tr").last().hide();
        $("#puiw_allow_users_use_invoices_criteria").parents("tr").last().hide();
      }
    });

    $("#puiw_send_invoices_via_email").parents("tr").last().css("border", "none");
    $("#puiw_send_invoices_via_email_admin").parents("tr").last().css("border", "none");

    $(document).on("click tap", "#wp-admin-bar-puiw_toolbar_dark_btn", function(e) {
      e.preventDefault();
      let me = $(this);
      $("html").toggleClass("dark");
    });

    $(document).on("change", "#puiw_template, #puiw_preinvoice_template", function(e) {
      e.preventDefault();
      let $this = $(this);
      $this.parent().find("img, .clearmedia").remove();
      image_url = ``;
      $.each(_peproUltimateInvoice.themeData, function(index, val) {
        if (val.path == $this.val()) {
          image_url = val.icon;
        }
      });
      $this.parent().append($(`<img src="${image_url}" rel="wppopup" style="${imagePreviewInlineStyle}" />`));
    });

    $(document).on("click tap", ".short-tags.button", function(e) {
      e.preventDefault();
      let me = $(this);
      $("#puiw_address_display_method").val($("#puiw_address_display_method").val() + me.text());
      $("#puiw_address_display_method").focus();
    });

    $(document).on("click tap", ".labelforinput", function(e) {
      e.preventDefault();
      let me = $(this);
      me.parent().find("input").first().click();
    });

    $(document).on("click tap", ".clearmedia", function(e) {
      e.preventDefault();
      let me = $(this);
      $(me.data("ref")).val("");
      me.parent().find("img, .clearmedia").remove();
    });

    $(document).on("click tap", "img[rel=wppopup]", function(e) {
      e.preventDefault();
      let me = $(this);
      tb_show(_peproUltimateInvoice.currentlogo, me.attr("src"));
    });

    $(document).on("click tap", ".wc-select-uploader", function(e) {
      e.preventDefault();
      var $this = $(this);
      var image = wp.media({
        title: _peproUltimateInvoice.title,
        multiple: false,
        button: {
          text: _peproUltimateInvoice.btntext
        }
      }).open().on('select', function(e) {
        var uploaded_image = image.state().get('selection').first();
        var image_url = uploaded_image.toJSON().url;
        $this.val(image_url);
        $this.parent().find("img, .clearmedia").remove();
        $this.parent().append($(`
           <img src="${image_url}" rel="wppopup" style="${imagePreviewInlineStyle}" />
           <a href="#" class="clearmedia"
            style="${imagePreviewRemoveInlineStyle}"
            title="${_peproUltimateInvoice.clear}" data-ref="#${$this.attr("id")}">&times;</a>
           `));
      });
    });

    $("[selecteditem]").each(function(index, val) {
      $(val).find("option").removeAttr("selected");
      $(val).find("option[value='" + $(val).attr("selecteditem") + "']").attr("selected");
      $(val).val($(val).attr("selecteditem"));
      $(val).trigger("change");
    });

    var DEFAULT_SWATACHES = `[{"n":"Smoke","p":"#9E9E9E","s":"#A6A6A6","t":"#B3B3B3"},{"n":"Mango","p":"#FFCC80","s":"#FCD59A","t":"#FFDFB0"},{"n":"Gold","p":"#FAEE84","s":"#FFF59D","t":"#FFF8B5"},{"n":"Grass","p":"#A5D6A7","s":"#AFE0B1","t":"#C3EBC5"},{"n":"Sea","p":"#90CAF9","s":"#A6D5FC","t":"#B5DEFF"},{"n":"Peach","p":"#EF9A9A","s":"#F5ABAB","t":"#F0BBBB"}]`;

    $("select.swatch-select").each(function(index, select) {
      var swatches = $(select).attr("swatches");
      if (!swatches) {
        swatches = DEFAULT_SWATACHES;
      }
      $(select).empty();
      try {
        var dJSON = $.parseJSON(swatches);
      } catch (err) {
        var dJSON = DEFAULT_SWATACHES;
      }
      styles = '';
      $.each(dJSON, function(index, val) {
        var $elidraw = Math.floor(Math.random() * 26) + Date.now() + index++;
        var $elid = 'eu_puiw_' + $elidraw;
        el = $(select).append(`<option data-uniq="${$elid}" data-p="${val.p}" data-s="${val.s}" data-t="${val.t}" value="${$elid}">${val.n}</option>`);
        styles += `
           \n/* style for swatch -- item with color: ${val.p}, ${val.s}, ${val.t} | title: ${val.n}*/
           ul.select2-results__options li[id$='${$elid}']{
             background:rgb(255,255,255);
             background:linear-gradient(90deg, rgba(255,255,255,1) 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%) !important;
             background:-moz-linear-gradient(90deg, rgba(255,255,255,1) 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             background:-webkit-linear-gradient(90deg, rgba(255,255,255,1) 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#1d8bfc",GradientType=1) !important;
           }
           [dir=rtl] ul.select2-results__options li[id$='${$elid}']{
             background:rgb(255,255,255);
             background:linear-gradient(-90deg, rgba(255,255,255,1) 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%) !important;
             background:-moz-linear-gradient(-90deg, rgba(255,255,255,1) 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             background:-webkit-linear-gradient(-90deg, rgba(255,255,255,1) 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#1d8bfc",GradientType=1) !important;
           }
           ul.select2-results__options li[id$='${$elid}'].select2-results__option--highlighted{
             background:#0073aa;
             background:linear-gradient(90deg, #0073aa, white 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%) !important;
             background:-moz-linear-gradient(90deg, #0073aa, white 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             background:-webkit-linear-gradient(90deg, #0073aa, white 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#1d8bfc",GradientType=1) !important;
           }
           [dir=rtl] ul.select2-results__options li[id$='${$elid}'].select2-results__option--highlighted{
             background:#0073aa;
             background:linear-gradient(-90deg, #0073aa, white 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%) !important;
             background:-moz-linear-gradient(-90deg, #0073aa, white 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             background:-webkit-linear-gradient(-90deg, #0073aa, white 70%, ${val.p} 70%, ${val.p} 80%, ${val.s} 80%, ${val.s} 90%, ${val.t} 90%, ${val.t} 100%);
             filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#1d8bfc",GradientType=1) !important;
           }\n`;
      });
      $("body").append(`<style>${styles}</style>`);
    });
    $("select.swatch-select").addClass("wc-enhanced-select");
    $(document.body).trigger('wc-enhanced-select-init');

    $(document).on("change", "#puiw_swatch", function(e) {
      e.preventDefault();
      var me = $(this).find("option:selected").first();
      $("input#puiw_theme_color").val(me.data("p")).trigger("change");
      $("input#puiw_theme_color2").val(me.data("s")).trigger("change");
      $("input#puiw_theme_color3").val(me.data("t")).trigger("change");
    });

    $(document).on("change", "#puiw_preinvoice_swatch", function(e) {
      e.preventDefault();
      var me = $(this).find("option:selected").first();
      $("input#puiw_preinvoice_theme_color").val(me.data("p")).trigger("change");
      $("input#puiw_preinvoice_theme_color2").val(me.data("s")).trigger("change");
      $("input#puiw_preinvoice_theme_color3").val(me.data("t")).trigger("change");
    });

  });
})(jQuery);
