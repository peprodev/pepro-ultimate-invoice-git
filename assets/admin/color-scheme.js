/**
 * @Author: Amirhosseinhpv
 * @Date:   2020/10/20 22:23:23
 * @Email:  its@hpv.im
 * @Last modified by:   Amirhosseinhpv
 * @Last modified time: 2021/07/12 21:32:13
 * @License: GPLv2
 * @Copyright: Copyright © Amirhosseinhpv (https://hpv.im), all rights reserved.
 */


(function($) {
  var names = [`${msg.primary}: `, `${msg.secondary}: `, `${msg.tertiary}: `,];
  var DEFAULT_SWATACHES = `[{"n":"Smoke","p":"#9E9E9E","s":"#A6A6A6","t":"#B3B3B3"},{"n":"Mango","p":"#FFCC80","s":"#FCD59A","t":"#FFDFB0"},{"n":"Gold","p":"#FAEE84","s":"#FFF59D","t":"#FFF8B5"},{"n":"Grass","p":"#A5D6A7","s":"#AFE0B1","t":"#C3EBC5"},{"n":"Sea","p":"#90CAF9","s":"#A6D5FC","t":"#B5DEFF"},{"n":"Peach","p":"#EF9A9A","s":"#F5ABAB","t":"#F0BBBB"}]`;
  var _pepro_ajax_request = null;
  $.fn.swatchify = function() {
    return this.each(function() {
      var target = $(this);
      var name = target.data('name');
      var colors = typeof target.data('colors') === 'string' ? target.data('colors').split(',') : target.data('colors');
      var width = 100 / colors.length + '%';
      var infoContents = $('<div/>', { class: 'shade', title: `${msg.click2change}` });
      var editColor = $('<div/>', { class: 'swatch-edit' });
      var holder = $('<div/>', { class: 'holder' });
      for (var i = 0; i < colors.length; i++) {
        infoContents.append($('<span/>', { class: 'name' }).css('width', width).text( names[i] + colors[i].toUpperCase()));
        editColor.append($('<input/>', { class: `color-picker nth-${i} editswatches`}).css({'width': "100%", 'margin': 0,}).val(colors[i].toUpperCase()).attr("original-color",colors[i].toUpperCase()));
        holder.append($('<span/>', { class: `color nth-${i}`, title: `${msg.click2change}`  }).css({ width: width, 'background-color': colors[i] }));
      }
      holder.append(infoContents);
      target.append(holder);
      target.prepend($('<div/>', { class: 'info' }).html(`<span class="swatch-name" title="${msg.click2change}">${name}</span><input title="${msg.swatchinp}" class="edit-swatch-name" value="${name}" />`).append(`<div class="swatches-tool">
      <span title="${msg.delete}"     class="btn swatches-trash dashicons dashicons-trash"></span>
      <span title="${msg.edit}"       class="btn swatches-edit dashicons dashicons-edit-large"></span>
      <span title="${msg.discard}"    class="btn swatches-discard dashicons dashicons-no"></span>
      <span title="${msg.apply}"      class="btn swatches-editsok dashicons dashicons-yes"></span>
      </div>`));
      holder.append(editColor);
      target.addClass('jqueryswatches');
    });
  };
  $.minicolors = {
    defaults: {
      animationSpeed: 50,
      animationEasing: 'swing',
      changeDelay: 0,
      control: 'hue',
      defaultValue: '',
      format: 'hex',
      hide: null,
      hideSpeed: 100,
      inline: false,
      keywords: '',
      letterCase: 'uppercase',
      opacity: false,
      position: 'bottom left',
      show: null,
      showSpeed: 100,
      theme: 'default',
      swatches: ["#ef9a9a","#90caf9","#a5d6a7","#fff59d","#ffcc80","#bcaaa4","#eeeeee","#f44336","#2196f3","#4caf50","#ffeb3b","#ff9800","#795548","#9e9e9e"]
    }
  };
  $(document).ready(function() {
    var color_scheme = {
      init: function() {
        that = this;

        $(document).on('mouseenter', '.jqueryswatches .holder', function() {
          $(this).find('.shade').stop().animate({
            height: '27px'
          }, 200);
          $(this).find('.name').stop().animate({
            opacity: '1'
          }, 200);})
        .on('mouseleave', '.jqueryswatches .holder', function() {
          $(this).find('.shade').stop().animate({
            height: '10px'
          }, 200);
          $(this).find('.name').stop().animate({
            opacity: '0'
          }, 200);
        });

        $(document).on("click tap",".swatches-trash",function(e){
          e.preventDefault();
          var me = $(this);
          me.parents(".swatch").first().addClass("highlight");
          var jc = $.confirm({
              title: _i18n.confirmTxt,
              content: _i18n.confirm_trash,
              type: "red",
              boxWidth: '600px', icon: 'fa fa-question-circle', closeIcon: true, animation: 'scale',
              buttons: { no: { text: txtNop, btnClass: 'btn-red', keys: ['n','esc'], action: function(){
                me.parents(".swatch").first().removeClass("highlight");
              } },
                yes: { text: txtYes, btnClass: 'btn-red', keys: ['enter'], action: function() {
                  me.parents(".swatch").first().remove();
                  that.update();
                }},
              }, });

        });

        $(document).on("keyup",".jqueryswatches input",function(e){
          e.preventDefault();
          var me = $(this);
          switch (e.keyCode) {
            case 27: //esc
                that.discard();
                break;
            case 13: // enter
                $(".swatches-editsok").click();
                break;
            default:
              // do nothing about it
          }
        });

        $(document).on("click tap",".swatches-edit",function(e){
          e.preventDefault();
          var me = $(this);
          that.discard();
          me.parents(".swatch").addClass("editingnow"),
          me.parents(".swatch").find(".swatch-edit,.edit-swatch-name").show(),
          me.parents(".swatch").find(".swatch-name").hide(),
          me.parents(".swatch").find(".edit-swatch-name").focus(),
          me.parents(".swatch").find(".btn.swatches-trash,.btn.swatches-edit").hide(),
          me.parents(".swatch").find(".btn.swatches-discard,.btn.swatches-editsok").show();
          $("input.editswatches").minicolors();
        });

        $(document).on("click tap",".swatch-name",function(e){
          e.preventDefault();
          var me = $(this);
          me.parents(".swatch").find(".swatches-edit").click();
        });

        $(document).on("click tap",".swatch:not(.editingnow) .holder span.color",function(e){
          e.preventDefault();
          var me = $(this);
          me.parents(".swatch").find(".swatches-edit").click();
          if (me.is(".nth-0")){
             me.parents(".swatch").find(".color-picker.editswatches.nth-0").focus();
          }
          if (me.is(".nth-1")){
             me.parents(".swatch").find(".color-picker.editswatches.nth-1").focus();
          }
          if (me.is(".nth-2")){
             me.parents(".swatch").find(".color-picker.editswatches.nth-2").focus();
          }
        });

        $(document).on("click tap",".swatch.editingnow .holder span.color",function(e){
          e.preventDefault();
          var me = $(this);
          if (me.is(".nth-0")){
             me.parents(".swatch").find(".color-picker.editswatches.nth-0").focus();
          }
          if (me.is(".nth-1")){
             me.parents(".swatch").find(".color-picker.editswatches.nth-1").focus();
          }
          if (me.is(".nth-2")){
             me.parents(".swatch").find(".color-picker.editswatches.nth-2").focus();
          }
        });

        $(document).on("click tap",".swatches-discard",function(e){
          e.preventDefault();
          var me = $(this);
          me.parents(".swatch").find(".edit-swatch-name").val(me.parents(".swatch").find(".swatch-name").text());
          me.parents(".swatch").find(".edit-swatch-name").hide().prev("span").show();

          me.parents(".swatch").find(".btn.swatches-trash,.btn.swatches-edit").show();
          $("input.editswatches").each(function(index, val) {
            $(val).val($(val).attr("original-color"));
          });
          $("input.minicolors-input").minicolors("destroy");
          me.parents(".swatch").find(".btn.swatches-discard,.btn.swatches-editsok,.swatch-edit").hide();
          that.update_colors();
        });

        $(document).on("keyup change cut paste","input.editswatches",function(e){
          var me = $(this);
          if ($.Color(me.val())) {
            that.update_colors();
          }
        });

        $(document).on("click tap",".swatches-editsok",function(e){
          e.preventDefault();
          var me = $(this);

          me.parents(".swatch").find(".edit-swatch-name").prev("span").text(me.parents(".swatch").find(".edit-swatch-name").val());
          me.parents(".swatch").first().attr("data-name", me.parents(".swatch").find(".edit-swatch-name").val());

          me.parents(".swatch").find(".edit-swatch-name").hide().prev("span").show();

          me.parents(".swatch").find(".btn.swatches-trash,.btn.swatches-edit").show();
          me.parents(".swatch").find(".btn.swatches-discard,.btn.swatches-editsok,.swatch-edit").hide();

          $("input.editswatches").each(function(index, val) {
            $(val).attr("original-color", $(val).val());
          });

          that.update();

        });

        $(document).on("click tap",".swatches-save",function(e){
          e.preventDefault();
          that.discard();
          that.save();
        });

        $(document).on("click tap",".swatches-add-new",function(e){
          e.preventDefault();
          that.discard();
          var me = $(this);
          var el = $("<div />").attr("data-name","New Schemes").attr("data-colors", "#886655,#DD9977,#EECCAA").addClass("swatch");
          $(".puiw_color_schemes_workspace").prepend(el);
          that.refresh();
          that.update();
        });

        $(document).on("click tap",".swatches-delete-all",function(e){
          e.preventDefault();
          that.discard();
          var jc = $.confirm({
              title: _i18n.confirmTxt,
              content: _i18n.confirm_delete,
              type: "red",
              boxWidth: '600px', icon: 'fa fa-question-circle', closeIcon: true, animation: 'scale',
              buttons: { no: { text: txtNop, btnClass: 'btn-red', keys: ['n','esc'], action: function(){} },
                yes: { text: txtYes, btnClass: 'btn-red', keys: ['enter'], action: function() {
                  $(".puiw_color_schemes_workspace .swatch").remove();
                  that.update();
                }},
              }, });
        });

        $(document).on("click tap",".swatches-restore-default",function(e){
          e.preventDefault();
          that.discard();
          var jc = $.confirm({
              title: _i18n.confirmTxt,
              content: _i18n.confirm_restore,
              boxWidth: '600px', icon: 'fa fa-question-circle', closeIcon: true, animation: 'scale',
              buttons: { no: { text: txtNop, btnClass: 'btn-blue', keys: ['n','esc'], action: function(){} },
                yes: { text: txtYes, btnClass: 'btn-blue', keys: ['enter'], action: function() {
                  $("textarea#puiw_color_schemes").val(DEFAULT_SWATACHES);
                  that.load(1);
                  that.refresh();
                  that.update();
                }},
              }, });
        });

        $(document).on("click tap",".swatches-export",function(e){
          e.preventDefault();
          var me = $(this); that.update();
          var currentData = $("textarea#puiw_color_schemes").val();
          var jc = $.confirm({
              title: msg.export,
              content: `<textarea title="${msg.insertErr}" placeholder="${msg.insertErr}" onclick="this.focus();this.select()" onfocus="this.focus();this.select();" readonly="readonly" id="puiw_color_schemes_export" rows="8" style="width: 100%;">${currentData}</textarea>`,
              boxWidth: '600px',
              icon: 'fa fa-box',
              closeIcon: true,
              animation: 'scale',
              buttons: {
                ok: { text: okTxt, btnClass: 'btn-blue', keys: ['enter'], action: function(){} },
                yes: { text: txtCopy, btnClass: 'btn-blue', keys: ['esc'], action: function() {
                  that.copyToClipboard(currentData);
                }},
              },
          });
        });

        $(document).on("click tap",".swatches-import",function(e){
          e.preventDefault();
          var me = $(this);
          that.discard();

          var jc = $.confirm({
              title: msg.import,
              content: `<textarea title="${msg.insertErr}" placeholder="${msg.insertErr}" id="puiw_color_schemes_import" rows="8" style="width: 100%;"></textarea>`,
              boxWidth: '600px',
              icon: 'fa fa-folder-open',
              closeIcon: true,
              animation: 'scale',
              buttons: {
                no: { text: cancelbTn, btnClass: 'btn-blue', keys: ['n','esc'], action: function(){} },
                yes: { text: txtImport, btnClass: 'btn-blue', keys: ['enter'], action: function() {
                  var importData = $("#puiw_color_schemes_import").val();
                  if ($.trim(importData) == ""){$("#puiw_color_schemes_import").focus(); return false; }
                  try {
                    var dJSON = $.parseJSON(importData);
                    $("textarea#puiw_color_schemes").val(importData);
                    if (!that.load(0,1)){
                      $.confirm({ title: errorTxt, content: `${msg.importErr}<br>${msg.importErr2}` , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                      return false;
                    }
                  }
                  catch (err) {
                    $.confirm({ title: errorTxt, content: `${msg.importErr}<br>${msg.importErr2}` , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
                    return false;
                  }
                }},
              },
          });

        });

        this.jquery_confirm_init();
        this.load();
      },
      refresh: function() {
        $('.swatch:not(.jqueryswatches)').swatchify();
        $(".swatches-discard,.swatches-editsok").hide();
      },
      discard: function() {
        $(".swatch").removeClass("editingnow");
        $(".swatches-discard").click();
      },
      load: function(force=false,prepend=false) {
        var data = $("textarea#puiw_color_schemes").val();
        if ($.trim(data) == ""){ data = DEFAULT_SWATACHES; }
        if (force){
          $(".puiw_color_schemes_workspace").empty();
        }
        try {
          var dJSON = $.parseJSON(data);
          $.each(dJSON, function(index, val) {
            var el = $("<div />").attr("data-name",val.n).attr("data-colors", `${val.p},${val.s},${val.t}`).addClass("swatch");
            if (prepend){
              $(".puiw_color_schemes_workspace").prepend(el);
            }else{
              $(".puiw_color_schemes_workspace").append(el);
            }
          });
          this.refresh();
          that.update();
          return true;
        }
        catch (err) {
          setTimeout(console.log.bind(console, `%cERR: Cannot load previous data, try reseting to default or contact support@pepro.dev .`, "color:cyan;", ""));
          return false;
        }

      },
      save: function() {
        if (_pepro_ajax_request != null) { _pepro_ajax_request.abort(); }
        that.update();
        var data = $("textarea#puiw_color_schemes").val();
        if ($.trim(data) == ""){ data = DEFAULT_SWATACHES; }
        $(".puiw_color_schemes_tool a, .swatches-tool .btn").prop("disabled",true).addClass("disabled");
        $(".puiw_color_schemes_workspace").addClass("loading");
        _pepro_ajax_request = $.ajax({
          type: "POST",
          dataType: "json",
          url: _i18n.ajax,
          data: {
            action: _i18n.td,
            nonce: _i18n.nonce,
            wparam: "save-swatches",
            lparam: data,
          },
          success: function(result) {
            if (result.success === true){
              $.confirm({ title: successTtl, content: result.data.msg , icon: 'fas fa-check-circle', type: 'green', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
            } else {
              $.confirm({ title: errorTxt, content: e.data.msg , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
            }
          },
          error: function(e) {
            $.confirm({ title: errorTxt, content: "Unknown Error!" , icon: 'fa fa-exclamation-triangle', type: 'red', boxWidth: '400px', buttons: { close : { text: closeTxt, keys: ['enter','esc'], action:function(){} } } });
          },
          complete: function(e) {
            $(".puiw_color_schemes_tool a, .swatches-tool .btn").prop("disabled",false).removeClass("disabled");
            $(".puiw_color_schemes_workspace").removeClass("loading");
          },
        });
      },
      update: function() {
        var data = [];
        $(".puiw_color_schemes_workspace .swatch").each(function(i,v) {
            data[i] = {
              n: $(v).attr("data-name"),
              p: $(v).attr("data-colors").split(",")[0],
              s: $(v).attr("data-colors").split(",")[1],
              t: $(v).attr("data-colors").split(",")[2],
          };
        });
        $("textarea").val(JSON.stringify(data))
      },
      update_colors: function() {
        $(".holder").each(function(i,v) {
          var color_1 = $(v).find("span.color:nth-of-type(1)");
          var color_2 = $(v).find("span.color:nth-of-type(2)");
          var color_3 = $(v).find("span.color:nth-of-type(3)");

          var name_1 = $(v).find("span.name:nth-of-type(1)");
          var name_2 = $(v).find("span.name:nth-of-type(2)");
          var name_3 = $(v).find("span.name:nth-of-type(3)");

          var editswatches_1 = $(v).find("input.editswatches.nth-0").val();
          var editswatches_2 = $(v).find("input.editswatches.nth-1").val();
          var editswatches_3 = $(v).find("input.editswatches.nth-2").val();

          if ($.Color(editswatches_1)) {
            color_1.css("background", editswatches_1);
            name_1.text(names[0] + editswatches_1);
          }
          if ($.Color(editswatches_2)) {
            color_2.css("background", editswatches_2);
            name_2.text(names[1] + editswatches_2);
          }
          if ($.Color(editswatches_3)) {
            color_3.css("background", editswatches_3);
            name_3.text(names[2] + editswatches_3);
          }

          $(this).parent(".jqueryswatches").first().attr("data-colors", `${editswatches_1},${editswatches_2},${editswatches_3}`);
        });
      },
      rgb2hex: function(rgb) {
        return $.Color(rgb).toHexString().toUpperCase();
      },
      rgb2hex_old: function(rgb) {
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        return "#" + that.hex(rgb[1]) + that.hex(rgb[2]) + that.hex(rgb[3]);
      },
      copyToClipboard: function (data) {
        var $temp = $("<input />");
        $("body").append($temp);
        $temp.val(data).select();
        var result = false;
        try {
          result = document.execCommand("copy");
        } catch (err) {
        }
        $temp.remove();
        return result;
      },
      hex: function(x) {
        var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
        return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
      },
      jquery_confirm_init: function() {
        errorTxt = _i18n.errorTxt;
        cancelTtl = _i18n.cancelTtl;
        confirmTxt = _i18n.confirmTxt;
        successTtl = _i18n.successTtl;
        submitTxt = _i18n.submitTxt;
        okTxt = _i18n.okTxt;
        closeTxt = _i18n.closeTxt;
        txtCopy = _i18n.txtCopy;
        cancelbTn = _i18n.cancelbTn;
        sendTxt = _i18n.sendTxt;
        titleTx = _i18n.titleTx;
        expireNowE = _i18n.expireNowE;
        txtYes = _i18n.txtYes;
        txtImport = _i18n.txtImport;
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
      },
    };
    color_scheme.init();
  });
})(jQuery);
