<style>
.buttons-row .btn-style-1 {
  color: white;
  font-family: SANS-SERIF;
  text-transform: capitalize;
  border: 1px solid grey;
  font-weight: normal;
  /* margin-left: 19px; */
  min-width: 150px;
  text-align: center;
  text-decoration: none;
  padding: 10px;
  font-size: 14px;
  line-height: 1.2;
  background-color: #839CC4;
  margin-bottom: 0;

}

.buttons-row .bg-bulk {
  background-color: #A4A5A7;
}

.buttons-row .bg-asset {
  background-color: #A7C483
}


/* Items_grid.scss */

.buttons-row.items_grid_row {
  justify-content: right;
  margin-top: 15px;
  gap: 8px;
}

.mam-filters-index .holder {
  position: relative;
}

.mam-filters-index .holder:not(:last-of-type):after {
  content: "";
  background-color: #727272;
  width: 3px;
  height: 29px;
  position: absolute;
  top: 2px;
  right: 25px;
  margin: auto;
}
</style>

<div class="container">
  <div class="class-">
    <div class="dropdown-style">
      <div class="class">
        <div class="container">
          <div class="row">
            <div class="mam-filters-index">

              <div class="d-flex align-items-center justify-content-between">
                <?php
									$stm = SettingsManager::GI();
									$stm->setup_mam_filters();
								?>
              </div>

              <?php
								if(current_user_can( "photo_edit" ) || current_user_can( "shopvac_photo_edit" )){
									?>
              <div class="buttons-row items_grid_row">
                <input type="hidden" value="false" id="bult_edit_enabled">
                <a class="delete-bulk-assets btn-style-1 btn-black bg-bulk" href="javascript:;" role="button">Delete
                  Assets</a>
                <a class="save-bulk-to-queue btn-style-1 btn-black" href="javascript:;" role="button">Add to Queue</a>
                <a class="enable-bulk-to-queue btn-style-1 btn-black bg-bulk" href="javascript:;" role="button">Bulk
                  Select</a>
                <a class="add-media btn-style-1 btn-black bg-asset"
                  href="<?php  echo home_url('/').'kvi/mam/add';?>">Add Asset</a>
              </div>
              <?php
								}
								?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>