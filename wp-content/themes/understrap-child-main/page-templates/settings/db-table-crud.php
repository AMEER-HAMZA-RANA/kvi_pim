<?php

/**
 * Template Name: Settings DB Table Edit Page
 *
 *
 * @package arrow
 */


// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header("blank-subheader");
$container = get_theme_mod('arrow_container_type');
$stm = SettingsManager::GI();
// $stm->g_importer->load_js();
// $field_groups_structure = $stm->generate_field_groups_structure(1742247);
// echo "<pre>";
// echo json_encode($field_groups_structure, JSON_PRETTY_PRINT);
// phpinfo();
// die();
?>

<style>
.importer_form {
  font-size: 14px;
}

.add-fields {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background: #F6F7F7;
  border-radius: 10px;
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

.select2-selection.select2-selection--multiple {
  display: flex !important;
  height: auto !important;
}


.add-fields button.select2-selection__choice__remove {
  width: initial;
}

.add-fields li.select2-selection__choice {
  height: 22px !important;
}

.add-fields button:hover {
  background-color: #0056b3;
}

.hidden {
  display: none !important;
}

#add_field_btn {
  padding: 6px 10px;
  border-radius: 6px;
  /* border: 2px solid white; */
  /* background: #000; */
  color: white;
  font-size: 20px;
  margin-left: auto;
  display: block;
  margin-bottom: 0px;
  /* width: 100%; */
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
  /* width: 100%; */
}

.option_field input[type="text"] {
  display: inline-block;
  width: 94%;
}

.add_remove_options {
  display: flex;
  gap: 20px;
}

.existing_tables_table {
  margin: 0 auto;
  padding: 20px 0px;
  background: #F6F7F7;
  border-radius: 10px;
}

tr,
th,
tr td {
  border: none !important;
}

tr td p a,
tr td p a:hover {
  /* color: #656161; */
  text-decoration: none;
}

tr td p button,
tr td p button:hover {
  text-decoration: none !important;
  border: none;
  background-color: transparent;
  padding: 0;
}

.table {
  max-height: 720px;
  overflow-y: scroll;
  display: block;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: #F6F7F7 !important;
}

.table-striped tbody tr:nth-of-type(even) {
  background-color: #fff !important;
}

thead tr {
  background-color: white;
}

.table thead th {
  vertical-align: middle !important;
}

.btn-danger {
  color: #fff;
  background: #811C19;
  border-color: #811C19;
}

form {
  margin: 0;
}

.existing_tables_table .row {
  margin-top: 10px;
  margin-bottom: 30px;
}

td p {
  margin-top: 1rem;
}

.btn-delete {
  display: block;
  width: 70px;
  color: white;
  border-radius: 4px;
  padding: 4px 10px;
  font-size: 12px;
  text-decoration: none;
  text-align: center;
}

.actions {
  display: flex;
  gap: 10px;
  align-items: center;
}

.btn-link {
  font-weight: 400;
  color: #0d6efd !important;
  text-decoration: underline;
}

#select2-products_select-container {
  display: flex !important;
  margin-bottom: 0;
  flex-wrap: wrap !important;
}
</style>

<?php

// get table id from url query params and tbale_meta from db using tbale_id
$table_meta_id = intval(get_query_var('tbl_id'));
$table_meta = $stm->get_full_row_from_table('pim_table_metas', $table_meta_id);

$stm->return_if_empty_or_not_found($table_meta_id, 'Table Id not found.');
$stm->return_if_empty_or_not_found($table_meta, 'Table Meta not found.');

// get dynamic table name to be created, updated, modified
$dynamic_table_name = $stm->get_pim_dynamic_table_name($table_meta);

// **********************
// Handle the delete action
// **********************
if (isset($_POST['delete_field_id'])) {
	echo "A";
  $stm->handle_field_delete_in_db($_POST['delete_field_id'], $dynamic_table_name);
  // Redirect to avoid resubmission
  $stm->redirect_to_same_page();
  exit;
}

// **********************
// Handle form submission for adding/updating fields in pim_field_metas table + generating dynamic DB tables and adding/updating their columns
// **********************
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
	echo "B";
  $stm->handle_field_meta_create_or_update_in_db($_POST, $table_meta_id, $dynamic_table_name);
  // Redirect to same page avoid resubmission
  $stm->redirect_to_same_page();
  exit;
}
?>

