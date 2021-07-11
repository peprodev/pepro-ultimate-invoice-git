/**
 * @Author: Amirhosseinhpv
 * @Date:   2020/10/20 22:23:23
 * @Email:  its@hpv.im
 * @Last modified by:   Amirhosseinhpv
 * @Last modified time: 2021/04/15 12:32:30
 * @License: GPLv2
 * @Copyright: Copyright © 2020 Amirhosseinhpv, All rights reserved.
 */


(function($) {
  var ULTIMATE_INVOICE_CURRENT_AJAX = null;
  var _pepro_ajax_request = null;
  isRTL = _i18n.rtl === 1 ? true : false;
  $checkboxforpdf = `<p>
  <input class='pdfattachment' id='pdfattachment' type='checkbox' />
  <label style="user-select: none;-moz-user-select: none;-ms-user-select: none;-webkit-user-select: none;" for='pdfattachment'>${_i18n.attach}</label>
  </p>`;
  errorTxt = _i18n.errorTxt;
  cancelTtl = _i18n.cancelTtl;
  confirmTxt = _i18n.confirmTxt;
  successTtl = _i18n.successTtl;
  submitTxt = _i18n.submitTxt;
  okTxt = _i18n.okTxt;
  closeTxt = _i18n.closeTxt;
  cancelbTn = _i18n.cancelbTn;
  sendTxt = _i18n.sendTxt;
  titleTx = _i18n.titleTx;
  expireNowE = _i18n.expireNowE;
  txtYes = _i18n.txtYes;
  txtNop = _i18n.txtNop;
  jconfirm.defaults = {
    title: '',
    titleClass: '',
    type: 'blue', // red green orange blue purple dark
    typeAnimated: true,
    draggable: true,
    dragWindowGap: 15,
    dragWindowBorder: true,
    animateFromElement: true,
    smoothContent: true,
    content: '',
    buttons: {},
    defaultButtons: {
      ok: {
        keys: ['enter'],
        text: okTxt,
        action: function() {}
      },
      close: {
        keys: ['enter'],
        text: closeTxt,
        action: function() {}
      },
      cancel: {
        keys: ['esc'],
        text: cancelbTn,
        action: function() {}
      },
    },
    contentLoaded: function(data, status, xhr) {},
    icon: '',
    lazyOpen: false,
    bgOpacity: null,
    theme: 'modern', /*light dark supervan material bootstrap modern*/
    animation: 'scale',
    closeAnimation: 'scale',
    animationSpeed: 400,
    animationBounce: 1,
    rtl: $("body").is(".rtl") ? true : false,
    container: 'body',
    containerFluid: false,
    backgroundDismiss: false,
    backgroundDismissAnimation: 'shake',
    autoClose: false,
    closeIcon: null,
    closeIconClass: false,
    watchInterval: 100,
    columnClass: 'm',
    boxWidth: '500px',
    scrollToPreviousElement: true,
    scrollToPreviousElementAnimate: true,
    useBootstrap: false,
    offsetTop: 40,
    offsetBottom: 40,
    bootstrapClasses: {
      container: 'container',
      containerFluid: 'container-fluid',
      row: 'row',
    },
    onContentReady: function() {},
    onOpenBefore: function() {},
    onOpen: function() {},
    onClose: function() {},
    onDestroy: function() {},
    onAction: function() {},
    escapeKey: true,
  };
  $(document).ready(function() {

    var imagePreviewInlineStyle = 'height: 5rem;border-radius: 5px;cursor: zoom-in;';
    var imagePreviewRemoveInlineStyle = ' position: relative; text-decoration: none; color: white; background: red; border-radius: 100%; display: block; width: 15px; height: 15px; text-align: center; line-height: 13px; align-self: baseline; margin-inline-start: -3rem; margin-block-start: -0.3rem; ';

    var $_railalign = $("body").is(".rtl") ? "left" : "right";
    $(".ultimate_invoice.column-ultimate_invoice").addClass("no-link");
    $("[rel=puiw_tooltip]").tipTip();
    $("p._shipping_puiw_invoice_shipdaterow_field").append($("#puiw_DateSelectorContainer").html());

    var prevdata = $("#_shipping_puiw_invoice_shipdate").val();
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var today = `${yyyy}-${mm}-${dd}`;
    if ($.trim(prevdata) == "") {
      prevdata = today;
    }
    defaultdate_format = "YYYY-MM-DD";
    var day = new pu_persianDate(Date.parse(prevdata));

    $("#puiw_DateContainer").data("date", prevdata);

    $("#puiw_DateContainer").pu_pDatepicker({
      onSelect: function(unix) {
        var day = new pu_persianDate(unix);
        $("#_shipping_puiw_invoice_shipdatefa").val(day.toCalendar('persian').toLocale('en').format(defaultdate_format));
        $("#_shipping_puiw_invoice_shipdate").val(day.toCalendar('gregorian').toLocale('en').format(defaultdate_format));
      },
      inline: 1,
      viewMode: "day",
      format: defaultdate_format,
      initialValue: true,
      initialValueType: "gregorian",
      minDate: null,
      maxDate: null,
      autoClose: false,
      position: "auto",
      onlyTimePicker: false,
      onlySelectOnDate: true,
      calendarType: _i18n.calendarType,
      inputDelay: 800,
      observer: 1,
      calendar: {
        persian: {
          locale: "fa",
          leapYearMode: "algorithmic",
          showHint: 1,
        },
        gregorian: {
          locale: "en",
          showHint: true
        }
      },
      navigator: {
        enabled: true,
        scroll: { enabled: false },
        text: {
          btnNextText: isRTL ? ">" : "<",
          btnPrevText: isRTL ? "<" : ">"
        },
      },
      toolbox: {
        enabled: true,
        calendarSwitch: {
          enabled: true,
          format: "MMMM",
          onSwitch: function(e) {
            unix = e.api.model.state.selected.unixDate;
            var day = new pu_persianDate(unix);
            $("#_shipping_puiw_invoice_shipdatefa").val(day.toCalendar('persian').toLocale('en').format(defaultdate_format));
            $("#_shipping_puiw_invoice_shipdate").val(day.toCalendar('gregorian').toLocale('en').format(defaultdate_format));
          },
        },
        todayButton: {
          enabled: true,
          text: {
            fa: _i18n.tr_today,
            en: "Today"
          }
        },
        submitButton: {
          enabled: true,
          text: {
            fa: _i18n.tr_submit,
            en: "Submit"
          },
          onSubmit: function(e) {},
        },
        onToday: function(datepickerObject) {
          var day = new pu_persianDate();
          $("#_shipping_puiw_invoice_shipdatefa").val(day.toCalendar('persian').toLocale('en').format(defaultdate_format));
          $("#_shipping_puiw_invoice_shipdate").val(day.toCalendar('gregorian').toLocale('en').format(defaultdate_format));
          return false;
        },
        text: {
          btnToday: _i18n.tr_today
        }
      },
      timePicker: {
        enabled: false,
      },
      dayPicker: {
        enabled: true,
        titleFormat: "YYYY MMMM"
      },
      monthPicker: {
        enabled: true,
        titleFormat: "YYYY"
      },
      yearPicker: {
        enabled: true,
        titleFormat: "YYYY"
      },
      persianDigit: false,
      responsive: true,
    });

    if ($("#puiw_DateContainer").length){
      $("#puiw_DateContainer").before(`<p>${_i18n.shipping_procc} <a href="#" class='clear_shipped_date'>${_i18n.shipping_clear}</a></p>`)
    }

    if (imgurl = _i18n.prev_img_url) {
      $url = $(".order_data_column div.address").find(`p:contains("${_i18n.prev_img_url}")`);
      if ($url) {
        content = $url.html();
        imgPreview = `<img src="${imgurl}" rel="wppopup" style="${imagePreviewInlineStyle}" />`;
        newContent = content.replace(imgurl, imgPreview);
        $url.html(newContent);
      }
    }

    $(".wc-select-uploader").each(function(n, i) {
      $this = $(this);
      if ($(this).val() !== "") {
        imgurl = $(this).val();
        $(this).parent().append($(`
          <img src="${imgurl}" rel="wppopup" style="${imagePreviewInlineStyle}" />
          <a href="#" class="clearmedia"
           style="${imagePreviewRemoveInlineStyle}"
           title="${_i18n.clear}" data-ref="#${$this.attr("id")}">&times;</a>
          `));
      }
      $(this).after(`<button class='button button-primary labelforinput' >${_i18n.selectbtn}</button>`);
    });

    $("p.puiw_shopmngr_provided_note.preview").appendTo($(".order_data_column:nth-of-type(3) div.address"));
    $("p.puiw_shopmngr_provided_note.edit").appendTo($(".order_data_column:nth-of-type(3) div.edit_address"));
    $("p.puiw_shopmngr_provided_note.edit *").show();

    $(document).on("click tap",".clear_shipped_date",function(e){
      e.preventDefault();
      var me = $(this);
      $("#_shipping_puiw_invoice_shipdatefa, #_shipping_puiw_invoice_shipdate").val("");
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
      tb_show(_i18n.currentlogo, me.attr("src"));
    });

    $(document).on("click tap", ".wc-select-uploader", function(e) {
      e.preventDefault();
      var $this = $(this);
      var image = wp.media({
        title: _i18n.title,
        multiple: false,
        button: {
          text: _i18n.btntext
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
           title="${_i18n.clear}" data-ref="#${$this.attr("id")}">&times;</a>
          `));
      });
    });

    $(document).on("click tap", ".button.pwui_opts.maincog", function(e) {
      e.preventDefault();
      let me = $(this);
      var order_id = me.data("ref");
      puiw_hide_all();
      $(`.pwui_overlyblockui[data-ref=${order_id}], .pwui_overly[data-ref=${order_id}]`).show();
    });

    $(document).on("click tap", ".puiw_close_overly, .pwui_overlyblockui", function(e) {
      e.preventDefault();
      puiw_hide_all();
    });

    $(document).on("click tap", "ul.inner_content li a[data-action], #pepro-ultimate-invoice p a[data-action]", function(e) {
      e.preventDefault();
      let me = $(this);
      if (me.data("action") !== "") {
        var order_id = me.data("ref");
        switch (me.data("action")) {
          case "puiw_act1":
            puiw_act1(order_id);
            break;
          case "puiw_act2":
            puiw_act2(order_id);
            break;
          case "puiw_act3":
            puiw_act3(order_id);
            break;
          case "puiw_act4":
            puiw_act4(order_id);
            break;
          case "puiw_act5":
            puiw_act5(order_id);
            break;
          case "puiw_act6":
            puiw_act6(order_id);
            break;
          case "puiw_act7":
            puiw_act7(order_id);
            break;
          case "puiw_act8":
            puiw_act8(order_id);
            break;
          case "puiw_act9":
            puiw_act9(order_id);
            break;
          case "puiw_act10":
            puiw_act10(order_id);
            break;
          case "puiw_act11":
            puiw_act11(order_id);
            break;
          case "puiw_act12":
            puiw_act12(order_id);
            break;
          case "puiw_act13":
            puiw_act13(order_id);
            break;
          case "puiw_act14":
            puiw_act14(order_id);
            break;
          case "puiw_act_href":
              url = me.attr("href");
              if ($("#puiwc_advanced_opts").prop("checked")){
                var theme_path = encodeURIComponent(window.btoa($("#puiw_metabox_theme_select").val()));
                var theme_color = encodeURIComponent(window.btoa($("input#puiw_metabox_theme_color").val()));
                var theme_color2 = encodeURIComponent(window.btoa($("input#puiw_metabox_theme_color2").val()));
                var theme_color3 = encodeURIComponent(window.btoa($("input#puiw_metabox_theme_color3").val()));
                url = `${url}&pclr=${theme_color}&sclr=${theme_color2}&tclr=${theme_color3}&tp=${theme_path}`;
              }
              window.open(url);
            break;
          default:
        }
      }
    });

    $(document).on("click tap", "#editpuiw_billing_uin", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(2) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("#_billing_puiw_billing_uin").focus();
    });

    $(document).on("click tap", "#editpuiw_billing_transaction_id", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(2) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("#_transaction_id").focus();
    });

    $(document).on("click tap", "#editpuiw_invoice_track_id", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(3) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("#_shipping_puiw_invoice_track_id").focus();
    });

    $(document).on("click tap", "#editpuiw_invoice_shipdate", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(3) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("#puiw_DateContainer table td.selected").focus();
    });

    $(document).on("click tap", "#editpuiw_invoice_customer_signature", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(3) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("p._shipping_puiw_customer_signature_field button.labelforinput").focus();
    });

    $(document).on("click tap", "#editpuiw_invoice_customer_note", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(3) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("#excerpt").focus();
    });

    $(document).on("click tap", "#editpuiw_invoice_shop_manager_note", function(e) {
      e.preventDefault();
      var editbtn = $(".order_data_column:nth-of-type(3) a.edit_address");
      if (editbtn.is(":visible")) {
        editbtn.click();
      }
      $("#puiw_shopmngr_provided_note").focus();
    });

    $(document).on("click tap", ".puiw_back_overly", function(e) {
      e.preventDefault();
      let me = $(this);
      var order_id = me.data("ref");
      $(`.pwui_overlyblockui[data-ref=${order_id}], .pwui_overly[data-ref=${order_id}]`).show();
      puiw_hide_all(`.pwui_ajax_data[data-ref],`);
    });

    $(document).on("click tap", ".puiw_print_overly", function(e) {
      e.preventDefault();
      let me = $(this);
      var lparam = me.data("ref");
      document.getElementById(`dataajaxloaded_${lparam}`).contentWindow.print();

    });

    $(document).on("click tap", ".puiw_open_newtab", function(e) {
      e.preventDefault();
      let me = $(this);
      var lparam = me.data("ref");
      window.open($(`iframe#dataajaxloaded_${lparam}`).attr("src"));

    });

    $(document).on("click tap", ".puiw_download_pdf", function(e) {
      e.preventDefault();
      let me = $(this);
      var lparam = me.data("ref");
      window.open(_i18n.home + "/?invoice-pdf=" + lparam + "&download=1");
    });

    var DEFAULT_SWATACHES = `[{"n":"Smoke","p":"#9E9E9E","s":"#A6A6A6","t":"#B3B3B3"},{"n":"Mango","p":"#FFCC80","s":"#FCD59A","t":"#FFDFB0"},{"n":"Gold","p":"#FAEE84","s":"#FFF59D","t":"#FFF8B5"},{"n":"Grass","p":"#A5D6A7","s":"#AFE0B1","t":"#C3EBC5"},{"n":"Sea","p":"#90CAF9","s":"#A6D5FC","t":"#B5DEFF"},{"n":"Peach","p":"#EF9A9A","s":"#F5ABAB","t":"#F0BBBB"}]`;

    if (_i18n.load_themes){
      themes = _i18n.load_themes;
      get_template = _i18n.get_template; ind=0;
      styles = '';
      $.each(themes, function(index, val) {
        var $elid = 'th_puiw_' + Math.floor(Math.random() * 26) + Date.now() + ind++;
        var sel = "";
        if (get_template == val.path){ sel = "selected='selected'"; }
        $(".jqui-select").append(`<option class="th_puiw_selector" ${sel} data-name="${val.name}" data-version="${val.version}" data-author="${val.author}" data-uniq="${$elid}" data-icon="${val.icon}" value="${val.path}">${val.name}</option>`);
        styles += `
          \n/* style for theme selector -- title: ${val.name}*/
          ul.select2-results__options li[id$='${val.path}']{
            background: white url('${val.icon}') no-repeat center left;
            background-position-x: 4px;
            background-size: 64px;
            height: 64px;
            line-height: 64px;
            padding-inline-start: 74px;
            font-style: italic;
            font-weight: bold;
          }
          [dir=rtl] ul.select2-results__options li[id$='${val.path}']{
            background-position-x: calc(100% - 4px);
          }
          ul.select2-results__options li[id$='${val.path}'].select2-results__option--highlighted{
            background-color:#0073aa;
            color: white;
          }\n`;
      });
      $("body").append(`<style>${styles}</style>`);
    }
    $("select#puiw_metabox_theme_select").addClass("wc-enhanced-select");

    $("select#puiw_metabox_swatch_select").each(function(index, select) {
      var swatches = $(select).attr("swatches");
      if (!swatches){swatches = DEFAULT_SWATACHES;}
      $(select).empty();
      try {var dJSON = $.parseJSON(swatches);}
      catch (err) {var dJSON = DEFAULT_SWATACHES;}
      styles = '';
      $.each(dJSON, function(index, val) {
        var $elidraw = Math.floor(Math.random() * 26) + Date.now() + index++;
        var $elid = 'eu_puiw_' + $elidraw;
        el = $(select).append(`<option data-uniq="${$elid}" data-p="${val.p}" data-s="${val.s}" data-t="${val.t}" value="${$elid}">${val.n}</option>`);
        styles += `
          \n/* style for swatch -- item with colors: ${val.p}, ${val.s}, ${val.t} | title: ${val.n}*/
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
    $("select#puiw_metabox_swatch_select").addClass("wc-enhanced-select");
    $(document.body).trigger('wc-enhanced-select-init');

    $("[selecteditem]").each(function(index, val) {
      $(val).find("option").removeAttr("selected");
      $(val).find("option[value='"+$(val).attr("selecteditem")+"']").attr("selected");
      $(val).val($(val).attr("selecteditem"));
      $(val).trigger("change");
    });

    $(".wc-color-picker").wpColorPicker();

    $("#puiwc_advanced_opts").prop("checked",false).trigger("change");

    $(document).on("change","#puiw_metabox_swatch_select",function(e){
      e.preventDefault();
      var me = $(this).find("option:selected").first();
      $("input#puiw_metabox_theme_color").val(me.data("p")).trigger("change");
      $("input#puiw_metabox_theme_color2").val(me.data("s")).trigger("change");
      $("input#puiw_metabox_theme_color3").val(me.data("t")).trigger("change");
    });

    $(document).on("click tap change",".pwui_reset_advanced",function(e){
      e.preventDefault();
      $("select#puiw_metabox_swatch_select").val("").trigger("change");
      $("select#puiw_metabox_theme_select").val(_i18n.get_template).trigger("change");
      $("input#puiw_metabox_theme_color").val(_i18n.theme_color).trigger("change");
      $("input#puiw_metabox_theme_color2").val(_i18n.theme_color2).trigger("change");
      $("input#puiw_metabox_theme_color3").val(_i18n.theme_color3).trigger("change");
    });

    $(document).on("click tap change","#puiwc_advanced_opts",function(e){
      var me = $(this);
      if (me.prop("checked")){
        $("div.advabced_puiwc").slideDown();
      }else{
        $("div.advabced_puiwc").slideUp();
      }
    });

    function _advanced_get_opts() {
      $data = false;
      if ($("#puiwc_advanced_opts").prop("checked")){
        $data = {
          "tp" : encodeURIComponent(window.btoa($("#puiw_metabox_theme_select").val())),
          "pclr" : encodeURIComponent(window.btoa($("input#puiw_metabox_theme_color").val())),
          "sclr" : encodeURIComponent(window.btoa($("input#puiw_metabox_theme_color2").val())),
          "tclr" : encodeURIComponent(window.btoa($("input#puiw_metabox_theme_color3").val())),
        };
      }
      return $data;
    }

    /* Hide overly divs */
    function puiw_hide_all(defs = '.pwui_overly[data-ref], .pwui_overlyblockui[data-ref], .pwui_ajax_data[data-ref],') {
      if (ULTIMATE_INVOICE_CURRENT_AJAX != null) {
        ULTIMATE_INVOICE_CURRENT_AJAX.abort();
      }
      $(`${defs}.puiw_back_overly, .puiw_print_overly, .puiw_open_newtab, .puiw_download_pdf`).hide();
      $(`.pwui_ajax_data[data-ref]`).getNiceScroll().remove();
    }

    /* Get Packing Slips */
    function puiw_act2(id) {
      loading = `<div class="loadingio-spinner-dual-ring-raf87e8fn7f"><div class="ldio-ltr1g772pal"><div></div><div><div></div></div></div></div>`;
      $(`.pwui_overly, .piuw_toolkit a.secondary`).hide();
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).html(loading);
      $(`.pwui_ajax_data[data-ref='${id}']`).show();
      if (ULTIMATE_INVOICE_CURRENT_AJAX != null) {
        ULTIMATE_INVOICE_CURRENT_AJAX.abort();
      }
      lparam = parseInt(id);
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).append(`<iframe style="display:none;" src="${_i18n.home + "/?invoice-slips=" + lparam}" id="dataajaxloaded_${lparam}"></iframe>`);
      $(`#dataajaxloaded_${lparam}`).on("load", function() {
        $(`.puiw_back_overly, .puiw_print_overly, .puiw_open_newtab`).fadeIn();
        $(".loadingio-spinner-dual-ring-raf87e8fn7f").hide();
        $(this).fadeIn();
      });
    }

    /* Get Inventory report */
    function puiw_act3(id) {
      loading = `<div class="loadingio-spinner-dual-ring-raf87e8fn7f"><div class="ldio-ltr1g772pal"><div></div><div><div></div></div></div></div>`;
      $(`.pwui_overly, .piuw_toolkit a.secondary`).hide();
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).html(loading);
      $(`.pwui_ajax_data[data-ref='${id}']`).show();
      if (ULTIMATE_INVOICE_CURRENT_AJAX != null) {
        ULTIMATE_INVOICE_CURRENT_AJAX.abort();
      }
      lparam = parseInt(id);
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).append(`<iframe style="display:none;" src="${_i18n.home + "/?invoice-inventory=" + lparam}" id="dataajaxloaded_${lparam}"></iframe>`);
      $(`#dataajaxloaded_${lparam}`).on("load", function() {
        $(`.puiw_back_overly, .puiw_print_overly, .puiw_open_newtab`).fadeIn();
        $(".loadingio-spinner-dual-ring-raf87e8fn7f").hide();
        $(this).fadeIn();
      });
    }

    /* Get HTML Invoice */
    function puiw_act4(id) {
      loading = `<div class="loadingio-spinner-dual-ring-raf87e8fn7f"><div class="ldio-ltr1g772pal"><div></div><div><div></div></div></div></div>`;
      $(`.pwui_overly, .piuw_toolkit a.secondary`).hide();
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).html(loading);
      $(`.pwui_ajax_data[data-ref='${id}']`).show();
      if (ULTIMATE_INVOICE_CURRENT_AJAX != null) {
        ULTIMATE_INVOICE_CURRENT_AJAX.abort();
      }
      lparam = parseInt(id);
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).append(`<iframe style="display:none;" src="${_i18n.home + "/?invoice=" + lparam}" id="dataajaxloaded_${lparam}"></iframe>`);
      $(`#dataajaxloaded_${lparam}`).on("load", function() {
        $(`.puiw_back_overly, .puiw_print_overly, .puiw_open_newtab`).fadeIn();
        $(".loadingio-spinner-dual-ring-raf87e8fn7f").hide();
        $(this).fadeIn();
      });
    }

    /* Get HTML Invoice Ajax*/
    function puiw_act4_(id) {
      loading = `<div class="loadingio-spinner-dual-ring-raf87e8fn7f"><div class="ldio-ltr1g772pal"><div></div><div><div></div></div></div></div>`;
      $(`.pwui_overly, .puiw_back_overly, .puiw_print_overly, .puiw_open_newtab, .puiw_download_pdf`).hide();
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).html(loading);
      $(`.pwui_ajax_data[data-ref='${id}']`).show();
      if (ULTIMATE_INVOICE_CURRENT_AJAX != null) {
        ULTIMATE_INVOICE_CURRENT_AJAX.abort();
      }
      ULTIMATE_INVOICE_CURRENT_AJAX = $.ajax({
        type: "POST",
        dataType: "json",
        url: _i18n.ajax,
        data: {
          action: _i18n.td,
          nonce: _i18n.nonce,
          wparam: (wparam = "puiw_act4"),
          lparam: (lparam = parseInt(id)),
        },
        success: function(rdata) {
          if (rdata.success == true) {
            htmldatta = rdata.data.html;
            $(document).trigger(`pwui_ajax_request_${wparam}_success`, rdata.data);
          } else {
            htmldatta = rdata.data.message;
            $(document).trigger(`pwui_ajax_request_${wparam}_failed`, rdata.data);
          }
          $(`.puiw_back_overly, .puiw_print_overly, .puiw_open_newtab, .puiw_download_pdf`).fadeIn();
          $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).html(`<iframe src="javascript:void(0);" id="dataajaxloaded_${lparam}"></iframe>`);
          $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data iframe`).contents().find('html').html(`${htmldatta}<br>`);
          $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data iframe`).niceScroll({
            autohidemode: true,
            enableobserver: true,
            scrollbarid: `pwui_ajax_data__${id}`,
            cursorwidth: "3.5px",
            zindex: "111115",
            cursorcolor: "var(--puiw--link)",
            cursorborder: "var(--puiw--link)",
            railalign: $_railalign,
          });
          $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).getNiceScroll().resize();
        }
      });
    }

    /* Get PDF Invoice */
    function puiw_act5(id) {
      loading = `<div class="loadingio-spinner-dual-ring-raf87e8fn7f"><div class="ldio-ltr1g772pal"><div></div><div><div></div></div></div></div>`;
      $(`.pwui_overly, .piuw_toolkit a.secondary`).hide();
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).html(loading);
      $(`.pwui_ajax_data[data-ref='${id}']`).show();
      if (ULTIMATE_INVOICE_CURRENT_AJAX != null) {
        ULTIMATE_INVOICE_CURRENT_AJAX.abort();
      }
      lparam = parseInt(id);
      $(`.pwui_ajax_data[data-ref='${id}'] .ajax_data`).append(`<iframe style="display:none;" src="${_i18n.home + "/?invoice-pdf=" + lparam}" id="dataajaxloaded_${lparam}"></iframe>`);
      $(`#dataajaxloaded_${lparam}`).on("load", function() {
        $(`.puiw_back_overly, .puiw_open_newtab, .puiw_download_pdf`).fadeIn();
        $(".loadingio-spinner-dual-ring-raf87e8fn7f").hide();
        $(this).fadeIn();
      });
    }

    /* Email Invoice to Customer */
    function puiw_act6(id) {
      var uID = id;
      var jc = $.confirm({
          title: _i18n.emailCustomerTitle,
          content: _i18n.emailCustomerAsk.replace("%s",`<u>${CURRENT_ORDER_MAIL[id]}</u>`) + $checkboxforpdf,
          boxWidth: '600px',
          icon: 'fa fa-envelope',
          closeIcon: true,
          animation: 'scale',
          buttons: {
            no: { text: txtNop, btnClass: 'btn-red', keys: ['n','esc'], action: function(){} },
            yes: { text: txtYes, btnClass: 'btn-red', keys: ['y','enter'], action: function() {
                $(".jconfirm-closeIcon").hide();
                jc.showLoading(true);
                jc.setBoxWidth("400px");
                $.ajax({
                  type: 'POST',
                  dataType: "json",
                  url: _i18n.ajax,
                  data: {
                    action: _i18n.td,
                    nonce: _i18n.nonce,
                    wparam: "send-mail-html",
                    qparam: "",
                    dparam: $('input.pdfattachment').prop('checked') ? "PDF" : "",
                    lparam: id,
                    eparam: _advanced_get_opts(),
                  },
                  success: function(result) {
                    jc.close();
                    if (result.success === true){
                      $.confirm({ title: successTtl, content: result.data.msg , icon: 'fas fa-check-circle', type: 'green', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                    } else {
                      $.confirm({ title: errorTxt, content: result.data.msg , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                    }
                  }
                });
                return false;
            }},
          },
      });
    }

    /* Email Invoice to Shop Managers */
    function puiw_act9(id) {
      var uID = id;
      var jc = $.confirm({
          title: "",
          content: "",
          boxWidth: '400px',
          icon: 'fa fa-envelope',
          closeIcon: true,
          animation: 'scale',
          onOpenBefore: function () {
            $(".jconfirm-closeIcon, .jconfirm-buttons").hide();
            jc.showLoading(true);
            $.ajax({
              type: 'POST',
              dataType: "json",
              url: _i18n.ajax,
              data: {
                action: _i18n.td,
                nonce: _i18n.nonce,
                wparam: "retrive-admins-emails",
              },
              success: function(result) {
                jc.close();
                if (result.success === true){
                    CURRENT_ADMINS = '';
                    CURRENT_ADMINS_EMAILS = [];
                    $.each(result.data.emails,function(i,x){
                      CURRENT_ADMINS += `<a target='_blank' rel='puiw_tooltip' title='${x}<br>${i}' href='users.php?s=${i}'>${i}</a>, `;
                      CURRENT_ADMINS_EMAILS.push(i);
                    });
                    var jcv = $.confirm({
                        title: _i18n.emailShopMngrTitle,
                        content: _i18n.emailShopMngrAsk.replace("%s", `<div style="direction: ltr;">${CURRENT_ADMINS}</div>` + $checkboxforpdf),
                        boxWidth: '600px',
                        icon: 'fa fa-envelope',
                        closeIcon: true,
                        animation: 'scale',
                        onContentReady: function(){
                          $("[rel=puiw_tooltip]").tipTip();
                        },
                        buttons: {
                          no: { text: txtNop, btnClass: 'btn-red', keys: ['n','esc'], action: function(){} },
                          yes: { text: txtYes, btnClass: 'btn-red', keys: ['y','enter'], action: function() {
                              $(".jconfirm-closeIcon").hide();
                              jcv.showLoading(true);
                              jcv.setBoxWidth("400px");
                              $.ajax({
                                type: 'POST',
                                dataType: "json",
                                url: _i18n.ajax,
                                data: {
                                  action: _i18n.td,
                                  nonce: _i18n.nonce,
                                  wparam: "send-mail-html",
                                  qparam: CURRENT_ADMINS_EMAILS,
                                  dparam: $('input.pdfattachment').prop('checked') ? "PDF" : "",
                                  lparam: id,
                                  eparam: _advanced_get_opts(),
                                },
                                success: function(result) {
                                  jcv.close();
                                  if (result.success === true){
                                    $.confirm({ title: successTtl, content: result.data.msg , icon: 'fas fa-check-circle', type: 'green', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                                  } else {
                                    $.confirm({ title: errorTxt, content: result.data.msg , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                                  }
                                }
                              });
                              return false;
                          }},
                        },
                    });
                } else {
                  $.confirm({ title: errorTxt, content: result.data.msg , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                }
              }
            });
          },
          buttons: { wait:{} },
      });
    }

    /* Mail Invoice to custom list */
    function puiw_act10(id) {
      var uID = id;
      var jc = $.confirm({
          title: _i18n.emailCustomTitle,
          content: _i18n.emailCustomlistAsk +$checkboxforpdf+
            '<form action="" class="formName">' +
            '<input type="hidden" placeholder="Enter Addresses sepreated by comma ..." class="address" required />' +
            '</form>',
          boxWidth: '600px',
          icon: 'fa fa-envelope',
          closeIcon: true,
          animation: 'scale',
          onContentReady: function () {
            var jcw = this;
            this.$content.find('.address').multiple_emails({position: 'bottom',});
            this.$content.find('.multiple_emails-container').prepend(`<button class="button button-primary" style="float: inline-start; margin-top: 5px;">+</button>`);

            this.$content.find('form').on('submit', function (e) {
                e.preventDefault();
                // jcw.$$sendall.trigger('click');
            });
          },
          buttons: {
            cancel: { text: cancelbTn, btnClass: 'btn-red', keys: ['esc'], action: function(){} },
            sendall: { text: sendTxt, btnClass: 'btn-red', keys: ['enter'], action: function() {

                var name = this.$content.find('.address').val();
                if(!name || $.isEmptyObject(name) || !name.length || $.trim(name) == "[]"){
                    $.confirm({ title: errorTxt, content: _i18n.anEmailisrequid , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                    return false;
                }

                $(".jconfirm-closeIcon").hide();
                jc.showLoading(true);
                jc.setBoxWidth("400px");
                $.ajax({
                  type: 'POST',
                  dataType: "json",
                  url: _i18n.ajax,
                  data: {
                    action: _i18n.td,
                    nonce: _i18n.nonce,
                    wparam: "send-mail-html",
                    qparam: JSON.parse(name),
                    dparam: $('input.pdfattachment').prop('checked') ? "PDF" : "",
                    lparam: id,
                    eparam: _advanced_get_opts(),
                  },
                  success: function(result) {
                    jc.close();
                    if (result.success === true){
                      $.confirm({ title: successTtl, content: result.data.msg , icon: 'fas fa-check-circle', type: 'green', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                    } else {
                      $.confirm({ title: errorTxt, content: result.data.msg , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                    }
                  }
                });
                return false;
            }},
          },
      });
    }
  });
})(jQuery);
