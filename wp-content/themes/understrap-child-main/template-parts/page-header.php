<?php
// $sm = StateManager::GI();
// $options = $sm->options;
// $active_theme_prefix =  $sm->current_brand->brand_code;
?>
<style>
.dropdown-form-width {
  width: 523px;
  padding: 5px 10px;
  margin: 0;
}

/* .search_field_container .has-search{
		display: inline-block;
	}
	.search_field_container label{
		margin: 0;
	} */
.dropdown-form-width .search_field_container {
  width: 100%;
}

.search_field_container .has-search .form-control-feedback {
  position: absolute;
  z-index: 2;
  display: block;
  width: 2.375rem;
  height: 2.375rem;
  line-height: 30px;
  text-align: center;
  pointer-events: none;
  color: #aaa;
}

.search_field_container .has-search .search-field {
  font-size: 14px;
  height: 30px;
  color: #707070;
  font-weight: 400;
  padding-left: 2.375rem;
}

.dropdown-form-width .search_field_container .sku-search-form {
  display: flex;
  justify-content: space-between;
  flex-direction: row;
  flex-wrap: nowrap;
  align-content: space-between;
  align-items: center;
}
</style>

<div class="header-mini ">
  <nav class="navbar navbar-expand-lg menu-1">
    <div class="container">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
      </button>
      <!-- The WordPress Menu goes here -->
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
        <ul class="navbar-nav">
          <?php

				$menu_items = wp_get_nav_menu_items( 'Menu 1' );
				// $stm = SettingsManager::GI();
				// // $stm->dump($menu_items);
				// $all_brands = $sm->get_all_brands();
				$dropdown = "";
				$dropdown_toggle = "";
				$brands= false;
				$link_attr = "";
				$current_page = get_query_var( 'pg'  );
				foreach ( $menu_items as $menu_item ) {

					// -------Permission Logic Starts------------
					if($menu_item->post_name == 'brands') {
						// bcz brands page is named products-index in `pim_pages` DB table
						$menu_item->post_name = 'products-index';
					}

					// check permissions (arrow method)
					if($stm->check_page_permission($menu_item->post_name) == 'HIDE') {
						continue;
					}

					if($menu_item->post_name == 'products-index') {
						// bcz we manipulated it previously , so lets restore it (as we have checked its permission in previou code block)
						$menu_item->post_name = 'brands';
					}
					// -------Permission Logic ENDs------------

					$dropdown = "";
					$brands= false;
					$dropdown_toggle = "";
					$subclass="";
					$link_attr="";
					$slug_extended= '';
					$href=esc_url( home_url( '/'.$menu_item->post_name ) );
					if($menu_item->post_title=='Brands' || $menu_item->post_title=='MAM'  || $menu_item->post_title=='Retail Channels'  || $menu_item->post_title=='Favorites'){
						$dropdown = "dropdown";
						$dropdown_toggle = "dropdown-toggle";
						$subclass=" drop-sub-menu-row";
						$link_attr = 'id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"';
						$brands= true;
						if($menu_item->post_title=='MAM'  || $menu_item->post_title=='Retail Channels' || $menu_item->post_title=='Favorites')
						{
							$slug_extended= "/".$menu_item->post_name;
						}

						$href="#";
					}

					if($menu_item->post_title=='SKUs' ){
						$dropdown = "dropdown";
						$dropdown_toggle = "dropdown-toggle";
						$subclass=" dropdown-form-width";
						$link_attr = 'id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"';
						$href="#";
					}
					$active = "";
					if($current_page ==$menu_item->post_name){
						$active = "active";
					}
					echo '	<li class="nav-item '.$dropdown.'">
								<a class="nav-link '.$dropdown_toggle.' '.$active.'" '.$link_attr.' href="'.$href.'">'.$menu_item->post_title.'</a>';
							if($dropdown!=''){
								echo '<ul class="dropdown-menu '.$subclass.'" aria-labelledby="navbarDropdownMenuLink">';
								if($brands){
									// foreach ($all_brands as $brand) {
									// 	if($menu_item->post_title=='MAM'  || $menu_item->post_title=='Retail Channels' || $menu_item->post_title=='Favorites'){
									// 		if(in_array($brand->slug,$sm->current_user->user_brand_slugs) || current_user_can( 'administrator' )){
									// 			echo '<li><a class="dropdown-item" href="'.esc_url( home_url( '/'.$brand->slug.$slug_extended ) ).'">'.$brand->brand_name.'</a></li>';
									// 		}
									// 	}else{
									// 		echo '<li><a class="dropdown-item" href="'.esc_url( home_url( '/'.$brand->slug.$slug_extended ) ).'">'.$brand->brand_name.'</a></li>';
									// 	}
									// }

									if($menu_item->post_title=='MAM' ){
										echo '<li><a class="dropdown-item" href="'.esc_url( home_url( '/download-queue' ) ).'">Download Queue</a></li>';
									}
								}
								if($menu_item->post_title=='SKUs'){
									echo '<div class="search_field_container">
											<form action="'.esc_url(home_url('/search-results')).'" class="sku-search-form">
												<label for="search_txt">SKU Number, Product Name Or Model Number</label>
												<div class="form-group has-search">
													<span class="fa fa-search form-control-feedback"></span>
													<input type="text" id="search_txt" name="search_txt" class="search-field form-control  mam_search_field" placeholder="Search" value="">

												</div>
											</form>
										</div>';
										// echo '<div class="search_field_container">
										// 	<form action="'.esc_url(home_url('/search-results')).'">
										// 		<label for="search_txt">SKU Number, Product Name Or Model Number</label>
										// 		<div class="input-group search-group">
										// 			<input type="text" class="search-field form-control" id="search_txt"
										// 				name="search_txt" placeholder="Search">
										// 			<button class="input-group-addon search-btn mm-search-btn"
										// 				type="submit">
										// 				<i class="fa fa-arrow-right" aria-hidden="true"></i>
										// 			</button>
										// 		</div>
										// 	</form>
										// </div>';
								}
								echo '</ul>';
							}
					echo '	</li>';


				}
				?>


          <?php
							// $stm = SettingsManager::GI();
							if($stm->check_page_permission('user-guide-wiki') != 'HIDE'): ?>


          <li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" id="menu-item-176964"
            class="wiki-popup menu-item menu-item-type-custom menu-item-object-custom menu-item-176964 nav-item"><a
              data-toggle="modal" data-target="#wikiModal" class="nav-link" title="User Guide Wiki" href="#"><span>User
                Guide Wiki</span></a></li>

          <?php endif; ?>





        </ul>
      </div>

    </div>
  </nav>
</div>