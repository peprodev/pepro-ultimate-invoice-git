/**
 * @Author: Amirhosseinhpv
 * @Date:   2021/07/12 16:58:32
 * @Email:  its@hpv.im
 * @Last modified by:   Amirhosseinhpv
 * @Last modified time: 2021/07/12 22:23:33
 * @License: GPLv2
 * @Copyright: Copyright © Amirhosseinhpv (https://hpv.im), all rights reserved.
 */

(function ($) {
  var _pepro_ajax_request = null;
  $(document).ready(function () {
    var migrate_backup = {
      init: function () {
        that = this;
        $(document).on("click tap", ".backup-export", function (e) {
          e.preventDefault();
          var me = $(this);
          try {
            var currentData = JSON.stringify(_i18n.json_data);
          } catch (err) {
            $.confirm({
              title: errorTxt,
              content: `${msg.exportErr}<br>${msg.importErr2}`,
              icon: 'fa fa-exclamation-triangle',
              type: 'red',
              boxWidth: '400px',
              buttons: {
                close: {
                  text: closeTxt,
                  keys: ['enter', 'esc'],
                  action: function() {}
                }
              }
            });
            return false;
          }
          var jc = $.confirm({
            title: msg.export,
            content: `<textarea title="${msg.insertErr}" placeholder="${msg.insertErr}" onclick="this.focus();this.select()" onfocus="this.focus();this.select();" readonly="readonly" id="puiw_color_schemes_export" rows="8" style="width: 100%;">${currentData}</textarea>`,
            boxWidth: "600px",
            icon: "fa fa-box",
            closeIcon: true,
            animation: "scale",
            buttons: {
              ok: {
                text: okTxt,
                btnClass: "btn-blue",
                keys: ["enter"],
                action: function () {},
              },
              yes: {
                text: txtCopy,
                btnClass: "btn-blue",
                keys: ["esc"],
                action: function () {
                  that.copyToClipboard(currentData);
                },
              },
            },
          });
        });
        $(document).on("click tap", ".backup-import", function (e) {
          e.preventDefault();
          var me = $(this);
          var jc = $.confirm({
            title: msg.import,
            content: `<textarea title="${msg.insertErr}" placeholder="${msg.insertErr}" id="puiw_options_import" rows="8" style="width: 100%;"></textarea>`,
            boxWidth: "600px",
            icon: "fa fa-folder-open",
            closeIcon: true,
            animation: "scale",
            buttons: {
              no: {
                text: cancelbTn,
                btnClass: "btn-blue",
                keys: ["n", "esc"],
                action: function () {},
              },
              yes: {
                text: txtImport,
                btnClass: "btn-blue",
                keys: ["enter"],
                action: function () {
                  try {
                    var dJSON = JSON.parse($("#puiw_options_import").val());
                  } catch (err) {
                    $.confirm({
                      title: errorTxt,
                      content: `${msg.importErr}<br>${msg.importErr2}`,
                      icon: 'fa fa-exclamation-triangle',
                      type: 'red',
                      boxWidth: '400px',
                      buttons: {
                        close: {
                          text: closeTxt,
                          keys: ['enter', 'esc'],
                          action: function() {}
                        }
                      }
                    });
                    return false;
                  }
                  $(".jconfirm-closeIcon").hide();
                  jc.showLoading(true);
                  jc.setBoxWidth("400px");
                  $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: _i18n.ajax,
                    data: {
                      action: _i18n.td,
                      nonce: _i18n.nonce,
                      wparam: "import_options",
                      lparam: dJSON,
                    },
                    success: function (result) {
                      if (result.success === true) {
                        $.confirm({
                          title: successTtl,
                          content: result.data.msg,
                          icon: "fas fa-check-circle",
                          type: "green",
                          boxWidth: "400px",
                          buttons: {
                            close: {
                              text: closeTxt,
                              keys: ["enter", "esc"],
                              action: function () {},
                            },
                            reload: {
                              text: reloadTxt,
                              keys: ["enter", "esc"],
                              action: function () {
                                window.location.href = window.location.href;
                              },
                            },
                          },
                        });
                      } else {
                        $.confirm({
                          title: errorTxt,
                          content: result.data.msg,
                          icon: "fas fa-exclamation-triangle",
                          type: "red",
                          boxWidth: "400px",
                          buttons: {
                            close: {
                              text: closeTxt,
                              keys: ["enter", "esc"],
                              action: function () {},
                            },
                          },
                        });
                      }
                    },
                    error: function (result) {
                      $.confirm({
                        title: errorTxt,
                        content: _i18n.UnknownErr,
                        icon: "fas fa-exclamation-triangle",
                        type: "red",
                        boxWidth: "400px",
                        buttons: {
                          close: {
                            text: closeTxt,
                            keys: ["enter", "esc"],
                            action: function () {},
                          },
                        },
                      });
                    },
                    complete: function (result) {
                      jc.close();
                    },
                  });
                  return false;
                },
              },
            },
          });
        });
        $(document).on("click tap", ".btn-confirm", function(e){
          e.preventDefault();
          var me = $(this);
          var jc = $.confirm({
            title: _i18n.cautiontl,
            content: _i18n.confirmMsg,
            boxWidth: "600px",
            icon: "fa fa-flask",
            closeIcon: true,
            animation: "scale",
            buttons: {
              no: {
                text: _i18n.txtNop,
                btnClass: "btn-green",
                keys: ["esc"],
                action: function () {},
              },
              yes: {
                text: _i18n.txtYes,
                btnClass: "btn-red",
                keys: ["enter"],
                action: function () {jc.close(); window.open(me.attr("href"));},
              },
            },
          });
        });
        this.jquery_confirm_init();
      },
      copyToClipboard: function (data) {
        var $temp = $("<input />");
        $("body").append($temp);
        $temp.val(data).select();
        var result = false;
        try {
          result = document.execCommand("copy");
        } catch (err) {}
        $temp.remove();
        return result;
      },
      jquery_confirm_init: function () {
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
        reloadTxt = _i18n.reloadTxt;
        txtImport = _i18n.txtImport;
        txtNop = _i18n.txtNop;
        jconfirm.defaults = {
          title: "",
          titleClass: "",
          type: "blue", // red green orange blue purple dark
          typeAnimated: true,
          draggable: true,
          dragWindowGap: 15,
          dragWindowBorder: true,
          animateFromElement: true,
          smoothContent: true,
          content: "",
          buttons: {},
          defaultButtons: {
            ok: {
              keys: ["enter"],
              text: okTxt,
              action: function () {},
            },
            close: {
              keys: ["enter"],
              text: closeTxt,
              action: function () {},
            },
            cancel: {
              keys: ["esc"],
              text: cancelbTn,
              action: function () {},
            },
          },
          contentLoaded: function (data, status, xhr) {},
          icon: "",
          lazyOpen: false,
          bgOpacity: null,
          theme: "modern",
          /*light dark supervan material bootstrap modern*/
          animation: "scale",
          closeAnimation: "scale",
          animationSpeed: 400,
          animationBounce: 1,
          rtl: $("body").is(".rtl") ? true : false,
          container: "body",
          containerFluid: false,
          backgroundDismiss: false,
          backgroundDismissAnimation: "shake",
          autoClose: false,
          closeIcon: null,
          closeIconClass: false,
          watchInterval: 100,
          columnClass: "m",
          boxWidth: "500px",
          scrollToPreviousElement: true,
          scrollToPreviousElementAnimate: true,
          useBootstrap: false,
          offsetTop: 40,
          offsetBottom: 40,
          bootstrapClasses: {
            container: "container",
            containerFluid: "container-fluid",
            row: "row",
          },
          onContentReady: function () {},
          onOpenBefore: function () {},
          onOpen: function () {},
          onClose: function () {},
          onDestroy: function () {},
          onAction: function () {},
          escapeKey: true,
        };
      },
    };
    migrate_backup.init();
  });
})(jQuery);
