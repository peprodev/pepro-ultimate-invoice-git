 (function($) {
   $(document).ready(function() {
     if (_l10n.zephyrfix) {
       $("link#us-core-admin-css").remove();
       $(".usof-colpick").remove();
     }
     var imagePreviewInlineStyle = 'height: 5rem;border-radius: 5px;cursor: zoom-in;';
     var imagePreviewRemoveInlineStyle = 'position: absolute;margin-inline: -0.5rem; margin-block: -0.2rem; text-decoration: none;color: white;background: red;border-radius: 100%;display: inline-block;width: 15px;height: 15px;text-align: center;line-height: 13px;';
     if (_l10n.darkmode) {
       $("html").addClass("dark");
     }

     $(".wc-select-uploader").each(function(n, i) {
       $this = $(this);
       if ($(this).val() !== "") {
         imgurl = $(this).val();
         $(this).parent().append($(`
           <img src="${imgurl}" rel="wppopup" style="${imagePreviewInlineStyle}" />
           <a href="#" class="clearmedia"
            style="${imagePreviewRemoveInlineStyle}"
            title="${_l10n.clear}" data-ref="#${$this.attr("id")}">&times;</a>
           `));
       }
       $(this).after(`<a class='button button-primary labelforinput' >${_l10n.selectbtn}</a>`);
     });
     $(".wc-color-picker").wpColorPicker();

     var $this = $("#puiw_template");
     if ($this.length > 0) {
       $this.parent().find("img, .clearmedia").remove();
       image_url = `${_l10n.plugin_url}/template/${$this.val()}/screenshot.png`;
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

     $(document).on("change", "#puiw_template", function(e) {
       e.preventDefault();
       let $this = $(this);
       $this.parent().find("img, .clearmedia").remove();
       image_url = `${_l10n.plugin_url}/template/${$this.val()}/screenshot.png`;
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
       tb_show(_l10n.currentlogo, me.attr("src"));
     });

     $(document).on("click tap", ".wc-select-uploader", function(e) {
       e.preventDefault();
       var $this = $(this);
       var image = wp.media({
         title: _l10n.title,
         multiple: false,
         button: {
           text: _l10n.btntext
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
            title="${_l10n.clear}" data-ref="#${$this.attr("id")}">&times;</a>
           `));
       });
     });

     /*end*/
   });
 })(jQuery);
