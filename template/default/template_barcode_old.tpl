<body dir="rtl">
  <div class="page body">
    <p style="text-align:center;">
      <a class="print-button" target="_blank" href="{{{home_url}}}?invoice-pdf={{{invoice_id_nm}}}">GET PDF</a>
      <a class="print-button" href="javascript:;" onclick="window.print();return false;">PRINT</a></p>
    <h1 style="text-align: center">{{{invoice_title}}}</h1>
    <table class="header-table" style="width: 100%; margin: 0;">
      <tr>
        <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle;padding-bottom: 4px;">
          <div class="header-item-wrapper">
            <div class="portait">{{{trnslt__seller}}}</div>
          </div>
        </td>
        <td style="padding: 0 4px 4px;height: 2.5cm;">
          <div class="bordered grow header-item-data">
            <table class="grow centered">
              <tr>
                <td style="width: 7cm">
                  <span class="label">فروشنده:</span>{{{store_name}}}</td>
                <td style="width: 5cm">
                  <span class="label">شناسه ملی:</span><span class='autodir'>{{{store_national_id}}}</span></td>
                <td>
                  <span class="label">شماره ثبت:</span><span class='autodir'>{{{store_registration_number}}}</span></td>
                <td>
                  <span class="label">شماره اقتصادی:</span><span class='autodir'>{{{store_economical_number}}}</span></td>
              </tr>
              <tr>
                <td colspan="2"><span class="label">نشانی شرکت:</span>{{{store_address}}}</td>
                <td><span class="label">کدپستی:</span><span class='autodir'>{{{store_postcode}}}</span></td>
                <td>
                  <span class="label">تلفن و فکس:</span><span class='autodir'>{{{store_phone}}}</span></td>
              </tr>
            </table>
          </div>
        </td>
        <td style="width: 4.5cm;height: 2.5cm;padding: 0 0 4px;">
          <div class="bordered grow" style="padding: 2mm 5mm;">
            <div class="flex" style="flex-direction: column;text-align: center;">
              <div class="font-small">شماره فاکتور: {{{invoice_id_en}}}</div>
              <div class="flex-grow">
                <img alt='Barcode {{{invoice_id_en}}}' style="width: 100%;height: auto;" src='https://barcode.tec-it.com/barcode.ashx?data={{{invoice_id_en}}}'/>
              </div>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle; padding: 0 0 4px">
          <div class="header-item-wrapper">
            <div class="portait" style="margin: 20px">{{{trnslt__buyer}}}</div>
          </div>
        </td>
        <td style="height: 2.5cm;vertical-align: middle; padding: 0 4px 4px">
          <div class="bordered header-item-data">
            <table style="height: 100%" class="centered">
              <tr style="display:none">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              <tr>
                <td style="width: 7cm"><span class="label">خریدار:</span>{{{customer_fullname}}}</td>
                <td style="width: 5cm"><span class="label">شرکت:</span>{{{customer_company}}}</td>
                <td colspan="2"><span class="label">شماره‌ اقتصادی/کدملی:</span><span class='autodir'>{{{customer_uin}}}</span></td>
              </tr>
              <tr>
                <td colspan="4">
                  <span class="label">نشانی:</span>{{{customer_address}}}</td>
              </tr>
              <tr>
                <td style="width: 7cm">
                  <span class="label">شماره تماس:</span><span class='autodir'>{{{customer_phone}}}</span></td>
                <td colspan="3">
                  <span class="label">کد پستی:</span><span class='autodir'>{{{customer_postcode}}}</span></td>
              </tr>
            </table>
          </div>
        </td>
        <td style="padding: 0 0 4px; height:2.5cm;">
          <div class="grow bordered" style="padding: 2mm 5mm;">
            <div class="flex" style="flex-direction: column;text-align: center">
              <div>کد مرسوله</div>
              <div class="flex-grow font-medium">
                <img alt='Barcode {{{invoice_track_id_en}}}' style="width: 100%;height: auto;" src='https://barcode.tec-it.com/barcode.ashx?data={{{invoice_track_id_en}}}'/>
              </div>
            </div>
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
        <tr>
            <th style="width: 1.8cm;">ردیف</th>
            <th style="width: 3cm;">تصویر</th>
            <th>کد کالا</th>
            <th colspan="4">شرح کالا</th>
            <th>تعداد</th>
            <th style="width: 2.3cm">مبلغ واحد</th>
            <th style="width: 2.3cm">تخفیف</th>
            <th style="width: 2.3cm">مالیات</th>
            <th style="width: 2.5cm">جمع کل</th>
        </tr>
      </thead>
      <tbody>{{{invoice_products_list}}}</tbody>
      <tfoot>
        <tr>
          <td colspan="8"></td>
          <td colspan="3" class="font-small">جمع کل</td>
          <td>
            <span class="ltr">{{{invoice_final_price}}}</span></td>
        </tr>
        <tr style="background: #fff">
          <td colspan="12" style="height: 2.5cm;vertical-align: top">
            <div class="flex">
              <div class="flex-grow">مهر و امضای فروشنده:<br>
                <img class="footer-img uk-align-center" alt="signature" style="width:150px" src="https://www.digikala.com/static/files/8c0a8cdc.png">
              </div>
              <div class="flex-grow">مهر و امضای خریدار:<br>
                <img class="footer-img uk-align-center" alt="signature" style="width:150px" src="https://www.digikala.com/static/files/8c0a8cdc.png">
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="12">{{{invoices_footer}}}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</body>
