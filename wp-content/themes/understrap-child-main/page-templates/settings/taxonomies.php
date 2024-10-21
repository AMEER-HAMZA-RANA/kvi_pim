<?php
/**
 * Template Name: Settings DB Table List Page
 *
 *
 * @package arrow
 */



// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header("blank-subheader");
$container = get_theme_mod( 'arrow_container_type' );
// $sm = StateManager::GI();
$stm = SettingsManager::GI();
// $sm->g_importer->load_js();

?>
<style>
.form-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f7f7f7;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

#add_taxonomy_btn {
  padding: 6px 10px;
  border-radius: 6px;
  border: 2px solid white;
  background: #000;
  color: white;
  font-size: 20px;
  margin-left: auto;
  display: block;
  /* width: 100%; */
  margin-bottom: 10px;
}

#add_taxonomy_form {
  margin-bottom: 100px;
}
</style>
<style>
.tables_div {
  background-color: gray;
  margin-top: 200px;
}
</style>
<style>
.importer_form {
  font-size: 14px;
}
</style>

<div class="wrapper-product wrapper <?php echo $active_theme_prefix;?>">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1>Existing Taxonomies</h1>
        </div>
      </div>
    </div>
  </section>

  <div class="<?php echo esc_attr( $container ); ?>" id="content">

    <div class="row ">
      <div class="col-md-12 brand-logo">


      </div>

      <div class="col-md-12 content-area" id="primary">

        <main class="site-main" id="main" role="main">
          <div class="items_grid container ">
            <div class="importer_form row justify-content-center py-4" style="background: white;"
              id="importer_form_container">

              <div class="col-md-12">
                <?php




// Handle the delete action
if (isset($_POST['delete_taxonomy_id'])) {
	$stm->delete_row_from_table($_POST['delete_taxonomy_id'], 'pim_taxonomies');
	echo '<div class="success-message"Table deleted successfully!</div>';
	$stm->redirect_to_same_page();
	exit;
}



// Handle taxonomy update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['tax_id']) && $_POST['tax_id']) {
	$stm->update_taxonomy_in_db($_POST);
	$stm->redirect_to_same_page();
	exit;
}

