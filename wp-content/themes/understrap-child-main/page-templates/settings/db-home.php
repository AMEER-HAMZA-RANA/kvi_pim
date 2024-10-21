<?php
/**
 * Template Name: Settings DB Home Page
 *
 *
 * @package arrow
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header("blank-subheader");
$container = get_theme_mod( 'arrow_container_type' );
// $sm = StateManager::GI();
// $sm->g_importer->load_js();

?>
<style>
.importer_form {
  font-size: 14px;
}

/* .fa {
  position: static !important;
} */
</style>

<div class="wrapper-product wrapper <?php echo $active_theme_prefix;?>">
  <section class="page-heading-sec">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1 class="text-center ">Settings Page</h1>
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
                <!-- <h1>Dashboard</h1> -->
                <a class="d-block text-decoration-none  mb-2" href="<?php echo site_url('settings/db/tables'); ?>">All
                  Table Metas (Field Groups)</a>
                <a class="d-block text-decoration-none  mb-2"
                  href="<?php echo site_url('settings/db/taxonomies'); ?>">All Taxonomies</a>
                <a class="d-block text-decoration-none  mb-2" href="<?php echo site_url('settings/db/media'); ?>">All
                  Media</a>
                <a class="d-block text-decoration-none  mb-2"
                  href="<?php echo site_url('settings/db/seller-apps'); ?>">All Seller Apps</a>
                <a class="d-block text-decoration-none  mb-2"
                  href="<?php echo site_url('/permissions-management'); ?>">Permissions Management</a>

                <!-- <a href="<?php echo site_url('/db/tables'); ?>">Add New Table</a> -->
                <?php
								// echo "home <br>";
								// $set_mgr = SettingsManager::GI();



							?>
              </div>
              <!-- <div class="col-md-6">

              </div> -->



            </div>
          </div>
        </main><!-- #main -->

      </div><!-- #primary -->

    </div><!-- .row end -->

  </div><!-- #content -->

</div><!-- #full-width-page-wrapper -->

<?php get_footer();