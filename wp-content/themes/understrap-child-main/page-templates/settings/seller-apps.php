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

#add_seller_app_btn {
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

#add_seller_app_form {
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
          <h1>Existing Seller Apps</h1>
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


// Handle the update action
if (isset($_POST['update_seller_app_id']) && !empty($_POST['update_seller_app_id'])) {
	$stm->dump('sda asds asd');
	$stm->update_seller_app($_POST);
	echo '<div class="success-message">Seller App updated successfully!</div>';
	$stm->redirect_to_same_page();
	exit;
}

// Handle the delete action
if (isset($_POST['delete_seller_apps_id'])) {
	$stm->dump('sda asds asd 22');

	$stm->delete_seller_app_from_db($_POST['delete_seller_apps_id'], 'pim_seller_apps');
	echo '<div class="success-message"Seller App deleted successfully!</div>';
	$stm->redirect_to_same_page();
	exit;
}



// Handle new seller_app creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['seller_name']) && !empty($_POST['seller_name'])) {
	$stm->dump('sda asds asd 11');
		$stm->create_new_seller_app($_POST);
    $stm->redirect_to_same_page();
    exit;
}

?>

                <button id="add_seller_app_btn">Create new Seller App</button>

                <div id="add_seller_app_form" class="form-container hidden">
                  <div class="success-message" id="successMessage">
                    Form submitted successfully!
                  </div>
                  <form action="" method="post">
                    <!-- <label for="brand_id">Brand *</label> -->

                    <!-- <input type="text" name="brand_id" required> -->
                    <!-- <select name="brand_id" required>
                      <option disabled selected>Select a brand</option>
                      <option value="1">Arrow</option>
                    </select> -->

                    <!-- <select id="brand_id" name="brand_id" required>
                      <option value="" disabled selected>Select a brand</option>
                      <?php $brands = $stm->get_all_rows_and_cols_from_table('pim_brands'); ?>
                      <?php if ($brands): ?>
                      <?php foreach ($brands as $brand): ?>
                      <option value="<?= $brand->id ?>"><?= ucfirst($brand->name) ?></option>
                      <?php endforeach; ?>
                      <?php endif; ?>
                    </select> -->

                    <label for="seller_name">Seller Name *</label>
                    <input id="seller_name" type="text" name="seller_name" required>

                    <label for="desc">Description</label>
                    <textarea id="desc" name="desc"></textarea>

                    <!-- <label for="display_order">Display Order</label>
                    <input type="number" name="display_order"> -->

                    <!-- <label for="ui_color">UI Color *</label>
                    <input type="color" name="ui_color" required> -->

                    <!-- <label for="add_info">Additional Info(optional)</label>
                    <textarea name="add_info"></textarea> -->
                    <input type="hidden" id="edit_seller_app_id" name="update_seller_app_id">


                    <button type="submit" name="submit">Create Seller App</button>
                  </form>
                </div>


              </div>


              <!-- ALREADY existing tables table -->
              <div class='existing_seller_apps_table'>
                <?php $seller_apps = $stm->get_all_rows_and_cols_from_table('pim_seller_apps');  ?>
                <?php if ($seller_apps): ?>
                <table class="table">
                  <thead>
                    <tr>
                      <th>Seller Name</th>
                      <th>Slug</th>
                      <th>Description</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($seller_apps as $seller_app): ?>
                    <tr>
                      <td><?php echo esc_html($seller_app->name); ?></td>
                      <td><?php echo esc_html($seller_app->slug); ?></td>
                      <td><?php echo esc_html($seller_app->description); ?></td>
                      <td>
                        <!-- <a style="display:block;width:60px;margin-bottom:5px;color:white; background:darkblue;border-radius:4px;padding:4px 10px;font-size:10px;text-decoration:none; text-align:center;"
                          href="<?php echo site_url("/settings/db/tables/edit/?tbl_id={$seller_app->id}/"); ?>"
                          class="">Fields</a> -->

                        <button style="width:60px;" class="btn btn-primary edit-btn"
                          data-id="<?php echo $seller_app->id; ?>"
                          data-name="<?php echo esc_attr($seller_app->name); ?>"
                          data-desc="<?php echo esc_attr($seller_app->description); ?>">Edit</button>

                        <form method="post"
                          onsubmit="return confirm('Are you sure you want to delete this seller app ?');">
                          <input type="hidden" name="delete_seller_apps_id"
                            value="<?php echo esc_attr($seller_app->id); ?>">
                          <button style="width:60px;" type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php else: ?>
                <p>No seller apps found.</p>
                <?php endif; ?>

              </div>


              <div class="tables_div"></div>

            </div>
          </div>
        </main><!-- #main -->

      </div><!-- #primary -->

    </div><!-- .row end -->

  </div><!-- #content -->

</div><!-- #full-width-page-wrapper -->


<script>
document.addEventListener('DOMContentLoaded', e => {
  const addSellerAppButton = document.querySelector('#add_seller_app_btn')
  const addSellerAppForm = document.querySelector('#add_seller_app_form')

  addSellerAppButton.addEventListener('click', () => {
    activateOrDeactivateSellerForm("Create new Seller App")

    document.querySelector('button[type="submit"]').innerHTML = 'Create new Seller App'
    addSellerAppForm.querySelectorAll('input').forEach(el => el.value = '')
  })


  function activateOrDeactivateSellerForm(buttonText) {
    addSellerAppForm.classList.toggle('hidden')
    if (addSellerAppForm.classList.contains('hidden')) {
      addSellerAppButton.
      textContent = buttonText
    } else {
      addSellerAppButton.textContent = "Close form"
    }
  }

  function activateSellerForm(buttonText) {
    addSellerAppForm.classList.remove('hidden')
    if (addSellerAppForm.classList.contains('hidden')) {
      addSellerAppButton.
      textContent = buttonText
    } else {
      addSellerAppButton.textContent = "Close form"
    }
  }


  const sellerEditButton = document.querySelector('button.edit-btn')

  sellerEditButton.addEventListener('click', e => {
    e.preventDefault()

    // if (addSellerAppForm.classList.contains('hidden')) {

    // addSellerAppForm.classList.remove('hidden')
    console.log('cliclked')

    const id = e.target.dataset.id
    const name = e.target.dataset.name
    const desc = e.target.dataset.desc

    addSellerAppForm.querySelector('#seller_name').value = name
    addSellerAppForm.querySelector('#desc').value = desc
    addSellerAppForm.querySelector('#edit_seller_app_id').value = id

    activateSellerForm("Update Seller App")

    window.scrollTo(0, 300);

    document.querySelector('button[type="submit"]').innerHTML = 'Update existing Seller App'

    // }
  })
})
</script>




<?php get_footer();