<?php

/**
 * Template Name: Product Information Page
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package arrow
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
$stm = SettingsManager::GI();

if($stm->check_page_permission('products-index') == 'HIDE') {
	$stm->page_not_permitted();
}

$sm = StateManager::GI();
$prod;
$retailers;
$price_book_codes;

$form_builder;



$product_id = get_query_var('p_id');

$stm->increase_ar_prod_viewed_times($product_id);

$current_product = $stm->get_full_row_from_table('pim_products', $product_id);

if(!$current_product) $stm->item_not_found();

$all_products = $stm->get_all_rows_and_cols_from_table('pim_products');

$field_groups_structure = $stm->generate_field_groups_structure($product_id)['field_groups'];


// $all_seller_apps = $stm->get_all_rows_and_cols_from_table('pim_seller_apps');




// $retailers_active = $sm->get_retailers_list_for_user($current_product->id);
$price_books = $sm->pricing_manager->get_price_books();
$price_book_codes = $sm->get_ar_pricebook_info_for_sku($current_product->id);
// var_dump($price_book_codes);
$form_builder = new FormBuilder($current_product->id);

get_header();

$container = get_theme_mod('arrow_container_type');
wp_enqueue_script("select-2");
wp_enqueue_style("select-2-css");
wp_enqueue_script("magnific-popup");
wp_enqueue_style("magnific-popup-css");
// $prod->load_JS();
wp_enqueue_script("prod-edit-handler");
wp_enqueue_script("media-popup-handler");

$base_link = $sm->media_base_url;

// echo "<pre>";
// print_r($prod);
$child = $prod->is_child;
// echo "</pre>";

?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.product-img img {
  max-height: 348px;
  object-fit: contain;
}

.img-tab form {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.ast-dt {
  display: block;
  /* position: relative;
		top: -58px;
		left: 145px; */
  color: grey;
  width: fit-content;
  padding-left: 15px;
}

.frm-btn.delete-asset-btn {
  position: absolute;
  right: 110px;
  padding: 0 !important;
  background: transparent !important;
  color: red !important;
  top: 28px;
  font-weight: 700 !important;
}

.table-responsive {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  flex-wrap: wrap;
}

.table-responsive form {
  flex: 0 0 100%;
}

#btn-back-to-top {
  position: fixed;
  bottom: 20px;
  right: 8%;
  display: none;
}

.wrapper .glue-area .table td,
.prod-info .table-containers table tbody td {
  background-color: #fff;
  font-size: 1.4rem;
}

.wrapper .assets-table table tbody tr th,
.wrapper .glue-area .table td,
.prod-info .table-containers table tbody td {
  line-height: 1.5;
  padding: 0.5rem 0.5rem;
  font-weight: 400;
  font-size: 14px;
}

.wrapper .assets-table table tbody tr td,
.section .details-prod .tab-content .table th {
  padding-left: 1.6rem;
  border: 1px solid black;
  background-color: #E4E4E3;
  color: black;
  line-height: 2.6;
  border-right: 0;
  /* padding-left: 14px;
        padding-right: 14px; */
}

.wrapper .glue-area .product-completion span {
  font-size: 14px;
  text-transform: capitalize;
  color: #6C6C6C;
}

.wrapper .wrapper-text span {
  color: #707070;
  font-weight: 700;
  margin-bottom: 27px;
  font-size: 18px;
  display: block;
  text-align: center;
}

.active-status input {
  vertical-align: bottom;
}

.select2-container .select2-search--inline .select2-search__field {
  top: 0px;
  margin-top: 6px;
}

.prod-info .table-containers .hide {
  display: none !important;
}

.prod-info .table-containers table tbody td input,
.prod-info .table-containers table tbody td select,
.prod-info .table-containers table tbody td textarea {
  font-size: 12px;
}

/* Product main data */

#product-main-data,
#product-main-data * {
  font-size: 13px !important;
}

#product-main-data input,
#product-main-data textarea,
#product-main-data select {
  width: 100%;
  border: none;
  height: 100%;
  /* padding: 10px 5px; */
  min-height: 31px;
  padding: 0 10px;
}


#product-main-data th {
  padding: 6px 10px;
}

#product-main-data td {
  padding: 0;
}


#product-main-data .prod-image {
  min-width: 300px;
}

#product-main-data .prod_title_main {
  font-size: 3rem !important;
}

#mark_product_complete {
  width: auto !important;
  height: auto !important;
  padding: 0 !important;
  min-height: 20px !important;
}

.abs-parent {
  top: -45px;
  left: 39px;
  width: 550px;
}

#assign_product_parent {
  /* width: auto !important;
  border: none !important;
  height: auto !important;
  min-height: 20px !important;
  padding: 10px 6px !important;
  color: white;
  background: gray !important; */
  /* padding: 5px !important; */
}

#remove_parent {
  background: none;
  border: none;
}

.assign_product_btn {
  min-width: fit-content;
  padding: .25rem .8rem !important;
  font-size: 16px !important;
  border-radius: .2rem;
}


/* COMPLETION BUTTON */
.completion-btn {
  color: #fff;
  display: block;
  width: 160px;
  height: 30px;
  line-height: 27px;
  text-align: center;
  background-color: #a7c483;
  margin-left: 10px;
  border: 1px solid #707070;
  -webkit-box-shadow: 2px 2px 10px 2px rgba(0, 0, 0, .1019607843);
  box-shadow: 2px 2px 10px 2px rgba(0, 0, 0, .1019607843);
}

.completion-status {
  font-size: 14px;
  text-transform: capitalize;
  color: #6C6C6C;
}

.completion-btn .progress-bar {
  height: 28px;
  background-color: #a7c483;
  position: relative;

}

.progress-bar {
  display: flex;
  flex-direction: column;
  justify-content: center;
  overflow: hidden;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  background-color: #0d6efd;
  transition: width .6s ease;
}

.zero-progress-bar {
  width: 100%;
  display: block;
  position: absolute;
  top: -28px;
  z-index: 10000;
  background: white !important;
  color: black;

}

.arrow-main-row {
  background: #EEEEEE;
  border: 3px solid #707070;
}

.section .details-prod .product-info {
  padding: 12px 40px;
  background-color: #707070;
}

.section .details-prod {
  background-color: initial;
  margin-left: 25px;
  margin-right: 25px;
  border: 0;
  border: 3px solid darkgray;
  padding: 0;
}

.ar-user-selected-tab li {
  list-style: none;
}

.ar-user-selected-tab li a {
  width: 255px;
  text-align: center;
}

select[pim-data-field] {
  width: 100%;
  height: 100%;
  padding: 9px 0;
  border: none;
}

.arrow-main-row table th:first-child {
  background-color: #f4474c;
  color: #fff;
}

