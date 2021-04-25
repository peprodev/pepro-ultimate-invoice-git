<?php
# @Last modified time: 2021/04/25 08:39:44
namespace peproulitmateinvoice;
use voku\CssToInlineStyles\CssToInlineStyles;

defined("ABSPATH") or die("Pepro Ultimate Invoice :: Unauthorized Access!");

if (!class_exists("PeproUltimateInvoice_Print")) {
    class PeproUltimateInvoice_Print
    {
        protected $td;
        protected $fn;
        public function __construct()
        {
            $this->td = "puice";
            $this->fn = new PeproUltimateInvoice_Template;
            add_filter( "puiw_get_default_dynamic_params", array( $this, "puiw_get_default_dynamic_params"), 10, 2);
            // add compatibility with WPC Product Bundles for WooCommerce By WPClever
            $this->hide_bundles_parent = 0;
            $this->hide_bundles_child = 0;

              $this->_woosb_show_bundles = $this->fn->get_woosb_show_bundles();
            $this->_woosb_show_bundles_subtitle = $this->fn->get_woosb_show_bundles_subtitle();
            $this->_woosb_show_bundled_products = $this->fn->get_woosb_show_bundled_products();
            $this->_woosb_show_bundled_subtitle = $this->fn->get_woosb_show_bundled_subtitle();
            $this->_woosb_show_bundled_hierarchy = $this->fn->get_woosb_show_bundled_hierarchy();
            $this->_woosb_show_bundled_prefix = $this->fn->get_woosb_bundled_subtitle_prefix(_x("Bundled in:", "wc-setting", $this->td));
            $this->_woosb_show_bundles_prefix = $this->fn->get_woosb_bundles_subtitle_prefix(_x("Bundled products:", "wc-setting", $this->td));

            add_filter( "puiw_order_items", array($this, "puiw_sort_order_items"), 2, 2);

            if ($this->_woosb_show_bundles == "no"){
              add_filter( "puiw_order_items", array($this, "woosb_puiw_hide_bundles_parent"), 10, 2);
            }

            if ($this->_woosb_show_bundled_products == "no"){
              add_filter( "puiw_order_items", array($this, "woosb_puiw_hide_bundled_childs"), 10, 2);
            }


            add_action( "woocommerce_before_order_itemmeta", array( $this, "woosb_before_order_item_meta" ), 10, 1 );
            add_filter( "puiw_invoice_item_extra_classes", array($this, "puiw__item_extra_classes"), 10, 6);
            add_filter( "puiw_get_custom_css_style", array($this, "custom_css_per_invoice"), 10, 2);
            add_filter( "puiw_get_pdf_css_style", array($this, "custom_css_per_invoice"), 10, 2);
            add_filter( "puiw_get_inventory_css_style", array($this, "custom_css_per_invoice"), 10, 2);


        }
        public function puiw_get_default_dynamic_params($opts, $order)
        {

          if ( isset($_GET["tp"]) && !empty($_GET["tp"]) ){
            $opts["template"] = sanitize_text_field(base64_decode(urldecode($_GET["tp"])));
            $opts["preinvoice_template"] = sanitize_text_field(base64_decode(urldecode($_GET["tp"])));
          }

          if ( isset($_GET["pclr"]) && !empty($_GET["pclr"]) ){
            $opts["theme_color"] = sanitize_hex_color(base64_decode(urldecode(trim($_GET["pclr"]))));
            $opts["preinvoice_theme_color"] = sanitize_hex_color(base64_decode(urldecode($_GET["pclr"])));
          }

          if ( isset($_GET["sclr"]) && !empty($_GET["sclr"]) ){
            $opts["theme_color2"] = sanitize_hex_color(base64_decode(urldecode($_GET["sclr"])));
            $opts["preinvoice_theme_color2"] = sanitize_hex_color(base64_decode(urldecode($_GET["sclr"])));
          }

          if ( isset($_GET["tclr"]) && !empty($_GET["tclr"]) ){
            $opts["theme_color3"] = sanitize_hex_color(base64_decode(urldecode($_GET["tclr"])));
            $opts["preinvoice_theme_color3"] = sanitize_hex_color(base64_decode(urldecode($_GET["tclr"])));
          }

          return $opts;
        }
        public function get_default_dynamic_params($order_id,$order)
        {
          $opts = array(
              "order_date_created"                        => $order->get_date_created() ? $this->fn->get_date($order->get_date_created()) : "",
              "order_date_modified"                       => $order->get_date_modified() ? $this->fn->get_date($order->get_date_modified()) : "",
              "order_date_completed"                      => $order->get_date_completed() ? $this->fn->get_date($order->get_date_completed()) : "",
              "order_date_current"                        => $this->fn->get_date(date_i18n("Y/m/d H:i",current_time("timestamp"))),
              "order_date_paid"                           => $order->get_date_paid() ? $this->fn->get_date($order->get_date_paid()) :"",
              "order_status"                              => wc_get_order_status_name($order->get_status()),
              "order_total"                               => $order->get_formatted_order_total(),
              "order_date_shipped"                        => !empty(get_post_meta($order_id,"_shipping_puiw_invoice_shipdate",true)) ? $this->fn->get_date( get_post_meta($order_id,"_shipping_puiw_invoice_shipdate",true),"Y/m/d",true ) : "",
              "order_date_shipped_raw"                    => get_post_meta($order_id,"_shipping_puiw_invoice_shipdate",true),
              "order_date_shipped_raw_shamsi"             => get_post_meta($order_id,"_shipping_puiw_invoice_shipdatefa",true),
              "store_name"                                => $this->fn->get_store_name(),
              "store_logo"                                => $this->fn->get_store_logo(),
              "store_website"                             => $this->fn->get_store_website(),
              "store_email"                               => $this->fn->get_store_email(),
              "store_phone"                               => $this->fn->get_store_phone(),
              "store_address"                             => $this->fn->get_store_address(),
              "wc_store_address"                          => $this->fn->get_wc_store_address(),
              "store_postcode"                            => $this->fn->get_store_postcode(),
              "store_national_id"                         => $this->fn->get_store_national_id(),
              "store_registration_number"                 => $this->fn->get_store_registration_number(),
              "store_economical_number"                   => $this->fn->get_store_economical_number(),
              "send_invoices_via_email"                   => $this->fn->get_send_invoices_via_email(),
              "send_invoices_via_email_admin"             => $this->fn->get_send_invoices_via_email_admin(),
              "allow_users_use_invoices"                  => $this->fn->get_allow_users_use_invoices(),
              "show_invoices_id_barcode"                  => $this->fn->get_show_invoices_id_barcode(),
              "show_shippingslip_store"                   => $this->fn->get_show_shippingslip_store(),
              "show_shippingslip_customer"                => $this->fn->get_show_shippingslip_customer(),
              "show_qr_code_id"                           => $this->fn->get_show_qr_code_id(),
              "postal_qr_code_label_for_store"            => $this->fn->get_postal_qr_code_label_for_store(),
              "postal_qr_code_label_for_customer"         => $this->fn->get_postal_qr_code_label_for_customer(),
              "date_shamsi"                               => $this->fn->get_date_shamsi(),
              "disable_wc_dashboard"                      => $this->fn->get_disable_wc_dashboard(),
              "allow_preorder_invoice"                    => $this->fn->get_allow_preorder_invoice(),
              "allow_preorder_emptycart"                  => $this->fn->get_allow_preorder_emptycart(),
              "allow_pdf_customer"                        => $this->fn->get_allow_pdf_customer(),
              "allow_pdf_guest"                           => $this->fn->get_allow_pdf_guest(),
              "pdf_size"                                  => $this->fn->get_pdf_size(),
              "pdf_orientation"                           => $this->fn->get_pdf_orientation(),
              "attach_pdf_invoices_to_mail"               => $this->fn->get_attach_pdf_invoices_to_mail(),
              "custom_css_style"                          => $this->fn->get_custom_css_style(),
              "custom_pdf_css_style"                      => $this->fn->get_pdf_css_style(),
              "inventory_css_style"                       => $this->fn->get_inventory_css_style(),
              "template"                                  => $this->fn->get_template(),
              "theme_color"                               => $this->fn->get_theme_color(),
              "theme_color2"                              => $this->fn->get_theme_color2(),
              "theme_color3"                              => $this->fn->get_theme_color3(),
              "preinvoice_template"                       => $this->fn->get_preinvoice_template(),
              "preinvoice_theme_color"                    => $this->fn->get_preinvoice_theme_color(),
              "preinvoice_theme_color2"                   => $this->fn->get_preinvoice_theme_color2(),
              "preinvoice_theme_color3"                   => $this->fn->get_preinvoice_theme_color3(),
              "font_size"                                 => $this->fn->get_font_size() . "px",
              "font_sizes"                                => $this->fn->get_font_size() - 4 . "px",
              "font_sizem"                                => $this->fn->get_font_size() - 2 . "px",
              "invoice_prefix"                            => $this->fn->get_invoice_prefix(),
              "invoice_suffix"                            => $this->fn->get_invoice_suffix(),
              "invoice_start"                             => $this->fn->get_invoice_start(),
              "signature"                                 => $this->fn->get_signature(),
              "watermark"                                 => $this->fn->get_watermark(),
              "watermark_opacity"                         => $this->fn->get_watermark_opacity(),
              "watermark_opacity_10"                      => $this->fn->get_watermark_opacity()/100,
              "invoices_footer"                           => $this->fn->get_invoices_footer(),
              "show_custom_footer"                        => empty($this->fn->get_invoices_footer())?"no":"yes",
              "show_signature"                            => $this->fn->get_show_signatures(),
              "show_shelf_number_id"                      => $this->fn->get_show_shelf_number_id(),
              "show_product_sku_inventory"                => $this->fn->get_show_product_sku_inventory(),
              "show_product_sku2_inventory"               => $this->fn->get_show_product_sku2_inventory(),
              "show_product_image_inventory"              => $this->fn->get_show_product_image_inventory(),
              "show_product_weight_in_inventory"          => $this->fn->get_show_product_weight_in_inventory(),
              "show_product_total_weight_in_inventory"    => $this->fn->get_show_product_total_weight_in_inventory(),
              "show_product_dimensions_in_inventory"      => $this->fn->get_show_product_dimensions_in_inventory(),
              "show_product_quantity_in_inventory"        => $this->fn->get_show_product_quantity_in_inventory(),
              "show_product_note_in_inventory"            => $this->fn->get_show_product_note_in_inventory(),
              "price_inventory_report"                    => $this->fn->get_price_inventory_report(),
              "show_order_note_inventory"                 => $this->fn->get_show_order_note_inventory(),
              "show_store_national_id"                    => $this->fn->get_show_store_national_id(),
              "show_store_registration_number"            => $this->fn->get_show_store_registration_number(),
              "show_store_economical_number"              => $this->fn->get_show_store_economical_number(),
              "show_customer_address"                     => $this->fn->get_show_customer_address(),
              "show_customer_phone"                       => $this->fn->get_show_customer_phone(),
              "show_customer_email"                       => $this->fn->get_show_customer_email(),
              "show_order_date"                           => $this->fn->get_show_order_date(),
              "show_payment_method"                       => $this->fn->get_show_payment_method(),
              "show_shipping_method"                      => $this->fn->get_show_shipping_method(),
              "show_shipping_address"                     => $this->fn->get_show_shipping_address(),
              "address_display_method"                    => $this->fn->get_address_display_method(),
              "show_transaction_ref_id"                   => $this->fn->get_show_transaction_ref_id(),
              "show_paid_date"                            => $this->fn->get_show_paid_date(),
              "show_purchase_complete_date"               => $this->fn->get_show_purchase_complete_date(),
              "show_shipping_date"                        => $this->fn->get_show_shipping_date(),
              "show_order_status"                         => $this->fn->get_show_order_status(),
              "show_product_image"                        => $this->fn->get_show_product_image(),
              "show_product_purchase_note"                => $this->fn->get_show_product_purchase_note(),
              "show_order_items"                          => $this->fn->get_show_order_items(),
              "show_order_total"                          => $this->fn->get_show_order_total(),
              "show_order_note"                           => $this->fn->get_show_order_note(),
              "show_user_uin"                             => $this->fn->get_show_user_uin(),
              "show_shipping_ref_id"                      => $this->fn->get_show_shipping_ref_id(),
              "show_price_template"                       => $this->fn->get_show_price_template(),
              "show_product_weight"                       => $this->fn->get_show_product_weight(),
              "show_product_dimensions"                   => $this->fn->get_show_product_dimensions(),
              "show_discount_precent"                     => $this->fn->get_show_discount_precent(),
              "show_product_tax"                          => $this->fn->get_show_product_tax(),
              "show_product_sku"                          => $this->fn->get_show_product_sku(),
              "show_product_sku2"                         => $this->fn->get_show_product_sku2(),
              "trnslt__print"                             => __("Print", $this->td),
              "trnslt__seller"                            => __("Seller", $this->td),
              "trnslt__buyer"                             => __("Buyer", $this->td),
              "trnslt__dates"                             => __("Extras", $this->td),
              "show_shipping_ref_id_colspan"              => 1,
              "invoice_final_prices_pre_colspan"          => 7,
              "product_description_colspan"               => 4,
              "product_nettotal_colspan"                  => 1,
              "invoice_final_prices_colspan"              => 5,
              "invoice_final_row_colspan"                 => 14,
          );
          $use_billing = $opts["show_shipping_address"];

          $opts["invoice_id"]                 = apply_filters("puiw_printinvoice_getinvoice_id",                $opts["invoice_prefix"] . ($opts["invoice_start"]+$order->get_id()) . $opts["invoice_suffix"], $opts, $order);
          $opts["invoice_id_en"]              = apply_filters("puiw_printinvoice_getinvoice_id_en",             $opts["invoice_prefix"] . ($opts["invoice_start"]+$order->get_id()) . $opts["invoice_suffix"] ?: "0000000000000000", $opts, $order);
          $opts["invoice_id_nm"]              = apply_filters("puiw_printinvoice_getinvoice_id_raw",            $order->get_id(), $opts, $order);
          $opts["invoice_title"]              = apply_filters("puiw_printinvoice_getinvoice_title",             sprintf($this->fn->get_invoice_title(__("Invoice %s", $this->td)), $opts["invoice_id"]), $opts, $order);
          $opts["order_payment_method"]       = apply_filters("puiw_printinvoice_getinvoice_payment_method",    $order->get_payment_method_title(), $opts, $order);
          $opts["order_shipping_method"]      = apply_filters("puiw_printinvoice_getinvoice_shipping_method",   $order->get_shipping_method(), $opts, $order);
          $opts["customer_email"]             = apply_filters("puiw_printinvoice_getcustomer_email",            $order->get_billing_email(), $opts, $order);
          $opts["customer_phone"]             = apply_filters("puiw_printinvoice_getcustomer_phone",            $order->get_billing_phone(), $opts, $order);
          $opts["customer_fname"]             = apply_filters("puiw_printinvoice_getcustomer_firstname",        ($use_billing == "billing") ? $order->get_billing_first_name() :  $order->get_shipping_first_name(), $opts, $order, $use_billing);
          $opts["customer_lname"]             = apply_filters("puiw_printinvoice_getcustomer_lastname",         ($use_billing == "billing") ? $order->get_billing_last_name() :   $order->get_shipping_last_name(), $opts, $order, $use_billing);
          $opts["customer_fullname"]          = apply_filters("puiw_printinvoice_getcustomer_fullname",         ($use_billing == "billing") ? "{$order->get_billing_first_name()} {$order->get_billing_last_name()}" : "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}", $opts, $order, $use_billing);
          $opts["customer_company"]           = apply_filters("puiw_printinvoice_getcustomer_company",          ($use_billing == "billing") ? $order->get_billing_company() :     $order->get_shipping_company(), $opts, $order, $use_billing);
          $opts["customer_country"]           = apply_filters("puiw_printinvoice_getcustomer_country",          ($use_billing == "billing") ? ($order->get_billing_country() ?: "") : ($order->get_shipping_country() ?: ""), $opts, $order, $use_billing);
          $opts["customer_state"]             = apply_filters("puiw_printinvoice_getcustomer_state",            ($use_billing == "billing") ? ($order->get_billing_state() ?: "") : ($order->get_shipping_state() ?: ""), $opts, $order, $use_billing);
          $opts["customer_city"]              = apply_filters("puiw_printinvoice_getcustomer_city",             ($use_billing == "billing") ? ($order->get_billing_city() ?: "") : ($order->get_shipping_city() ?: ""), $opts, $order, $use_billing);
          $opts["customer_address_1"]         = apply_filters("puiw_printinvoice_getcustomer_address_1",        ($use_billing == "billing") ? $order->get_billing_address_1() :   $order->get_shipping_address_1(), $opts, $order, $use_billing);
          $opts["customer_address_2"]         = apply_filters("puiw_printinvoice_getcustomer_address_2",        ($use_billing == "billing") ? $order->get_billing_address_2() :   $order->get_shipping_address_2(), $opts, $order, $use_billing);
          $opts["customer_postcode"]          = apply_filters("puiw_printinvoice_getcustomer_postcode",         ($use_billing == "billing") ? $order->get_billing_postcode() :    $order->get_shipping_postcode(), $opts, $order, $use_billing);
          $opts["customer_signature"]         = apply_filters("puiw_printinvoice_getinvoicecustomer_signature", get_post_meta($order->get_id(), '_shipping_puiw_customer_signature', true), $opts, $order);
          $opts["order_transaction_ref_id"]   = apply_filters("puiw_printinvoice_getinvoice_shipping_method",   get_post_meta($order->get_id(), '_transaction_id', true), $opts, $order);
          $opts["customer_uin"]               = apply_filters("puiw_printinvoice_getcustomer_uin",              get_post_meta($order->get_id(), 'puiw_billing_uin', true), $opts, $order);
          $opts["invoice_qrcode"]             = apply_filters("puiw_printinvoice_getinvoice_qrdata",            add_query_arg( "invoice", $order->get_id(),home_url()), $opts, $order);
          $opts["invoice_qrcode"]             = wp_strip_all_tags( $opts["invoice_qrcode"], true );
          $opts["customer_address"]           = str_replace(
              apply_filters(
                  "puiw_printinvoice_address_template",
                  array(
                  "[first_name]",
                  "[last_name]",
                  "[company]",
                  "[country]",
                  "[province]",
                  "[city]",
                  "[address1]",
                  "[address2]",
                  "[po_box]",
                  "[email]",
                  "[phone]",
                  "[uin]"
                  ),  $opts, $order
              ),
              apply_filters(
                  "puiw_printinvoice_address_template_replace",
                  array(
                  $opts["customer_fname"],
                  $opts["customer_lname"],
                  $opts["customer_company"],
                  $opts["customer_country"],
                  (!empty($opts["customer_country"]) && !empty($opts["customer_state"])) ? WC()->countries->get_states($opts["customer_country"])[$opts["customer_state"]] : "",
                  $opts["customer_city"],
                  $opts["customer_address_1"],
                  $opts["customer_address_2"],
                  $opts["customer_postcode"],
                  $opts["customer_email"],
                  $opts["customer_phone"],
                  $opts["customer_uin"]
                  ),  $opts, $order
              ),
              $this->fn->get_address_display_method()
          );
          // $opts["customer_address"]        = ($use_billing == "billing") ? $order->get_formatted_billing_address() : $order->get_formatted_shipping_address();
          $opts["invoice_track_id"]           = get_post_meta($order->get_id(), 'puiw_invoice_track_id', true);
          $opts["invoice_track_id_en"]        = $opts["invoice_track_id"] ?: "0000000000000000";
          $opts["invoice_final_price"]        = $order->get_formatted_order_total();
          $opts["invoice_final_prices"]       = $this->get_order_final_prices($order);
          $opts["invoice_final_prices_pdf"]   = $this->get_order_final_prices_pdf($order);
          $opts["invoice_total_qty"]          = "";
          $opts["invoice_total_weight"]       = "";
          $opts["invoice_products_list"]      = "";
          $opts["home_url"]                   = home_url();

          if (empty(trim($opts["customer_signature"]))){
            $opts["customer_signature_css"] = "display: none !important; width:0; height:0;";
            $opts["customer_signature"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
          }
          if (empty(trim($opts["signature"]))){
            $opts["signature_css"] = "display: none !important; width:0; height:0;";
            $opts["signature"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
          }

          global $PeproUltimateInvoice;
          $generator = $PeproUltimateInvoice->barcode;

          $opts["invoice_barcode"]            = 'data:image/jpeg;base64,' . base64_encode($generator->getBarcode($opts["invoice_id_en"], $generator::TYPE_CODE_128));

          if (!empty($opts["invoice_track_id_en"])){
            $opts["invoice_track_barcode"]      = 'data:image/jpeg;base64,' . base64_encode($generator->getBarcode($opts["invoice_track_id_en"], $generator::TYPE_CODE_128));
          }else{
            $opts["invoice_track_barcode"]      = 'data:image/jpeg;';
          }

          if (!empty($opts["customer_postcode"])){
            $opts["customer_postcode_barcode"]      = 'data:image/jpeg;base64,' . base64_encode($generator->getBarcode($opts["customer_postcode"], $generator::TYPE_CODE_128));
          }else{
            $opts["customer_postcode_barcode"]      = 'data:image/jpeg;';
          }

          if (!empty($opts["store_postcode"])){
            $opts["store_postcode_barcode"]      = 'data:image/jpeg;base64,' . base64_encode($generator->getBarcode($opts["store_postcode"], $generator::TYPE_CODE_128));
          }else{
            $opts["store_postcode_barcode"]      = 'data:image/jpeg;';
          }

          return apply_filters( "puiw_get_default_dynamic_params", $opts, $order);
        }
        public function get_preserve_english_numbers($opt, $order)
        {
          return $keepOriginalENnumbers = apply_filters(
            "puiw_printinvoice_preserve_english_numbers",
            array(
              "invoice_id_en",
              "invoice_track_id_en",
              "invoice_products_list",
              "customer_postcode",
              "invoice_final_price",
              "store_postcode",
              "customer_postcode_barcode",
              "store_postcode_barcode",
              "invoice_id_nm",
              "product_description_colspan",
              "product_nettotal_colspan",
              "invoice_final_prices_pre_colspan",
              "invoice_final_prices_colspan",
              "invoice_final_row_colspan",
              "invoice_final_prices",
              "invoice_final_prices_pdf",
              "store_logo",
              "order_total",
              "invoice_qrcode",
              "invoice_barcode",
              "invoice_track_barcode",
              "store_website",
              "show_invoices_id_barcode_colspan",
              "custom_css_style",
              "custom_pdf_css_style",
              "inventory_css_style",
              "store_email",
              "base_price",
              "discount",
              "nettotal",
              "tax",
              "sku",
              "font_sizes",
              "font_sizem",
              "font_size",
              "signature",
              "customer_signature",
              "watermark",
              "watermark_opacity",
              "watermark_opacity_10",
              "theme_color",
              "theme_color2",
              "theme_color3",
              "show_shipping_ref_id_colspan",
              "extra_classes",
              "img",
            ),
            $opt,
            $order
          );
        }
        public function get_preserve_html_tags($opt, $order)
        {
          return apply_filters(
            "puiw_printinvoice_preserve_html_tags",
            array(
              "invoice_final_prices",
              "invoice_final_prices_pdf",
              "invoice_products_list",
              "invoices_footer",
              "base_price",
              "invoice_notes",
              "invoice_note_customer",
              "invoice_note_shopmngr",
              "order_total",
              "custom_css_style",
              "custom_pdf_css_style",
              "inventory_css_style",
            ),
            $opt,
            $order
          );
        }
        public function has_access($type="HTML", $order)
        {
          if ("yes" == $this->fn->get_allow_guest_users_view_invoices()){
            $access_guest = $this->fn->get_allow_pdf_guest();
            if ("PDF" == $type){ if ( "pdf" == $access_guest  || "both" == $access_guest ){ return true; } }
            else{ if ( "html" == $access_guest  || "both" == $access_guest ){ return true; } }
          }
          if (is_user_logged_in()){
            $user = wp_get_current_user();
            $user_id = get_current_user_id();
            $access_customer = $this->fn->get_allow_pdf_customer();
            if ( in_array( "administrator", (array) $user->roles )  || in_array( "shop_manager", (array) $user->roles ) ){
              return true;
            }
            if ( $user_id !== $order->get_user_id() ){ return false; /* prevent other users to see others' invoices */ }else{
              if ("yes" !== $this->fn->get_allow_users_have_invoices()){return false;}
              if ("PDF" == $type){
                if ( "pdf" == $access_customer  || "both" == $access_customer ){ return true; }
              }else{
                if ( "html" == $access_customer  || "both" == $access_customer ){ return true; }
              }
            }

          }
          return false;
        }
        public function create_html($order_id=0, $MODE="HTML",$part="",$email_printout="",$skipAuth=false)
        {
          if (!$order_id || empty(trim($order_id)) || !is_numeric(trim($order_id))) {return __('Incorrect data!', $this->td);}
          (int) $order_id = trim($order_id);
          $order = wc_get_order($order_id);
          if (!$order) {return __('Incorrect Order!', $this->td);}
          if (!$skipAuth){
            if ("HTML" == $MODE && !$this->has_access("HTML",$order)){ global $PeproUltimateInvoice; $PeproUltimateInvoice->die("printClass_create_html auth_check", __("Err 403 - Access Denied", $this->td), $PeproUltimateInvoice->Unauthorized_Access); }
            if ("PDF" == $MODE && !$this->has_access("PDF",$order)){ global $PeproUltimateInvoice; $PeproUltimateInvoice->die("printClass_create_html_pdf auth_check", __("Err 403 - Access Denied", $this->td), $PeproUltimateInvoice->Unauthorized_Access); }
          }
          ob_start();
          $opts = $this->get_default_dynamic_params($order_id,$order);
          if ("PDF" == $MODE) { $opts["invoice_final_prices"] = $this->get_order_final_prices_pdf($order); }
          $opt = apply_filters("puiw_printinvoice_create_html_options", $opts, $order);
          $opt["CURENT_DIR_URL"] = apply_filters( "puiw_get_template_dir_url", plugin_dir_url($opt["template"]) ,$opt["template"] ,$order);
          $keepOriginalHTMLtags = $this->get_preserve_html_tags($opt, $order);
          $keepOriginalENnumbers = $this->get_preserve_english_numbers($opt, $order);
          do_action("puiw_printinvoice_before_create_html", $opt, $opts, $order);
          $main_css_style_inline = "";
          $order_note_a = apply_filters( "puiw_printinvoice_order_note_customer", "<strong>".__("Note provided by Customer",$this->td)."</strong><br><div>" . $this->fn->get_order_note($order,"a")."</div>", $this->fn->get_order_note($order,"a"), $order, $opt);
          $order_note_b = apply_filters( "puiw_printinvoice_order_note_shopmngr", "<strong>".__("Note provided by Shop manager",$this->td)."</strong><br><div>" . $this->fn->get_order_note($order,"b")."</div>", $this->fn->get_order_note($order,"b"), $order, $opt);
          switch ($opt["show_order_note"]) {
            // hide_note_from_invoice, note_provided_by_customer, note_provided_by_shop_manager, note_provided_by_both
            case 'note_provided_by_customer':
              $notes = "<td>$order_note_a</td>";
              $opt["show_order_note"] = "yes";
              break;
            case 'note_provided_by_shop_manager':
              $notes = "<td>$order_note_b</td>";
              $opt["show_order_note"] = "yes";
              break;
            case 'note_provided_by_both':
              $notes = "<td>$order_note_b</td>
                        <td>$order_note_a</td>";
              $opt["show_order_note"] = "yes";
              break;
            default:
              $notes = "";
              $opt["show_order_note"] = "no";
              break;
          }
          foreach ($opt as $key => $value) {
            if (substr($key,0,5) == "show_" && $value !== "yes"){
              $main_css_style_inline .= "[if~='$key']{display:none !important; visibility: hidden !important;}" . PHP_EOL;
              $main_css_style_inline .= ".$key{width: 0cm; display:none !important; visibility: hidden !important;}" . PHP_EOL;
              if ("PDF" == $MODE){
                $opt["{$key}_hide_css"] = 'width:0 !important; margin: 0 !important; padding: 0 !important; font-size: 0 !important; display: none !important; visibility: hidden !important;';
                $opt["{$key}_display_none"] = 'display: none;';
                $opt["{$key}_hc"] = 'width:0 !important; margin: 0 !important; padding: 0 !important; font-size: 0 !important; display: none !important; visibility: hidden !important;';
                $opt["{$key}_dn"] = 'display: none;';
              }
            }

            if ((substr($key,0,5) == "show_") && ($value == "yes") && ("PDF" == $MODE)){
              $opt["{$key}_hide_css"] = '';
              $opt["{$key}_display_none"] = '';
              $opt["{$key}_hc"] = '';
              $opt["{$key}_dn"] = '';
            }
          }
          if (trim($opt["watermark"]) == ""){
            // $main_css_style_inline .= '[if~="watermark"]{display:none !important; visibility: hidden !important;}';
          }
          if ($opt["show_invoices_id_barcode"] !== "yes" || $email_printout){
            $opt["show_invoices_id_barcode_colspan"] = 2 ;
          }
          if ($opt["show_shipping_ref_id"] !== "yes" || $email_printout){
            $opt["show_shipping_ref_id_colspan"] = 2;
          }
          if ($opt["show_product_weight"] !== "yes"){
            if ("PDF" !== $MODE){$opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1;}
          }
          if ($opt["show_product_dimensions"] !== "yes"){
            if ("PDF" !== $MODE){
              $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
              $opt["invoice_final_prices_colspan"] = $opt["invoice_final_prices_colspan"] -1 ;
            }
          }
          if ($opt["show_product_sku"] !== "yes"){
            if ("PDF" !== $MODE){
              $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
              $opt["invoice_final_prices_pre_colspan"] = $opt["invoice_final_prices_pre_colspan"] -1 ;
            }
          }
          if ($opt["show_product_image"] !== "yes"){
            if ("PDF" !== $MODE){
              $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
              $opt["invoice_final_prices_pre_colspan"] = $opt["invoice_final_prices_pre_colspan"] -1 ;
            }
          }
          if ($opt["show_product_image"] !== "yes"){
            if ("PDF" !== $MODE){
              $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
              $opt["invoice_final_prices_pre_colspan"] = $opt["invoice_final_prices_pre_colspan"] -1 ;
            }
          }
          if ($opt["show_discount_precent"] !== "yes"){
            if ("PDF" !== $MODE){
              $opt["product_nettotal_colspan"] = $opt["product_nettotal_colspan"] + 1;
              // $opt["invoice_final_prices_colspan"] = $opt["invoice_final_prices_colspan"] + 1;
              // $opt["invoice_final_prices_pre_colspan"] = $opt["invoice_final_prices_pre_colspan"] +1 ;
            }
          }
          if ($opt["show_product_tax"] !== "yes"){
            if ("PDF" !== $MODE){
              $opt["product_nettotal_colspan"] = $opt["product_nettotal_colspan"] + 1;
              // $opt["invoice_final_prices_colspan"] = $opt["invoice_final_prices_colspan"] + 1;
              // $opt["invoice_final_prices_pre_colspan"] = $opt["invoice_final_prices_pre_colspan"] +1 ;
            }
          }
          $templateDirpath = apply_filters( "puiw_get_template_dir_path", $opt["template"], $order);
          $opt["invoice_note_customer"] = $order_note_a;
          $opt["invoice_note_shopmngr"] = $order_note_b;
          $opt["invoice_notes"] = $notes;
          if ("PDF_EXTRA_STYLE" == $MODE) { return $main_css_style_inline.$opts["custom_pdf_css_style"];}
          if ("PDF" == $MODE) {
              $template                     = file_get_contents("$templateDirpath/template.pdf.tpl");
              if(!empty($part)){ $template  = file_get_contents("$templateDirpath/template.pdf.{$part}.tpl"); }
              $product_row_RAW              = file_get_contents("$templateDirpath/template.pdf.row.tpl");
          }
          else{
              $extrainvoiceheaddata   = '';
              $main_css_style         = file_get_contents("$templateDirpath/style.css");
              if ($email_printout){   $main_css_style = file_get_contents("$templateDirpath/style.email.css"); }
              $main_css_style         = $main_css_style_inline . $main_css_style;
              $body_content           = '';
              if (!$email_printout)   {
                $body_content           = '<p style="text-align:center;">';
                if ($skipAuth){
                  $body_content .= '<a class="print-button" href="javascript:;" onclick="window.print();return false;" >'.__("PRINT",$this->td).'</a>';
                  $body_content .= '<a class="print-button" href="javascript:;" onclick="window.open(window.location.href.replace(\'?invoice=\',\'?invoice-pdf=\'))" >'.__("GET PDF",$this->td).'</a>';
                }else{
                  if ($this->has_access("HTML",$order)) { $body_content .= '<a class="print-button" href="javascript:;" onclick="window.print();return false;">'.__("PRINT",$this->td).'</a>'; }
                  if ($this->has_access("PDF",$order))  { $body_content .= '<a class="print-button" href="javascript:;" onclick="window.open(window.location.href.replace(\'?invoice=\',\'?invoice-pdf=\'))" >'.__("GET PDF",$this->td).'</a>'; }
                }
                $body_content           .= '</p>';
              }
              $body_content           .= file_get_contents("$templateDirpath/template.tpl");
              $invoicehtmltitle       = "{{{invoice_title}}} | {{{store_name}}}";
              $body_content           = apply_filters( "puiw_printinvoice_HTML_body", $body_content, $order_id);
              $extrainvoiceheaddata   = apply_filters( "puiw_printinvoice_HTML_extrahead", $extrainvoiceheaddata, $order_id);
              $invoicehtmltitle       = apply_filters( "puiw_printinvoice_HTML_title", $invoicehtmltitle, $order_id);
              $main_css_style         = apply_filters( "puiw_printinvoice_HTML_style", $main_css_style,$main_css_style_inline, $order_id);
              $product_row_RAW        = file_get_contents("$templateDirpath/template.row.tpl");
              $template               = "<!DOCTYPE html><html lang=\"fa\" dir=\"ltr\"><head><title>$invoicehtmltitle</title>$extrainvoiceheaddata<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style type='text/css'>{$main_css_style}{{{custom_css_style}}}</style></head>$body_content</html>";
          }

          $n=0;$total_weight = 0;
          foreach ( apply_filters( "puiw_order_items", $order->get_items(), $order) as $item_id => $item ) {
              $n+=1;
              $product_row  = ($product_row_RAW);
              $product_id   = $item->get_product_id();
              $variation_id = $item->get_variation_id();
              $somemeta     = $item->get_meta('_whatever', true);
              $product      = $item->get_product();
              $type         = $item->get_type();
              $active_price = $product->get_price(); // The product active raw price
              $sale_price   = $product->get_sale_price(); // The product raw sale price
              $regular_price= $product->get_regular_price(); // The product raw regular price
              $name         = apply_filters( "puiw_invoice_item_get_name",              $item->get_name(), $product, $item, $order);
              $extra_classes= apply_filters( "puiw_invoice_item_extra_classes",         [], $product, $item_id, $item, $order, $n);
              $quantity     = apply_filters( "puiw_invoice_item_get_quantity",          $item->get_quantity(), $product, $item, $order);
              $subtotal     = apply_filters( "puiw_invoice_item_get_subtotal",          wc_price($item->get_subtotal()), $product, $item, $order);
              $total        = apply_filters( "puiw_invoice_item_get_total",             wc_price($item->get_total()), $product, $item, $order);
              $tax          = apply_filters( "puiw_invoice_item_get_subtotal_tax",      wc_price($item->get_subtotal_tax()), $product, $item, $order);
              $taxclass     = apply_filters( "puiw_invoice_item_get_tax_class",         $item->get_tax_class(), $product, $item, $order);
              $taxstat      = apply_filters( "puiw_invoice_item_get_tax_status",        $item->get_tax_status(), $product, $item, $order);
              $allmeta      = apply_filters( "puiw_invoice_item_get_meta_data",         $item->get_meta_data(), $product, $item, $order);
              $description  = apply_filters( "puiw_invoice_item_get_purchase_note",     $product->get_purchase_note(), $product, $item, $order);
              $description .= (empty($description)?"":"$description<br>").$this->get_item_meta($item_id, $item, $product);
              $weight_raw   = apply_filters( "puiw_invoice_item_get_product_weight_raw",$product->get_weight() , $product, $item, $order);
              $weight       = apply_filters( "puiw_invoice_item_get_product_weight",    $this->fn->get_format_weight($weight_raw), $product, $item, $order);
              $dimension    = apply_filters( "puiw_invoice_item_get_product_dimension", $this->fn->get_product_dimension($item->get_product_id()), $product, $item, $order);
              $percentage   = "0.00%";
              if( $product->is_on_sale() && !$product->is_type('variable')){
                $percentage = number_format(round(100-((float)$active_price / (float)$regular_price * 100 ), 1 )) . "%";
              }
              $discount     = apply_filters( "puiw_invoice_item_get_product_discount", $percentage, $product, $item, $order);
              $sku          = $product->get_sku();
              if ($opt["show_product_sku2"] == "yes"){$sku = empty($sku) || !$sku ? "#$product_id" : $sku;}
              $product_weight = $weight_raw ?: 0;
              if ($product_weight) {
                $total_weight += floatval( $product_weight * $quantity );
                $opt["invoice_total_weight"] = apply_filters( "puiw_printinvoice_calculate_invoice_total_weight", $this->fn->get_format_weight($total_weight), $total_weight, $product_weight, $quantity, $product, $item, $order);
              }
              $opt["invoice_total_qty"] = (int) $opt["invoice_total_qty"] + $quantity;
              switch ($opt["show_price_template"]) {
                case 'show_only_regular_price':
                  $base_price = wc_price($product->get_regular_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'show_only_sale_price':
                  $base_price = wc_price($product->get_sale_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'show_both_regular_and_sale_price':
                  $base_price = wc_price($product->get_price(), array( 'currency' => $order->get_currency() ));
                  break;
                default:
			            $base_price = wc_price($order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );
                  break;
              }

              if ($this->is_bundled_child($item_id)){
                $base_price = "";
                $subtotal = "";
                if ($this->_woosb_show_bundled_hierarchy == "yes"){$n--;}
              }

              $optm = array(
                "n" => $n,
                "img" => ("PDF" == $MODE) ? $product->get_image(array( 50, 50 )) : $product->get_image('shop_thumbnail'),
                "sku" => $sku,
                "title" => $name,
                "qty" => $quantity,
                "base_price" => $base_price,
                "description" => $description,
                "extra_classes" => implode(" ", (array) $extra_classes),
                "discount" => $discount,
                "weight" => $weight,
                "product_description_colspan" => $opt["product_description_colspan"],
                "product_nettotal_colspan" => $opt["product_nettotal_colspan"],
                "total_weight" => apply_filters( "puiw_printinvoice_create_html_total_weight", $this->fn->get_format_weight(floatval($product_weight*$quantity)),
                floatval($product_weight*$quantity),$product_weight, $quantity, $item_id, $item, $product_id, $order ),
                "dimension" => $dimension,
                "tax" => $tax,
                "nettotal" => $subtotal,
                "shelf_number_id" => get_post_meta( $product_id, "_shelf_number_id", true),
              );
              $optm = apply_filters( "puiw_printinvoice_create_html_item_row_metas", $optm, $item_id, $item, $product_id, $order );

              foreach ($optm as $key => $value) {
                  if (!in_array($key, $keepOriginalENnumbers)) {
                    $value = $this->fn->parse_number($value);
                  }
                  $product_row = str_replace("{{{{$key}}}}", $value, $product_row);
              }
              $opt["invoice_products_list"] .= $product_row;
          }
          foreach ($opt as $key => $value) {
            if (!in_array($key, $keepOriginalENnumbers)) {
              $value = $this->fn->parse_number($value);
            }
            $value = apply_filters("puiw_printinvoice_create_html_options_{$key}_value", $value, $key, $order);
            if (!in_array($key, $keepOriginalHTMLtags)){
              $template = str_replace("{{{{$key}}}}", wp_strip_all_tags($value, 1), ($template));
            }
            else{
              $template = str_replace("{{{{$key}}}}", $value, $template);
            }
          }
          if(apply_filters("puiw_printinvoice_return_html_minfied", true, $template, $opt, $order)){
            $template = $this->minify_html($template);
          }

          do_action("puiw_printinvoice_after_create_html", $opt, $opts, $order);
          do_action("puiw_printinvoice_before_return_html", $opt, $opts, $order);
          echo apply_filters("puiw_printinvoice_return_html", $template, $opt, $opts, $order);
          do_action("puiw_printinvoice_after_return_html", $opt, $opts, $order);
          $tcona = ob_get_contents();
          ob_end_clean();
          return $tcona;
        }
        public function create_pdf($order_id=0, $force_download = false, $MODE="I")
        {
          if (!$order_id || empty(trim($order_id)) || !is_numeric(trim($order_id))){ return __('Incorrect data!', $this->td); }
          (int) $order_id = trim($order_id);
          $order = wc_get_order($order_id);
          if (!$order) {return false;}
          $skipAuth = true;
          if ("S" !== $MODE){
            $skipAuth = false;
            if (!$this->has_access("PDF",$order)){ global $PeproUltimateInvoice; $PeproUltimateInvoice->die("printClass_create_pdf auth_check", __("Err 403 - Access Denied", $this->td), $PeproUltimateInvoice->Unauthorized_Access); }
          }
          if (!$this->CheckPDFRequirements()){
            $this->CheckPDFRequirements(true);
          }
          global $PeproUltimateInvoice;
          // add_filter("woocommerce_price_format", function () {return '%2$s %1$s';}, 10000, 2); // you should change it from wc setting
          require_once PEPROULTIMATEINVOICE_DIR . '/include/vendor/autoload.php';
          $class = "pepro-one-page-purchase---invoice-simple";
          $class .= is_rtl() ? "rtl" : "";
          $dire = is_rtl() ? 'rtl' : 'ltr';
          $texe = is_rtl() ? 'right' : 'left';
          $order = wc_get_order($order_id);
          $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
          $fontDirs = $defaultConfig['fontDir'];
          $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
          $fontData = $defaultFontConfig['fontdata'];

          $template = $this->fn->get_template();
          $templateDirpath = apply_filters( "puiw_get_template_dir_path", $template, $order);

          $contents = file_get_contents("$templateDirpath/default.cfg");

          $template_pdf_setting = $this->parseTemplate($contents);
          /* if we had an error, don't let script stop !*/
          $get_allow_pdf_customer = $this->fn->get_allow_pdf_customer();
          $get_pdf_size = $this->fn->get_pdf_size();
          $get_pdf_orientation = $this->fn->get_pdf_orientation();

          $_fontData = $fontData + [
            'dejavu' => [
              'R' => 'DejaVuSans.ttf',
              'B' => 'DejaVuSans-Bold.ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
            'iranyekanen' => [
              'R' => 'IRANYekanRegular.ttf',
              'B' => 'IRANYekanBold.ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
            'iranyekanfa' => [
              'R' => 'IRANYekanRegular(FaNum).ttf',
              'B' => 'IRANYekanBold(FaNum).ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
            'iransans' => [
              'R' => 'IRANSans.ttf',
              'B' => 'IRANSans_Bold.ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
            'iransansfa' => [
              'R' => 'IRANSans(FaNum).ttf',
              'B' => 'IRANSans(FaNum)_Bold.ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
            'danaen' => [
              'R' => 'Dana-Regular.ttf',
              'B' => 'Dana-Bold.ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
            'danafa' => [
              'R' => 'Dana-FaNum-Regular.ttf',
              'B' => 'Dana-FaNum-Bold.ttf',
              'useOTL' => 0xFF,
              'useKashida' => 75,
            ],
          ];


          if ("L" == $get_pdf_orientation){$get_pdf_size = "$get_pdf_size-L";}
          $mpdf = new \Mpdf\Mpdf(array(
            'fontDir' => array_merge($fontDirs, [plugin_dir_path(__FILE__)]),
            'fontdata' => $_fontData,
            'default_font' => $this->fn->get_pdf_font(),
            // 'default_font' => "danafa",
            'format' => "$get_pdf_size", // A4-L
            'mode' => 'utf-8',
            'margin_right' => $template_pdf_setting["pdf_margin_right"],
            'margin_left' => $template_pdf_setting["pdf_margin_left"],
            'margin_top' => $template_pdf_setting["pdf_margin_top"],
            'margin_bottom' => $template_pdf_setting["pdf_margin_bottom"],
            'margin_header' => $template_pdf_setting["pdf_margin_header"],
            'margin_footer' => $template_pdf_setting["pdf_margin_footer"],
            'debug' => 0,
            'allow_output_buffering' => true,
            'showImageErrors' => 0,
            'mirrorMargins' => 0,
            'autoLangToFont' => true,
            'defaultPageNumStyle' => 'arabic-indic',
            ));
            $opts = apply_filters( "puiw_generate_pdf_name_orderid_format", array(
              "invoice_prefix"=> $this->fn->get_invoice_prefix(),
              "invoice_suffix"=> $this->fn->get_invoice_suffix(),
              "invoice_start"=> $this->fn->get_invoice_start(),
            ));
            $order_id_formatted = $opts["invoice_prefix"] . ($opts["invoice_start"]+$order_id) . $opts["invoice_suffix"] ?: "0000000000000000";
            $pdf_title = sprintf(_x("Invoice #%s on %s", "invoice-template", $PeproUltimateInvoice->td), $order_id_formatted, get_bloginfo('title'));
            $pdf_title = apply_filters( "puiw_generate_pdf_title_of_pdf",$pdf_title);
            $datenow = current_time('timestamp');
            $mpdf->SetDirectionality($dire);
            $mpdf->SetTitle($pdf_title);
            $mpdf->SetSubject($pdf_title);
            $mpdf->SetAuthor($PeproUltimateInvoice->title_d);
            $mpdf->SetCreator($PeproUltimateInvoice->title_tw);
            if (!$order) {
              $errrxt = _x('Error! Invoice does not exist.', 'invoice-template', $PeproUltimateInvoice->td);
              $err_html .= "<body dir='$dire'><h2 style='text-align:center;'>$errrxt</h2><p style='text-align:center;' dir='$dire'>" .
                sprintf(_x('Created by %s<br>( %s )', 'invoice-template', $PeproUltimateInvoice->td), $PeproUltimateInvoice->title_t,"<a href='https://pepro.dev/'>".$PeproUltimateInvoice->title_d.'</a>') .
                "</p><br><span><hr style='width: 80%;'><p style='text-align:center;' dir='$dire'>" .
                  sprintf(_x('%sBack to Dashboard%s', 'invoice-template', $PeproUltimateInvoice->td), "<a href='".get_permalink(get_option('woocommerce_myaccount_page_id'))."'>", '</a>'). " / " .
                  sprintf(_x('%sBack to Orders%s', 'invoice-template', $PeproUltimateInvoice->td), "<a href='".wc_get_endpoint_url('orders', '', get_permalink(get_option('woocommerce_myaccount_page_id')))."'>", '</a>').
                  "</span></p></body>";
                  $mpdf->WriteHTML($err_html);
            }
            else{
              $stylesheet = $this->get_pdf_style($order_id,$order);
              $PDF_EXTRA_STYLE = $this->create_html($order_id, "PDF_EXTRA_STYLE","","",$skipAuth);
              $stylesheet = $PDF_EXTRA_STYLE . $stylesheet;
              $html_invoice = $this->create_html($order_id, "PDF","","",$skipAuth);
              $html_header = $this->create_html($order_id, "PDF", "header","",$skipAuth);
              $html_footer = $this->create_html($order_id, "PDF", "footer","",$skipAuth);
              // echo "<pre dir='ltr' style='border:1px solid gray'>". print_r([$html_invoice, $PDF_EXTRA_STYLE],1) ."</pre>";exit;
              // $cssToInlineStyles = new CssToInlineStyles($html_invoice);
              // $cssToInlineStyles->setCleanup(true);
              // $cssToInlineStyles->setUseInlineStylesBlock(true);
              // $html_invoice = $cssToInlineStyles->convert();
              // echo "<style type='text/css'>$stylesheet</style>$html_header $html_invoice $html_footer";exit;
              $datec = date_i18n("Y-m-d H:i", $datenow);
              if ($this->fn->get_date_shamsi() == "yes") {
                $datec = pu_jdate("Y-m-d H:i", (int) $datenow, "", "local", "en");
              }
              $footerhtml0 = "<div class='footerauto' style='text-align:center;' dir='ltr'>";
              $footerhtml1 = "<strong>" . sprintf(_x('%s -- Page {PAGENO} / {nbpg}', 'invoice-template', $PeproUltimateInvoice->td), $pdf_title)."</strong><br>";
              $footerhtml2 = sprintf(_x('Created by %s at %s', 'invoice-template', $PeproUltimateInvoice->td),$PeproUltimateInvoice->title_t,$datec);
              $footerhtml3 = "</div>";
              $footerhtml = apply_filters( "puiw_printinvoice_pdf_footer", "{$footerhtml1}{$footerhtml2}", $footerhtml1, $footerhtml2, $order, $order_id);
              $footerhtml = "{$footerhtml0}{$footerhtml}{$footerhtml3}";
              $mpdf->SetHTMLHeader( $html_header );
              $mpdf->SetHTMLFooter( $html_footer.$footerhtml);
              $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
              $mpdf->WriteHTML($html_invoice, \Mpdf\HTMLParserMode::HTML_BODY);
            }

            $datetime = date_i18n("Y_m_d_H_i", $datenow);
            if ($this->fn->get_date_shamsi() == "yes") {
              $datetime = pu_jdate("Y-m-d_H-i-s", (int) $datenow, "", "local", "en");
            }
            $name = "Invoice-$order_id_formatted-$datetime";
            $name = apply_filters("puiw_get_export_pdf_name", $name , $order_id_formatted, $order_id, $datenow);
            if ("S" == $MODE){
              $rand = md5(time());
              $namedir = PEPROULTIMATEINVOICE_DIR . "/pdf_temp";
              $namedotext = "Invoice-$order_id_formatted.$datetime.pdf";
              $namedir = apply_filters( "puiw_get_default_mail_pdf_temp_path", $namedir);
              $namedotext = apply_filters( "puiw_get_default_mail_pdf_temp_name", $namedotext);
              wp_mkdir_p($namedir);
              $tmpname = "$namedir/$namedotext";
              $er = $mpdf->Output($tmpname, "F");
              return $namedotext;
            }
            // 'D': download the PDF file
            // 'I': serves in-line to the browser
            // 'S': returns the PDF document as a string
            // 'F': save as file $file_out
            $mpdf->Output($name . ($force_download ? ".pdf" : ""),($force_download ? "D" : "I"));
        }
        public function create_slips($order_id=0, $MODE="HTML")
        {
          if (!$order_id || empty(trim($order_id)) || !is_numeric(trim($order_id))) {return __('Incorrect data!', $this->td);}
          (int) $order_id = trim($order_id);
          $order = wc_get_order($order_id);
          if (!$order) {return __('Incorrect Order!', $this->td);}
          ob_start();
          $opts = $this->get_default_dynamic_params($order_id,$order);
          $opt = apply_filters("puiw_printslips_create_html_options", $opts, $order);

          $templateDirpath = apply_filters( "puiw_get_template_dir_path", $opt["template"], $order);
          $opt["CURENT_DIR_URL"] = apply_filters( "puiw_get_template_dir_url", plugin_dir_url($opt["template"]) ,$opt["template"] ,$order);

          $keepOriginalHTMLtags = $this->get_preserve_html_tags($opt, $order);
          $keepOriginalENnumbers = $this->get_preserve_english_numbers($opt, $order);
          do_action("puiw_printslips_before_create_html", $opt, $opts, $order);
          $extrainvoiceheaddata = '';
          $main_css_style = file_get_contents("$templateDirpath/style.slips.css");
          $body_content = '<p style="text-align:center;"><a class="print-button" href="javascript:;" onclick="window.print();return false;">'.__("PRINT",$this->td).'</a></p>';
          $body_content .= file_get_contents("$templateDirpath/template.slips.tpl");
          $invoicehtmltitle = "{{{invoice_title}}} | {{{store_name}}}";
          $extrainvoiceheaddata .= '<script src="'.PEPROULTIMATEINVOICE_URL.'/assets/js/qrcode.min.js"></script>';
          $order_note_a = apply_filters( "puiw_printslips_order_note_customer", "<strong>".__("Note provided by Customer",$this->td)."</strong><br>" . $this->fn->get_order_note($order,"a") , $this->fn->get_order_note($order,"a"), $order, $opt);
          $order_note_b = apply_filters( "puiw_printslips_order_note_shopmngr", "<strong>".__("Note provided by Shop manager",$this->td)."</strong><br>" . $this->fn->get_order_note($order,"b") , $this->fn->get_order_note($order,"b"), $order, $opt);

          switch ($opt["show_order_note"]) {
            case 'note_provided_by_customer':
              $notes = "<td>$order_note_a</td>";
              $opt["show_order_note"] = "yes";
              break;
            case 'note_provided_by_shop_manager':
              $notes = "<td>$order_note_b</td>";
              $opt["show_order_note"] = "yes";
              break;
            case 'note_provided_by_both':
              $notes = "<td>$order_note_b</td>
                        <td>$order_note_a</td>";
              $opt["show_order_note"] = "yes";
              break;
            default:
              $notes = "";
              $opt["show_order_note"] = "no";
              break;
          }
          foreach ($opt as $key => $value) {
            if (substr($key,0,5) == "show_" && $value !== "yes"){
              $main_css_style .= "[if~='$key']{display:none !important;}.$key{display:none !important;}";
            }
          }
          if (trim($opt["watermark"]) == ""){
            $main_css_style .= '[if~="watermark"]{display:none !important;}';
            $main_css_style .= '."watermark{display:none !important;}';
          }

          if ($opt["show_invoices_id_barcode"] !== "yes"){
            $opt["show_invoices_id_barcode_colspan"] = 2 ;
          }
          if ($opt["show_shipping_ref_id"] !== "yes"){
            $opt["show_shipping_ref_id_colspan"] = 2;
          }
          if ($opt["show_product_weight"] !== "yes"){
            $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
          }
          if ($opt["show_product_dimensions"] !== "yes"){
            $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
            $opt["invoice_final_prices_colspan"] = $opt["invoice_final_prices_colspan"] -1 ;
          }

          if ($opt["show_discount_precent"] !== "yes"){
            $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
            $opt["invoice_final_prices_colspan"] = $opt["invoice_final_prices_colspan"] -1 ;
          }
          if ($opt["show_product_tax"] !== "yes"){
            $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
            $opt["invoice_final_prices_colspan"] = $opt["invoice_final_prices_colspan"] -1 ;
          }

          // $opt["product_nettotal_colspan"] = $opt["product_nettotal_colspan"] + 1 ;

          if ($opt["show_product_sku"] !== "yes"){
            $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
          }
          if ($opt["show_product_image"] !== "yes"){
            $opt["invoice_final_row_colspan"] = $opt["invoice_final_row_colspan"] -1 ;
            $opt["invoice_final_prices_pre_colspan"] = $opt["invoice_final_prices_pre_colspan"] -1 ;
          }
          $opt["invoice_notes"] = $notes;

          $body_content = apply_filters( "puiw_printslips_HTML_body", $body_content);
          $extrainvoiceheaddata = apply_filters( "puiw_printslips_HTML_extrahead", $extrainvoiceheaddata);
          $invoicehtmltitle = apply_filters( "puiw_printslips_HTML_title", $invoicehtmltitle);
          $main_css_style = apply_filters( "puiw_printslips_HTML_style", $main_css_style);
          $template = "<!DOCTYPE html><html lang=\"fa\" dir=\"ltr\"><head><title>$invoicehtmltitle</title>$extrainvoiceheaddata<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style type='text/css'>{$main_css_style}{{{custom_css_style}}}</style></head>$body_content</html>";
          $product_row_RAW = file_get_contents("$templateDirpath/template.row.tpl");
          $n=0;$total_weight = 0;
          foreach ( apply_filters( "puiw_order_items", $order->get_items(), $order) as $item_id => $item ) {
            $n+=1;
            $product_row  = ($product_row_RAW);
            $product_id   = $item->get_product_id();
            $variation_id = $item->get_variation_id();
            $somemeta     = $item->get_meta('_whatever', true);
            $product      = $item->get_product();
            $type         = $item->get_type();
            $active_price = $product->get_price(); // The product active raw price
            $sale_price   = $product->get_sale_price(); // The product raw sale price
            $regular_price= $product->get_regular_price(); // The product raw regular price
            $name         = apply_filters( "puiw_invoice_item_get_name",              $item->get_name(), $product, $item, $order);
            $extra_classes= apply_filters( "puiw_invoice_item_extra_classes",         [], $product, $item_id, $item, $order, $n);
            $quantity     = apply_filters( "puiw_invoice_item_get_quantity",          $item->get_quantity(), $product, $item, $order);
            $subtotal     = apply_filters( "puiw_invoice_item_get_subtotal",          wc_price($item->get_subtotal()), $product, $item, $order);
            $total        = apply_filters( "puiw_invoice_item_get_total",             wc_price($item->get_total()), $product, $item, $order);
            $tax          = apply_filters( "puiw_invoice_item_get_subtotal_tax",      wc_price($item->get_subtotal_tax()), $product, $item, $order);
            $taxclass     = apply_filters( "puiw_invoice_item_get_tax_class",         $item->get_tax_class(), $product, $item, $order);
            $taxstat      = apply_filters( "puiw_invoice_item_get_tax_status",        $item->get_tax_status(), $product, $item, $order);
            $allmeta      = apply_filters( "puiw_invoice_item_get_meta_data",         $item->get_meta_data(), $product, $item, $order);
            $description  = apply_filters( "puiw_invoice_item_get_purchase_note",     $product->get_purchase_note(), $product, $item, $order);
            $description .= (empty($description)?"":"$description<br>").$this->get_item_meta($item_id, $item, $product);
            $weight_raw   = apply_filters( "puiw_invoice_item_get_product_weight_raw",$product->get_weight() , $product, $item, $order);
            $weight       = apply_filters( "puiw_invoice_item_get_product_weight",    $this->fn->get_format_weight($weight_raw), $product, $item, $order);
            $dimension    = apply_filters( "puiw_invoice_item_get_product_dimension", $this->fn->get_product_dimension($item->get_product_id()), $product, $item, $order);

            $percentage   = "0.00%";
            if( $product->is_on_sale() && !$product->is_type('variable')){$percentage   = number_format(round(100-((float)$active_price / (float)$regular_price * 100 ), 1 )) . "%";}
            $discount     = apply_filters( "puiw_invoice_item_get_product_discount", $percentage, $product, $item, $order);
            $sku          = $product->get_sku();
            if ($opt["show_product_sku2"] == "yes"){$sku = empty($sku) || !$sku ? "#$product_id" : $sku;}
            $product_weight = $weight_raw ?: 0;
              if ($product_weight) {
                $total_weight += floatval( $product_weight * $quantity );
                $opt["invoice_total_weight"] = apply_filters( "puiw_printslips_calculate_invoice_total_weight", $this->fn->get_format_weight($total_weight), $total_weight, $product_weight, $quantity, $product, $item, $order);
              }
              $opt["invoice_total_qty"] = (int) $opt["invoice_total_qty"] + $quantity;
              switch ($opt["show_price_template"]) {
                //show_only_regular_price, show_only_sale_price, show_both_regular_and_sale_price
                case 'show_only_regular_price':
                  $base_price = wc_price($product->get_regular_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'show_only_sale_price':
                  $base_price = wc_price($product->get_sale_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'show_both_regular_and_sale_price':
                  $base_price = wc_price($product->get_price(), array( 'currency' => $order->get_currency() ));
                  break;
                default:
			            $base_price = wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );
                  break;
              }

              if ($this->is_bundled_child($item_id)){
                $base_price = "";
                $subtotal = "";
                if ($this->_woosb_show_bundled_hierarchy == "yes"){$n--;}
              }

              $optm = array(
                "n" => $n,
                "img" => ("PDF" == $MODE) ? $product->get_image(array( 50, 50 )) : $product->get_image('shop_thumbnail'),
                "sku" => $sku,
                "title" => $name,
                "qty" => $quantity,
                "base_price" => $base_price,
                "description" => $description,
                "extra_classes" => implode(" ", (array) $extra_classes),
                "discount" => $discount,
                "weight" => $weight,
                "total_weight" => apply_filters( "puiw_printslips_create_html_total_weight", $this->fn->get_format_weight(floatval($product_weight*$quantity)),
                floatval($product_weight*$quantity),$product_weight, $quantity, $item_id, $item, $product_id, $order ),
                "dimension" => $dimension,
                "tax" => $tax,
                "nettotal" => $subtotal,
                "shelf_number_id" => get_post_meta( $product_id, "_shelf_number_id", true),
              );
              $optm = apply_filters( "puiw_printslips_create_html_item_row_metas", $optm, $item_id, $item, $product_id, $order );

              foreach ($optm as $key => $value) {
                  if (!in_array($key, $keepOriginalENnumbers)) {
                    $value = $this->fn->parse_number($value);
                  }
                  $product_row = str_replace("{{{{$key}}}}", $value, $product_row);
              }
              $opt["invoice_products_list"] .= $product_row;
          }
          foreach ($opt as $key => $value) {
            if (!in_array($key, $keepOriginalENnumbers)) {
              $value = $this->fn->parse_number($value);
            }
            $value = apply_filters("puiw_printslips_create_html_options_{$key}_value", $value, $key, $order);
            if (!in_array($key, $keepOriginalHTMLtags)){
              $template = str_replace("{{{{$key}}}}", wp_strip_all_tags($value, 1), ($template));
            }
            else{
              $template = str_replace("{{{{$key}}}}", $value, $template);
            }
          }
          if(apply_filters("puiw_printslips_return_html_minfied", true, $template, $opt, $order)){
            $template = $this->minify_html($template);
          }
          do_action("puiw_printslips_after_create_html", $opt, $opts, $order);
          do_action("puiw_printslips_before_return_html", $opt, $opts, $order);
          echo apply_filters("puiw_printslips_return_html", $template, $opt, $opts, $order);
          do_action("puiw_printslips_after_return_html", $opt, $opts, $order);
          $tcona = ob_get_contents();
          ob_end_clean();
          return $tcona;
        }
        public function create_inventory($order_id=0, $MODE="HTML")
        {
          if (!$order_id || empty(trim($order_id)) || !is_numeric(trim($order_id))) {return __('Incorrect data!', $this->td);}
          (int) $order_id = trim($order_id);
          $order = wc_get_order($order_id);
          if (!$order) {return __('Incorrect Order!', $this->td);}
          ob_start();
          $opts = $this->get_default_dynamic_params($order_id,$order);
          $opt = apply_filters("puiw_printinventory_create_html_options", $opts, $order);

          $templateDirpath = apply_filters( "puiw_get_template_dir_path", $opt["template"], $order);
          $opt["CURENT_DIR_URL"] = apply_filters( "puiw_get_template_dir_url", plugin_dir_url($opt["template"]) ,$opt["template"] ,$order);

          $keepOriginalHTMLtags = $this->get_preserve_html_tags($opt, $order);
          $keepOriginalENnumbers = $this->get_preserve_english_numbers($opt, $order);
          do_action("puiw_printinventory_before_create_html", $opt, $opts, $order);
          $extrainvoiceheaddata = '';
          $main_css_style = file_get_contents("$templateDirpath/style.inventory.css");
          $body_content = '<p style="text-align:center;"><a class="print-button" href="javascript:;" onclick="window.print();return false;">'.__("PRINT",$this->td).'</a></p>';
          $body_content .= file_get_contents("$templateDirpath/template.inventory.tpl");
          $invoicehtmltitle = "{{{invoice_title}}} | {{{store_name}}}";
          $extrainvoiceheaddata .= '<script src="'.PEPROULTIMATEINVOICE_URL.'/assets/js/qrcode.min.js"></script>';
          $order_note_a = apply_filters( "puiw_printinventory_order_note_customer", "<strong>".__("Note provided by Customer",$this->td)."</strong><br>" . $this->fn->get_order_note($order,"a") , $this->fn->get_order_note($order,"a"), $order, $opt);
          $order_note_b = apply_filters( "puiw_printinventory_order_note_shopmngr", "<strong>".__("Note provided by Shop manager",$this->td)."</strong><br>" . $this->fn->get_order_note($order,"b") , $this->fn->get_order_note($order,"b"), $order, $opt);
          $opt["show_inventory_price"] = "yes";
          if ($opt["price_inventory_report"] == "hide_all_price") { $opt["show_inventory_price"] = "no"; }
          switch ($opt["show_order_note_inventory"]) {
            case 'note_provided_by_customer':
              $notes = "<td>$order_note_a</td>";
              $opt["show_inventory_note"] = "yes";
              break;
            case 'note_provided_by_shop_manager':
              $notes = "<td>$order_note_b</td>";
              $opt["show_inventory_note"] = "yes";
              break;
            case 'note_provided_by_both':
              $notes = "<td>$order_note_b</td><td>$order_note_a</td>";
              $opt["show_inventory_note"] = "yes";
              break;
            default:
              $notes = "";
              $opt["show_inventory_note"] = "no";
              break;
          }
          foreach ($opt as $key => $value) { if (substr($key,0,5) == "show_" && $value !== "yes"){ $main_css_style .= "[if~='$key']{display:none !important;}.$key{display:none !important;}"; } }
          $opt["invoice_final_prices_pre_colspan"] = $nmpo = 8;
          if (trim($opt["watermark"]) == ""){ $main_css_style .= '[if~="watermark"]{display:none !important;}.watermark{display:none !important;}'; }
          if ($opt["show_product_image_inventory"] !== "yes"){ $nmpo-=1; }
          if ($opt["show_product_sku_inventory"] !== "yes"){ $nmpo-=1; }
          if ($opt["show_shelf_number_id"] !== "yes"){ $nmpo-=1; }
          if ($opt["show_inventory_price"] !== "yes"){ $nmpo-=1; }
          if ($opt["show_product_weight_in_inventory"] !== "yes"){ $nmpo-=1; }
          $opt["invoice_final_prices_pre_colspan"] = $nmpo;
          $opt["invoice_notes"] = $notes;
          $body_content = apply_filters( "puiw_printinventory_HTML_body", $body_content);
          $extrainvoiceheaddata = apply_filters( "puiw_printinventory_HTML_extrahead", $extrainvoiceheaddata);
          $invoicehtmltitle = apply_filters( "puiw_printinventory_HTML_title", $invoicehtmltitle);
          $main_css_style = apply_filters( "puiw_printinventory_HTML_style", $main_css_style);
          $template = "<!DOCTYPE html><html lang=\"fa\" dir=\"ltr\"><head><title>$invoicehtmltitle</title>$extrainvoiceheaddata<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style type='text/css'>{$main_css_style}{{{inventory_css_style}}}</style></head>$body_content</html>";
          $product_row_RAW = file_get_contents("$templateDirpath/template.row.inventory.tpl");
          $n=0;$total_weight = 0;
          foreach ( apply_filters( "puiw_order_items", $order->get_items(), $order) as $item_id => $item ) {
              $n+=1;
              $product_row  = ($product_row_RAW);
              $product_id   = $item->get_product_id();
              $variation_id = $item->get_variation_id();
              $somemeta     = $item->get_meta('_whatever', true);
              $product      = $item->get_product();
              $type         = $item->get_type();
              $active_price = $product->get_price(); // The product active raw price
              $sale_price   = $product->get_sale_price(); // The product raw sale price
              $regular_price= $product->get_regular_price(); // The product raw regular price
              $name         = apply_filters( "puiw_invoice_item_get_name",              $item->get_name(), $product, $item, $order);
              $extra_classes= apply_filters( "puiw_invoice_item_extra_classes",         [], $product, $item_id, $item, $order, $n);
              $quantity     = apply_filters( "puiw_invoice_item_get_quantity",          $item->get_quantity(), $product, $item, $order);
              $subtotal     = apply_filters( "puiw_invoice_item_get_subtotal",          wc_price($item->get_subtotal()), $product, $item, $order);
              $total        = apply_filters( "puiw_invoice_item_get_total",             wc_price($item->get_total()), $product, $item, $order);
              $tax          = apply_filters( "puiw_invoice_item_get_subtotal_tax",      wc_price($item->get_subtotal_tax()), $product, $item, $order);
              $taxclass     = apply_filters( "puiw_invoice_item_get_tax_class",         $item->get_tax_class(), $product, $item, $order);
              $taxstat      = apply_filters( "puiw_invoice_item_get_tax_status",        $item->get_tax_status(), $product, $item, $order);
              $allmeta      = apply_filters( "puiw_invoice_item_get_meta_data",         $item->get_meta_data(), $product, $item, $order);
              $description  = apply_filters( "puiw_invoice_item_get_purchase_note",     $product->get_purchase_note(), $product, $item, $order);
              $description .= (empty($description)?"":"$description<br>").$this->get_item_meta($item_id, $item, $product);
              $weight_raw   = apply_filters( "puiw_invoice_item_get_product_weight_raw",$product->get_weight() , $product, $item, $order);
              $weight       = apply_filters( "puiw_invoice_item_get_product_weight",    $this->fn->get_format_weight($weight_raw), $product, $item, $order);
              $dimension    = apply_filters( "puiw_invoice_item_get_product_dimension", $this->fn->get_product_dimension($item->get_product_id()), $product, $item, $order);


              $percentage   = "0.00%";
              if( $product->is_on_sale() && !$product->is_type('variable')){$percentage   = number_format(round(100-((float)$active_price / (float)$regular_price * 100 ), 1 )) . "%";}
              $discount     = apply_filters( "puiw_invoice_item_get_product_discount", $percentage, $product, $item, $order);
              $sku          = $product->get_sku();
              if ($opt["show_product_sku2_inventory"] == "yes"){
               $sku = empty($sku) || !$sku ? "#$product_id" : $sku;
              }
              $product_weight = $weight_raw ?: 0;
              if ($product_weight) {
                $total_weight += floatval( $product_weight * $quantity );
                $opt["invoice_total_weight"] = apply_filters( "puiw_printinventory_calculate_invoice_total_weight", $this->fn->get_format_weight($total_weight), $total_weight, $product_weight, $quantity, $product, $item, $order);
              }
              $opt["invoice_total_qty"] = (int) $opt["invoice_total_qty"] + $quantity;
              switch ($opt["price_inventory_report"]) {
                //show_only_regular_price, show_only_sale_price, show_both_regular_and_sale_price
                case 'show_only_regular_price':
                  $base_price = wc_price($product->get_regular_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'show_only_sale_price':
                  $base_price = wc_price($product->get_sale_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'show_both_regular_and_sale_price':
                  // $opt["show_product_image"] = "yes";
                  $base_price = wc_price($product->get_price(), array( 'currency' => $order->get_currency() ));
                  break;
                case 'hide_all_price':
                  $base_price = "";
                  break;
                default:
                  // $base_price = wc_price($item->get_total() / $item->get_quantity());
		              $base_price = wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );
                  break;
              }

              if ($this->is_bundled_child($item_id)){
                $base_price = "";
                $subtotal = "";
                if ($this->_woosb_show_bundled_hierarchy == "yes"){$n--;}
              }

              $optm = array(
                "n" => $n,
                "img" => ("PDF" == $MODE) ? $product->get_image(array( 50, 50 )) : $product->get_image('shop_thumbnail'),
                "sku" => $sku,
                "title" => $name,
                "qty" => $quantity,
                "base_price" => $base_price,
                "description" => $description,
                "extra_classes" => implode(" ", (array) $extra_classes),
                "discount" => $discount,
                "weight" => $weight,
                "total_weight" => apply_filters( "puiw_printinventory_create_html_total_weight", $this->fn->get_format_weight(floatval($product_weight*$quantity)),
                floatval($product_weight*$quantity),$product_weight, $quantity, $item_id, $item, $product_id, $order ),
                "dimension" => $dimension,
                "tax" => $tax,
                "nettotal" => $subtotal,
                "shelf_number_id" => get_post_meta( $product_id, "_shelf_number_id", true),
              );
              $optm = apply_filters( "puiw_printinventory_create_html_item_row_metas", $optm, $item_id, $item, $product_id, $order );

              foreach ($optm as $key => $value) {
                  if (!in_array($key, $keepOriginalENnumbers)) {
                    $value = $this->fn->parse_number($value);
                  }
                  $product_row = str_replace("{{{{$key}}}}", $value, $product_row);
              }
              $opt["invoice_products_list"] .= $product_row;
          }
          foreach ($opt as $key => $value) {
            if (!in_array($key, $keepOriginalENnumbers)) {
              $value = $this->fn->parse_number($value);
            }
            $value = apply_filters("puiw_printinventory_create_html_options_{$key}_value", $value, $key, $order);
            if (!in_array($key, $keepOriginalHTMLtags)){
              $template = str_replace("{{{{$key}}}}", wp_strip_all_tags($value, 1), ($template));
            }
            else{
              $template = str_replace("{{{{$key}}}}", $value, $template);
            }
          }
          if(apply_filters("puiw_printinventory_return_html_minfied", true, $template, $opt, $order)){
            $template = $this->minify_html($template);
          }
          do_action("puiw_printinventory_after_create_html", $opt, $opts, $order);
          do_action("puiw_printinventory_before_return_html", $opt, $opts, $order);
          echo apply_filters("puiw_printinventory_return_html", $template, $opt, $opts, $order);
          do_action("puiw_printinventory_after_return_html", $opt, $opts, $order);
          $tcona = ob_get_contents();
          ob_end_clean();
          return $tcona;
        }
        protected function CheckPDFRequirements($md = false)
        {
          $ok = true;
          foreach ( array(
              "mbstring" => "extension",
              "mb_regex_encoding" => "function",
              "gd" => "extension",
              "zlib" => "extension",
              "bcmath" => "extension",
              "xml" => "extension",
              "curl" => "extension",
            ) as $key => $value) {
              if ($md){
                echo
                  ("extension" == $value)
                  ?
                  (extension_loaded($key) ? "<p>extension <strong>$key</strong>: OK</p>" : "<p>extension <strong>$key</strong>: DOES NOT EXISTS</p>" )
                  :
                  (function_exists($key) ? "<p>function <strong>$key</strong>: OK</p>" : "<p>function <strong>$key</strong>: DOES NOT EXISTS</p>")
                ;
              }else{
                if (!$ok){ return $ok;}
                if ("extension" == $value){
                  if (!extension_loaded($key)){ $ok = false; }
                }else{
                  if (!function_exists($key)){ $ok = false; }
                }
              }

          }
          return $ok;
        }
        protected function puiw__download_file($url)
        {
            // Initialize the cURL session
            $ch = curl_init($url);
            // Use basename() function to return
            // the base name of file
            $file_name = basename($url);
            // Save file into file location
            $save_file_loc = plugin_dir_path(__FILE__) . "logo.png";
            // Open file
            $fp = fopen($save_file_loc, 'wb');
            // It set an option for a cURL transfer
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            // Perform a cURL session
            curl_exec($ch);
            // Closes a cURL session and frees all resources
            curl_close($ch);
            // Close file
            fclose($fp);
            return $save_file_loc;
        }
        protected function get_pdf_style($order_id=0,$order)
        {
          $template = $this->fn->get_template();
          $templateDirpath = apply_filters( "puiw_get_template_dir_path", $template, $order);
          $template = file_get_contents("$templateDirpath/style.pdf.css");
          $opts = $this->get_default_dynamic_params($order_id,$order);
          $opts["invoice_final_prices"] = $this->get_order_final_prices_pdf($order);
          $opt = apply_filters("puiw_printinvoice_create_html_options", $opts, $order);
          $opt["CURENT_DIR_URL"] = apply_filters( "puiw_get_template_dir_url", plugin_dir_url($template) ,$template ,$order);

          $keepOriginalHTMLtags = $this->get_preserve_html_tags($opt, $order);
          $keepOriginalENnumbers = $this->get_preserve_english_numbers($opt, $order);
          foreach ($opt as $key => $value) {
            if (!in_array($key, $keepOriginalENnumbers)) {
              $value = $this->fn->parse_number($value);
            }
            $value = apply_filters("puiw_printinvoice_create_html_options_{$key}_value", $value, $key, $order);
            if (!in_array($key, $keepOriginalHTMLtags)){
              $template = str_replace("{{{{$key}}}}", wp_strip_all_tags($value, 1), ($template));
            }
            else{
              $template = str_replace("{{{{$key}}}}", $value, $template);
            }
          }
          return $template;
        }
        protected function minify_html($input)
        {
            if(trim($input) === "") { return $input;
            }
            // Remove extra white-space(s) between HTML attribute(s)
            $input = preg_replace_callback(
                '#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
                    return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
                }, str_replace("\r", "", $input)
            );
              // Minify inline CSS declaration(s)
            if(strpos($input, ' style=') !== false) {
                $input = preg_replace_callback(
                    '#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                        return '<' . $matches[1] . ' style=' . $matches[2] . $this->minify_css($matches[3]) . $matches[2];
                    }, $input
                );
            }
            if(strpos($input, '</style>') !== false) {
                $input = preg_replace_callback(
                    '#<style(.*?)>(.*?)</style>#is', function ($matches) {
                        return '<style' . $matches[1] .'>'. $this->minify_css($matches[2]) . '</style>';
                    }, $input
                );
            }
            if(strpos($input, '</script>') !== false) {
                $input = preg_replace_callback(
                    '#<script(.*?)>(.*?)</script>#is', function ($matches) {
                        return '<script' . $matches[1] .'>'. $this->minify_js($matches[2]) . '</script>';
                    }, $input
                );
            }

            return preg_replace(
                array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
                ),
                array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
                ),
                $input
            );
        }
        protected function minify_css($input)
        {
            if(trim($input) === "") { return $input;
            }
            return preg_replace(
                array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
                ),
                array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
                ),
                $input
            );
        }
        protected function minify_js($input)
        {
            if(trim($input) === "") { return $input;
            }
            return preg_replace(
                array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
                ),
                array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
                ),
                $input
            );
        }



        /**
         * hook to alter css styles
         *
         * @method  custom_css_per_invoice
         * @param   string $custom_css_style
         * @param   string $default
         * @return  string
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function custom_css_per_invoice($custom_css_style, $default)
        {
          if ($this->_woosb_show_bundled_hierarchy == "yes"){
            $custom_css_style .= "tr.even {background: var(--theme_color) !important;}";
            $custom_css_style .= "tr.odd {background: white !important;}";
            $custom_css_style .= ".woosb-order-item.woosb-item-child .show_product_image *,.woosb-order-item.woosb-item-child .show_product_n * {display: none;}";
          }
          return $custom_css_style;
        }
        /**
         * Get Order Final Prices
         *
         * @method  get_order_final_prices
         * @param   WC_Order $order
         * @return  string
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function get_order_final_prices($order)
        {
          ob_start();
          echo '<table style="width: 100%;float: left;">';
          foreach ( $order->get_order_item_totals() as $key => $total ) {
            ?>
            <tr class="puiw_totalrow <?php echo $key;?>">
              <th style="border: none;"scope="row"><?php echo esc_html( $total['label'] ); ?></th>
              <td style="border: none;"><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
            </tr>
            <?php
          }
          echo "</table>";
          $tr = ob_get_contents();
          $tr = apply_filters( "puiw_printinvoice_get_order_final_prices", $tr, $order);
          ob_end_clean();
          return $tr;
        }
        /**
         * Get Order Final Prices Pdf
         *
         * @method  get_order_final_prices_pdf
         * @param   WC_Order $order
         * @return  string
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function get_order_final_prices_pdf($order)
        {
          ob_start();
          foreach ( $order->get_order_item_totals() as $key => $total ) {
            echo "<p dir='rtl'>" . esc_html($total['label']) . " " . (( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] )) . "</p><br>";
          }
          $tr = ob_get_contents();
          $tr = apply_filters( "puiw_printinvoice_get_order_final_prices_pdf", $tr, $order);
          ob_end_clean();
          return apply_filters("puiw_return_pdf_total_prices_as_single_price", false , $order) ? $order->get_order_item_totals()["order_total"]["value"] : $tr;
        }
        /**
         * show item meta supporting WPC Product Bundles and wc hooks
         *
         * @method  get_item_meta
         * @param   int $item_id
         * @param   Object $item
         * @param   WC_Product $product
         * @return  string
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        private function get_item_meta($item_id, $item, $product)
        {
          ob_start();
          $ob_get_contents = ""; $echo = ""; $found_any = false;
          if (!$item || empty($item)) return '';
          $hidden_order_itemmeta = apply_filters( 'woocommerce_hidden_order_itemmeta', array( '_qty', '_tax_class', '_product_id', '_variation_id', '_line_subtotal', '_line_subtotal_tax', '_line_total', '_line_tax', 'method_id', 'cost', '_reduced_stock', ) );

          do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $product );

          if ( $meta_data = $item->get_formatted_meta_data('') ) {
            $echo .= "<div class='view order_item_meta'><table cellspacing='0' class='display_meta'>";
            foreach ( $meta_data as $meta_id => $meta ){
              if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) { continue; }
              if ($this->_woosb_show_bundles_subtitle == "no" && "bundled-products" === sanitize_title($meta->key,"")){ continue; }
              if ($this->_woosb_show_bundles_subtitle == "yes" && "bundled-products" === sanitize_title($meta->key,"")){ $meta->display_key = $this->_woosb_show_bundles_prefix; }
              $found_any = true;
              $echo .= "<tr style='background: unset;' class='". sanitize_title("$meta->key","") ."'>
                      <th style='border: none;'><strong>" . wp_kses_post( $meta->display_key ) . "</strong>:</th>
                      <td style='border: none;'>" . wp_kses_post( force_balance_tags( $meta->display_value ) ) . "</td>
              </tr>";
            }
            if ($found_any){
              echo "$echo</table></div>";
            }
            $ob_get_contents = ob_get_contents();
            ob_end_clean();
          }

          do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $product );

          return $ob_get_contents;
        }
        /**
         * WPC Product Bundles Hide Bundles Parent
         *
         * @method  woosb_puiw_hide_bundles_parent
         * @param   array $data_list
         * @param   WC_Order $order
         * @return  array
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woosb_puiw_hide_bundles_parent( $data_list, $order )
        {
          foreach ( $data_list as $key => $data ) {
            $bundles = wc_get_order_item_meta( $key, '_woosb_ids', true );
            if ( ! empty( $bundles ) ) {
              unset( $data_list[ $key ] );
            }
          }
          return $data_list;
        }
        /**
         * WPC Product Bundles Hide Bundled Childs
         *
         * @method  woosb_puiw_hide_bundled_childs
         * @param   array $data_list
         * @param   WC_Order $order
         * @return  array
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woosb_puiw_hide_bundled_childs( $data_list, $order )
        {
          foreach ( $data_list as $key => $data ) {
            $bundled = wc_get_order_item_meta( $key, '_woosb_parent_id', true );
            if ( ! empty( $bundled ) ) {
              unset( $data_list[ $key ] );
            }
          }
          return $data_list;
        }
        /**
         * WPC Product Bundles isBundledParent
         *
         * @method  is_bundled_parent
         * @param   string $key
         * @return  boolean
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function is_bundled_parent($key)
        {
          return wc_get_order_item_meta( $key, '_woosb_ids', true );
        }
        /**
         * WPC Product Bundles isBundledChild
         *
         * @method  is_bundled_child
         * @param   string $key
         * @return  boolean
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function is_bundled_child($key)
        {
          return wc_get_order_item_meta( $key, '_woosb_parent_id', true );
        }
        /**
         * invoice items tr html classes
         *
         * @method  puiw__item_extra_classes
         * @param   array $classes
         * @param   WC_Product $product
         * @param   int $item_id
         * @param   Object $item
         * @param   WC_Order $order
         * @param   int $nthitem
         * @return  array classes
         * @version 1.0.0
         * @since   1.0.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function puiw__item_extra_classes($classes, $product, $item_id, $item, $order, $nthitem)
        {
          if ($nthitem % 2) {
            $odd_even = "odd";
          } else {
            $odd_even = "even";
          }

          global $puiw_last_woosb_bundle_parent_index, $puiw_last_woosb_bundle_child_index;


          if ($this->is_bundled_parent($item_id)){
            $classes[] = "woosb-order-item woosb-item-parent";
            if ($this->_woosb_show_bundled_hierarchy == "yes"){
              $puiw_last_woosb_bundle_child_index = false;
              $puiw_last_woosb_bundle_parent_index = $nthitem;
              $classes[] = $odd_even;
              return $classes;
            }
          }
          if ($this->is_bundled_child($item_id)){
            $classes[] = "woosb-order-item woosb-item-child";
            if ($this->_woosb_show_bundled_hierarchy == "yes"){
              if ($puiw_last_woosb_bundle_parent_index % 2){$odd_even = "odd";}else{$odd_even = "even";}
              $puiw_last_woosb_bundle_child_index = $nthitem;
              $classes[] = $odd_even;
              return $classes;
            }
          }
          if ($this->_woosb_show_bundled_hierarchy == "yes"){
            if ($puiw_last_woosb_bundle_child_index && $puiw_last_woosb_bundle_child_index>0){
              if ( ( $nthitem - 1 -$puiw_last_woosb_bundle_child_index ) % 2) {
                $odd_even = "odd";
              } else {
                $odd_even = "even";
              }
              $classes[] = $odd_even;
              return $classes;
            }
          }

          $classes[] = $odd_even;
          return $classes;
        }
        /**
         * add bundled in to bundled products child
         *
         * @method  woosb_before_order_item_meta
         * @param   int $order_item_id
         * @return  string
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function woosb_before_order_item_meta($order_item_id)
        {
          if ( $parent_id = wc_get_order_item_meta( $order_item_id, '_woosb_parent_id', true ) ) {
            if ($this->_woosb_show_bundled_subtitle == "yes"){
              echo $this->_woosb_show_bundled_prefix." ".get_the_title($parent_id);
            }
          }
        }
        /**
         * Sort Order Items
         *
         * @method  puiw_sort_order_items
         * @param   array $items
         * @param   WC_Order $order
         * @return  array $items
         * @version 1.0.0
         * @since   1.3.0
         * @license https://pepro.dev/license Pepro.dev License
         */
        public function puiw_sort_order_items($items, $order)
        {
          if( count($items) > 1 ) {
            $item_qty = array();
            $item_order = array();
            $sort_by = apply_filters( "puiw_order_items_sort_by", $this->fn->get_items_sorting("NONE"));
            if ( !$sort_by || empty($sort_by) || $sort_by == "NONE" ){
              return $items;
            }
            foreach( $items as $items_id => $item ){
              // Check items type: for versions before Woocommerce 3.3
              if( $item->is_type('line_item') && method_exists( $item, 'get_product' ) ){
                $product = $item->get_product(); // Get the product Object
                if( is_a( $product, 'WC_Product' ) ) {
                  switch ($sort_by) {
                    case 'ID':
                      $item_sku[$items_id] = $items_id;
                      break;
                    case 'PID':
                      $item_sku[$items_id] = $items_id;
                      break;
                    case 'SKU':
                      $item_sku[$product->get_sku()] = $items_id;
                      break;
                    case 'QTY':
                      $item_order[$item->get_quantity()] = $items_id;
                      break;
                    case 'NAME':
                      $item_order[$item->get_name()] = $items_id;
                      break;
                    case 'PRICE':
                      $item_order[$order->get_item_subtotal( $item, false, true )] = $items_id;
                      break;
                    case 'TOTAL':
                      $item_order[$item->get_total()] = $items_id;
                      break;
                    case 'WEIGHT':
                      $item_order[$product->get_weight()] = $items_id;
                      break;
                    case 'SUBTOTAL':
                      $item_order[$item->get_subtotal()] = $items_id;
                      break;
                    case 'SUBTOTAL_TAX':
                      $item_order[$item->get_subtotal_tax()] = $items_id;
                      break;
                    default: // by Hook
                      $sort_by_force = apply_filters( "puiw_order_items_sort_by_force", $items_id, $item, $product, $items, $sort_by);
                      $item_order[$sort_by_force] = $items_id;
                      break;
                  }
                }
              }
            }
            if(!empty($item_order)) {
              if (apply_filters( "puiw_order_items_sort_desc", false) === true){ krsort($item_order); }else{ ksort($item_order); }
              foreach( $item_order as $sku => $item_id ){ $sorted_items[$item_id] = $items[$item_id]; }
              $items = $sorted_items;
            }
          }
          return $items;
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
    }
}
/*##################################################
Lead Developer: [amirhosseinhpv](https://hpv.im/)
##################################################*/
