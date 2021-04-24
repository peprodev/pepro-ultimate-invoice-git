<?php
# @Last modified time: 2021/04/24 21:18:07

namespace peproulitmateinvoice;

defined("ABSPATH") or die("Pepro Ultimate Invoice :: Unauthorized Access!");

if (!class_exists("PeproUltimateInvoice_Template")) {
  class PeproUltimateInvoice_Template
  {
    /**
     * return wc product weight in a human readable way
     *
     * @method PeproUltimateInvoice_Template->get_product_weight()
     * @param WC_Product $product
     * @return string weight and unit
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_product_weight($product_id)
    {
      $product = wc_get_product($product_id);
      $weight = $product->get_weight();
      return apply_filters( "puiw_get_product_weight",$this->get_format_weight($weight), $weight, $product);
    }
    /**
     * return wc product weight in a human readable way
     *
     * @method PeproUltimateInvoice_Template->get_format_weight()
     * @param WC_Product $product
     * @return string weight and unit
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_format_weight($weight)
    {
      return apply_filters( "puiw_get_format_weight", wc_format_weight($weight), $weight);
    }
    /**
     * return wc product dimensions in a huuman readable way
     *
     * @method PeproUltimateInvoice_Template->get_product_dimension()
     * @param WC_Product $product
     * @return string dimensions and unit
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_product_dimension($product_id)
    {
      $product = wc_get_product($product_id);
      return apply_filters( "puiw_get_product_dimension",wc_format_dimensions( $product->get_dimensions( false ) ), $product->get_dimensions( false ),$product);
    }
    /**
    * get formatted datatime based on plugin's date parsing setting
    *
    * @method PeproUltimateInvoice_Template->get_date()
    * @param string $date_str datatime
    * @param string $default_format default format
    * @param boolean $force_format force default format overwrite plugin setting
    * @return string formatted datetime
    * @version 1.0.0
    * @since 1.0.0
    * @license https://pepro.dev/license Pepro.dev License
    */
    public function get_date($date_str="",$default_format="Y/m/d H:i", $force_format=false)
    {
      $date_format = get_option("puiw_date_format",$default_format);
      $date_format = empty($date_format) ? $default_format : $date_format;
      $date_format = $force_format ? $default_format : $date_format;
      /**
       * filter date if persian date option is set to true
       */

      (int) $timestamp = strtotime($date_str);
      $converted_date = date_i18n( $date_format, $timestamp);
      if ($this->get_date_shamsi() == "yes") {
        $converted_date = pu_jdate($date_format, $timestamp, "", "local", "en");
      }
      return apply_filters("puiw_get_date", $converted_date, $date_str, $timestamp, $date_format, $default_format, $force_format);
    }
    /**
     * change numbers into western arabic or farsi or keep it as is
     *
     * @method PeproUltimateInvoice_Template->parse_number()
     * @param string $context
     * @return string formtatted context
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function parse_number($context="")
    {
      if ($this->get_force_persian_numbers() == "arabic") {
          $western_arabic = array('0','1','2','3','4','5','6','7','8','9');
          $eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');
          $context = str_replace($western_arabic, $eastern_arabic, $context);
          return $context;
      }
      if ($this->get_force_persian_numbers() == "farsi") {
          $english = array("0","1","2","3","4","5","6","7","8","9");
          $farsi = array("۰","۱","۲","۳","۴","۵","۶","۷","۸","۹");
          $context = str_replace($english, $farsi, $context);
          return $context;
      }
      return $context;
    }
    /**
     * get store name
     *
     * @method PeproUltimateInvoice_Template->get_store_name()
     * @return string store name
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_name()
    {
      $store_name = get_option("puiw_store_name","");
      $store_name = empty($store_name) ? get_bloginfo('name') : $store_name;
      return apply_filters("puiw_get_store_name", $store_name);
    }
    /**
     * get Invoice Title
     *
     * @method PeproUltimateInvoice_Template->get_invoice_title()
     * @return string invoice title
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_invoice_title($default="")
    {
      $invoice_title = get_option("puiw_invoice_title","");
      $invoice_title = empty($invoice_title) ? $default : $invoice_title;
      return apply_filters("puiw_get_invoice_title", $invoice_title);
    }
    /**
    * get logo url
    *
    * @method PeproUltimateInvoice_Template->get_store_logo()
    * @param string $default default logo url
    * @return string logo
    * @version 1.0.0
    * @since 1.0.0
    * @license https://pepro.dev/license Pepro.dev License
    */
    public function get_store_logo($default="")
    {
      $store_logo = get_option("puiw_store_logo",$default);
      $store_logo = empty($store_logo) ? $default : $store_logo;
      return apply_filters("puiw_get_store_logo", $store_logo,$default);
    }
    /**
     * get store website
     *
     * @method PeproUltimateInvoice_Template->get_store_website()
     * @param string $default default store url
     * @return string store url
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_website($default="")
    {
      $store_website = get_option("puiw_store_website",$default);
      $default = empty($default) ? get_bloginfo('url') : $default;
      $store_website = empty($store_website) ? $default : $store_website;
      return apply_filters("puiw_get_store_website", $store_website, $default);
    }
    /**
     * get email subject
     *
     * @method PeproUltimateInvoice_Template->get_email_subject()
     * @return string invoice title
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_email_subject($default="")
    {
      $invoice_title = get_option("puiw_email_subject","");
      $invoice_title = empty($invoice_title) ? $default : $invoice_title;
      return apply_filters("puiw_get_email_subject", $invoice_title);
    }
    /**
     * get sender email from name
     *
     * @method PeproUltimateInvoice_Template->get_email_from_name()
     * @param string $default sender email from name
     * @return string sender email from name
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_email_from_name($default="")
    {
      $email_from_name = get_option("puiw_email_from_name",$default);
      $default = empty($default) ? get_bloginfo('name','display') : $default;
      $email_from_name = empty($email_from_name) ? $default : $email_from_name;
      return apply_filters("puiw_get_email_from_name", $email_from_name, $default);
    }
    /**
     * get sender email from address
     *
     * @method PeproUltimateInvoice_Template->get_email_from_address()
     * @param string $default sender email from address
     * @return string sender email from address
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_email_from_address($default="")
    {
      $email_from_address = get_option("puiw_email_from_address",$default);
      $default = empty($default) ? get_option("admin_email") : $default;
      $email_from_address = empty($email_from_address) ? $default : $email_from_address;
      return apply_filters("puiw_get_email_from_address", $email_from_address, $default);
    }
    /**
     * get store email
     *
     * @method PeproUltimateInvoice_Template->get_store_email()
     * @param string $default default store email
     * @return string store email
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_email($default="")
    {
      $store_email = get_option("puiw_store_email",$default);
      $default = empty($default) ? get_option("admin_email") : $default;
      $store_email = empty($store_email) ? $default : $store_email;
      return apply_filters("puiw_get_store_email", $store_email, $default);
    }
    /**
     * get store phone
     *
     * @method PeproUltimateInvoice_Template->get_store_phone()
     * @param string $default default store phone
     * @return string store phone
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_phone($default="")
    {
      $store_phone = get_option("puiw_store_phone",$default);
      $store_phone = empty($store_phone) ? $default : $store_phone;
      return apply_filters("puiw_get_store_phone", $store_phone, $default);
    }
    /**
     * get store National ID
     *
     * @method PeproUltimateInvoice_Template->get_store_national_id()
     * @param string $default default National ID
     * @return string store National ID
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_national_id($default="")
    {
      $store_national_id = get_option("puiw_store_national_id",$default);
      $store_national_id = empty($store_national_id) ? $default : $store_national_id;
      return apply_filters("puiw_get_store_national_id", $store_national_id, $default);
    }
    /**
     * get Registration Number
     *
     * @method PeproUltimateInvoice_Template->get_store_registration_number()
     * @param string $default default Registration Number
     * @return string store Registration Number
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_registration_number($default="")
    {
      $store_registration_number = get_option("puiw_store_registration_number",$default);
      $store_registration_number = empty($store_registration_number) ? $default : $store_registration_number;
      return apply_filters("puiw_get_store_registration_number", $store_registration_number, $default);
    }
    /**
     * get Economical Number
     *
     * @method PeproUltimateInvoice_Template->get_store_economical_number()
     * @param string $default default Economical Number
     * @return string store Economical Number
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_economical_number($default="")
    {
      $store_economical_number = get_option("puiw_store_economical_number",$default);
      $store_economical_number = empty($store_economical_number) ? $default : $store_economical_number;
      return apply_filters("puiw_get_store_economical_number", $store_economical_number, $default);
    }
    /**
     * Show Store National Id
     *
     * @method PeproUltimateInvoice_Template->get_show_store_national_id()
     * @param string $default default state
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_store_national_id($default="yes")
    {
      $show_store_national_id = get_option("puiw_show_store_national_id",$default);
      $show_store_national_id = empty($show_store_national_id) ? $default : $show_store_national_id;
      return apply_filters("puiw_get_show_store_national_id", $show_store_national_id, $default);
    }
    /**
     * Show Store Registration Number
     *
     * @method PeproUltimateInvoice_Template->get_show_store_registration_number()
     * @param string $default default state
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_store_registration_number($default="yes")
    {
      $show_store_registration_number = get_option("puiw_show_store_registration_number",$default);
      $show_store_registration_number = empty($show_store_registration_number) ? $default : $show_store_registration_number;
      return apply_filters("puiw_get_show_store_registration_number", $show_store_registration_number, $default);
    }
    /**
     * Show Store Economical Number
     *
     * @method PeproUltimateInvoice_Template->get_show_store_economical_number()
     * @param string $default default state
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_store_economical_number($default="yes")
    {
      $show_store_economical_number = get_option("puiw_show_store_economical_number",$default);
      $show_store_economical_number = empty($show_store_economical_number) ? $default : $show_store_economical_number;
      return apply_filters("puiw_get_show_store_economical_number", $show_store_economical_number, $default);
    }
    /**
     * Force Persian Numbers?
     *
     * @method PeproUltimateInvoice_Template->get_force_persian_numbers()
     * @param string $default default state
     * @return string arabic/farsi/no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_force_persian_numbers($default="no")
    {
      $force_persian_numbers = get_option("puiw_force_persian_numbers",$default);
      $force_persian_numbers = empty($force_persian_numbers) ? $default : $force_persian_numbers;
      return apply_filters("puiw_get_force_persian_numbers", $force_persian_numbers, $default);
    }
    /**
     * get store address
     *
     * @method PeproUltimateInvoice_Template->get_store_address()
     * @param string $default default store address
     * @return string store address
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_address($default="")
    {
      $store_address = get_option("puiw_store_address",$default);
      $default = empty($default) ? $this->get_wc_store_address() : $default;
      $store_address = empty($store_address) ? $default : $store_address;
      return apply_filters("puiw_get_store_address", $store_address, $default);
    }
    /**
     * get woocommerce store address
     *
     * @method get_wc_store_address
     * @return string woocommerce store address
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_wc_store_address()
    {
      // The main address pieces:
      $store_address     = get_option( 'woocommerce_store_address' );
      $store_address_2   = get_option( 'woocommerce_store_address_2' );
      $store_city        = get_option( 'woocommerce_store_city' );
      $store_postcode    = get_option( 'woocommerce_store_postcode' );

      // The country/state
      $store_raw_country = get_option( 'woocommerce_default_country' );

      // Split the country/state
      $split_country = explode( ":", $store_raw_country );

      // Country and state separated:
      $store_country = $split_country[0];
      $store_state   = $split_country[1];

      $return = $store_address . "<br />";
      $return .= ( $store_address_2 ) ? $store_address_2 . "<br />" : '';
      $return .= $store_city . ', ' . $store_state . ' ' . $store_postcode . "<br />";
      $return .= $store_country;
      return apply_filters("puiw_get_wc_store_address", $return, $store_address, $store_address_2, $store_city, $store_postcode, $store_country, $store_state, $store_raw_country);
    }
    /**
     * get store postcode / zip
     *
     * @method PeproUltimateInvoice_Template->get_store_postcode()
     * @param string $default default postcode / zip
     * @return string store postcode / zip
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_store_postcode($default="")
    {
      $store_p_o_box = get_option("puiw_store_postcode",$default);
      $store_p_o_box = empty($store_p_o_box) ? $default : $store_p_o_box;
      return apply_filters("puiw_get_store_postcode", $store_p_o_box, $default);
    }
    /**
     * get send invoices via email to customer method
     *
     * @method PeproUltimateInvoice_Template->get_send_invoices_via_email()
     * @param string $default default send invoices via email method
     * @return string manual / automatic
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_send_invoices_via_email($default="manual")
    {
      $send_invoices_via_email = get_option("puiw_send_invoices_via_email",$default);
      $send_invoices_via_email = empty($send_invoices_via_email) ? $default : $send_invoices_via_email;
      return apply_filters("puiw_get_send_invoices_via_email", $send_invoices_via_email, $default);
    }
    /**
     * get order statuses for automatic sending to customers
     *
     * @method PeproUltimateInvoice_Template->get_send_invoices_via_email_opt()
     * @param array $default default wc order statuses
     * @return array wc order statuses
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_send_invoices_via_email_opt($default=array())
    {
      $send_invoices_via_email_opt = get_option("puiw_send_invoices_via_email_opt",$default);
      $send_invoices_via_email_opt = empty($send_invoices_via_email_opt) ? $default : $send_invoices_via_email_opt;
      return apply_filters("puiw_get_send_invoices_via_email_opt", $send_invoices_via_email_opt, $default);
    }
    /**
     * get send invoices via email admin method
     *
     * @method PeproUltimateInvoice_Template->get_send_invoices_via_email_admin()
     * @param string $default default send invoices via email admin method
     * @return string manual / automatic
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_send_invoices_via_email_admin($default="")
    {
      $send_invoices_via_email_admin = get_option("puiw_send_invoices_via_email_admin",$default);
      $send_invoices_via_email_admin = empty($send_invoices_via_email_admin) ? $default : $send_invoices_via_email_admin;
      return apply_filters("puiw_get_send_invoices_via_email_admin", $send_invoices_via_email_admin, $default);
    }
    /**
     * get order statuses for send invoices via email opt admin
     *
     * @method PeproUltimateInvoice_Template->get_send_invoices_via_email_opt_admin()
     * @param array $default default order statuses for send invoices via email opt admin
     * @return array wc order statuses
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_send_invoices_via_email_opt_admin($default=array())
    {
      $send_invoices_via_email_opt_admin = get_option("puiw_send_invoices_via_email_opt_admin",$default);
      $send_invoices_via_email_opt_admin = empty($send_invoices_via_email_opt_admin) ? $default : $send_invoices_via_email_opt_admin;
      return apply_filters("puiw_get_send_invoices_via_email_opt_admin", $send_invoices_via_email_opt_admin, $default);
    }
    /**
     * get selected shop managers email
     *
     * @method PeproUltimateInvoice_Template->get_send_invoices_via_email_shpmngrs()
     * @param array $default default shop managers email
     * @return array selected shop managers email
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_send_invoices_via_email_shpmngrs($default=array())
    {
      $send_invoices_via_email_shpmngrs = get_option("puiw_send_invoices_via_email_shpmngrs",$default);
      $send_invoices_via_email_shpmngrs = empty($send_invoices_via_email_shpmngrs) ? $default : $send_invoices_via_email_shpmngrs;
      return apply_filters("puiw_get_send_invoices_via_email_shpmngrs", $send_invoices_via_email_shpmngrs, $default);
    }
    /**
     * allow users use invoices ?
     *
     * @method PeproUltimateInvoice_Template->get_allow_users_use_invoices()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_users_use_invoices($default="no")
    {
      $allow_users_use_invoices = get_option("puiw_allow_users_use_invoices",$default);
      $allow_users_use_invoices = empty($allow_users_use_invoices) ? $default : $allow_users_use_invoices;
      return apply_filters("puiw_get_allow_users_use_invoices", $allow_users_use_invoices, $default);
    }
    /**
     * allow users have invoices ?
     *
     * @method PeproUltimateInvoice_Template->get_allow_users_have_invoices()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_users_have_invoices($default="no")
    {
      $allow_users_use_invoices = get_option("puiw_allow_users_have_invoices",$default);
      $allow_users_use_invoices = empty($allow_users_use_invoices) ? $default : $allow_users_use_invoices;
      return apply_filters("puiw_get_allow_users_have_invoices", $allow_users_use_invoices, $default);
    }
    /**
     * allow guest users use invoices ?
     *
     * @method PeproUltimateInvoice_Template->get_allow_guest_users_view_invoices()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_guest_users_view_invoices($default="no")
    {
      $allow_users_use_invoices = get_option("puiw_allow_guest_users_view_invoices",$default);
      $allow_users_use_invoices = empty($allow_users_use_invoices) ? $default : $allow_users_use_invoices;
      return apply_filters("puiw_get_allow_guest_users_view_invoices", $allow_users_use_invoices, $default);
    }
    /**
     * allowed users use invoices wc order statuses
     *
     * @method PeproUltimateInvoice_Template->get_allow_users_use_invoices_criteria()
     * @param string $default default wc order statuses
     * @return string wc order statuses
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_users_use_invoices_criteria($default=array())
    {
      $allow_users_use_invoices_criteria = get_option("puiw_allow_users_use_invoices_criteria",$default);
      $allow_users_use_invoices_criteria = empty($allow_users_use_invoices_criteria) ? $default : $allow_users_use_invoices_criteria;
      return apply_filters("puiw_get_allow_users_use_invoices_criteria", $allow_users_use_invoices_criteria, $default);
    }
    /**
     * Add & Show Invoices Number Barcode?
     *
     * @method PeproUltimateInvoice_Template->get_show_invoices_id_barcode()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_invoices_id_barcode($default="no")
    {
      $show_barcode_id = get_option("puiw_show_barcode_id",$default);
      $show_barcode_id = empty($show_barcode_id) ? $default : $show_barcode_id;
      return apply_filters("puiw_get_show_invoices_id_barcode", $show_barcode_id, $default);
    }
    /**
     * Add & Show Barcode in Sender Postal Label?
     *
     * @method PeproUltimateInvoice_Template->get_show_shippingslip_store()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shippingslip_store($default="no")
    {
      $postal_stickey_label_for_store = get_option("puiw_postal_stickey_label_for_store",$default);
      $postal_stickey_label_for_store = empty($postal_stickey_label_for_store) ? $default : $postal_stickey_label_for_store;
      return apply_filters("puiw_get_postal_stickey_label_for_store", $postal_stickey_label_for_store, $default);
    }
    /**
     * Add & Show Barcode in Recipient Postal Label?
     *
     * @method PeproUltimateInvoice_Template->get_show_shippingslip_customer()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shippingslip_customer($default="no")
    {
      $postal_stickey_label_for_customer = get_option("puiw_postal_stickey_label_for_customer",$default);
      $postal_stickey_label_for_customer = empty($postal_stickey_label_for_customer) ? $default : $postal_stickey_label_for_customer;
      return apply_filters("puiw_get_postal_stickey_label_for_customer", $postal_stickey_label_for_customer, $default);
    }
    /**
     * Add & Show Invoices QR Code?
     *
     * @method PeproUltimateInvoice_Template->get_show_qr_code_id()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_qr_code_id($default="no")
    {
      $show_qr_code_id = get_option("puiw_show_qr_code_id",$default);
      $show_qr_code_id = empty($show_qr_code_id) ? $default : $show_qr_code_id;
      return apply_filters("puiw_get_show_qr_code_id", $show_qr_code_id, $default);
    }
    /**
     * show postal qr code label for store
     *
     * @method PeproUltimateInvoice_Template->get_postal_qr_code_label_for_store()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_postal_qr_code_label_for_store($default="no")
    {
      $postal_qr_code_label_for_store = get_option("puiw_postal_qr_code_label_for_store",$default);
      $postal_qr_code_label_for_store = empty($postal_qr_code_label_for_store) ? $default : $postal_qr_code_label_for_store;
      return apply_filters("puiw_get_postal_qr_code_label_for_store", $postal_qr_code_label_for_store, $default);
    }
    /**
     * show postal qr code label for customer
     *
     * @method PeproUltimateInvoice_Template->get_postal_qr_code_label_for_customer()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_postal_qr_code_label_for_customer($default="")
    {
      $postal_qr_code_label_for_customer = get_option("puiw_postal_qr_code_label_for_customer",$default);
      $postal_qr_code_label_for_customer = empty($postal_qr_code_label_for_customer) ? $default : $postal_qr_code_label_for_customer;
      return apply_filters("puiw_get_postal_qr_code_label_for_customer", $postal_qr_code_label_for_customer, $default);
    }
    /**
     * use shamsi date ?
     *
     * @method PeproUltimateInvoice_Template->get_date_shamsi()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_date_shamsi($default="no")
    {
      $date_shamsi = get_option("puiw_date_shamsi",$default);
      $date_shamsi = empty($date_shamsi) ? $default : $date_shamsi;
      return apply_filters("puiw_get_date_shamsi", $date_shamsi, $default);
    }
    /**
     * Show Shipped Date?
     *
     * @method PeproUltimateInvoice_Template->show_shipped_date()
     * @param string $default default status
     * @return string yes / no
     * @version 1.1.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function show_shipped_date($default="no")
    {
      $puiw_show_shipped_date = get_option("puiw_show_shipped_date",$default);
      $puiw_show_shipped_date = empty($puiw_show_shipped_date) ? $default : $puiw_show_shipped_date;
      return apply_filters("puiw_show_shipped_date", $puiw_show_shipped_date, $default);
    }
    /**
     * Show Shipping Serial?
     *
     * @method PeproUltimateInvoice_Template->show_shipping_serial()
     * @param string $default default status
     * @return string yes / no
     * @version 1.1.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function show_shipping_serial($default="no")
    {
      $puiw_show_shipping_serial = get_option("puiw_show_shipping_serial",$default);
      $puiw_show_shipping_serial = empty($puiw_show_shipping_serial) ? $default : $puiw_show_shipping_serial;
      return apply_filters("puiw_show_shipping_serial", $puiw_show_shipping_serial, $default);
    }
    /**
     * disable wc dashboard ?
     *
     * @method PeproUltimateInvoice_Template->get_disable_wc_dashboard()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_disable_wc_dashboard($default="no")
    {
      $disable_wc_dashboard = get_option("puiw_disable_wc_dashboard",$default);
      $disable_wc_dashboard = empty($disable_wc_dashboard) ? $default : $disable_wc_dashboard;
      return apply_filters("puiw_get_disable_wc_dashboard", $disable_wc_dashboard, $default);
    }
    /**
     * get allow preorder invoice
     *
     * @method PeproUltimateInvoice_Template->get_allow_preorder_invoice()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_preorder_invoice($default="no")
    {
      $allow_preorder_invoice = get_option("puiw_allow_preorder_invoice",$default);
      $allow_preorder_invoice = empty($allow_preorder_invoice) ? $default : $allow_preorder_invoice;
      return apply_filters("puiw_get_allow_preorder_invoice", $allow_preorder_invoice, $default);
    }
    /**
     * Allow Quick Shop feature?
     *
     * @method PeproUltimateInvoice_Template->get_allow_quick_shop()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_quick_shop($default="no")
    {
      $allow_preorder_invoice = get_option("puiw_quick_shop",$default);
      $allow_preorder_invoice = empty($allow_preorder_invoice) ? $default : $allow_preorder_invoice;
      return apply_filters("puiw_get_allow_quick_shop", $allow_preorder_invoice, $default);
    }
    /**
     * clear cart on quick buy (pre-buy invoice) ?
     *
     * @method PeproUltimateInvoice_Template->get_allow_preorder_emptycart()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_preorder_emptycart($default="no")
    {
      $allow_preorder_emptycart = get_option("puiw_allow_preorder_emptycart",$default);
      $allow_preorder_emptycart = empty($allow_preorder_emptycart) ? $default : $allow_preorder_emptycart;
      return apply_filters("puiw_get_allow_preorder_emptycart", $allow_preorder_emptycart, $default);
    }
    /**
     * get allow pdf customer
     *
     * @method PeproUltimateInvoice_Template->get_allow_pdf_customer()
     * @param string $default default status
     * @return string html/pdf/both
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_pdf_customer($default="html")
    {
      $allow_pdf_customer = get_option("puiw_allow_pdf_customer",$default);
      $allow_pdf_customer = empty($allow_pdf_customer) ? $default : $allow_pdf_customer;
      return apply_filters("puiw_get_allow_pdf_customer", $allow_pdf_customer, $default);
    }
    /**
     * get allow pdf guest
     *
     * @method PeproUltimateInvoice_Template->get_allow_pdf_guest()
     * @param string $default default status
     * @return string html/pdf/both
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_allow_pdf_guest($default="html")
    {
      $allow_pdf_customer = get_option("puiw_allow_pdf_guest",$default);
      $allow_pdf_customer = empty($allow_pdf_customer) ? $default : $allow_pdf_customer;
      return apply_filters("puiw_get_allow_pdf_guest", $allow_pdf_customer, $default);
    }
    /**
     * Get Pdf Size
     *
     * @method PeproUltimateInvoice_Template->get_pdf_size()
     * @param string $default default status
     * @return string pdf page size
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_pdf_size($default="A4")
    {
      $pdf_size = get_option("puiw_pdf_size",$default);
      $pdf_size = empty($pdf_size) ? $default : $pdf_size;
      return apply_filters("puiw_get_pdf_size", $pdf_size, $default);
    }
    /**
     * Get Pdf orientation
     *
     * @method PeproUltimateInvoice_Template->get_pdf_orientation()
     * @param string $default default status
     * @return string pdf page orientation (P: Portrait/ L: Landscape)
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_pdf_orientation($default="P")
    {
      $pdf_orientation = get_option("puiw_pdf_orientation",$default);
      $pdf_orientation = empty($pdf_orientation) ? $default : $pdf_orientation;
      return apply_filters("puiw_get_pdf_orientation", $pdf_orientation, $default);
    }
    /**
     * get attach pdf invoices to mail
     *
     * @method PeproUltimateInvoice_Template->get_attach_pdf_invoices_to_mail()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_attach_pdf_invoices_to_mail($default="no")
    {
      $attach_pdf_invoices_to_mail = get_option("puiw_attach_pdf_invoices_to_mail",$default);
      $attach_pdf_invoices_to_mail = empty($attach_pdf_invoices_to_mail) ? $default : $attach_pdf_invoices_to_mail;
      return apply_filters("puiw_get_attach_pdf_invoices_to_mail", $attach_pdf_invoices_to_mail, $default);
    }
    /**
     * get custom css style
     *
     * @method PeproUltimateInvoice_Template->get_custom_css_style()
     * @param string $default default custom css style
     * @return string custom css style
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_custom_css_style($default="")
    {
      $custom_css_style = get_option("puiw_custom_css_style",$default);
      $custom_css_style = empty($custom_css_style) ? $default : $custom_css_style;
      return apply_filters("puiw_get_custom_css_style", $custom_css_style, $default);
    }
    /**
     * get custom pdf css style
     *
     * @method PeproUltimateInvoice_Template->get_pdf_css_style()
     * @param string $default default custom css style
     * @return string custom css style
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_pdf_css_style($default="")
    {
      $custom_css_style = get_option("puiw_pdf_css_style",$default);
      $custom_css_style = empty($custom_css_style) ? $default : $custom_css_style;
      return apply_filters("puiw_get_pdf_css_style", $custom_css_style, $default);
    }
    /**
     * get extra notes on pre order by shop manager
     *
     * @method PeproUltimateInvoice_Template->get_preorder_shopmngr_extra_note()
     * @param string $default default note
     * @return string extra notes on pre order by shop manager
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_preorder_shopmngr_extra_note($default="")
    {
      $custom_css_style = get_option("puiw_preorder_shopmngr_extra_note",$default);
      $custom_css_style = empty($custom_css_style) ? $default : $custom_css_style;
      return apply_filters("puiw_get_preorder_shopmngr_extra_note", $custom_css_style, $default);
    }
    /**
     * get extra notes on pre order by customer
     *
     * @method PeproUltimateInvoice_Template->get_preorder_customer_extra_note()
     * @param string $default default note
     * @return string extra notes on pre order by shop manager
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_preorder_customer_extra_note($default="")
    {
      $custom_css_style = get_option("puiw_preorder_customer_extra_note",$default);
      $custom_css_style = empty($custom_css_style) ? $default : $custom_css_style;
      return apply_filters("puiw_get_preorder_customer_extra_note", $custom_css_style, $default);
    }
    /**
     * get inventory css style
     *
     * @method PeproUltimateInvoice_Template->get_inventory_css_style()
     * @param string $default default inventory css style
     * @return string inventory css style
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_inventory_css_style($default="")
    {
      $inventory_css_style = get_option("puiw_inventory_css_style",$default);
      $inventory_css_style = empty($inventory_css_style) ? $default : $inventory_css_style;
      return apply_filters("puiw_get_inventory_css_style", $inventory_css_style, $default);
    }
    /**
     * get template
     *
     * @method PeproUltimateInvoice_Template->get_template()
     * @param string $default default template
     * @return string template slug
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_template($default="default")
    {
      $template = get_option("puiw_template",$default);
      $template = empty($template) ? $default : $template;
      if (!file_exists("{$template}/default.cfg")){
        $template = PEPROULTIMATEINVOICE_DIR ."/template/default";
      }
      return apply_filters("puiw_get_template", $template, $default);
    }
    /**
     * get theme color
     *
     * @method PeproUltimateInvoice_Template->get_theme_color()
     * @param string $default default theme color
     * @return string theme color
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_theme_color($default="teal")
    {
      $theme_color = get_option("puiw_theme_color",$default);
      $theme_color = empty($theme_color) ? $default : $theme_color;
      return apply_filters("puiw_get_theme_color", $theme_color, $default);
    }
    /**
     * get theme secondary color
     *
     * @method PeproUltimateInvoice_Template->get_theme_color2()
     * @param string $default default theme secondary color
     * @return string theme secondary color
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_theme_color2($default="#2271b9")
    {
      $theme_color = get_option("puiw_theme_color2",$default);
      $theme_color = empty($theme_color) ? $default : $theme_color;
      return apply_filters("puiw_get_theme_color2", $theme_color, $default);
    }
    /**
     * get theme tertiary color
     *
     * @method PeproUltimateInvoice_Template->get_theme_color2()
     * @param string $default default theme secondary color
     * @return string theme secondary color
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_theme_color3($default="#555")
    {
      $theme_color = get_option("puiw_theme_color3",$default);
      $theme_color = empty($theme_color) ? $default : $theme_color;
      return apply_filters("puiw_get_theme_color3", $theme_color, $default);
    }
    /**
     * get template for pre-invoice status
     *
     * @method PeproUltimateInvoice_Template->get_preinvoice_template()
     * @param string $default default template
     * @return string template slug
     * @version 1.0.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_preinvoice_template($default="default-pre-invoice")
    {
      $template = get_option("puiw_preinvoice_template",$default);
      $template = empty($template) ? $default : $template;
      if (!file_exists("{$template}/default.cfg")){
        $template = PEPROULTIMATEINVOICE_DIR ."/template/default";
      }
      return apply_filters("puiw_get_preinvoice_template", $template, $default);
    }
    /**
     * get theme color
     *
     * @method PeproUltimateInvoice_Template->get_preinvoice_theme_color()
     * @param string $default default theme color
     * @return string theme color
     * @version 1.0.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_preinvoice_theme_color($default="teal")
    {
      $theme_color = get_option("puiw_preinvoice_theme_color",$default);
      $theme_color = empty($theme_color) ? $default : $theme_color;
      return apply_filters("puiw_get_preinvoice_theme_color", $theme_color, $default);
    }
    /**
     * get theme secondary color
     *
     * @method PeproUltimateInvoice_Template->get_preinvoice_theme_color2()
     * @param string $default default theme secondary color
     * @return string theme secondary color
     * @version 1.0.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_preinvoice_theme_color2($default="#2271b9")
    {
      $theme_color = get_option("puiw_preinvoice_theme_color2",$default);
      $theme_color = empty($theme_color) ? $default : $theme_color;
      return apply_filters("puiw_get_preinvoice_theme_color2", $theme_color, $default);
    }
    /**
     * get theme tertiary color
     *
     * @method PeproUltimateInvoice_Template->get_preinvoice_theme_color3()
     * @param string $default default theme secondary color
     * @return string theme secondary color
     * @version 1.0.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_preinvoice_theme_color3($default="#555")
    {
      $theme_color = get_option("puiw_preinvoice_theme_color3",$default);
      $theme_color = empty($theme_color) ? $default : $theme_color;
      return apply_filters("puiw_get_preinvoice_theme_color3", $theme_color, $default);
    }
    /**
     * get font size
     *
     * @method PeproUltimateInvoice_Template->get_font_size()
     * @param string $default default font size
     * @return string font size
     * @version 1.0.0
     * @since 1.1.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_font_size($default="16")
    {
      $font_size = get_option("puiw_font_size",$default);
      $font_size = empty($font_size) ? $default : $font_size;
      return apply_filters("puiw_get_font_size", $font_size, $default);
    }
    /**
     * get invoice prefix
     *
     * @method PeproUltimateInvoice_Template->get_invoice_prefix()
     * @param string $default default invoice prefix
     * @return string invoice prefix
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_invoice_prefix($default="")
    {
      $invoice_prefix = get_option("puiw_invoice_prefix",$default);
      $invoice_prefix = empty($invoice_prefix) ? $default : $invoice_prefix;
      return apply_filters("puiw_get_invoice_prefix", $invoice_prefix, $default);
    }
    /**
     * Get PDF Font
     *
     * @method PeproUltimateInvoice_Template->get_pdf_font()
     * @param string $default default font
     * @return string font name
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_pdf_font($default="iranyekanfa")
    {
      $invoice_prefix = get_option("puiw_pdf_font",$default);
      $invoice_prefix = empty($invoice_prefix) ? $default : $invoice_prefix;
      return apply_filters("puiw_get_pdf_font", $invoice_prefix, $default);
    }
    /**
     * get invoice suffix
     *
     * @method PeproUltimateInvoice_Template->get_invoice_suffix()
     * @param string $default default invoice suffix
     * @return string invoice suffix
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_invoice_suffix($default="")
    {
      $invoice_suffix = get_option("puiw_invoice_suffix",$default);
      $invoice_suffix = empty($invoice_suffix) ? $default : $invoice_suffix;
      return apply_filters("puiw_get_invoice_suffix", $invoice_suffix, $default);
    }
    /**
     * get invoice start number
     *
     * @method PeproUltimateInvoice_Template->get_invoice_start()
     * @param string $default default invoice start number
     * @return string invoice start number
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_invoice_start($default="0")
    {
      $invoice_start = get_option("puiw_invoice_start",$default);
      $invoice_start = empty($invoice_start) ? $default : $invoice_start;
      return apply_filters("puiw_get_invoice_start", $invoice_start, $default);
    }
    /**
     * get signature img url
     *
     * @method PeproUltimateInvoice_Template->get_signature()
     * @param string $default default signature img url
     * @return string signature img url
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_signature($default="")
    {
      $signature = get_option("puiw_signature",$default);
      $signature = empty($signature) ? $default : $signature;
      return apply_filters("puiw_get_signature", $signature, $default);
    }
    /**
     * show signature or not?
     *
     * @method PeproUltimateInvoice_Template->get_show_signatures()
     * @param string $default default
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_signatures($default="no")
    {
      $signature = get_option("puiw_show_signatures",$default);
      $signature = empty($signature) ? $default : $signature;
      return apply_filters("puiw_get_show_signatures", $signature, $default);
    }
    /**
     * get watermark img url
     *
     * @method PeproUltimateInvoice_Template->get_watermark()
     * @param string $default default watermark img url
     * @return string watermark img url
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_watermark($default="")
    {
      $watermark = get_option("puiw_watermark",$default);
      $watermark = empty($watermark) ? $default : $watermark;
      return apply_filters("puiw_get_watermark", $watermark, $default);
    }
    /**
     * get watermark opacity
     *
     * @method PeproUltimateInvoice_Template->get_watermark_opacity()
     * @param string $default default watermark opacity
     * @return string watermark opacity
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_watermark_opacity($default="80")
    {
      $watermark_opacity = get_option("puiw_watermark_opacity",$default);
      $watermark_opacity = empty($watermark_opacity) ? $default : $watermark_opacity;
      return apply_filters("puiw_get_watermark_opacity", $watermark_opacity, $default);
    }
    /**
     * get invoices footer html
     *
     * @method PeproUltimateInvoice_Template->get_invoices_footer()
     * @param string $default default invoices footer html
     * @return string invoices footer html
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_invoices_footer($default="")
    {
      $invoices_footer = get_option("puiw_invoices_footer",$default);
      $invoices_footer = empty($invoices_footer) ? $default : $invoices_footer;
      return apply_filters("puiw_get_invoices_footer", $invoices_footer, $default);
    }
    /**
     * use shelf number id?
     *
     * @method PeproUltimateInvoice_Template->get_show_shelf_number_id()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shelf_number_id($default="no")
    {
      $shelf_number_id = get_option("puiw_shelf_number_id",$default);
      $shelf_number_id = empty($shelf_number_id) ? $default : $shelf_number_id;
      return apply_filters("puiw_get_shelf_number_id", $shelf_number_id, $default);
    }
    /**
     * show product sku inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_sku_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_sku_inventory($default="no")
    {
      $show_product_sku_inventory = get_option("puiw_show_product_sku_inventory",$default);
      $show_product_sku_inventory = empty($show_product_sku_inventory) ? $default : $show_product_sku_inventory;
      return apply_filters("puiw_get_show_product_sku_inventory", $show_product_sku_inventory, $default);
    }
    /**
     * show product sku2 inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_sku2_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_sku2_inventory($default="no")
    {
      $show_product_sku2_inventory = get_option("puiw_show_product_sku2_inventory",$default);
      $show_product_sku2_inventory = empty($show_product_sku2_inventory) ? $default : $show_product_sku2_inventory;
      return apply_filters("puiw_get_show_product_sku2_inventory", $show_product_sku2_inventory, $default);
    }

    /**
     * show Product Weight In Inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_weight_in_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_weight_in_inventory($default="no")
    {
      $show_product_image_inventory = get_option("puiw_show_product_weight_in_inventory",$default);
      $show_product_image_inventory = empty($show_product_image_inventory) ? $default : $show_product_image_inventory;
      return apply_filters("puiw_get_show_product_weight_in_inventory", $show_product_image_inventory, $default);
    }
    /**
     * show Product Total Weight In Inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_total_weight_in_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_total_weight_in_inventory($default="no")
    {
      $show_product_image_inventory = get_option("puiw_show_product_total_weight_in_inventory",$default);
      $show_product_image_inventory = empty($show_product_image_inventory) ? $default : $show_product_image_inventory;
      return apply_filters("puiw_get_show_product_total_weight_in_inventory", $show_product_image_inventory, $default);
    }
    /**
     * show Product Dimensions In Inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_dimensions_in_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_dimensions_in_inventory($default="no")
    {
      $show_product_image_inventory = get_option("puiw_show_product_dimensions_in_inventory",$default);
      $show_product_image_inventory = empty($show_product_image_inventory) ? $default : $show_product_image_inventory;
      return apply_filters("puiw_get_show_product_dimensions_in_inventory", $show_product_image_inventory, $default);
    }
    /**
     * show Product Discount Precent?
     *
     * @method PeproUltimateInvoice_Template->get_show_discount_precent()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.1.6
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_discount_precent($default="no")
    {
      $show_discount_precent = get_option("puiw_show_discount_precent",$default);
      $show_discount_precent = empty($show_discount_precent) ? $default : $show_discount_precent;
      return apply_filters("puiw_get_show_discount_precent", $show_discount_precent, $default);
    }
    /**
     * show Product tax?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_tax()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.1.6
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_tax($default="no")
    {
      $show_product_tax = get_option("puiw_show_product_tax",$default);
      $show_product_tax = empty($show_product_tax) ? $default : $show_product_tax;
      return apply_filters("puiw_get_show_product_tax", $show_product_tax, $default);
    }
    /**
     * show Product Quantity In Inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_quantity_in_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_quantity_in_inventory($default="no")
    {
      $show_product_image_inventory = get_option("puiw_show_product_quantity_in_inventory",$default);
      $show_product_image_inventory = empty($show_product_image_inventory) ? $default : $show_product_image_inventory;
      return apply_filters("puiw_get_show_product_quantity_in_inventory", $show_product_image_inventory, $default);
    }
    /**
     * show Product Note In Inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_note_in_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_note_in_inventory($default="no")
    {
      $show_product_image_inventory = get_option("puiw_show_product_note_in_inventory",$default);
      $show_product_image_inventory = empty($show_product_image_inventory) ? $default : $show_product_image_inventory;
      return apply_filters("puiw_get_show_product_note_in_inventory", $show_product_image_inventory, $default);
    }
    /**
     * show product image inventory?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_image_inventory()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_image_inventory($default="no")
    {
      $show_product_image_inventory = get_option("puiw_show_product_image_inventory",$default);
      $show_product_image_inventory = empty($show_product_image_inventory) ? $default : $show_product_image_inventory;
      return apply_filters("puiw_get_show_product_image_inventory", $show_product_image_inventory, $default);
    }
    /**
     * price template in inventory report
     *
     * @method PeproUltimateInvoice_Template->get_price_inventory_report()
     * @param string $default default price template
     * @return string price template ( hide_all_price, show_only_regular_price, show_only_sale_price, show_both_regular_and_sale_price)
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_price_inventory_report($default="show_only_sale_price")
    {
      $price_inventory_report = get_option("puiw_price_inventory_report",$default);
      $price_inventory_report = empty($price_inventory_report) ? $default : $price_inventory_report;
      return apply_filters("puiw_get_price_inventory_report", $price_inventory_report, $default);
    }
    /**
     * order note inventory template
     *
     * @method PeproUltimateInvoice_Template->get_show_order_note_inventory()
     * @param string $default default order note inventory template
     * @return string order note inventory template ( hide_note_from_invoice, note_provided_by_customer, note_provided_by_shop_manager, note_provided_by_both)
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_order_note_inventory($default="note_provided_by_customer")
    {
      $show_order_note_inventory = get_option("puiw_show_order_note_inventory",$default);
      $show_order_note_inventory = empty($show_order_note_inventory) ? $default : $show_order_note_inventory;
      return apply_filters("puiw_get_show_order_note_inventory", $show_order_note_inventory, $default);
    }
    /**
     * show customer address?
     *
     * @method PeproUltimateInvoice_Template->get_show_customer_address()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_customer_address($default="no")
    {
      $show_customer_address = get_option("puiw_show_customer_address",$default);
      $show_customer_address = empty($show_customer_address) ? $default : $show_customer_address;
      return apply_filters("puiw_get_show_customer_address", $show_customer_address, $default);
    }
    /**
     * show customer phone?
     *
     * @method PeproUltimateInvoice_Template->get_show_customer_phone()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_customer_phone($default="no")
    {
      $show_customer_phone = get_option("puiw_show_customer_phone",$default);
      $show_customer_phone = empty($show_customer_phone) ? $default : $show_customer_phone;
      return apply_filters("puiw_get_show_customer_phone", $show_customer_phone, $default);
    }
    /**
     * show customer email?
     *
     * @method PeproUltimateInvoice_Template->get_show_customer_email()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_customer_email($default="no")
    {
      $show_customer_email = get_option("puiw_show_customer_email",$default);
      $show_customer_email = empty($show_customer_email) ? $default : $show_customer_email;
      return apply_filters("puiw_get_show_customer_email", $show_customer_email, $default);
    }
    /**
     * show order date?
     *
     * @method PeproUltimateInvoice_Template->get_show_order_date()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_order_date($default="no")
    {
      $show_order_date = get_option("puiw_show_order_date",$default);
      $show_order_date = empty($show_order_date) ? $default : $show_order_date;
      return apply_filters("puiw_get_show_order_date", $show_order_date, $default);
    }
    /**
     * show payment method?
     *
     * @method PeproUltimateInvoice_Template->get_show_payment_method()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_payment_method($default="no")
    {
      $show_payment_method = get_option("puiw_show_payment_method",$default);
      $show_payment_method = empty($show_payment_method) ? $default : $show_payment_method;
      return apply_filters("puiw_get_show_payment_method", $show_payment_method, $default);
    }
    /**
     * show shipping method?
     *
     * @method PeproUltimateInvoice_Template->get_show_shipping_method()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shipping_method($default="no")
    {
      $show_shipping_method = get_option("puiw_show_shipping_method",$default);
      $show_shipping_method = empty($show_shipping_method) ? $default : $show_shipping_method;
      return apply_filters("puiw_get_show_shipping_method", $show_shipping_method, $default);
    }
    /**
     * shipping/billing address method
     *
     * @method PeproUltimateInvoice_Template->get_show_shipping_address()
     * @param string $default default status
     * @return string shipping/billing
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shipping_address($default="billing")
    {
      $show_shipping_address = get_option("puiw_show_shipping_address",$default);
      $show_shipping_address = empty($show_shipping_address) ? $default : $show_shipping_address;
      return apply_filters("puiw_get_show_shipping_address", $show_shipping_address, $default);
    }
    /**
     * get address display template
     *
     * @method PeproUltimateInvoice_Template->get_address_display_method()
     * @param string $default default address display template
     * @return string address display template
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_address_display_method($default="[province], [city], [address1], [address2] ([po_box])")
    {
      $address_display_method = get_option("puiw_address_display_method",$default);
      $address_display_method = empty($address_display_method) ? $default : $address_display_method;
      return apply_filters("puiw_get_address_display_method", $address_display_method, $default);
    }
    /**
     * show transaction ref id?
     *
     * @method PeproUltimateInvoice_Template->get_show_transaction_ref_id()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_transaction_ref_id($default="no")
    {
      $transaction_ref_id = get_option("puiw_transaction_ref_id",$default);
      $transaction_ref_id = empty($transaction_ref_id) ? $default : $transaction_ref_id;
      return apply_filters("puiw_get_show_transaction_ref_id", $transaction_ref_id, $default);
    }
    /**
     * Show Paid Date
     *
     * @method PeproUltimateInvoice_Template->get_show_paid_date()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_paid_date($default="no")
    {
      $transaction_ref_id = get_option("puiw_paid_date",$default);
      $transaction_ref_id = empty($transaction_ref_id) ? $default : $transaction_ref_id;
      return apply_filters("puiw_get_show_paid_date", $transaction_ref_id, $default);
    }
    /**
     * Show Order Status
     *
     * @method PeproUltimateInvoice_Template->get_show_order_status()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_order_status($default="no")
    {
      $transaction_ref_id = get_option("puiw_order_status",$default);
      $transaction_ref_id = empty($transaction_ref_id) ? $default : $transaction_ref_id;
      return apply_filters("puiw_get_show_order_status", $transaction_ref_id, $default);
    }
    /**
     * show Purchase Complete Date
     *
     * @method PeproUltimateInvoice_Template->get_show_purchase_complete_date()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_purchase_complete_date($default="no")
    {
      $transaction_ref_id = get_option("puiw_purchase_complete_date",$default);
      $transaction_ref_id = empty($transaction_ref_id) ? $default : $transaction_ref_id;
      return apply_filters("puiw_get_show_purchase_complete_date", $transaction_ref_id, $default);
    }
    /**
     * Show Shipping Date
     *
     * @method PeproUltimateInvoice_Template->get_show_shipping_date()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shipping_date($default="no")
    {
      $transaction_ref_id = get_option("puiw_shipping_date",$default);
      $transaction_ref_id = empty($transaction_ref_id) ? $default : $transaction_ref_id;
      return apply_filters("puiw_get_show_shipping_date", $transaction_ref_id, $default);
    }
    /**
     * show product image?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_image()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_image($default="no")
    {
      $show_product_image = get_option("puiw_show_product_image",$default);
      $show_product_image = empty($show_product_image) ? $default : $show_product_image;
      return apply_filters("puiw_get_show_product_image", $show_product_image, $default);
    }
    /**
     * show product purchase note?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_purchase_note()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_purchase_note($default="no")
    {
      $show_product_image = get_option("puiw_show_product_purchase_note",$default);
      $show_product_image = empty($show_product_image) ? $default : $show_product_image;
      return apply_filters("puiw_get_show_product_purchase_note", $show_product_image, $default);
    }
    /**
     * show order items?
     *
     * @method PeproUltimateInvoice_Template->get_show_order_items()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_order_items($default="no")
    {
      $show_order_items = get_option("puiw_show_order_items",$default);
      $show_order_items = empty($show_order_items) ? $default : $show_order_items;
      return apply_filters("puiw_get_show_order_items", $show_order_items, $default);
    }
    /**
     * show order total?
     *
     * @method PeproUltimateInvoice_Template->get_show_order_total()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_order_total($default="no")
    {
      $show_order_total = get_option("puiw_show_order_total",$default);
      $show_order_total = empty($show_order_total) ? $default : $show_order_total;
      return apply_filters("puiw_get_show_order_total", $show_order_total, $default);
    }
    /**
     * get show order note template
     *
     * @method PeproUltimateInvoice_Template->get_show_order_note()
     * @param string $default default show order note template
     * @return string order note template (hide_note_from_invoice note_provided_by_customer note_provided_by_shop_manager note_provided_by_both)
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_order_note($default="note_provided_by_customer")
    {
      $show_order_note = get_option("puiw_show_order_note", $default);
      $show_order_note = empty($show_order_note) ? $default : $show_order_note;
      return apply_filters("puiw_get_show_order_note", $show_order_note, $default);
    }
    /**
     * Get order note
     *
     * @method get_order_note
     * @param WC_Order $order
     * @param string $note_type customer|shop_manager
     * @return string order note
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_order_note($order, $note_type='a')
    {
      $notes = "";
      switch ($note_type) {
        case 'a':
        case 'customer':
          if ( $order->get_customer_note() ){
            $notes .= wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) );
          }
          break;
        case 'b':
        case 'shop_manager':
          $notes .= wp_kses_post( nl2br( wptexturize( get_post_meta( $order->get_id(), "puiw_shopmngr_provided_note", true) ) ) );
          break;
        default:
          // hide_note_from_invoice
          $note_type = "default";
          break;
      }

      return apply_filters("puiw_get_order_note", $notes, $order, $note_type);
    }
    /**
     * show User Unique Identification ID?
     *
     * @method PeproUltimateInvoice_Template->get_show_user_uin()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_user_uin($default="no")
    {
      $show_user_uin = get_option("puiw_show_user_uin",$default);
      $show_user_uin = empty($show_user_uin) ? $default : $show_user_uin;
      return apply_filters("puiw_get_show_user_uin", $show_user_uin, $default);
    }
    /**
     * show shipping ref. id?
     *
     * @method PeproUltimateInvoice_Template->get_show_shipping_ref_id()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_shipping_ref_id($default="no")
    {
      $show_shipping_ref_id = get_option("puiw_show_shipping_ref_id",$default);
      $show_shipping_ref_id = empty($show_shipping_ref_id) ? $default : $show_shipping_ref_id;
      return apply_filters("puiw_get_show_shipping_ref_id", $show_shipping_ref_id, $default);
    }
    /**
     * get price template?
     *
     * @method PeproUltimateInvoice_Template->get_show_price_template()
     * @param string $default default show price template
     * @return string price template (show_only_regular_price, show_only_sale_price, show_both_regular_and_sale_price)
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_price_template($default="show_only_sale_price")
    {
      $show_price_template = get_option("puiw_show_price_template",$default);
      $show_price_template = empty($show_price_template) ? $default : $show_price_template;
      return apply_filters("puiw_get_show_price_template", $show_price_template, $default);
    }
    /**
     * show product weight?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_weight()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_weight($default="no")
    {
      $show_product_weight = get_option("puiw_show_product_weight",$default);
      $show_product_weight = empty($show_product_weight) ? $default : $show_product_weight;
      return apply_filters("puiw_get_show_product_weight", $show_product_weight, $default);
    }
    /**
     * show product dimensions?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_dimensions()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_dimensions($default="no")
    {
      $show_product_dimensions = get_option("puiw_show_product_dimensions",$default);
      $show_product_dimensions = empty($show_product_dimensions) ? $default : $show_product_dimensions;
      return apply_filters("puiw_get_show_product_dimensions", $show_product_dimensions, $default);
    }
    /**
     * show product sku?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_sku()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_sku($default="no")
    {
      $show_product_sku = get_option("puiw_show_product_sku",$default);
      $show_product_sku = empty($show_product_sku) ? $default : $show_product_sku;
      return apply_filters("puiw_get_show_product_sku", $show_product_sku, $default);
    }
    /**
     * show product sku2?
     *
     * @method PeproUltimateInvoice_Template->get_show_product_sku2()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.0.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_show_product_sku2($default="no")
    {
      $show_product_sku2 = get_option("puiw_show_product_sku2",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_show_product_sku2", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Show bundles
     *
     * @method PeproUltimateInvoice_Template->get_woosb_show_bundles()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_show_bundles($default="no")
    {
      $show_product_sku2 = get_option("puiw_woosb_show_bundles",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_show_bundles", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Show bundles subtitle
     *
     * @method PeproUltimateInvoice_Template->get_woosb_show_bundles_subtitle()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_show_bundles_subtitle($default="no")
    {
      $show_product_sku2 = get_option("puiw_woosb_show_bundles_subtitle",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_show_bundles_subtitle", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Show bundled products
     *
     * @method PeproUltimateInvoice_Template->get_woosb_show_bundled_products()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_show_bundled_products($default="no")
    {
      $show_product_sku2 = get_option("puiw_woosb_show_bundled_products",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_show_bundled_products", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Show bundled products subtitle
     *
     * @method PeproUltimateInvoice_Template->get_woosb_show_bundled_subtitle()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_show_bundled_subtitle($default="no")
    {
      $show_product_sku2 = get_option("puiw_woosb_show_bundled_subtitle",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_show_bundled_subtitle", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Show bundled products hierarchy
     *
     * @method PeproUltimateInvoice_Template->get_woosb_show_bundled_hierarchy()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_show_bundled_hierarchy($default="no")
    {
      $show_product_sku2 = get_option("puiw_woosb_show_bundled_hierarchy",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_show_bundled_hierarchy", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Bundled products subtitle prefix
     *
     * @method PeproUltimateInvoice_Template->get_woosb_bundled_subtitle_prefix()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_bundled_subtitle_prefix($default="")
    {
      $show_product_sku2 = get_option("puiw_woosb_bundled_subtitle_prefix",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_bundled_subtitle_prefix", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Bundles products subtitle prefix
     *
     * @method PeproUltimateInvoice_Template->get_woosb_bundles_subtitle_prefix()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_woosb_bundles_subtitle_prefix($default="")
    {
      $show_product_sku2 = get_option("puiw_woosb_bundles_subtitle_prefix",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_woosb_bundles_subtitle_prefix", $show_product_sku2, $default);
    }
    /**
     * WPC Product Bundles: Bundled products prefix
     *
     * @method PeproUltimateInvoice_Template->get_woosb_bundled_subtitle_prefix()
     * @param string $default default status
     * @return string yes / no
     * @version 1.0.0
     * @since 1.3.0
     * @license https://pepro.dev/license Pepro.dev License
     */
    public function get_items_sorting($default="")
    {
      $show_product_sku2 = get_option("puiw_items_sorting",$default);
      $show_product_sku2 = empty($show_product_sku2) ? $default : $show_product_sku2;
      return apply_filters("puiw_get_items_sorting", $show_product_sku2, $default);
    }

  }
}
/*##################################################
Lead Developer: [amirhosseinhpv](https://hpv.im/)
##################################################*/
