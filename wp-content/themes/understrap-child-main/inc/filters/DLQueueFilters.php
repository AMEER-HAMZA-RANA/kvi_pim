<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The values from this class will be used by Download Queue
 */

class DLQueueFilters {

	public $media_filters;
	public $search_string;
	public $current_brand;

	public $active_filters;

	public $searchFilterSelector;
	public $searchBtnFilterSelector;
	public $mediaFiltersSelector;

	public function __construct($brand)
	{
		$this->active_filters = array();

		//Hide Media Filters on already filtered sub pages in MAM Index
		$ptype = get_query_var( "ptype" );
		if($ptype != 'media_type'){
			$this->active_filters[] = "media_filters";
		}
		$this->active_filters[] = "status_filters";

		$this->current_brand = $brand;

		$this->searchFilterSelector = "mam_search_field";
		$this->searchBtnFilterSelector = "mam_search_btn";
		$this->mediaFiltersSelector  = "mam_cb_filters";

		$this->load_search_filters();
		$this->load_media_filters();

		//Enqueue JS
		$this->load_JS();
	}

	public function display_filters(){

		$this->display_search_filter();
		if(in_array('media_filters', $this->active_filters)){
			$this->display_media_filters();
		}
	}


	public function load_search_filters(){
		$this->search_string = "";
	}

	public function load_media_filters(){
		$terms = get_terms( array(
			'taxonomy' => 'media_type',
			'hide_empty' => false,
		) );
		$this->media_filters = array();

		foreach ($terms as $value) {
			$icon = get_term_meta($value->term_id, "category_icon", true);
			$this->media_filters[] = array(
				"ID" => $value->term_id,
				"Name" => $value->name,
				"Selected" => false,
				"Icon" => $icon["guid"]
			);
		}
	}


	/**
	 * Displays the HTML for search field
	 *
	 * @return void
	 */
	public function display_search_filter(){?>
		<div class="input-group search-group">
			<input type="text" class="search-field form-control <?php echo $this->searchFilterSelector;?>" placeholder="Search" value="<?php echo $this->search_string;?>"/>
			<button class="input-group-addon search-btn <?php echo $this->searchBtnFilterSelector;?>">
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
			</button>
		</div>
		<?php
	}

	/**
	 * Displays the html for media filters
	 *
	 * @return void
	 */
	public function display_media_filters(){
?>
		<div class="checkboxes-container filter-section-container">
			<h4 class="filter-title">Media Filters</h4>
			<?php

			foreach ( $this->media_filters as $mf) {
				$selected = "";
				if($mf['Selected']){
					$selected = " checked ";
				}
				?>
				<div class="form-check">
					<input class="form-check-input  <?php echo $this->mediaFiltersSelector;?>" type="checkbox" value="<?php echo $mf['ID']; ?>" id="filter_cats_<?php echo $mf['Name']; ?>" <?php echo $selected; ?>>
					<label class="form-check-label" for="filter_cats_<?php echo $mf['Name']; ?>">
						<img style="width:15px; filter: invert(100%);" src="<?php echo  $mf['Icon'];?>" alt="">
						<?php echo $mf['Name']; ?>
					</label>
				</div>

				<?php
			}

			?>
		</div>

<?php
	}


	public function load_JS(){
		/**
		 * Mam Browser, Directory Creator
		 */
		// wp_localize_script( 'mam-filters-handler', 'mamFilterObj', array(
		// 	"searchFilterSelector" => $this->searchFilterSelector,
		// 	"mediaFiltersSelector" => $this->mediaFiltersSelector,
		// 	"statusFiltersSelector" => '',
		// 	"searchBtnFilterSelector" => $this->searchBtnFilterSelector,
		// 	'ajaxurl' => admin_url( 'admin-ajax.php' ),
		// 	'nonce'  =>  wp_create_nonce("mam_dir_filters"),
		// 	'current_directory' => '',
		// 	'current_directory_name' => "",
		// 	// 'root'	=>$this->current_brand->brand_code,
		// 	'action_dir' => '',
		// 	// 'action_paged_request' => 'ajax_get_fitlered_paged_assets',
		// 	)
		// );
		// wp_enqueue_script( 'mam-filters-handler' );
	}



}
