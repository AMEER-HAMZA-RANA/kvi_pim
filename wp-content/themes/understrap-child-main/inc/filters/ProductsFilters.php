<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The values from this class will be used by Assets Class
 */

class ProductsFilters {

	public $search_string;
	public $prod_filters;
	public $prod_sub_filters;
	public $most_viewed_filter;
	public $fav_filter;
	public $inactive_filter;
	public $current_brand;

	public $active_filters;
	public $current_category;
	public $searchFilterSelector;
	public $searchBtnFilterSelector;
	public $searchExactFilterSelector;
	public $prodFiltersSelector;
	public $prodFilterslabel;
	public $prodFiltersarrow;
	public $mostViewedFilterSelector;
	public $favFilterSelector;
	public $inactiveFilterSelector;


	public function __construct($brand)
	{

		$this->current_brand = $brand;

		$this->searchFilterSelector = "prods_search_field";
		$this->searchBtnFilterSelector = "prods_search_btn";
		$this->searchExactFilterSelector = "prods_search_exact_filter";
		$this->prodFiltersSelector = "prods_filters";
		$this->prodFilterslabel = "shopvac-collapse-label";
		$this->prodFiltersarrow = "shopvac-collapse-arrow";
		$this->mostViewedFilterSelector = "prods_most_viewed";
		$this->favFilterSelector = "prods_fav_filters";
		$this->inactiveFilterSelector= "prods_inactive_filter";


		$this->load_search_filters();
		$this->load_prod_filters();

		// //Enqueue JS
		$this->load_JS();
	}

	public function display_filters(){

		if( get_query_var( 'ptype'  ) == "category" ) {

			$this->display_search_filter();

		} else {

			$this->display_prod_filters();
			$this->display_search_filter();

		}

	}

	public function load_search_filters(){
		$this->search_string = "";
	}

	public function load_prod_filters(){
	// 	$term_args = array(
	// 		'taxonomy' => 'product_filter',
	// 		'hide_empty' => false,
	// 		'meta_query' => array(
	// 			array(
	// 			'key'       => 'brand',
	// 			'value'     => $this->current_brand->brand_id,
	// 			'compare'   => '='
	// 			)
	// 		)
	// 	);

		// if($this->current_brand->brand_code=='sv'){
		// 	$term_args['parent']=0;
		// }

		//Product Catefory Filters
		// if($this->current_brand->brand_code == 'ar'){

			$stm = SettingsManager::GI();

			$terms = $stm->get_ar_sidebar_filters();
			// echo "asdasd";
			// $stm->dump($terms);

		// } else {

		// 	$terms = get_terms( $term_args);

		// }


		$this->prod_filters = array();

		// if($this->current_brand->brand_code == 'ar'){

			foreach ($terms as $value) {

				$this->prod_filters[] = array(
					"ID" => $value->id,
					"Name" => $value->name,
					"Slug" => $value->slug,
					"Selected" => false,
				);
			}

		// } else {

		// 	foreach ($terms as $value) {

		// 		$this->prod_filters[] = array(
		// 			"ID" => $value->term_id,
		// 			"Name" => $value->name,
		// 			"Slug" => $value->slug,
		// 			"Selected" => false,
		// 		);
		// 	}

		// }




		$this->most_viewed_filter = array(
			"ID" => "most_viewed",
			"Name" => "Most Viewed",
			"Selected" => "false"
		);
		$this->fav_filter = array(
			"ID" => "favorites",
			"Name" => "Favorites",
			"Selected" => "false"
		);

		$this->inactive_filter = array(
			"ID" => "inactive_filter",
			"Name" => "Inactive",
			"Selected" => "false"
		);

	}

