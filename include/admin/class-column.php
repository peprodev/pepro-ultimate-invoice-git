<?php
# @Author: Amirhosseinhpv
# @Date:   2020/09/20 23:08:04
# @Email:  its@hpv.im
# @Last modified by:   Amirhosseinhpv
# @Last modified time: 2021/07/12 17:17:36
# @License: GPLv2
# @Copyright: Copyright © 2020 Amirhosseinhpv, All rights reserved.


namespace peproulitmateinvoice;

defined("ABSPATH") or die("Pepro Ultimate Invoice :: Unauthorized Access!");

if (!class_exists("PeproUltimateInvoice_Columns")) {
    class PeproUltimateInvoice_Columns
    {
        private $td;
        public function __construct()
        {
            $this->td = "pepro-ultimate-invoice";
            add_action("admin_enqueue_scripts", array( $this, "admin_enqueue_scripts" ) );
            add_filter('manage_edit-shop_order_columns', array( $this , 'column_header'), 20);
            add_action('manage_shop_order_posts_custom_column', array( $this , 'column_content'));
        }
        public function localize_script()
        {
          return array(
            "td"                  => "puiw_{$this->td}",
            "ajax"                => admin_url("admin-ajax.php"),
            "home"                => home_url(),
            "nonce"               => wp_create_nonce($this->td),
            "title"               => _x("Select image file", "wc-setting-js", $this->td),
            "btntext"             => _x("Use this image", "wc-setting-js", $this->td),
            "clear"               => _x("Clear", "wc-setting-js", $this->td),
            "currentlogo"         => _x("Current preview", "wc-setting-js", $this->td),
            "selectbtn"           => _x("Select image", "wc-setting-js", $this->td),
            "plugin_url"          => PEPROULTIMATEINVOICE_URL,

            "rtl"                 => is_rtl() ? 1 : 0,
            "tr_submit"           => _x("Submit","js-string",$this->td),
            "tr_today"            => _x("Today","js-string",$this->td),
            "errorTxt"            => _x("Error", "wc-setting-js", $this->td),
            "cancelTtl"           => _x("Canceled", "wc-setting-js", $this->td),
            "confirmTxt"          => _x("Confirm", "wc-setting-js", $this->td),
            "successTtl"          => _x("Success", "wc-setting-js", $this->td),
            "submitTxt"           => _x("Submit", "wc-setting-js", $this->td),
            "okTxt"               => _x("Okay", "wc-setting-js", $this->td),
            "txtYes"              => _x("Yes", "wc-setting-js", $this->td),
            "txtNop"              => _x("No", "wc-setting-js", $this->td),
            "cancelbTn"           => _x("Cancel", "wc-setting-js", $this->td),
            "sendTxt"             => _x("Send to all", "wc-setting-js", $this->td),
            "closeTxt"            => _x("Close", "wc-setting-js", $this->td),
            "attach"              => _x("Attach a PDF version to email?", "wc-setting-js", $this->td),
            "emailCustomerAsk"    => _x("Are you sure you want to email current order's invoice to customer's email address?<br>Customer Email Address: %s", "wc-setting-js", $this->td),
            "emailCustomlistAsk"  => _x("<p>Enter Email list below by adding addresses following by Enter/Space key.</p><p>You can also bulk import Emails from Excel/CSV or any other formats.</p>", "wc-setting-js", $this->td),
            "emailShopMngrAsk"    => _x("Are you sure you want to email current order's invoice to shop managers listed below?<br>%s", "wc-setting-js", $this->td),
            "emailCustomerTitle"  => _x("Email Invoice to Customer", "wc-setting-js", $this->td),
            "emailShopMngrTitle"  => _x("Email Invoice to Shop managers", "wc-setting-js", $this->td),
            "emailCustomTitle"    => _x("Email Invoice to Custom List", "wc-setting-js", $this->td),
            "anErrExprienced"     => _x("An Error Has Occurred", "wc-setting-js", $this->td),
            "anEmailisrequid"     => _x("Enter at least an email address", "wc-setting-js", $this->td),

          );
        }
        public function debugEnabled($debug_true = true, $debug_false = false)
        {
            return apply_filters("puiw_debug_enabled", $debug_true);
        }
        public function admin_enqueue_scripts()
        {
          wp_enqueue_script("jquery-confirm",PEPROULTIMATEINVOICE_ASSETS_URL . "/js/jquery-confirm.min.js", array("jquery"));
          wp_enqueue_style("jquery-confirm",PEPROULTIMATEINVOICE_ASSETS_URL . "/css/jquery-confirm.min.css", array(), '1.0', 'all');
          wp_register_script( "pepro-ultimate-invoice-multiple-emails", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/multiple-emails" . $this->debugEnabled(".js",".min.js"), array("jquery"));
          wp_register_style( "pepro-ultimate-invoice-multiple-emails", PEPROULTIMATEINVOICE_ASSETS_URL . "/css/multiple-emails" . $this->debugEnabled(".css",".min.css"), array(), '1.0', 'all');
          wp_enqueue_style("fontawesome", PEPROULTIMATEINVOICE_ASSETS_URL . "/fontawesome-free/css/all.css", array(), '5.15.3', 'all');
          wp_register_script("pepro-ultimate-invoice-orders-options", PEPROULTIMATEINVOICE_ASSETS_URL . "/admin/wc_orders" . $this->debugEnabled(".js",".min.js"), array("jquery"),current_time('timestamp'));
          wp_localize_script( "pepro-ultimate-invoice-orders-options", "_i18n", $this->localize_script());
          wp_register_script( "pepro-ultimate-invoice-nicescroll", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/jquery.nicescroll.min.js", array("jquery"),'1.0.2');
          wp_register_style( "pepro-ultimate-invoice-orders-options", PEPROULTIMATEINVOICE_ASSETS_URL . "/admin/wc_orders" . $this->debugEnabled(".css",".min.css"));
          wp_register_script( "pepro-ultimate-invoice-persian-date", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/persian-date.min.js", array("jquery"),'1.0.2');
          wp_register_script( "pepro-ultimate-invoice-persian-datepicker", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/persian-datepicker.min.js", array("jquery"),'1.0.2');
          wp_register_style( "pepro-ultimate-invoice-persian-datepicker", PEPROULTIMATEINVOICE_ASSETS_URL . "/css/persian-datepicker.min.css");

        }
        /**
         * Adds 'Ultimate Invoice' column header to 'Orders' page immediately after 'Status' column.
         *
         * @param  array $columns
         * @return array $new_columns
         */
        public function column_header( $columns )
        {
            $new_columns = array();
            foreach ( $columns as $column_name => $column_info ) {
                $new_columns[ $column_name ] = $column_info;
                if ('order_status' === $column_name ) {
                    $new_columns['ultimate_invoice'] = __('Ultimate Invoice', $this->td);
                }
            }
            return $new_columns;
        }
        /**
         * Adds 'Ultimate Invoice' column content to 'Orders' page immediately after 'Status' column.
         *
         * @param array $column name of column being displayed
         */
        public function column_content( $column )
        {
            global $post;
            if (!wp_script_is("pepro-ultimate-invoice-orders-options")){
              add_thickbox();
              wp_enqueue_media();
              wp_enqueue_script("jquery");
              wp_enqueue_style("wp-color-picker");
              wp_enqueue_script("wp-color-picker");
              wp_enqueue_script('jquery-ui-core');
              wp_enqueue_script('jquery-ui-selectmenu');
              wp_enqueue_style("pepro-ultimate-invoice-persian-datepicker");
              wp_enqueue_style("pepro-ultimate-invoice-multiple-emails");
              wp_enqueue_style("pepro-ultimate-invoice-orders-options");
              wp_enqueue_script("pepro-ultimate-invoice-nicescroll");
              wp_enqueue_script("pepro-ultimate-invoice-multiple-emails");
              wp_enqueue_script("pepro-ultimate-invoice-persian-date");
              wp_enqueue_script("pepro-ultimate-invoice-persian-datepicker");
              wp_enqueue_script("pepro-ultimate-invoice-orders-options");
              echo          "<script>var CURRENT_ORDER_MAIL = [];</script>";
            }
            if ('ultimate_invoice' === $column ) {
                $order    = wc_get_order($post->ID);
                $total    = (float) $order->get_total();
                $email    = $order->get_billing_email();
                $id       = $order->get_id();
                $coldata  = "<script>CURRENT_ORDER_MAIL['$id'] = '$email';</script>" . $this->popup_html_data($id);
                echo apply_filters( "pepro_ultimate_invoice_orders_column_data", $coldata, $post->ID);
            }
        }
        public function popup_html_data($id,$mode=true)
        {
          // <a class='button pwui_opts' href='#' data-ref='{$id}'>"._x("Invoice Options","wc-orders-popup",$this->td)."</a>
          $url1 = home_url("?invoice={$id}");
          $url2 = home_url("?invoice-pdf={$id}");
          $url4 = home_url("?invoice-inventory={$id}");
          $url3 = home_url("?invoice-slips={$id}");
          $mode = $mode ? "
          <a class='button admn pwui_opts maincog' href='#' data-ref='{$id}' rel='puiw_tooltip' title='"._x("View Invoice Options","wc-orders-popup",$this->td)."'>
            <img src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/puzzle.png'/>
          </a>
          <a class='button admn pwui_opts html' href='$url1' data-ref='{$id}' target='_blank' rel='puiw_tooltip' title='"._x("View Order HTML Invoice","wc-orders-popup",$this->td)."'>
            <img src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/document.png'/>
          </a>
          <a class='button admn pwui_opts pdf' href='$url2' data-ref='{$id}' target='_blank' rel='puiw_tooltip' title='"._x("View Order PDF Invoice","wc-orders-popup",$this->td)."'>
            <img src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/pdf.png'/>
          </a>
          " : "";


          return "$mode
          <div class='pwui_overlyblockui' data-ref='{$id}'></div>
          <div class='pwui_ajax_data' data-ref='{$id}'>
            <div class='piuw_toolkit'>
              <a class='puiw_close_overly' title='"._x("Close","wc-orders-popup",$this->td)."' rel='puiw_tooltip' href='#'><span class='dashicons dashicons-no'></span></a>
              <a class='secondary puiw_back_overly' data-ref='{$id}' title='"._x("Back to menu","wc-orders-popup",$this->td)."' rel='puiw_tooltip' href='#'><span class='dashicons dashicons-undo'></span></a>
              <a class='secondary puiw_open_newtab' data-ref='{$id}' title='"._x("Open in new Tab","wc-orders-popup",$this->td)."' rel='puiw_tooltip' href='#'><span class='dashicons dashicons-external'></span></a>
              <a class='secondary puiw_print_overly' data-ref='{$id}' title='"._x("Print Now","wc-orders-popup",$this->td)."' rel='puiw_tooltip' href='#'><span class='dashicons dashicons-media-document'></span></a>
              <a class='secondary puiw_download_pdf' data-ref='{$id}' title='"._x("Download PDF","wc-orders-popup",$this->td)."' rel='puiw_tooltip' href='#'><span class='dashicons dashicons-download'></span></a>
            </div>
            <div class='ajax_data'></div>
          </div>
          <div class='pwui_overly' data-ref='{$id}'>
            <div class='piuw_toolkit'>
              <a class='puiw_close_overly' title='"._x("Close","wc-orders-popup",$this->td)."' rel='puiw_tooltip' href='#'><span class='dashicons dashicons-no'></span></a>
            </div>
            <h2>".sprintf(_x("Invoice #%s Options","wc-orders-popup",$this->td),$id)."</h2>
            <ul class='inner_content' data-ref='{$id}'>

              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/document.png'/> <a            data-action='puiw_act4' data-ref='{$id}' href='$url1'> "   . _x("HTML Invoice","wc-orders-popup",$this->td)."</a></li>
              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/pdf.png'/> <a                 data-action='puiw_act5' data-ref='{$id}' href='$url2'> "   . _x("PDF Invoice","wc-orders-popup",$this->td)."</a></li>
              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/document-delivery.png'/> <a   data-action='puiw_act3' data-ref='{$id}' href='$url4'> "   . _x("Inventory report","wc-orders-popup",$this->td)."</a></li>
              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/unpacking.png'/> <a           data-action='puiw_act2' data-ref='{$id}' href='$url3'> "   . _x("Sender/Receiver Packing Slip","wc-orders-popup",$this->td)."</a></li>
              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/mail-account.png'/> <a        data-action='puiw_act6' data-ref='{$id}' href='#'> "   . _x("Mail Invoice to Customer","wc-orders-popup",$this->td)."</a></li>
              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/secure-mail.png'/> <a         data-action='puiw_act9' data-ref='{$id}' href='#'> "  . _x("Mail Invoice to Shop Managers","wc-orders-popup",$this->td)."</a></li>
              <li><img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/edit-message.png'/> <a        data-action='puiw_act10' data-ref='{$id}' href='#'> "  . _x("Mail Invoice to Custom List","wc-orders-popup",$this->td)."</a></li>
            </ul>
          </div>
          ";
        }
    }
}
