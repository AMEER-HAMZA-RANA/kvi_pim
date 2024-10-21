<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The values from this class will be used by Assets Class
 */

class FavFilters {

	public $item_type;
	public $media_filters;
	public $search_string;
	public $current_brand;

	public $active_filters;

	public $searchFilterSelector;
	public $searchBtnFilterSelector;
	public $mediaFiltersSelector;
	public $itemTypeFiltersSelector;

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
		$this->current_directory = $this->current_brand->brand_code."/";

		$this->searchFilterSelector = "mam_search_field";
		$this->searchBtnFilterSelector = "mam_search_btn";
		$this->mediaFiltersSelector  = "mam_cb_filters";
		$this->itemTypeFiltersSelector  = "asset_type_cb_filters";

		$this->load_search_filters();
		$this->load_media_filters();
		$this->load_item_type_filters();

		//Enqueue JS
		$this->load_JS();
	}

	public function display_filters(){

		$this->display_item_type_filters();

		// $this->display_media_filters();
		if($this->current_brand->brand_code == 'ar') {
			// $this->display_ar_media_filters();
		} else {
			$this->display_media_filters();
		}

		$this->display_search_filter();

	}

	public function display_ar_media_filters(){
		?>
<select id="media-type-select" disabled style="background-color:lightgray;">
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

	public function load_item_type_filters(){

		$this->item_type = array(
			array(
				"ID" => "media",
				"Name" => "media",
				"Selected" => false,
			),
			array(
				"ID" => "product",
				"Name" => "product",
				"Selected" => false,
			)
		);

	}


	/**
	 * Displays the HTML for search field
	 *
	 * @return void
	 */
	public function display_search_filter(){?>
<div class="form-group has-search position-relative">
  <span class="fa fa-search form-control-feedback top-0 start-0"></span>
  <input id="ar-fav-search-filter" type="text"
    class="search-field form-control  <?php echo $this->searchFilterSelector;?>" placeholder="Search"
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
			<input type="text" class="search-field form-control <?php //echo $this->searchFilterSelector;?>" placeholder="Search" value="<?php //echo $this->search_string;?>"/>
			<button class="input-group-addon search-btn <?php //echo $this->searchBtnFilterSelector;?>">
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
			</button>
		</div> -->
<?php
	}

	/**
	 * Displays the html for Item Type
	 *
	 * @return void
	 */
	public function display_item_type_filters(){
		?>
<select class="<?php echo $this->itemTypeFiltersSelector;?>" id="">
  <option value="0">ALL</option>
  <?php

					foreach ( $this->item_type as $mf) {
						$selected = "";
						if($mf['Selected']){
							$selected = " selected ";
						}?>

  <option value="<?php echo $mf['ID']; ?>"><?php echo ucfirst($mf['Name']); ?> </option>
  <?php

				} ?>

</select>
<label>Item Type</label>


<?php
	}

	/**
	 * Displays the html for media filters
	 *
	 * @return void
	 */
	public function display_media_filters(){
	?>
<select name="image" class="<?php echo $this->mediaFiltersSelector;?>" id="">
  <option value="0">ALL</option>
  <?php
				foreach ( $this->media_filters as $mf) {
					$selected = "";
					if($mf['Selected']){
						$selected = " checked ";
					}
				?>

  <option value="<?php echo $mf['ID']; ?>"><?php echo ucfirst($mf['Name']); ?> </option>
  <?php

			} ?>

</select>
<label>Media Filters</label>



<?php
	}

	public function load_JS(){
		/**
		 * Mam Browser, Directory Creator
		 */
		wp_localize_script( 'fav-filters-handler', 'FavFilterObj', array(
			"searchFilterSelector" => $this->searchFilterSelector,
			"mediaFiltersSelector" => $this->mediaFiltersSelector,
			"searchBtnFilterSelector" => $this->searchBtnFilterSelector,
			"itemTypeFiltersSelector" => $this->itemTypeFiltersSelector
			)
		);
		wp_enqueue_script( 'fav-filters-handler' );
	}

}