<?php
	$sm = StateManager::GI();
	$queues = $sm->get_all_queues();
	$queue_id = get_query_var("queue_id");
?>
<div id="mam-index-filter" class="mam-index-filter">
	<div class="mam-filters-index">
		<a class="filters-header" data-toggle="collapse" href="#filters-body" aria-expanded="true" aria-controls="filters-body" role="button">
			<div class="filter-container" >
				<div class="filter-heading">
					<h4>Queue History</h4>
				</div>
				<div class="dropdown d-md-none"><i class="fa fa-caret-down" aria-hidden="true"></i></div>
			</div>
		</a>
		<div class="filters-body collapse"  id="filters-body">
			<div class="filter-container">
				<div class="checkboxes-container filter-section-container">
					<h4 class="filter-title">Queues History</h4>
					<ul>
					<?php
						$class = "";
						if(empty($queue_id)){
							$class = "active";
						}
						echo "<li><a class='{$class}' href='".home_url( "/download-queue/" )."'>Current Queue</a></li>";
						foreach($queues as $queue){
							$class = "";
							if(!empty($queue_id) && $queue_id == $queue['id']){
								$class = "active";
							}
							if("Live" == $queue['status']){
								echo "<li><a class='{$class}' href='".home_url( "/download-queue/view/" )."{$queue['id']}'>Prepared Archive - <span style='font-size:smaller;'>{$queue['date']} ({$queue['items']} items)</span></a></li>";
							}
							else{
								if($queue['name'] == ''){
									$queue_name = $queue['status'];
								}else{
									$queue_name = $queue['name'];
									if($queue['duration'] != ''){
										$queue_name .= " - ".$queue['duration'];
									}
								}
								echo "<li><a class='{$class}' href='".home_url( "/download-queue/view/" )."{$queue['id']}'>".$queue_name." - <span style='font-size:smaller;'>({$queue['items']} items)</span></a></li>";
							}

						}

					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
