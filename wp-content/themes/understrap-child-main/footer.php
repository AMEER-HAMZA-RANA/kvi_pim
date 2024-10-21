<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package arrow
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
// $options = get_option( 'arrow_options' );
global $active_brand_prefix;
$active_theme_prefix = $active_brand_prefix;
// $sm = SettingsManager::GI();
$brand_slug = 'kvi';
// $sm->wiki->load_js();
// $user_roles = $sm->current_user->user_roles;
// echo "<pre>";
// print_r($user_roles[0]);
// echo "</pre>";
?>
<?php require( locate_template( 'template-parts/wiki-modal.php', false, false ) );?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<footer class="site-footer wrapper" id="wrapper-footer">

  <div class="<?php echo esc_attr( $container ); ?>">

    <div class="row">
      <div class="col-md-3">
        <div class="site_info">
          <a class="logo" href="<?php echo home_url('/');?>">KVI</a>
          <p class="highlighted_footer_text">Product Information Management System</p>
          <p class="address-info">KVI Address</p>
        </div><!-- .site-info -->
      </div>
      <!--col end -->
      <div class="col-md-9 px-md-6">
        <div class="row">
          <div class="col-md-3">
            <div class="footer_menu_block">
              <p class="menu-heading">Brand</p>
              <ul>
                <li><a href="<?php echo home_url( "/" );?>">KVI</a></li>
                <!-- <?php if(substr($user_roles[0],0,7)!="shopvac"){?>
                <li><a href="<?php echo home_url( "/arrow" );?>">Arrow Fastener</a></li>
                <li><a href="<?php echo home_url( "/pony-jorgensen" );?>">PONY | Jorgensen</a></li>
                <li><a href="<?php echo home_url( "/goldblatt" );?>">Goldblatt</a></li>
                <?php if($user_roles[0]=='administrator'){?>
                <li><a href="<?php echo home_url( "/shopvac" );?>">Shop.Vac</a></li>
                <?php }?>
                <?php }else{?>
                <li><a href="<?php echo home_url( "/shopvac" );?>">Shop.Vac</a></li>
                <?php }?>
              </ul> -->
            </div><!-- .site-info -->
          </div>
          <!--col end -->
          <div class="col-md-3">
            <div class="footer_menu_block">
              <p class="menu-heading">MAM</p>
              <ul>
                <li><a href="<?php echo home_url( "/kvi/mam" );?>">KVI</a></li>
                <!-- <?php if(substr($user_roles[0],0,7)!="shopvac"){?>
                <li><a href="<?php echo home_url( "/arrow/mam" );?>">Arrow Fastener</a></li>
                <li><a href="<?php echo home_url( "/pony-jorgensen/mam" );?>">PONY | Jorgensen</a></li>
                <li><a href="<?php echo home_url( "/goldblatt/mam" );?>">Goldblatt</a></li>
                <?php if($user_roles[0]=='administrator'){?>
                <li><a href="<?php echo home_url( "/shopvac/mam" );?>">Shop.Vac</a></li>
                <?php }?>
                <?php }else{?>
                <li><a href="<?php echo home_url( "/shopvac/mam" );?>">Shop.Vac</a></li>
                <?php }?> -->

              </ul>

            </div><!-- .site-info -->
          </div>
          <!--col end -->

          <div class="col-md-3 pl-md-5">
            <div class="footer_menu_block">
              <p class="menu-heading">Retail Channels</p>
              <ul>
                <li><a href="<?php echo home_url( "/kvi/retail-channels" );?>">KVI</a></li>
                <!-- <?php if(substr($user_roles[0],0,7)!="shopvac"){?>
                <li><a href="<?php echo home_url( "/arrow/retail-channels" );?>">Arrow Fastener</a></li>
                <li><a href="<?php echo home_url( "/pony-jorgensen/retail-channels" );?>">PONY | Jorgensen</a></li>
                <li><a href="<?php echo home_url( "/goldblatt/retail-channels" );?>">Goldblatt</a></li>
                <?php if($user_roles[0]=='administrator'){?>
                <li><a href="<?php echo home_url( "/shopvac/retail-channels" );?>">Shop.Vac</a></li>
                <?php }?>
                <?php }else{?>
                <li><a href="<?php echo home_url( "/shopvac/retail-channels" );?>">Shop.Vac</a></li>
                <?php }?> -->
              </ul>
            </div><!-- .site-info -->
          </div>
          <!--col end -->

          <div class="col-md-3">
            <div class="footer_menu_block">
              <p class="menu-heading">Search SKU</p>
              <form action="<?php echo home_url('/search-results/');?>">
                <div class="input-group search-group mb-3">
                  <input type="text" class="search-field form-control" name="search_txt" placeholder="Search" />
                  <button class="input-group-addon search-btn mm-search-btn" type="submit">
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                  </button>
                </div>
              </form>
              <p class="menu-heading">Quick Links</p>
              <ul>
                <li><a href="<?php echo home_url( "/sales-territories" );?>">Sales Territories</a></li>
                <li><a href="<?php echo home_url( "/{$brand_slug}/favorites" );?>">Favorites</a></li>
              </ul>


            </div><!-- .site-info -->
          </div>
          <!--col end -->

        </div>
      </div>



    </div><!-- row end -->

  </div><!-- container end -->

</footer><!-- #wrapper-footer -->

<!-- </div>#page we need this extra closing tag here -->

<?php wp_footer(); ?>
<script>
if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
  window.location.reload();
}
</script>
</body>

</html>