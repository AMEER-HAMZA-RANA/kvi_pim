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

.add_assignments {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.add_assignments h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #333;
  text-align: center;
}

.add_assignments label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #555;
}

.add_assignments input[type="text"],
.add_assignments input[type="number"],
.add_assignments textarea,
.add_assignments select {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.add_assignments .radio-group {
  display: flex;
  flex-direction: column;
  /* justify-content: space-between; */
  margin-bottom: 15px;
}

.add_assignments .radio-group input[type="radio"] {
  margin-right: 5px;
}

.add_assignments button {
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

.add_assignments button:hover {
  background-color: #0056b3;
}

.hidden {
  display: none;
}

#add_assignment_btn {
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

#add_assignment_form {
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

// get table id from url query params and tbale_meta from db using tbale_id
$media_group_id = intval(get_query_var('m_grp_id'));
$media_group = $stm->get_full_row_from_table('pim_media_groups', $media_group_id);

$stm->return_if_empty_or_not_found($media_group_id, 'Media Assignment Id not found.');
$stm->return_if_empty_or_not_found($media_group, 'Media Group not found.');


// Handle the delete action
if (isset($_POST['delete_assignment_id']) && $_POST['delete_assignment_id']) {
	$stm->delete_row_from_table($_POST['delete_assignment_id'], 'pim_media_assignments');
	echo '<div class="success-message"Table deleted successfully!</div>';
	$stm->redirect_to_same_page();
	exit;
}



// Handle taxonomy update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignment_name']) && isset($_POST['m_assignment_id']) && $_POST['m_assignment_id']) {
	$stm->update_media_assignment_in_db($_POST);
	$stm->redirect_to_same_page();
	exit;
}

// Handle new taxonomy creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignment_name'])) {
		$stm->create_media_assignment_in_db($_POST, $media_group_id);
    $stm->redirect_to_same_page();
    exit;
}
?>

<div class="wrapper-product wrapper <?php echo $active_theme_prefix;?>">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1>Media Assignments for: <?= $media_group->group_name ?></h1>
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
                <button id="add_assignment_btn">Create new assignment</button>
                <div id="add_assignment_form" class="add_assignments hidden">
                  <h1>
                    <span id="top-heading">Add new assignment</span>
                  </h1>
                  <form id="media_assignment_form" action="" method="post">
                    <input type="hidden" name="m_assignment_id" id="m_assignment_id">

                    <label for="assignment_name">Assignment Name *</label>
                    <input type="text" name="assignment_name" id="assignment_name" required>

                    <label for="file_type">File Type *</label>
                    <select id="file_type" name="file_type" required>
                      <option value="" disabled selected>Select a file type</option>
                      <option value="image">Image</option>
                      <option value="audio">Audio</option>
                      <option value="video">Video</option>
                      <option value="link">Link</option>
                      <option value="zip">Zip</option>
                      <option value="doc">Doc</option>
                      <option value="pdf">PDF</option>
                      <option value="youtube_url">Youtube URL</option>
                      <option value="vimeo_url">Vimeo URL</option>
                      <option value="audio_url">Audio URL</option>
                      <option value="video_url">Video URL</option>
                      <option value="image_url">Image URL</option>
                    </select>

                    <label for="display_order">Display Order</label>
                    <input type="number" name="display_order" id="display_order">

                    <button id="new_assignment_submit_button" type="submit">Create Assignment</button>
                  </form>
                </div>



                <!-- ALREADY existing media_assignments table -->
                <?php
								$media_assignments = $stm->get_all_cols_in_one_to_many('pim_media_assignments', 'media_group_id', $media_group_id);

								if ($media_assignments): ?>
                <table class="table">
                  <thead>
                    <tr>
                      <!-- <th>Id</th> -->
                      <th>Assignment Name</th>
                      <th>File Type</th>
                      <th>Display Order</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($media_assignments as $assignment): ?>
                    <tr>
                      <!-- <td><?php echo esc_html($assignment->id); ?></td> -->
                      <td><?php echo esc_html($assignment->assignment_name); ?></td>
                      <td><?php echo esc_html($assignment->file_type); ?></td>
                      <td><?php echo esc_html($assignment->display_order); ?></td>
                      <td>
                        <button style="width:60px; margin-bottom:5px;" class="btn btn-primary edit-assignment-btn"
                          data-id="<?php echo esc_attr($assignment->id); ?>"
                          data-assignment-name="<?php echo esc_attr($assignment->assignment_name); ?>"
                          data-file-type="<?php echo esc_attr($assignment->file_type); ?>"
                          data-display-order="<?php echo esc_attr($assignment->display_order); ?>">Edit</button>
                        <form method="post"
                          onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                          <input type="hidden" name="delete_assignment_id"
                            value="<?php echo esc_attr($assignment->id); ?>">
                          <button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php else: ?>
                <p>No media assignments found.</p>
                <?php endif; ?>

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

  const addMediaAssignmentButton = document.querySelector('#add_assignment_btn')
  const addMediaAssignmentForm = document.querySelector('#add_assignment_form')

  document.addEventListener('click', e => {
    if (e.target.closest('.edit-assignment-btn')) {
      // update submit button and top heading text
      document.querySelector('#new_assignment_submit_button').textContent = 'Update'
      document.querySelector('#top-heading').textContent = 'Update media assignment'

      document.getElementById('file_type').disabled = true

      //scroll to top (to edit form)
      window.scrollTo(0, 300);

      // get current(previous) values and populate form fields with them
      const id = e.target.dataset.id;
      const assignmentName = e.target.dataset.assignmentName;
      const displayOrder = e.target.dataset.displayOrder;
      const fileType = e.target.dataset.fileType;


      document.getElementById('m_assignment_id').value = id;
      document.getElementById('assignment_name').value = assignmentName;
      document.getElementById('file_type').value = fileType;
      document.getElementById('display_order').value = displayOrder;

      addMediaAssignmentForm.classList.remove('hidden');
      addMediaAssignmentButton.textContent = "Close form";

    }
  })


  addMediaAssignmentButton.addEventListener('click', () => {
    addMediaAssignmentForm.classList.toggle('hidden')
    if (addMediaAssignmentForm.classList.contains('hidden')) {
      // Reset button text
      addMediaAssignmentButton.textContent = "Create new media assignment"

      document.getElementById('file_type').disabled = false

      document.getElementById('m_assignment_id').value = '';
      document.getElementById('assignment_name').value = '';
      document.getElementById('file_type').value = '';
      document.getElementById('display_order').value = '';

      // Reset submit Button & top heading text
      document.querySelector('#new_assignment_submit_button').textContent = 'Create new media assignment'
      document.querySelector('#top-heading').textContent = 'Add new media assignment'


    } else {
      // Update button text
      addMediaAssignmentButton.textContent = "Close form"
    }
  })


});
</script>



<?php get_footer();
