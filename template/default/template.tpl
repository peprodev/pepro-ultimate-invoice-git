<body dir="rtl">
  <div class="page body">
    <h1 class="puiw_title"><img height="64px" src="{{{store_logo}}}" />  {{{invoice_title}}} <div if="show_qr_code_id" id="invoice_qrcode"></div></h1>
    <div if="watermark" style="opacity: {{{watermark_opacity_10}}};filter: alpha(opacity={{{watermark_opacity}}});" data-opacity="{{{watermark_opacity}}}" class="watermark"></div>
    <table class="header-table" style="width: 100%; margin: 0;">
      <tr><td style="width: 1.8cm;"></td><td></td><td style="width: 4.5cm !important;"></td></tr>
      <tr class="show_invoices_id_barcode_colspan">
        <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle;padding-bottom: 4px;">
          <div class="header-item-wrapper">
            <div class="portait">{{{trnslt__seller}}}</div>
          </div>
        </td>
        <td colspan="{{{show_invoices_id_barcode_colspan}}}" style="padding: 0 4px 4px;height: 2.5cm;">
          <div class="bordered grow header-item-data">
            <table class="grow centered">
              <tr>
                <td style="width: 7cm">
                  <span class="label">Seller:</span> {{{store_name}}}
                </td>
                <td if="show_store_national_id" style="width: 5cm">
                  <span class="label">National ID:</span> <span class='autodir'>{{{store_national_id}}}</span></td>
                <td if="show_store_registration_number">
                  <span class="label">Registration No.:</span> <span class='autodir'>{{{store_registration_number}}}</span></td>
                <td if="show_store_economical_number">
                  <span class="label">Economical No.:</span> <span class='autodir'>{{{store_economical_number}}}</span></td>
              </tr>
              <tr>
                <td colspan="2"><span class="label">Store Address:</span> {{{store_address}}}</td>
                <td><span class="label">Postcode:</span> <span class='autodir'> {{{store_postcode}}}</span></td>
                <td>
                  <span class="label">Phone:</span> <span class='autodir'> {{{store_phone}}}</span></td>
              </tr>
            </table>
          </div>
        </td>
        <td if="show_invoices_id_barcode" style="width: 4.5cm;height: 2.5cm;padding: 0 0 4px;">
          <div class="bordered grow" style="padding: 2mm 5mm;">
            <div class="flex" style="flex-direction: column;text-align: center;">
              <div class="font-small">Invoice No.</div>
              <img alt='{{{invoice_id_en}}}' class="barcode" style="width: 100%;height: auto;" src='{{{invoice_barcode}}}'/>
              {{{invoice_id_en}}}
            </div>
          </div>
        </td>
      </tr>
      <tr class="">
        <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle; padding: 0 0 4px">
          <div class="header-item-wrapper">
            <div class="portait" style="margin: 20px">{{{trnslt__buyer}}}</div>
          </div>
        </td>
        <td class='shipping_ref_id' colspan="{{{show_shipping_ref_id_colspan}}}" style="height: 2.5cm;vertical-align: middle; padding: 0 4px 4px">
          <div class="bordered header-item-data">
            <table style="height: 100%" class="centered">
              <tr style="display:none">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              <tr>
                <td style="width: 7cm"><span class="label">Customer:</span> {{{customer_fullname}}}</td>
                <td style="width: 5cm"><span class="label">Company:</span> {{{customer_company}}}</td>
                <td colspan="2" if="show_user_uin"><span class="label">Customer UIN:</span> <span class='autodir'>{{{customer_uin}}}</span></td>
              </tr>
              <tr>
                <td style="width: 7cm" if="show_customer_phone">
                  <span class="label">Customer Phone:</span> <span class='autodir'>{{{customer_phone}}}</span>
                </td>
                <td if="show_customer_address">
                  <span class="label">Customer Postcode:</span> <span class='autodir'>{{{customer_postcode}}}</span>
                </td>
                <td  colspan="2" if="show_customer_email">
                  <span class="label">Customer Email:</span> <span class='autodir'>{{{customer_email}}}</span>
                </td>
              </tr>
              <tr if="show_customer_address">
                <td colspan="4">
                  <span class="label">Customer Address:</span> {{{customer_address}}}</td>
                </tr>
            </table>
          </div>
        </td>
        <td if="show_shipping_ref_id" style="padding: 0 0 4px; height:2.5cm;">
          <div class="grow bordered" style="padding: 2mm 5mm;">
            <div class="flex" style="flex-direction: column;text-align: center">
              <div class="font-small">Tracking No.</div>
              <img alt='{{{invoice_track_id_en}}}' class="barcode" style="width: 100%;height: auto;" src='{{{invoice_track_barcode}}}'/>
              {{{invoice_track_id_en}}}
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle; padding: 0 0 4px">
          <div class="header-item-wrapper">
            <div class="portait">{{{trnslt__dates}}}</div>
          </div>
        </td>
        <td style="height: 2.5cm;vertical-align: middle; padding: 0 4px 4px 0" colspan="2">
          <div class="bordered header-item-data">
            <table class="centered" style="height:100%">
              <tr>
                <td if="show_order_date" style="line-height: 2;"><span class="label">Order Date: </span> <span class="date_digit">{{{order_date_created}}}</span></td>
                <td if="show_paid_date" style="line-height: 2;"><span class="label">Date Paid: </span> <span class="date_digit">{{{order_date_paid}}}</span></td>
                <td if="show_payment_method" style="line-height: 2;"><span class="label">Payment Method: </span> <span class="date_digit">{{{order_payment_method}}}</span></td>
                <td if="show_transaction_ref_id" style="line-height: 2;"><span class="label">Transaction Ref: </span> <span class="date_digit">{{{order_transaction_ref_id}}}</span></td>
              </tr>
              <tr>
                <td if="show_shipping_date" style="line-height: 2;"><span class="label">Order Status: </span> <span class="date_digit">{{{order_status}}}</span></td>
                <td if="show_purchase_complete_date" style="line-height: 2;"><span class="label">Date Completed: </span> <span class="date_digit">{{{order_date_completed}}}</span></td>
                <td if="show_shipping_date" style="line-height: 2;"><span class="label">Date Shipped: </span> <span class="date_digit">{{{order_date_shipped}}}</span></td>
                <td if="show_shipping_method" style="line-height: 2;"><span class="label">Shipping Method: </span> <span class="date_digit">{{{order_shipping_method}}}</span></td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
    </table>
    <table class="content-table">
      <thead>
        <tr style="display:none">
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
        <tr if="show_order_items">
            <th style="width: 1.8cm;">No.
              <div if="watermark" style="opacity: {{{watermark_opacity_10}}};filter: alpha(opacity={{{watermark_opacity}}});" data-opacity="{{{watermark_opacity}}}" class="watermark_print"></div>
            </th>
            <th if="show_product_image" style="width: 3cm;">Image</th>
            <th if="show_product_sku">SKU</th>
            <th colspan="4">Product / Description</th>
            <th>QTY</th>
            <th if="show_product_weight">Weight</th>
            <th if="show_product_dimensions">Dimensions</th>
            <th style="width: 2.3cm">Price</th>
            <th style="width: 2.3cm">Discount</th>
            <th style="width: 2.3cm">Tax</th>
            <th style="width: 2.5cm">Total</th>
        </tr>
      </thead>
      <tbody>
        {{{invoice_products_list}}}
      </tbody>
      <tfoot>
        <tr if="show_order_total">
          <td colspan="{{{invoice_final_prices_pre_colspan}}}">Totals</td>
          <td if="show_product_qty">{{{invoice_total_qty}}}</td>
          <td if="show_product_weight">{{{invoice_total_weight}}}</td>
          <td colspan="{{{invoice_final_prices_colspan}}}">{{{invoice_final_prices}}}</td>
        </tr>
        <tr if="show_order_note">
          <td colspan="{{{invoice_final_row_colspan}}}">
            <table class="transp">
              <tr>{{{invoice_notes}}}</tr>
            </table>
          </td>
        </tr>
        <tr if="show_signature" style="background: #fff">
          <td colspan="{{{invoice_final_row_colspan}}}" style="height: 2.5cm;vertical-align: top">
            <div class="flex">
              <div class="flex-grow">Shop Signature and Stamp<br>
                <img class="footer-img uk-align-center" alt="" style="width:150px" src="{{{signature}}}">
              </div>
              <div class="flex-grow">Customer Signature and Stamp<br>
                <img class="footer-img uk-align-center" alt="" style="width:150px" src="{{{customer_signature}}}">
              </div>
            </div>
          </td>
        </tr>
        <tr if="show_custom_footer">
          <td colspan="{{{invoice_final_row_colspan}}}">{{{invoices_footer}}}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</body>
