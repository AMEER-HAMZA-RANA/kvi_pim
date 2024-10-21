<?php
/**
 * Template Name: NEW MAM Asset Detail
 *
 *
 * @package arrow
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$stm = SettingsManager::GI();
$sm = StateManager::GI();

$current_asset_id = get_query_var('asset_id');

$current_asset_data = $stm->get_media_record($current_asset_id);

if(!$current_asset_data) $stm->item_not_found();

$current_asset_assignment = $stm->get_full_row_from_table('pim_media_assignments', $current_asset_data->media_assignment_id);

// only for image resizer
if( !empty(trim($current_asset_data->resolution_x)) ) {

	$sm->rp->load_JS(array("media_id" => $current_asset_id, "asset_name" => 'Package Front', 'brand_name' => $stm->get_its_brand($current_asset_data->brand_id)));

}

// $stm->dump($stm->get_current_brand_name());

get_header("mam-brands"); //change it

?>

<style>
.asset-container {
  max-width: 85%;
  margin: auto;
  margin-bottom: 40px;
}

.top-title {
  background-color: #F6494E;
  padding: 15px 10px;
  text-align: center;
  margin-bottom: 20px;
}

.top-title h1 {
  margin: 0;
  font-size: 24px;
  color: #fff;
}

.asset-details {
  /* display: flex; */
  /* margin: 0px auto 30px 0; */
  /* padding: 20px 0; */
  /* gap: 20px; */
}

.asset-image {
  /* width: 40%; */
  /* text-align: center; */
}

.asset-image img {
  max-width: 100%;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  min-width: 200px;
  padding-top: 20px;
  padding-bottom: 20px;
  padding-right: 10px;
  padding-left: 10px;
}

.asset-info {
  /* width: 60%; */
}

.asset-info table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
}

.asset-info th {
  padding: 2px 5px;
  border: 1px solid #fff;
  text-align: left;
}

.asset-info td {
  padding: 2px 5px;
  border-top: 1px solid #000;
  border-bottom: 1px solid #000;
  text-align: left;
}

table td {
  /* background-color: #e0e0e0; */
  background-color: #F8F8F8;
}

table input,
table select,
table textarea,
.asset-info th,
.asset-info td {
  font-weight: normal !important;
}

.asset-info th {
  background-color: #F6494E;
  color: #fff;
  width: 35%;
  font-size: 14px;
}

.asset-info td input,
#media_status {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #fff;
  color: #333;
  font-size: 14px;
}

#media_status {
  padding: 0;
}



.asset-info td input:disabled,
.asset-info td textarea:disabled,
#media_status:disabled {
  /* background-color: #e0e0e0; */
  background-color: #F8F8F8;
  border: none;
}

.actions {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  width: 100%;
  align-items: center;
}

.actions button {
  /* padding: 10px 20px;
  margin-left: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px; */
}

.btn-edit {
  /* background-color: #4CAF50;
  color: #fff; */
}

.btn-save {
  background-color: #2196F3;
  color: #fff;
}

.btn-delete {
  background-color: #F44336;
  color: #fff;
}

.source-download {
  /* background-color: gray !important;
  color: white !important;
  display: block;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  background: lightblue;
  max-width: fit-content;
  text-decoration: none !important; */

}

textarea#note {
  font-size: 14px;
  font-weight: 700;
}

.hidden {
  display: none !important;
}

.loader {
  width: 25px !important;
  height: 25px !important;
  padding: 8px;
  margin-left: auto !important;
  inset: 0 !important;
  top: -10px !important;
  position: absolute;
  aspect-ratio: 1;
  border-radius: 50%;
  margin-left: auto;
  background: #25b09b;
  --_m:
    conic-gradient(#0000 10%, #000),
    linear-gradient(#000 0 0) content-box;
  -webkit-mask: var(--_m);
  mask: var(--_m);
  -webkit-mask-composite: source-out;
  mask-composite: subtract;
  animation: l3 0.5s infinite linear;
}

@keyframes l3 {
  to {
    transform: rotate(1turn)
  }

}

.loader_and_buttons {
  position: relative;
  display: flex;
  justify-content: end;
}

.loader-parent {
  position: absolute;

}

.asset-heading-title {
  color: #707070;
  font-weight: 700;
  margin-bottom: 35px;
  font-size: 22px;
}






#source_download_link {
  background-color: #839cc4;
}

#source_download_link {
  min-width: unset;
  border: 0px;
  padding: 0 10px;
  height: unset;
  color: unset;
}