	/*
	 * Displays the HTML for search field
	 *
	 * @return void
	 */
	public function display_search_filter(){?>
<label>
  <input class="checkbox  <?php echo $this->searchExactFilterSelector;?>" type="checkbox" value="exact"
    id="filter_sku_exact">
  Exact Search
</label>
<div class="form-group has-search position-relative">
  <span class="fa fa-search form-control-feedback top-0 start-0"></span>
  <input type="text" class="search-field form-control  <?php echo $this->searchFilterSelector;?>"
    placeholder="Search Sku" value="<?php echo $this->search_string;?>">

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

<!-- <button class="input-group-addon search-btn <?php //echo $this->searchBtnFilterSelector;?>">
			<i class="fa fa-arrow-right" aria-hidden="true"></i>
		</button> -->


<?php
	}



	/**
	 * Displays the html for product filters
	 *
	 * @return void
	 */
	public function display_prod_filters(){
?>
<select name="image" class="<?php echo $this->prodFiltersSelector;?>" id="">
  <option value="0">ALL</option>
  <?php
			foreach ( $this->prod_filters as $pf) {
				$selected = "";
				if($pf['Selected']){
					$selected = " selected ";
				}
				if($this->current_brand->brand_code=='sv'){
					$sub_filters = $this->has_sub_prod_filers($pf['ID']);
					?>
  <optgroup label="<?php echo $pf['Name']; ?>">
    <?php
							$this->display_sub_prod_filers();
					  	?>
  </optgroup>
  <?php }	else{ ?>
  <option value="<?php echo $pf['ID']; ?>"><?php echo $pf['Name']; ?> </option>
  <?php

				}
			} ?>

  <option value="<?php echo $this->most_viewed_filter["ID"];?>"><?php echo $this->most_viewed_filter["Name"] ?>
  </option>
  <option value="<?php echo $this->inactive_filter["ID"]; ?>"><?php echo $this->inactive_filter["Name"]; ?> </option>
</select>
<label>Filters</label>
<!-- <div class="checkboxes-container filter-section-container"> -->

<?php
			// foreach ( $this->prod_filters as $pf) {
			// 	// print_r($pf);
			// 	// if($this->current_brand->brand_code=='sv'){ break; }
			// 	$selected = "";
			// 	if($pf['Selected']){
			// 		$selected = " checked ";
			// 	}
			// 	$slug = get_query_var( "cat_slug" );
			// 	// print_r($slug);
			// 	// echo $pf['Slug'];
			// 	if($slug!=''){
			// 		if($slug!=$pf['Slug']){
			// 			continue;
			// 		}
			// 	}

					// $sub_filters = $this->has_sub_prod_filers($pf['ID']);
					// if($slug==''){
				?>
<!-- <div class="form-check"> -->
<?php
						// if($sub_filters==false){
						// 	$label_class ='';
					?>
<!-- <input class="form-check-input  <?php //echo $this->prodFiltersSelector;?>" type="checkbox" value="<?php //echo $pf['ID']; ?>" id="filter_cats_<?php //echo $pf['Slug']; ?>" <?php //echo $selected; ?>> -->
<?php //}else{ ?>
<!-- <i class="fa fa-chevron-down shopvac-collapse-arrow"></i> -->
<?php
						// $label_class = 'shopvac-collapse-label';
					//} ?>
<!-- <label class="form-check-label <?php //echo $label_class;?>" for="filter_cats_<?php //echo $pf['Slug']; ?>"> -->
<?php //echo $pf['Name']; ?>
<!-- </label> -->
<?php //} ?>
<?php
						// if($this->current_brand->brand_code=='sv'){
						// 	$this->display_sub_prod_filers();
						// }
					?>

<?php
					// if($slug==''){
				?>
<!-- </div> -->
<?php //} ?>

<?php
			// }
			?>

<!-- <div class="form-check">
				<input class="form-check-input <?php //echo $this->mostViewedFilterSelector;?>" type="checkbox" value="<?php //echo $this->most_viewed_filter["ID"];?>" id="filter_mv" <?php //echo $this->most_viewed_filter["Selected"];?>>
				<label class="form-check-label" for="filter_mv">
					<?php //echo $this->most_viewed_filter["Name"];?>
				</label>
			</div>

			<div class="form-check">
				<input class="form-check-input <?php //echo $this->inactiveFilterSelector;?>" type="checkbox" value="<?php //echo $this->inactive_filter["ID"];?>" id="filter_ia" <?php //echo $this->inactive_filter["Selected"];?>>
				<label class="form-check-label" for="filter_ia">
					<?php //echo $this->inactive_filter["Name"];?>
				</label>
			</div> -->

<?php
			// if($this->current_brand->brand_code=='sv')
			// {
			// 	$this->display_active_category_filters();
			// }
			?>

<!-- </div> -->

<?php
	}

