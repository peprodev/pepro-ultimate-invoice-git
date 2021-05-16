<?php
/*
Plugin Name: Pepro Ultimate Invoice
Description: The most complete invoice plugin you will ever need.
Contributors: amirhosseinhpv
Tags: woocommerce invoice, pdf invoice, ultimate invoicing
Author: Pepro Dev. Group
Developer: Amirhosseinhpv
Author URI: https://pepro.dev/
Developer URI: https://hpv.im/
Plugin URI: https://pepro.dev/ultimate-invoice/
Version: 1.3.3
Stable tag: 1.3.3
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 7.0
WC requires at least: 4.4
WC tested up to: 5.1.0
Text Domain: puice
Domain Path: /languages
Copyright: (c) 2020 Pepro Dev. Group, All rights reserved.
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
# @Last modified time: 2021/05/16 11:58:22

namespace peproulitmateinvoice;
use voku\CssToInlineStyles\CssToInlineStyles;

/**
 * prevent data leak
 */
defined("ABSPATH") or die("Pepro Ultimate Invoice :: Unauthorized Access!");
/**
* if plugin was not initiated before, let's do it
*/
if (!class_exists("PeproUltimateInvoice")) {
    /**
     * Main class, accessible on initiation with global $PeproUltimateInvoice
     */
    class PeproUltimateInvoice
    {
        private static $_instance = null;
        public $td;
        public $plugin_dir;
        public $plugin_url;
        public $assets_url;
        public $plugin_basename;
        public $plugin_file;
        public $version;
        public $db_slug;
        public $title;
        public $title_w;
        public $title_t;
        public $barcode;
        public $tpl;
        public $Unauthorized_Access;
        protected $print;
        protected $invoice;
        protected $mta;
        protected $db_table = null;
        protected $manage_links = array();
        protected $meta_links = array();

        /**
         * construct plugin and set initiation hook and declare consts
         *
         * @method  __construct
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function __construct()
        {
            $this->version = "1.3.3";
            self::$_instance = $this;
            $this->td = "puice";
            $this->db_slug = $this->td;
            $this->plugin_file = __FILE__;
            $this->plugin_dir = plugin_dir_path(__FILE__);
            $this->plugin_url = plugins_url("", __FILE__);
            $this->assets_url = plugins_url("/assets/", __FILE__);
            $this->plugin_basename = plugin_basename(__FILE__);
            $this->url = admin_url("admin.php?page=wc-settings&tab=pepro_ultimate_invoice&section=general");
            $this->title = __("Ultimate Invoice", $this->td);
            $this->title_t = __("Pepro Ultimate Invoice for Woocommerce", $this->td);
            $this->title_tw = sprintf(__("%2\$s ver. %1\$s", $this->td), $this->version, $this->title_t);
            $this->title_d = __("Pepro Development Group", $this->td);
            $this->title_w = sprintf(__("%2\$s ver. %1\$s", $this->td), $this->version, $this->title);
            $this->Unauthorized_Access = "<h2 dir='rtl' align='center'>".__("Unauthorized Access!",$this->td)."</h2>";
            $this->Unauthorized_Access .= "<a href='".home_url()."' class='button button-primary'>".__("Go Back",$this->td)."</a>";

            // define constants to be accessible out of the plugin class
            defined('PEPROULTIMATEINVOICE_VER') || define('PEPROULTIMATEINVOICE_VER', $this->version);
            defined('PEPROULTIMATEINVOICE_DIR') || define('PEPROULTIMATEINVOICE_DIR', plugin_dir_path(__FILE__));
            defined('PEPROULTIMATEINVOICE_URL') || define('PEPROULTIMATEINVOICE_URL', plugins_url("", __FILE__));
            defined('PEPROULTIMATEINVOICE_ASSETS_URL') || define('PEPROULTIMATEINVOICE_ASSETS_URL', $this->assets_url);

            // hook into wp init and load plugin other hooks
            add_action( "init", array($this, 'init_plugin'));

            include_once $this->plugin_dir . 'include/vendor/autoload.php';
            $this->barcode = new \Picqer\Barcode\BarcodeGeneratorJPG();
            $this->barcode->useGd();
            include_once $this->plugin_dir . "include/admin/class-setting.php";
            include_once $this->plugin_dir . "include/admin/class-print.php";
            include_once $this->plugin_dir . "include/admin/class-template.php";
            include_once $this->plugin_dir . "include/admin/class-column.php";
            include_once $this->plugin_dir . 'include/admin/class-jdate.php';
            include_once $this->plugin_dir . 'include/admin/class-wcproduct-panel.php';


            // handle template based funtions
            $this->tpl    = new \peproulitmateinvoice\PeproUltimateInvoice_Template;
            // handle print invoice funtions
            $this->print  = new \peproulitmateinvoice\PeproUltimateInvoice_Print;
            // handle metaboxes and extras for wc orders
            $this->mta    = new \peproulitmateinvoice\PeproUltimateInvoice_Columns;

            // attach pdf to woocommerce emails automaticlly
            if ("yes" == $this->tpl->get_attach_pdf_invoices_to_mail()){
              add_filter( 'woocommerce_email_attachments', array($this,"attach_pdf_to_wC_emails"), 10, 3);
            }

            // automatic email sending
            if ( "automatic" == $this->tpl->get_send_invoices_via_email() || "automatic" == $this->tpl->get_send_invoices_via_email_admin() ){
              add_action( "woocommerce_order_status_changed", array($this,"woocommerce_order_status_changed_action"), 10, 3 );
              add_action( "woocommerce_new_order", array($this,"woocommerce_new_order_action"),  10, 1  );
            }

            // initiate plguin main instance
            if ( is_admin() ) { (new PeproUltimateInvoice_wcPanel())->init(); }


            // disable woocommerce modern admin dashboard, has to be called here!
            if ( "yes" == $this->tpl->get_disable_wc_dashboard()){
              add_filter( 'woocommerce_admin_disabled', '__return_true');
              add_filter( 'woocommerce_marketing_menu_items', '__return_empty_array' );
              add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );
            }

            add_filter('woocommerce_format_weight', function($weight) {
              return str_replace(array( 'kg', 'g', 'lbs', 'oz', ), array( __( 'kg', $this->td ), __( 'g', $this->td ), __( 'lbs', $this->td ), __( 'oz', $this->td ), ), $weight);
            });
            add_filter('woocommerce_format_dimensions', function($weight) {
              return str_replace(array(
                'mm',
                'cm',
                'in',
                'yd',
              ), array(
                __( 'mm', $this->td ),
                __( 'cm', $this->td ),
                __( 'in', $this->td ),
                __( 'yd', $this->td ),
               ), $weight);
            });

            if (isset($_GET["tab"]) && sanitize_text_field($_GET["tab"]) == sanitize_text_field("pepro_ultimate_invoice")){
                add_filter( 'woocommerce_admin_disabled', '__return_true');
                add_filter( 'woocommerce_marketing_menu_items', '__return_empty_array' );
                add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );
                if (!isset($_GET["section"]) || empty(sanitize_text_field($_GET["section"]))) {
                    wp_safe_redirect(admin_url("admin.php?page=wc-settings&tab=pepro_ultimate_invoice&section=general"));
                }
            }

            // change colors and template if invoice is pre-invoice
            add_filter( "puiw_printinvoice_pdf_footer", array( $this, "puiw_printinvoice_pdf_footer"), 10, 5);
            add_filter( "puiw_get_default_dynamic_params", array( $this, "puiw_get_default_dynamic_params"), 10, 2);

            add_filter( 'query_vars', function( $vars ){
              $vars[] = "tp";
              $vars[] = "pclr";
              $vars[] = "sclr";
              $vars[] = "tclr";
              return $vars;
            } );

        }
        public function debug_enabled($true = true,$false = false)
        {
          return $true;
          // return defined("WP_DEBUG") && true == WP_DEBUG ? $true : $false;
        }
        /**
         * woocommerce order placed hook to send emails
         *
         * @method woocommerce_new_order_action
         * @param int $order_id
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_new_order_action($order_id)
        {
          $order = wc_get_order($order_id);
          $note = "";
          if ( "automatic" == $this->tpl->get_send_invoices_via_email()){
              $email = $order->get_billing_email();
              $wp_mail = $this->send_formatted_email($order_id,$email,true);
              if ($wp_mail){
                $note .= "<br />". sprintf(__("Automatic Email sent to customer mail address:<br> %s",$this->td), $email );
              }else{
                $note .= "<br />". sprintf(__("An error occured sending Automatic Email to customer mail address:<br> %s",$this->td), $email);
              }
          }
          if ( "automatic" == $this->tpl->get_send_invoices_via_email_admin()){
              $valid_reciever_shopmngrs = $this->tpl->get_send_invoices_via_email_shpmngrs();
              if (!empty($valid_reciever_shopmngrs)){
                $wp_mail = $this->send_formatted_email($order_id,$valid_reciever_shopmngrs,true);
                if ($wp_mail){
                  $note .= "<br />". sprintf(__("Automatic Email sent to following shop managers:<br> %s",$this->td), (count($valid_reciever_shopmngrs) > 1 ? implode(", ",$valid_reciever_shopmngrs) : $valid_reciever_shopmngrs) );
                }else{
                  $note .= "<br />". sprintf(__("An error occured sending Automatic Email to following shop managers:<br> %s",$this->td), (count($valid_reciever_shopmngrs) > 1 ? implode(", ",$valid_reciever_shopmngrs) : $valid_reciever_shopmngrs) );
                }
              }else{
                $note .= "<br />". __("No Shop Manager selected to send Automatic Email to.",$this->td);
              }
          }
          if (!empty($note)){
            $order->add_order_note($note);
          }
        }
        /**
         * woocommerce order status changed hook to send emails
         *
         * @method woocommerce_order_status_changed_action
         * @param int $order_id
         * @param string $old_status
         * @param string $new_status
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_order_status_changed_action( $order_id, $old_status, $new_status )
        {
          // $order = wc_get_order( $order_id );
          $old_status = "wc-$old_status";
          $new_status = "wc-$new_status";
          $order = wc_get_order($order_id);
          $note = "";
          if ( "automatic" == $this->tpl->get_send_invoices_via_email()){
            $valid_order_status = $this->tpl->get_send_invoices_via_email_opt(array("wc-completed"));
            if (in_array($new_status, apply_filters( 'puiw_valid_order_statuses_customer_auto_email', (array) $valid_order_status))){
              $email = $order->get_billing_email();
              $wp_mail = $this->send_formatted_email($order_id,$email,true);
              if ($wp_mail){
                $note .= "<br />". sprintf(__("Automatic Email sent to customer mail address:<br> %s",$this->td), $email );
              }else{
                $note .= "<br />". sprintf(__("An error occured sending Automatic Email to customer mail address:<br> %s",$this->td), $email);
              }
            }
          }
          if ( "automatic" == $this->tpl->get_send_invoices_via_email_admin()){
            $valid_order_status = $this->tpl->get_send_invoices_via_email_opt_admin(array("wc-completed"));
            if (in_array($new_status, apply_filters( 'puiw_valid_order_statuses_shopmngr_auto_email', (array) $valid_order_status))){
              $valid_reciever_shopmngrs = $this->tpl->get_send_invoices_via_email_shpmngrs();
              if (!empty($valid_reciever_shopmngrs)){
                $wp_mail = $this->send_formatted_email($order_id,$valid_reciever_shopmngrs,true);
                if ($wp_mail){
                  $note .= "<br />". sprintf(__("Automatic Email sent to following shop managers:<br> %s",$this->td), (count($valid_reciever_shopmngrs) > 1 ? implode(", ",$valid_reciever_shopmngrs) : $valid_reciever_shopmngrs) );
                }else{
                  $note .= "<br />". sprintf(__("An error occured sending Automatic Email to following shop managers:<br> %s",$this->td), (count($valid_reciever_shopmngrs) > 1 ? implode(", ",$valid_reciever_shopmngrs) : $valid_reciever_shopmngrs) );
                }
              }else{
                $note .= "<br />". __("No Shop Manager selected to send Automatic Email to.",$this->td);
              }
            }
          }
          if (!empty($note)){
            $order->add_order_note($note);
          }
        }
        /**
         * send mail using pepro ultinate invoice core
         *
         * @method send_formatted_email
         * @param int $order_id
         * @param string $email
         * @param boolean $attach
         * @return string wp_mail status
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function send_formatted_email($order_id,$email,$attach)
        {
          add_filter( "puiw_printinvoice_return_html_minfied", function(){return false;},10,1);
          $invc = $this->print->create_html($order_id,"HTML","",true);
          $cssToInlineStyles = new CssToInlineStyles($invc);
          $cssToInlineStyles->setCleanup(true);
          $cssToInlineStyles->setUseInlineStylesBlock(true);
          $invc = $cssToInlineStyles->convert();
          $subject = apply_filters( "puiw_email_invoice_customer_subject", sprintf($this->tpl->get_email_subject(_x("Order #%s invoice on ", "wc-setting", $this->td) . get_bloginfo('name','display')), $order_id) );
          $PDFattachments = false;
          if ($attach){
            $PDFattachments = true;
            $namedir = PEPROULTIMATEINVOICE_DIR . "/pdf_temp";
            $namedir = apply_filters( "puiw_get_default_mail_pdf_temp_path", $namedir);
            $invcPDF = $this->print->create_pdf($order_id,false,"S");
            $attachments = array("$namedir/$invcPDF");
          }
          $wp_mail = wp_mail( $email, $subject, $invc, array('Content-Type: text/html; charset=UTF-8', "From: {$this->tpl->get_email_from_name()} <{$this->tpl->get_email_from_address()}>"),$attachments);
          if ($PDFattachments){ unlink("$namedir/$invcPDF"); }
          return $wp_mail;
        }
        /**
         * Auth Check
         *
         * @method auth_check
         * @return boolean
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function auth_check()
        {
          // if (is_user_logged_in() || "yes" == $this->tpl->get_allow_guest_users_view_invoices()){
            return true;
          // }
          return false;
        }
        /**
         * customized die wp function
         *
         * @method die
         * @param string $title
         * @param string $msg
         * @return string html die msg
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function die($preTitle="",$title="ERR",$msg="")
        {
          $ext = " @font-face { font-family: 'bodyfont'; font-style: normal; font-weight: 400; src: url('".PEPROULTIMATEINVOICE_URL."/assets/css/96594ad4.woff2') format('woff2'); }";
          die('<title>'. $title .'</title><!--ERR: '.$preTitle.' --><style type="text/css">'.$ext.
          'html { background: #f1f1f1; } body { background: #fff; color: #444; font-family: bodyfont, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
          margin: 2em auto; padding: 1em 2em; max-width: 700px; -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13); box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13); width: 80%; max-height: 130px;}
          h1 { border-bottom: 1px solid #dadada; clear: both; color: #666; font-size: 24px; margin: 30px 0 0 0; padding: 0; padding-bottom: 7px; } #error-page { margin-top: 50px; }
          #error-page p, #error-page .wp-die-message { font-size: 14px; line-height: 1.5; margin: 25px 0 20px; }
          #error-page code { font-family: Consolas, Monaco, monospace; } ul li { margin-bottom: 10px; font-size: 14px ; }
          a { color: #0073aa; } a:hover, a:active { color: #00a0d2; }
          a:focus { color: #124964; -webkit-box-shadow: 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8); box-shadow: 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8); outline: none; }
          .button { background: #f7f7f7; border: 1px solid #ccc; color: #555; display: inline-block; text-decoration: none; font-size: 13px; line-height: 2; height: 28px; margin: 0; padding: 0 10px 1px; cursor: pointer;
          -webkit-border-radius: 3px; -webkit-appearance: none; border-radius: 3px; white-space: nowrap; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; -webkit-box-shadow: 0 1px 0 #ccc;
          box-shadow: 0 1px 0 #ccc; vertical-align: top; } .button.button-large { height: 30px; line-height: 2.15384615; padding: 0 12px 2px; } .button:hover, .button:focus { background: #fafafa; border-color: #999; color: #23282d; }
          .button:focus { border-color: #5b9dd9; -webkit-box-shadow: 0 0 3px rgba(0, 115, 170, 0.8); box-shadow: 0 0 3px rgba(0, 115, 170, 0.8); outline: none; } .button:active { background: #eee; border-color: #999;
          -webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5); box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5); }body { font-family: bodyfont, Tahoma, Arial; }	</style>
          <body id="error-page'.$preTitle.'"><div class="wp-die-message">'.$msg.'</div></body></html>');
        }
        /**
         * Initiate plugin with init hook
         *
         * @method  init_plugin
         * @version 1.1.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function init_plugin()
        {
          // add compatibility with WPC Product Bundles for WooCommerce By WPClever
          if (class_exists('WPCleverWoosb') && function_exists('WPCleverWoosb')){
            $WPCleverWoosb = WPCleverWoosb();
            remove_action("woocommerce_before_order_itemmeta", array( $WPCleverWoosb, 'woosb_before_order_item_meta'), 10);
            remove_action( 'woocommerce_order_formatted_line_subtotal', array( $WPCleverWoosb, 'woosb_order_formatted_line_subtotal' ), 10, 2 );
            // remove_all_actions("woocommerce_before_order_itemmeta", 10);
          }

          if ( isset($_GET["invoice"]) && !empty( trim( sanitize_text_field($_GET["invoice"]) ) ) ){
            die($this->print->create_html((int) trim(sanitize_text_field($_GET["invoice"]))));
          }

          if (isset($_GET["invoice-pdf"]) && !empty(trim(sanitize_text_field($_GET["invoice-pdf"])))){
            $force_download = false;
            if (isset($_GET["download"]) && !empty(sanitize_text_field($_GET["download"]))){
              $force_download = true;
            }
            die($this->print->create_pdf((int) trim(sanitize_text_field($_GET["invoice-pdf"])), $force_download));
          }

          if (isset($_GET["invoice-slips"]) && !empty(trim(sanitize_text_field($_GET["invoice-slips"])))){
            die($this->print->create_slips((int) trim(sanitize_text_field($_GET["invoice-slips"]))));
          }

          if (isset($_GET["invoice-inventory"]) && !empty(trim(sanitize_text_field($_GET["invoice-inventory"])))){
            die($this->print->create_inventory((int) trim(sanitize_text_field($_GET["invoice-inventory"]))));
          }

          add_filter( "plugin_action_links_{$this->plugin_basename}", array($this, 'plugins_row_links'));
          add_action( "plugin_row_meta", array( $this, 'plugin_row_meta' ), 10, 2);
          add_action( "admin_menu", array($this, 'admin_menu'), 1000);
          add_action( "admin_init", array($this, 'admin_init'));
          add_action( "wp_ajax_nopriv_puiw_{$this->td}", array($this, 'handel_ajax_req'));
          add_action( "wp_ajax_puiw_{$this->td}", array($this, 'handel_ajax_req'));
          if ("yes" == $this->tpl->get_allow_preorder_invoice()){
            add_action( "woocommerce_proceed_to_checkout", array( $this,"woocommerce_after_cart_contents"), 1000);
          }
          if ("yes" == $this->tpl->get_allow_users_use_invoices()){
            add_filter( "woocommerce_my_account_my_orders_actions", array( $this,'add_view_invoice_button_orderpage'), 10, 2);
          }
          add_action( "wp_before_admin_bar_render", array( $this,'wp_before_admin_bar_render'));
          if ("yes" == $this->tpl->get_allow_quick_shop()){
            add_shortcode( "puiw_quick_shop", array($this, 'integrate_with_shortcode'));
          }
          add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this,'after_shipping_shopmngr_provided_note'), 10, 1 );
          add_action( "woocommerce_order_details_after_order_table_items", array($this, "woocommerce_order_details_after_order_table_items") );
          add_action( 'woocommerce_checkout_update_order_meta', array($this,'woocommerce_checkout_update_order_meta'));
          add_action( 'woocommerce_checkout_update_user_meta', array($this,'woocommerce_checkout_update_user_meta'), 10, 2);
          add_filter( 'woocommerce_checkout_fields', array($this,'checkout_fields_add_uin') );
          // add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this,'checkout_field_admin_display_uin'), 10, 1 );
          // add_action( 'woocommerce_admin_order_data_after_billing_address', array($this,'checkout_field_admin_display_uin'), 10, 1 );
          // add_action( 'woocommerce_admin_order_data_after_order_details', array($this,'checkout_field_admin_display_odt'), 10, 1 );

          add_filter( "woocommerce_admin_billing_fields", function($ar){
            if ("yes" == $this->tpl->get_show_user_uin()){
              $default_UIN = "";
              if (get_current_user_id()){ $default_UIN = get_user_meta( get_current_user_id(), "billing_uin", true); }
              $ar['puiw_billing_uin']  = array(
              'label' => __( 'User Unique Identification Number', $this->td ),
              'default'     => $default_UIN,
              'class'   => 'long',
            );
            }
            return $ar;
          });
          add_filter( "woocommerce_admin_shipping_fields", function($ar){
              $ar['puiw_invoice_shipdaterow']  = array("label" => "", "name"=>"", "style"=>"display:none","class"=> 'long persianDatepickerRow',);
              $ar['puiw_invoice_shipdatefa']  = array(
                'label' => __( 'Shipped Date (Shamsi)', $this->td ),
                'class'   => 'long persianDatepicker disabled',
                'placeholder' => __( 'Select Shipped Date', $this->td ),
                'custom_attributes' => array("readonly"=>"true"),
              );
              $ar['puiw_invoice_shipdate']  = array(
                'label' => __( 'Shipped Date (Gregorian)', $this->td ),
                'class'   => 'long persianDatepicker disabled',
                'placeholder' => __( 'Select Shipped Date', $this->td ),
                'custom_attributes' => array("readonly"=>"true"),
              );
              $ar['puiw_invoice_track_id']  = array(
                  'label' => __( 'Shipping Track Serial', $this->td ),
                  'class'   => 'long',
                  'placeholder' => __( 'Enter Shipping Track Serial', $this->td ),

                );
              $ar['puiw_customer_signature']  = array(
                'label' => __( 'Customer Signature', $this->td ),
                'class'   => 'wc-select-uploader',
                'style'   => 'display:none',
              );
              return $ar;
            });
          $this->add_wc_prebuy_status();

          add_action("woocommerce_order_details_before_order_table", array( $this ,'woocommerce_order_details_before_order_table'), -1000);

          add_filter("wc_order_statuses", array( $this,"add_wc_order_statuses"));

          // apply_filters( "puiw_get_template_dir_url", PEPROULTIMATEINVOICE_URL . "/template/{$opt["template"]}", $opt["template"]);
          // $templateDirpath = apply_filters( "puiw_get_template_dir_path", PEPROULTIMATEINVOICE_DIR . "/template/{$opt["template"]}", $opt["template"]);
          // puiw_get_templates_list
          // puiw_load_themes_simple_path
          // puiw_load_themes_simple_title
          // puiw_load_themes_advanced_info
          // puiw_load_themes_return_simple
          // puiw_load_themes_return_advanced
          // puiw_get_default_dynamic_params
          // puiw_printinvoice_pdf_footer

          // add_filter( "puiw_get_templates_list", function ($temp){$temp[] = 'C:\xampp\htdocs\amirhosseinhpv\wp-content\plugins\_sample.plugin\custom-puiw\default.cfg'; return $temp;} );

        }
        /**
         * print get invoices buttons on view order and order recieved pages
         *
         * @method woocommerce_order_details_before_order_table
         * @param WC_Order $order
         * @return string html buttons
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_order_details_before_order_table($order)
        {
          $allowed_statuses = $this->tpl->get_allow_users_use_invoices_criteria("");
          echo "<div class='puiw_orders_invoice_btn_container'>";
          if ( !empty($allowed_statuses) && in_array( "wc-{$order->get_status()}" , (array) $allowed_statuses )){
            if ($this->print->has_access("HTML",$order)){
              echo '<a style="margin-inline-end: 1rem;-webkit-margin-end: 1rem;" href="'.home_url("?invoice=".$order->get_order_number()).'" class="button">'._x('View Invoice', "order-page", $this->td).'</a>';
            }
            if ($this->print->has_access("PDF",$order)){
              echo '<a href="'.home_url("?invoice-pdf=".$order->get_order_number()).'" class="button">'._x('PDF Invoice', "order-page", $this->td).'</a>';
            }
          }
          echo "</div>";
        }
        /**
         * change footer and remove site name, order id and other details if order is pre-invoice
         *
         * @method puiw_printinvoice_pdf_footer
         * @param string $footer previous footer string
         * @param string $f1 part one, invoice name
         * @param string $f2 part two, page number
         * @param WC_Order $order
         * @param int $order_id
         * @return string new footer html data
         * @version 1.0.0
         * @since 1.1.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function puiw_printinvoice_pdf_footer($footer, $f1, $f2, $order, $order_id)
        {
          if ("prebuy-invoice" == $order->get_status()){
            $footer = "$f2 | " . __("Page {PAGENO} / {nbpg}",$this->td);
          }
          return $footer;
        }
        /**
         * alter invoice template and colors if pre-invoice is status of order
         *
         * @method puiw_get_default_dynamic_params
         * @param array $opts pre data
         * @param WC_Order $order
         * @return array filtered data
         * @version 1.0.0
         * @since 1.1.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function puiw_get_default_dynamic_params($opts, $order)
        {
          if ("prebuy-invoice" == $order->get_status()){
            $opts["template"] = $this->tpl->get_preinvoice_template();
            $opts["theme_color"] = $this->tpl->get_preinvoice_theme_color();
            $opts["theme_color2"] = $this->tpl->get_preinvoice_theme_color2();
            $opts["theme_color3"] = $this->tpl->get_preinvoice_theme_color3();

            $opts["preinvoice_template"] = $this->tpl->get_preinvoice_template();
            $opts["preinvoice_theme_color"] = $this->tpl->get_preinvoice_theme_color();
            $opts["preinvoice_theme_color2"] = $this->tpl->get_preinvoice_theme_color2();
            $opts["preinvoice_theme_color3"] = $this->tpl->get_preinvoice_theme_color3();
          }

          return $opts;
        }
        /**
         * Woocommerce Cart Totals After Order Total Description
         *
         * @method woocommerce_order_details_after_order_table_items
         * @return string details about shipping date and tracking
         * @version 1.1.0
         * @since 1.1.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_order_details_after_order_table_items($order)
        {
          $order_id = $order->get_id();
          $date = get_post_meta( $order_id, "_shipping_puiw_invoice_shipdate" , true);
          $serial = get_post_meta( $order_id, "_shipping_puiw_invoice_track_id" , true);
          if ("yes" == $this->tpl->show_shipped_date()){
            echo '<tr><td><strong>'._x("Shipped Date:", "wc-orders",$this->td).' </strong></td><td>'. $this->tpl->get_date($date,"Y/m/d l",true) .'</td></tr>';
          }
          if ("yes" == $this->tpl->show_shipping_serial()){
            echo '<tr><td><strong>'._x("Shipping tracking Serial:", "wc-orders",$this->td).' </strong></td><td>'. $serial .'</td></tr>';
          }
        }
        /**
         * attach pdf invoices to woocommerce emails
         *
         * @method attach_pdf_to_wC_emails
         * @param array $attachments
         * @param string $status
         * @param WC_Order $order
         * @return array $attachments
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function attach_pdf_to_wC_emails ( $attachments, $status , $order )
        {
          // $allowed_statuses = array( 'new_order', 'customer_invoice', 'customer_processing_order', 'customer_completed_order' );
          // if( isset( $status ) && in_array ( $status, $allowed_statuses ) ) {
            $invcPDF = $this->print->create_pdf($order->get_id(),false,"S");
            if (!$invcPDF){ return $attachments; }
            $namedir = PEPROULTIMATEINVOICE_DIR . "/pdf_temp";
            $namedir = apply_filters( "puiw_get_default_mail_pdf_temp_path", $namedir);
            $attachments[] = "$namedir/$invcPDF";
          // }
          return $attachments;
        }
        /**
         * add metabox to cpts
         *
         * @method add_meta_boxes
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function add_meta_boxes()
        {
          add_meta_box(
            $this->td,
            $this->title,
            array( $this, 'wc_shop_order_metabox' ),
            'shop_order',
            'side',
            'high'
          );
        }
        /**
         * wc orders screen on save
         *
         * @method wc_save_shop_order_metabox
         * @param int $post_id
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function wc_save_shop_order_metabox($post_id)
        {

        		if (
              !isset( $_POST["{$this->td}_nonce"] ) ||
              !wp_verify_nonce( $_POST["{$this->td}_nonce"], 'security_nonce' ) ||
              !current_user_can( 'edit_post' ) ||
              ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            ){
                return;
            }


            if ( !isset( $_POST['puiw_shopmngr_provided_note'] ) ) {
        			delete_post_meta( $post_id, 'puiw_shopmngr_provided_note' );
        		} else {
        			update_post_meta( $post_id, 'puiw_shopmngr_provided_note', ( $_POST['puiw_shopmngr_provided_note'] ));
        			// update_post_meta( $post_id, 'puiw_shopmngr_provided_note', sanitize_textarea_field( $_POST['puiw_shopmngr_provided_note'] ));
        		}


        		if ( !isset( $_POST['_billing_puiw_billing_uin'] ) ) {
        			delete_post_meta( $post_id, 'puiw_billing_uin' );
        		} else {
        			update_post_meta( $post_id, 'puiw_billing_uin', sanitize_text_field( $_POST['_billing_puiw_billing_uin'] ));
        		}

            if ( !isset( $_POST['_shipping_puiw_invoice_track_id'] ) ) {
              delete_post_meta( $post_id, 'puiw_invoice_track_id' );
            } else {
              update_post_meta( $post_id, 'puiw_invoice_track_id', sanitize_text_field( $_POST['_shipping_puiw_invoice_track_id'] ));
            }

        }
        /**
         * read css file header and info
         *
         * @method parseTemplate
         * @param string $contents css content
         * @return array header info
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function parseTemplate($contents)
        {
          preg_match('!/\*[^*]*\*+([^/][^*]*\*+)*/!', $contents, $themeinfo);
          $ss = str_ireplace(array("\n"), "|", $themeinfo[0]);
          $ss = substr($ss,4,-3);
          $ss = str_ireplace(array("\n","\r\n","\r"), "", $ss);
          $styleExifDAta = array();
          foreach (explode("|",$ss) as $tt) {
            $uu = explode(":",$tt);
            $styleExifDAta[strtolower($uu[0])] = substr($uu[1],1);
          }
          return $styleExifDAta;
        }
        /**
        * return array list of available templates
        *
        * @method load_themes
        * @param boolean $advanced return advanced or simple
        * @return array template info
        * @version 1.1.0
        * @since 1.0.0
        * @license https://pepro.dev/license Pepro.dev License
        */
        public function load_themes($advanced=false)
        {
          $tempaltes = array();
          $tempaltesInfo = array();
          $styleFiles = glob( PEPROULTIMATEINVOICE_DIR ."template/*/default.cfg");
          $styleFiles = apply_filters( "puiw_get_templates_list", $styleFiles);
          foreach ($styleFiles as $style) {
            $file = file($style);
            $contents = '';
            foreach($file as $lines => $line){ $contents.= $line; }
            $styleExifDAta = $this->parseTemplate($contents);
            $template = sprintf(_x('%1$s — by %2$s',"theme-name",$this->td), $styleExifDAta["name"], $styleExifDAta["designer"]);
            $simple_path = apply_filters( "puiw_load_themes_simple_path", dirname($style), $style);
            $simple_title = apply_filters( "puiw_load_themes_simple_title", $template, $styleExifDAta["name"], $styleExifDAta["designer"], $styleExifDAta);
            $tempaltes[$simple_path] = $simple_title;
            $tempaltesInfoTemp = array(
              "title" => $template,
              "name" => $styleExifDAta["name"],
              "author" => $styleExifDAta["designer"],
              "version" => $styleExifDAta["version"],
              "folder" => basename(dirname($style)),
              "path" => dirname($style),
              "url" => plugin_dir_url($style),
              "icon" => trailingslashit( plugin_dir_url($style) )."screenshot.png",
            );
            $tempaltesInfo[dirname($style)] = apply_filters( "puiw_load_themes_advanced_info", $tempaltesInfoTemp);
          }
          if ($advanced){
            return apply_filters( "puiw_load_themes_return_advanced", $tempaltesInfo);
          }else{
            return apply_filters( "puiw_load_themes_return_simple", $tempaltes);
          }
        }
        /**
         * wc order metabox callback
         *
         * @method wc_shop_order_metabox
         * @param WP_Post $post
         * @return string html wrapper to metabox
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function wc_shop_order_metabox($post)
        {
          $puiw_billing_uin = get_post_meta( $post->ID, 'puiw_billing_uin', true );
          $_billing_puiw_billing_uin = get_post_meta( $post->ID, '_billing_puiw_billing_uin', true );
          $puiw_invoice_track_id = get_post_meta( $post->ID, 'puiw_invoice_track_id', true );

          wp_nonce_field('security_nonce',"{$this->td}_nonce");

          wp_enqueue_script("jquery-confirm","{$this->assets_url}js/jquery-confirm.min.js", array("jquery"));
          wp_enqueue_style("jquery-confirm","{$this->assets_url}css/jquery-confirm.min.css", array(), '1.0', 'all');
          wp_enqueue_style("fontawesome","//use.fontawesome.com/releases/v5.13.1/css/all.css", array(), '1.0', 'all');

          $localize_script = (new PeproUltimateInvoice_Columns)->localize_script();
          wp_register_script("pepro-ultimate-invoice-orders-options", "{$this->assets_url}/admin/wc_orders" . $this->debug_enabled(".js",".min.js"), array("jquery"), current_time('timestamp'));
          wp_localize_script( "pepro-ultimate-invoice-orders-options", "_i18n",
            array_merge($localize_script,array(
              "calendarType"        => ($this->tpl->get_date_shamsi()=="yes") ? "persian" : "gregorian",
              "prev_img_url"        => get_post_meta($post->ID, '_shipping_puiw_customer_signature', true),
              "shipping_procc"      => __("Select The Date Product Shipped",$this->td),
              "shipping_clear"      => __("Clear",$this->td),
              "load_themes"         => $this->load_themes(1),
              "get_template"        => $this->tpl->get_template(),
              "theme_color"         => get_option( "puiw_theme_color"),
              "theme_color2"        => get_option( "puiw_theme_color2"),
              "theme_color3"        => get_option( "puiw_theme_color3"),
            ))
          );

          wp_register_script( "pepro-ultimate-invoice-nicescroll", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/jquery.nicescroll.min.js", array("jquery"),'1.0.2');
          wp_register_script( "pepro-ultimate-invoice-persian-date", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/persian-date.min.js", array("jquery"),'1.0.2');
          wp_register_script( "pepro-ultimate-invoice-persian-datepicker", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/persian-datepicker.min.js", array("jquery"),'1.0.2');

          wp_register_style( "pepro-ultimate-invoice-multiple-emails", PEPROULTIMATEINVOICE_ASSETS_URL . "/css/multiple-emails" . $this->debug_enabled(".css",".min.css"));
          wp_register_script( "pepro-ultimate-invoice-multiple-emails", PEPROULTIMATEINVOICE_ASSETS_URL . "/js/multiple-emails" . $this->debug_enabled(".js",".min.js"), array("jquery"));

          wp_register_style( "pepro-ultimate-invoice-orders-options", PEPROULTIMATEINVOICE_ASSETS_URL . "/admin/wc_orders" . $this->debug_enabled(".css",".min.css"));
          wp_register_style( "pepro-ultimate-invoice-persian-datepicker", PEPROULTIMATEINVOICE_ASSETS_URL . "/css/persian-datepicker.min.css");

          wp_enqueue_style("pepro-ultimate-invoice-multiple-emails");
          wp_enqueue_style("pepro-ultimate-invoice-orders-options");
          wp_enqueue_style("pepro-ultimate-invoice-persian-datepicker");

          wp_enqueue_script("jquery");
          wp_enqueue_style("wp-color-picker");
          wp_enqueue_script("wp-color-picker");
          wp_enqueue_script('jquery-ui-core');
          wp_enqueue_script('jquery-ui-selectmenu');
          wp_enqueue_media();
          add_thickbox();

          wp_enqueue_script("pepro-ultimate-invoice-nicescroll");
          wp_enqueue_script("pepro-ultimate-invoice-multiple-emails");
          wp_enqueue_script("pepro-ultimate-invoice-persian-date");
          wp_enqueue_script("pepro-ultimate-invoice-persian-datepicker");


          wp_enqueue_script("pepro-ultimate-invoice-orders-options");

          $order    = wc_get_order($post->ID);
          $total    = (float) $order->get_total();
          $email    = $order->get_billing_email();
          $id       = $order->get_id();
          echo      "<script>var CURRENT_ORDER_MAIL = []; CURRENT_ORDER_MAIL['$id'] = '$email';</script>";
          $url1     = home_url("?invoice={$id}");
          $url2     = home_url("?invoice-pdf={$id}");
          $url4     = home_url("?invoice-inventory={$id}");
          $url3     = home_url("?invoice-slips={$id}");

          echo '<template id="puiw_DateSelectorContainer" style="display:none;"><div id="puiw_DateContainer" data-date="" style="width: 100%;"></div></template>';

          echo "";

          ?>
            <!-- <p>
              <a rel='puiw_tooltip' title='<?php echo _x("View Invoice Options","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide maincog' href='#' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/puzzle.png'/>" .
              _x("Invoice Options","wc-orders-popup",$this->td);?></a>
            </p> -->
            <p>
              <input type="checkbox" value="1" id="puiwc_advanced_opts" />
              <label for="puiwc_advanced_opts"><?php echo __("Use Advanced Options?",$this->td);?></label>
            </p>
            <div class="advabced_puiwc" style="display: none;">
              <p>
                <label style="color: gray;"><?php echo __("Force Use theme",$this->td);?></label>
              </p>
              <p>
                <select id="puiw_metabox_theme_select" class='jqui-select' selecteditem='<?php echo $current;?>'></select>
              </p>
              <p>
                <label style="color: gray;"><?php echo __("Primary, Secondary and Tertiary Colors",$this->td);?></label>
              </p>
              <p>
                <input type="text" id="puiw_metabox_theme_color" value="<?php echo get_option( "puiw_theme_color");?>" class="wc-color-picker"/>
              </p>
              <p>
                <input type="text" id="puiw_metabox_theme_color2" value="<?php echo get_option( "puiw_theme_color2");?>" class="wc-color-picker"/>
              </p>
              <p>
                <input type="text" id="puiw_metabox_theme_color3" value="<?php echo get_option( "puiw_theme_color3");?>" class="wc-color-picker"/>
              </p>
              <p>
                <label style="color: gray;"><?php echo __("Load colors from Schemes",$this->td);?></label>
              </p>
              <p>
                <select id="puiw_metabox_swatch_select" class="swatch-select" swatches="<?php echo esc_js(get_option("puiw_color_swatches",""));?>"></select>
              </p>
              <p>
                <a rel='puiw_tooltip' title='<?php echo _x("Reset Advanced Options to default","wc-orders-popup",$this->td);?>' class='pwui_reset_advanced' href='#'><?php echo _x("Reset","wc-orders-popup",$this->td);?></a>
              </p>
              <p>
                <hr>
              </p>
            </div>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act_href' title='<?php echo _x("View Order HTML Invoice","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url1;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/document.png'/>" .
              _x("HTML Invoice","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act_href' title='<?php echo _x("View Order PDF Invoice","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url2;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/pdf.png'/>" .
              _x("PDF Invoice","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act_href' title='<?php echo _x("View Order Inventory report","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url4;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/document-delivery.png'/>" .
              _x("Inventory report","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act_href' title='<?php echo _x("View Packing Slip for shipping","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url3;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/unpacking.png'/>" .
              _x("Sender/Receiver Packing Slip","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act6' title='<?php echo _x("Mail Order Invoice to Customer","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url2;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/mail-account.png'/>" .
              _x("Mail Invoice to Customer","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act9' title='<?php echo _x("Mail Order Invoice to Shop Managers","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url2;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/secure-mail.png'/>" .
              _x("Mail Invoice to Shop Managers","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' data-action='puiw_act10' title='<?php echo _x("Mail Order Invoice to Custom List","wc-orders-popup",$this->td);?>' class='button button-primary pwui_opts btn-wide' href='<?php echo $url2;?>' target='_blank' data-ref='<?php echo $id;?>'><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/new-message.png'/>" .
              _x("Mail Invoice to Custom List","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit User Unique Identification Number","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_billing_uin"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/writer-male.png'/>" .
              _x("Edit User UIN","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit transaction ID","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_billing_transaction_id"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/receipt-and-change.png'/>" .
              _x("Edit Transaction ID","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit shipped date","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_invoice_shipdate"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/delivery.png'/>" .
              _x("Edit Shipped Date","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit shipping track serial","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_invoice_track_id"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/in-transit.png'/>" .
              _x("Edit Shipping Track Serial","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit customer signature image","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_invoice_customer_signature"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/sign-up.png'/>" .
              _x("Edit Customer Signature","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit customer provided note","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_invoice_customer_note"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/pencil--v2.png'/>" .
              _x("Edit Customer Note","wc-orders-popup",$this->td);?></a>
            </p>
            <p>
              <a rel='puiw_tooltip' title='<?php echo _x("Edit shop manager provided note","wc-orders-popup",$this->td);?>' class="button button-primary pwui_opts btn-wide type2" href="#" id="editpuiw_invoice_shop_manager_note"><?php echo "<img style=\"display: inline-block;-webkit-margin-end: 5px;margin-inline-end: 5px;-webkit-filter: invert(.9);filter: invert(.9);\" src='".PEPROULTIMATEINVOICE_ASSETS_URL."/img/pencil--v2.png'/>" .
              _x("Edit Shop manager Note","wc-orders-popup",$this->td);?></a>
            </p>
          <?php
          echo $this->mta->popup_html_data($id,false);
        }
        /**
         * add shopmngr provided note description after shipping details in wc_order screen
         *
         * @method after_shipping_shopmngr_provided_note
         * @return string
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function after_shipping_shopmngr_provided_note($order)
        {
          global $post;
          $value_raw = get_post_meta( $order->get_id(), "puiw_shopmngr_provided_note", true);
          $value = wp_kses_post( nl2br( wptexturize( $value_raw ) ) );
          echo "
            <p class=\"form-field form-field-wide puiw_shopmngr_provided_note preview\">
              <strong style=\"display: block;\">".__( 'Shop Manager provided note:', $this->td )."</strong>
              $value
            </p>
            <p class=\"form-field form-field-wide puiw_shopmngr_provided_note edit\">
              <label style=\"display:none;\" for=\"puiw_shopmngr_provided_note\">".__( 'Shop Manager provided note:', $this->td )."</label>
              <textarea style=\"display:none;\" rows=\"3\" cols=\"40\" name=\"puiw_shopmngr_provided_note\" id=\"puiw_shopmngr_provided_note\" placeholder=\"".__( 'Enter Shop Manager provided note here', $this->td )."\">$value_raw</textarea>
            </p>
          ";
        }
        /**
         * save wc checkout metas
         *
         * @method woocommerce_checkout_update_order_meta
         * @param int $order_id
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_checkout_update_order_meta( $order_id )
        {
            if ( ! empty( $_POST['puiw_billing_uin'] ) ) {
              update_post_meta( $order_id, 'puiw_billing_uin', sanitize_text_field( $_POST['puiw_billing_uin'] ) );
              update_post_meta( $order_id, '_billing_puiw_billing_uin', sanitize_text_field( $_POST['puiw_billing_uin'] ) );
            }
        }
        /**
         * save wc checkout user meta
         *
         * @method woocommerce_checkout_update_user_meta
         * @param int $order_id
         * @version 1.0.0
         * @since 1.1.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_checkout_update_user_meta( $customer_id, $posted )
        {
            if (isset($posted['puiw_billing_uin'])) {
              update_user_meta( $customer_id, 'billing_uin', sanitize_text_field( $posted['puiw_billing_uin'] ));
            }
        }
        /**
         * add extra field to checkout to receive user uin
         *
         * @method checkout_fields_add_uin
         * @param array $fields
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function checkout_fields_add_uin( $fields )
        {
          if ("yes" == $this->tpl->get_show_user_uin()){
            $default_UIN = "";
            if (get_current_user_id()){ $default_UIN = get_user_meta( get_current_user_id(), "billing_uin", true); }

            $fields['billing']['puiw_billing_uin'] = array(
              'label'       => _x("Unique Identification Number","wc-order-screen",$this->td),
              'placeholder' => _x("Unique Identification Number","wc-order-screen",$this->td),
              'required'    => true,
              'default'     => $default_UIN,
              'class'       => array( 'form-row-wide' ),
              'clear'       => true
            );
          }
          return $fields;
        }
        /**
         * enqueue scripts and styles on admin side
         *
         * @method admin_enqueue_scripts
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function admin_enqueue_scripts()
        {
            wp_register_style("{$this->td}-dmy", false);
            wp_enqueue_style("{$this->td}-dmy");
            $plugin_settinng_url = admin_url('admin.php?page=wc-settings&tab=pepro_ultimate_invoice');
            wp_add_inline_style("{$this->td}-dmy",
              "#wpadminbar #wp-admin-bar-puiw_toolbar_setting_btn a .ab-icon::before { font-family: WooCommerce !important; content: '\\e03d'; font-size: smaller;}
              #wpadminbar #wp-admin-bar-puiw_toolbar_dash_btn a .ab-icon::before {content: \"\\f340\";top: 3px; }
              .nav-tab-wrapper.woo-nav-tab-wrapper a.nav-tab[href='$plugin_settinng_url'] {display:none;}");
        }
        /**
         * add a link to the WP Toolbar and remove all others
         *
         * @method  custom_toolbar_link
         * @param   object $wp_admin_bar
         * @return  object wp_toolbar
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function wp_before_admin_bar_render_back()
        {
            global $wp_admin_bar;
            foreach ($wp_admin_bar->get_nodes() as $key => $value) {
                $wp_admin_bar->remove_menu($key);
            }
            $wp_admin_bar->add_menu(
                array(
                'id' => 'puiw_toolbar_dash_btn',
                'title' => '<span class="ab-icon"></span>' . __("Back to Dashboard", $this->td),
                'href' => admin_url()
                )
            );
            $wp_admin_bar->add_menu(
                array(
                'id' => 'puiw_toolbar_dark_btn',
                'title' => '<span class="ab-icon"></span>' . __("Switch Dark-mode", $this->td),
                'href' => admin_url()
                )
            );
            $wp_admin_bar->add_menu(
                array(
                'id' => 'puiw_toolbar_wc_orders',
                'title' => '<span class="ab-icon"></span>' . __("WC Orders", $this->td),
                'href' => admin_url("edit.php?post_type=shop_order")
                )
            );
        }
        /**
         * add a link to the WP Toolbar
         *
         * @method  custom_toolbar_link
         * @param   object $wp_admin_bar
         * @return  object wp_toolbar
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function wp_before_admin_bar_render()
        {
            global $wp_admin_bar;
            if (is_admin()) {
                $wp_admin_bar->add_menu(
                    array(
                    'id' => 'puiw_toolbar_setting_btn',
                    'title' => '<span class="ab-icon"></span>' . $this->title,
                    'href' => admin_url("admin.php?page=wc-settings&tab=pepro_ultimate_invoice")
                    )
                );

            }
        }
        /**
         * add wc-prebuy-invoice status to wc order statuses
         *
         * @method  add_wc_prebuy_status
         * @version 1.1.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function add_wc_prebuy_status()
        {
            register_post_status(
                'wc-prebuy-invoice', array(
                'label'                     => __('Pre-buy Invoice', $this->td),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop('Pre-buy Invoice (%s)', 'Pre-buy Invoices (%s)', $this->td)
                )
            );

        }
        /**
         * parse url and handle invoice-pdf query
         *
         * @method  url_rewrite_templates
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function url_rewrite_templates()
        {
            // if (get_query_var('invoice-pdf')) {
            //     global $order_id;
            //     $order_id = get_query_var('invoice-pdf');
            //     add_filter(
            //         'template_include', function () {
            //             return "$this->plugin_dir/include/mpdf/invoice-pdf.php";
            //         }
            //     );
            // }
        }
        /**
         * check if user has access to use print invoice fn
         *
         * @method has_access_print
         * @param string $mode
         * @param WC_Order $order
         * @return boolean
         * @version 1.0.0
         * @since 1.1.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function has_access_print($mode = "HTML",$order)
        {
          return $this->print->has_access($mode,$order);
        }
        /**
         * add get invoice button to customer's order list column
         *
         * @method  add_view_invoice_button_orderpage
         * @param   array    $actions
         * @param   WC_Order $order
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function add_view_invoice_button_orderpage( $actions, $order )
        {
             $allowed_statuses = $this->tpl->get_allow_users_use_invoices_criteria("");

             if ( !empty($allowed_statuses) && in_array( "wc-{$order->get_status()}" , (array) $allowed_statuses )){

               if ($this->print->has_access("HTML",$order)){
                 $actions["{$this->td}_html"] = array( 'url'  => home_url("?invoice=".$order->get_order_number()), 'name' => _x('View Invoice', "order-page", $this->td), );
               }

               if ($this->print->has_access("PDF",$order)){
                 $actions["{$this->td}_pdf"] = array( 'url'  => home_url("?invoice-pdf=".$order->get_order_number()), 'name' => _x('PDF Invoice', "order-page", $this->td), );
               }

             }

             return apply_filters( "pepro-ultimate-invoice-orders-action", $actions, $order, $allowed_statuses,
                                     $this->print->has_access("HTML",$order),
                                     $this->print->has_access("PDF",$order)
                   );
         }
        /**
         * add pre-order invoice status to woocommerce
         *
         * @method  add_wc_order_statuses
         * @param   array $order_statuses
         * @version 1.1.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function add_wc_order_statuses($order_statuses)
        {
            $new_order_statuses = array();
            foreach ( $order_statuses as $key => $status ) {
                $new_order_statuses[ $key ] = $status;
                if ('wc-processing' === $key ) { $new_order_statuses['wc-prebuy-invoice'] = __('Pre-buy Invoice', $this->td); }
            }
            return $new_order_statuses;
        }
        /**
         * add Get Pre-Order Invoice button to cart page, before proccess to checkout button
         *
         * @method  woocommerce_after_cart_contents
         * @return  string
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_after_cart_contents()
        {
            wp_enqueue_style("$this->td-ml", "$this->assets_url/css/mobileLayer" . $this->debug_enabled(".css",".min.css"), array(), "1.0.0", "all");
            wp_enqueue_script("$this->td-ml", "$this->assets_url/js/mobileLayer". $this->debug_enabled(".js",".min.js"), array("jquery","jquery-ui-core"), "1.0.0", true);
            if (!is_user_logged_in()) {
                wp_enqueue_script("$this->td-cart", "$this->assets_url/js/wc.cart.public" . $this->debug_enabled(".js",".min.js"), array("jquery"), "1.0.0", true);
                wp_localize_script("$this->td-cart", "_i18n", array(
                    "title"     => _x("Unauthorized Access!", "js-cart-page", $this->td),
                    "okaylabel" => _x("Okay", "js-cart-page", $this->td),
                    "msg"       => sprintf( _x("We are sorry for inconvenience, this feature is only available to logged-in users.<br>Please %sLogin / Register%s.", "js-cart-page", $this->td), "<a href='".get_permalink(get_option('woocommerce_myaccount_page_id'))."' target='_blank'>", "</a>"), )
                );
            }else{
                wp_enqueue_script("$this->td-cart", "$this->assets_url/js/wc.cart.private". $this->debug_enabled(".js",".min.js"), array("jquery"), "1.0.0", true);
                wp_localize_script("$this->td-cart", "_i18n",
                array(
                      "td"        => "puiw_{$this->td}",
                      "okaylabel" => _x("Okay", "js-cart-page", $this->td),
                      "ajax"      => admin_url("admin-ajax.php"),
                      "nonce"     => wp_create_nonce($this->td),
                    )
                );
            }
            echo "<a href=\"\" id=\"pepro-one-page-purchase--submit-invoice\" class=\"checkout-button button alt\"><span class=\"fa fa-spin fa-cog\" style='display: none;'></span>  ".__("Get Pre-buy Invoice", $this->td)."</a>";
        }
        /**
         * Initiate plugins settings in wp options
         *
         * @method  get_setting_options
         * @return  array settings and sections
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function get_setting_options()
        {
            return array(
              array(
                "name" => "{$this->td}_general",
                "data" => array(
                  "{$this->td}_invoice_img" => plugins_url("/assets/img/pepro.png", __FILE__),
                )
              ),
            );
        }
        /**
         * get plugin meta links
         *
         * @method  get_meta_links
         * @return  array list of links
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function get_meta_links()
        {
            if (!empty($this->meta_links)) {return $this->meta_links;
            }
            $this->meta_links = array(
                'sett'            => array(
                    'title'       => __('Setting', $this->td),
                    'description' => __('Setting', $this->td),
                    'target'      => '_self',
                    'url'         => admin_url("admin.php?page=wc-settings&tab=pepro_ultimate_invoice&section=general"),
                ),
                'support'         => array(
                  'title'         => __('Support', $this->td),
                  'description'   => __('Support', $this->td),
                  'target'        => '_blank',
                  'url'           => "mailto:support@pepro.dev?subject=Pepro+Ultimate+Invoice+Support",
                ),
            );
            return $this->meta_links;
        }
        /**
         * get plugin mamange links
         *
         * @method  get_manage_links
         * @return  array list of links
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function get_manage_links()
        {
            if (!empty($this->manage_links)) {return $this->manage_links; }
            // $this->manage_links = array( __("Settings", $this->td) => admin_url("admin.php?page=wc-settings&tab=pepro_ultimate_invoice"), );
            return $this->manage_links;
        }
        /**
         * run on plugin deactivation
         *
         * @method  deactivation_hook
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public static function deactivation_hook()
        {
        }
        /**
         * run on plugin activation
         *
         * @method  activation_hook
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public static function activation_hook()
        {
          flush_rewrite_rules(true);
        }
        /**
         * run on plugin uninstalation
         *
         * @method  uninstall_hook
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public static function uninstall_hook()
        {
            $ppa = new PeproUltimateInvoice;
            $peproultimateinvoice_options = $ppa->get_setting_options();
            foreach ($peproultimateinvoice_options as $options) {
                $opparent = $options["name"];
                foreach ($options["data"] as $optname => $optvalue) {
                    // unregister_setting($opparent, $optname);
                    // delete_option($optname);
                }
            }
        }
        /**
         * import default setting for plugin
         *
         * @method  set_deafault_setting
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function set_deafault_setting($clear_mode=false)
        {
            $wc_opt = array(
                    "puiw_show_customer_address" => "yes",
                    "puiw_show_customer_phone" => "yes",
                    "puiw_show_customer_email" => "yes",
                    "puiw_show_order_date" => "yes",
                    "puiw_show_payment_method" => "yes",
                    "puiw_show_shipping_method" => "yes",
                    "puiw_show_shipping_address" => "billing",
                    "puiw_address_display_method" => "[province], [city], [address1], [address2] ([po_box])",
                    "puiw_transaction_ref_id" => "yes",
                    "puiw_show_product_image" => "yes",
                    "puiw_show_product_purchase_note" => "yes",
                    "puiw_show_order_items" => "yes",
                    "puiw_show_order_total" => "yes",
                    "puiw_show_total_items" => "yes",
                    "puiw_show_discount_precent" => "yes",
                    "puiw_show_product_tax" => "yes",
                    "puiw_show_total_tax" => "yes",
                    "puiw_custom_css_style" => "",
                    "puiw_show_order_note" => "note_provided_by_both",
                    "puiw_show_user_uin" => "no",
                    "puiw_show_shipping_ref_id" => "yes",
                    "puiw_show_price_template" => "show_wc_price",
                    "puiw_show_product_weight" => "yes",
                    "puiw_show_product_dimensions" => "yes",
                    "puiw_show_product_sku" => "yes",
                    "puiw_show_product_sku2" => "yes",
                    "puiw_shelf_number_id" => "yes",
                    "puiw_show_product_sku_inventory" => "yes",
                    "puiw_show_product_sku2_inventory" => "yes",
                    "puiw_show_product_image_inventory" => "yes",
                    "puiw_price_inventory_report" => "show_wc_price",
                    "puiw_show_order_note_inventory" => "note_provided_by_both",
                    "puiw_show_create_sender_postal_label_button" => "yes",
                    "puiw_show_create_recipient_postal_label_button" => "yes",
                    "puiw_template" => PEPROULTIMATEINVOICE_DIR ."/template/default" . (is_rtl() ? "-rtl" : ""),
                    "puiw_preinvoice_template" => PEPROULTIMATEINVOICE_DIR ."/template/default-pre-invoice",
                    "puiw_invoice_title" => _x("Invoice %s", "wc-setting",$this->td),
                    "puiw_theme_color" => "#90caf9",
                    "puiw_theme_color2" => "#a6d5fc",
                    "puiw_theme_color3" => "#b5deff",
                    "puiw_preinvoice_theme_color" => "#faee84",
                    "puiw_preinvoice_theme_color2" => "#fff59d",
                    "puiw_preinvoice_theme_color3" => "#fff8b5",
                    "puiw_font_size" => "12",
                    "puiw_watermark_opacity" => "80",
                    "puiw_dark_mode" => "no",
                    "puiw_date_format" => "Y/m/d H:i",
                    "puiw_date_shamsi" => "no",
                    "puiw_disable_wc_dashboard" => "no",
                    "puiw_allow_preorder_invoice" => "no",
                    "puiw_allow_pdf" => "html",
                    "puiw_allow_pdf_customer" => "html",
                    "puiw_use_a5" => "no",
                    "attach_pdf_invoices_to_mail" => "no",
                    "allow_preorder_emptycart" => "no",
                    "puiw_show_barcode_id" => "yes",
                    "puiw_postal_stickey_label_for_store" => "yes",
                    "puiw_postal_stickey_label_for_customer" => "yes",
                    "puiw_show_qr_code_id" => "no",
                    "puiw_postal_qr_code_label_for_store" => "no",
                    "puiw_postal_qr_code_label_for_customer" => "no",
                    "puiw_send_invoices_via_email" => "manual",
                    "puiw_send_invoices_via_email_opt" => "wc-completed",
                    "puiw_send_invoices_via_email_admin" => "manual",
                    "puiw_send_invoices_via_email_opt_admin" => "wc-completed",
                    "puiw_allow_pdf_customer" => "both",
                    "puiw_allow_users_use_invoices" => "yes",
                    "puiw_allow_users_use_invoices_criteria" => "wc-completed",
                    "puiw_allow_users_have_invoices" => "yes",
                    "puiw_store_name" => get_bloginfo('name'),
                    "puiw_store_website" => get_bloginfo('url'),
                    "puiw_store_email" => get_option("admin_email"),
                    "puiw_show_store_national_id" => "yes",
                    "puiw_show_store_registration_number" => "yes",
                    "puiw_show_store_economical_number" => "yes",
                    "puiw_force_persian_numbers" => (is_rtl() ? "yes" : "no"),
                  );
            foreach ($wc_opt as $key => $value) {
                if (!get_option($key, "")) {
                  if ($clear_mode){
                    delete_option($key);
                  }else{
                    update_option($key, $value);
                  }
                };
            }

        }
        /**
         * clear database from all current plugin's setting
         *
         * @method  clear_out_settings
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function clear_out_settings()
        {
          $this->set_deafault_setting(true);
        }
        /**
         * adminmenu callback
         *
         * @method  help_container
         * @param   array $hook
         * @return  string page content
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function help_container($hook)
        {
            ob_start();
            wp_enqueue_style("{$this->db_slug}_bkend", "{$this->assets_url}css/backend" . $this->debug_enabled(".css",".min.css"));
            wp_enqueue_script("{$this->db_slug}_bkend", "{$this->assets_url}/js/settings". $this->debug_enabled(".js",".min.js"), array('jquery','wp-color-picker'), null, true);
            wp_add_inline_style("{$this->db_slug}_bkend", ".form-table th {} ");
            is_rtl() AND wp_add_inline_style("{$this->db_slug}_bkend", ".form-table th {}#wpfooter, #wpbody-content *:not(.dashicons ), #wpbody-content input:not([dir=ltr]), #wpbody-content textarea:not([dir=ltr]), h1.had{ font-family: bodyfont, roboto, Tahoma; }");
            $this->update_footer_info();
            $n___defs = 'dir="ltr" lang="en-US" min="0" step="1" required';
            $t___defs = 'dir="ltr" lang="en-US" required';
            $s___defs = 'required';

            echo "<h1 class='had'>".$this->title_w."</h1>";
            echo '<div class="wrap">';

            // Docmunentations are shown here
            $hll = "<dev><h4><strong>".__("Shortcode and Hooks for Developers", $this->td)."</strong></h4>
            <pre class='caqpde' dir='ltr' align='left' lang='en-US'>".implode(
                apply_filters(
                    "{$this->db_slug}_documentation", array(
                    "<b ".(is_rtl()?"class='fa'":"").">"._x("WordPress Shortcode", "setting-general", $this->td)."</b><hr>",
                    '<strong class="tag">[puiw_quick_shop el_id="" el_class=""]</strong>      <i>Outputs Quick Shop page. You can use Visual Composer Widget too.</i>',



                    '<br />',
                    "<b ".(is_rtl()?"class='fa'":"").">"._x("WordPress Action Hooks", "setting-general", $this->td).'</b>',
                    '<hr>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',



                    '<br />',
                    "<b ".(is_rtl()?"class='fa'":"").">"._x("WordPress Filter Hooks", "setting-general", $this->td).'</b>',
                    '<hr>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',



                    '<br />',
                    "<b ".(is_rtl()?"class='fa'":"").">"._x("jQuery Triggering Hooks", "setting-general", $this->td).'</b>',
                    '<hr>',
                    '<strong class="tag" style="display: block;" >const PEOPCA_MOTHER = $(".pepro-one-page-purchase---top-parent");</strong>',
                    '<strong class="tag" >order_ajax_request_success</strong>            <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button successes</i>',
                    '<strong class="tag" >order_ajax_request_failed</strong>             <i>TRIGGERS ON DOCUMENT        --Fires on cart page, when "Get invoice" button fails</i>',
                    '<strong class="tag" >ajax_request_success</strong>                  <i>TRIGGERS ON PEOPCA_MOTHER   --Fires on instant shop page, when "Procceed to chekout" button successes</i>',
                    '<strong class="tag" >ajax_request_failed</strong>                   <i>TRIGGERS ON PEOPCA_MOTHER   --Fires on instant shop page, when "Procceed to chekout" button fails</i>',
                    '<strong class="tag" >update_cart</strong>                           <i>TRIGGERS ON PEOPCA_MOTHER   --Fires on instant shop page, when basket data updates</i>',
                    '<strong class="tag" >startup</strong>                               <i>TRIGGERS ON PEOPCA_MOTHER   --Fires on instant shop page, when page loads</i>',
                    )
                ), "\n"
            )."</pre></dev>";
            echo "$hll</div>";
            $tcona = ob_get_contents();
            ob_end_clean();
            print $tcona;



        }
        /**
         * change admin area footer text
         *
         * @method  update_footer_info
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
         public function update_footer_info()
         {
            $f = "pepro_temp_stylesheet.".current_time("timestamp");
            wp_register_style($f, null);
            wp_add_inline_style($f," #footer-left b a::before { content: ''; background: url('{$this->assets_url}/img/peprodev.svg') no-repeat; background-position-x: center; background-position-y: center; background-size: contain; width: 60px; height: 40px; display: inline-block; pointer-events: none; position: absolute; -webkit-margin-before: calc(-60px + 1rem); margin-block-start: calc(-60px + 1rem); -webkit-filter: opacity(0.0);
            filter: opacity(0.0); transition: all 0.3s ease-in-out; }#footer-left b a:hover::before { -webkit-filter: opacity(1.0); filter: opacity(1.0); transition: all 0.3s ease-in-out; }[dir=rtl] #footer-left b a::before {margin-inline-start: calc(30px);}");
            wp_enqueue_style($f);
            add_filter( 'admin_footer_text', function () { return sprintf(_x("Thanks for using %s products.", "footer-copyright", $this->td), "<b><a href='https://pepro.dev/' target='_blank' >".__("Pepro Dev", $this->td)."</a></b>");}, 11000 );
            add_filter( 'update_footer', function () { return sprintf(_x("%s — Version %s", "footer-copyright", $this->td), $this->title, $this->version); }, 1100 );
          }
        /**
         * receive and return ajax json/data
         *
         * @method  handel_ajax_req
         * @return  json
         * @version 1.1.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function handel_ajax_req()
        {
            if(wp_doing_ajax() && !empty($_POST['wparam']) && !empty($_POST['nonce'])) {
                if (!wp_verify_nonce($_POST['nonce'], $this->td) ) {
                    wp_send_json_error(array("message"=>__('Unauthorized Access Denied!', $this->td)));
                    die();
                }
                global $woocommerce;
                switch ($_POST['wparam']) {
                  case "add-cart":
                    $cart_date = sanitize_post($_POST['lparam']);
                    if (is_array($cart_date) && !empty($cart_date)){
                      $woocommerce->cart->empty_cart();
                      foreach ($cart_date as $pid => $qty) {
                        $product = wc_get_product($pid);
                        if (!$product) {
                          continue;
                        }
                        if ($product->get_type() === "simple" && $product->get_stock_status() === "instock") {
                          $woocommerce->cart->add_to_cart($pid, $qty);
                        }
                      }
                      wp_send_json_success(array("message"=>__('Products successfully added to cart!', $this->td),"url"=>wc_get_cart_url()));
                      die();
                    }
                    else{
                      wp_send_json_error(array("message"=>__('Incorrect data!', $this->td)));
                      die();
                    }
                    break;
                  case "place-order":
                    $customer_id = get_current_user_id();
                    $billing_address = array(
                      'first_name' => get_user_meta($customer_id, 'billing_first_name', true),
                      'last_name' => get_user_meta($customer_id, 'billing_last_name', true),
                      'company' => get_user_meta($customer_id, 'billing_company', true),
                      'address_1' => get_user_meta($customer_id, 'billing_address_1', true),
                      'address_2' => get_user_meta($customer_id, 'billing_address_2', true),
                      'city' => get_user_meta($customer_id, 'billing_city', true),
                      'state' => get_user_meta($customer_id, 'billing_state', true),
                      'postcode' => get_user_meta($customer_id, 'billing_postcode', true),
                      'country' => get_user_meta($customer_id, 'billing_country', true),
                      'email' => get_user_meta($customer_id, 'billing_email', true),
                      'phone' => get_user_meta($customer_id, 'billing_phone', true),
                    );
                    $shipping_address = array(
                      'first_name' => get_user_meta($customer_id, 'shipping_first_name', true),
                      'last_name'  => get_user_meta($customer_id, 'shipping_last_name', true),
                      'company'  => get_user_meta($customer_id, 'shipping_company', true),
                      'address_1'  => get_user_meta($customer_id, 'shipping_address_1', true),
                      'address_2'  => get_user_meta($customer_id, 'shipping_address_2', true),
                      'city'  => get_user_meta($customer_id, 'shipping_city', true),
                      'state'  => get_user_meta($customer_id, 'shipping_state', true),
                      'postcode'  => get_user_meta($customer_id, 'shipping_postcode', true),
                      'country'  => get_user_meta($customer_id, 'shipping_country', true),
                    );
                    $default_args = array(
                      'status' => "wc-prebuy-invoice",
                      'customer_id' => $customer_id,
                      'customer_note' => wp_kses_post( nl2br( wptexturize($this->tpl->get_preorder_customer_extra_note()))),
                    );
                    $order = wc_create_order($default_args);
                    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
                        $item_id = $order->add_product(
                            $values['data'],
                            $values['quantity'],
                            array(
                              'variation' => $values['variation'],
                              'totals' => array(
                                'subtotal' => $values['line_subtotal'],
                                'subtotal_tax' => $values['line_subtotal_tax'],
                                'total' => $values['line_total'],
                                'tax' => $values['line_tax'],
                                'tax_data' => $values['line_tax_data'] // Since 2.2
                              )
                            )
                        );
                    }
                    $order->set_address($billing_address, 'billing');
                    $order->set_address($shipping_address, 'shipping');
                    $order->add_order_note(sprintf(__("Invoice Created by %s%s", $this->td), "<strong>{$this->title_w}</strong>", "<br /><a class='delete_note' style='font-size: 0.7rem;' href='https://pepro.dev/'>".__("Pepro Dev. Group", $this->td)."</a>"));
                    $order->calculate_totals();
                    $order->save();
                    update_post_meta( $order->get_id(), 'puiw_shopmngr_provided_note', wp_kses_post( nl2br( wptexturize($this->tpl->get_preorder_shopmngr_extra_note()))));
                    if ("yes" == $this->tpl->get_allow_preorder_emptycart()){ $woocommerce->cart->empty_cart(); }
                    wp_send_json_success(
                        array(
                          "url" => home_url("?invoice=".$order->get_order_number()),
                          "view" => $order->get_view_order_url(),
                          "invoice" => $order->get_order_number(),
                          "msg" => __('Request success!', $this->td),
                        )
                    );
                    die();
                    break;
                  case "send-mail-html":
                    $order_id = (int) trim($_POST['lparam']);
                    $order = wc_get_order($order_id);
                    if (!$order){wp_send_json_error( array("msg"=>__("No valid order",$this->td)));}
                    $id = $order->get_id();
                    $email = $order->get_billing_email();
                    if (!empty($_POST['qparam'])){$email = $_POST['qparam'];}
                    $advanced = false;
                    if (!empty($_POST['eparam'])){
                      global $puiw_send_mail_params_advanced;
                      $puiw_send_mail_params_advanced = sanitize_post($_POST['eparam']);

                      add_filter( "puiw_get_default_dynamic_params", function($opts, $order)
                      {
                        global $puiw_send_mail_params_advanced;
                        $g_GET = $puiw_send_mail_params_advanced;

                        if ( isset($g_GET["tp"]) && !empty($g_GET["tp"]) ){
                          $opts["template"] = sanitize_text_field(base64_decode(urldecode($g_GET["tp"])));
                          $opts["preinvoice_template"] = sanitize_text_field(base64_decode(urldecode($g_GET["tp"])));
                        }

                        if ( isset($g_GET["pclr"]) && !empty($g_GET["pclr"]) ){
                          $opts["theme_color"] = sanitize_hex_color(base64_decode(urldecode(trim($g_GET["pclr"]))));
                          $opts["preinvoice_theme_color"] = sanitize_hex_color(base64_decode(urldecode($g_GET["pclr"])));
                        }

                        if ( isset($g_GET["sclr"]) && !empty($g_GET["sclr"]) ){
                          $opts["theme_color2"] = sanitize_hex_color(base64_decode(urldecode($g_GET["sclr"])));
                          $opts["preinvoice_theme_color2"] = sanitize_hex_color(base64_decode(urldecode($g_GET["sclr"])));
                        }

                        if ( isset($g_GET["tclr"]) && !empty($g_GET["tclr"]) ){
                          $opts["theme_color3"] = sanitize_hex_color(base64_decode(urldecode($g_GET["tclr"])));
                          $opts["preinvoice_theme_color3"] = sanitize_hex_color(base64_decode(urldecode($g_GET["tclr"])));
                        }

                        return $opts;
                      } , 10, 2);

                    }
                    if (!empty($email)){
                      if (!empty($_POST['dparam']) && "PDF" == trim($_POST['dparam'])){
                        $wp_mail = $this->send_formatted_email($order_id, $email, true);
                      }else{
                        $wp_mail = $this->send_formatted_email($order_id, $email, false);
                      }

                      $email_label = (is_array($email)&&count($email)>0) ? count($email) > 3 ? sprintf(__("%d emails",$this->td),count($email)) : implode("<br>", $email) : $email;

                      if ($wp_mail) {
                        wp_send_json_success( array( "e" => $puiw_send_mail_params_advanced, "msg"=> sprintf(__("Sending email to <br><strong>%s</strong><br>was successfully done",$this->td), $email_label)));
                      }else{
                        wp_send_json_error( array( "e" => $puiw_send_mail_params_advanced, "msg"=> sprintf(__("Error sending email to <br><strong>%s</strong>",$this->td), $email_label)));
                      }
                    }else{
                      wp_send_json_error( array("msg"=>__("No valid email found for this order",$this->td)));
                    }
                    die();
                    break;
                  case "retrive-admins-emails":
                    $shopmngrs_mail = $this->get_wc_managers();
                    if ($shopmngrs_mail && !empty($shopmngrs_mail) && count($shopmngrs_mail) > 0) {
                      wp_send_json_success( array("emails"=> $shopmngrs_mail));
                    }else{
                      wp_send_json_error( array("msg"=> sprintf(__("Error fetching shop managers.",$this->td), $email)));
                    }
                    die();
                    break;
                  case "save-swatches":
                    $lparam = sanitize_textarea_field( $_POST['lparam'] );
                    update_option( "puiw_color_swatches", $lparam);
                    wp_send_json_success( array( "msg" => __('Saved successfully!', $this->td), ) );
                    die();
                    break;
                }

            }
        }
        /**
         * list woocommerce managers and administrators
         *
         * @method  get_wc_managers
         * @return  array                     array of users with their name and email
         * @access  public
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function get_wc_managers()
        {
          $_wc_managers = array();
          $users = get_users( array(
            "role__in" => array( "administrator", "shop_manager" ) )
          );
          foreach ($users as $user) {
            $_wc_managers[$user->user_email] = "$user->user_firstname $user->user_lastname";
          }
          return $_wc_managers;
        }
        /**
         * add menu to dashboard, options page
         *
         * @method  admin_menu
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function admin_menu()
        {
          add_submenu_page("woocommerce", $this->title, $this->title, "manage_options", $this->url);
        }
        /**
         * fire this hook on admin side load
         *
         * @method  admin_init
         * @param   array $hook
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function admin_init($hook)
        {
            if (!$this->_wc_activated()) {
                add_action(
                    'admin_notices', function () {
                        echo "<div class=\"notice error\"><p>".sprintf(
                            _x('%1$s needs %2$s in order to function', "required-plugin", "$this->td"),
                            "<strong>".$this->title."</strong>", "<a href='".admin_url("plugin-install.php?s=woocommerce&tab=search&type=term")."' style='text-decoration: none;' target='_blank'><strong>".
                            _x("WooCommerce", "required-plugin", "$this->td")."</strong> </a>"
                        )."</p></div>";
                    }
                );
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
                deactivate_plugins(plugin_basename(__FILE__));
            }

            if ("yes" == $this->tpl->get_allow_quick_shop()){
              if ($this->_vc_activated()) {
                  add_shortcode(  "puiw_quick_shop",  array($this,  "integrate_with_shortcode"));
                  add_action(     "vc_before_init",   array($this,  "integrate_with_vc"));
                  if (function_exists('vc_add_shortcode_param')) {
                      vc_add_shortcode_param("{$this->td}_about", array($this,'vc_add_pepro_about'), plugins_url("/assets/js/vc.init" . $this->debug_enabled(".js",".min.js"), __FILE__));
                  }
              }
            }
            $this->set_deafault_setting();
            $peproultimateinvoice_options = $this->get_setting_options();
            foreach ($peproultimateinvoice_options as $sections) {
                foreach ($sections["data"] as $id=>$def) {
                    add_option($id, $def);
                    register_setting($sections["name"], $id);
                }
            }
            add_action("add_meta_boxes",  array( $this,"add_meta_boxes") );
            add_action("admin_enqueue_scripts", array( $this,'admin_enqueue_scripts'));
            add_action('save_post', array( $this, 'wc_save_shop_order_metabox' ) );
            add_filter("woocommerce_email_styles", array( $this,"woocommerce_email_styles_edit"));

        }
        /**
         * Woocommerce Email Styles Edit
         *
         * @method woocommerce_email_styles_edit
         * @param string $css
         * @return string css code
         * @version 1.0.0
         * @since 1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woocommerce_email_styles_edit($css)
        {
          $bg        = get_option( 'woocommerce_email_background_color' );
          $body      = get_option( 'woocommerce_email_body_background_color' );
          // $base      = $this->tpl->get_theme_color(get_option( 'woocommerce_email_base_color',"teal"));
          $base      = get_option( 'woocommerce_email_base_color' );
          $base_text = wc_light_or_dark( $base, '#202020', '#ffffff' );
          $text      = get_option( 'woocommerce_email_text_color' );
          $link_color = wc_hex_is_light( $base ) ? $base : $base_text;
          if ( wc_hex_is_light( $body ) ) {
            $link_color = wc_hex_is_light( $base ) ? $base_text : $base;
          }
          $bg_darker_10    = wc_hex_darker( $bg, 10 );
          $body_darker_10  = wc_hex_darker( $body, 10 );
          $base_lighter_20 = wc_hex_lighter( $base, 20 );
          $base_lighter_40 = wc_hex_lighter( $base, 40 );
          $text_lighter_20 = wc_hex_lighter( $text, 20 );
          $text_lighter_40 = wc_hex_lighter( $text, 40 );

          ob_start();
          echo $css;
          echo " @font-face { font-family: 'iranyekan'; font-style: normal; font-weight: normal; src: url('".PEPROULTIMATEINVOICE_URL."/assets/css/96594ad4.woff2') format('woff2'); }";
          $currentDir = PEPROULTIMATEINVOICE_URL . "/template/default";
          ?>
          @font-face {
          	font-family: iranyekan;
          	font-style: normal;
          	font-weight: bold;
          	src: url("<?php echo $currentDir;?>/fonts/woff2/iranyekanwebbold.woff2") format('woff2'), url("<?php echo $currentDir;?>/fonts/woff/iranyekanwebbold.woff") format('woff'));
          }

          @font-face {
          	font-family: iranyekan;
          	font-style: normal;
          	font-weight: normal;
          	src: url("<?php echo $currentDir;?>/fonts/woff2/iranyekanwebregular.woff2") format('woff2'), url("<?php echo $currentDir;?>/fonts/woff/iranyekanwebregular.woff") format('woff'));
          }

          #wrapper { background-color: <?php echo esc_attr( $bg ); ?>!important; }
          #template_container { background-color: <?php echo esc_attr( $body ); ?>!important; border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>!important; }
          #template_header { background-color: <?php echo esc_attr( $base ); ?>!important; color: <?php echo esc_attr( $base_text ); ?>!important; }
          #template_header h1, #template_header h1 a { color: <?php echo esc_attr( $base_text ); ?>!important; }
          #template_footer #credit { color: <?php echo esc_attr( $text_lighter_40 ); ?>!important; }
          #body_content { background-color: <?php echo esc_attr( $body ); ?>!important; }
          .td, .address { border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>!important; }
          h1 { text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>!important; }
          .td, .address, #body_content_inner { color: <?php echo esc_attr( $text_lighter_20 ); ?>!important; }
          h1, h2, h3, .text, .link{ color: <?php echo esc_attr( $base ); ?>!important; }
          a { color: <?php echo esc_attr( $link_color ); ?>!important; }
          body, html, p, td, th, table, tr, a, span, h1, h2, h3, h4, h5, h6, pre, #template_header, #template_footer #credit, #body_content_inner, .text { font-family: iranyekan, Tahoma, Arial, sans-serif !important; }
          h1, strong, h2{font-weight: bold !important;}
          <?php
          $css = ob_get_clean();
          ob_end_clean();
          return $css;
        }
        /**
         * funtion for [puiw_quick_shop] shortcode, callback fn
         *
         * @method  integrate_with_shortcode
         * @param   array $atts shortcode attributes
         * @return  string shortcode data
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function integrate_with_shortcode($atts=array(),$content)
        {
            $atts = extract(
                shortcode_atts(
                    array(
                    'css'=>"",
                    'el_class'=>"",
                    'el_id'=>"",
                    ), $atts
                )
            );
            ob_start();
            $css_class = "";
            if ($this->_vc_activated()) {
              $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), "puiw_quick_shop", $atts);
            }
            $uniqid = uniqid("{$this->db_slug}-");
            echo "<div class='pepro-one-page-purchase---top-parent $uniqid $el_class $css_class' id='$el_id'>";
            $cats = "<option value=\"[ALL]\">".__("All Categories", $this->td)."</option>";
            $products = "";
            $products_js = array();
            $cat_args = array( 'orderby'=> "name", 'order'=> "ASC", 'hide_empty' => true, );
            $prodcuts_args = array(
              'orderby' => 'modified',
              'order' => 'DESC',
              'return' => 'ids',
              'status' => 'publish',
              'type' => 'simple',
              'stock_status' => 'instock',
              'limit' => -1,
            );
            $product_categories = get_terms('product_cat', apply_filters("pepro-one-page-purchase--categories-args", $cat_args));
            if(!empty($product_categories) ) {
                foreach ($product_categories as $key => $category) {
                    $cats .= "<option value=\"$category->slug\">$category->name</option>";
                }
            }
            // https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
            $loop = wc_get_products(apply_filters("pepro-one-page-purchase--prodcuts-args", $prodcuts_args));
            $currencysymbol = get_woocommerce_currency_symbol();
            foreach ($loop as $product) {
                $product = wc_get_product($product);
                $price = $product->get_price_html();
                $sku = $product->get_sku();
                $total_sales = get_post_meta($product->get_id(), 'total_sales', true);
                $_wc_average_rating = get_post_meta($product->get_id(), '_wc_average_rating', true);
                $skutxt = _x("SKU:", "js-i18n", $this->td);
                $skutxt = _x("SKU:", "js-i18n", $this->td);
                $stock = $product->get_stock_quantity();
                //https://www.php.net/manual/en/function.date.php
                $dateformat = apply_filters("pepro-one-page-purchase--prodcuts-date-format", "Y-m-j h:i:s");
                $imgurl = wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0];
                $categoris_raw = strip_tags(wc_get_product_category_list($product->get_id()));
                $updated = $product->get_date_modified()->format($dateformat);
                $products .= "
            <li class=\"pepro-one-page-purchase--product-item catfiltered\" data-pid=\"{$product->get_id()}\" data-last-update=\"{$updated}\">
              <div class=\"pepro-one-page-purchase--product-image\"><img src=\"$imgurl\" style=\"height: 96px;\" /></div>
              <div class=\"pepro-one-page-purchase--product-item-info\">
                <div class=\"pepro-one-page-purchase--product-item-info-primary\" data-total-sales='{$total_sales}' data-av-rating='{$_wc_average_rating}' >
                  <div class=\"pepro-one-page-purchase--product-title\"><a target='_blank' href=\"".get_permalink($product->get_id())."\">{$product->get_name()}</a></div>
                  <div class=\"pepro-one-page-purchase--product-item-info-secondary\">
                    <div class=\"pepro-one-page-purchase--product-cat\">{$categoris_raw}</div>
                    <div class=\"pepro-one-page-purchase--product-sku\">{$skutxt} {$product->get_sku()}</div>
                    </div>
                  </div>
                <div class=\"pepro-one-page-purchase--product-item-pruchase\">
                  <div class=\"pepro-one-page-purchase--product-item-pricelist\">
                    <div class=\"pepro-one-page-purchase--product-regular_price\" data-raw=\"{$product->get_regular_price()}\">".wc_price($product->get_regular_price(), array("currency"=>" "))."</div>
                    <div class=\"pepro-one-page-purchase--product-sale_price\" data-raw=\"{$product->get_price()}\">".wc_price($product->get_price(), array("currency"=>" "))."</div>
                  </div>
                  <div class=\"pepro-one-page-purchase--product-item-pricesymbol\">
                    <div class=\"pepro-one-page-purchase--product-currency_symbol\">".$currencysymbol."</div>
                  </div>
                </div>
                <div class=\"pepro-one-page-purchase--product-add2cart\" >
                  <a href=\"#\" data-pid=\"{$product->get_id()}\" class=\"pepro-one-page-purchase--add2cart\" title=\"".__("Add to cart", $this->td)."\" >
                    <i class=\"fa fa-shopping-bag\"></i>
                  </a>
                </div>

              </div>
            </li>";
                $products_js[$product->get_id()] = array(
                "get_id"              =>  $product->get_id(),
                "get_date"            =>  $product->get_date_modified()->format($dateformat),
                "get_name"            =>  $product->get_name(),
                "get_sku"             =>  $product->get_sku(),
                "get_permalink"       =>  get_permalink($product->get_id()),
                "get_price_raw"       =>  $product->get_price(),
                "get_price"           =>  wc_price($product->get_price(), array("currency"=>" ")),
                "get_regular_price_raw" => $product->get_regular_price(),
                "get_regular_price"   =>  wc_price($product->get_regular_price(), array("currency"=>" ")),
                "get_sale_price_raw"  =>  $product->get_sale_price(),
                "get_sale_price"      =>  wc_price($product->get_sale_price(), array("currency"=>" ")),
                "get_image"           =>  $imgurl,
                );
            }
            ?>
            <script>
              var peproOnePagePurchaseAndInvoice = <?php echo json_encode($products_js);?>;
            </script>
            <div class="pepro-one-page-purchase--container tool">
              <div class="pepro-one-page-purchase--categories-container">
                <div class="pepro-one-page-purchase--categories">
                  <h4 class="pepro-one-page-purchase--title"><?php echo __("Filter Products by Category", $this->td);?></h4>
                  <select id="pepro-one-page-purchase--select-categories"><?php echo $cats;?></select>
                </div>
              </div>
              <div class="pepro-one-page-purchase--products">
                <div class="pepro-one-page-purchase--product-search">
                  <h4 class="pepro-one-page-purchase--title"><?php echo __("Sort and Search shown products", $this->td);?></h4>
                  <div class="pepro-one-page-purchase--product-search-container">
                    <input type="search" id="pepro-one-page-purchase--search-input" placeholder="<?php echo __("Search among current shown products", $this->td);?> ..." title="<?php echo __("Search among current shown products", $this->td);?>" />
                  </div>
                  <div class="pepro-one-page-purchase--product-sort-container">
                    <span class="pepro-one-page-purchase--title"><?php echo __("Sort By: ", $this->td);?></span>
                    <a class="pepro-one-page-purchase-filter" data-query="alphabetically" href="javascript:void(0);"><?php echo __("Alphabetically", $this->td);?></a>
                    <a class="pepro-one-page-purchase-filter" data-query="popularity" href="javascript:void(0);"><?php echo __("Popularity", $this->td);?></a>
                    <a class="pepro-one-page-purchase-filter" data-query="total_sales" href="javascript:void(0);"><?php echo __("Total Sales", $this->td);?></a>
                    <a class="pepro-one-page-purchase-filter active" data-query="latest" href="javascript:void(0);"><?php echo __("Latest", $this->td);?></a>
                    <a class="pepro-one-page-purchase-filter" data-query="price_asc" href="javascript:void(0);"><?php echo __("Price (ASC)", $this->td);?></a>
                    <a class="pepro-one-page-purchase-filter" data-query="price_desc" href="javascript:void(0);"><?php echo __("Price (DESC)", $this->td);?></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="pepro-one-page-purchase--container">
            <div class="pepro-one-page-purchase--cart-list">
              <div class="pepro-one-page-purchase--cart-body" data-empty="<?php echo __("Your cart is empty!", $this->td);?>"></div>
            </div>
            <ul class="pepro-one-page-purchase--product-list" data-empty="<?php echo __("Nothing found!", $this->td);?>">
                <?php echo $products;?>
            </ul>
            </div>
            <?php
            echo "</div>";
            wp_enqueue_style("$this->td-ml", "$this->assets_url/css/mobileLayer" . $this->debug_enabled(".css",".min.css"), array(), "1.0.0", "all");
            wp_enqueue_script("$this->td-ml", "$this->assets_url/js/mobileLayer". $this->debug_enabled(".js",".min.js"), array("jquery","jquery-ui-core"), "1.0.0", true);
            wp_enqueue_style("$this->td", "$this->assets_url/css/front-end" . $this->debug_enabled(".css",".min.css"), array(), "1.0.0", "all");
            wp_enqueue_script("$this->td", "$this->assets_url/js/front-end". $this->debug_enabled(".js",".min.js"), array("jquery"), "1.0.0", true);
            wp_enqueue_style("select2", "{$this->assets_url}css/select2.min.css", false, "4.0.6", "all");
            wp_enqueue_script("select2", "{$this->assets_url}js/select2.min.js", array( "jquery" ), "4.0.6", true);
            wp_localize_script("$this->td", "_i18n",
              array(
                "ajax"=> admin_url("admin-ajax.php"),
                "td"=> "puiw_$this->td",
                "okaylabel" => _x("Okay", "js-cart-page", $this->td),
                "nonce"=> wp_create_nonce($this->td),
                "addedtocard"=>_x("Added to cart", "js-i18n", $this->td),
                "thisIsAnError"=>_x("THIS IS AN ERROR!", "js-i18n", $this->td),
                "emptycartSubmit"=>_x("Your basket is empty!", "js-i18n", $this->td),
                "tti"=>_x("Total Items:", "js-i18n", $this->td),
                "ttp"=>_x("Total Products:", "js-i18n", $this->td),
                "unknownError"=>_x("There is an unknown error and we are unable to proccess your request", "js-i18n", $this->td),
                "removefromcart"=>_x("Remove from cart", "js-i18n", $this->td),
                "removefromcartContent"=>_x("Clear cart", "js-i18n", $this->td),
                "skuText"=>_x("SKU:", "js-i18n", $this->td),
                "removeFrombasketConfirmation"=>_x("Are you sure you want to remove this item from basket?", "js-i18n", $this->td),
                "removeFrombasketConfirmation2"=>_x("Are you sure you want to remove all items from basket?", "js-i18n", $this->td),
                "removeFrombasketTitle"=>_x("Removing", "js-i18n", $this->td),
                "removeFrombasketTitle2"=>_x("Clear basket", "js-i18n", $this->td),
                "instantShoppingBasketUpated"=>_x("Instant Shopping Basket Upated!", "js-i18n", $this->td),
                "confirmYes"=>_x("Yes", "js-i18n", $this->td),
                "confirmNo"=>_x("No", "js-i18n", $this->td),
                "currencySymbol"=>$currencysymbol,
                "proceedToCheckout"=>_x("Proceed to Checkout / Invoice", "js-i18n", $this->td),
                )
            );
            $s = ob_get_contents();
            ob_end_clean();
            return do_shortcode("$s");
        }
        /**
         * add Visual Composer widget if it was installed
         *
         * @method  integrate_with_vc
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function integrate_with_vc()
        {
            vc_map(array(
                  'base' => "puiw_quick_shop",
                  'name' => __("Quick Shop", $this->td),
                  'description' => __('One-page Purchase', "$this->td"),
                  'class' => "{$this->td}__class",
                  'icon' => plugin_dir_url(__file__)."assets/img/pepro.png",
                  'show_settings_on_create' => true,
                  'admin_enqueue_css' => array("{$this->assets_url}/css/vc.init" . $this->debug_enabled(".css",".min.css"),"{$this->assets_url}/css/select2.min.css"),
                  'admin_enqueue_js' => array("{$this->assets_url}/js/select2.min.js"),
                  'category' => __('Pepro Elements', "$this->td"),
                  'params' => array(
                    array(
                        'group' => __("Setting", "$this->td"),
                        'type' => "{$this->td}_about",
                        'edit_field_class' => 'vc_column vc_col-sm-12',
                        'admin_label' => false,
                        'param_name' => "{$this->td}_about",
                    ),
                    // vc_map_add_css_animation(),
                    array(
                        'type' => 'el_id',
                        'heading' => esc_html__('Element ID', $this->td),
                        'param_name' => 'el_id',
                        'edit_field_class' => 'vc_column vc_col-sm-6',
                        'description' => sprintf(esc_html__('Enter element ID (Note: make sure it is unique and valid according to %sW3C Specification%s).', $this->td), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>'),
                        'group' => esc_html__('Design Options', $this->td),
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Extra class name', $this->td),
                        'edit_field_class' => 'vc_column vc_col-sm-6',
                        'param_name' => 'el_class',
                        'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', $this->td),
                        'group' => esc_html__('Design Options', $this->td),
                    ),
                    array(
                        'type' => 'css_editor',
                        'heading' => esc_html__('CSS box', $this->td),
                        'param_name' => 'css',
                        'group' => esc_html__('Design Options', $this->td),
                    ),
            )));
        }
        /**
         * Visual Composer custom widgets type
         *
         * @method  vc_add_pepro_about
         * @param   array  $settings
         * @param   string $value
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function vc_add_pepro_about($settings, $value)
        {
            ob_start();
            echo "<div style='display: flex;align-items: center;justify-content: flex-start;flex-direction: row-reverse;'>
                  <p style='margin-right: 1rem;'><img src='".plugins_url("/assets/img/pepro-logo.png", __FILE__)."' width='55px' /></p>
                  <p>Proudly Developed by <a target='_blank' href='https://pepro.dev/'>Pepro Dev. Group</a></p>
                </div>";
            $tcona = ob_get_contents();
            ob_end_clean();
            return $tcona;
        }

        /* ======================== MISC. FNs ======================== */

        /**
         * check if woocommerce is activated
         *
         * @method  _wc_activated
         * @return  boolean true if installed and activated
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function _wc_activated()
        {
            if (!function_exists('is_woocommerce')
                || !class_exists('woocommerce')
            ) {
                return false;
            }else{
                return true;
            }
        }
        /**
         * check if visual composer is activated
         *
         * @method  _wc_activated
         * @return  boolean true if installed and activated
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function _vc_activated()
        {
            if (!defined('WPB_VC_VERSION')) {
                return false;
            }else{
                return true;
            }
        }
        /**
         * read wp option from database
         *
         * @method  read_opt
         * @param   string $mc  setting name
         * @param   string $def default value
         * @return  string option value
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function read_opt($mc, $def="")
        {
            return get_option($mc) <> "" ? get_option($mc) : $def;
        }
        /**
         * plugins row links
         *
         * @method  plugins_row_links
         * @param   array $links
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function plugins_row_links($links)
        {
            foreach ($this->get_manage_links() as $title => $href) {
                array_unshift($links, "<a href='$href'>$title</a>");
            }
            return $links;
        }
        /**
         * plugins meta links
         *
         * @method  plugin_row_meta
         * @param   array  $links
         * @param   string $file  plugin file
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function plugin_row_meta($links, $file)
        {
            if ($this->plugin_basename === $file) {
                $icon_attr = array(
                'style' => array(
                    'font-size: inherit;',
                    'line-height: inherit;',
                    'display: inline;',
                    'vertical-align: text-top;',
                ),
                );
                foreach ($this->get_meta_links() as $id => $link) {
                    $title = (!empty($link['icon'])) ? self::do_icon($link['icon'], $icon_attr) . ' ' . esc_html($link['title']) : esc_html($link['title']);
                    $links[ $id ] = '<a href="' . esc_url($link['url']) . '" title="'.esc_attr($link['description']).'" target="'.(empty($link['target'])?"_blank":$link['target']).'">' . $title . '</a>';
                }
            }
            unset($links[2]); // hide visit plugin page
            return $links;
        }
        /**
         * print out html icon for dashicons
         *
         * @method  do_icon
         * @param   string $icon icon name
         * @param   array  $attr el's attributes
         * @return  string html icon el
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public static function do_icon($icon, $attr = array(), $content = '')
        {
            $class = '';
            if (false === strpos($icon, '/') && 0 !== strpos($icon, 'data:') && 0 !== strpos($icon, 'http')) {
                // It's an icon class.
                $class .= ' dashicons ' . $icon;
            } else {
                // It's a Base64 encoded string or file URL.
                $class .= ' vaa-icon-image';
                $attr   = self::merge_attr(
                    $attr, array(
                    'style' => array( 'background-image: url("' . $icon . '") !important' ),
                    )
                );
            }

            if (! empty($attr['class'])) {
                $class .= ' ' . (string) $attr['class'];
            }
            $attr['class']       = $class;
            $attr['aria-hidden'] = 'true';

            $attr = self::parse_to_html_attr($attr);
            return '<span ' . $attr . '>' . $content . '</span>';
        }
        /**
         * make attributes from html tag
         *
         * @method  parse_to_html_attr
         * @param   array $array attributes
         * @return  string attributes
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public static function parse_to_html_attr($array)
        {
            $str = '';
            if (is_array($array) && ! empty($array)) {
                foreach ($array as $attr => $value) {
                    if (is_array($value)) {
                        $value = implode(' ', $value);
                    }
                    $array[ $attr ] = esc_attr($attr) . '="' . esc_attr($value) . '"';
                }
                $str = implode(' ', $array);
            }
            return $str;
        }
        /**
         * print input field for setting
         *
         * @method  print_setting_iput
         * @param   string $SLUG
         * @param   string $CAPTION
         * @param   string $extraHtml
         * @param   string $type
         * @param   string $extraClass
         * @return  string input html
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function print_setting_iput($SLUG="", $CAPTION="", $extraHtml="", $type="text",$extraClass="")
        {
            $ON = sprintf(_x("Enter %s", "setting-page", $this->td), $CAPTION);
            echo "<tr>
            			<th scope='row'>
            				<label for='$SLUG'>$CAPTION</label>
            			</th>
            			<td>
                    <input name='$SLUG' $extraHtml type='$type' id='$SLUG' placeholder='$CAPTION' title='$ON' value='" . $this->read_opt($SLUG) . "' class='regular-text $extraClass' />
                  </td>
    		        </tr>";
        }
        /**
         * print select option field for setting
         *
         * @method  print_setting_select
         * @param   string $SLUG
         * @param   string $CAPTION
         * @param   array  $dataArray
         * @return  string option html
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function print_setting_select($SLUG="", $CAPTION="", $dataArray=array())
        {
            $ON = sprintf(_x("Choose %s", "setting-page", $this->td), $CAPTION);
            $OPTS = "";
            foreach ($dataArray as $key => $value) {
                if ($key == "EMPTY") {
                    $key = "";
                }
                $OPTS .= "<option value='$key' ". selected($this->read_opt($SLUG), $key, false) .">$value</option>";
            }
            echo "<tr>
    			<th scope='row'>
    				<label for='$SLUG'>$CAPTION</label>
    			</th>
    			<td><select name='$SLUG' id='$SLUG' title='$ON' class='regular-text'>
          ".$OPTS."
          </select>
          </td>
    		</tr>";
        }
        /**
         * print wp editor for setting
         *
         * @method  print_setting_editor
         * @param   string $SLUG
         * @param   string $CAPTION
         * @param   string $re
         * @return  string wp editor
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function print_setting_editor($SLUG="", $CAPTION="", $re="")
        {
            echo "<tr><th><label for='$SLUG'>$CAPTION</label></th><td>";
            wp_editor(
                $this->read_opt($SLUG, ''), strtolower(str_replace(array('-', '_', ' ', '*'), '', $SLUG)), array(
                'textarea_name' => $SLUG
                )
            );
            echo "<p class='$SLUG'>$re</p></td></tr>";
        }
        /**
         * sample callback fn
         *
         * @method  _callback
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function _callback($a)
        {
            return $a;
        }
        /**
         * get user ip address
         *
         * @method  getIP
         * @return  string ip address
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function getIP()
        {
            // Get server IP address
            $server_ip = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : '';

            // If website is hosted behind CloudFlare protection.
            if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $_SERVER['HTTP_CF_CONNECTING_IP'];
            }

            if (isset($_SERVER['X-Real-IP']) && filter_var($_SERVER['X-Real-IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $_SERVER['X-Real-IP'];
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) && $ip != $server_ip) {
                    return $ip;
                }
            }

            if (isset($_SERVER['DEV_MODE'])) {
                return '175.138.84.5';
            }

            return $_SERVER['REMOTE_ADDR'];
        }
    }
    /**
     * register plugin activation and deactivation statuses
     */
    /**
     * load plugin and load textdomain then set a global varibale to access plugin class!
     *
     * @method  PeproUltimateInvoice___plugin_init
     * @version 1.0.0
     * @since   1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    add_action("plugins_loaded", function(){
      global $PeproUltimateInvoice;
      load_plugin_textdomain("puice", false, dirname(plugin_basename(__FILE__))."/languages/");
      $PeproUltimateInvoice = new \peproulitmateinvoice\PeproUltimateInvoice;
      register_activation_hook(__FILE__, array("PeproUltimateInvoice", "activation_hook"));
      register_deactivation_hook(__FILE__, array("PeproUltimateInvoice", "deactivation_hook"));
      register_uninstall_hook(__FILE__, array("PeproUltimateInvoice", "uninstall_hook"));
    });
}
/*##################################################
Lead Developer: [amirhosseinhpv](https://hpv.im/)
##################################################*/