// Handle new taxonomy creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
		$stm->create_taxonomy_in_db($_POST);
    $stm->redirect_to_same_page();
    exit;
}
?>

                <button id="add_taxonomy_btn">Create new taxonomy</button>

                <div id="add_taxonomy_form" class="form-container hidden">
                  <h1>
                    <span id="top-heading">Add new taxonomy</span>
                  </h1>
                  <form action="" method="post">
                    <input type="hidden" name="tax_id" id="tax_id">
                    <!-- <input type="text" name="brand_id" required> -->
                    <!-- <select id="brand_id" name="brand_id" required>
											<option disabled selected>Select a brand</option>
                      <option value="1">Arrow</option>
                    </select> -->
                    <label for="brand_id">Brand *</label>
                    <select id="brand_id" name="brand_id" required>
                      <option value="" disabled selected>Select a brand</option>
                      <?php $brands = $stm->get_all_rows_and_cols_from_table('pim_brands'); ?>
                      <?php if ($brands): ?>
                      <?php foreach ($brands as $brand): ?>
                      <option value="<?= $brand->id ?>"><?= ucfirst($brand->name) ?></option>
                      <?php endforeach; ?>
                      <?php endif; ?>
                    </select>

                    <label for="taxonomy_name">Taxonomy Name *</label>
                    <input id="name" type="text" name="name" required>

                    <label for="desc">Description</label>
                    <textarea id="desc" name="desc"></textarea>

                    <label for="is_system_defined">Is System Defined</label>
                    <div class="radio-group">
                      <label><input type="radio" name="is_system_defined" value="1"> Yes</label>
                      <label><input type="radio" name="is_system_defined" value="0"> No</label>
                    </div>

                    <!-- <label for="display_order">Display Order *</label>
                    <input type="number" name="display_order" required> -->

                    <!-- <label for="ui_color">UI Color *</label>
                    <input type="color" name="ui_color" required> -->

                    <!-- <label for="add_info">Additional Info(optional)</label>
                    <textarea name="add_info"></textarea> -->

                    <button id="new_tax_submit_button" type="submit" name="submit">Create Taxonomy</button>
                  </form>
                </div>


              </div>





              <!-- ALREADY Taxonomies table -->
              <div class='existing_taxonomies_table'>
                <?php $taxonomies = $stm->get_all_rows_and_cols_from_table('pim_taxonomies'); ?>
                <?php if ($taxonomies): ?>
                <table class="table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Slug</th>
                      <th>Description</th>
                      <th>Is System Defined</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($taxonomies as $taxonomy): ?>
                    <tr>
                      <td><?php echo esc_html($taxonomy->name); ?></td>
                      <td><?php echo esc_html($taxonomy->slug); ?></td>
                      <td><?php echo esc_html($taxonomy->desc); ?></td>
                      <td><?php echo esc_html($taxonomy->is_system_defined); ?></td>
                      <td>
                        <a style="display:block;width:60px;margin-bottom:5px;color:white; background:green;border-radius:4px;padding:4px 10px;font-size:10px;text-decoration:none; text-align:center;"
                          href="<?php echo site_url("/settings/db/taxonomies/edit/?tax_id={$taxonomy->id}/"); ?>"
                          class="">Terms</a>
                        <button style="width:60px; margin-bottom:5px;" class="btn btn-primary edit-tax-btn"
                          data-tax-id="<?php echo esc_attr($taxonomy->id); ?>"
                          data-name="<?php echo esc_attr($taxonomy->name); ?>"
                          data-is-system-defined="<?php echo esc_attr($taxonomy->is_system_defined); ?>"
                          data-desc="<?php echo esc_attr($taxonomy->desc); ?>"
                          data-brand-id="<?php echo esc_attr($taxonomy->brand_id); ?>">Edit</button>
                        <form method="post"
                          onsubmit="return confirm('Are you sure you want to delete this taxonomy?');">
                          <input type="hidden" name="delete_taxonomy_id" value="<?php echo esc_attr($taxonomy->id); ?>">
                          <button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php else: ?>
                <p>No taxonomies found.</p>
                <?php endif; ?>

                <?php
								// $stm->get_all_existing_taxonomies_admin_template()

								// Fetch all records from pim_taxonomies table
						// $taxonomies = $stm->get_all_rows_and_cols_from_table('pim_taxonomies');

						// if ($taxonomies) {
						// 		echo '<table class="table">';
						// 		echo '
						// 				<thead>
						// 						<tr>
						// 								<th>Name</th>
						// 								<th>Slug</th>
						// 								<th>Description</th>
						// 								<th>Is System Defined</th>
						// 								<th>Action</th>
						// 						</tr>
						// 				</thead>
						// 		';
						// 		echo '<tbody>';

						// 		foreach ($taxonomies as $taxonomy) {
						// 				echo '<tr>';
						// 				echo '<td>' . esc_html($taxonomy->name) . '</td>';
						// 				echo '<td>' . esc_html($taxonomy->slug) . '</td>';
						// 				echo '<td>' . esc_html($taxonomy->desc) . '</td>';
						// 				echo '<td>' . esc_html($taxonomy->is_system_defined) . '</td>';
						// 				echo '<td>';
						// 				echo '<a style="display:block;width:60px;margin-bottom:5px;color:white; background:green;border-radius:4px;padding:4px 10px;font-size:10px;text-decoration:none; text-align:center;" href=' . site_url("/settings/db/taxonomies/edit/?tax_id={$taxonomy->id}/") . ' class="">Terms</a>';
						// 				echo '<button style="width:60px; margin-bottom:5px;" class="btn btn-primary edit-tax-btn"
						// 					data-tax-id="' . esc_attr($taxonomy->id) . '" data-name="' . esc_attr($taxonomy->name) . '"
						// 					data-is-system-defined="' . esc_attr($taxonomy->is_system_defined) . '"
						// 					data-desc="' . esc_attr($taxonomy->desc) . '"
						// 					data-brand-id="' . esc_attr($taxonomy->brand_id) . '"
						// 					>Edit</button>';
						// 					// echo '<form method="post"
						// 					// onsubmit="return confirm(\'Are you sure you want to delete this field?\');">';
						// 					// echo '<input type="hidden" name="delete_field_id" value="' . esc_attr($field->id) . '">';
						// 					// echo '<button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>';
						// 					// echo '</form>';
						// 				echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this taxonomy ?\');">';
						// 				echo '<input type="hidden" name="delete_taxonomy_id" value="' . esc_attr($taxonomy->id) . '">';
						// 				echo '<button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>';
						// 				echo '</form>';
						// 				echo '</td>';
						// 				echo '</tr>';
						// 		}

						// 		echo '</tbody>';
						// 		echo '</table>';
						// } else {
						// 		echo '<p>No taxonomies found.</p>';
						// }

								?>
              </div>


              <!-- <div class="tables_div"></div> -->

            </div>
          </div>
        </main><!-- #main -->

      </div><!-- #primary -->

    </div><!-- .row end -->

  </div><!-- #content -->

