<?php
namespace peproulitmateinvoice;
defined("ABSPATH") or die("Pepro Ultimate Invoice :: Unauthorized Access!");

if (!class_exists("PeproUltimateInvoice_wcPanel")) {
    class PeproUltimateInvoice_wcPanel
    {
        public function __construct()
        {
        }
        public function init()
        {
            add_action('woocommerce_product_options_inventory_product_data', array( $this, 'display_extra_inventory_fields' ));
            add_action('woocommerce_process_product_meta', array( $this, 'save_fields' ));
        }
        public function display_extra_inventory_fields()
        {
            echo "<div class='options_group show_if_simple show_if_variable'>";
            woocommerce_wp_text_input(
                array(
                  'id'        => '_shelf_number_id',
                  'label'     => __('Storage rack No/ID', 'pepro-ultimate-invoice'),
                  'desc_tip'  => __('Enter the Number/ID of rack in storage that product is in', 'pepro-ultimate-invoice')
                )
            );
            echo "</div>";
        }
        public function save_fields( $post_id )
        {
            $product = wc_get_product($post_id);

            // $product->update_meta_data('field_id', sanitize_text_field((isset($_POST['field_id']) ? 'yes' : 'no')));

            $product->update_meta_data('_shelf_number_id', sanitize_text_field((isset($_POST['_shelf_number_id']) ? $_POST['_shelf_number_id'] : '')));

            update_post_meta( $post_id, "_shelf_number_id", sanitize_text_field((isset($_POST['_shelf_number_id']) ? $_POST['_shelf_number_id'] : '')));

            $product->save();
        }
    }
}
