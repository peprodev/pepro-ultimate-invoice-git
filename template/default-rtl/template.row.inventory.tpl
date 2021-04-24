<tr if="show_order_items" class="{{{extra_classes}}}">
  <td class="show_product_n"><span class="nn">{{{n}}}</span></td>
  <td class="show_product_image" if="show_product_image_inventory">{{{img}}}</td>
  <td class="show_product_sku" if="show_product_sku_inventory">{{{sku}}}</td>
  <td class="show_shelf_number_id" if="show_shelf_number_id">{{{shelf_number_id}}}</td>
  <td class="show_product_title" colspan="2">{{{title}}}</td>
  <td class="show_product_base_price" if="show_inventory_price">{{{base_price}}}</td>
  <td class="show_product_weight single_weight" if="show_product_weight_in_inventory" width="80px"><span class="single_weight">{{{weight}}}</span></td>
  <td class="show_product_weight total_weight" if="show_product_total_weight_in_inventory" width="80px"><span class="totalweight">{{{total_weight}}}</span></td>
  <td class="show_product_dimensions" if="show_product_dimensions_in_inventory" width="120px">{{{dimension}}}</td>
  <td class="show_product_qty" if="show_product_quantity_in_inventory" >{{{qty}}}</td>
  <td class="show_product_description" if="show_product_note_in_inventory" colspan="2">{{{description}}}</span></td>
</tr>