td.unit-input-td::after {
  right: 0;
  position: absolute;
  width: 40px;
  background: #E4E4E3;
  height: 44.5px;
  top: 0px;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media(min-width: 750px) {
  .popup.revision-popup {
    min-height: 441.6px;
  }

  .popup.revision-popup .revision-popup-body {
    min-height: 400px;
  }

  .popup.revision-popup .revision-popup-body {
    min-height: 400px;
  }

  .popup.revision-popup .revision-loader-body {
    min-height: 310px;
  }
}

.popup.revision-popup .revision-popup-body {
  padding-top: 22px;
}

.popup.revision-popup .popup-loader {
  width: 50px;
  padding: 8px;
  aspect-ratio: 1;
  border-radius: 50%;
  background: #25b09b;
  --_m:
    conic-gradient(#0000 10%, #000),
    linear-gradient(#000 0 0) content-box;
  -webkit-mask: var(--_m);
  mask: var(--_m);
  -webkit-mask-composite: source-out;
  mask-composite: subtract;
  animation: l3 1s infinite linear;
  align-self: center;
  justify-self: center;
}

@keyframes l3 {
  to {
    transform: rotate(1turn)
  }
}
</style>


<button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
  <i class="fa fa-arrow-up"></i>
</button>
<script>
//Get the button
let mybutton = document.getElementById("btn-back-to-top");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {
  scrollFunction();
};

function scrollFunction() {
  if (
    document.body.scrollTop > 20 ||
    document.documentElement.scrollTop > 20
  ) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}
// When the user clicks on the button, scroll to the top of the document
mybutton.addEventListener("click", backToTop);

function backToTop() {
  document.body.scrollTop = document.getElementById("myTab").offsetTop;
  document.documentElement.scrollTop = document.getElementById("myTab").offsetTop;
}
</script>




<div class="wrapper prod-info pt-0" id="full-width-page-wrapper">

  <div class="<?php echo esc_attr($container); ?>" id="content">


    <section id="product-main-data">



      <div class="container ">
        <div class="row my-5">
          <div class="col-12 text-center">
            <h1 class="prod_title_main"><?= $current_product->title ?></h1>
          </div>
        </div>


        <div class="arrow-main-row row mb-5 pb-5 pe-5 pt-5">
          <div class="container">

            <!-- Completion -->
            <div class="row mb-3">
              <div class="d-flex align-items-center gap-5 justify-content-end">

                <!-- Completion Status -->
                <div class="completion-status d-flex  justify-content-end align-items-center">
                  <span>completion</span>
                  <div class="completion-btn bg-light">
                    <div class="progress-bar "
                      data-original-weightage="<?= $stm->current_product_completion_weightage ?>"
                      style="width:<?= $current_product->is_completed ? '100' : $stm->current_product_completion_weightage ?>%;">
                      <?= $current_product->is_completed ? '100' : $stm->current_product_completion_weightage ?>%
                    </div>

                    <div
                      class="zero-progress-bar progress-bar <?= $stm->current_product_completion_weightage > 0 ? 'hidden' : ''  ?>"
                      style="width:<?= $current_product->is_completed && !$stm->current_product_completion_weightage ? '0' : '100'  ?>%; ">
                      <?= $stm->current_product_completion_weightage ?>%
                    </div>

                  </div>
                  <!-- <div class="completion-btn">46.67%</div> -->
                </div>


                <!-- MARK COMPLETE CHECKBOX -->
                <div id="mark_product_complete_div" class="d-flex align-items-center gap-1"
                  style="top: -35px !important; right: 10px;">
                  <input id="mark_product_complete" type="checkbox" name="mark_product_complete"
                    id="mark_product_complete" <?= $current_product->is_completed ? 'checked' : '' ?> />
                  <label for="mark_product_complete" class="mb-0">Mark Complete</label>
                </div>

                <!-- MARK COMPLETE CHECKBOX -->
                <div id="assign_product_parent_div" class="d-flex align-items-center gap-1"
                  style="top: -35px !important; right: 10px;">


                  <?php //if($current_product->parent_id):
                  ?>
                  <button id="remove_parent" class="btn-danger <?= $current_product->parent_id ? '' : 'd-none' ?>">
                    <svg style="width: 20px;
    										height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="red">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                  </button>
                  <?php //endif;
                  ?>

                  <div class="d-flex justify-content-center align-items-center gap-1">
                    <select name="assign_product_parent" id="assign_product_parent">
                      <?php // echo $current_product->parent_id ? $current_product->parent_id : '0'
                      ?>
                      <option value="0" disabled <?= $current_product->parent_id ? '' : 'selected' ?>>Select Parent
                        Product
                      </option>
                      <?php
                      foreach ($all_products as $prod) {
                        if ($prod->id != $current_product->id) {

                          $selected =  $current_product->parent_id == $prod->id ? 'selected' : '';

                          echo "<option value='$prod->id' $selected> $prod->sku </option>";
                        }
                      }
                      ?>

                    </select>

                    <button type="button" style="min-width:fit-content;"
                      class="assign_product_btn btn btn-warning ">Assign
                    </button>
                  </div>
                  <!-- <label for="assign_product_parent" class="mb-0">Select Parent</label> -->
                </div>

              </div>
            </div>

            <div class="row">
              <div class="col-md-4 text-center">

                <img src="<?= $stm->get_media_url($current_product->main_image, 'thumbnail') ?>" alt=""
                  class="prod-image img-fluid border border-2" style="border:2px solid #707070 !important;padding: 20px;
    background: white;"
                  onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png';">

              </div>
              <div class="col-md-4">
                <table>
                  <tr>
                    <th>Product ID</th>
                    <td><input type="text" value="<?= $current_product->id ?>" disabled></td>
                  </tr>
                  <tr>
                    <th>Product SKU</th>
                    <td><input name="asset_title" data-editable type="text" value="<?= $current_product->sku ?>"
                        disabled>
                    </td>
                  </tr>
                  <tr>
                    <th>Brand</th>
                    <td><input type="text" value="<?= $stm->get_its_brand($current_product->brand_id) ?>" disabled>
                    </td>
                  </tr>
                  <tr>
                    <th>Parent Id</th>
                    <td><input name="parent_id_input" type="text"
                        value="<?= $current_product->parent_id ? $current_product->parent_id : '' ?>" disabled>
                    </td>
                  </tr>
                  <tr>
                    <th>Product Description</th>
                    <td><textarea name="prod_desc" data-editable rows="3" type="text" style="resize: none;" value=""
                        data-val="<?= $current_product->simple_desc ?>" disabled></textarea>
                    </td>
                  </tr>
                  <tr>
                    <th>Uploaded by</th>
                    <td><input type="text" value="<?= $stm->get_users_name($current_product->user_id) ?>" disabled>
                      <br>
                    </td>
                  </tr>
                  <tr>
                    <th>Country Of Origin</th>
                    <td><input type="text" value="<?= $current_product->country_of_origin ?>" disabled></td>
                  </tr>
                  <tr>
                    <th>Standard Cost</th>
                    <td><input type="text" value="<?= $current_product->std_cost ?>" disabled></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-4">
                <table>
                  <tr>
                    <th>Standard Cost Currency</th>
                    <td><input type="text"
                        value="<?= empty($current_product->std_cost_currency) ? 'Un-specified' : $current_product->std_cost_currency ?>"
                        disabled></td>
                  </tr>
                  <tr>
                    <th>Master Price</th>
                    <td><input type="text" value="<?= $current_product->master_price ?>" disabled></td>
                  </tr>
                  <tr>
                    <th>Manufactured/Purchased Status</th>
                    <td><input type="text" value="<?= $current_product->manuf_purch_status ?>" disabled></td>
                  </tr>
                  <tr>
                    <th>Parent/Child Status</th>
                    <td><input type="text"
                        value="<?= empty($current_product->parent_child_status) ? 'Parent' : $current_product->parent_child_status ?>"
                        disabled></td>
                  </tr>
                  <tr>
                    <th>Product Status</th>
                    <td><input type="text" value="<?= $current_product->product_status ?>" disabled></td>
                  </tr>
                  <tr>
                    <th>Is Completed</th>
                    <td><input type="text" value="<?= empty($current_product->is_completed) ? 'False' : 'True' ?>"
                        disabled>
                    </td>
                  </tr>
                  <tr>
                    <th>Is Dirty</th>
                    <td><input type="text" value="<?= empty($current_product->is_dirty) ? 'False' : 'True' ?>" disabled>
                    </td>
                  </tr>
                  <tr>
                    <th>Sync DateTime</th>
                    <td><input type="text" value="<?= $current_product->sync_datetime ?>" disabled></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>





        <script>
        document.addEventListener('DOMContentLoaded', e => {

          jQuery("#assign_product_parent").select2({
            placeholder: "Select a parent",
            // allowClear: true
          });

          // Remove parent
          const removeParentButton = document.querySelector('#remove_parent')

          const zeroProgressBar = document.querySelector('.zero-progress-bar ')
          const progressBar = document.querySelector('.progress-bar ')


          // mark complete
          const mark_complete_input = document.querySelector('#mark_product_complete')
          const mark_complete_div = document.querySelector('#mark_product_complete_div')
          mark_complete_input.addEventListener('click', e => {
            e.preventDefault()
            e.stopPropagation()

            const isChecked = e.target.checked
            mark_complete_div.style.pointerEvents = "none"
            console.log('<?= $current_product->id ?>', isChecked ? 1 : 0)
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: new URLSearchParams({
                  product_id: '<?= $current_product->id ?>',
                  is_complete: isChecked ? 1 : 0,
                  action: 'mark_product_complete',
                  _wpnonce: '<?php echo wp_create_nonce('mark_product_complete'); ?>'
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  console.log('is_complete status updated.');
                  e.target.checked = !e.target.checked;

                  if (isChecked) {
                    // console.log("JOASDJAOSDJ")
                    progressBar.style.width = '100%'
                    progressBar.textContent = '100%'
                    zeroProgressBar.classList.add('hidden')
                    zeroProgressBar.style.width = '0%'
                    // progressBar.textContent = '100%'
                  } else {
                    // console.log("JOASDJAOSDJ00000000---", progressBar.dataset.originalWeightage)
                    progressBar.style.width = progressBar.dataset.originalWeightage + '%'
                    progressBar.textContent = progressBar.dataset.originalWeightage + '%'
                    zeroProgressBar.classList.remove('hidden')
                    zeroProgressBar.style.width = '100%'
                    console.log('sss', progressBar.dataset.originalWeightage + '%')
                    zeroProgressBar.textContent = progressBar.dataset.originalWeightage + '%'
                  }

                  // if (e.target.checked) {
                  //   document.querySelector('#completion_weightage').textContent = '100%'
                  // } else {
                  //   document.querySelector('#completion_weightage').textContent = document.querySelector(
                  //     '#completion_weightage').dataset.prevValue + '%'
                  // }

                } else {
                  console.error('Failed to update is_complete status.');
                }
              })
              .
            catch(error => {
                console.error('Error:', error);
              })
              .finally(() => {
                mark_complete_div.style.pointerEvents = "auto"

              });

          })







          const assign_product_parent = document.querySelector('#assign_product_parent')
          const assign_product_button = document.querySelector('.assign_product_btn')
          const assign_product_parent_div = document.querySelector('#assign_product_parent_div')
          assign_product_button.addEventListener('click', e => {

            if (assign_product_parent.value.trim() == 0) return

            assign_product_parent_div.style.pointerEvents = "none"

            console.log('assign_product_parent.value ', assign_product_parent.value)

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: new URLSearchParams({
                  product_id: '<?= $current_product->id ?>',
                  parent_id: assign_product_parent.value,
                  action: 'assign_product_parent',
                  _wpnonce: '<?php echo wp_create_nonce('assign_product_parent'); ?>'
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  alert('PARENT ASSIGNED');
                  removeParentButton.classList.remove('d-none')
                  document.querySelector('input[name="parent_id_input"]').value = assign_product_parent
                    .value

                } else {
                  console.error('Failed to ASSIGN PARENT');
                }
              })
              .
            catch(error => {
                console.error('Error:', error);
              })
              .finally(() => {
                assign_product_parent_div.style.pointerEvents = "auto"

              });

          })



          removeParentButton.addEventListener('click', e => {

            removeParentButton.style.pointerEvents = "none"

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: new URLSearchParams({
                  product_id: '<?= $current_product->id ?>',
                  parent_id: document.querySelector('#assign_product_parent').value,
                  action: 'remove_parent',
                  _wpnonce: '<?php echo wp_create_nonce('remove_parent'); ?>'
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  console.log('Removed Parent');
                  jQuery("#assign_product_parent").val('').trigger('change');

                  removeParentButton.classList.add('d-none')
                  assign_product_parent.value = '0'
                  document.querySelector('input[name="parent_id_input"]').value = ''

                } else {
                  console.error('Failed to Remove Parent');
                }
              })
              .
            catch(error => {
                console.error('Error:', error);
              })
              .finally(() => {
                removeParentButton.style.pointerEvents = "auto"

              });

          })


        })
        </script>





      </div>

      <script>
      const prod_desc_el = document.querySelector('#product-main-data textarea[name="prod_desc"]')
      prod_desc_el.value = prod_desc_el.dataset.val
      </script>
    </section>


    <div class="section">
      <div class="container">
        <div class="details-prod">
          <div class="product-info">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Product-Information"
                  type="button" role="tab" aria-controls="home" aria-selected="true">Product Information</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Price-Books"
                  type="button" role="tab" aria-controls="profile" aria-selected="false">Price Books/Retailers</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#Media-Assets"
                  type="button" role="tab" aria-controls="contact" aria-selected="false">Media Assets</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#Sell-Sheets"
                  type="button" role="tab" aria-controls="contact" aria-selected="false">Sell Sheets</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#Revisions" type="button"
                  role="tab" aria-controls="contact" aria-selected="false">Revisions</button>
              </li>
            </ul>
          </div>
          <div class="tab-content main_info_source" id="myTabContent"
            data-brand-code="<?= substr($sm->current_brand->slug, 0, 2) ?>" data-prod-id="<?= $product_id ?>"
            data-prod-parent-id="<?= $current_pr_parent_id ?>"
            data-ajax-url='<?php echo admin_url("admin-ajax.php"); ?>'>
            <div class="tab-pane fade show active" id="Product-Information" role="tabpanel" aria-labelledby="home-tab">










              <!-- ************************************ -->
              <!-- *****  CUSTOM CODE - AMEER - START ******* -->
              <!-- ************************************ -->

              <style>
              /* HTML: <div class="loader"></div> */
              .loader {
                width: 25px !important;
                height: 25px !important;
                padding: 8px;
                margin-left: auto !important;
                right: 20px;
                aspect-ratio: 1;
                border-radius: 50%;
                background: #25b09b;
                --_m:
                  conic-gradient(#0000 10%, #000),
                  linear-gradient(#000 0 0) content-box;
                -webkit-mask: var(--_m);
                mask: var(--_m);
                -webkit-mask-composite: source-out;
                mask-composite: subtract;
                animation: l3 1s infinite linear;
              }

              @keyframes l3 {
                to {
                  transform: rotate(1turn)
                }
              }

              .product-spec {
                /* margin: 20px 0; */
                padding: 35px 80px !important;
              }

              .section .details-prod .product-spec {
                margin-top: 0;
              }

              .tabs {
                list-style: none;
                padding: 0;
                display: grid;
                /* grid-template-columns: repeat(4, 1fr); */
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                ;
                /* border-bottom: 1px solid #ddd; */
                margin-bottom: 0 !important;
                color: black !important;
                flex-wrap: wrap;
                gap: 20px;
              }

              .tab {
                /* margin-right: 10px; */
              }

              .tab a {
                text-decoration: none;
                padding: 10px 15px;
                display: block;
                color: #333;
                border: 1px solid transparent;
                font-size: 15px;
                border-bottom: none;
              }

              .tab.active a {
                /* border-color: #ddd #ddd #fff; */
                /* background-color: #f9f9f9; */
              }

              .tabs-content {
                /* border: 1px solid #ddd; */
                /* padding: 20px; */
              }

              .tab-content {
                /* display: none; */
              }

              .tab-content.active {
                display: block;
              }

              .importer_form {
                font-size: 14px;
              }

              .add-fields {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
              }

              .add-fields h1 {
                margin-bottom: 20px;
                font-size: 24px;
                color: #333;
                text-align: center;
              }

              .add-fields label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
                color: #555;
              }

              .add-fields input[type="text"],
              .add-fields input[type="number"],
              .add-fields textarea,
              .add-fields select {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
              }

              .add-fields .radio-group {
                display: flex;
                flex-direction: column;
                /* justify-content: space-between; */
                margin-bottom: 15px;
              }

              .add-fields .radio-group input[type="radio"] {
                margin-right: 5px;
              }

              .add-fields button {
                display: block;
                width: 100%;
                padding: 10px;
                background-color: #007bff;
                color: #fff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
              }

              .add-fields button:hover {
                background-color: #0056b3;
              }

              .hidden {
                display: none;
              }

              #add_field_btn {
                padding: 6px 10px;
                border-radius: 6px;
                border: 2px solid white;
                background: #000;
                color: white;
                font-size: 20px;
                margin-left: auto;
                display: block;
                /* width: 100%; */
              }

              #add_field_form {
                margin-bottom: 100px;
              }

              #add_option_btn,
              #remove_option_btn {
                width: min-content;
                margin-top: 10px;
                margin-bottom: 10px;
                padding: 6px 13px;
                border-radius: 100%;
                font-weight: bold;

              }

              .hidden {
                display: none;
              }

              .option_field {
                margin-bottom: 5px;
              }

              .add_remove_options {
                display: flex;
                gap: 20px;
              }



              .form-group {
                display: flex;
                align-items: center;
                margin-bottom: 10px !important;
                max-width: 80vw;
                margin: auto;
                font-size: 14px;
              }

              .form-group label {
                flex: 0 0 150px;
                padding-right: 10px;
                font-weight: bold;
              }

              .form-group input[type="text"],
              .form-group input[type="number"],
              .form-group input[type="range"],
              .form-group textarea,
              .form-group select {
                /* margin-top: 20px; */
                flex: 1;
                padding: 5px;
              }

              .form-group input[type="checkbox"],
              .form-group input[type="radio"] {
                margin-left: 0;
              }

              .form-group label span {
                display: inline-block;
                margin-right: 5px;
              }

              .tab_heading {
                font-size: 25px;
                font-weight: bold;
                margin: 20px auto;
                width: 100%;
                text-align: center;
              }

              input,
              select,
              textarea,
              .arrow_prod_detail_form,
              .arrow_prod_detail_form table tr th,
              .arrow_prod_detail_form table tr td,
              .arrow_prod_detail_form input::placeholder,
              .arrow_prod_detail_form textarea::placeholder {
                /* border: 1px solid gray; */
                font-size: 1.4rem !important;
                font-weight: normal !important;
              }

              .arrow_prod_detail_form table tr td input,
              .arrow_prod_detail_form table tr td textarea {
                width: 100% !important;
                border: none !important;
              }

              .section .details-prod div.product-spec {
                margin-bottom: 0 !important;
              }

              .arrow_prod_detail_form input,
              .arrow_prod_detail_form textarea,
              .arrow_prod_detail_form th {
                padding: .5rem;
              }

              .arrow_prod_detail_form .table>:not(caption)>*>* {
                padding-top: .2rem;
                padding-bottom: .2rem;
              }

              .arrow_prod_detail_form td {
                background-color: white;
              }

              /* .arrow_prod_detail_form, .arrow_prod_detail_form input, .arrow_prod_detail_form textarea {
								font-size: 1.2rem !important;
							} */

              /* {
								font-size: 1.2rem !important;
							} */

              .edit_div,
              .cancel_or_save_div,
              .media_edit_div,
              .media_cancel_or_save_div {
                display: flex;
                gap: 5px;
                /* margin-left: auto; */
              }

              select[data-purpose="filter_seller_apps"] {
                width: auto !important;
                border: none !important;
                height: auto !important;
                /* padding: 5px !important; */
                min-width: 140px;
                min-height: 20px !important;
                padding: 7px 6px !important;
                /* color: white; */
                /* background: gray !important; */
                border: 2px solid black !important;
              }

              .arrow_prod_detail_form .edit_div button,
              .media_edit_div button,
              .media_cancel_or_save_div button {
                background: #707070;
                border-color: #707070 !important;
                padding: 5px 10px;
                color: #fff;
                font-size: 14px;
                font-family: "Open Sans";
                text-decoration: none;
                font-weight: 400;
                display: block;
                width: max-content;
                margin-left: auto;
                outline: none;
                box-shadow: none;
              }

              .media_edit_div.un_assigned {
                top: -23px;
              }

              .media_edit_div.un_assigned .media_edit_btn {
                align-items: center;
                justify-content: center;
              }

              #arrow-media .arrow_prod_detail_form table tr td {
                padding: 20px 5px !important;
              }

              span.pim_span {
                background: white;
                color: black;
                min-height: 40.8px;

                display: inline-flex;
                align-items: center;
                padding: 0 10px;
                font-size: 13px;
                /* margin-right: 10px; */
                width: 100%;
              }

              .arrow_prod_detail_form table tr td.brand__span {
                background: white;
                width: 50px !important;
                color: black !important;
                min-height: 40.8px;
                align-items: center;
                padding: 0 10px !important;
                background-color: white !important;
                font-size: 13px !important;
                margin-right: 10px;
              }

              .ar-main-tab-content-row {
                padding-right: 80px;
                padding-left: 80px;
              }

              .single-media.assigned-single-media .media-assigned img {
                /* text-align: center; */
                margin-top: 10px;
              }

              .single-media.assigned-single-media h4 {
                color: black;
                font-size: 12px;
              }

              .single-media.assigned-single-media a {
                /* color: black; */
                font-size: 12px;
              }

              .retailer-tab a {
                text-decoration: none;
                padding: 10px 15px;
                display: block;
                color: #333;
                border: 1px solid transparent;
                font-size: 15px;
                border-bottom: none;
                cursor: pointer;
              }
              </style>

              <?php

              $prod_id = !$current_pr_parent_id ? $prod->prod_id : $current_pr_parent_id;

              // $field_groups_structure = $field_data['field_groups'];
              $field_groups_structure = $stm->order_field_groups_structure_by_display_order($field_groups_structure);

              // $stm->dump(count($field_groups_structure['field_groups']));

              // $tables = $stm->get_all_rows_and_cols_from_table('pim_table_metas');
              // $stm->return_if_empty_or_not_found($tables, "No tables found.");
              ?>

              <div class="product-spec">
                <ul class="tabs">
                  <?php $stm->generate_html_tabs_for_each_table_meta($field_groups_structure); ?>
                </ul>
              </div>

              <div class="row ar-main-tab-content-row pb-5">
                <div class="col-md-12">
                  <div class="tabs-content">
                    <?php
                    $stm->generate_html_fields_for_each_table_meta($product_id, $field_groups_structure);
                    ?>
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="tabs-content">
                    <?php
                    //$stm->generate_html_fields_for_each_table_meta($product_id, $field_groups_structure);
                    ?>
                  </div>
                </div> -->
              </div>

              <script>
              document.addEventListener("DOMContentLoaded", () => {

                console.log("YESR")


                document.addEventListener('click', e => {
                  if (e.target.matches('.pim_inner_radio_input')) {

                    setRadioValueToSingleInput(e.target)

                  }

                  if (e.target.matches('.pim_inner_checkbox_input')) {

                    setCheckboxValueToSingleInput(e.target)

                  }

                })


                // document.addEventListener('input', e => {

                //   if (e.target.closest('.range_min')) {
                //     // mainRangeInput.dataset.rangeMin = e.target.closest('.range_min').value

                //     setRangeValueToSingleInput(e.target)
                //   }

                //   if (e.target.closest('.range_max')) {
                //     // mainRangeInput.dataset.rangeMax = e.target.closest('.range_max').value

                //     setRangeValueToSingleInput(e.target)

                //   }

                // })

                // document.addEventListener('keydown', e => {
                //   console.log('o 2k rriiitt');
                //   if (e.key === "Backspace" || e.key === "Delete") {
                //     console.log('ok rriiitt');
                //     if (e.target.closest('.range_min')) {
                //       // mainRangeInput.dataset.rangeMin = e.target.closest('.range_min').value

                //       setRangeValueToSingleInput(e.target)
                //     }

                //     if (e.target.closest('.range_max')) {
                //       // mainRangeInput.dataset.rangeMax = e.target.closest('.range_max').value

                //       setRangeValueToSingleInput(e.target)

                //     }
                //   }

                // })

                function setRangeValueToSingleInput(target) {
                  const mainRangeInput = target.closest('td').querySelector('.range_main_input')

                  const rangeMinValue = [...target.closest('td').children][1].value
                  const rangeMaxValue = [...target.closest('td').children][2].value
                  console.log('NOT SAVING ANyway (range)');
                  // if (rangeMinValue.trim() == '' && rangeMaxValue.trim() == '') return
                  console.log('SAVING ANyway (range)');
                  const finalValue = [rangeMinValue, rangeMaxValue]

                  mainRangeInput.value = JSON.stringify(finalValue)
                  mainRangeInput.dataset.finalValue = JSON.stringify(finalValue)
                }

                function setRadioValueToSingleInput(target) {
                  const mainRadioInput = target.closest('td').querySelector('.pim_radio_main_input')

                  mainRadioInput.value = target.value
                  console.log(target, target.value, mainRadioInput, mainRadioInput.value);

                  // const rangeMinValue = [...target.closest('td').children][1].value
                  // const rangeMaxValue = [...target.closest('td').children][2].value
                  // console.log('NOT SAVING ANyway (range)');
                  // // if (rangeMinValue.trim() == '' && rangeMaxValue.trim() == '') return
                  // console.log('SAVING ANyway (range)');
                  // const finalValue = [rangeMinValue, rangeMaxValue]

                  // mainRangeInput.value = JSON.stringify(finalValue)
                  // mainRangeInput.dataset.finalValue = JSON.stringify(finalValue)
                }

                function setCheckboxValueToSingleInput(target) {
                  const parentTd = target.closest('td');
                  const mainRadioInput = parentTd.querySelector('.pim_checkbox_main_input')

                  const allCheckedCheckboxes = [...parentTd.querySelectorAll('.pim_inner_checkbox_input:checked')]
                  allCheckboxesValues = allCheckedCheckboxes.map(el => el.value)

                  mainRadioInput.value = JSON.stringify(allCheckboxesValues)
                  console.log(mainRadioInput.value);
                }

                // const allMinRanges = [...document.querySelectorAll('.range_min')]
                // const allMaxRanges = [...document.querySelectorAll('.range_max')]

                // allMinRanges.forEach(range => {
                //   range.addEventListener('change', e => {
                //     console.log(range.value)
                //   })
                // })


                // JavaScript for Product Information Tabs - START
                let tabs = document.querySelectorAll(".product-spec .tabs .tab a");
                let tabContents = document.querySelectorAll(".tabs-content .tab-content");


                handleTabContentsDisplay(tabs, tabContents)

                function handleTabContentsDisplay(tabs, tabContents) {
                  tabs.forEach(function(tab, i) {

                    deActivateTabContent(tabContents[i])

                    tab.addEventListener("click", function(event) {
                      event.preventDefault();

                      tabs.forEach(function(t, i) {
                        t.parentElement.classList.remove("active");
                        deActivateTabContent(tabContents[i])
                      });

                      tab.parentElement.classList.add("active");
                      activateTabContent(tab)

                      // const activeTab = d
                      // console.log(event.target.closest('li'))
                      // const userSelectedTab = [...document.querySelectorAll('.ar-user-selected-tab')];
                      // const closestListElement = event.target.closest('li')
                      // const tabIndex = closestListElement.dataset.tabNum;
                      // userSelectedTab[tabIndex].innerHTML =
                      //   `<li class="tab"> ${closestListElement.innerHTML} </li>`
                    });
                  });

                  activateTabContent(tabs[0])

                }

                function activateTabContent(tab) {
                  let target = tab?.getAttribute("href");
                  if (target) {
                    document.querySelector(target).classList.add("active");
                    document.querySelector(target).style.display = 'block';
                  }
                }

                function deActivateTabContent(content) {
                  content.classList.remove("active");
                  content.style.display = 'none';
                }


                // JavaScript for Product Information Tabs - ENDS


                // EDIT, Save, cancel functionality - STARTS
                const cancel_or_save_divs = document.querySelectorAll('#cancel_or_save_div');
                cancel_or_save_divs.forEach(div => handleCancelDiv(div, 'hide'))

                document.addEventListener('click', e => {

                  if (e.target.closest('#edit_btn')) {
                    handleEditButton(e.target, 'hide')
                    handleCancelDiv(e.target, 'show')

                    handleFields(e.target, 'enable')

                    handleValue(e.target, 'edit')
                  }

                  if (e.target.closest('#save_btn')) {
                    handleCancelDiv(e.target, 'hide')

                    handleFields(e.target, 'disable')

                    handleValue(e.target, 'save')

                    // isAllDataSame(e.target) || makeAJAXRequestToSaveOrUpdateFormData(e.target)

                    if (isAllDataSame(e.target)) {
                      handleEditButton(e.target, 'show')
                    } else {
                      handleLoader(e.target, 'show')
                      makeAJAXRequestToSaveOrUpdateFormData(e.target)
                    }


                  }

                  if (e.target.closest('#cancel_btn')) {
                    handleCancelDiv(e.target, 'hide')
                    handleEditButton(e.target, 'show')

                    handleFields(e.target, 'disable')

                    handleValue(e.target, 'cancel')
                  }

                })





                function isAllDataSame(target) {
                  console.log('same ', [...target.closest('form.arrow_prod_detail_form').querySelectorAll(
                      '[pim-data-field]'
                    )].map(el => {
                      if (el.type == 'file') return
                      // console.log(el.value, el.dataset.previousValue)
                      return el.value === el.dataset.previousValue
                    })
                    .every(bool => bool))
                  // return [...target.closest('form.arrow_prod_detail_form').querySelectorAll(
                  //     ':scope > table.table input, :scope > table.table textarea, :scope > table.table select, :scope > table.table checkbox, :scope > table.table radio'
                  //   )]
                  return [...target.closest('form.arrow_prod_detail_form').querySelectorAll(
                      '[pim-data-field]'
                    )].map(el => {
                      if (el.type == 'file') return
                      // console.log(el.value, el.dataset.previousValue)
                      return el.value === el.dataset.previousValue
                    })
                    .every(bool => bool)
                }

                function handleEditButton(target, action) {
                  if (action == 'hide') {
                    if (!target) {
                      document.querySelectorAll('form.arrow_prod_detail_form').forEach(el => {
                        el.querySelector('#edit_btn')
                          .style.display = 'none'
                      })
                      return
                    }

                    target.closest('form.arrow_prod_detail_form').querySelector('#edit_btn')
                      .style.display = 'none'
                  } else if (action == 'show') {
                    if (!target) {
                      document.querySelectorAll('form.arrow_prod_detail_form').forEach(el => {
                        el.querySelector('#edit_btn').style.display = 'flex'
                      })
                      return
                    }

                    target.closest('form.arrow_prod_detail_form')
                      .querySelector('#edit_btn').style.display = 'flex'
                  }
                }

                function handleLoader(target, action) {

                  if (action == 'hide') {
                    target.closest('form.arrow_prod_detail_form').querySelector('.loader')
                      .classList.add('hidden')
                  } else if (action == 'show') {
                    target.closest('form.arrow_prod_detail_form')
                      .querySelector('.loader').classList.remove('hidden')
                  }
                }

                function handleCancelDiv(target, action) {
                  if (action == 'hide') {
                    if (!target) {
                      document.querySelectorAll('form.arrow_prod_detail_form').forEach(el => {
                        el.querySelector('#cancel_or_save_div').style.display = 'none'
                      })
                      return
                    }

                    target.closest('form.arrow_prod_detail_form').querySelector(
                      '#cancel_or_save_div').style.display = 'none'
                  } else if (action == 'show') {
                    if (!target) {
                      document.querySelectorAll('form.arrow_prod_detail_form').forEach(el => {
                        el.querySelector('#cancel_or_save_div').style.display =
                          'flex'
                      })
                      return
                    }

                    target.closest(
                        'form.arrow_prod_detail_form').querySelector('#cancel_or_save_div').style.display =
                      'flex'
                  }
                }

                function handleFields(target, action) {
                  if (action == 'enable') {
                    // target.closest('form.arrow_prod_detail_form').querySelectorAll(
                    //     ':scope > table.table input, :scope > table.table textarea, :scope > table.table select, :scope > table.table checkbox, :scope > table.table radio'
                    //   )
                    target.closest('form.arrow_prod_detail_form').querySelectorAll(
                      '[pim-data-field][pim-data-permitted]'
                    ).forEach(el => el.removeAttribute('disabled'))
                  } else if (action == 'disable') {
                    // target.closest('form.arrow_prod_detail_form').querySelectorAll(
                    //     ':scope > table.table input, :scope > table.table textarea, :scope > table.table select, :scope > table.table checkbox, :scope > table.table radio'
                    //   )
                    target.closest('form.arrow_prod_detail_form').querySelectorAll(
                      '[pim-data-field][pim-data-permitted]'
                    ).forEach(el => el.setAttribute('disabled', true))
                  }
                }

                // Function to restore the state of checkboxes based on the main input's JSON value
                function restoreCheckboxesState() {
                  const allPimMainCheckboxes = [...document.querySelectorAll('.pim_checkbox_main_input')];

                  allPimMainCheckboxes.forEach(mainCheckbox => {
                    // Parse the JSON value from the main input
                    const selectedValues = JSON.parse(mainCheckbox.value || '[]');

                    // Get all sibling checkboxes (excluding the main hidden input)
                    const parent = mainCheckbox.closest(
                      'td'); // Assuming they are siblings within a <td> element
                    const innerCheckboxes = [...parent.querySelectorAll('.pim_inner_checkbox_input')];

                    innerCheckboxes.forEach(checkbox => {
                      // If the checkbox value is in the selected values array, check it
                      if (selectedValues.includes(checkbox.value)) {
                        checkbox.checked = true;
                      } else {
                        checkbox.checked = false;
                      }
                    });
                  });
                }

                function updateCheckboxesToPreviousValue() {
                  const allPimMainCheckboxes = [...document.querySelectorAll('.pim_checkbox_main_input')];

                  allPimMainCheckboxes.forEach(mainCheckbox => {
                    // Parse the JSON value from the main input
                    const selectedValues = JSON.parse(mainCheckbox.value || '[]');

                    // Get all sibling checkboxes (excluding the main hidden input)
                    const parent = mainCheckbox.closest(
                      'td'); // Assuming they are siblings within a <td> element
                    const innerCheckboxes = [...parent.querySelectorAll('.pim_inner_checkbox_input')];

                    innerCheckboxes.forEach(checkbox => {
                      // If the checkbox value is in the selected values array, check it
                      if (selectedValues.includes(checkbox.value)) {
                        checkbox.checked = true;
                      } else {
                        checkbox.checked = false;
                      }
                    });
                  });
                }



                function updateRadiosToPreviousValue() {
                  const allPimMainRadios = [...document.querySelectorAll('.pim_radio_main_input')];
                  allPimMainRadios.forEach(mian_el => {
                    const parent = mian_el.closest('td');
                    const mainRadioChildren = [...parent.querySelectorAll('.pim_inner_radio_input')];

                    mainRadioChildren.forEach(inner_el => {
                      // Remove disabled and uncheck all radios
                      inner_el.removeAttribute('disabled');
                      inner_el.removeAttribute('checked');
                      inner_el.checked = false;

                      // Check the specific radio if its value matches the main radio's value
                      if (inner_el.value === mian_el.value) {
                        console.log('Matching value found:', mian_el.value, inner_el.value);

                        // Check the radio button
                        inner_el.checked = true;
                        inner_el.setAttribute('checked', 'checked'); // Update the attribute
                        inner_el.dataset.shouldBeChecked = true;

                        // Optional click event to trigger any associated listeners
                        inner_el.dispatchEvent(new Event('change', {
                          bubbles: true
                        }));

                        console.log('Radio button is checked:', inner_el.checked);
                      }

                      // Disable the radio button after setting the checked state
                      inner_el.setAttribute('disabled', 'true');
                    });
                  });
                }

                function handleValue(target, action) {
                  // target.closest('form.arrow_prod_detail_form').querySelectorAll(
                  //     ':scope > table.table input, :scope > table.table textarea, :scope > table.table select, :scope > table.table checkbox, :scope > table.table radio'
                  //   )
                  target.closest('form.arrow_prod_detail_form').querySelectorAll(
                    '[pim-data-field][pim-data-permitted]'
                  ).forEach(el => {
                    if (el.type == 'file') return
                    if (action == 'edit') el.dataset.previousValue = el.value
                    if (action == 'cancel') {

                      // console.log('yellow light')
                      if (!el.classList.contains('repeater-input')) {
                        // console.log('green light')
                        el.value = el.dataset.previousValue
                      }

                      updateRadiosToPreviousValue()

                      updateCheckboxesToPreviousValue()


                      // const allPimMainRadios = [...document.querySelectorAll('.pim_radio_main_input')]
                      // allPimMainRadios.forEach(mian_el => {
                      //   const parent = el.closest('td')
                      //   const mainRadioChildren = [...parent.querySelectorAll('.pim_inner_radio_input')]
                      //   mainRadioChildren.forEach(inner_el => {

                      //     inner_el.removeAttribute('disabled')
                      //     inner_el.removeAttribute('checked')
                      //     inner_el.checked = false

                      //     if (inner_el.value === mian_el.value) {
                      //       // console.log(mian_el.value, inner_el.value, inner_el.value == mian_el.value,
                      //       //   inner_el.checked);

                      //       inner_el.setAttribute('checked', 'checked');
                      //       inner_el.checked = true
                      //       inner_el.dispatchEvent(new Event('change', {
                      //         bubbles: true
                      //       }));
                      //       // setTimeout(() => {
                      //       //   inner_el.click();
                      //       //   'clicked input-'
                      //       // }, 3000)

                      //       // console.log(mian_el.value, inner_el.value, inner_el.value == mian_el.value,
                      //       //   inner_el.checked);
                      //     }
                      //     inner_el.setAttribute('disabled', true)
                      //   })
                      // })

                    }
                    if (action == 'save') el.value = el.value
                  })
                }

                function makeAJAXRequestToSaveOrUpdateFormData(target) {
                  const form = target.closest('form.arrow_prod_detail_form');
                  const productId = `${document.querySelector('.main_info_source').dataset.prodId}`;
                  // console.log("Parent Form", form, form.dataset, form.dataset.tableMetaName)
                  const dynamicTableName =
                    `pim_${document.querySelector('.main_info_source').dataset.brandCode}_${form.dataset.tableMetaName}`;

                  // setting all range(min, max) values to single inputs in thier own tds
                  const allRangesMins = [...document.querySelectorAll('.range_min')]
                  allRangesMins.forEach(el => {
                    setRangeValueToSingleInput(el)
                  })

                  const allRadios = [...document.querySelectorAll('.pim_inner_radio_input:checked')]
                  allRadios.forEach(el => {
                    setRadioValueToSingleInput(el)
                  })

                  const allCheckboxes = [...document.querySelectorAll('.pim_inner_checkbox_input:checked')]
                  allCheckboxes.forEach(el => {
                    setCheckboxValueToSingleInput(el)
                  })

                  // console.log('dynamicTableName ', dynamicTableName)
                  // Enable all inputs temporarily
                  // const inputs = form.querySelectorAll('input, textarea, checkbox, radio, select');
                  const inputs = form.querySelectorAll('[pim-data-field]');

                  const abandonedClasses = ['range_min', 'range_max', 'pim_inner_radio_input',
                    'pim_inner_checkbox_input', 'pim_products_select_INNER_input', 'repeater-input'
                  ];

                  inputs.forEach(input => {
                    const inputClasses = [...input.classList]
                    // if (!input.classList.contains('range_min') && !input.classList.contains('range_max')) {
                    if (!inputClasses.some(v => abandonedClasses.includes(v))) {
                      console.log(input.name, ' #1# ', input.disabled);
                      input.removeAttribute('disabled')
                    } else {
                      console.log(input.name, ' -2- ', input.disabled);
                    }
                  });

                  const formData = new FormData(form);
                  // Display the key/value pairs
                  // for (let [key, value] of formData.entries()) {
                  // 	if(key == 'range_main_input') {
                  // 		formData.delete('range_main_input');

                  // 		const val = JSON.stringify();

                  // 		formData.set('field1', 'test');
                  // 	}
                  //   console.log(key + ', ' + value);
                  // }


                  formData.append('action', 'save_dynamic_table_data'); // Add the action
                  formData.append('dynamic_table_name', dynamicTableName);
                  formData.append('product_id', productId);
                  // Disable all inputs again
                  inputs.forEach(input => input.setAttribute('disabled', 'true'));


                  fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                      method: 'POST',
                      body: formData, // Send the FormData object directly
                    })
                    .then(response => response.json())
                    .then(data => {
                      if (data.success) {
                        // console.log('data ', data)
                        alert(data.data.message);
                      } else {
                        alert('Failed to save data. Message: ' + data.data.message);
                      }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                      handleEditButton(target, 'show')
                      handleLoader(target, 'hide')
                    });
                }

                // EDIT, Save, cancel functionality - ENDS
                document.addEventListener('click', e => {
                  if (e.target.matches('[data-bs-target="#Product-Information"]')) {
                    if (e.target.classList.contains('clicked')) return

                    var productId = Number(
                      '<?= $product_id ?>'); // Adjust this to the actual product ID you need
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

                    fetch(ajaxurl, {
                        method: 'POST',
                        // headers: {
                        //   'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        // },
                        body: new URLSearchParams({
                          action: 'get_product_information',
                          product_id: productId
                        })
                      })
                      .then(response => response.json())
                      .then(data => {
                        if (data.success) {

                          document.querySelector('.product-spec').innerHTML = data.data.product_specs_html;
                          document.querySelector('.tabs-content').innerHTML = data.data.tabs_content_html;

                          let tabs = document.querySelectorAll(".tabs .tab a");
                          let tabContents = document.querySelectorAll(".tabs-content .tab-content");

                          handleTabContentsDisplay(tabs, tabContents)

                          handleEditButton(null, 'show')
                          handleCancelDiv(null, 'hide')

                          e.target.classList.add('clicked')

                          setTimeout(() => {
                            loadPimJsState()
                          }, 1000)


                        } else {
                          alert(data.data.message);
                        }
                      })
                      .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching the product information.');
                      });
                  } else {
                    document.querySelector('[data-bs-target="#Product-Information"]').classList.remove(
                      'clicked')
                  }
                })



                // MEDIA EDIT SAVE DELETE OPERATION STARTS
                // document.addEventListener('click', e => {
                // EDIT, Save, cancel functionality - STARTS
                // {
                //   const editButton = document.querySelectorAll('#media_edit_btn')
                //   const cancelButton = document.querySelectorAll('#media_cancel_btn')
                //   const saveButton = document.querySelectorAll('#media_save_btn')
                //   const deleteButton = document.querySelectorAll('#media_delete_btn')

                //   editButton.forEach(el => {
                //     el.addEventListener('click', e => {
                //       m_handleEditButton(e.target, 'hide')
                //       m_handleCancelDiv(e.target, 'show')

                //       m_handleFields(e.target, 'enable')

                //       m_handleValue(e.target, 'edit')
                //     })
                //   })

                //   cancelButton.forEach(el => {
                //     el.addEventListener('click', e => {
                //       m_handleCancelDiv(e.target, 'hide')
                //       m_handleEditButton(e.target, 'show')

                //       m_handleFields(e.target, 'disable')

                //       m_handleValue(e.target, 'cancel')
                //     })
                //   })

                //   saveButton.forEach(el => {
                //     el.addEventListener('click', e => {
                //       m_handleCancelDiv(e.target, 'hide')

                //       m_handleFields(e.target, 'disable')

                //       m_handleValue(e.target, 'save')

                //       m_handleEditButton(e.target, 'show')
                //     })
                //   })

                //   deleteButton.forEach(el => {
                //     el.addEventListener('click', e => {
                //       m_handleCancelDiv(e.target, 'hide')

                //       m_handleFields(e.target, 'disable')

                //       m_handleValue(e.target, 'save')

                //       m_handleEditButton(e.target, 'show')
                //     })
                //   })

                // 	const media_cancel_or_save_divs = document.querySelectorAll('#media_cancel_or_save_div');
                // media_cancel_or_save_divs.forEach(div => m_handleCancelDiv(div, 'hide'))



                $ = jQuery;


                // }
                // const media_cancel_or_save_divs = document.querySelectorAll('#media_cancel_or_save_div');
                // media_cancel_or_save_divs.forEach(div => m_handleCancelDiv(div, 'hide'))
                // const mediaButton = document.querySelector('[data-bs-target="#Media-Assets"]')
                function showClickedTabOnly(target) {
                  const allMediaGroupTabs = document.querySelectorAll(`.media-groups-tabs .tab-content`)
                  const clickedTabContentId = target.closest('.media-groups-tabs ul.tabs li a').getAttribute(
                    'href')

                  allMediaGroupTabs.forEach(tabContent => tabContent.style.display = 'none')

                  document.querySelector(`.media-groups-tabs ${clickedTabContentId}`).style.display = 'block'

                }

                function addMediaAsset(target) {
                  const form = target.closest('form.single-media')
                  console.log('closest form ', form)

                  const inputs = form.querySelectorAll('input, textarea', 'select', 'radio', 'checkbox');
                  inputs.forEach(input => input.removeAttribute('disabled'));

                  const formData = new FormData(form)
                  const currentAssignmentId = form.dataset.assignmentId

                  formData.append('action', 'add_media_asset_from_prod_page')
                  formData.append('assignment_id', currentAssignmentId)

                  inputs.forEach(input => input.setAttribute('disabled', 'true'));

                  performUpload(form, formData)
                }

                function checkFormIsEmpty(activeForm = null) {
                  console.log("checking")
                  if (!activeForm) {
                    console.log('setting active form')
                    activeForm = document.querySelector('form.single-media.active_form')
                  }
                  console.log("active FORM ", activeForm)
                  const fileInput = activeForm?.querySelector('#upload-file')?.files[0]
                  const linkInput = activeForm?.querySelector('#enter-link')?.trim()?.value
                  const imageInput = activeForm?.querySelector('#upload-image')?.files[0]

                  console.log(fileInput, linkInput, fileInput?.name)
                  if (!fileInput && !linkInput && !imageInput) {
                    return true
                  }
                  return false
                }

                function performUpload(form, data) {
                  console.log('data ', data)
                  for (const item of data) {
                    console.log(item, data, data[item])
                  }


                  // if (checkFormIsEmpty(form)) {
                  //   alert("Something required is missing.")
                  //   return
                  // }

                  // if (form.querySelector('#upload-file')?.files[
                  //     0] == null || form.querySelector('#upload-image')?.files[0] == null) {
                  //   console.log("SOMETHING IS MISSING IN FORM")
                  //   return
                  // }

                  form_submit_handler(form)
                  // let uploadedImageSize = null;
                  // let uploadedFileSize = null;
                  // let fileExtension = null;



                }

                // document.addEventListener('submit', e => {
                //   if (e.target.closest('form#single-media')) {
                //     console.log("FORM SUBMITTING")
                //   }
                // })
                let mam_asset_form = {}

                // form_submit_handler();
                function mappedLinkType(selectedType) {
                  switch (selectedType) {
                    case "link":
                    case "youtube_url":
                    case "viemo_url":
                    case "video_url":
                    case "image_url":
                      return "url";
                    default:
                      return null;
                  }
                }

                function mappedFileType(selectedType) {
                  switch (selectedType) {
                    case "zip":
                    case "doc":
                    case "pdf":
                      return "zip";
                    default:
                      return null;
                  }
                }

                function setup_amazon() {
                  var bucketName = "com.altprod.arrow.raw-data";
                  var bucketRegion = "us-east-2";
                  var IdentityPoolId = "us-east-2:8bf33c68-9244-463e-ada6-211b31a8a19d";
                  AWS.config.region = bucketRegion; // Region
                  AWS.config.credentials = new AWS.CognitoIdentityCredentials({
                    IdentityPoolId: IdentityPoolId,
                  });
                  mam_asset_form.s3 = new AWS.S3({
                    apiVersion: "2006-03-01",
                    params: {
                      Bucket: bucketName
                    },
                  });
                }
                setup_amazon();

                function activateForm(target) {
                  document.querySelectorAll('form.single-media').forEach(formEl => formEl.classList.remove(
                    'active_form'))

                  target.closest('form.single-media')?.classList?.add('active_form')
                  target.closest('.add-new-media-div')?.querySelector('form.single-media').classList?.add(
                    'active_form')
                }

                function deActivateForm(target) {
                  console.log("De-activating");
                  target.closest('form.single-media').classList.remove('active_form')
                }


                function showMediaEditForm(target) {
                  target.closest('.add-new-media-div').querySelector('.add-new-media-form').style.display =
                    'block'
                  target.style.display = 'none'
                }

                function hideNewMediaForm(target) {
                  target.closest('.add-new-media-div').querySelector('.add-new-media-form').style.display =
                    'none'
                  target.closest('.add-new-media-div').querySelector('.media-add-btn').style.display =
                    'inline-block'
                }



                function upload_media(data) {
                  //alert(data);
                  //check if the files exist in that directory and Get available file names
                  //upload files to s3
                  //let the form submit with just the file names, or maybe just submit via ajax.
                  console.log("FINAL DATA SENT TO S3 => ", data)
                  mam_asset_form.upload_paths = data;
                  mam_asset_form.total_uploading = 0;
                  if (data.img_path != false && data.img_path != "") {
                    const image = document.querySelector('.active_form #upload-image')
                    const thumb = document.querySelector('.active_form #upload-thumb')

                    if (thumb) {
                      console.log("A");
                      upload_item_s3("upload-thumb", data.img_path);
                      mam_asset_form.total_uploading++;
                    } else if (image) {
                      console.log("B");
                      upload_item_s3("upload-image", data.img_path);
                      mam_asset_form.total_uploading++;
                    } else {
                      console.log("NO IMAGE OR THUMB FOUND")
                    }

                  }
                  if (data.zip_path != false && data.zip_path != "") {
                    upload_item_s3("upload-file", data.zip_path);
                    mam_asset_form.total_uploading++;
                  }
                  if (data.vid_path != false && data.vid_path != "") {
                    upload_item_s3("upload-file", data.vid_path);
                    mam_asset_form.total_uploading++;
                  }
                }


                function upload_item_s3(file_id, dest_path) {
                  console.log("UPLOADING ITEM TO __S3__", file_id, dest_path);
                  var files = document.querySelector(`#arrow-media .active_form #${file_id}`).files;
                  //alert(files);
                  //return;
                  console.log("BEFORE S3 IF");
                  if (files) {
                    var file = files[0];
                    console.log("S3 FILE => ", file, files);
                    // $("#" + file_id + "-progress").removeClass("hidden");
                    mam_asset_form.s3
                      .upload({
                          Key: dest_path,
                          Body: file,
                          ACL: "public-read",
                        },
                        function(err, data) {
                          if (err) {
                            reject("error");
                          }
                          mam_asset_form.total_uploading--;
                          if (mam_asset_form.total_uploading == 0) {
                            create_pim_asset();
                          }
                        }
                      )
                    // .on("httpUploadProgress", function (progress) {
                    // 	var uploaded = parseInt((progress.loaded * 100) / progress.total);
                    // 	$("#" + file_id + "-progress").attr("value", uploaded);
                    // });
                  }
                }



                function create_pim_asset() {
                  console.log("** FINALLY CREATING PIM___ASSET **")
                  // return
                  // var media_type = $("input[name='media_type']:checked").val();
                  // var media_type = $(".media-type").val();
                  // var media_type = mam_asset_form.fileType
                  // $(".media_assignments_select").dataset.selectedFileType ||
                  // $(".media_assignments_select").attr("data-selected-file-type");
                  // const fileType = document.querySelector(".media_assignments_select")
                  // .dataset.selectedFileType;
                  var status = $(".chck-statuses:checked")
                    .map(function() {
                      return $(this).val();
                    })
                    .get();
                  var keywords = $("#sel_keywords").val();

                  const activeForm = document.querySelector('#arrow-media').querySelector('.active_form')
                  mam_asset_form.assignmentId = activeForm.dataset.assignmentId
                  mam_asset_form.associatedItemId = activeForm.dataset.associatedItemId

                  mam_asset_form.title = activeForm.querySelector('input#title').value

                  const videoInput = activeForm.querySelector('input[data-exact-media-type="video"]')?.closest(
                    'form.single-media')?.querySelector('input#upload-file')

                  if (videoInput && videoInput.files[0]) {
                    mam_asset_form.videoDuration = videoInput?.dataset.duration
                    mam_asset_form.videoHeight = videoInput?.dataset.height
                    mam_asset_form.videoWidth = videoInput?.dataset.width
                  }


                  const sourceInput = activeForm.querySelector('input#upload-file')
                  if (sourceInput && sourceInput.files[0]) {
                    mam_asset_form.sourceExtension = sourceInput?.dataset.fileExtension
                    mam_asset_form.sourceSize = sourceInput?.dataset.fileSize
                  }


                  const imageInput = activeForm.querySelector('input#upload-image')
                  if (imageInput && imageInput.files[0]) {
                    mam_asset_form.imageExtension = imageInput?.dataset.fileExtension
                    mam_asset_form.imageHeight = imageInput?.dataset.height
                    mam_asset_form.imageWidth = imageInput?.dataset.width
                    mam_asset_form.imageSize = imageInput?.dataset.fileSize
                    mam_asset_form.sourceSize = imageInput?.dataset.fileSize
                  }

                  const thumbInput = activeForm.querySelector('input#upload-thumb')
                  if (thumbInput && thumbInput.files[0]) {
                    mam_asset_form.imageExtension = thumbInput?.dataset.fileExtension
                    mam_asset_form.imageHeight = thumbInput?.dataset.height
                    mam_asset_form.imageWidth = thumbInput?.dataset.width
                    mam_asset_form.imageSize = thumbInput?.dataset.fileSize
                  }

                  // const linkInput = activeForm.querySelector('input#enter-link')
                  // if(linkInput.value.trim() != '') {

                  // }



                  var args = {
                    sku: mam_asset_form.sku,
                    original_sku: document.querySelector('#arrow-media').dataset.prodSku,
                    title: mam_asset_form.title,
                    file_type: mam_asset_form.fileType,
                    action: 'ajax_add_ar_asset',
                    // nonce: mam_asset_form.nonce,
                    status: "active",
                    assignment_id: mam_asset_form.assignmentId,
                    // assignment_name: $(".media_assignments_select").attr(
                    //   "data-selected-assignment"
                    // ),
                    media_type: mam_asset_form.fileType,
                    // keywords: keywords,
                    // note: $("#note").val(),
                    zip_path: mam_asset_form.upload_paths.zip_path,
                    img_path: mam_asset_form.upload_paths.img_path,
                    vid_path: mam_asset_form.upload_paths.vid_path,
                    link_path: mam_asset_form.upload_paths.link_path,
                    // vid_link: $("#vid_link").val(),
                    // img_size: jQuery("#sb_asset_file_size").attr("data-size-bytes"),
                    width: mam_asset_form.imageWidth,
                    height: mam_asset_form.imageHeight,
                    // colorspace: jQuery("#sb_optimization").text(),
                    // img_format: jQuery("#sb_file_type").text(),
                    img_size: mam_asset_form.imageSize,
                    src_file_size: mam_asset_form.sourceSize,
                    fileExtension: mam_asset_form.sourceExtension || mam_asset_form.imageExtension,
                    video_duration: mam_asset_form.videoDuration,
                    // $(".media_assignments_select").dataset.selectedAssignmentId ||
                    // $(".media_assignments_select").dataset.selectedAssignment ||
                    // video_assignment: $("#video-assignment").val(),
                    // artwork_assignment: $("#artwork-assignment").val(),
                    // manual_assignment: $("#manual-assignment").val(),
                    // engineering_drawing: $("#engineering-drawings").val(),
                  };
                  console.log("FINAL ARGS : ", args);
                  $.ajax({
                    type: "POST",
                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                    data: args,
                    dataType: "json",
                    beforeSend: function() {},
                    success: function(data) {
                      if (data.code == 1) {
                        if (!alert("Asset has been added")) {
                          // window.location.reload();
                          const prod_id = document.querySelector('#arrow-media').dataset.prodId
                          refresh_media_html(prod_id)

                          // save button enable
                          console.log("REACHED AT FINAL POINT")
                          document.querySelector('.PROCESSING_FORM_BUTTON')?.removeAttribute('disabled')
                          document.querySelector('.PROCESSING_FORM_BUTTON')?.classList?.remove(
                            'PROCESSING_FORM_BUTTON')
                        }
                        // $(".progrss-bar").addClass("hidden");
                        mam_asset_form.upload_in_progress = false;
                      } else {
                        alert("Error adding asset." + data.message);
                      }
                    },
                    error: function(xhr) {
                      console.log("WEIRED ERROR_____")
                      alert(xhr.statusText + xhr.responseText);
                    },
                    complete: function() {
                      mam_asset_form.processing = false;
                      mam_asset_form = {}
                      setup_amazon()
                    },
                  });
                }




                function form_submit_handler(form) {
                  $ = jQuery;
                  console.log("FORMMMM__ ", form)
                  // constmam_asset_form = mamAssetForm;

                  function formatString(input) {
                    if (!input) return;
                    return input
                      .replace(/\s+/g, "-") // Replace whitespace with dash
                      .replace(/[()]/g, ""); // Remove parentheses
                  }
                  // $("#new_asset_frm :input").change(function () {
                  // 	$(this).closest("form").data("changed", true);
                  // });

                  // $("#new_asset_frm").submit(function(e) {
                  // e.preventDefault();



                  // if (mam_asset_form.processing == true) {
                  //   return;
                  // }


                  mam_asset_form.processing = true;
                  if (mam_asset_form.upload_in_progress == true) {
                    create_pim_asset();
                    return;
                  }
                  mam_asset_form.upload_in_progress = true;
                  mam_asset_form.upload_paths = {};
                  // mam_asset_form.form_changed = $("#new_asset_frm").data("changed");
                  // var sku = $("#sku").val();
                  // var media_type = $("input[name='media_type']:checked").val();
                  // var media_type = $(".media-type").val();
                  // var media_assignment;
                  // var supported_media_types = [
                  // 	"Images",
                  // 	"Videos",
                  // 	"Art Work",
                  // 	"Manuals",
                  // 	"Drawings",
                  // ];
                  // const sku = form.dataset.associatedItemId


                  // const sku = form.closest('#arrow-media').dataset.prodId
                  const sku = form.closest('#arrow-media').dataset.prodId
                  mam_asset_form.sku = sku
                  // const media_type = form.dataset.fileType
                  // mam_asset_form.media_type = media_type
                  const fileType = form.dataset.fileType
                  mam_asset_form.fileType = fileType
                  let mediaAssignment = null;

                  const supportedMediaTypes = [
                    "image",
                    "video",
                    "audio",
                    "vimeo_url",
                    "audio_url",
                    "image_url",
                    "youtube_url",
                    "video_url",
                    "link",
                    "zip",
                    "doc",
                    "pdf",
                  ];

                  let zipName = "";
                  let imgName = "";
                  let vidName = "";
                  let url = "";

                  const vidLink = form.querySelector("#enter-link")?.value;
                  const vidFile = form.querySelector("#upload-file")?.files[0];

                  let assetFile = form.querySelector("#upload-thumb")?.files[0] || form.querySelector(
                    "#upload-image")?.files[0];
                  console.log("ASSET+ ", assetFile)
                  if (!assetFile) {
                    // alert("Please choose an asset image to upload first.");
                    // return;
                    console.log("ASSET NOT FOUND")
                  }
                  imgName = assetFile?.name;
                  // imgName = imgName || vidFile
                  // console.log("SELECTED IMAGE NAME ", imgName, assetFile);

                  if (
                    supportedMediaTypes.includes(fileType)
                  ) {
                    if (fileType === "image") {
                      // mediaAssignment =
                      // 	form.querySelector("#image-assignment")?.value;
                    } else if (fileType === "video") {
                      // mediaAssignment =
                      // 	form.querySelector("#video-assignment")?.value;

                      if (!vidFile && !vidLink) {
                        alert("Please choose a video link or video file.");
                        return;
                      }
                      if (vidFile) {
                        vidName = vidFile.name;
                      }
                    } else if (mappedFileType(fileType)) {
                      // mediaAssignment =
                      // 	form.querySelector("#artwork-assignment")?.value;
                      const zipFile = form.querySelector("#upload-file")?.files[0];
                      if (!zipFile) {
                        alert("Please select an asset to upload first.");
                        return;
                      }
                      zipName = zipFile.name;
                    } else if (mappedLinkType(fileType)) {
                      if (!vidLink) {
                        alert("Please enter a video link.");
                        return;
                      }
                      url = vidLink;
                    }
                    var args = {
                      sku: sku,
                      media_assignment: mediaAssignment,
                      media_type: fileType,
                      action: 'ajax_check_ar_duplicate_media_assignment',
                      nonce: '<?php echo wp_create_nonce('check_duplicate'); ?>',
                      zip_path: formatString(zipName),
                      img_path: formatString(imgName),
                      vid_path: formatString(vidName),
                      link_path: formatString(url),
                    };
                    // const args = {
                    // 	sku: sku,
                    // 	media_assignment: mediaAssignment,
                    // 	media_type: media_group_name,
                    // 	// file_type: fileType,
                    // 	action: mamAssetForm.actionDuplicateChecker,
                    // 	nonce: mamAssetForm.nonce,
                    // 	zip_path: formatString(zipName),
                    // 	img_path: formatString(imgName),
                    // 	vid_path: formatString(vidName),
                    // };

                    console.log("ARGS ", args);

                    $.ajax({
                      type: "POST",
                      url: '<?php echo admin_url("admin-ajax.php"); ?>',
                      data: args,
                      dataType: "json",
                      beforeSend: function() {},
                      success: function(data) {
                        console.log(data);
                        if (data.is_duplicate == 1) {
                          var r = confirm(
                            "This image assignment for the selected SKU Already exists. Do you want to override it?"
                          );
                          if (r == true) {
                            console.log("DUP_A");
                            // $("#new_asset_frm").data("changed", false);
                            // $(".progrss-bar").removeClass("hidden");
                            upload_media(data);
                          } else {
                            console.log("DUP_B");
                            mam_asset_form.upload_in_progress = false;
                            mam_asset_form.processing = false;
                          }
                        } else {
                          console.log("DUP NOT FOUND");
                          // $("#new_asset_frm").data("changed", false);
                          // $(".progrss-bar").removeClass("hidden");
                          upload_media(data);
                          console.log("upload_media DONE");
                        }
                      },
                      error: function(xhr) {
                        alert(xhr.statusText + xhr.responseText);
                        mam_asset_form.upload_in_progress = false;
                        mam_asset_form.processing = false;
                      },
                      complete: function() {},
                    });
                  } else {
                    console.log("NOPE");
                    console.log(
                      "SOMETHING MISSING",
                      fileType,
                      supportedMediaTypes.includes(fileType),
                      // mamAssetForm.formChanged,
                      // mam_asset_form.form_changed
                    );
                    return true;
                  }

                  // });
                }

                document.addEventListener('change', e => {
                  if (e.target.closest('#upload-thumb') || e.target.closest('#upload-image')) {
                    const file = e.target.files[0];

                    if (file && file.type.startsWith('image/')) {
                      const img = new Image();

                      img.onload = function() {
                        // Set data-height and data-width attributes on the input element
                        e.target.setAttribute('data-height', img.height);
                        e.target.setAttribute('data-width', img.width);

                        console.log('Height:', img.height);
                        console.log('Width:', img.width);
                      };

                      img.src = URL.createObjectURL(file);
                    }
                  }

                  if (e.target.closest("#arrow-media input[type='file']")) {
                    let file = e.target.files[0];
                    console.log(file)
                    if (!file) return;

                    e.target.dataset.fileSize = e.target?.files[0]
                      ?.size ?
                      (e.target.files[0].size / 1024).toFixed(2) :
                      null;



                    let ext = file.name.split('.').pop();
                    e.target.dataset.fileExtension = ext;

                    if (file.type.startsWith('video/')) {
                      const video = document.createElement('video');
                      video.onloadedmetadata = function() {
                        e.target.dataset.duration = video.duration;
                        e.target.dataset.height = video.videoHeight;
                        e.target.dataset.width = video.videoWidth;
                        // URL.revokeObjectURL(url); // Clean up
                      };
                      video.src = URL.createObjectURL(file);
                    }
                  }

                  if (e.target.closest("[data-purpose='filter_seller_apps']")) {
                    // console.log('clicked seller apps')
                    const selectedApp = e.target.value
                    const allFields = e.target.closest('form#product_detail_form').querySelectorAll(
                      '[data-seller-apps]')
                    // console.log(selectedApp, selectedApp == true)
                    if (selectedApp) {
                      if (selectedApp == 0) {
                        // console.log('fale')
                        allFields.forEach(f => f.style.display = 'table-cell')
                      } else {
                        allFields.forEach(f => f.dataset.sellerApps.split(',').includes(selectedApp) ? f.style
                          .display = 'table-cell' : f.style.display = 'none')
                      }
                    }
                  }

                })




                document.addEventListener('click', e => {

                  if (e.target.closest('.media-groups-tabs ul.tabs li a')) {
                    showClickedTabOnly(e.target)
                  }

                  if (e.target.closest('.add-new-media-div .media-add-btn')) {
                    const target = e.target.closest('.add-new-media-div .media-add-btn')

                    activateForm(target)
                    showMediaEditForm(target)

                    // m_handleEditButton(e.target, 'hide')
                    // m_handleCancelDiv(e.target, 'show')

                    // m_handleFields(e.target, 'enable')

                    // m_handleValue(e.target, 'edit')
                  }

                  if (e.target.closest('.add-new-media-div .media-terminate-button')) {
                    const target = e.target.closest('.add-new-media-div .media-terminate-button')
                    hideNewMediaForm(target)
                    deActivateForm(target)
                  }

                  if (e.target.closest('.add-new-media-div .media-save-btn')) {
                    const target = e.target.closest('.add-new-media-div .media-save-btn')
                    activateForm(target)
                    // hideNewMediaForm(e.target)

                    // m_handleCancelDiv(e.target, 'hide')

                    // m_handleFields(e.target, 'disable')

                    // m_handleValue(e.target, 'save')
                    console.log("______TARGET DISABLED_________")
                    target.disabled = true
                    target.classList.add("PROCESSING_FORM_BUTTON")
                    // disableFormBeingProcessed()
                    // m_handleEditButton(e.target, 'show')
                    console.log("EMPTY OR NOT : ", checkFormIsEmpty())
                    if (!checkFormIsEmpty()) {
                      addMediaAsset(target);
                      m_handleLoader(target, 'show')
                    } else {
                      alert("SOMETHING IS MISSING")
                      document.querySelector('.PROCESSING_FORM_BUTTON').removeAttribute('disabled')
                      document.querySelector('.PROCESSING_FORM_BUTTON').classList.remove(
                        'PROCESSING_FORM_BUTTON')
                    }



                  }






                  if (e.target.closest('#media_edit_btn')) {
                    const target = e.target.closest('#media_edit_btn')
                    m_handleEditButton(target, 'hide')
                    m_handleCancelDiv(target, 'show')

                    m_handleFields(target, 'enable')

                    // m_handleValue(e.target, 'edit')
                  }

                  if (e.target.closest('#media_save_btn')) {
                    if (!checkFormIsEmpty()) {

                      const target = e.target.closest('#media_save_btn')

                      m_handleCancelDiv(target, 'hide')

                      m_handleFields(target, 'disable')

                      // m_handleValue(e.target, 'save')

                      // m_handleEditButton(e.target, 'show')

                      addMediaAsset(target);
                      target.disabled = true
                      console.log("should show loader for unassigned")
                      m_handleLoader(target, 'show')
                    } else {
                      alert("SOMETHING IS MISSING")
                    }
                    // isAllDataSame(e.target) || m_makeAJAXRequestToSaveOrUpdateFormData(e.target)

                    // if (isAllDataSame(e.target)) {
                    //   handleEditButton(e.target, 'show')
                    // } else {
                    //   m_makeAJAXRequestToSaveOrUpdateFormData(e.target)
                    // }


                  }

                  const closestForm = e.target.closest('form.single-media')
                  if ((closestForm && closestForm.contains(e.target)) && (!e.target.classList.contains(
                      '#media_cancel_btn') || !e.target.classList.contains(
                      '.media-terminate-button'))) {
                    console.log("Activating IT")
                    activateForm(e.target)
                  }

                  if (e.target.closest('#media_cancel_btn')) {
                    const target = e.target.closest('#media_cancel_btn')
                    m_handleCancelDiv(target, 'hide')
                    m_handleEditButton(target, 'show')

                    m_handleFields(target, 'disable')

                    // m_handleValue(e.target, 'cancel')

                    deActivateForm(target)
                  }

                  if (e.target.closest('#media_delete_btn')) {
                    const target = e.target.closest('#media_delete_btn')
                    // m_handleCancelDiv(e.target, 'hide')

                    //m_handleFields(e.target, 'disable')

                    const userConf = confirm("Are you sure ?")
                    userConf ? delete_media(target) : ''

                    // m_handleValue(e.target, 'save')


                    // m_handleEditButton(e.target, 'show')
                  }

                  if (e.target.closest('.media_edit_div.un_assigned #media_edit_btn')) {
                    const target = e.target.closest('.media_edit_div.un_assigned #media_edit_btn')

                    activateForm(target)
                    m_handleEditButton(target, 'hide')
                    m_handleCancelDiv(target, 'show')

                    m_handleFields(target, 'enable')

                    // m_handleValue(e.target, 'edit')

                  }



                  // })
                  if (e.target.closest('[data-bs-target="#Media-Assets"]')) {

                    if (e.target.closest('[data-bs-target="#Media-Assets"]').classList.contains('clicked'))
                      return

                    const prod_id = document.querySelector('#arrow-media').dataset.prodId

                    refresh_media_html(prod_id)

                    e.target.closest('[data-bs-target="#Media-Assets"]').classList.add('clicked')
                  } else {
                    if (document.querySelector('#arrow-media').contains(e.target)) return
                    document.querySelector('[data-bs-target="#Media-Assets"]').classList.remove('clicked')
                  }

                  // function isAllDataSame(target) {
                  //   return [...target.closest('form.arrow_prod_detail_form').querySelectorAll(
                  //       ':scope > table.table input, :scope > table.table textarea')]
                  //     .map(el => {
                  //       if (el.type == 'file') return
                  //       // console.log(el.value, el.dataset.previousValue)
                  //       return el.value === el.dataset.previousValue
                  //     })
                  //     .every(bool => bool)
                  // }
                })

                function refresh_media_html(product_id) {
                  fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                      method: "POST",
                      headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                      },
                      body: new URLSearchParams({
                        action: 'refresh_media_html',
                        product_id: product_id,
                        _wpnonce: '<?php echo wp_create_nonce('refresh_media_html_nonce'); ?>'
                      })
                    })
                    .then(response => response.json())
                    .then(data => {
                      if (data.success) {
                        // alert('success')
                        console.log(data)
                        document.querySelector('#arrow-media').innerHTML = data.data.media_html
                      } else {
                        console.log(data)
                        // alert(data.message)
                      }
                    })
                    .catch(error => {
                      console.error('Error:', error);
                    })
                    .finally(() => {
                      document.querySelectorAll(`.media-groups-tabs .tab-content`)[0].style.display = 'block'
                    });
                }

                function delete_media(target) {
                  const mediaId = target.closest('.single-media').dataset.mediaId
                  fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                      method: "POST",
                      headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                      },
                      body: new URLSearchParams({
                        action: 'delete_media',
                        media_id: mediaId,
                        _wpnonce: '<?php echo wp_create_nonce('delete_media_nonce'); ?>'
                      })
                    })
                    .then(response => response.json())
                    .then(data => {
                      if (data.success) {
                        alert('success')
                        console.log(data)
                      } else {
                        console.log(data)
                        // alert(data.message)
                      }
                    })
                    .catch(error => {
                      console.error('Error:', error);
                    })
                    .finally(() => {
                      const prod_id = document.querySelector('#arrow-media').dataset.prodId
                      refresh_media_html(prod_id)
                    });
                }

                function m_handleEditButton(target, action) {
                  if (action == 'hide') {
                    // if (!target) {
                    //   document.querySelectorAll('form.arrow_prod_detail_form').forEach(el => {
                    //     el.querySelector('#edit_btn')
                    //       .style.display = 'none'
                    //   })
                    //   return
                    // }

                    target.closest('form.single-media').querySelector('#media_edit_btn')
                      .style.display = 'none'
                  } else if (action == 'show') {
                    // if (!target) {
                    //   document.querySelectorAll('form.single-media').forEach(el => {
                    //     el.querySelector('#edit_btn').style.display = 'flex'
                    //   })
                    //   return
                    // }

                    target.closest('form.single-media')
                      .querySelector('#media_edit_btn').style.display = 'flex'
                  }
                }

                function m_handleLoader(target, action) {
                  target.querySelectorAll('input, textarea, button').forEach(el => el.disabled = true)

                  if (action == 'hide') {
                    if (!target) {
                      document.querySelectorAll('.loader').classList.add('hidden')
                      return
                    }
                    target.closest('form.single-media').querySelector('.loader')
                      .classList.add('hidden')
                  } else if (action == 'show') {
                    target.closest('form.single-media')
                      .querySelector('.loader').classList.remove('hidden')
                  }
                }

                function m_handleCancelDiv(target, action) {
                  if (action == 'hide') {
                    // if (!target) {
                    //   document.querySelectorAll('form.single-media').forEach(el => {
                    //     el.querySelector('#cancel_or_save_div').style.display = 'none'
                    //   })
                    //   return
                    // }

                    target.closest('form.single-media').querySelector(
                      '#media_cancel_or_save_div').style.display = 'none'
                  } else if (action == 'show') {
                    // if (!target) {
                    //   document.querySelectorAll('form.single-media').forEach(el => {
                    //     el.querySelector('#cancel_or_save_div').style.display =
                    //       'flex'
                    //   })
                    //   return
                    // }

                    target.closest(
                        'form.single-media').querySelector('#media_cancel_or_save_div').style.display =
                      'flex'
                  }
                }

                function m_handleFields(target, action) {
                  console.log('HANDLING FIELDs')
                  if (action == 'enable') {
                    const closestForm = target.closest('form.single-media')
                    if (closestForm.classList.contains('assigned-single-media')) {
                      closestForm.querySelector('.media-edit-field').style.display = 'block'
                    } else if (closestForm.classList.contains('un-assigned-single-media')) {
                      closestForm.querySelectorAll('input').forEach(el => el.removeAttribute('disabled'))
                    }

                    const inputsDiv = target.closest('form.single-media').querySelector('.media-edit-field')
                    if (inputsDiv) {
                      inputsDiv.style.display =
                        'block'
                    }
                  } else if (action == 'disable') {
                    const closestForm = target.closest('form.single-media')
                    if (closestForm.classList.contains('assigned-single-media')) {
                      closestForm.querySelector('.media-edit-field').style.display = 'none'
                    } else if (closestForm.classList.contains('un-assigned-single-media')) {
                      closestForm.querySelectorAll('input').forEach(el => el.disabled = true)

                    }

                    const inputsDiv = target.closest('form.single-media').querySelector('.media-edit-field')
                    if (inputsDiv) {
                      inputsDiv.style.display =
                        'none'
                    }
                  }

                  // if (action == 'enable') {
                  //   target.closest('form.single-media').querySelectorAll(
                  //       ':scope > table.table input, :scope > table.table textarea')
                  //     .forEach(el => el.removeAttribute('disabled'))
                  // } else if (action == 'disable') {
                  //   target.closest('form.single-media').querySelectorAll(
                  //       ':scope > table.table input, :scope > table.table textarea')
                  //     .forEach(el => el.setAttribute('disabled', true))
                  // }
                }

                // function m_handleValue(target, action) {
                //   target.closest('form.single-media').querySelectorAll(
                //       ':scope > table.table input, :scope > table.table textarea')
                //     .forEach(el => {
                //       // if (el.type == 'file') return
                //       // if (action == 'edit') el.dataset.previousValue = el.value
                //       // if (action == 'cancel') el.value = el.dataset.previousValue
                //       // if (action == 'save') el.value = el.value
                //       console.log('HANDLING VALUE')
                //     })
                // }

                // function m_makeAJAXRequestToSaveOrUpdateFormData(target) {
                //   const form = target.closest('form.single-media');
                //   const productId = `${document.querySelector('.main_info_source').dataset.prodId}`;
                //   const dynamicTableName =
                //     `pim_${document.querySelector('.main_info_source').dataset.brandCode}_${form.dataset.tableMetaName}`;
                //   // console.log('dynamicTableName ', dynamicTableName)
                //   // Enable all inputs temporarily
                //   const inputs = form.querySelectorAll('input, textarea', 'select', 'radio', 'checkbox');
                //   inputs.forEach(input => input.removeAttribute('disabled'));

                //   const formData = new FormData(form);
                //   // Display the key/value pairs
                //   // for (let [key, value] of formData.entries()) {
                //   //   console.log(key + ', ' + value);
                //   // }

                //   formData.append('action', 'save_dynamic_table_data'); // Add the action
                //   formData.append('dynamic_table_name', dynamicTableName);
                //   formData.append('product_id', productId);

                //   // Disable all inputs again
                //   inputs.forEach(input => input.setAttribute('disabled', 'true'));

                //   fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                //       method: 'POST',
                //       body: formData, // Send the FormData object directly
                //     })
                //     .then(response => response.json())
                //     .then(data => {
                //       if (data.success) {
                //         // console.log('data ', data)
                //         alert('Data saved successfully');
                //       } else {
                //         alert('Failed to save data: ' + data.message);
                //       }
                //     })
                //     .catch(error => console.error('Error:', error))
                //     .finally(() => {
                //       handleEditButton(target, 'show')
                //       handleLoader(target, 'hide')
                //     });
                // }

                // EDIT, Save, cancel functionality - ENDS
              })
              </script>

              <?php

              // $data = $stm->get_media_groups_and_assignments();
              // $media_structure = $stm->generate_media_groups_and_assignments_structure($data);

              // $args = array(
              // "media_structure" => json_encode($media_structure)
              //  );
              // $stm->localize_media_edit_script($args);

              ?>

              <!-- ************************************ -->
              <!-- *****  CUSTOM CODE - END ******* -->
              <!-- ************************************ -->

            </div>
            <div class="tab-pane fade" id="Price-Books" role="tabpanel" aria-labelledby="profile-tab">
              <div class="product-spec py-0">
                <!-- Retailers and Pricing sidebar widget -->
                <?php if (current_user_can("retailers_list_read")  || current_user_can('shopvac_retailers_list_read')) {
                  if ($prod->brand_slug != 'shopvac') {
                ?>
                <div class="retailers-selectors p-4">
                  <!-- <ul class="tabs" style="grid-template-columns: auto auto;">
                        <li class="tab active relatiner-tab-0" data-tab-num="0">
                          <a style="background-color:#6b80a2; color:#ffffff;" href="#tab-1">Packaging Specifications</a>
                        </li>
                        <li class="tab  retailer-tab-1" data-tab-num="1">
                          <a style="background-color:#a7c484; color:#ffffff;" href="#tab-1">Approved Marketing Copy</a>
                        </li>
                       </ul> -->
                  <div class="row" style=" grid-template-columns: auto auto;">
                    <div class="col retailer-tab" style="display: block;">
                      <a style="background-color:#6b80a2; color:#ffffff;" id="price_btn">Price Book</a>
                    </div>
                    <div class="col retailer-tab">
                      <a style="background-color:#a7c484; color:#ffffff;" id="retailer_btn">Retailer</a>
                    </div>

                  </div>
                  <div class="row mt-4">
                    <div class="col-md-12">
                      <div id="tab-pricebook" class=" sidebar-collapser">
                        <ul id="pricebook-index" class="mb-2 px-0">
                          <li id="pricebook-label" class="text-left">Select a Price Book</li>
                        </ul>
                        <div class="">
                          <select name="pb-select" id="pb-select" class="pb-selects select2 my-2 custom-select">
                            <?php
                                echo '<option value="-1"  data-target-pbc-info="pricebook_codes" selected>All</option>';
                                foreach ($price_books as $key => $price_book) {
                                  echo '<option value="' . $key . '" data-target="tbls-pbc-' . $key . '"  data-target-pbc-info="' . $price_book['book_name'] . "_" . $key . '">' . $price_book['book_name'] . '</option>';
                                } ?>
                          </select>
                        </div>
                      </div>

                      <div id="tab-retailer" class="territory_selection_box" style="display: none;">
                        <ul id="retailers-index" class="px-0 d-grid mb-2">
                          <li id="retailer-label" class="text-left">Select a Retailer</li>
                          <div class="loader-parent" style="position:absolute; width: 100%;float: right;top: -6px;">
                            <div class="loader hidden"></div>
                          </div>
                        </ul>
                        <div class="">
                          <select name="retailer-select" id="retailer-select" disabled
                            class="retailer-selects my-2 custom-select">
                            <!-- <?php
                                      foreach ($retailers_active as $key => $retailer) {
                                        echo '<option value="' . $key . '" data-target="tbls-ret-' . $key . '" data-target-contract="#tbls-contr-' . $key . '" data-target-ret-info="#tbl-ret-' . $key . '">' . $retailer['name'] . '</option>';
                                      } ?> -->
                          </select>
                          <?php if ($prod->brand_slug != 'shopvac') { ?>
                          <ul id="retailer-index"
                            class="position-relative py-4 buttons-row d-flex w-100 justify-content-end product-spec-list">
                            <li class="sb-item mr-4" data-target="tbl-contr-info"><a class="btn-blue"
                                href="#tb-contr-info">Contracts</a></li>
                            <li class="sb-item" data-target="tbl-ret-info"><a class="btn-green"
                                href="#tbl-ret-info">Retailer
                                Information</a></li>
                          </ul>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php }
                } ?>
              </div>
              <div class="table-containers w-100">
                <?php if (current_user_can('pricebooks_read') || current_user_can('shopvac_pricebooks_read')) {
                  if ($prod->brand_slug != 'shopvac') { ?>
                <?php if ($prod->brand_slug == 'shopvac') { ?>
                <!-- <h3 class="section-title">Pricebooks/Distribution</h3> -->
                <?php } else { ?>
                <!-- <h3 class="section-title">Price Books/Retailers</h3> -->
                <?php } ?>
                <div class="table-responsive">
                  <table class="table" id="tbl-pricing">
                    <!-- <thead>
													<tr>
														<th>Price Book - <span class="pricebooks_label_th">All</span></th>
														<td><?php echo $prod->sku; ?></td>
													</tr>
												</thead> -->
                    <tbody>
                      <tr>
                        <th>Price Book - <span class="pricebooks_label_th">All</span></th>
                        <td><?php
                                // var_dump($current_product);
                                echo $current_product->sku; ?></td>
                      </tr>
                      <?php
                          foreach ($price_book_codes as $price_book_code) {
                          ?>
                      <tr
                        class="pricebook_codes <?php echo $price_book_code["pricebook_name"] . "_" . $price_book_code["pricebook_id"]; ?>">
                        <th>
                          <?php echo $price_book_code['book_code'] . " - " . $price_book_code['price_code_description']; ?>
                        </th>

                        <?php
                              if (!empty($price_book_code['price']) && !empty($price_book_code['price'])) {
                              ?>
                        <td><input type="text" readonly style="width: 100%;"
                            value="<?php echo  $price_book_code['currency'] . " " . $price_book_code['price']; ?>" />

                          <?php
                                  if (!empty($price_book_code['start_date']) && !empty($price_book_code['end_date'])) {
                                  ?>
                          <input type="text" readonly style="width: 100%;"
                            value="Validity: <?php echo $price_book_code['start_date'] . " - " . $price_book_code['end_date']; ?>" />

                          <?php
                                  }
                                }
                                ?>
                        </td>
                      </tr>
                      <?php
                          }
                          ?>
                      <!-- <tr>
														<th></th>
														<td></td>
													</tr> -->
                    </tbody>
                  </table>
                </div>
                <?php } ?>
                <div class="hidden retailer-tables">
                  <!-- Retailer's Pricing -->
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Selected Retailer</th>
                          <th id="rt-retailer-name" class="ret-fields">Loading...</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- <tr>
															<th>Store SKU</th>
															<td><input id="rt-store-sku" class="ret-fields" type="text"  readonly style="width: 100%;" value=""/></td>
														</tr>
														<tr>
															<th>Internet SKU</th>
															<td><input id="rt-internet-sku" class="ret-fields" type="text"  readonly style="width: 100%;" value=""/></td>
														</tr> -->
                        <tr>
                          <th>Current Price</th>
                          <td><input id="rt-cur-price" class="ret-fields" type="text" readonly style="width: 100%;"
                              value="" />
                          </td>
                        </tr>
                        <tr>
                          <th>Contract</th>
                          <td><input id="rt-contract" class="ret-fields" type="text" readonly style="width: 100%;"
                              value="" /><?php
                                            ?></td>
                        </tr>
                        <tr>
                          <th>Price Book</th>
                          <td><input id="rt-pricebook" class="ret-fields" type="text" readonly style="width: 100%;"
                              value="" /></td>
                        </tr>
                        <tr>
                          <th>Price Book Code</th>
                          <td><input id="rt-price_code" class="ret-fields" type="text" readonly style="width: 100%;"
                              value="" /></td>
                        </tr>
                        <tr>
                          <th>Retailer Online SKU Value</th>
                          <td><input id="rt-online_sku_value" class="ret-fields" type="text" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr>
                          <th>Retailer SKU Value</th>
                          <td><input id="rt-sku_value" class="ret-fields" type="text" readonly style="width: 100%;"
                              value="" /></td>
                        </tr>
                        <!-- <tr>
															<th></th>
															<td></td>
														</tr> -->
                      </tbody>
                    </table>
                  </div>


                  <?php if ($prod->brand_slug != 'shopvac') { ?>
                  <!-- Contracts -->
                  <div class="table-responsive">
                    <table class="table" id="tb-contr-info">
                      <thead>
                        <tr>
                          <th>Contract Info</th>
                          <th id="ct-current-expired" class="ret-fields"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="description">
                          <th>Contract Number</th>
                          <td class="data-field"><input type="text" id="ct-contract-num" class="ret-fields" readonly
                              style="width: 100%;" value="Loading..." /></td>
                        </tr>
                        <tr class="description">
                          <th>Contract Description</th>
                          <td class="data-field"><textarea id="ct-contract-desc" class="form-control ret-fields"
                              style="width: 100%;" rows="4" readonly></textarea></td>
                        </tr>
                        <tr class="currency">
                          <th>Currency</th>
                          <td class="data-field"><input type="text" id="ct-contract-currency" class="ret-fields"
                              readonly style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="price">
                          <th>Price</th>
                          <td class="data-field"><input type="text" id="ct-price" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="start_date">
                          <th>Start Date</th>
                          <td class="data-field"><input type="text" id="ct-start-date" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="end_date">
                          <th>End Date</th>
                          <td class="data-field"><input id="ct-end-date" class="ret-fields" type="text" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <!-- <tr class="products_covered">
																<th>Products Covered</th>
																<td class="data-field"><input type="text" id="ct-other-products" class="ret-fields"  readonly style="width: 100%;" value=""/></td>
															</tr> -->
                        <!-- <tr>
																<th></th>
																<td></td>
															</tr> -->
                      </tbody>
                    </table>
                  </div>

                  <div class="table-responsive" id="tbl-ret-info">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Retailer Information</th>
                          <th id='rti-retailer-name' class="ret-fields">Loading...</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="first_name">
                          <th>Contact</th>
                          <td class="data-field"><input id="rti-contact-name" class="ret-fields" type="text" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="email">
                          <th>Email</th>
                          <td class="data-field"><input id="rti-contact-email" class="ret-fields" type="text" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="phone">
                          <th>Phone</th>
                          <td class="data-field"><input type="text" id="rti-contact-phone" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="address">
                          <th>Mailing Address</th>
                          <td class="data-field"><input type="text" id="rti-contact-address" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="city">
                          <th>City</th>
                          <td class="data-field"><input type="text" id="rti-contact-city" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="country">
                          <th>Country</th>
                          <td class="data-field"><input type="text" id="rti-contact-country" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="class">
                          <th>Class</th>
                          <td class="data-field"><input type="text" id="rti-class" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="pricebook">
                          <th>Price Book</th>
                          <td class="data-field"><input type="text" id="rti-pricebook" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="price_code">
                          <th>Price Book Code</th>
                          <td class="data-field"><input type="text" id="rti-price_code" class="ret-fields" readonly
                              style="width: 100%;" value="" /></td>
                        </tr>
                        <tr class="contracts">
                          <th>Contracts (Current SKU)</th>
                          <td><input type="text" id="rti-contracts" class="ret-fields" readonly style="width: 100%;"
                              value="" /></td>
                        </tr>
                        <tr class="sales_rep">
                          <th>Sales Representative</th>
                          <td><input type="text" id="rti-sales-reps" class="ret-fields" readonly style="width: 100%;"
                              value="" /></td>
                        </tr>
                        <tr>
                          <th>Territory</th>
                          <td><input type="text" id="rti-ter" class="ret-fields" readonly style="width: 100%;"
                              value="" /></td>
                        </tr>
                        <!-- <tr>
																<th></th>
																<td></td>
															</tr> -->
                      </tbody>
                    </table>
                  </div>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane fade" id="Media-Assets" role="tabpanel" aria-labelledby="contact-tab">


              <style>
              #Media-Assets .media-groups-tabs {
                /* background-color: #F4474C; */
                /* padding: 10px; */
                /* padding: 35px 80px !important; */
                /* border-radius: 5px; */
              }

              #Media-Assets .tabs {
                list-style: none;
                /* padding: 0;
                display: flex;
                gap: 0; */
                margin-bottom: 20px;

                list-style: none;
                padding: 35px 80px !important;

                display: grid;
                /* grid-template-columns: repeat(4, 1fr); */
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                ;
                /* border-bottom: 1px solid #ddd; */
                margin-bottom: 0 !important;
                color: black !important;
                flex-wrap: wrap;
                gap: 20px;

                border-bottom: 2px solid #707070;
                background-color: #ebebeb;
                /* border-bottom: 2px solid #F4474C; */
              }

              #Media-Assets .media-tabs-contents-div {
                padding: 35px 80px;
              }

              #Media-Assets th {
                font-size: 14px !important;
              }

              #Media-Assets .tabs li {
                margin-right: 10px;
              }

              #Media-Assets .tabs a {
                text-decoration: none;
                padding: 10px 15px;
                color: #fff;
                background-color: #707070;
                font-size: 15px;
                display: block;
                transition: background-color 0.3s;
                text-align: center;
              }

              #Media-Assets .media_edit_div.un_assigned .media_cancel_or_save_div {
                margin-top: 6px;
              }

              #Media-Assets .media_edit_div.assigned .media_delete_btn svg {
                width: 24px;
                height: 24px;
              }

              #Media-Assets .media-edit-field input[name=title] {
                border: 1px solid gray !important;
                margin-bottom: 10px;
              }

              #Media-Assets .add-new-media-div .media-add-btn {
                width: 100%;
                background: #707070;
                color: white;
                font-size: 14px;
                padding: 7px 5px;
                border: 0;
                margin-bottom: -20px;
              }

              #Media-Assets .add-new-media-div .cancel_or_save_new_media {
                display: flex;
                margin-top: 5px;
                gap: 5px;
              }

              #Media-Assets .add-new-media-div .media-terminate-button,
              #Media-Assets .add-new-media-div .media-save-btn {
                width: 50%;
                background: #707070;
                color: white;
                border: 0;
                font-size: 14px;
                padding: 7px 5px;
              }

              #Media-Assets th {
                padding-left: 1.2rem;
              }

              #Media-Assets .tabs a:hover,
              #Media-Assets .tabs a.active {
                background-color: #708080;
              }

              #Media-Assets .tab-content {
                display: none;
                background-color: #fff;
                padding: 10px 0;
                border-radius: 0 5px 5px 5px;
                /* border: 2px solid #F4474C; */
              }

              #Media-Assets .tab-content.active {
                display: block;
              }

              #Media-Assets .media-assignment {
                margin-bottom: 15px;
              }

              #Media-Assets button {
                border: 0;
              }

              #Media-Assets .media-assignment label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
              }

              #Media-Assets .media-assignment input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
              }
              </style>
              <!-- <?php $prod_sku = get_post_meta(get_query_var('p_id'), 'sku', true); ?> -->
              <!-- <?php $product = $stm->get_full_row_from_table('pim_products', $product_id) ?> -->
              <div id="arrow-media" data-prod-id='<?= $product_id ?>' data-prod-sku='<?= $current_product->sku ?>'
                data-old-prod-id='<?= $current_product->old_id ?>'>

                <?php $stm->display_media_fields($product_id); ?>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                  const tabs = document.querySelectorAll('#Media-Assets .tabs a');
                  const tabContents = document.querySelectorAll('#Media-Assets .tab-content');

                  tabs.forEach(tab => {
                    tab.addEventListener('click', function(event) {
                      event.preventDefault();
                      const target = document.querySelector(this.getAttribute('href'));

                      tabs.forEach(t => t.classList.remove('active'));
                      tabContents.forEach(tc => tc.classList.remove('active'));

                      this.classList.add('active');
                      target.classList.add('active');
                    });
                  });

                  // Activate the first tab by default
                  if (tabs.length > 0) {
                    tabs[0].classList.add('active');
                    tabContents[0].classList.add('active');
                  }
                });
                </script>


              </div>

            </div>
            <div class="tab-pane fade" id="Sell-Sheets" role="tabpanel" aria-labelledby="contact-tab">
              <!-- <div class="product-spec">
								<ul class="product-spec-list">
									<li>
										<a href="#tbl-sellsheets">Sell Sheets</a>
									</li>
								</ul>
							</div> -->
              <div class="table-containers assets-table my-4 w-100">
                <!-- Table Sell Sheets-->
                <?php
                if (current_user_can("sell_sheet_read") || current_user_can("shopvac_sell_sheet_read")) {
                ?>
                <!-- <h3 class="section-title" id="bottom-scroll-reference">Sell Sheets</h3> -->
                <div class="table-responsive">
                  <table class="table mt-5" id="tbl-sellsheets">
                    <tbody>
                      <tr>
                        <th>
                          <h3>Sell Sheets</h3>
                        </th>
                        <td></td>
                      </tr>
                      <tr>
                        <th>Create Sell Sheet</th>
                        <td>
                          <button type="button" class="btn btn-info btn-lg" data-toggle="modal"
                            data-target="#sellSheetsModal">Setup Options</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <?php
                }
                ?>
              </div>
            </div>
            <div class="tab-pane fade" id="Revisions" role="tabpanel" aria-labelledby="contact-tab">
              <!-- <div class="product-spec">
								<ul class="product-spec-list">
									<li>
										<a href="#tbl-revisions">Revisions</a>
									</li>
								</ul>
							</div> -->
              <?php if ($prod->brand_slug != 'shopvac') { ?>
              <!-- Table Revisions -->
              <!-- <h3 class="section-title" id="bottom-scroll-reference">Revisions</h3> -->
              <div class="table-responsive">
                <style>
                #arrow_revisions_table {
                  width: 100%;
                  text-align: center;
                }

                #arrow_revisions_table th {
                  font-size: 16px;
                }

                #arrow_revisions_table td {
                  font-size: 13px;
                }

                .popup {
                  position: fixed;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                  background-color: #fff;
                  border: 1px solid #ccc;
                  padding: 20px;
                  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                  z-index: 1000;
                }

                .popup-content {
                  max-height: 400px;
                  overflow-y: auto;
                }

                .close-popup {
                  display: block;
                  margin-bottom: 10px;
                  position: absolute;
                  right: 10px;
                  top: 10px;
                  border-radius: 10000px;
                  border: 0;
                  padding: 3px;
                }

                .close-popup svg {
                  width: 18px;
                  height: 18px;
                }





                /* Table styles */
                table {
                  width: 100%;
                  border-collapse: collapse;
                  margin-bottom: 20px;
                }

                th,
                td {
                  padding: 12px;
                  border: 1px solid #ddd;
                  text-align: left;
                }


                #arrow_revisions_table td:first-child {
                  border: 1px solid black;
                  background-color: #E4E4E3;
                  color: black;
                }

                td:not(:first-child) {
                  background-color: #fff;
                }

                /* Button styles */
                .btn {
                  padding: 8px 16px;
                  border: none;
                  cursor: pointer;
                  margin: 5px;
                  text-decoration: none;
                  display: inline-block;
                }

                .btn-view {
                  background-color: #f4474c;
                  color: #fff;
                  border: none;
                }

                .btn-restore {
                  background-color: #fff;
                  color: #f4474c;
                  border: 1px solid #f4474c;
                }

                .btn-restore:hover {
                  background-color: #f4474c;
                  color: #fff;
                }

                /* Popup styles */
                .popup {
                  /* display: none; */
                  position: fixed;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                  width: 50%;
                  background-color: #fff;
                  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                  z-index: 1000;
                  padding: 20px;
                }

                .popup-header {
                  background-color: #f4474c;
                  color: #fff;
                  padding: 10px;
                }

                .popup-body {
                  padding: 20px;
                }

                .popup-close {
                  float: right;
                  cursor: pointer;
                }

                .popup-overlay {
                  display: none;
                  position: fixed;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  background-color: rgba(0, 0, 0, 0.5);
                  z-index: 999;
                }

                #arrow_revisions_table th {
                  padding-left: 1.6rem;
                  border: 1px solid black;
                  background-color: #E4E4E3;
                  color: black;

                }

                /* form.arrow_prod_detail_form .table>:not(caption)>*>* {
                  padding-top: 0;
                  padding-bottom: 0;
                } */
                </style>
                <form method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>" id="revision_frm">
                  <?php
                    $revisions = array_reverse($stm->get_pim_product_type_revisions($product_id));

                    if (!empty($revisions)) {
                      echo '<table id="arrow_revisions_table">';
                      echo '<thead><tr>';
                      echo '<th>Revision No</th>';
                      echo '<th>Date and Time</th>';
                      echo '<th>Field Group</th>';
                      echo '<th>Action</th>';
                      echo '</tr></thead>';
                      echo '<tbody>';
$all_revs = [];
                      foreach ($revisions as $index => $revision) {
                        $revision_no = $index + 1;
                        $date_time = $revision['date_time'];
                        $previous_values_json_data = json_decode($revision['previous_value'], true);
												$all_revs[] = [
													'id' => $revision['id'],
													'data' => $previous_values_json_data
												];
                        $field_group = $previous_values_json_data['field_group_name'];
                        $restored = $previous_values_json_data['restored'];
                        $previous_value = $previous_values_json_data['previous_value'];
                        // $field_group = reset($previous_values)['table_meta_name'];
                        // $field_groups = array_unique(array_column($previous_values, 'table_meta_name'));

                        echo '<tr>';
                        echo '<td>' . $revision_no . '</td>';
                        echo '<td>' . $date_time . '</td>';
                        echo '<td>' . $field_group . '</td>';
                        echo '<td>';
                        echo '<button type="button" class="btn-view view-revision" data-revision-id="' . $revision['id'] . '" >View</button>';
                        echo '<button style="margin-left: 8px;" type="button" class="btn-restore restore-revision-btn" data-revision-id="' . $revision['id'] . '">Restore</button>';
                        echo '</td>';
                        echo '</tr>';
                      }

                      echo '</tbody>';
                      echo '</table>';
                    } else {
                      echo '<table id="arrow_revisions_table">';
                      echo '<thead><tr>';
                      echo '<td style="font-size: 18px;">No revisions found.</td>';
                      echo '</tr></thead>';
                      echo '<tbody>';
                      echo '</tbody>';
                      echo '</table>';
                    }
                    ?>

                  <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    // const viewButtons = document.querySelectorAll('.view-revision');
                    // const fieldGroup = "<?php echo $field_group ?>";
                    // viewButtons.forEach(button => {
                    //   button.addEventListener('click', function() {

                    //   });
                    // });

                    function showPopup() {
                      const popup = document.createElement('div');
                      popup.classList.add('revision-popup')
                      popup.classList.add('popup');
                      popup.innerHTML = `<div class="popup-content">
                                <button class="close-popup">
																<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
</svg>

																</button>
																<div class="revision-popup-body">
																	<div class="revision-loader-body d-flex flex-column justify-content-center align-items-center">
																		<div class="popup-loader"></div>
																	</div>
																</div>
                           </div>`;
                      document.body.appendChild(popup);

                      document.querySelector('.close-popup').addEventListener('click', () => {
                        document.body.removeChild(popup);
                      });
                    }


                    const revisionButton = document.querySelector('[data-bs-target="#Revisions"]')
                    // revisionButton.addEventListener('click', e => {
                    //   console.log("clicked Revisions")
                    // })

                    document.addEventListener('click', function(e) {


                      if (!e.target.closest('[data-bs-target="#Revisions"]')) {
                        document.querySelector('[data-bs-target="#Revisions"]').classList.remove('clicked')
                      } else {
                        if (e.target.closest('[data-bs-target="#Revisions"]').classList.contains('clicked'))
                          return

                        let product_id = Number('<?= get_query_var('p_id') ?>'); // Adjust selector as needed

                        if (e.target.classList.contains('clicked')) return

                        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                            method: 'POST',
                            body: new URLSearchParams({
                              action: 'get_latest_revisions',
                              product_id: product_id
                            })
                          })
                          .then(response => {
                            if (!response.ok) {
                              throw new Error('Network response was not ok');
                            }
                            return response.json();
                          })
                          .then(data => {
                            if (data.success) {
                              document.querySelector('#arrow_revisions_table thead').innerHTML =
                                `
																<tr><th>Revision No</th><th>Date and Time</th><th>Field Group</th><th>Action</th></tr>
																`;

                              document.querySelector('#arrow_revisions_table tbody').innerHTML =
                                renderRevisions(data.data
                                  .revisions);
                            } else {
                              // alert(data.data.message);
                              console.log(data.data.message)
                            }
                            e.target.closest('[data-bs-target="#Revisions"]').classList.add('clicked')
                          })
                          .catch(error => {
                            // alert('An error occurred while fetching the latest revisions: ' + error.message);
                            console.log('An error occurred while fetching the latest revisions: ' + error
                              .message);
                          });
                      }

                      // let hp_data_1 =
                      //   `<?php echo (stripslashes(json_encode($all_revs, JSON_UNESCAPED_UNICODE ))) ?>`;
                      // hp_data_1 = hp_data_1[0].data.replace(/\\/g, '')

                      // console.log('first ORIGIN ==> ', JSON.parse(hp_data_1[0].data))

                      if (e.target.matches('.view-revision')) {

                        let previousValues;
                        showPopup();

                        fetch('<?= admin_url("admin-ajax.php") ?>', {
                            method: 'POST',
                            body: new URLSearchParams({
                              revision_id: e.target.dataset.revisionId,
                              action: 'get_specific_revision',
                              _wpnonce: '<?= wp_create_nonce('get_specific_revision') ?>'
                            })
                          })
                          .then(data => data.json())
                          .then(res => {
                            if (res.success) {
                              console.log('res => ', JSON.parse(res.data.result.previous_value))
                              previousValues = JSON.parse(res.data.result.previous_value)


                              console.log("JSON str => ", previousValues)
                              const bullet1ValueEn = previousValues.fields;
                              console.log("Bullet 1 (English):", bullet1ValueEn);
                              let heading = document.createElement('h3');
                              let popupContent = `
												<h3 style="margin-top: -26px;">Field Group Name: ${previousValues.field_group_name}</h3>
												<ul style="font-size:14px;">`;

                              for (const [fieldName, fieldData] of Object.entries(previousValues
                                  .fields)) {
                                popupContent += `<li>
                                    <strong>${fieldData.field_name || fieldName}:</strong>
                                    ${fieldData.previous_value == null || fieldData.previous_value == "" ? 'EMPTY' : fieldData.previous_value.replace(/\\/g, '')}
                                 </li>`;
                              }

                              popupContent += '</ul>';

                              const popup = document.querySelector(
                                '.popup.revision-popup .revision-popup-body');
                              popup.innerHTML = popupContent;



                            } else {
                              console.log("failed to get SPECIFIC revision");
                            }
                          })
                          .catch((err) => {
                            console.log("revision error => ", err);
                          })
                          .finally(() => {

                          })

                        // let php_data =
                        //   `<?php echo (stripslashes(json_encode($previous_values_json_data, JSON_UNESCAPED_UNICODE ))); ?>`;
                        // php_data = php_data.replace(/\\/g, '')
                        // console.log('original ==> ', php_data)
                        // const stringifiedData = JSON.parse(JSON.stringify(e.target.getAttribute(
                        //   'data-revision')))
                        // const convertedValues = JSON.parse(e.target.getAttribute(
                        //   'data-revision'))
                        // const convertedValues = JSON.parse(php_data);
                        // const convertedValues = (e.target.getAttribute(
                        // 'data-revision'))
                        // const str = e.target.getAttribute('data-revision');
                        // const slashStrippedStr = str.replace(/\\/g, "");

                      }

                      if (e.target.matches('.restore-revision-btn')) {
                        const revisionId = e.target.getAttribute('data-revision-id');

                        if (confirm('Are you sure you want to restore this revision?')) {
                          fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                              method: 'POST',
                              body: new URLSearchParams({
                                action: 'restore_revision',
                                revision_id: revisionId,
                              })
                            })
                            .then(response => response.json())
                            .then(data => {
                              if (data.success) {
                                // alert('Revision restored successfully.');
                                console.log('Revision restored successfully.')
                              } else {
                                // alert('Failed to restore the revision: ' + data.message);
                                console.log('Failed to restore the revision: ' + data.message)
                              }
                            });
                        }
                      }
                    });

                    function renderRevisions(revisions) {
                      let html = ''
                      revisions.reverse().forEach(function(revision, index) {
                        let fields = JSON.stringify(revision.previous_value);
                        let dataRevision = fields.replace(/\\/g, '').replace(/^"|"$/g, '');
                        let prevValue = JSON.parse(revision.previous_value);
                        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${revision.date_time}</td>
                <td>${prevValue.field_group_name}</td>
                <td>
                    <button type="button" class="btn-view view-revision" data-revision-id="${revision.id}">View</button>
                    <button style="margin-left: 8px;" type="button" class="btn-restore restore-revision-btn" data-revision-id="${revision.id}">Restore</button>
                </td>
            </tr>
        `;
                      });
                      return html;
                    }
                  });
                  </script>

                  <!-- <table class="table mt-5" id="tbl-revisions">
                    <tbody>
                      <tr>
                        <th>
                          <h3>Revisions</h3>
                        </th>
                        <td></td>
                        <td></td>
                      </tr>
                      <?php

                      // Fields revisions
                      $params = array(
                        'limit' => -1,
                        'where' => 'd.product_id = ' . get_query_var('p_id')
                      );
                      $revisions = pods('fields_revision', $params);
                      if ($revisions->total() > 0) {
                        while ($revisions->fetch()) { ?>
                      <tr>
                        <th>Revision</th>
                        <td><button class="btn btn-info btn-lg restore-revision"
                            data-id="<?php echo $revisions->field('id'); ?>">Restore Revision</button></td>
                        <td><?php echo get_the_date('Y-m-d', $revisions->field('id')); ?></td>
                        <td>
                          <?php
                          if ($revisions->field('section_name') == 'ajax_prod_specs_edit') {
                            echo "Product Specifications";
                          } else {
                            echo "Marketing Copies";
                          }
                          ?>
                        </td>
                      </tr>
                      <?php }
                      }

                      // Assets revisions
                      $assets_IDs = $sm->assets->get_asset_id_by_product_id(get_query_var('p_id'));
                      if (!empty($assets_IDs)) {
                        foreach ($assets_IDs as $asset) {
                          //echo $asset;
                          $revisions = $sm->assets->get_revision_history_by_id($asset);
                          //print_r($revisions);
                          if (!empty($revisions)) {
                            foreach ($revisions as $key => $rev) { ?>
                      <tr>
                        <th>Revision</th>
                        <td>
                          <a data-none="<?php echo wp_create_nonce("mam_Rev"); ?>"
                            data-asset="<?php echo $rev->asset_id; ?>" data-revision="<?php echo $key; ?>"
                            class="btn btn-info btn-lg restore-asset-revision"
                            id="asset-restore-btn-<?php echo $key; ?>">Restore Revision</a>
                        </td>
                        <td><?php echo get_the_date('Y-m-d', $key); ?></td>
                        <td>
                          <?php
                              $asset_type = get_the_terms($rev->asset_id, 'media_type');
                              if ($asset_type[0]->name == 'Manuals') {
                                echo 'Manuals';
                              }
                              if ($asset_type[0]->name == 'Images') {
                                $image_assignment = get_the_terms($rev->asset_id, 'image_assignment');
                                echo "Images - " . $image_assignment[0]->name;
                              }
                              if ($asset_type[0]->name == 'Art Work') {
                                $aw_assignment = get_the_terms($rev->asset_id, 'art_work_assignment');
                                echo "Art Work - " . $aw_assignment[0]->name;
                              }
                              if ($asset_type[0]->name == 'Videos') {
                                $vid_assignment = get_the_terms($rev->asset_id, 'video_assignment');
                                echo "Videos - " . $vid_assignment[0]->name;
                              }
                          ?>
                        </td>
                      </tr>
                      <?php }
                          }
                        }
                      }
                      ?>
                    </tbody>
                  </table> -->
                </form>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 content-area" id="primary">
        <main class="site-main" id="main" role="main">
          <div class="row">
            <div class="col-md-12 col-lg-12">
              <!-- Content -->
              <div class="table-containers">
                <?php if (get_post_status($prod->prod_id) == 'inactive') { ?>
                <div class="row">
                  <div class="col-12 text-right">
                    <button data-ajax="<?php echo admin_url('admin-ajax.php'); ?>"
                      data-id="<?php echo $prod->prod_id; ?>" class="btn btn-primary btn-lg submit-for-approval">Submit
                      For Approval</button>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </main><!-- #main -->
      </div><!-- #primary -->
    </div><!-- .row end -->
  </div><!-- #content -->
</div>

<!-- Removed child info popup -->
<!-- Modal -->
<div class="modal fade hierarchy-modal" id="exampleModal" tabindex="-1" role="dialog"
  aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog mt-5" role="document">
    <div class="modal-content mt-5">
      <div class="modal-header">
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
          <span class="font-24" aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>
<!-- End -->

<!-- #full-width-page-wrapper -->
<?php get_template_part("template-parts/sell-sheets-modal"); ?>

<?php get_footer(); ?>

<style>
.retailers-selectors li {
  list-style: none;
}

.product-spec-list a {
  color: #fff !important;
  font-family: SANS-SERIF;
  text-transform: capitalize;
  /* border: 1px solid grey; */
  font-weight: normal;
  /* margin-left: 19px; */
  min-width: 162px;
  text-align: center;
  text-decoration: none;
  padding: 10px 15px;
  font-size: 14px;
  line-height: 1.2;
}

.btn-blue {
  background-color: #839cc4;
}

.btn-green {
  background-color: #A7C483;
}

.btn-gray {
  background-color: #707070;
}

.btn-red {
  background-color: #f4474c;
}

.product-spec-list .btn-white {
  background-color: #fff;
  color: #6c6c6c !important;
}

#retailer-index {
  top: 7px;
}

#Product-Information.tab-pane h3 {
  /* margin: 30px 0 -60px 0; */
}

#Media-Assets .tool_tip {
  color: grey;
}

.media-table .form-btns {
  margin: 15px 0 !important;
}

.prod-info .table-containers .frm-btn {
  display: block;
  width: max-content;
  margin-left: auto;
}

.table-containers .table-responsive .form-btns {
  margin: 0;
}

.table-containers table thead th:first-of-type:after {
  background-color: #E4E4E3 !important;
}
</style>

<script>
jQuery(document).ready(function() {
  jQuery('.pb-selects').select2({
    width: '100%' // need to override the changed default
  });
  jQuery(
    '.retailer-selects').select2({
    width: '100%' // need to override the changed default
  });

  $('.retailer-selects').on('select2:close', function(e) {
    let value = $(this).find(':selected').attr("value");
    $(".retailer-tables").removeClass("hidden");
    $("#retailer-label").addClass("sb-item").addClass("selecteditem");

    var aTag = $(".retailer-tables");
    $('html,body').animate({
      scrollTop: aTag.offset().top - 50
    }, 'slow');

    $(".prod-sidebar").removeClass("selected");
    $(this).closest(".prod-sidebar").addClass("selected");

    //send ajax request and collect retailer information
    let url = '<?php echo admin_url("admin-ajax.php"); ?>';
    let args = {
      action: 'ar_retailer_info',
      ret_id: value,
      prod_id: '<?php echo $current_product->id; ?>',
    };

    $.ajax({
      type: 'POST',
      url: url,
      data: args,
      beforeSend: function() {
        // setting a timeout
        $(".ret-fields").text("");
        $(".ret-fields").val("");
        $("#rt-retailer-name").val("Loading...");
        $("#rti-retailer-name").val("Loading...");


      },
      success: function(data) {
        // $("#dir_listing").html(data);
        if (data.code == 1) {
          if (!$.isEmptyObject(data.message)) {
            $("#rt-retailer-name").text(data.message.name);
            // $("#rt-store-sku").val("");//
            // $("#rt-internet-sku").val("");//
            var price = 0;
            var uom = "";
            var is_contract_pr_active = false;
            // $("#rt-cur-price").val(data.retailer);
            if (Array.isArray(data.message.contracts) && data.message.contracts.length > 0 && data.message
              .contracts[0].is_active != false) {



              $("#rt-contract").val(data.message.contracts[0].contract_number + " - " + data.message
                .contracts[0].contract_description);

              $("#ct-current-expired").text();
              $("#ct-contract-num").val(data.message.contracts[0].contract_number);
              $("#ct-contract-desc").val(data.message.contracts[0].contract_description);
              $("#ct-contract-currency").val(data.message.contracts[0].currency);

              price = data.message.contracts[0].currency + " " + data.message.contracts[0].price;
              uom = " /" + data.message.contracts[0].pricing_um + " - Contract Price";
              is_contract_pr_active = data.message.contracts[0].price_active;
              $("#ct-price").val(price + uom);
              // $("#ct-currency").val(data.retailer);
              $("#ct-start-date").val(data.message.contracts[0].start_date);
              $("#ct-end-date").val(data.message.contracts[0].end_date);
              // $("#ct-other-products").val(data.message.contracts[0].contract_description);
              for (var i = 0; i < data.message.contracts.length; i++) {
                $("#rti-contracts").val(data.message.contracts[i].contract_number + " - " + data.message
                  .contracts[i].contract_description + ", ");
              }

            } else {
              $("#rt-contract").text("Not Found");
              $("#ct-contract-num").val("Not Found");
            }

            if (!$.isEmptyObject(data.message.price_book)) {
              $("#rt-pricebook").val(data.message.price_book.book_name);
              $("#rti-pricebook").val(data.message.price_book.book_name);
            } else {
              $("#rt-pricebook").val("N/A");
              $("#rti-pricebook").val("N/A");
            }

            if (!$.isEmptyObject(data.message.price_book_code)) {
              $("#rt-price_code").val(data.message.price_book_code.book_code + " - " + data.message
                .price_book_code.price_code_description);
              $("#rti-price_code").val(data.message.price_book_code.book_code + " - " + data.message
                .price_book_code.price_code_description);
              if (price == 0 || is_contract_pr_active == false) {
                var currency = "";
                if (data.message.price_book_code.currency != null) {
                  currency = data.message.price_book_code.currency;
                }
                price = currency + " " + data.message.price_book_code.price;
                uom = " /" + data.message.price_book_code.uom + " - Price Book Price";
              }

            } else {
              $("#rt-price_code").val("N/A");
              $("#rti-price_code").val("N/A");
            }
            $("#rt-cur-price").val(price + uom);

            $("#rti-retailer-name").text(data.message.name);
            $("#rti-contact-name").val(data.message.first_name);
            $("#rti-contact-email").val(data.message.email);
            $("#rti-contact-phone").val(data.message.phone);
            $("#rti-contact-address").val(data.message.mailing_address);
            $("#rti-contact-city").val(data.message.city);
            $("#rti-contact-country").val(data.message.country);
            $("#rti-class").val(data.message.class);
            $("#rt-online_sku_value").val(data.message.price_book_code.retailer_online_sku_value);
            $("#rt-sku_value").val(data.message.price_book_code.retailer_sku_value);

            if (!$.isEmptyObject(data.message.sales_rep)) {
              $("#rti-sales-reps").val("(" + data.message.sales_rep.rep_id + ") " + data.message.sales_rep
                .rep_name);
            }



            if (!$.isEmptyObject(data.message.territory)) {
              $("#rti-ter").val("(" + data.message.territory.ter_id + ") " + data.message.territory
                .ter_name);
            }
          } else {
            alert("Record not found.");
          }
          // $("#dir_listing").html(data.message);
          //update location bar
          // MAMFilters.current_directory = $(this).attr("data-path");
        } else {
          alert("Record not found.");
        }

      },
      error: function(xhr) { // if error occured
        alert(xhr.statusText + xhr.responseText);
      },
      complete: function() {
        // MAMFilters.dir_filter = "";
        // MAMFilters.triggerFilterChangeEvent();
        // return false;
      },
      dataType: 'json'
    });



  });


  $('.pb-selects').on('select2:close', function(e) {
    var val = $(this).find(':selected').text();
    var val2 = $(this).find(':selected').attr("data-target-pbc-info");

    $("#pricebook-label").addClass("sb-item").addClass("selecteditem");

    var aTag = $("#tbl-pricing");
    $('html,body').animate({
      scrollTop: aTag.offset().top - 50
    }, 'slow');

    $(".prod-sidebar").removeClass("selected");
    $(this).closest(".prod-sidebar").addClass("selected");

    $(".pricebook_codes").addClass("hidden");
    $("." + val2).removeClass("hidden");
    $(".pricebooks_label_th").text(val);
  });

  $('[data-bs-target="#Price-Books"]').on('click', function() {
    document.querySelector("#price_btn").click()
    let url = '<?php echo admin_url("admin-ajax.php"); ?>';
    let productId = '<?php echo $current_product->id; ?>';
    $('#retailers-index .loader-parent .loader').removeClass('hidden');

    $.ajax({
      type: 'POST',
      url: url,
      data: {
        action: 'get_retailer_list',
        productId: productId
      },
      success: function(data) {
        // console.log("Raw response data: ", data);
        let retailers;

        try {
          retailers = (typeof data === "string") ? JSON.parse(data) : data;
        } catch (error) {
          console.error("Error parsing JSON:", error);
          return;
        }

        $('#retailer-select').empty();

        $.each(retailers, function(key, retailer) {
          let option = $('<option>', {
            value: retailer.id,
            text: retailer.name,
            'data-target': 'tbls-ret-' + retailer.id,
            'data-target-contract': '#tbls-contr-' + retailer.id,
            'data-target-ret-info': '#tbl-ret-' + retailer.id
          });

          $('#retailer-select').append(option);
        });
        $("#retailer-select").removeAttr('disabled');
        $('#retailers-index .loader-parent .loader').addClass('hidden');
      },
      error: function(xhr, status, error) {
        console.error("AJAX error:", status, error);
      }
    });
  });

  //tabs price book and retailer
  $('#retailer_btn').on('click', function() {
    $('#tbl-pricing').hide();
    $('#tab-pricebook').hide();
    $('#tab-retailer').show();
    $('.retailer-tables ').show();
  });
  $('#price_btn').on('click', function() {
    $('#tbl-pricing').show();
    $('#tab-pricebook').show();
    $('#tab-retailer').hide();
    $('.retailer-tables').hide();
  });



});
</script>

<script>
function loadPimJsState() {
  // Initialize Select2 for the product select field
  $('.pim_products_select_INNER_input').select2({
    placeholder: "Select products...",
    multiple: true,
    // allowClear: true
  });

  // Listen for change events in the Select2 field
  $('.pim_products_select_INNER_input').on('change', function() {
    // Get the selected values from the Select2 field
    var selectedValues = $(this).val();

    // Encode the selected values to JSON
    var jsonEncodedValues = JSON.stringify(selectedValues);

    // Find the related hidden input (main input) for this select field
    var mainInput = $(this).siblings('.pim_products_select_MAIN_input');

    // Set the JSON-encoded values in the hidden input
    mainInput.val(jsonEncodedValues);
  });



  // Cancel button click handler (assuming there's a Cancel button with a class '.cancel-btn')
  $('.cancel_btn').on('click', function() {
    // Find the closest parent form (#product_detail_form)
    var form = $(this).closest('#product_detail_form');

    // Find all hidden inputs and Select2 elements within this form and restore their original values
    form.find('.pim_products_select_MAIN_input').each(function() {
      var originalValue = $(this).attr('data-db-value'); // Get the original (DB) value

      // Restore the value in Select2 dropdown within this form
      var select2Element = $(this).siblings('.pim_products_select_INNER_input');
      var originalArray = JSON.parse(originalValue); // Parse the JSON back to array

      // Set the original value in Select2
      select2Element.val(originalArray).trigger('change');

      // Also restore the hidden input to its original state
      $(this).val(originalValue);
    });

  });




  // ***************
  // ***************
  //REPEATER INPUTS
  // ***************
  // ***************

  // Add a new repeater row
  $('.add-repeater-row').on('click', function() {
    var fieldName = $(this).data('field-name');
    var repeaterWrapper = $('#repeater_' + fieldName);

    // Get the next index
    var lastIndex = repeaterWrapper.find('.repeater-row').length;

    // Create new row HTML
    var newRow = `
            <div class="repeater-row" data-index="${lastIndex}">
                <input pim-data-field pim-data-permitted type="text" name="${fieldName}[${lastIndex}]" placeholder="Enter value" class="repeater-input">
                <button pim-data-field pim-data-permitted  type="button" class="remove-repeater-row">-</button>
            </div>
        `;

    // Append the new row to the repeater wrapper
    repeaterWrapper.append(newRow);

    // Update the hidden input with the new values
    updateRepeaterValues(fieldName);
  });

  // Remove a repeater row
  $(document).on('click', '.remove-repeater-row', function() {
    var fieldName = $(this).closest('.repeater-wrapper').attr('id')?.replace('repeater_', '');
    $(this).closest('.repeater-row').remove();

    // Update the hidden input with the new values
    updateRepeaterValues(fieldName);
  });

  // Update hidden input when repeater input changes
  $(document).on('input', '.repeater-input', function() {
    var fieldName = $(this).closest('.repeater-wrapper').attr('id').replace('repeater_', '');
    updateRepeaterValues(fieldName);
  });

  // Function to update the hidden input value (JSON encoded)
  function updateRepeaterValues(fieldName) {
    var repeaterWrapper = $('#repeater_' + fieldName);
    var values = [];

    repeaterWrapper.find('.repeater-input').each(function() {
      values.push($(this).val());
    });

    // Update the hidden input
    $('input[name="' + fieldName + '"]').val(JSON.stringify(values));
  }

  // Cancel button logic (restore previous state)
  $('.cancel_btn').on('click', function() {
    var form = $(this).closest('#product_detail_form');

    // Restore repeater values
    form.find('.repeater-main-input').each(function() {
      var originalValue = $(this).attr('data-db-value');
      var originalArray = JSON.parse(originalValue); // Parse the DB value

      var repeaterWrapper = $(this).siblings('.repeater-wrapper');
      repeaterWrapper.empty(); // Remove all current rows

      // Recreate the rows based on the original values
      for (var i = 0; i < originalArray.length; i++) {
        var newRow = `
                    <div class="repeater-row" data-index="${i}">
                        <input pim-data-field pim-data-permitted type="text" name="${$(this).attr('name')}[${i}]" value="${originalArray[i]}" placeholder="Enter value" class="repeater-input">
                        <button pim-data-field pim-data-permitted type="button" class="remove-repeater-row">-</button>
                    </div>
                `;
        repeaterWrapper.append(newRow);
      }

      // Update the hidden input to reflect the restored values
      $(this).val(originalValue);
    });
  });




  // $('.cancel_btn').on('click', function() {
  //   // Restore the original value from 'data-db-value'
  //   $('.pim_checkbox_main_input').each(function() {
  //     var originalValue = $(this).attr('data-db-value'); // Get the original (DB) value

  //     // Restore the value in Select2 dropdown
  //     var select2Element = $(this).siblings('.pim_inner_checkbox_input');
  //     var originalArray = JSON.parse(originalValue); // Parse the JSON back to array

  //     // Set the original value in Select2
  //     select2Element.val(originalArray).trigger('change');

  //     // Also restore the hidden input to its original state
  //     $(this).val(originalValue);
  //   });
  // });

}

jQuery(document).ready(function($) {
  loadPimJsState()

});
</script>

<style>
.select2-selection.select2-selection--multiple {
  display: flex !important;
  height: auto !important;
  flex-direction: column;
}


.details-prod button.select2-selection__choice__remove {
  width: initial;
}

.details-prod li.select2-selection__choice {
  height: 22px !important;
}

#select2-products_select-container {
  display: flex !important;
  margin-bottom: 0;
  flex-wrap: wrap !important;
}

.select2-container {
  width: 100% !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__display {
  margin-left: 15px !important;
  font-size: 11px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__rendered {
  display: flex !important;
  width: 100% !important;
  min-width: 100% !important;
  flex-wrap: wrap !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
  min-width: 80px !important;
  max-width: 100px !important;
}
</style>


<!-- Repeater row styles -->
<style>
.repeater-wrapper {
  /* margin-bottom: 20px; */
}

.repeater-row {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.repeater-row input {
  width: 80%;
  margin-right: 10px;
}

.repeater-row button {
  background-color: #ff6666;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.add-repeater-row {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.add-repeater-row:hover,
.repeater-row button:hover {
  opacity: 0.9;
}

.add-repeater-row:disabled:hover,
.repeater-row button:disabled:hover {
  opacity: initial;
  cursor: initial;
}

.add-repeater-row {
  width: 100%;
}
</style>
