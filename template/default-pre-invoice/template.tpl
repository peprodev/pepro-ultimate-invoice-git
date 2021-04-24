<body dir="rtl">
  <div class="page body">
    <div if="watermark" style="opacity: {{{watermark_opacity_10}}};filter: alpha(opacity={{{watermark_opacity}}});" data-opacity="{{{watermark_opacity}}}" class="watermark"></div>
    <table class="header-table" style="width: 100%; margin: 0;">
      <tr><td style="width: 1.8cm;"></td><td></td><td style="width: 4.5cm !important;"></td></tr>
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
                <td style="width: 7cm"><span class="label">خریدار:</span> {{{customer_fullname}}}</td>
                <td style="width: 5cm"><span class="label">شرکت:</span> {{{customer_company}}}</td>
                <td colspan="2" if="show_user_uin"><span class="label">شماره‌ اقتصادی/کدملی:</span><span class='autodir'>{{{customer_uin}}}</span></td>
              </tr>
              <tr>
                <td style="width: 7cm" if="show_customer_phone">
                  <span class="label">شماره تماس:</span> <span class='autodir'>{{{customer_phone}}}</span>
                </td>
                <td if="show_customer_address">
                  <span class="label">کد پستی:</span> <span class='autodir'>{{{customer_postcode}}}</span>
                </td>
                <td  colspan="2" if="show_customer_email">
                  <span class="label">ایمیل:</span> <span class='autodir'>{{{customer_email}}}</span>
                </td>
              </tr>
              <tr if="show_customer_address">
                <td colspan="4">
                  <span class="label">نشانی:</span> {{{customer_address}}}</td>
                </tr>
            </table>
          </div>
        </td>
        <td if="show_shipping_ref_id" style="padding: 0 0 4px; height:2.5cm;">
          <div class="grow bordered" style="padding: 2mm 5mm;">
            <div class="flex" style="flex-direction: column;text-align: center">
              <div class="font-small">شماره پیش فاکتور</div>
              <div class="flex-grow font-medium">
                <img alt='{{{invoice_id_en}}}' class="barcode" style="width: 100%;height: auto;" src='{{{invoice_barcode}}}'/>{{{invoice_id_en}}}
              </div>
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
                <td if="show_order_date" style="line-height: 2;"><span class="label">تاریخ خرید: </span><span class="date_digit">{{{order_date_created}}}</span></td>
                <td if="show_paid_date" style="line-height: 2;"><span class="label">تاریخ تسویه: </span><span class="date_digit">{{{order_date_paid}}}</span></td>
                <td if="show_payment_method" style="line-height: 2;"><span class="label">روش پرداخت: </span><span class="date_digit">{{{order_payment_method}}}</span></td>
                <td if="show_transaction_ref_id" style="line-height: 2;"><span class="label">رسید پرداخت: </span><span class="date_digit">{{{order_transaction_ref_id}}}</span></td>
              </tr>
              <tr>
                <td if="show_order_status" style="line-height: 2;"><span class="label">وضعیت سفارش: </span><span class="date_digit">{{{order_status}}}</span></td>
                <td if="show_purchase_complete_date" style="line-height: 2;"><span class="label">تاریخ تکمیل سفارش: </span><span class="date_digit">{{{order_date_completed}}}</span></td>
                <td if="show_shipping_date" style="line-height: 2;"><span class="label">تاریخ ارسال مرسوله: </span><span class="date_digit">{{{order_date_shipped}}}</span></td>
                <td if="show_shipping_method" style="line-height: 2;"><span class="label">روش ارسال مرسوله: </span><span class="date_digit">{{{order_shipping_method}}}</span></td>
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
            <th class="show_product_n" style="width: 1.8cm;">ردیف<div if="watermark" style="opacity: {{{watermark_opacity_10}}};filter: alpha(opacity={{{watermark_opacity}}});" data-opacity="{{{watermark_opacity}}}" class="watermark_print"></div></th>
            <th class="show_product_image" if="show_product_image" style="width: 3cm;">تصویر</th>
            <th class="show_product_sku" if="show_product_sku">کد کالا</th>
            <th class="show_product_title_description" colspan="{{{product_description_colspan}}}">شرح کالا</th>
            <th class="show_product_qty">تعداد</th>
            <th class="show_product_weight" if="show_product_weight">وزن</th>
            <th class="show_product_dimensions" if="show_product_dimensions">ابعاد</th>
            <th class="show_product_base_price" style="width: 2.3cm">مبلغ واحد</th>
            <th class="show_discount_precent" if="show_discount_precent" style="width: 2.3cm">درصد تخفیف</th>
            <th class="show_product_tax" if="show_product_tax" style="width: 2.3cm">مالیات</th>
            <th class="show_product_total_price" colspan="{{{product_nettotal_colspan}}}">جمع کل</th>
        </tr>
      </thead>
      <tbody>
        {{{invoice_products_list}}}
      </tbody>
      <tfoot>
        <tr if="show_order_total">
          <td colspan="{{{invoice_final_prices_pre_colspan}}}">جمع کل</td>
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
        <tr if="show_custom_footer">
          <td colspan="{{{invoice_final_row_colspan}}}">{{{invoices_footer}}}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</body>
