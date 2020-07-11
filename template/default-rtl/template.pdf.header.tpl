<div class="page body">
  <table id="headertitles">
    <tr class="headtr">
      <td class="header-item-wrapper" style="padding: 0.3cm;"><img height="64px" src="{{{store_logo}}}" /></td>
      <td class="header-item-wrapper" style="width: 70%; font-weight: bold; font-size: 1.7rem; text-align: right; padding: 0.1cm .5cm; display: block !important;">{{{invoice_title}}}</td>
      <td class="header-item-wrapper" style="padding: 1rem;">
        <div>شماره فاکتور</div>
        <div class="flex-grow">
          <img alt='Barcode {{{invoice_id_en}}}' style="width: 4cm;height: 1.5cm;" src='https://barcode.tec-it.com/barcode.ashx?data={{{invoice_id_en}}}'/>
        </div>
      </td>
      <td class="header-item-wrapper" style="padding: 1rem;">
        <div>کد رهگیری مرسوله</div>
        <div class="flex-grow">
          <img alt='Barcode {{{invoice_track_id_en}}}' style="width: 4cm;height: 1.5cm;" src='https://barcode.tec-it.com/barcode.ashx?data={{{invoice_track_id_en}}}'/>
        </div>
      </td>
    </tr>
  </table>
</div>
