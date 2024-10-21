<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The values from this class will be used by Assets Class
 */

class MAMFilters {

	public $media_filters;
	public $status_filters;
	public $current_directory;
	// public $current_listings;
	public $search_string;
	public $current_brand;

	public $active_filters;

	public $searchFilterSelector;
	public $searchBtnFilterSelector;
	public $mediaFiltersSelector;
	public $statusFiltersSelector;

	public function __construct($brand = null)
	{
		$this->active_filters = array();

		//Hide Media Filters on already filtered sub pages in MAM Index
		$ptype = get_query_var( "ptype" );
		if($ptype != 'media_type'){
			$this->active_filters[] = "media_filters";
		}
		$this->active_filters[] = "status_filters";

		// $this->current_brand = $brand;
		$this->current_directory = 'kvi' .'/';

		$this->searchFilterSelector = "mam_search_field";
		$this->searchBtnFilterSelector = "mam_search_btn";
		$this->mediaFiltersSelector  = "mam_cb_filters";
		$this->statusFiltersSelector = "status_cb_filters";

		// $this->load_search_filters();
		// $this->load_media_filters();
		// $this->load_status_filters();
		// $this->load_current_directory();

		//Enqueue JS
		// $this->load_JS();
	}

	public function display_filters(){
		echo "<div class='holder'>";

		// if($this->current_brand->brand_code == 'ar') {
			$this->display_ar_media_filters();
		// } 
		// else {
		// 	$this->display_media_filters();
		// }
		// $this->display_status_filters();
		echo "</div>";
		// echo "<div class='holder'>";
		// $this->display_current_dir_listings();
		// echo "</div>";
		echo "<div class='holder'>";
		$this->display_search_filter();
		echo "</div>";

		// echo '<div class="row">';
		// 	if(in_array('media_filters', $this->active_filters)){
		// 		echo '<div class="col-md-3">';

		// 		echo '</div>';
		// 	}
		// 	if(in_array('status_filters', $this->active_filters)){
		// 		echo '<div class="col-md-3">';

		// 		echo '</div>';
		// 	}
		// 	echo '<div class="col-md-3">';

		// 	echo '</div>';
		// 	echo '<div class="col-md-3">';

		// 	echo '</div>';
		// echo '</div>';
	}

	// public function load_current_directory(){
	// 	$sm = StateManager::GI();
	// 	$brand_code = $sm->current_brand->brand_code;
	// 	$listings = $sm->mam->get_listings_at_path($brand_code);
	// 	$this->current_listings = array();
	// 	foreach ($listings as $listing) {
	// 		if($listing['type'] == 'dir'){
	// 			$this->current_listings[] = array($listing['path'], $listing['basename']);
	// 		}
	// 	}
	// }

	// public function load_search_filters(){
	// 	$this->search_string = "";
	// }

	// public function load_media_filters(){
	// 	$terms = get_terms( array(
	// 		'taxonomy' => 'media_type',
	// 		'hide_empty' => false,
	// 	) );
	// 	$this->media_filters = array();

	// 	foreach ($terms as $value) {
	// 		$icon = get_term_meta($value->term_id, "category_icon", true);
	// 		$this->media_filters[] = array(
	// 			"ID" => $value->term_id,
	// 			"Name" => $value->name,
	// 			"Selected" => false,
	// 			"Icon" => $icon["guid"]
	// 		);
	// 	}
	// }

	// public function load_status_filters(){

	// 	$terms = array(
	// 		"Active",
	// 		"Inactive",
	// 		"Expired",
	// 		"Approved",
	// 		"Rejected"
	// 	);

	// 	$this->status_filters = array();

	// 	foreach ($terms as $value) {
	// 		$this->status_filters[] = array(
	// 			"ID" => $value,
	// 			"Name" => $value,
	// 			"Selected" => false,
	// 		);
	// 	}

	// }