<div class="wrapper-product wrapper <?php echo $active_theme_prefix; ?>">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1>Fields for Table: <?= $table_meta->title ?></h1>
        </div>
      </div>
    </div>
  </section>

  <div class="<?php echo esc_attr($container); ?>" id="content">

    <div class="row ">
      <div class="col-md-12 content-area" id="primary">
        <main class="site-main" id="main" role="main">
          <div class="items_grid container">
            <div class="importer_form row justify-content-center py-4" style="background: white;"
              id="importer_form_container">
              <div class="col-md-12">

                <!-- ADD new field form -->

                <div class="row">
                  <div class="col-md-4">
                    <div id="add_field_form" class="add-fields">
                      <div class="row">
                        <div class="col-md-6">
                          <h2>
                            <span id="top-heading">Add new field</span>
                          </h2>
                        </div>
                        <div class="col-md-6">
                          <button id="add_field_btn" style="display: none;">Create new field</button>
                        </div>
                      </div>
                      <form id="field_form" action="" method="post">

                        <input type="hidden" name="field_id" id="field_id">

                        <label for="title">Title *</label>
                        <input type="text" name="title" id="title" required>

                        <label for="user_defined_type">Field Type *</label>
                        <select id="user_defined_type" name="user_defined_type" required>
                          <option value="" disabled selected>Select a type</option>
                          <option value="text">Text</option>
                          <option value="number">Number</option>
                          <option value="textarea">Textarea</option>
                          <option value="checkbox">checkbox</option>
                          <option value="radio">radio</option>
                          <option value="select">select</option>
                          <option value="range">range</option>
                          <option value="multi_select">multi select</option>
                          <option value="currency">currency</option>
                          <option value="dimension">Dimension</option>
                          <option value="weight">Weight</option>
                          <option value="volume">Volume</option>
                          <option value="products">Products</option>
                          <option value="repeater">Repeater Field</option>
                        </select>

                        <div id="range_main_div" class=" d-flex hidden justify-content-center align-items-center gap-2">
                          <div id="range_min_div range_inner_div" style="width:50%">
                            <label for="range_min">Range Min <small>(Default value) </small></label>
                            <input type="number" name="range_min" id="range_min">
                          </div>

                          <div id="range_max_div range_inner_div" style="width:50%">
                            <label for="range_max">Range Max<small>(Default value) </small> </label>
                            <input type="number" name="range_max" id="range_max">
                          </div>
                        </div>

                        <div id="products_select_div" class="hidden mb-4" style="width:100%;">
                          <label for="products_select">Select Products *</label>
                          <select style="width:100%;" name="products_select[]" id="products_select">



                          </select>
                        </div>

                        <div id="unit_div" class="hidden">
                          <label for="unit">Unit *</label>
                          <select id="unit_select" name="unit_select" required>

                          </select>
                        </div>

                        <div id="default_value_div">
                          <label for="default_value">Default Value </label>
                          <input type="text" name="default_value" id="default_value">
                        </div>

                        <label for="completion_weightage">Completion Weightage</label>
                        <input type="number" name="completion_weightage" id="completion_weightage">


                        <div class="placeholder-field">
                          <label for="placeholder">Placeholder</label>
                          <input type="text" name="placeholder" id="placeholder">
                        </div>


                        <label for="seller-apps">Seller Apps</label>
                        <div id="seller-apps" class="w-full d-flex gap-3 mb-5">

                          <?php $seller_apps = $stm->get_all_rows_and_cols_from_table('pim_seller_apps'); ?>

                          <?php foreach ($seller_apps as $i => $seller_app): ?>

                          <div class="d-flex align-items-center gap-1">
                            <input type="checkbox" name="seller_apps[]" id="seller-app-<?= $seller_app->id ?>"
                              value="<?= $seller_app->id ?>" />
                            <label class="m-0" for="seller-app-<?= $seller_app->id ?>"> <?= $seller_app->name ?>
                            </label>
                          </div>

                          <?php endforeach; ?>

                        </div>



                        <label for="display_order">Display Order</label>
                        <input id="display_order" type="number" name="display_order">

                        <label for="is_erp">Is ERP</label>
                        <div class="radio-group">
                          <label><input type="radio" name="is_erp" value="1"> Yes</label>
                          <label><input type="radio" name="is_erp" value="0" checked> No</label>
                        </div>

                        <label for="is_system_defined">Is System Defined</label>
                        <div class="radio-group">
                          <label><input type="radio" name="is_system_defined" value="1"> Yes</label>
                          <label><input type="radio" name="is_system_defined" value="0" checked> No</label>
                        </div>



                        <!-- <div id="weight_unit_div">
                          <label for="unit">Unit </label>
                          <input type="text" name="unit" id="unit">
                        </div>

												<div id="volume_unit_div">
                          <label for="unit">Unit </label>
                          <input type="text" name="unit" id="unit">
                        </div>

												<div id="currency_unit_div">
                          <label for="unit">Unit </label>
                          <input type="text" name="unit" id="unit">
                        </div> -->


                        <!-- <div id="field_options">
                      <label for="field_options">Field Options</label>
                      <textarea name="field_options" id="field_options_textarea" required></textarea>
                    </div> -->
                        <div id="field_options">
                          <label for="field_options">Field Options - can also select default value for field *</label>
                          <div id="options_container">
                            <div class="add_remove_options">
                              <button type="button" id="add_option_btn">+</button>
                              <button type="button" id="remove_option_btn" class="hidden">-</button>
                            </div>

                            <div class="option_field">
                              <label for="option_1">Option 1 <span class="default_text_span">(Default)</span></label>
                              <input type="text" name="field_options[]" id="option_1" required>
                              <input type="radio" name="option_default_value" data-identity="default_value"
                                id="option_1" value='0' checked="true" required>
                            </div>
                          </div>
                        </div>
                        <button id="new_field_submit_button" type="submit">Create Field</button>
                      </form>
                    </div>
                  </div>
                  <!-- ALREADY existing fields table -->
                  <div class="col-md-8">
                    <div class='existing_fields_table'>
                      <?php
                  // Fetch all records from pim_field_metas table where table_meta_id matches
                  $fields = $stm->get_all_cols_in_one_to_many('pim_field_metas', 'table_meta_id', $table_meta_id);

                  if ($fields) {
                  ?>
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th style="padding:0px 0px 0px 15px; width: 20%">Title</th>
                            <!-- <th>Field Name(slug)</th> -->
                            <th>Type</th>
                            <!-- <th>Is ERP</th>
                        <th>Is System Defined</th>
                        <th>Placeholder</th> -->
                            <th>Field Options</th>
                            <th style="width: 15%;">Seller Apps </th>
                            <!-- <span class="bg-dark text-warning">(Ids) **</span> -->
                            <th>Completion Weightage</th>
                            <!-- <th>Is Sync</th> -->
                            <th>Display Order</th>
                            <th style="padding:0px 15px 0px 0px;">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($fields as $field) {
														$config = json_decode($field->CONFIG, true);
														// $stm->dump($config);
                        ?>
                          <tr>
                            <td style="padding:9px 0px 0px 15px;">
                              <?php echo esc_html($field->title); ?>
                              <p>
                                <button class="btn-links edit-field-btn" style="color: #0d6efd;font-weight: 400;"
                                  data-field-id="<?php echo esc_attr($field->id); ?>"
                                  data-title="<?php echo esc_attr($field->title); ?>"
                                  data-user-defined-type="<?php echo esc_attr($field->user_defined_type); ?>"
                                  data-is-erp="<?php echo esc_attr($field->is_erp); ?>"
                                  data-is-system-defined="<?php echo esc_attr($field->is_system_defined); ?>"
                                  data-placeholder="<?php echo esc_attr($config['placeholder']); ?>"
                                  data-field-value="<?php echo esc_attr($config['field_value']); ?>"
                                  data-field-options="<?php echo isset($config['field_options']) && is_array($config['field_options']) ? esc_attr(implode('__', $config['field_options'])) : ''; ?>"
                                  data-display-order="<?php echo esc_attr($field->display_order); ?>"
                                  data-default-value="<?php echo esc_attr($config['default_value']); ?>"
                                  data-unit="<?php echo esc_attr($config['unit']); ?>"
                                  data-completion-weightage="<?= esc_attr($config['completion_weightage']) ?>"
                                  data-selected-products="<?php echo isset($config['selected_products']) && is_array($config['selected_products']) ? esc_attr(implode('__', $config['selected_products'])) : ''; ?>"
                                  data-range="<?php echo isset($config['range']) && is_array($config['range']) ? esc_attr(implode('__', $config['range'])) : ''; ?>"
                                  data-seller-apps="<?php echo isset($config['seller_apps']) && is_array($config['seller_apps']) ? esc_attr(implode('__', $config['seller_apps'])) : ''; ?>">Edit</button>
                              </p>
                            </td>
                            <td><?php echo esc_html($field->user_defined_type); ?></td>
                            <!-- <td><?php echo esc_html($field->is_erp == 1 ? "true" : "false"); ?></td>
                        <td><?php echo esc_html($field->is_system_defined == 1 ? "true" : "false"); ?></td>
                        <td><?php echo esc_html($config['placeholder']); ?></td> -->
                            <td>
                              <?php echo esc_html(is_array($config['field_options']) ? implode(', ', $config['field_options']) : $config['field_options']); ?>
                            </td>
                            <td>
                              <?php echo esc_html(is_array($config['seller_apps']) ? implode(', ', $stm->get_seller_apps_names($config['seller_apps'])) : $config['seller_apps']); ?>
                            </td>
                            <td><?php echo esc_html($config['completion_weightage']); ?></td>
                            <!-- <td><?php // echo esc_html($field->is_sync) ? 'true' : 'false';
                                      ?></td> -->
                            <td><?php echo esc_html($field->display_order); ?></td>
                            <td style="padding:0px 15px 0px 0px;">
                              <div class="actions">
                                <button type="button"
                                  style="width:70px; color:white; border-radius: 4px; padding:6px 5px;font-size:12px; border:0; <?= $field->is_sync ? 'background-color:darkgreen;' : '' ?>"
                                  <?= $field->is_sync ? 'disabled' : '' ?>
                                  class="d-block  btn-danger field_sync_button is_sync"
                                  data-sync-id="<?= $field->is_sync ? '' : esc_attr($field->id) ?>">
                                  <?= $field->is_sync ? 'Synced' : 'Sync Now' ?>
                                </button>
                                <form method="post"
                                  onsubmit="return confirm('Are you sure you want to delete this field?');">
                                  <input type="hidden" name="delete_field_id"
                                    value="<?php echo esc_attr($field->id); ?>">
                                  <button type="submit" class="btn btn-danger btn-delete">Delete</button>
                                </form>
                              </div>
                            </td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                      <?php } else { ?>
                      <p>No fields found.</p>
                      <?php } ?>
                    </div>
                  </div>
                </div>



              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</div>


<!-- ************************ -->
<!-- *****  SHOW FIELDS IN HTML FORM FOR THIS pim_table_metas table  ***** -->
<!-- ************************ -->

<?php

// Fetch field metadata from pim_field_metas
// $fields = $stm->get_all_cols_in_one_to_many('pim_field_metas', 'table_meta_id', $table_meta_id);
// $stm->get_html_fields_stylings();
// $stm->get_html_fields_form($fields, $table_meta);

?>


<!-- ****************** -->
<!-- ****** JAVASCRIPT ******** -->
<!-- ****************** -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const userDefinedType = document.getElementById('user_defined_type');
  const placeholderField = document.querySelector('.placeholder-field');

  const optionsContainer = document.getElementById('options_container');
  const addOptionBtn = document.getElementById('add_option_btn');
  const removeOptionBtn = document.getElementById('remove_option_btn');

  showHideDiv('.placeholder-field', 'hide');
  requireUnrequireField('.placeholder-field', 'unReq');

  // showHideDiv('#default_value_div', 'hide');

  const field_options = document.querySelector('#field_options');
  // const field_value = document.querySelector('#field_value');

  showHideDiv('#field_options', 'hide');
  requireUnrequireField('#field_options', 'unReq');

  userDefinedType.addEventListener('change', function() {
    const selectedType = this.value;
    bringFieldsNeeded(selectedType)

    if (['number', 'currency', 'weight', 'dimension', 'volume'].includes(selectedType)) {

      document.querySelector('#default_value').type = 'number'
    } else {
      document.querySelector('#default_value').type = 'text'

    }
  });

  // document.addEventListener('change', e => {
  //   // select units
  //   if (e.target.closest('#unit_div')) {
  //     const unitSelect = e.target.closest('#unit_div').querySelector('select')
  //     document.querySelector('#default_value').value = unitSelect.value
  //   }
  // })

  // const editFieldButtons = document.querySelectorAll('.edit-field-btn');
  document.addEventListener('click', e => {

    if (e.target.closest('.field_sync_button')) {
      const fieldId = e.target.dataset.syncId
      if (fieldId) {
        syncField(fieldId, e.target)
      }
    }


    if (e.target.closest('.option_field input[name="option_default_value"]')) {
      setDefaultOption(e.target)
      console.log("ok", e.target.value)
    }


    if (e.target.closest('.edit-field-btn')) {

      // clear the previous data from fields before showing/adding new data to them
      // document.querySelector('#add_field_btn').click();
      // document.querySelector('#add_field_btn').click();

      // update submit button and top heading text
      document.querySelector('#new_field_submit_button').textContent = 'Update field'
      document.querySelector('#top-heading').textContent = 'Update field'


      //scroll to top (to edit form)
      window.scrollTo(0, 300);

      // get current(previous) values and populate form fields with them
      const fieldId = e.target.dataset.fieldId;
      const title = e.target.dataset.title;
      const userDefinedType = e.target.dataset.userDefinedType;
      const isErp = e.target.dataset.isErp;
      const isSystemDefined = e.target.dataset.isSystemDefined;
      const placeholder = e.target.dataset.placeholder;
      const fieldOptions = e.target.dataset.fieldOptions;
      const displayOrder = e.target.dataset.displayOrder;
      const fieldValue = e.target.dataset.fieldValue;
      const defaultValue = e.target.dataset.defaultValue;
      const sellerApps = e.target.dataset.sellerApps;
      const unit = e.target.dataset.unit;
      const selectedProducts = e.target.dataset.selectedProducts.split('__');
      const completionWeightage = e.target.dataset.completionWeightage;
      const [minRange, maxRange] = e.target.dataset.range.split('__');

      bringFieldsNeeded(userDefinedType)

      if (minRange) {
        document.querySelector('#range_min').value = minRange
      }

      if (maxRange) {
        document.querySelector('#range_max').value = maxRange
      }

      // if (selectedProducts) {
      //   console.log(selectedProducts);
      //   document.querySelector('#products_select_div').style.pointerEvents = 'none'
      //   setTimeout(() => {
      //     jQuery("#products_select").val(selectedProducts).trigger('change')
      //     // console.log('mkol');
      //     document.querySelector('#products_select_div').style.pointerEvents = 'auto'
      //   }, 2000)

      // } else {

      //   document.querySelector('#products_select').value = ''
      //   jQuery("#products_select").val([]).trigger('change')

      // }

      if (unit) {
        document.querySelector('#unit_div').classList.remove('hidden')
        document.querySelector('#unit_select').value = unit
        document.querySelector('#default_value').type = 'number'
      } else {
        document.querySelector('#unit_div').classList.add('hidden')
        document.querySelector('#unit_select').value = ''
        document.querySelector('#default_value').type = 'text'
      }

      // console.log("______defaultValue____ ", defaultValue)
      // if user tries to edit field, we are DISABLING field_type field and selecting its value (stored when CREATING field) saved in DB (to show on fronted)
      const typeInput = document.querySelector('#user_defined_type');
      // typeInput.setAttribute('disabled', true);
      [...typeInput.children].forEach(el => el.value === userDefinedType ? el.setAttribute('selected',
          true) :
        el.removeAttribute('selected'))
      // console.log('field_optoins ', fieldOptions.split('__'));


      document.getElementById('field_id').value = fieldId;
      document.getElementById('title').value = title;
      document.getElementById('user_defined_type').value = userDefinedType;
      document.querySelector(`input[name="is_erp"][value="${isErp}"]`).checked = true;
      document.querySelector(`input[name="is_system_defined"][value="${isSystemDefined}"]`).checked = true;
      document.getElementById('placeholder').value = placeholder;
      document.getElementById('display_order').value = displayOrder;
      document.getElementById('default_value').value = defaultValue;
      document.getElementById('completion_weightage').value = completionWeightage;
      // document.getElementById('field_options_textarea').value = fieldOptions;
      fieldOptions.split('__').forEach((val, i) => {
        const optionField = document.querySelector(`#option_${i+1}`)
        if (optionField) {
          document.querySelector(`#option_${i+1}`).value = val
        } else {
          const newOptionDiv = document.createElement('div')
          newOptionDiv.classList.add('option_field')
          const newOption = `
                          <label for="option${i+1}">Option ${i+1}<span class="default_text_span"></span></label>
                          <input type="text" name="field_options[]" id="option_${i+1}" required="true" value="${val}">
                          <input type="radio" name="option_default_value" data-identity="default_value" id="option_${i+1}" value='${i}' required>
													`
          console.log('newoption ', newOption)
          newOptionDiv.innerHTML = newOption
          document.querySelector('#options_container').appendChild(newOptionDiv);

        }
      });


      if (fieldOptions) {
        const defaultValueRadio = [...document.querySelectorAll('.option_field input[type="text"]')].find(
          el => {
            console.log(el.value, defaultValue)
            return el.value === defaultValue
          })?.closest('.option_field').querySelector('input[type="radio"]')
        console.log('defaultValueRadio ', defaultValueRadio)

        setDefaultOption(defaultValueRadio)
      }

      sellerApps.split('__').forEach((val, i) => {
        const sellerField = document.querySelector(`#seller-app-${val}`)
        if (sellerField) {
          sellerField.checked = true
        } else {

        }
      })
      // document.getElementById('field_value_input').value = fieldValue;

      // addFieldForm.classList.remove('hidden');
      // addFieldButton.textContent = "Close form";
      addFieldButton.style.display = 'block';
    }
  })


  function setDefaultOption(el) {

    const addFieldForm = document.querySelector('#add_field_form')

    let defaultValueField = addFieldForm.querySelector('#default_value')
    defaultValueField.value = ''

    document.querySelectorAll('.option_field input[name="option_default_value"]').forEach(el => el.checked = false)
    el.checked = true



    const optionsContainer = addFieldForm.querySelector('#options_container')

    // defaultValueField = optionsContainer.querySelector(
    //     '.option_field input[name="option_default_value"]:checked')
    //   .value


    document.querySelectorAll('.default_text_span').forEach(el => el.textContent = '')
    let defaultValueSpan = el.closest('.option_field').querySelector('.default_text_span').textContent = '(Default)'

    // not leveraging these 2 below lines currently
    defaultValueField = [...document.querySelectorAll('.option_field input[type="text"]')][el.value].value
    console.log(defaultValueField)
  }





  function showHideDiv(selector, operation) {
    const el = document.querySelector(selector)
    // hide/show fields divs
    operation === "hide" ? el.style.display = 'none' : operation === "show" ? el.style.display = 'block' : '';
    operation === "hide" ? el.classList.add('hidden') : operation === "show" ? el.classList.remove('hidden') : '';

  }

  const units = {
    volume: [{
        unitName: 'Litre',
        unitSymbol: 'L'
      },
      {
        unitName: 'Millilitre',
        unitSymbol: 'mL'
      },
      {
        unitName: 'Cubic Metre',
        unitSymbol: 'cm^3'
      },
    ],
    weight: [{
        unitName: 'Kilogram',
        unitSymbol: 'kg'
      },
      {
        unitName: 'Gram',
        unitSymbol: 'g'
      },
      {
        unitName: 'Milligram',
        unitSymbol: 'mg'
      },
      {
        unitName: 'Pound',
        unitSymbol: 'lb'
      },
    ],
    currency: [{
        unitName: 'US Dollar',
        unitSymbol: 'US$'
      },
      {
        unitName: 'Canadian Dollar',
        unitSymbol: 'CA$'
      },
      {
        unitName: 'Pound',
        unitSymbol: '£'
      },
      {
        unitName: 'Euro',
        unitSymbol: '€'
      },
    ],
    dimension: [{
        unitName: 'Metre',
        unitSymbol: 'm'
      },
      {
        unitName: 'Centimetre',
        unitSymbol: 'cm'
      },
      {
        unitName: 'Millimetre',
        unitSymbol: 'mm'
      },
      {
        unitName: 'Hertz',
        unitSymbol: 'Hz'
      },
      {
        unitName: 'Watt',
        unitSymbol: 'W'
      },
      {
        unitName: 'Volt',
        unitSymbol: 'V'
      },
    ],
  }

  function removeOptions(el) {
    while (el.options.length) {
      el.remove(0);
    }
  }

  function bringUnitsIfNeeded(selectedType) {
    const unitsDiv = document.querySelector('#unit_div')
    const unitsSelect = unitsDiv.querySelector('select')

    removeOptions(unitsSelect)

    const firstOption = '<option value="" disabled selected>Select a unit</option>'


    let optionsArr = units[selectedType].map(unitData =>
      `<option value="${unitData.unitSymbol}">${unitData.unitSymbol} (${unitData.unitName})</option>`).forEach(
      (option, i) => {
        if (i == 0) {
          unitsSelect.insertAdjacentHTML('beforeend', firstOption)
        }
        unitsSelect.insertAdjacentHTML('beforeend', option)
      })
  }

  function requireUnrequireField(selector, operation) {
    // mark fields required/unrequired (by adding/removing required attribute)
    selector = document.querySelectorAll(selector)
    operation === "req" ? selector.forEach(el => el.setAttribute("required", true)) : operation ===
      "unReq" ? selector.forEach(el => el.removeAttribute('required')) : ''
  }

  const addFieldButton = document.querySelector('#add_field_btn')
  const addFieldForm = document.querySelector('#add_field_form')
  addFieldButton.addEventListener('click', () => {
    // console.log("CLCIKED CLOSE BTN")

    // console.log("WAS HIDDEN")
    // Reset button text
    addFieldButton.textContent = "Create new field";

    // reset form fields
    document.getElementById('field_id').value = '';
    document.getElementById('title').value = '';
    document.getElementById('user_defined_type').value = '';
    document.querySelectorAll(`input[name="is_erp"]`)[1].checked = 'true';
    // document.querySelector(`input[name="is_erp"][value=1]`).checked =a
    // console.log('ooasdasdpasd ', document.querySelectorAll(`input[name="is_system_defined"]`));
    document.querySelectorAll(`input[name="is_system_defined"]`)[1].checked = 'true';
    // document.querySelector(`input[name="is_system_defined"][value=1]`).checked = 'false';
    document.getElementById('placeholder').value = '';
    document.querySelectorAll('#field_options input').forEach(el => el.value = '');
    document.getElementById('display_order').value = '';

    document.querySelector('#default_value').value = ''
    document.querySelector('#default_value').type = 'text'
    document.querySelector('#unit_select').value = ''
    document.querySelector('#unit_div').classList.add('hidden')

    // document.querySelector('#products_select').value = ''
    // jQuery("#products_select").val([]).trigger('change')
    // document.querySelector('#products_select_div').classList.add('hidden')

    document.querySelectorAll('#seller-apps input').forEach(el => el.checked = false)

    //  Enabling type field and unselecting all its values
    const typeInput = document.querySelector('#user_defined_type')
    typeInput.removeAttribute('disabled');
    [...typeInput.children].forEach(el => el.removeAttribute('selected'))

    // Reset submit Button & top heading text
    document.querySelector('#new_field_submit_button').textContent = 'Create Field'
    document.querySelector('#top-heading').textContent = 'Add new field'

    // reset number of option fields to only ONE and remove the rest.
    let totalOptions = [...document.querySelectorAll('.option_field')].length
    while (totalOptions > 1) {
      // [...document.querySelectorAll('.option_field')].at(-1).remove();
      const optionsContainer = document.querySelector('#options_container')
      optionsContainer.removeChild(optionsContainer.lastElementChild)
      totalOptions--
    }

    // if number of options is greater than ONE then show otherwise hide MINUS button in form
    if ([...document.querySelectorAll('.option_field')].length > 1) {
      removeOptionBtn.classList.remove('hidden');
    } else {
      removeOptionBtn.classList.add('hidden');
    }
    addFieldButton.style.display = 'none';

  })

  async function bringFieldsNeeded(selectedType) {
    // show/hide placeholder field based on field type selected by user
    if (['text', 'number', 'textarea', 'select', 'multi_select', 'currency'].includes(selectedType)) {
      showHideDiv('.placeholder-field', 'show');
      requireUnrequireField('.placeholder-field', 'req');
    } else {
      showHideDiv('.placeholder-field', 'hide');
      requireUnrequireField('.placeholder-field', 'unReq');
    }

    if (['text', 'number', 'textarea', 'currency', 'weight', 'dimension', 'volume'].includes(selectedType)) {

      showHideDiv('#default_value_div', 'show');
      requireUnrequireField('#default_value', 'unReq');

    } else {

      showHideDiv('#default_value_div', 'hide');

    }

    // show/hide options field based on field type selected by user
    if (['select', 'multi_select', 'radio', 'checkbox'].includes(selectedType)) {
      showHideDiv('#field_options', 'show');
      requireUnrequireField('#field_options input', 'req');

    } else {
      showHideDiv('#field_options', 'hide');
      requireUnrequireField('#field_options input', 'unReq');
    }

    // unit
    if (['currency', 'weight', 'dimension', 'volume'].includes(selectedType)) {
      console.log("YESSS");
      showHideDiv('#unit_div', 'show');
      requireUnrequireField('#unit_div select', 'req');

      bringUnitsIfNeeded(selectedType)

    } else {
      console.log("YESSS 22");
      showHideDiv('#unit_div', 'hide');
      requireUnrequireField('#unit_div select', 'unReq');
    }

    if (['products'].includes(selectedType)) {

      // const allProducts = await getAllProducts()

      // setupSelect2ForProductsSelect(allProducts)

      // showHideDiv('#products_select_div', 'show');
      // requireUnrequireField('#products_select', 'req');

    } else {
      // showHideDiv('#products_select_div', 'hide');
      // requireUnrequireField('#products_select', 'unReq');
      // document.querySelector('#products_select').value = ''
    }

    if (['range'].includes(selectedType)) {
      showHideDiv('#range_main_div', 'show');
      requireUnrequireField('#range_main_div #range_min', 'unReq');
      requireUnrequireField('#range_main_div #range_max', 'unReq');

    } else {
      showHideDiv('#range_main_div', 'hide');
      requireUnrequireField('#range_main_div #range_min', 'unReq');
      requireUnrequireField('#range_main_div #range_max', 'unReq');
      document.querySelector('#range_min').value = ''
      document.querySelector('#range_max').value = ''
    }

  }

  function setupSelect2ForProductsSelect(allProducts) {

    const productsSelect = document.querySelector('#products_select')

    removeOptions(productsSelect)


    // const firstOption = '<option value="" disabled selected> Select Products </option>';
    const firstOption = '<option></option>';

    allProducts.map(item => `<option value="${item.id}"> ${item.sku} </option> `).forEach((option, i) => {
      if (i == 0) {
        productsSelect.insertAdjacentHTML('beforeend', firstOption)
      }

      productsSelect.insertAdjacentHTML('beforeend', option)

    })

    jQuery("#products_select").select2({
      placeholder: "Select Products...",
      multiple: true,
      // allowClear: true
    });

    setTimeout(() => {
      jQuery("#products_select").val([]).trigger('change')
    }, 500)

  }

  async function getAllProducts() {
    const adminUrl = '<?= admin_url("admin-ajax.php") ?>';
    const nonce = '<?= wp_create_nonce("get_all_products") ?>';
    const data = new URLSearchParams({
      action: 'get_all_products',
      _wpnonce: nonce
    })

    try {
      const apiRequest = await fetch(adminUrl, {
        method: 'POST',
        body: data
      })
      const response = await apiRequest.json()
      if (response.success) {
        console.log("prods retrieved")
        return response.data.products
      } else {
        console.log('error getting prods 1');
      }

    } catch (err) {
      console.log('error getting prods 2');
      console.log(err);
    }

    // console.log('response ', response)
  }

  // ******************************
  // Manage(add/remove) options fields and show/hide remove button
  // ******************************

  addOptionBtn.addEventListener('click', function() {
    let optionCount = [...document.querySelectorAll('.option_field')].length + 1;
    // optionCount++;
    const newOption = document.createElement('div');
    newOption.classList.add('option_field');
    newOption.innerHTML = `
      <label for="option_${optionCount}">Option ${optionCount}<span class="default_text_span"></span></label>
      <input type="text" name="field_options[]" id="option_${optionCount}" required>
      <input type="radio" name="option_default_value" data-identity="default_value" id="option_${optionCount}" value='${optionCount - 1}' required>

    `;
    optionsContainer.appendChild(newOption);
    removeOptionBtn.classList.remove('hidden');
  });

  removeOptionBtn.addEventListener('click', function() {
    let optionCount = [...document.querySelectorAll('.option_field')].length;
    if (optionCount > 1) {
      optionsContainer.removeChild(optionsContainer.lastElementChild);
      optionCount--;
    }
    if (optionCount === 1) {
      removeOptionBtn.classList.add('hidden');
    }
  });

  // if number of options is greater than ONE ONLY then hide MINUS button in form
  if ([...document.querySelectorAll('.option_field')].length > 1) {
    removeOptionBtn.classList.remove('hidden');
  }



  function syncField(fieldId, target) {

    target.style.pointerEvents = 'none'
    const syncId =
      target.dataset.syncId
    target.dataset.syncId = ''
    target.disabled = true

    fetch('<?= admin_url('admin-ajax.php') ?>', {
        method: 'POST',
        body: new URLSearchParams({
          action: 'sync_field_to_syndication',
          field_id: fieldId,
          _wpnonce: '<?= wp_create_nonce('sync_field_to_syndication') ?>'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          console.log(data, 'SUCCESS')
          target.style.backgroundColor = '#599A59';
          target.textContent = 'Synced';
        } else {
          console.log(data, 'ERROR')
          target.style.pointerEvents = 'auto'
          target.dataset.syncId = syncId
          target.disabled = false
          target.textContent = 'Sync Now';
        }
      })
      .catch(error => {
        console.log(error)
        console.log(error, 'ERROR')
        target.style.pointerEvents = 'auto'
        target.dataset.syncId = syncId
        target.disabled = false
        target.textContent = 'Sync Now';
      })
      .finally(() => {})
  }

});
</script>









<?php get_footer();
