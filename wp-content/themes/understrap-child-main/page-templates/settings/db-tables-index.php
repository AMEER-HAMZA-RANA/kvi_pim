<?php

/**
 * Template Name: Settings DB Table List Page
 *
 *
 * @package arrow
 */



// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header("blank-subheader");
$container = get_theme_mod('arrow_container_type');
// $sm = StateManager::GI();
$stm = SettingsManager::GI();

function get_number_of_fields_for_current_table($table_meta_id, $stm)
{

  return sizeof($stm->get_all_cols_in_one_to_many('pim_field_metas', 'table_meta_id', $table_meta_id));
}

// $sm->g_importer->load_js();

?>
<style>
.form-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background: #F6F7F7;

  border-radius: 10px;

}

.form-container label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
}

.form-container input,
.form-container textarea,
.form-container select,
.form-container button {
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.form-container button {
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

.form-container button:hover {
  background-color: #45a049;
}

.success-message {
  display: none;
  margin: 20px 0;
  padding: 10px;
  border: 1px solid green;
  background-color: #d4edda;
  color: green;
  border-radius: 5px;
}

.form-container select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="%23333" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-chevron-down" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>') no-repeat right 10px center;
  background-size: 16px;
}

.radio-group {
  display: flex;
  flex-direction: column;
  /* justify-content: space-between; */
  margin-bottom: 15px;
}

.radio-group input {
  max-width: min-content;
}

.hidden {
  display: none;
}

#add_table_btn {
  padding: 6px 10px;
  border-radius: 6px;
  /* border: 2px solid white; */
  color: white;
  font-size: 20px;
  margin-left: auto;
  display: block;
  margin-bottom: 0;
  /* width: 100%; */
}


/* .tables_div {
    background-color: gray;
    margin-top: 200px;
  } */

.importer_form {
  font-size: 14px;
}

.actions {
  display: flex;
  gap: 10px;
  align-items: center;
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
  background: #811C19 !important;
  border-color: #811C19 !important;
}

.selectoption {
  width: 154.46px;
  height: 30px;
  top: 416px;
  left: 612px;
  gap: 0px;
  border: 1px 0px 0px 0px;
  opacity: 0px;
  border: 1px solid #00000033;
  background: #FFFFFF;
  font-family: Inter;
  font-size: 12px;
  font-style: italic;
  font-weight: 300;
  line-height: 14.52px;
  text-align: center;
  color: #0000007A;

}

.applybtn {
  width: 93.1px;
  height: 30px;
  top: 416px;
  left: 779.16px;
  gap: 0px;
  border: 1px 0px 0px 0px;
  opacity: 0px;
  border: 1px solid #811C19;
  font-family: Inter;
  font-size: 14px;
  font-weight: 400;
  line-height: 16.94px;
  text-align: center;
  color: #811C19;
  background: #FFFFFF;
}

.searchfeld {
  width: 153.07px;
  height: 34px;
  top: 416px;
  left: 1079px;
  gap: 0px;
  border: 1px 0px 0px 0px;
  opacity: 0px;
  background: #FFFFFF;
  border: 1px solid #00000033 !important;
  font-family: Inter;
  font-size: 12px;
  font-style: italic;
  font-weight: 300;
  line-height: 14.52px;
  text-align: center;
  color: #0000007A;
}

.searchfeld::placeholder {
  font-family: Inter;
  font-size: 12px;
  font-style: italic;
  font-weight: 300;
  line-height: 14.52px;
  text-align: center;
  color: #0000007A;
}

.searchbtn {
  width: 98.56px;
  height: 34px;
  top: 416px;
  left: 1238.37px;
  gap: 0px;
  opacity: 0px;
  background: #811C19;
  font-family: Inter;
  font-size: 14px;
  font-weight: 400;
  line-height: 16.94px;
  text-align: center;
  color: #FFFFFF;
  border: none;
}

form {
  margin: 0;
}

