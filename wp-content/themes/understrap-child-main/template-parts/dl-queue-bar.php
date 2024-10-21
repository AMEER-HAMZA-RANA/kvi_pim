<?php

$sm = StateManager::GI();
global $queued_assets;
global $download_link;
global $count_of_items;
$queued_assets = $sm->get_the_queued_assets();
$download_link = $sm->get_the_queues_download_link();
$count_of_items = count($queued_assets);
$queue_id = get_query_var("queue_id");
?>
<style>
    .mam-index-filter:after{
        content: unset;
    }
	.wrapper .items_grid_row{
        justify-content: space-between;
    }
		.buttons-row .btn-style-1{
    color: white;
    font-family: SANS-SERIF;
    text-transform: capitalize;
    border: 1px solid grey;
    font-weight: normal;

    margin-left: 19px;
    min-width: 162px;
    text-align: center;
    text-decoration: none;
    padding: 10px;
    margin-bottom: 0;

    font-size: 14px;

    line-height: 1.2;

}
.buttons-row .bg-download{
background-color: #839CC4;
}
.buttons-row .bg-share{
background-color: #A7C483
}
.buttons-row .bg-download-q{
background-color: #A7C483
}



/* Items_grid.scss */

.buttons-row.items_grid_row{

    justify-content: right;
    margin-top: 15px;

}

.note-dl{
    font-size: 14px;
    margin-left: 8px;
    color: #707070;
}
</style>

<div class="container">	
	<div class="class-">
		<div class="dropdown-style">
			<div class="class">
				<div class="container">
					<div class="row">
						<div class="mam-filters-index">

							<div class="d-flex align-items-center flex-column ">
                                <div class="note-dl">Please Note: The queue and the associated zipped file will be deleted 48 hours after creation time and date.</div>
                                <div class="buttons-row items_grid_row  align-items-center">
                                    <div class="note-dl"><?php echo "Items in queue: <span id='item_count'>{$count_of_items}</span>/{$sm->dl_queue->max_limit}"; ?></div>
                                    <?php
                                    if($count_of_items>0){
                                        if(!empty($download_link)){
                                            ?>
                                            <a target="_blank" href="<?php echo $download_link;?>" id="btn-download-1" download=""><i class="fa fa-download" aria-hidden="true"></i> Download</a>
                                            <?php
                                        }
                                        else{
                                            ?>
                                            <a class="btn-style-1 btn-black bg-download" target="_blank" href="#" data-toggle="modal" data-target="#assetDownloadModal">Download</a>
                                            <a class="btn-style-1 btn-black bg-share" href="#" class="share"  data-toggle="modal" data-target="#assetShareModal">Share</a>
                                            <a class="btn-style-1 btn-black bg-empty-q" href="#" id="btn-delete">Empty Queue</a>
                                            <?php
                                        }
                                        ?>

                                    <?php
                                    }
                                    ?>  
                                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>