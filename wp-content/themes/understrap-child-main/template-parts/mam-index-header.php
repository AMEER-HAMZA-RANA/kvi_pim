<?php
	// wp_enqueue_script("owl-carousel");
	// wp_enqueue_style("swiper-carousel-css");
	// wp_enqueue_script('carousel-swiper-subheader');
	// $sm = StateManager::GI();
	$stm = SettingsManager::GI();

	$options = $sm->options;
	// $active_theme_prefix =  $sm->current_brand->brand_code;
	// $media_types = $sm->mam->get_media_types();
	// $active_media_type = $sm->mam->get_current_media_type();
  
  
  // Arrow (new) way
	$active_brand_slug = 'kvi';
		$ar_active_media_group = $stm->get_current_media_group();
		$media_groups = $stm->get_media_groups();
		// media_type
?>


<nav class="navbar navbar-expand-lg p-0 menu-1">
  <div class="container">
    <a class="navbar-brand" href="#">
      KVI
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown1"
      aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown1">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php if(empty($ar_active_media_group->slug) ==""){echo "active";}?>" aria-current="page"
            href="<?php echo home_url('/'). $active_brand_slug .'/mam';?>">ALL</a>
        </li>
        <?php
					foreach($media_groups as $media_type){
						$img = get_term_meta($media_type->id, "category_icon", true);
						$class = "";
						if(isset($ar_active_media_group->slug) && $ar_active_media_group->slug  ==$media_type->slug){
							$class = "active";
						}
						?>
        <li class="nav-item">
          <a class="nav-link <?php echo $class?>" data-id="<?php echo $media_type->id;?>"
            href="<?php  echo home_url('/'). $active_brand_slug .'/mam/type/'.$media_type->slug;?>"><?php echo $media_type->group_name;?></a>
        </li>
        <?php
					}
					?>

      </ul>
    </div>
  </div>
</nav>