#source_download_link,
.btn-edit,
.btn-save,
.btn-terminate {
  height: 30px;
  line-height: 26px;
  text-decoration: none;
  color: #fff;
  border: 1px solid;
  display: inline-block;
  min-width: 159px;
  text-align: center;
  border: 2px solid #707070;
  font-size: 14px;
  -webkit-box-shadow: 2px 2px 10px 2px rgba(0, 0, 0, .1019607843);
  box-shadow: 2px 2px 10px 2px rgba(0, 0, 0, .1019607843);
}

.btn-edit {
  background: #707070;
}

.btn-terminate {
  color: black;
}
</style>

<div class="asset-container">
  <!-- <div class="top-title">

  </div> -->






  <div class="asset-details row mb-5">
    <div class="col-md-6">
      <div class="asset-image text-center">
        <h1 class="asset-heading-title mt-5"><?php echo htmlspecialchars($current_asset_data->title); ?></h1>
        <img id="asset_thumb img-fluid" src="<?= htmlspecialchars($current_asset_data->thumb_url); ?>"
          onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png';"
          alt="Asset Thumbnail">
      </div>
    </div>
    <div class="col-md-6">
      <div class="asset-info">
        <div class="loader_and_buttons">
          <div class='loader-parent' style='height: 41px !important;'>
            <div class='loader hidden'></div>
          </div>
          <div class="actions">

            <a class="source-download" id="source_download_link"
              href="<?php echo htmlspecialchars($current_asset_data->source_url); ?>" target="_blank">Download</a>
            <button class="btn-edit" onclick="enableEditing()">
              <i class="fa fa-edit" aria-hidden="true"></i>
              Edit</button>
            <button class="btn-terminate hidden" onclick="terminateChanges()">Cancel</button>
            <button class="btn-save hidden" onclick="saveChanges()">Save</button>
            <!-- <button class="btn-delete" onclick="deleteAsset()">Delete Media</button> -->
          </div>
        </div>
        <table>
          <tr>
            <th>Asset ID</th>
            <td><input type="text" value="<?php echo htmlspecialchars($current_asset_data->id); ?>" disabled></td>
          </tr>
          <tr>
            <th>Asset Title</th>
            <td><input name="asset_title" data-editable type="text"
                value="<?php echo htmlspecialchars($current_asset_data->title); ?>" disabled></td>
          </tr>
          <tr>
            <th>Brand</th>
            <td><input type="text" value="<?php echo $stm->get_its_brand($current_asset_data->brand_id); ?>" disabled>
            </td>
          </tr>
          <tr>
            <th>Product Id</th>
            <td><input type="text" value="<?php echo htmlspecialchars($current_asset_data->associated_item_id); ?>"
                disabled></td>
          </tr>
          <!-- <tr>
		<th>Media Assignment Id</th>
		<td><input type="text" value="<?php echo htmlspecialchars($current_asset_data->media_assignment_id); ?>"
				disabled></td>
	</tr> -->
          <tr>
            <th>Media Assignment Name</th>
            <td><input type="text"
                value="<?php echo $stm->get_assignment_name_of_asset($current_asset_data->media_assignment_id); ?>"
                disabled></td>
          </tr>
          <tr>
            <th>Media Assignment Type</th>
            <td><input type="text"
                value="<?php echo $stm->get_assignment_type_of_asset($current_asset_data->media_assignment_id); ?>"
                disabled></td>
          </tr>
          <tr>
            <th>Uploaded by</th>
            <td><input type="text" value="<?php echo $stm->get_users_name( $current_asset_data->uploader ); ?>"
                disabled>
              <br>
            </td>
          </tr>
          <tr>
            <th>Upload Time</th>
            <td><input type="text" value="<?php echo htmlspecialchars($current_asset_data->created_at); ?>" disabled>
            </td>
          </tr>
          <tr>
            <th>Source File Size</th>
            <td><input type="text"
                value="<?php echo ($current_asset_data->source_size ? $current_asset_data->source_size . ' kb' : '') ?> "
                disabled></td>
          </tr>
          <tr>
            <th>Image Dimensions</th>

            <?php if( !empty(trim($current_asset_data->resolution_x)) ): ?>

            <td>
              <input type="text"
                value="<?php echo htmlspecialchars($current_asset_data->resolution_x) . ' x ' . htmlspecialchars($current_asset_data->resolution_y); ?>"
                disabled>
            </td>

            <?php else: ?>

            <td><input type="text" value="" disabled></td>

            <?php endif; ?>

          </tr>
          <tr>
            <th>Media(Source File) Type</th>
            <td><input type="text" value="<?php echo htmlspecialchars($current_asset_data->file_extension); ?>"
                disabled>
            </td>
          </tr>
          <!-- <tr>
		<th>Status</th>
		 <td>
			<select name="asset_status" data-editable id="media_status"
				value="<?php echo htmlspecialchars($current_asset_data->media_status); ?>" disabled>
				<option value="" disabled>Select Media Status</option>
				<option value="active" <?php echo $current_asset_data->media_status === 'active' ? 'selected' : '' ?>>
					Active</option>
				<option value="inactive" <?php echo $current_asset_data->media_status === 'inactive' ? 'selected' : '' ?>>
					Inactive</option>
				<option value="approved" <?php echo $current_asset_data->media_status === 'approved' ? 'selected' : '' ?>>
					Approved</option>
				<option value="unapproved"
					<?php echo $current_asset_data->media_status === 'unapproved' ? 'selected' : '' ?>>Un Approved</option>
				<option value="expired" <?php echo $current_asset_data->media_status === 'expired' ? 'selected' : '' ?>>
					Expired
				</option>
			</select>
		</td>
	</tr> -->
          <!-- <tr>
		<th>Notes</th>
		<td>
			<textarea data-editable id="note" class="form-control" style="width: 100%;" rows="4" name="asset_note"
				value="" disabled></textarea>
		</td>
	</tr> -->
        </table>
      </div>
    </div>
  </div>

  <!-- <div class="asset-details">



  </div> -->
  <style>
  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel {
    background-color: #000;
    color: #fff;
    padding: 5px 10px;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .text-section p {
    font-size: 14px;
    max-width: 90%;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container {
    max-width: 85%;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .panel-body .form-inline {
    -webkit-box-align: start;
    -webkit-align-items: flex-start;
    -moz-box-align: start;
    -ms-flex-align: start;
    align-items: flex-start;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .panel-body .form-inline .w-40 {
    width: 40%;
    display: block;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container select {
    height: 33px;
    margin-bottom: 5px;
    font-size: 13px;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container select {
    height: 33px;
    margin-bottom: 5px;
    font-size: 13px;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .panel-body .presets-btns {
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -moz-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container .save-preset-btn {
    background-color: #839CC4;
    border-color: #839CC4;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container .btn {
    height: 35px;
    color: white;
    font-size: 17px;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container .download {
    background-color: #A7C483;
    border-color: #A7C483;
  }

  .asset-detail-wrapper .asset-detail-box .img-sizes-panel .panel .form-section .form-container input {
    height: 33px;
    margin-bottom: 7px;
    font-size: 13px;
  }

  .asset-detail-wrapper .asset-detail-box {
    padding: 0 8px;
  }





  /*
<!-- ************* -->
<!-- RELATED ITEMS -->
<!-- ************* --> */

  .mam-container {
    padding: 20px;
    max-width: 1000px;
    margin: auto;
    max-width: 1200px;
    /* min-width: 1200px; */
    margin-bottom: 100px;
  }

  .media-items-count {
    text-align: right;
    margin-bottom: 10px;
  }

  .media-items-grid {
    display: flex;
    flex-wrap: wrap;
    column-gap: 20px;
    row-gap: 35px;
    margin: auto;
    justify-content: center;
  }

  .media-item {
    flex: 1 1 30%;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    margin: 0;
    max-width: 145px;
    border: 1px solid black;
    position: relative;
  }

  .media-item a {
    display: flex;
    max-width: 155px;
    margin: auto !important;
    position: relative;
    min-height: 120px;
  }

  .media-item img {
    /* max-width: 100%;
  height: auto; */
    height: 151px;
    width: 151px;
    object-fit: cover;
    margin: auto;
  }

  .product-id {
    text-align: center;
    /* margin-top: 5px; */
    font-weight: bold;
    position: absolute;
    left: 50%;
    bottom: -20px;
    transform: translateX(-50%);
    width: 100%;
  }


  .hidden {
    display: none;
  }


  /* table  */
  .brands table tbody tr th {
    background-color: #707070;
    color: #fff;
    font-weight: 400;
    font-size: 14px;
    width: 35%;
  }

  .brands table tbody tr td {
    background-color: #f8f8f8;
  }

  .brands .table>:not(caption)>*>* {
    padding: .5rem .5rem;
    border-bottom-width: 1px;
  }

  .dlt-btn {
    color: #fff;
    background-color: #f40e14;
    display: inline-block;
    max-width: 201px;
    height: 25px;
    line-height: 24px;
    text-align: center;
    display: block;
    margin: auto;

    outline: none;
    border: none;
    width: 200px;
  }


  /* FAV */
  .favorite_selection {
    position: absolute;
    bottom: 7px;
    right: 7px;
    -webkit-transition: all .5s ease;
    -o-transition: all .5s ease;
    -moz-transition: all .5s ease;
    transition: all .5s ease;
    background-color: #839cc4;
    color: #fff;
    padding: 2px 10px;
    border-radius: 3px;
    cursor: pointer;
  }

  .favorite_selection .favorite-icon {
    background-color: #000;
    width: 19px;
    height: 19px;
    line-height: 13px;
    text-align: center;
    border-radius: 100%;
  }

  .favorite_selection .favorite-icon .fa {
    font-size: 12px;
    position: absolute;
  }

  .fa {
    display: inline-block;
    font: normal normal normal 14px / 1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    position: absolute;
    top: 26%;
    right: 35%;
  }

  .fa-heart:before {
    content: "\f004";
  }

  .favorite_selection i:hover {
    color: #da1f19;
    background-color: #fff;

  }

  .favorite_selection i:hover {
    color: #ff6319;
  }

  .favorite_selection:hover .favorite-icon {
    color: #ff6319;
    background-color: #fff;

  }

  .favorite_selection.active .favorite-icon .fa {
    color: #da1f19;
  }

  .favorite_selection.active .favorite-icon {
    background-color: #fff;
  }

  .media-item:has(.favorite_selection.active) {
    border: 2px solid red;
  }
  </style>
  <div class="asset-detail-wrapper row">

    <?php if($current_asset_assignment->file_type === 'image' && !empty(trim($current_asset_data->resolution_x)) ): ?>
    <!-- Image Resizer -->
    <div class="asset-detail-box col-md-6 border-0">
      <div class="img-control w-100">
        <div class="img-sizes-panel">
          <div class="presets-saver panel">
            <div class="panel-head">

            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-6 text-section">
                  <h3 class="text-white">Canvas Size</h3>
                  <p class="text-white">Create variants of this image to your required DPI and size. You can SAVE that
                    variant as a PRESET or DOWNLOAD specifically for 1 time use. Note: Variant images are not saved
                    within
                    PIM</p>
                </div>
                <div class="col-md-6 form-section">
                  <div class="form-container">
                    <div class="form-inline">
                      <div class="w-40">
                      </div>
                      <div class="w-20">
                        <span>DPI</span>
                      </div>
                      <div class="w-40">
                        <select name="txt-dpi" id="txt-dpi" class="form-control" style="width: 100%;">
                          <option value="300" selected="">300</option>
                          <option value="150">150</option>
                          <option value="96">96</option>
                          <option value="72">72</option>
                        </select>
                      </div>
                    </div>
                    <select name="presets" id="presets" class="form-control ">
                      <option value="978752" data-aspect-ratio="1" data-width="6000" data-height="6000" data-dpi="300">
                        Original 6000 x 6000</option>
                      <option value="794845" data-aspect-ratio="1" data-width="1000" data-height="1000" data-dpi="96">
                        Example 1000 x 1000</option>
                      <option value="51203" data-aspect-ratio="1" data-width="20" data-height="20" data-dpi="300">
                        twitter 20
                        x 20</option>
                      <option value="1036" data-aspect-ratio="1.914" data-width="1200" data-height="627" data-dpi="72">
                        Facebook Link 1200 x 627</option>
                      <option value="1035" data-aspect-ratio="1" data-width="1200" data-height="1200" data-dpi="72">
                        Facebook
                        Image 1200 x 1200</option>
                      <option value="1034" data-aspect-ratio="1" data-width="6000" data-height="6000" data-dpi="72">
                        Default
                        6000 x 6000</option>
                      <option value="-1" data-aspect-ratio="1" data-width="100" data-height="100" data-dpi="300">Custom
                      </option>
                    </select>
                    <div class="form-inline">
                      <div class="w-50 pr-1">
                        <input type="number" placeholder="Px" id="txt-width" class="w-100" value="6000">
                        <label>Width</label>
                      </div>
                      <!-- <div class="w-20">
														<button id="btn-lock"><i class="fa fa-lock" aria-hidden="true" ></i></button>
													</div> -->
                      <div class="w-50 pl-1">
                        <input type="number" placeholder="Px" id="txt-height" class="w-100" value="6000">
                        <label>Height</label>
                      </div>
                    </div>
                    <div class="presets-btns mb-3 mt-3">
                      <button id="pre-preset-save-btn" class="btn btn-info mb-2 save-preset-btn">Save as Preset</button>
                      <button id="btn-download" class="btn btn-success mb-2 download">Download</button>
                    </div>
                    <div id="preset-save-div" class="preset-name text-right" style="display: none;">
                      <input class="form-control" type="text" value="" placeholder="Name of Preset"
                        id="txt-preset-name">
                      <button id="preset-save-btn" class="btn btn-lg btn-success my-2 mt-3">Save</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>


    <!-- Media Assignment Table -->
    <div
      class="col-md-6 assets-sidebar <?php if($current_asset_assignment->file_type !== 'image' || empty(trim($current_asset_data->resolution_x)) ) { echo 'offset-md-6'; } ?> ">
      <div class="table-responsive brands mb-4">
        <section>
          <table style="width:100%" class="table" id="">

            <tbody>
              <tr>
                <th>Brand
                </th>
                <td><?= $stm->get_its_brand($current_asset_data->brand_id) ?></td>
              </tr>
              <tr>
                <th>Uploaded File Type</th>
                <td><?= $current_asset_assignment->file_type ?></td>
              </tr>
              <tr>
                <th>Media Assignment</th>
                <td><?= $current_asset_assignment->assignment_name ?></td>
              </tr>
              <tr>
                <th>Media Status</th>
                <td>
                  <select name="asset_status" data-editable id="media_status"
                    value="<?php echo htmlspecialchars($current_asset_data->media_status); ?>" disabled>
                    <option value="" disabled>Select Media Status</option>
                    <option value="active"
                      <?php echo $current_asset_data->media_status === 'active' ? 'selected' : '' ?>>
                      Active</option>
                    <option value="inactive"
                      <?php echo $current_asset_data->media_status === 'inactive' ? 'selected' : '' ?>>
                      Inactive</option>
                    <option value="approved"
                      <?php echo $current_asset_data->media_status === 'approved' ? 'selected' : '' ?>>
                      Approved</option>
                    <option value="unapproved"
                      <?php echo $current_asset_data->media_status === 'unapproved' ? 'selected' : '' ?>>Un Approved
                    </option>
                    <option value="expired"
                      <?php echo $current_asset_data->media_status === 'expired' ? 'selected' : '' ?>>
                      Expired
                    </option>
                  </select>
                </td>
              </tr>
              <tr>
                <th>Notes</th>
                <td>
                  <textarea data-editable id="note" class="form-control" style="width: 100%;" rows="4" name="asset_note"
                    value="" disabled></textarea>
                </td>
              </tr>
              <tr>
                <th>Delete</th>
                <td>
                  <button class="dlt-btn text-light btn-delete1" id="delete-btn" onclick="deleteAsset()">Delete
                    Media</button>
                  <!-- <a class="dlt-btn text-light" id="delete-btn">Delete Media</a> -->
                  <!-- <p>Note: Deleting this image will remove it from PIM. Before deleting please consider changing the IMAGE STATUS</p> -->
                </td>
              </tr>
            </tbody>

          </table>
        </section>
      </div>
    </div>

  </div>

</div>

<script>
document.querySelector('#note').value = "<?= htmlspecialchars($current_asset_data->media_note); ?>";


const buttonsContainer = document.querySelector('.actions')
const loader = document.querySelector('.loader')

const editBtn = document.querySelector('.btn-edit')
const terminateBtn = document.querySelector('.btn-terminate')
const saveBtn = document.querySelector('.btn-save')

function enableEditing() {
  const inputs = document.querySelectorAll('[data-editable]');
  inputs.forEach(input => input.disabled = false);

  editBtn.classList.add('hidden')

  terminateBtn.classList.remove('hidden')
  saveBtn.classList.remove('hidden')

}

function saveChanges() {

  const inputs = document.querySelectorAll('[data-editable]');
  inputs.forEach(input => input.disabled = true);

  editBtn.classList.remove('hidden')

  terminateBtn.classList.add('hidden')
  saveBtn.classList.add('hidden')

  const title = document.querySelector('[name="asset_title"]').value
  const status = document.querySelector('[name="asset_status"]').value
  const note = document.querySelector('[name="asset_note"]').value

  if (isAllDataSame()) return

  updateAssetData(title, status, note)

}

function updateAssetData(title, status, note) {

  showLoader()

  fetch('<?= admin_url("admin-ajax.php") ?>', {
      method: 'POST',
      body: new URLSearchParams({
        id: <?= $current_asset_id ?>,
        title,
        status,
        note,
        action: 'update_asset_data',
        _wpnonce: '<?= wp_create_nonce('update_asset_data') ?>'
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.data.message)
      } else {
        alert(data.data.message)
      }
    })
    .finally(() => {
      hideLoader()
    })


}

function showLoader() {
  loader.classList.remove('hidden')
  buttonsContainer.classList.add('hidden')
}

function hideLoader() {
  loader.classList.add('hidden')
  buttonsContainer.classList.remove('hidden')
}

function terminateChanges() {

  const inputs = document.querySelectorAll('[data-editable]');
  inputs.forEach(input => input.disabled = true);

  editBtn.classList.remove('hidden')

  terminateBtn.classList.add('hidden')
  saveBtn.classList.add('hidden')

}

function deleteAsset() {
  if (confirm("Are you sure ?")) {

    const thisMediaId = '<?= $current_asset_id ?>'
    deleteThisMediaAsset(thisMediaId)
  }

  terminateChanges()


}

function deleteThisMediaAsset(id) {
  showLoader()

  fetch('<?= admin_url("admin-ajax.php") ?>', {
      method: 'POST',
      body: new URLSearchParams({
        media_id: id,
        action: 'delete_media',
        _wpnonce: '<?= wp_create_nonce('delete_media_nonce') ?>'
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.data.message)
        window.location.href = '<?= get_home_url() ?>' + '/arrow/mam/'
      } else {
        alert(data.data.message)
      }
    })
    .finally(() => {
      hideLoader()
    })
}


let previousData = [...document.querySelectorAll(
  '[data-editable]'
)].map(el => el.value)

function isAllDataSame() {

  let currentData = [...document.querySelectorAll(
    '[data-editable]'
  )].map(el => el.value)

  const result = currentData.every((val, i) => val.trim() === previousData[i].trim())

  previousData = [...currentData]

  return result
}
</script>






<!-- ------------- -->
<!-- RELATED ITEMS -->
<!-- ------------- -->


<div class="mam-container hidden">


  <!-- Media items display area -->
  <!-- <div class="media-items-grid" id="media-items-grid"> -->
  <!-- Media items will be loaded here via AJAX -->
  <!-- </div> -->
  <div class=" item-grid  ">
    <h2 class="my-5 text-center display-4 fw-bold ">Related Items</h2>
    <div id="media-items-grid" class="media-items-grid row flex-column flex-md-row justify-content-center"
      data-selection-mode="0" id="items_container">

    </div>

  </div>


</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
  function get_related_media_items() {
    fetch('<?= admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: new URLSearchParams({
          assignment_id: '<?= $current_asset_data->media_assignment_id ?>',
          prod_id: '<?= $current_asset_data->associated_item_id ?>',
          // media_type: mediaType, // Include media type in the request
          action: 'get_related_media_items',
          _wpnonce: '<?= wp_create_nonce('get_related_media_items'); ?>'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const mediaItemsGrid = document.getElementById('media-items-grid');

          // Clear the current grid
          mediaItemsGrid.innerHTML = '';

          if (data.data.media_items.length == 0) {
            mediaItemsGrid.innerHTML = '<p>No related items found.</p>'
            return
          }

          // Populate new media items
          data.data.media_items.forEach(function(item) {

            if (item.id != "<?= $current_asset_id ?>") {


              //               const mediaItemHtml = `
              //   <div class="media-item">
              //     <a href="/arrow/mam/view/${item.id}">
              //       <img src="${item.thumb_url}" alt="${item.title}" onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png';">
              //       <div class="product-id">${item.associated_item_id}</div>
              //     </a>
              //   </div>
              // `;

              const mediaItemHtml = `
  <div class="media-item mx-auto mx-md-0 px-0">
    <a href="/arrow/mam/view/${item.id}">
      <img src="${item.thumb_url}" alt="${item.title}" onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png';">
      <div class="product-id fs-5">${item.title.length > 21 ? item.title.substr(0, 19) + '...' : item.title}</div>
    </a>

		<div class="favorite_selection d-flex ${item.is_favorite ? 'active' : ''}" data-id="${item.id}">

												<div class="favorite-icon">
													<i class="fa fa-heart" aria-hidden="true"></i>
												</div>

											</div>

  </div>
`;

              mediaItemsGrid.innerHTML += mediaItemHtml;

            }

          });

        }
      })
      .catch(error => {
        console.error('Error:', error);
      })
      .finally(() => {
        document.querySelector('.mam-container').classList.remove('hidden')
        document.querySelector('.loader').classList.add('hidden')
      });
  }

  // Initial load
  get_related_media_items();


  document.addEventListener('click', e => {

    // fav
    if (e.target.closest('.favorite_selection')) {
      // const checkbox = e.target;
      // const itemId = checkbox.dataset.itemId;
      // const isChecked = checkbox.checked;
      const favMarkDiv = e.target.closest('.favorite_selection');
      const itemId = favMarkDiv.dataset.id;
      const isChecked = favMarkDiv.classList.contains('active');

      favMarkDiv.style.pointerEvents = "none"

      fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
          method: 'POST',
          body: new URLSearchParams({
            item_id: itemId,
            item_type: 'media',
            is_favorite: !isChecked ? 1 : 0,
            action: 'toggle_favorite_item',
            _wpnonce: '<?php echo wp_create_nonce('toggle_favorite_item'); ?>'
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log('Favorite status updated.');
            !isChecked ? favMarkDiv.classList.add('active') : favMarkDiv.classList.remove('active');
          } else {
            console.error('Failed to update favorite status.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
        })
        .finally(() => {
          favMarkDiv.style.pointerEvents = "auto"

        });;
    }


  })




});
</script>







<?php get_footer();
