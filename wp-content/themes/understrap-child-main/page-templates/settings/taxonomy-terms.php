<?php
/**
 * Template Name: Settings DB Table Edit Page
 *
 *
 * @package arrow
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header("blank-subheader");
$container = get_theme_mod( 'arrow_container_type' );
$stm = SettingsManager::GI();
// $stm->g_importer->load_js();
// $field_groups_structure = $stm->generate_field_groups_structure(1742247);
// echo "<pre>";
// echo json_encode($field_groups_structure, JSON_PRETTY_PRINT);

?>

<style>
.importer_form {
  font-size: 14px;
}

.add-terms {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.add-terms h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #333;
  text-align: center;
}

.add-terms label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #555;
}

.add-terms input[type="text"],
.add-terms input[type="number"],
.add-terms textarea,
.add-terms select {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.add-terms .radio-group {
  display: flex;
  flex-direction: column;
  /* justify-content: space-between; */
  margin-bottom: 15px;
}

.add-terms .radio-group input[type="radio"] {
  margin-right: 5px;
}

.add-terms button {
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

.add-terms button:hover {
  background-color: #0056b3;
}

.hidden {
  display: none;
}

#add_term_btn {
  padding: 6px 10px;
  border-radius: 6px;
  border: 2px solid white;
  background: #000;
  color: white;
  font-size: 20px;
  margin-left: auto;
  display: block;
  margin-bottom: 10px;
  /* width: 100%; */
}

#add_term_form {
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

th,
table {
  border: 2px solid black !important;
  font-size: 18px;
}
</style>

<?php

// get taxonomy id from url query params and taxonomy from db using tbale_id
$tax_id = intval(get_query_var('tax_id'));
$taxonomy = $stm->get_full_row_from_table('pim_taxonomies', $tax_id);

$stm->return_if_empty_or_not_found($tax_id, 'Taxonomy Id not found.');
$stm->return_if_empty_or_not_found($taxonomy, 'Taxonomy Meta not found.');


// Handle the delete action
if (isset($_POST['delete_term_id'])) {
	$stm->delete_row_from_table($_POST['delete_term_id'], 'pim_taxonomy_terms');
	echo '<div class="success-message"Table deleted successfully!</div>';
	$stm->redirect_to_same_page();
	exit;
}



// Handle taxonomy update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['term_id']) && $_POST['term_id']) {
	$stm->update_taxonomy_term_in_db($_POST);
	$stm->redirect_to_same_page();
	exit;
}

// Handle new taxonomy creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
		$stm->create_taxonomy_term_in_db($_POST, $tax_id);
    $stm->redirect_to_same_page();
    exit;
}
?>