	/**
	 * Displays the HTML for search field
	 *
	 * @return void
	 */
	public function display_search_filter(){?>
<div class="form-group has-search position-relative">
  <span class="fa fa-search form-control-feedback top-0 start-0"></span>
  <input type="text" id="media-search-input"
    class="search-field form-control  <?php echo $this->searchFilterSelector;?>" placeholder="Search Title"
    value="<?php echo $this->search_string;?>">

  <style>
  .search-input-loader {
    position: absolute;
    top: 7px;
    background-color: white;
    right: 5px;
    width: 16px;
    height: 16px;
    border: 2px solid #000;
    border-bottom-color: transparent;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
  }

  @keyframes rotation {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
  </style>

  <!-- loader -->
  <span class="search-input-loader hidden"></span>

</div>
<!-- <div class="input-group search-group">
			<input type="text" class="search-field form-control <?php echo $this->searchFilterSelector;?>" placeholder="Search" value="<?php echo $this->search_string;?>"/>
			<button class="input-group-addon search-btn <?php echo $this->searchBtnFilterSelector;?>">
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
			</button>
		</div> -->
<?php
	}

	/**
	 * Displays the html for media filters
	 *
	 * @return void
	 */
	public function display_media_filters(){
?>
<select class="<?php echo $this->mediaFiltersSelector;?>" id="">
  <option value="0">ALL</option>
  <?php

				foreach ( $this->media_filters as $mf) {
					$selected = "";
					if($mf['Selected']){
						$selected = " selected ";
					}?>

  <option value="<?php echo $mf['ID']; ?>"><?php echo ucfirst($mf['Name']); ?> </option>
  <?php

			} ?>

</select>
<label>Media Type</label>

<?php

	}

	public function display_ar_media_filters(){

		if( get_query_var('ptype') == 'media_type' ) {

			$this->display_ar_media_assignments_filter();

		} else {

			$this->display_ar_media_types_filter();

		}

			}

			public function display_ar_media_assignments_filter() {

				$stm = SettingsManager::GI();
				$ar_active_media_group = $stm->get_current_media_group();

				global $wpdb;
				$active_media_group_assignments = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_media_assignments WHERE media_group_id = %d ", $ar_active_media_group->id));

	?>

<select id="media-assignment-select">
  <option value="0">All</option>
  <?php

				foreach($active_media_group_assignments as $assignment) {
					echo "<option value='$assignment->id'>$assignment->assignment_name</option>";
				}

	?>

</select>
<label>Media Assignment</label>

<?php

			}

			public function display_ar_media_types_filter() {

				?>
<select id="media-type-select">
  <option value="all">All</option>
  <option value="image">Image</option>
  <option value="video">Video</option>
  <option value="audio">Audio</option>
  <option value="vimeo_url">Vimeo URL</option>
  <option value="audio_url">Audio URL</option>
  <option value="image_url">Image URL</option>
  <option value="youtube_url">YouTube URL</option>
  <option value="video_url">Video URL</option>
  <option value="link">Link</option>
  <option value="zip">Zip</option>
  <option value="doc">Doc</option>
  <option value="pdf">PDF</option>
</select>
<label>Media Type</label>

<?php

			}

	/**
	 * Displayes the html for status filters
	 *
	 * @return void
	 */
	public function display_status_filters(){
		?>
<select class="<?php echo $this->statusFiltersSelector;?>" id="">
  <option value="0">ALL</option>
  <?php

				foreach ( $this->status_filters as $mf) {
					$selected = "";
					if($mf['Selected']){
						$selected = " selected ";
					}?>

  <option value="<?php echo $mf['ID']; ?>"><?php echo ucfirst($mf['Name']); ?> </option>
  <?php

			} ?>

</select>
<label>Status</label>
<?php
		/*

		<div class="checkboxes-container filter-section-container">
			<!-- <h4 class="filter-title">Status Filters</h4> -->
			<select name="media_filters" class="media_filters custom-select <?php echo $this->statusFiltersSelector;?>">
<option value="">Status</option>
<?php
			$i = 0;
			foreach ($this->status_filters as $status) {
				$selected = "";
				if($status['Selected']){
					$selected = " checked ";
				}
				$i++;
				$ico = "fa-circle";
				if(strtolower($status['Name']) == 'approved'){
					$ico = "fa-check-circle";
				}
				?>
<!-- <div class="form-check">
					<input class="form-check-input <?php echo $this->statusFiltersSelector;?>" type="checkbox" value="<?php echo strtolower($status['Name']);?>" id="status-<?php echo strtolower($status['Name']);?>" name="status[]" <?php echo $selected; ?>>
					<label class="form-check-label" for="status-<?php echo strtolower($status['Name']);?>">
						<i class="fa <?php echo $ico." ".strtolower($status['Name']);?>" aria-hidden="true"></i> <?php echo $status['Name'];?>
					</label>
				</div> -->
<option value="<?php echo $status['Name']; ?>"><?php echo $status['Name']; ?></option>
<?php
			}
			?>
</select>
</div>
*/?>
<?php

	}

	/**
	 * Display HTML for curent Directory Listings
	 *
	 * @return void
	 */
	
	// public function load_JS(){
	// 	/**
	// 	 * Mam Browser, Directory Creator
	// 	 */
	// 	wp_localize_script( 'mam-filters-handler', 'mamFilterObj', array(
	// 		"searchFilterSelector" => $this->searchFilterSelector,
	// 		"mediaFiltersSelector" => $this->mediaFiltersSelector,
	// 		"statusFiltersSelector" => $this->statusFiltersSelector,
	// 		"searchBtnFilterSelector" => $this->searchBtnFilterSelector,
	// 		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	// 		'nonce'  =>  wp_create_nonce("mam_dir_filters"),
	// 		'current_directory' => $this->current_directory,
	// 		'current_directory_name' => "Home",
	// 		// 'root'	=>$this->current_brand->brand_code,
	// 		'action_dir' => 'ajax_get_dir_listings',
	// 		// 'action_paged_request' => 'ajax_get_fitlered_paged_assets',
	// 		)
	// 	);
	// 	wp_enqueue_script( 'mam-filters-handler' );
	// }



}