.checkbox {
  width: 14px;
  height: 14px;
  gap: 0px;
  border-radius: 3px 0px 0px 0px;
  border: 1px 0px 0px 0px;
  opacity: 0px;
  border: 1px solid;
  border-image-source: linear-gradient(360deg, #C8C8C8 0%, #979797 100%);

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

.color-circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
}
</style>

<div class="wrapper-product wrapper <?php echo $active_theme_prefix; ?>">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1>Tables</h1>
        </div>
      </div>
    </div>
  </section>

  <div class="<?php echo esc_attr($container); ?>" id="content">

    <div class="row ">
      <div class="col-md-12 brand-logo">


      </div>

      <div class="col-md-12 content-area" id="primary">

        <main class="site-main" id="main" role="main">
          <div class="items_grid container ">
            <div class="importer_form row justify-content-center py-4" style="background: white;"
              id="importer_form_container">

              <div class="row">
                <?php
                // $stm->dump($_POST);

                // Handle the delete action
                if (isset($_POST['delete_table_meta_id'])) {
                  $stm->delete_row_from_table($_POST['delete_table_meta_id'], 'pim_table_metas');
                  echo '<div class="success-message"Table deleted successfully!</div>';
                  $stm->redirect_to_same_page();
                  exit;
                }
                // unset($_POST);
                // $stm->dump($_POST);
                // Handle table_meta update
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])  && isset($_POST['table_update_id']) && $_POST['table_update_id'] == TRUE) {
                  $stm->update_table_meta_in_db($_POST);
                  $stm->redirect_to_same_page();
                  exit;
                }
                // $stm->dump($_POST);

                // Handle new table_meta creation
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
                  $stm->create_table_meta_in_db($_POST);
                  $stm->redirect_to_same_page();
                  exit;
                }



                ?>


                <div class="col-md-4">
                  <div id="add_table_form" class="form-container ">
                    <div class="success-message" id="successMessage">
                      Form submitted successfully!
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <h2>
                          <span id="top-heading">Add a Table</span>
                        </h2>
                      </div>
                      <div class="col-md-6">
                        <button id="add_table_btn" style="display: none;">Create new table</button>
                      </div>
                    </div>
                    <form action="" method="post">

                      <input type="hidden" name="table_update_id" id="table_update_id">

                      <label for="brand_id">Brand *</label>
                      <!-- <input type="text" name="brand_id" required> -->
                      <!-- <select name="brand_id" required>
                      <option disabled selected>Select a brand</option>
                      <option value="1">Arrow</option>
                    </select> -->
                      <select id="brand_id" name="brand_id" required>
                        <option value="" disabled selected>Select a brand</option>
                        <?php $brands = $stm->get_all_rows_and_cols_from_table('pim_brands'); ?>
                        <?php if ($brands): ?>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand->id ?>"><?= ucfirst($brand->name) ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                      </select>

                      <label for="table_name">Table Title *</label>
                      <input id="title" type="text" name="title" required>

                      <label for="desc">Description</label>
                      <textarea id="desc" name="desc"></textarea>

                      <label for="display_order">Display Order</label>
                      <input id="display_order" type="number" name="display_order">

                      <!-- <label for="ui_color">UI Color *</label>
                    <input type="color" name="ui_color" required> -->

                      <label for="background_color">Background Color</label>
                      <input id="background_color" type="color" name="background_color" value="#000000">

                      <label for="text_color">Text Color</label>
                      <input id="text_color" type="color" name="text_color" value="#ffffff">

                      <label for="add_info">Additional Info(optional)</label>
                      <textarea id="add_info" name="add_info"></textarea>

                      <button id="table-form-submit-button" type="submit" name="submit">Create Table</button>
                    </form>
                  </div>
                </div>

                <div class="col-md-8">
                  <!-- ALREADY existing tables table -->
                  <div class='existing_tables_table'>
                    <!-- Bulk Action and Search Bar -->
                    <!-- <div class="row">
                      <div class="col-md-6">
                        <form>
                          <div class="form-row">
                            <div class="col-sm-5">
                              <select class="selectoption" name="delete">
                                <option value="">Bulk Action</option>
                                <option value="Delete">Delete</option>
                              </select>
                            </div>
                            <div class="col-sm-5">
                              <input type="submit" class="applybtn" value="Apply">
                            </div>
                          </div>
                        </form>
                      </div>
                      <div class="col-md-6">
                        <form>
                          <div class="form-row" style="justify-content: end;">
                            <div class="col-sm-5">
                              <input type="text" name="Search" class="searchfeld" placeholder="search meta..">
                            </div>
                            <div class="col-sm-4">
                              <input type="submit" class="searchbtn" value="Search">
                            </div>
                          </div>
                        </form>
                      </div>
                    </div> -->
                    <?php $table_metas = $stm->get_all_rows_and_cols_from_table('pim_table_metas');  ?>
                    <?php if ($table_metas): ?>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <!-- <th><input class="checkbox" type="checkbox" value="[]"></th> -->
                          <th style="padding:0px 0px 0px 15px;">Title</th>
                          <th style="width: 12%;">Fields</th>
                          <th style="width: 15%;">Class/Product specific</th>
                          <th style="width: 12%;">UI Color</th>
                          <th style="width: 14%;">Display Order</th>
                          <th style="padding:0px 15px 0px 0px;">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($table_metas as $table_meta): ?>

                        <?php $num_of_fields = get_number_of_fields_for_current_table($table_meta->id, $stm); ?>

                        <?php $config = json_decode($table_meta->CONFIG, true); ?>
                        <tr>
                          <!-- <td><input type="checkbox" class="checkbox" value="<?php echo $table_meta->id; ?>" name="table"></td> -->
                          <td style="padding:9px 0px 0px 15px;">
                            <?php echo esc_html($table_meta->title); ?>
                            <p>
                              <a href="<?php echo site_url("/settings/db/tables/edit/?tbl_id={$table_meta->id}/"); ?>"
                                class="">View</a>
                              <button class="btn-link edit-table-btn"
                                data-table-id="<?php echo esc_attr($table_meta->id); ?>"
                                data-table-title="<?php echo esc_attr($table_meta->title); ?>"
                                data-table-brand-id="<?php echo esc_attr($table_meta->brand_id); ?>"
                                data-table-slug="<?php echo esc_attr($table_meta->table_name); ?>"
                                data-table-desc="<?php echo esc_attr($table_meta->desc); ?>"
                                data-is-class-specific="<?php echo esc_attr($table_meta->is_class_specific); ?>"
                                data-display-order="<?php echo esc_attr($table_meta->display_order); ?>"
                                data-add-info="<?php echo esc_attr($config['add_info'] ?? ''); ?>"
                                data-ui-color="<?php echo esc_attr($config['ui_color'] ?? ''); ?>"
                                data-background-color="<?php echo esc_attr($config['background_color'] ?? ''); ?>"
                                data-text-color="<?php echo esc_attr($config['text_color'] ?? ''); ?>">
                                Edit
                              </button>

                              <!-- | <button class="btn-link edit-table-btn"
                                data-table-id="<?php echo esc_attr($table_meta->id); ?>"
                                data-table-title="<?php echo esc_attr($table_meta->title); ?>"
                                data-table-brand-id="<?php echo esc_attr($table_meta->brand_id); ?>"
                                data-table-slug="<?php echo esc_attr($table_meta->table_name); ?>"
                                data-table-desc="<?php echo esc_attr($table_meta->desc); ?>"
                                data-is-class-specific="<?php echo esc_attr($table_meta->is_class_specific); ?>"
                                data-display-order="<?php echo esc_attr($table_meta->display_order); ?>"
                                data-add-info="<?php echo esc_attr($config['add_info']); ?>"
                                data-ui-color="<?php echo esc_attr($config['ui_color']); ?>"
                                data-background-color="<?= esc_attr($config['background_color']); ?>"
                                data-text-color="<?= esc_attr($config['text_color']) ?>">Edit</button> -->
                            </p>
                          </td>
                          <td><?php echo esc_html($num_of_fields); ?></td>
                          <td><?php echo esc_html($table_meta->is_class_specific); ?></td>
                          <td>
                            <div class="color-circle"
                              style="background-color: <?php echo esc_html($config['background_color']); ?>;">

                            </div>
                          </td>

                          <td><?php echo esc_html($table_meta->display_order); ?></td>
                          <td style="padding:0px 15px 0px 0px;">
                            <div class="actions">
                              <a style="display:block;width:70px;color:white; background: #4005A0;border-radius:4px;padding:6px 10px;font-size:12px;text-decoration:none; text-align:center;"
                                href="<?php echo site_url("/settings/db/tables/edit/?tbl_id={$table_meta->id}/"); ?>"
                                class="">Fields</a>
                              <!-- EDIT BUTTON -->


                              <form method="post"
                                onsubmit="return confirm('Are you sure you want to delete this table_meta and all its fields?');">
                                <input type="hidden" name="delete_table_meta_id"
                                  value="<?php echo esc_attr($table_meta->id); ?>">
                                <button type="submit" class="btn btn-danger btn-delete">Delete</button>
                              </form>
                            </div>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                    <?php else: ?>
                    <p>No table metas found.</p>
                    <?php endif; ?>

                  </div>
                </div>


                <div class="tables_div"></div>
              </div>
            </div>
          </div>
        </main><!-- #main -->

      </div><!-- #primary -->

    </div><!-- .row end -->

  </div><!-- #content -->

