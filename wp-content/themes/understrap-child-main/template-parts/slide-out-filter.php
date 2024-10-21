<?php
	$stm = SettingsManager::GI();
	// $options = $sm->options;
	// $active_theme_prefix =  $sm->current_brand->brand_code;
?>
<div class="container" id="slide-out-filter">
  <div class="class-">
    <div class="dropdown-style">
      <div class="class">
        <div id="brand-index-filter" class="mam-index-filter position-relative">
          <div class="container">
            <div class="row">
              <div class="d-flex align-items-center">

                <?php
									//$sm = StateManager::GI();
									$stm->setup_products_filters();
								?>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- <div id="brand-index-filter" class="brands-index-filter open"  >
	<div class="brand-index-wrapper filter-pullout-bg">
		<a class="filters-header" data-toggle="collapse" href="#filters-body" aria-expanded="true" aria-controls="filters-body" role="button">
			<div class="filter-container" >
				<div class="filter-heading">
					<h4>Filters</h4>
				</div>
				<div class="dropdown d-md-none"><i class="fa fa-caret-down" aria-hidden="true"></i></div>
			</div>
		</a>
		<div class="filters-body collapse"  id="filters-body">
			<div class="filter-container">

			<?php
				//$sm = StateManager::GI();
				//$sm->setup_products_filters();
			?>

			</div>
		</div>
	</div>
</div> -->