<?php
$sm = StateManager::GI();
$product = $sm->products->current_product;
wp_enqueue_script("sell-sheet-modal");

?><!-- Modal -->
<div id="sellSheetsModal" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
	  </div>
	  <form action='<?php echo home_url('/'.$sm->get_current_brand_slug().'/sell-sheet/').$product->prod_id;?>/' method="POST" target="_blank">
      <div class="modal-body text-center">
	  	<p class="mb-5">You are generating a Sell Sheet for the <u><?php echo $product->product_name;?></u></p>

		<div>Would you like to include M1, D1, or D2 Pricing?</div>
		<fieldset class="mb-3">
			<div class="form-check form-check-inline mr-3">
				<input class="form-check-input" type="radio" name="show_pb_pricing" id="inlineRadio1" value="Yes" checked>
				<label class="form-check-label" for="inlineRadio1">Yes</label>
			</div>
			<div class="form-check form-check-inline ml-3">
				<input class="form-check-input" type="radio" name="show_pb_pricing" id="inlineRadio2" value="No">
				<label class="form-check-label" for="inlineRadio2">No</label>
			</div>
		</fieldset>

		<fieldset class="mb-4" id="pb_checkboxes">
			<div class="form-check form-check-inline mr-3">
				<input class="form-check-input" type="radio" id="inlineradio1"  name="pb_pricing" checked value="M1">
				<label class="form-check-label" for="inlineradio1">M1</label>
			</div>
			<div class="form-check form-check-inline mr-3 ml-3">
				<input class="form-check-input" type="radio" id="inlineradio2" name="pb_pricing"  value="D1">
				<label class="form-check-label" for="inlineradio2">D1</label>
			</div>
			<div class="form-check form-check-inline ml-3">
				<input class="form-check-input" type="radio" id="inlineradio3" name="pb_pricing"  value="D2">
				<label class="form-check-label" for="inlineradio3">D2</label>
			</div>
		</fieldset>

		<div>Would you like to include additional pricing value pairs?</div>
		<fieldset class="mb-4">
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="show_additional_pricing" id="inlineRadio3" value="Yes">
				<label class="form-check-label" for="inlineRadio3">Yes</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="show_additional_pricing" id="inlineRadio4" checked value="No">
				<label class="form-check-label" for="inlineRadio4">No</label>
			</div>
		</fieldset>
		<fieldset id="additional_fields" style="display: none;">
			<div class="form-row mb-2">
				<div class="col-3 offset-3">
					Title
				</div>
				<div class="col-3">
					Value
				</div>
			</div>
			<div class="form-row mb-2">
				<div class="col-3 offset-3">
					<input type="text" class="form-control" name="pr1_title">
				</div>
				<div class="col-3">
					<input type="text" class="form-control" name="pr1_value">
				</div>
			</div>
			<div class="form-row mb-2">
				<div class="col-3 offset-3">
					<input type="text" class="form-control" name="pr2_title">
				</div>
				<div class="col-3">
					<input type="text" class="form-control" name="pr2_value">
				</div>
			</div>
			<div class="form-row mb-2">
				<div class="col-3 offset-3">
					<input type="text" class="form-control" name="pr3_title">
				</div>
				<div class="col-3">
					<input type="text" class="form-control" name="pr3_value">
				</div>
			</div>
		</fieldset>

      </div>
      <div class="modal-footer">
	 	 <button type="submit" class="btn btn-black btn-style-2">Print</button>
	  </div>
	  </form>
    </div>

  </div>
</div>
