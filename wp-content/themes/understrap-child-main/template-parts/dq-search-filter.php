<div id="mam-index-filter" class="mam-index-filter">
	<div class="mam-filters-index">
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
				$sm = StateManager::GI();
				$sm->setup_mam_dq_filters();
				?>

			</div>
		</div>
	</div>
</div>
