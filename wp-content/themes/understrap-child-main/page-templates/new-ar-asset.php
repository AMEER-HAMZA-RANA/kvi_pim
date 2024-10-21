<?php

/**
 * Template Name: MAM Asset - New
 *
 *
 * @package arrow
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

use Rakit\Validation\Validator;

$sm = StateManager::GI();
$stm = SettingsManager::GI();
$errors = array();

get_header("mam-brands");
$container = get_theme_mod('arrow_container_type');
wp_enqueue_script("vue-js");
wp_enqueue_script("select-2");
wp_enqueue_style("select-2-css");
wp_enqueue_script("exif-js");
wp_enqueue_script("ezdz-ar-init");

$skus = $sm->current_brand->get_skus();
// $media_types = $sm->mam->get_media_types();

$prods = $stm->get_all_rows_and_cols_from_table('pim_products');
$media_types = $stm->get_all_rows_and_cols_from_table('pim_media_groups');
$data = $stm->get_media_groups_and_assignments();
$media_structure = $stm->generate_media_groups_and_assignments_structure($data);

// $image_assignments = $sm->mam->get_image_assignments();
// $video_assignments = $sm->mam->get_video_assignments();
// $artwork_assignments = $sm->mam->get_artwork_assignments();
// $engineering_drawings = $sm->mam->get_drawings_assignments();
// $manual_assignments = $sm->mam->get_manuals_assignments();
$asset_statuses = $sm->mam->get_asset_statuses();
$cats = $sm->get_current_brands_product_categories();
$source_types = $sm->mam->get_source_types();
$default_media_type = "Images";
if(isset($_GET["m_type"]) && !empty($_GET["m_type"])){
	$default_media_type = filter_var($_GET["m_type"], FILTER_SANITIZE_STRING);
}
$default_media_assignment = "Other";
if(isset($_GET["key"]) && !empty($_GET["key"])){
	$default_media_assignment = filter_var($_GET["key"], FILTER_SANITIZE_STRING);
}
$default_sku = -1;
if(isset($_GET["sku"]) && !empty($_GET["sku"])){
	$default_sku = intval($_GET["sku"]);
}

// echo $sm->current_brand->slug;
//The script for this page is being called in file-browser-popup, referenced at the end
?>

<div class="pt-0 wrapper-product wrapper ar" id="mam-detail-page-wrapper">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="page-heading text-center mb-0 mt-5">Add a MAM Asset</h2>
        </div>
      </div>
    </div>
  </section>

  <div class="<?php echo esc_attr($container); ?>" id="content">

    <div class="row">

      <div class="col-md-12" id="primary">
        <main class="site-main" id="main" role="main">
          <div class="asset-detail-wrapper asset-new-wrapper">

            <div class="asset-detail-box mt-5 add-mam-asset-sec" id="asset_selection_app">
              <div class="container">
                <?php
								if(count($errors)>0){
									?>
                <div class="alert alert-danger">
                  <?php echo implode(",", $errors); ?>
                </div>
                <?php
								}
								?>
                <form id="new_asset_frm" action="" method="POST" enctype='multipart/form-data'>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-3">
                          <label>Asset Type</label>
                        </div>
                        <div class="col-md-3">
                          <section>
                            <div class="asset-type-selector dropdown">
                              <select name="media_type" class="media-type" v-model="x">
                                <option value="" selected disabled>Select asset type</option>
                                <?php
																foreach($media_structure as $structure){
																	$group_id = $structure['id'];
																	$group_name = $structure['group_name'];
																	?>
                                <option data-cat="<?= $group_id ?>" value="<?= $group_name ?>">
                                  <?= $group_name ?></option>
                                <?php
																}
															// foreach($media_types as $media_type){
															// 	$sel = "";
															// 	if($media_type->name == $default_media_type){
															// 		$sel = "selected";
															// 	}
															// 	$image =  get_term_meta($media_type->term_id, "category_icon", true );
																?>
                                <!-- <div class="type-group">
																	<input id="cat_<?php echo $media_type->term_id;?>" type="radio" name="media_type" class="media_cat_rad" value="<?php echo $media_type->name;?>" v-model="x" <?php echo $sel ;?>/>
																	<label class="media_cat" for="cat_<?php echo $media_type->term_id;?>"  style="background-image:url('<?php echo $image['guid'];?>');"><?php echo $media_type->name;?></label>
																</div> -->
                                <!-- <option <?php echo $sel;?> data-cat="<?php echo $media_type->term_id;?>"
                                  value="<?php echo $media_type->name;?>"><?php echo $media_type->name;?></option> -->
                                <?php
															// }
															?>
                              </select>
                            </div>
                          </section>

                          <!-- media assignments -->
                          <!-- <div class="row"> -->
                        </div>
                        <div class="col-md-12 mt-5">
                          <!-- <div class="col-md-6"> -->
                          <div class="row">
                            <div class="col-md-3">
                              <label>Media Assignment</label>
                            </div>
                            <div class="col-md-3">
                              <section>
                                <div class="row">

                                  <div class="col-md-12 media_assignments_select_div">
                                    <select class="media_assignments_select">
                                      <option value="" disabled selected>Select media assignment</option>
                                    </select>
                                    <!-- <select v-show="x === 'Images'" name="image-assignment" id="image-assignment"
																		class="form-control" v-model="image_assignment">
																		<?php
																			// foreach($image_assignments as $image_assignment){
																			// 	$sel = "";
																			// 	if($image_assignment->name == $default_media_assignment){
																			// 		$sel = "selected";
																			// 	}
																			// 	echo "<option value='$image_assignment->name' $sel>$image_assignment->name</option>";
																			// }
																		?>
																	</select>

																	<select v-show="x === 'Videos'" name="video-assignment" id="video-assignment"
																		class="form-control" v-model="video_assignment">
																		<?php
																			// foreach($video_assignments as $video_assignment){
																			// 	$sel = "";
																			// 	if($video_assignment->name == $default_media_assignment){
																			// 		$sel = "selected";
																			// 	}
																			// 	echo "<option value='$video_assignment->name' $sel>$video_assignment->name</option>";
																			// }
																		?>
																	</select>

																	<select v-show="x === 'Art Work'" name="artwork-assignment" id="artwork-assignment"
																		class="form-control" v-model="artwork_assignment">
																		<?php
																			// foreach($artwork_assignments as $artwork_assignment){
																			// 	$sel = "";
																			// 	if($artwork_assignment->name == $default_media_assignment){
																			// 		$sel = "selected";
																			// 	}
																			// 	echo "<option value='$artwork_assignment->name' $sel>$artwork_assignment->name</option>";
																			// }
																		?>
																	</select>
																	<select v-show="x === 'Drawings'" name="engineering-drawings" id="engineering-drawings"
																		class="form-control" v-model="engineering_drawings">
																		<?php
																			// foreach($engineering_drawings as $engineering_drawing){
																			// 	$sel = "";
																			// 	if($engineering_drawing->name == $engineering_drawing){
																			// 		$sel = "selected";
																			// 	}
																			// 	echo "<option value='$engineering_drawing->name' $sel>$engineering_drawing->name</option>";
																			// }
																		?>
																	</select>
																	<select v-show="x === 'Manuals'" name="manual-assignment" id="manual-assignment"
																		class="form-control" v-model="manual_assignment">
																		<?php
																			// foreach($manual_assignments as $manual_assignment){
																			// 	$sel = "";
																			// 	if($manual_assignment->name == $manual_assignment){
																			// 		$sel = "selected";
																			// 	}
																			// 	echo "<option value='$manual_assignment->name' $sel>$manual_assignment->name</option>";
																			// }
																		?>
																	</select> -->
                                  </div>
                                </div>
                              </section>
                            </div>
                          </div>

                          <!-- </div> -->
                        </div>
                        <div class="col-md-12">
                          <div data-zipfile data-file-type="zip" class="form-group row"
                            v-show="x !== 'image' && x!== 'video'">
                            <label for="zipfile" class="col-sm-5">Select Source File</label>
                            <input type="file" class="form-control col-sm-12" id="zipfile" name="zipfile"
                              accept=".zip,.rar,.7zip,.pdf,.doc,.docx">
                          </div>
                          <div class="row" v-show="x !== 'image' && x!== 'video'">
                            <div class="col-sm-5"></div>
                            <progress id="zipfile-progress" class="hidden progress-bar mb-3" style="width: 134px;"
                              max="100" value="0"></progress>
                          </div>

                          <div data-vidfile data-file-type="video" class="form-group row" v-show="x == 'video'">
                            <label for="vidfile" class="col-sm-5">Select File MP4</label>
                            <input type="file" class="form-control col-sm-12" id="vidfile" name="vidfile"
                              accept="video/mp4,video/x-m4v">

                          </div>
                          <div class="row" v-show="x == 'video'">
                            <div class="col-sm-5"></div>
                            <progress id="vidfile-progress" class="hidden progress-bar mb-3" style="width: 134px;"
                              max="100" value="0"></progress>
                          </div>
                          <div data-vid_link data-file-type="url" class="form-group row" v-show="x == 'url'">
                            <label for="v_link" class="col-sm-5">Enter Link</label>
                            <input type="text" class="form-control  col-sm-7" id="vid_link" name="vid_link">
                          </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-12 first-half mt-5">

                          <section data-assetfile data-file-type="image">
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <div class="row">
                                    <div class="col-sm-6">
                                      <label for="assetfile" v-if="x === 'Images'">Select Media File</label>
                                    </div>
                                    <div class="col-sm-6">
                                      <!-- <label for="assetfile" class="col-sm-5" v-else>{{ x | singularize }} Image</label> -->
                                      <input type="file" class="form-control col-sm-12" id="assetfile" name="assetfile"
                                        accept="image/png,image/jpg,image/jpeg">
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <section id="img-prev-box">
                                  <div class="img-preview position-relative">
                                    <h4>Image Preview</h4><img id="prev-img" src="" alt="">
                                  </div>
                                </section>
                                <div class="col-12 mt-4">
                                  <div class="col-sm-12"></div>
                                  <progress id="assetfile-progress" class="hidden progress-bar mb-3"
                                    style="width: 100%;" max="100" value="0"></progress>
                                </div>
                              </div>
                            </div>
                          </section>


                          <section>

                            <div class="form-group row mt-5">
                              <div class="col-md-3">
                                <label for="title">Media Title</label>
                              </div>
                              <div class="col-md-9">
                                <input type="text" class="form-control" id="title" name="title" v-model="title">
                              </div>
                            </div>

                            <div class="form-group row mt-5">
                              <div class="col-md-3">
                                <label for="sku">Product SKU</label>
                              </div>
                              <div class="col-md-9">
                                <div class="px-0">
                                  <!-- <select name="sku" id="sku" v-select2 @change="handleChange" v-bind:skus="skus"> -->
                                  <select name="sku" id="sku">
                                    <option value='' selected disabled>Select SKU</option>
                                    <?php

foreach($prods as $prod){
$selected = '';
	?>
                                    <option <?= $selected;?> data-old-id="<?= $prod->old_id ?>"
                                      value="<?= $prod->id ?>">
                                      <?= $prod->sku ?></option>
                                    <?php
}

																		// foreach($skus as $sku){
																		// 	$selected = "";
																		// 	if($sku["id"] == $default_sku){
																		// 		$selected = " selected ";
																		// 	}
																			?>
                                    <!-- <option <?php echo $selected;?> value="<?php echo $sku["id"];?>">
                                      <?php echo $sku["sku"];?></option> -->
                                    <?php
																		// }
																		?>
                                  </select>
                                </div>
                              </div>



                            </div>

                          </section>
                        </div>

                      </div>


                    </div>
                    <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-12">
                          <!-- <section class="keywords1 select2style-1">
                            <div class="row">
                              <div class="col-md-2"></div>
                              <div class="col-md-2">
                                <label>Keywords</label>
                              </div>
                              <div class="col-md-8">
                                <div class="checkboxes-container">
                                  <div class="col-md-12 px-0">
                                    <div style="margin:0px auto;">
                                      <select id="sel_keywords" class="form-control" name="keywords[]"
                                        multiple="multiple" v-model="keywords" v-select2></select>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </section> -->

                          <section class="">
                            <div class="row">
                              <div class="col-md-2"></div>
                              <div class="col-md-2">
                                <label>Note</label>
                              </div>
                              <div class="col-md-8">
                                <div class="form-group">
                                  <textarea class="form-control" name="note" id="note" rows="18"
                                    v-model="notes"></textarea>
                                </div>
                              </div>
                            </div>
                          </section>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 mt-5">
                      <!-- <section class="checkboxes-container">
												<h3>Status</h3>
												<?php
												$i = 0;
												foreach ($asset_statuses as $status) {
													$i++;
													$ico = "fa-circle";
													if(strtolower($status) == 'approved'){
														$ico = "fa-check-circle";
													}
													?>
													<div class="form-check">
														<input class="form-check-input chck-statuses" type="checkbox" value="<?php echo strtolower($status);?>" id="status-<?php echo strtolower($status);?>" name="status[]" v-model="media_status">
														<label class="form-check-label" for="status-<?php echo strtolower($status);?>">
															<i class="fa <?php echo $ico." ".strtolower($status);?>" aria-hidden="true"></i> <?php echo $status;?>
														</label>
													</div>
													<?php
												}
												?>
											</section> -->

                      <section>
                        <button type="submit" class="btn-style-1 btn-new" id="btn_submit" name="submit"
                          value="submit">Add Asset</button>
                      </section>
                    </div>
                  </div>
                </form>
              </div>
              <!-- <div class="assets-sidebar">
								<h3 class="mb-3">Asset Information</h3>
								<section>
									<h4>Description</h4>
									<table>
										<tr>
											<td>Asset ID:</td>
											<td></td>
										</tr>
										<tr>
											<td>Asset Title:</td>
											<td>{{ title }}</td>
										</tr>
										<tr>
											<td>Product SKU:</td>
											<td id="sb_asset_sku">{{skus}}</td>
										</tr>

										<tr v-show="x !== 'Images' && x !== 'Videos'">
											<td>{{ x | singularize }} Zip Size:</td>
											<td id="sb_asset_zip_size"></td>
										</tr>

										<tr v-show="x == 'Videos'">
											<td>{{ x | singularize }} MP4 Size:</td>
											<td id="sb_asset_vid_size"></td>
										</tr>



										<tr v-show="x !== 'Images' && x !== 'Videos'">
											<td>Fonts Zip Size:</td>
											<td id="sb_fonts_zip_size"></td>
										</tr>

										<tr v-show="x == 'Manuals' || x == 'Warranties'">
											<td>Supporting Graphics Zip Size:</td>
											<td id="sb_sg_zip_size"></td>
										</tr>
										<tr>
											<td>Uploaded By:</td>
											<td></td>
										</tr>
										<tr>
											<td>Uploaded:</td>
											<td></td>
										</tr>
										<tr>
											<td>Image File Size:</td>
											<td id="sb_asset_file_size" data-size-bytes="0"></td>
										</tr>
										<tr>
											<td>Image Dimensions:</td>
											<td id="sb_asset_pixels" data-width="0" data-height="0"></td>
										</tr>
										<tr >
											<td>Image Color Space:</td>
											<td id="sb_asset_color"></td>
										</tr>
										<tr>
											<td>Image File Type:</td>
											<td id="sb_file_type"></td>
										</tr>
										<tr>
											<td>Image Optimization:</td>
											<td id="sb_optimization"></td>
										</tr>
									</table>
								</section>
								<section>
									<h4>Brand</h4>
									<p class="bolded"><?php echo $sm->current_brand->brand_name;?></p>
								</section>
								<section>
									<h4>Media Type</h4>
									<p class="bolded">{{ x }}</p>
								</section>
								<section>
									<h4>Media Assignment</h4>
									<p class="bolded">{{ image_assignment }}</p>
								</section>
								<section>
									<h4>Media Status</h4>
									<ul class="status-list">
									<?php
										foreach ($asset_statuses as $status) {
											$ico = "fa-circle";
											if(strtolower($status) == 'approved'){
												$ico = "fa-check-circle";
											}
											?>
											<li v-show="media_status.includes('<?php echo strtolower($status);?>')" ><i class="fa <?php echo $ico." ".strtolower($status);?>" aria-hidden="true"></i> <span><?php echo $status;?></span></li>
											<?php
										}
									?>
									</ul>
								</section>
								<section>
									<h4>Keywords</h4>
									<ul class="keywords">
										<li v-for="keyword in keywords">
											{{ keyword}}
										</li>
									</ul>
								</section>
								<section>
									<h4>Notes</h4>
									<p>{{notes}}</p>
								</section>
							</div> -->

            </div>

          </div>
        </main><!-- #main -->

      </div><!-- #primary -->

    </div><!-- .row end -->

  </div><!-- #content -->

</div><!-- #full-width-page-wrapper -->

<?php
$args = array("media_type" => $default_media_type,
"img_key" => ("Images" == $default_media_type)?$default_media_assignment:"Other",
"vid_key" => ("Video" == $default_media_type)?$default_media_assignment:"Other",
"artwork_key" => ("Art Work" == $default_media_type)?$default_media_assignment:"Other",
"drawing_key" => ("Drawings" == $default_media_type)?$default_media_assignment:"Other",
"manual_key" => ("Manuals" == $default_media_type)?$default_media_assignment:"Other",
"media_structure" => json_encode($media_structure)
 );
$sm->mam->localize_script($args);

//require( locate_template( 'template-parts/file-browser-popup.php', false, false ) );?>
<script>
function RefreshParent() {
  if (window.opener != null && !window.opener.closed) {
    window.opener.location.reload();
  }
}
window.onbeforeunload = RefreshParent;
</script>

<style>
.ezdz-dropzone {
  width: 100%;
}

.img-preview h4 {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  height: fit-content;
  margin: auto;
}

.img-preview {
  height: 180px;
  border: 2px dashed #000;
  text-align: center;
}

.img-preview img {
  height: 100%;
  object-fit: cover;
}

.add-mam-asset-sec label {
  font-size: 14px;
  margin-left: 8px;
  color: #707070;
}

.add-mam-asset-sec select,
.add-mam-asset-sec input[type="text"],
.select2-selection {
  font-size: 14px;
  height: 30px !important;
  color: #707070;
  font-weight: 400 !important;
  width: 100%;
  border-radius: 0 !important;
  border-color: #212529 !important;
  text-align: left !important;
}

.add-mam-asset-sec textarea {
  font-size: 14px;
  color: #707070;
  border-radius: 0 !important;
  border-color: #212529 !important;
}

.btn-new {
  border: 1px solid #707070 !important;
  background-color: #a7c483 !important;
  color: #fff;
  font-family: "Open Sans";
  font-weight: normal;
}
</style>


<?php get_footer();