</div><!-- #full-width-page-wrapper -->


<script>
document.addEventListener('DOMContentLoaded', function() {


  const addTableButton = document.querySelector('#add_table_btn');

  addTableButton.addEventListener('click', () => {
    // Reset button text
    addTableButton.textContent = "Create new table"

    document.getElementById('table_update_id').value = '';
    document.getElementById('brand_id').value = '';
    document.getElementById('brand_id').removeAttribute('disabled');
    document.getElementById('title').value = '';
    document.getElementById('title').readOnly = false;
    document.getElementById('desc').value = '';
    document.getElementById('display_order').value = '';
    document.getElementById('background_color').value = '#000000';
    document.getElementById('text_color').value = '#ffffff';
    document.getElementById('add_info').value = '';

    // Reset submit Button & top heading text
    document.querySelector('#table-form-submit-button').textContent = 'Create table'
    document.querySelector('#top-heading').textContent = 'Add a Table';
    addTableButton.style.display = 'none';



  })









  // const addTableButton = document.querySelector('#add_table_btn')
  // const addTableForm = document.querySelector('#add_table_form')

  document.addEventListener('click', e => {
    if (e.target.closest('.edit-table-btn')) {
      // update submit button and top heading text
      document.querySelector('#table-form-submit-button').textContent = 'Update Table'
      document.querySelector('#top-heading').textContent = 'Update Table'


      //scroll to top (to edit form)
      window.scrollTo(0, 300);

      // get current(previous) values and populate form fields with them
      const tableId = e.target.dataset.tableId;
      const tableTitle = e.target.dataset.tableTitle;
      const tableBrand = e.target.dataset.tableBrandId;
      const tableDesc = e.target.dataset.tableDesc;
      const isClassSpecific = e.target.dataset.isClassSpecific;
      const displayOrder = e.target.dataset.displayOrder;
      const addInfo = e.target.dataset.addInfo;
      const uiColor = e.target.dataset.uiColor;
      const backgroundColor = e.target.dataset.backgroundColor;
      const textColor = e.target.dataset.textColor;



      document.getElementById('table_update_id').value = tableId;
      document.getElementById('brand_id').value = tableBrand;
      document.getElementById('brand_id').setAttribute('disabled', true);
      document.getElementById('title').value = tableTitle;
      document.getElementById('title').readOnly = true;
      document.getElementById('desc').value = tableDesc;
      document.getElementById('display_order').value = displayOrder;
      document.getElementById('background_color').value = backgroundColor || '#000000';
      document.getElementById('text_color').value = textColor || '#ffffff';
      document.getElementById('add_info').value = addInfo;



      // addTableForm.classList.remove('hidden');
      addTableButton.style.display = 'block';


      // show all select parent options
      // [...document.getElementById('parent_id').children].forEach(el => el.style.display = 'block');
      // hide the term being edited from options of parent, bcz same term cannot be the parent of its own
      // [...document.getElementById('parent_id').children].find(el => el.value == termId).style.display = 'none';

    }
  })


  // addTableButton.addEventListener('click', () => {
  //   addTableForm.classList.toggle('hidden')
  //   if (addTableForm.classList.contains('hidden')) {
  //     // Reset button text
  //     addTableButton.textContent = "Create new table"

  //     document.getElementById('table_update_id').value = '';
  //     document.getElementById('brand_id').value = '';
  //     document.getElementById('title').value = '';
  // document.getElementById('title').readOnly = false;
  //     document.getElementById('desc').value = '';
  //     document.getElementById('display_order').value = '';
  //     document.getElementById('background_color').value = '';
  //     document.getElementById('text_color').value = '';
  //     document.getElementById('add_info').value = '';

  //     // Reset submit Button & top heading text
  //     document.querySelector('#table-form-submit-button').textContent = 'Create table'
  //     document.querySelector('#top-heading').textContent = 'Add new Table(Field Group)'


  //   } else {
  //     // Update button text
  //     addTableButton.textContent = "Close form"
  //   }
  // })


});
</script>


<?php get_footer();