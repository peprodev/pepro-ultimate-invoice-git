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
                  <span class="label">فروشنده:</span> {{{store_name}}}
                </td>
                <td if="show_store_national_id" style="width: 5cm">
                  <span class="label">شناسه ملی:</span> <span class='autodir'>{{{store_national_id}}}</span></td>
                <td if="show_store_registration_number">
                  <span class="label">شماره ثبت:</span> <span class='autodir'>{{{store_registration_number}}}</span></td>
                <td if="show_store_economical_number">
                  <span class="label">شماره اقتصادی:</span> <span class='autodir'>{{{store_economical_number}}}</span></td>
              </tr>
              <tr>
                <td colspan="2"><span class="label">نشانی شرکت:</span> {{{store_address}}}</td>
                <td><span class="label">کدپستی:</span> <span class='autodir'>{{{store_postcode}}}</span></td>
                <td>
                  <span class="label">تلفن و فکس:</span> <span class='autodir'>{{{store_phone}}}</span></td>
              </tr>
            </table>
          </div>
        </td>
        <td if="show_invoices_id_barcode" style="width: 4.5cm;height: 2.5cm;padding: 0 0 4px;">
          <div class="bordered grow" style="padding: 2mm 5mm;">
            <div class="flex" style="flex-direction: column;text-align: center;">
              <div class="font-small">شماره فاکتور</div>
              <div class="flex-grow">
                <img alt='Barcode {{{invoice_id_en}}}' style="width: 100%;height: auto;" src='https://barcode.tec-it.com/barcode.ashx?data={{{invoice_id_en}}}'/>
                <!-- <svg class="barcode" jsbarcode-displayValue="true" jsbarcode-value="{{{invoice_id_en}}}" jsbarcode-displayValue="false"></svg> -->
              </div>
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
              <div class="font-small">بارکد رهگیری مرسوله</div>
              <div class="flex-grow font-medium">
                <img alt='Barcode {{{invoice_track_id_en}}}' style="width: 100%;height: auto;" src='https://barcode.tec-it.com/barcode.ashx?data={{{invoice_track_id_en}}}'/>
                <!-- <svg class="barcode" jsbarcode-displayValue="true" jsbarcode-value="{{{invoice_track_id_en}}}" jsbarcode-displayValue="false"></svg> -->
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
                <td if="show_shipping_date" style="line-height: 2;"><span class="label">وضعیت سفارش: </span><span class="date_digit">{{{order_status}}}</span></td>
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
            <th style="width: 1.8cm;">ردیف
              <div if="watermark" style="opacity: {{{watermark_opacity_10}}};filter: alpha(opacity={{{watermark_opacity}}});" data-opacity="{{{watermark_opacity}}}" class="watermark_print"></div>
            </th>
            <th if="show_product_image" style="width: 3cm;">تصویر</th>
            <th if="show_product_sku">کد کالا</th>
            <th colspan="4">شرح کالا</th>
            <th>تعداد</th>
            <th if="show_product_weight">وزن</th>
            <th if="show_product_dimensions">ابعاد</th>
            <th style="width: 2.3cm">مبلغ واحد</th>
            <th style="width: 2.3cm">تخفیف</th>
            <th style="width: 2.3cm">مالیات</th>
            <th style="width: 2.5cm">جمع کل</th>
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
        <tr if="show_signature" style="background: #fff">
          <td colspan="{{{invoice_final_row_colspan}}}" style="height: 2.5cm;vertical-align: top">
            <div class="flex">
              <div class="flex-grow">مهر و امضای فروشنده:<br>
                <img class="footer-img uk-align-center" alt="" style="width:150px" src="{{{signature}}}">
              </div>
              <div class="flex-grow">مهر و امضای خریدار:<br>
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
