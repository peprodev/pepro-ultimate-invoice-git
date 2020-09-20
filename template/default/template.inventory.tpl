<body dir="rtl">
  <div class="bordered header-item-data">
    <table>
      <tr>
        <td><table style="height: 100%" class="centered">
          <tr>
            <td style="width: 7cm">
              <span class="label">Customer:</span> {{{customer_fullname}}}</td>
            <td style="width: 5cm">
              <span class="label">Company:</span> {{{customer_company}}}</td>
            <td colspan="2" if="show_user_uin">
              <span class="label">Customer UIN:</span> <span class='autodir'>{{{customer_uin}}}</span></td>
          </tr>
          <tr>
            <td style="width: 7cm" if="show_customer_phone">
              <span class="label">Customer Phone:</span> <span class='autodir'>{{{customer_phone}}}</span>
            </td>
            <td if="show_customer_address">
              <span class="label">Customer Postcode:</span> <span class='autodir'>{{{customer_postcode}}}</span>
            </td>
            <td colspan="2" if="show_customer_email">
              <span class="label">Customer Email:</span> <span class='autodir'>{{{customer_email}}}</span>
            </td>
          </tr>
          <tr if="show_customer_address">
            <td colspan="4">
              <span class="label">Customer Address:</span> {{{customer_address}}}</td>
          </tr>
        </table></td>
        <td><div class="grow bordered" style="padding: 2mm 5mm;">
          <div class="flex" style="flex-direction: column;text-align: center">
            <div class="font-small">Tracking No.</div>
            <img alt='{{{invoice_track_id_en}}}' style="width: 100%;height: auto;" src='{{{invoice_track_barcode}}}'/>
            {{{invoice_track_id_en}}}
          </div>
        </div></td>
      </tr>
    </table>


  </div>
  <div>
    <table class="content-table">
      <thead>
        <tr>
          <th style="width: 1.8cm;">No.</th>
          <th if="show_product_image_inventory" style="width: 2cm;">Image</th>
          <th if="show_product_sku_inventory">SKU</th>
          <th if="show_shelf_number_id">Shelf No.</th>
          <th style="width: 8cm;" colspan="2">Product</th>
          <th if="show_inventory_price" style="width: 2.3cm">Price</th>
          <th if="show_product_weight_in_inventory">Weight</th>
          <th if="show_product_total_weight_in_inventory">Total Weight</th>
          <th if="show_product_dimensions_in_inventory">Dimensions</th>
          <th if="show_product_quantity_in_inventory" >QTY</th>
          <th if="show_product_note_in_inventory" colspan="2">Description</th>
        </tr>
      </thead>
      <tbody>
        {{{invoice_products_list}}}
      </tbody>
      <tfoot>
        <tr>
          <td colspan="{{{invoice_final_prices_pre_colspan}}}"></td>
          <td if="show_product_total_weight_in_inventory">{{{invoice_total_weight}}}</td>
          <td if="show_product_dimensions_in_inventory"></td>
          <td if="show_product_quantity_in_inventory">{{{invoice_total_qty}}}</td>
        </tr>
        <tr if="show_inventory_note">
          <td colspan="105">
            <table class="transp">
              <tr>
                {{{invoice_notes}}}
              </tr>
            </table>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</body>
