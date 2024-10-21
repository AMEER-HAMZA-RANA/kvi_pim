<div class="modal fade" id="productAddModel" tabindex="-1" role="dialog" aria-labelledby="productAddModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productAddModalLongTitle">Add Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	 	<div id="add-product-alert" class="alert alert-primary alert-danger hidden" role="alert"></div>
        <div class="form-group">
            <label for="product_name">Product name</label>
            <input type='hidden' id="prod_detail_url" value="<?php echo home_url('/'. $sm->current_brand->slug.'/products/view/');?>" >
            
            <input type='hidden' id="current_brand_id" value="<?php echo $sm->current_brand->brand_id;?>" >

            <input type="text" class="form-control" id="product_name" placeholder="Product Name">
        </div>
        <div class="form-group">
            <label for="product_sku">Product SKU</label>
            <input type="text" class="form-control" id="product_sku"  placeholder="Product SKU">
        </div>
        <div class="form-group">
            <label for="product_status">Is Active</label>
            <select class="custom-select" id="product_status" required>
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="form-group">
        <div class="alert alert-success" style="display:none;" id="new-product-status"></div>
        </div>
      </div>
      <div class="modal-footer">
       
        <button type="button" class="btn btn-black btn-cancel-add-product" data-dismiss="modal">Cancel</button>
        <button type="button" id="add-product-front" class="btn btn-black">Add Product</button>
      </div>
    </div>
  </div>
</div>
