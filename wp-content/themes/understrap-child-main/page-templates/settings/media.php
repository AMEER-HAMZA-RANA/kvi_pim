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

#add_group_btn {
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

#add_group_form {
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
          <h1>Existing Media Groups</h1>
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
if (isset($_POST['delete_m_group_id']) && $_POST['delete_m_group_id']) {
	$stm->delete_row_from_table($_POST['delete_m_group_id'], 'pim_media_groups');
	echo '<div class="success-message"Table deleted successfully!</div>';
	$stm->redirect_to_same_page();
	exit;
}



// Handle taxonomy update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_name']) && isset($_POST['m_group_id']) && $_POST['m_group_id']) {
	$stm->update_media_group_in_db($_POST);
	$stm->redirect_to_same_page();
	exit;
}

// Handle new taxonomy creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_name'])) {
		$stm->create_media_group_in_db($_POST);
    $stm->redirect_to_same_page();
    exit;
}
?>

                <button id="add_group_btn">Create new media group</button>

                <div id="add_group_form" class="form-container hidden">
                  <h1>
                    <span id="top-heading">Add new media group</span>
                  </h1>
                  <form action="" method="post">
                    <input type="hidden" name="m_group_id" id="m_group_id">
                    <label for="brand_id">Brand *</label>
                    <!-- <input type="text" name="brand_id" required> -->
                    <select id="brand_id" name="brand_id" required>
                      <option value="" disabled selected>Select a brand</option>
                      <?php $brands = $stm->get_all_rows_and_cols_from_table('pim_brands'); ?>
                      <?php if ($brands): ?>
                      <?php foreach ($brands as $brand): ?>
                      <option value="<?= $brand->id ?>"><?= ucfirst($brand->name) ?></option>
                      <?php endforeach; ?>
                      <?php endif; ?>
                    </select>

                    <label for="group_name">Group Name *</label>
                    <input id="group_name" type="text" name="group_name" required>

                    <!-- <label for="desc">Description(optional)</label>
                    <textarea id="desc" name="desc"></textarea> -->

                    <label for="is_class_specific">Is Class Specific</label>
                    <div class="radio-group">
                      <label><input type="radio" name="is_class_specific" value="1"> Yes</label>
                      <label><input type="radio" name="is_class_specific" value="0"> No</label>
                    </div>

                    <label for="display_order">Display Order</label>
                    <input type="number" name="display_order" id="display_order">

                    <!-- <label for="ui_color">UI Color *</label>
                    <input type="color" name="ui_color" required> -->

                    <!-- <label for="add_info">Additional Info(optional)</label>
                    <textarea name="add_info"></textarea> -->

                    <button id="new_media_submit_button" type="submit" name="submit">Create media group</button>
                  </form>
                </div>


              </div>





              <!-- ALREADY Taxonomies table -->
              <div class='existing_media_groups_table'>
                <?php $media_groups = $stm->get_all_rows_and_cols_from_table('pim_media_groups'); ?>
                <?php if ($media_groups): ?>
                <table class="table">
                  <thead>
                    <tr>
                      <!-- <th>id</th> -->
                      <th>Brand Id</th>
                      <th>Group Name</th>
                      <th>Is Class Specific</th>
                      <th>Display Order</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($media_groups as $group): ?>
                    <tr>
                      <!-- <td><?php echo esc_html($group->id); ?></td> -->
                      <td><?php echo esc_html($group->brand_id); ?></td>
                      <td><?php echo esc_html($group->group_name); ?></td>
                      <td><?php echo esc_html($group->is_class_specific); ?></td>
                      <td><?php echo esc_html($group->display_order); ?></td>
                      <td>
                        <a style="display:block;width:60px;margin-bottom:5px;color:white; background:green;border-radius:4px;padding:4px 10px;font-size:10px;text-decoration:none; text-align:center;"
                          href="<?php echo site_url("/settings/db/media/edit/?m_grp_id={$group->id}/"); ?>"
                          class="">Assigns.</a>
                        <button style="width:60px; margin-bottom:5px;" class="btn btn-primary edit-m-group-btn"
                          data-group-id="<?php echo esc_attr($group->id); ?>"
                          data-group-name="<?php echo esc_attr($group->group_name); ?>"
                          data-is-class-specific="<?php echo esc_attr($group->is_class_specific); ?>"
                          data-display-order="<?php echo esc_attr($group->display_order);?>"
                          data-brand-id="<?php echo esc_attr($group->brand_id);?>">Edit</button>
                        <form method="post"
                          onsubmit="return confirm('Are you sure you want to delete this media group?');">
                          <input type="hidden" name="delete_m_group_id" value="<?php echo esc_attr($group->id); ?>">
                          <button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php else: ?>
                <p>No media groups found.</p>
                <?php endif; ?>
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

  const addMediaGroupButton = document.querySelector('#add_group_btn')
  const addMediaGroupForm = document.querySelector('#add_group_form')


  // const editFieldButtons = document.querySelectorAll('.edit-field-btn');
  document.addEventListener('click', e => {
    if (e.target.closest('.edit-m-group-btn')) {
      // update submit button and top heading text
      document.querySelector('#new_media_submit_button').textContent = 'Update media group'
      document.querySelector('#top-heading').textContent = 'Update media group'


      //scroll to top (to edit form)
      window.scrollTo(0, 300);

      // get current(previous) values and populate form fields with them
      const id = e.target.dataset.groupId;
      const brandId = e.target.dataset.brandId;
      const groupName = e.target.dataset.groupName;
      const isClassSpecific = e.target.dataset.isClassSpecific;
      const displayOrder = e.target.dataset.displayOrder;


      document.getElementById('m_group_id').value = id;
      document.getElementById('brand_id').value = brandId;
      document.getElementById('group_name').value = groupName;
      document.querySelector(`input[name="is_class_specific"][value="${isClassSpecific}"]`).checked = true;
      document.getElementById('display_order').value = displayOrder;

      // document.querySelector('is_class_specific').value = isSystemDefined;
      // document.getElementById('field_options_textarea').value = fieldOptions;
      // document.getElementById('field_value_input').value = fieldValue;

      addMediaGroupForm.classList.remove('hidden');
      addMediaGroupButton.textContent = "Close form";
    }
  })


  addMediaGroupButton.addEventListener('click', () => {
    addMediaGroupForm.classList.toggle('hidden')
    if (addMediaGroupForm.classList.contains('hidden')) {
      // Reset button text
      addMediaGroupButton.textContent = "Create new media group"

      // reset form fields
      document.getElementById('m_group_id').value = '';
      document.getElementById('brand_id').value = '';
      document.getElementById('group_name').value = '';
      document.querySelector(`input[name="is_class_specific"][value="0"]`).checked = false;
      document.querySelector(`input[name="is_class_specific"][value="1"]`).checked = false;
      document.getElementById('display_order').value = '';

      // Reset submit Button & top heading text
      document.querySelector('#new_media_submit_button').textContent = 'Create new media group'
      document.querySelector('#top-heading').textContent = 'Add new media group'


    } else {
      // Update button text
      addMediaGroupButton.textContent = "Close form"
    }
  })


});
</script>



<?php get_footer();
