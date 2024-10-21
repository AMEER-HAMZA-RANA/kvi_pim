<?php
	// wp_enqueue_style("swiper-carousel-css");
	// wp_enqueue_script('carousel-swiper-subheader');

	// $sm = StateManager::GI();
	$stm = SettingsManager::GI();

	$brand_code = 'kvi';
	// $prod_categories = $sm->get_current_brands_product_categories();
	// $active_cat = $sm->pm->get_current_category();
	// $active_theme_prefix =  $sm->current_brand->brand_code;
	$active_brand_slug = 'kvi';
	// var_dump( get_term_meta($prod_categories[0]->term_id, "category_image", true ));
	// var_dump($prod_categories);
	// wp_die();

	// $brand_code (new) way
	$ar_active_cat = $stm->get_current_sub_brands_category();
	$sub_brand_cats = $stm->get_sub_brands_categories();
	// $stm->dump($ar_active_cat);
?>

<nav class="navbar navbar-expand-lg p-0 menu-1">
  <div class="container">
    <a class="navbar-brand" href="#">
      <!-- <img src="<?php echo $sm->options[$brand_code.'_logo_index_header']['url'];?>" alt=""> -->
      KVI
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown1"
      aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown1">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php if(!isset($ar_active_cat->slug)){echo "active";}?>" aria-current="page"
            href="<?php echo home_url('/') . $brand_code . '/products';?>">ALL</a>
        </li>
        <?php
					foreach($sub_brand_cats as $prod_category){
						$img = get_term_meta($prod_category->id, "category_icon", true);
						$class = "";
            // if(isset($ar_active_cat))
						if(isset($ar_active_cat->slug) && $ar_active_cat->slug ==$prod_category->slug){
							$class = "active";
						}
						?>
        <li class="nav-item">
          <a class="nav-link <?php echo $class?>"
            href="<?php  echo home_url('/'). $brand_code .'/products/category/'.$prod_category->slug;?>"
            data-id="<?php echo $prod_category->id;?>"><?php echo $prod_category->name;?></a>
        </li>
        <?php
					}
					?>

      </ul>
    </div>
  </div>
</nav>