	public function display_active_category_filters(){
		$slug = get_query_var( "cat_slug" );
		//echo $slug;
		if($slug != ''){
			$current_cat = get_term_by( 'slug', $slug, 'product_categories' );
			//print_r($current_cat);
			$sub_categories = get_term_children( $current_cat->term_id, 'product_categories' );
			if(!empty($sub_categories)){
				foreach($sub_categories as $id){
					$term = get_term_by( 'id', $id, 'product_categories' );
					?>
<div class="form-check">
  <input class="form-check-input term_filter" type="radio" value="<?php echo $term->term_id;?>">
  <label class="form-check-label" for="filter_ia">
    <a style="color: #fff;" href="/shopvac/products/category/<?php echo $term->slug; ?>"><?php echo $term->name; ?></a>
  </label>
</div>
<?php
				}
			}else{
				//echo $current_cat->parent;
				$psub_categories = get_term_children( $current_cat->parent, 'product_categories' );
				if(!empty($psub_categories)){
					foreach($psub_categories as $id){
						$term = get_term_by( 'id', $id, 'product_categories' );
						?>
<div class="form-check">
  <input <?php if($slug == $term->slug){ echo "checked"; } ?> class="form-check-input term_filter" type="radio"
    value="<?php echo $term->term_id;?>">
  <label class="form-check-label" for="filter_ia">
    <a style="color: #fff;" href="/shopvac/products/category/<?php echo $term->slug; ?>"><?php echo $term->name; ?></a>
  </label>
</div>
<?php
					}
				}
			}
		}
	}

	public function load_JS(){
		wp_localize_script( 'prods-filter-handler', 'ProdsFilterObj', array(
			"current_category" => $this->current_category,
			"searchFilterSelector" => $this->searchFilterSelector,
			"searchExactFilterSelector" => $this->searchExactFilterSelector,
			"favFilterSelector" => $this->favFilterSelector,
			"searchBtnFilterSelector" => $this->searchBtnFilterSelector,
			"prodFiltersSelector" => $this->prodFiltersSelector,
			"mostViewedFilterSelector" => $this->mostViewedFilterSelector,
			"inactiveFilterSelector" => $this->inactiveFilterSelector,
			"prodFilterslabel" => $this->prodFilterslabel,
			"prodFiltersarrow" => $this->prodFiltersarrow
			)
		);
		wp_enqueue_script( 'prods-filter-handler' );
	}
	public function has_sub_prod_filers($parent){
		$term_args = array(
			'taxonomy' => 'product_filter',
			'hide_empty' => false,
			'parent'	=> $parent,
			'meta_query' => array(
				array(
				'key'       => 'brand',
				'value'     => $this->current_brand->brand_id,
				'compare'   => '='
				)
			)
		);
		$terms = get_terms( $term_args);
		if(!empty($terms)){
			$this->prod_sub_filters = $terms;
			return true;
		}else{
			$this->prod_sub_filters = array();
			return false;
		}
	}
	public function display_sub_prod_filers(){

		$slug = get_query_var( "cat_slug" );
		$margin="";
		if($slug!=''){
			$margin="style='margin-left: 0px;'";
		}

		//Product Catefory Filters
		if(!empty($this->prod_sub_filters)){
			foreach ($this->prod_sub_filters as $value) {
				echo '<option value="'.$value->term_id.'">'.$value->name.'</option>';
			}
		}
	}
}