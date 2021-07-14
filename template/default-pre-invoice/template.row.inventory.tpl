<!--
@Author: Amirhosseinhpv
@Date:   2020/10/20 22:23:23
@Email:  its@hpv.im
@Last modified by:   Amirhosseinhpv
@Last modified time: 2021/07/14 17:36:06
@License: GPLv2
@Copyright: Copyright © Amirhosseinhpv (https://hpv.im), all rights reserved.
-->


<tr if="show_order_items" class="{{{extra_classes}}}">
  <td class="show_product_n"><span class="nn">{{{n}}}</span></td>
  <td class="show_product_image_inventory" if="show_product_image_inventory">{{{img}}}</td>
  <td class="show_product_sku_inventory" if="show_product_sku_inventory">{{{sku}}}</td>
  <td class="show_shelf_number_id" if="show_shelf_number_id">{{{shelf_number_id}}}</td>
  <td class="show_product_title" colspan="2">{{{title}}}</td>
  <td class="show_product_base_price show_inventory_price" if="show_inventory_price">{{{base_price}}}</td>
  <td class="show_product_weight show_product_weight_in_inventory single_weight" if="show_product_weight_in_inventory" width="80px"><span class="single_weight">{{{weight}}}</span></td>
  <td class="show_product_weight show_product_total_weight_in_inventory total_weight" if="show_product_total_weight_in_inventory" width="80px"><span class="totalweight">{{{total_weight}}}</span></td>
  <td class="show_product_dimensions show_product_dimensions_in_inventory" if="show_product_dimensions_in_inventory" width="120px">{{{dimension}}}</td>
  <td class="show_product_qty show_product_quantity_in_inventory" if="show_product_quantity_in_inventory" >{{{qty}}}</td>
  <td class="show_product_description show_product_note_in_inventory" if="show_product_note_in_inventory" colspan="2">{{{description}}}</span></td>
</tr>
