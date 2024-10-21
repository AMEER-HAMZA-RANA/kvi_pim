<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The values from this class will be used by Assets Class
 */

class SKUFilters {


	public $search_string;

	public $searchFilterSelector;
	public $searchBtnFilterSelector;
	public $prodFiltersSelector;

	public function __construct($search_string = "")
	{

		// $this->current_brand = $brand;
		// $this->current_directory = $this->current_brand->brand_code."/";


		$this->search_string = $search_string;
		$this->searchFilterSelector = "mam_search_field";
		$this->searchBtnFilterSelector = "mam_search_btn";
		$this->prodFiltersSelector = "";

		// $this->load_search_filters();

		//Enqueue JS
		$this->load_JS();
	}

	public function display_filters(){

		$this->display_search_filter();

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
	 * Displays the html for Item Type
	 *
	 * @return void
	 */
	public function display_item_type_filters(){
		?>
			<div class="checkboxes-container filter-section-container">
				<h4 class="filter-title">Item Type</h4>
				<?php

				foreach ( $this->item_type as $mf) {
					$selected = "";
					if($mf['Selected']){
						$selected = " checked ";
					}
					?>
					<div class="form-check">
						<input class="form-check-input  <?php echo $this->itemTypeFiltersSelector;?>" type="checkbox" value="<?php echo $mf['ID']; ?>" id="filter_cats_<?php echo $mf['Name']; ?>" <?php echo $selected; ?>>
						<label class="form-check-label" for="filter_cats_<?php echo $mf['Name']; ?>">
							<?php echo $mf['Name']; ?>
						</label>
					</div>

					<?php
				}

				?>
			</div>

		<?php
	}


	// public function load_JS(){
	// 	/**
	// 	 * Search Items Loader
	// 	 */
	// 	wp_localize_script( 'sku-search-handler', 'skuSearchObj', array(
	// 		"searchFilterSelector" => $this->searchFilterSelector,
	// 		"searchBtnFilterSelector" => $this->searchBtnFilterSelector,
	// 		)
	// 	);
	// 	wp_enqueue_script( 'sku-search-handler' );
	// }

	public function load_JS(){

		$search_str = "";
		if( isset($_GET["search_txt"]) && !empty($_GET["search_txt"]) ){
			$search_str = $_GET["search_txt"];
		}

		wp_localize_script( 'prods-filter-handler', 'ProdsFilterObj', array(
			"current_category" => "",
			"searchFilterSelector" => $this->searchFilterSelector,
			"favFilterSelector" => "",
			"search_string" => $search_str,
			"searchBtnFilterSelector" => $this->searchBtnFilterSelector,
			"prodFiltersSelector" => $this->prodFiltersSelector,
			"mostViewedFilterSelector" => ""
			)
		);
		wp_enqueue_script( 'prods-filter-handler' );
	}

}
