<!-- <div id="mam-index-filter" class="mam-index-filter">
	<div class="mam-filters-index">
		<div class="filters-body">
			<div class="filter-container">


				<?php
				// $sm = StateManager::GI();
				// $sm->setup_favorites_filters();
				?>

			</div>
		</div>
	</div>
</div> -->


<div class="container">
	<div class="class-">
		<div class="dropdown-style">
			<div class="class">
				<div class="container">
					<div class="row">
						<!-- <div id="mam-index-filter" class="mam-index-filter"> -->
							<div class="mam-filters-index" >
								<div class="d-flex align-items-center">
									
									<?php
									$sm = StateManager::GI();
									$sm->setup_favorites_filters();
									?>
								</div>

							</div>

						<!-- </div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>