<div class="wrapper-product wrapper <?php echo $active_theme_prefix;?>">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1>Taxonomy Terms for: <?= $taxonomy->name ?></h1>
        </div>
      </div>
    </div>
  </section>

  <div class="<?php echo esc_attr( $container ); ?>" id="content">

    <div class="row ">
      <div class="col-md-12 content-area" id="primary">
        <main class="site-main" id="main" role="main">
          <div class="items_grid container">
            <div class="importer_form row justify-content-center py-4" style="background: white;"
              id="importer_form_container">
              <div class="col-md-12">

                <!-- ADD new field form -->
                <button id="add_term_btn">Create new term</button>
                <div id="add_term_form" class="add-terms hidden">
                  <h1>
                    <span id="top-heading">Add new term</span>
                  </h1>
                  <form id="tax_term_form" action="" method="post">
                    <input type="hidden" name="term_id" id="term_id">

                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required>

                    <label for="parent_id">Parent (optional)</label>
                    <select id="parent_id" name="parent_id">
                      <option value="" disabled selected>Select parent</option>
                      <?php
                        $taxonomy_terms = $stm->get_all_cols_in_one_to_many('pim_taxonomy_terms', 'tax_id', $tax_id);
                        foreach($taxonomy_terms as $term) {
                          echo '<option value="' . $term->id . '">' . $term->name . '</option>';
                        }
                      ?>
                    </select>

                    <label for="icon">Icon Link (optional)</label>
                    <input type="text" id="icon" name="icon">

                    <button id="new_term_submit_button" type="submit">Create Term</button>
                  </form>
                </div>



                <!-- ALREADY existing taxonomy_terms table -->
                <?php
								$taxonomy_terms = $stm->get_all_cols_in_one_to_many('pim_taxonomy_terms', 'tax_id', $tax_id);

								if ($taxonomy_terms): ?>
                <table class="table">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Parent</th>
                      <th>Icon</th>
                      <th>Name</th>
                      <th>Slug</th>
                      <th>Level</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($taxonomy_terms as $term): ?>
                    <tr>
                      <td><?php echo esc_html($term->id); ?></td>
                      <td><?php echo esc_html($term->parent_id); ?></td>
                      <td><?php echo esc_html($term->icon); ?></td>
                      <td><?php echo esc_html($term->name); ?></td>
                      <td><?php echo esc_html($term->slug); ?></td>
                      <td><?php echo esc_html($term->level); ?></td>
                      <td>
                        <button style="width:60px; margin-bottom:5px;" class="btn btn-primary edit-term-btn"
                          data-term-id="<?php echo esc_attr($term->id); ?>"
                          data-parent-id="<?php echo esc_attr($term->parent_id); ?>"
                          data-name="<?php echo esc_attr($term->name); ?>"
                          data-icon="<?php echo esc_attr($term->icon); ?>">Edit</button>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this term?');">
                          <input type="hidden" name="delete_term_id" value="<?php echo esc_attr($term->id); ?>">
                          <button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php else: ?>
                <p>No taxonomy terms found.</p>
                <?php endif; ?>





                <div class="category_terms_structure" style="
								font-size: 20px; display: flex;
								align-items: center;
								justify-content: center;
								margin: 20px 0;
								">
                  <?php $stm->display_taxonomies_and_terms_tree($tax_id); ?>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</div>


<!-- ****************** -->
<!-- ****** JAVASCRIPT ******** -->
<!-- ****************** -->

<script>
document.addEventListener('DOMContentLoaded', function() {

  const addTaxTermButton = document.querySelector('#add_term_btn')
  const addTaxTermForm = document.querySelector('#add_term_form')

  document.addEventListener('click', e => {
    if (e.target.closest('.edit-term-btn')) {
      // update submit button and top heading text
      document.querySelector('#new_term_submit_button').textContent = 'Update taxonomy term'
      document.querySelector('#top-heading').textContent = 'Update taxonomy term'


      //scroll to top (to edit form)
      window.scrollTo(0, 300);

      // get current(previous) values and populate form fields with them
      const termId = e.target.dataset.termId;
      const parentId = e.target.dataset.parentId;
      const name = e.target.dataset.name;
      const icon = e.target.dataset.icon;


      document.getElementById('term_id').value = termId;
      document.getElementById('parent_id').value = parentId;
      document.getElementById('name').value = name;
      document.getElementById('icon').value = icon;

      addTaxTermForm.classList.remove('hidden');
      addTaxTermButton.textContent = "Close form";


      // show all select parent options
      [...document.getElementById('parent_id').children].forEach(el => el.style.display = 'block');
      // hide the term being edited from options of parent, bcz same term cannot be the parent of its own
      [...document.getElementById('parent_id').children].find(el => el.value == termId).style.display = 'none';

    }
  })


  addTaxTermButton.addEventListener('click', () => {
    addTaxTermForm.classList.toggle('hidden')
    if (addTaxTermForm.classList.contains('hidden')) {
      // Reset button text
      addTaxTermButton.textContent = "Create new taxonomy term"

      document.getElementById('term_id').value = '';
      document.getElementById('parent_id').value = '';
      document.getElementById('name').value = '';
      document.getElementById('icon').value = '';

      // Reset submit Button & top heading text
      document.querySelector('#new_term_submit_button').textContent = 'Create new taxonomy term'
      document.querySelector('#top-heading').textContent = 'Add new taxonomy term'


    } else {
      // Update button text
      addTaxTermButton.textContent = "Close form"
    }
  })


});
</script>



<?php get_footer();