</div><!-- #full-width-page-wrapper -->


<script>
document.addEventListener('DOMContentLoaded', function() {

  const addTaxonomyButton = document.querySelector('#add_taxonomy_btn')
  const addTaxonomyForm = document.querySelector('#add_taxonomy_form')
  // addTaxonomyButton.addEventListener('click', () => {
  //   addTaxonomyForm.classList.toggle('hidden')
  //   if (addTaxonomyForm.classList.contains('hidden')) {
  //     addTaxonomyButton.textContent = "Create new taxonomy"
  //   } else {
  //     addTaxonomyButton.textContent = "Close form"
  //   }
  // })






  // const editFieldButtons = document.querySelectorAll('.edit-field-btn');
  document.addEventListener('click', e => {
    if (e.target.closest('.edit-tax-btn')) {
      // update submit button and top heading text
      document.querySelector('#new_tax_submit_button').textContent = 'Update taxonomy'
      document.querySelector('#top-heading').textContent = 'Update taxonomy'


      //scroll to top (to edit form)
      window.scrollTo(0, 300);

      // get current(previous) values and populate form fields with them
      const taxId = e.target.dataset.taxId;
      const brandId = e.target.dataset.brandId;
      const name = e.target.dataset.name;
      const isSystemDefined = e.target.dataset.isSystemDefined;
      const desc = e.target.dataset.desc;


      document.getElementById('tax_id').value = taxId;
      document.getElementById('brand_id').value = brandId;
      document.getElementById('name').value = name;
      // document.querySelector('is_system_defined').value = isSystemDefined;
      document.querySelector(`input[name="is_system_defined"][value="${isSystemDefined}"]`).checked = true;
      document.getElementById('desc').value = desc;
      // document.getElementById('field_options_textarea').value = fieldOptions;
      // document.getElementById('field_value_input').value = fieldValue;

      addTaxonomyForm.classList.remove('hidden');
      addTaxonomyButton.textContent = "Close form";
    }
  })


  addTaxonomyButton.addEventListener('click', () => {
    addTaxonomyForm.classList.toggle('hidden')
    if (addTaxonomyForm.classList.contains('hidden')) {
      // Reset button text
      addTaxonomyButton.textContent = "Create new taxonomy"

      // reset form fields
      document.getElementById('tax_id').value = '';
      document.getElementById('brand_id').value = '';
      document.getElementById('name').value = '';
      // document.getElementById('is_system_defined').value = '';
      document.querySelector(`input[name="is_system_defined"][value="0"]`).checked = 'false';
      document.querySelector(`input[name="is_system_defined"][value="1"]`).checked = 'false';

      document.getElementById('desc').value = '';

      // Reset submit Button & top heading text
      document.querySelector('#new_tax_submit_button').textContent = 'Create new taxonomy'
      document.querySelector('#top-heading').textContent = 'Add new taxonomy'


    } else {
      // Update button text
      addTaxonomyButton.textContent = "Close form"
    }
  })


});
</script>


<?php get_footer();